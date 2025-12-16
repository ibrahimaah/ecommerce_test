<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrderService
{
    /**
     * Create a new order with items
     */
    public function create(array $data, int $userId): array
    {
        try {
            DB::beginTransaction();

            $totalPrice = 0;

            // Group items by product_id 
            $items = collect($data['items'])
                ->groupBy('product_id')
                ->map(fn($group) => [
                    'product_id' => $group->first()['product_id'],
                    'quantity'   => $group->sum('quantity'),
                ]);

            foreach ($items as $item) {
                // LOCK the product row
                $product = Product::where('id', $item['product_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    throw new Exception("Product ID {$item['product_id']} not found.");
                }

                if ($product->stock_quantity < $item['quantity']) {
                    throw new Exception("Insufficient stock for product '{$product->name}'.");
                }

                $totalPrice += $item['quantity'] * $product->price;
            }

            // Create order
            $order = Order::create([
                'user_id'     => $userId,
                'total_price' => $totalPrice,
                'status'      => OrderStatus::PENDING,
            ]);

            // Create order items + reduce stock
            foreach ($items as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->lockForUpdate()
                    ->first();

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $product->price,
                ]);

                // SAFE stock update
                $product->update([
                    'stock_quantity' => $product->stock_quantity - $item['quantity'],
                ]);
            }

            DB::commit();

            return [
                'code' => 1,
                'data' => $order->load('items.product'),
            ];
        } catch (Throwable $th) {
            DB::rollBack();

            return [
                'code' => 0,
                'msg'  => $th->getMessage(),
            ];
        }
    }


    /**
     * Get user orders
     */
    public function getUserOrders(int $userId): array
    {
        try {
            $orders = Order::where('user_id', $userId)
                ->with('items.product')
                ->orderBy('created_at', 'desc')
                ->get();

            return [
                'code' => 1,
                'data' => $orders,
            ];
        } catch (Throwable $th) {
            return [
                'code' => 0,
                'msg' => $th->getMessage(),
            ];
        }
    }

    /**
     * Get single order by ID
     */
    public function getOrderDetails(int $id, int $userId): array
    {
        try {
            $order = Order::with('items.product')
                ->where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$order) {
                throw new Exception('Order not found');
            }

            return [
                'code' => 1,
                'data' => $order,
            ];
        } catch (Throwable $th) {
            return [
                'code' => 0,
                'msg' => $th->getMessage(),
            ];
        }
    }
}
