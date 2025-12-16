<?php

namespace App\Services;

use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProductService
{
    /**
     * List all products
     */
    public function list(int $perPage = 15, int $page = 1): array
    {
        try {
            $products = Product::orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            return [
                'code' => 1,
                'data' => $products->items(),       // array of current page items
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page'    => $products->lastPage(),
                    'per_page'     => $products->perPage(),
                    'total'        => $products->total(),
                ],
            ];
        } catch (Throwable $th) {
            return [
                'code' => 0,
                'msg' => $th->getMessage(),
            ];
        }
    }


    /**
     * Create a new product
     */
    public function create(array $data): array
    {
        try {

            $product = Product::create($data);

            return [
                'code' => 1,
                'data' => $product,
            ];
        } catch (Throwable $th) {

            return [
                'code' => 0,
                'msg' => $th->getMessage(),
            ];
        }
    }

    /**
     * Find a product by ID
     */
    public function find(int $id): array
    {
        try {
            $product = Product::find($id);

            if (!$product) { throw new Exception('Product not found'); }

            return [
                'code' => 1,
                'data' => $product,
            ];
        } catch (Throwable $th) {
            return [
                'code' => 0,
                'msg' => $th->getMessage(),
            ];
        }
    }

    /**
     * Update a product
     */
    public function update(int $id, array $data): array
    {
        try {

            $product = Product::find($id);

            if (!$product) { throw new Exception('Product not found'); }

            $product->update($data);

            return [
                'code' => 1,
                'data' => $product,
            ];

        } catch (Throwable $th) {

            return [
                'code' => 0,
                'msg' => $th->getMessage(),
            ];
        }
    }

    /**
     * Delete a product
     */
    public function delete(int $id): array
    {
        try {
            $product = Product::find($id);
            if (!$product) {
                return [
                    'code' => 0,
                    'msg' => 'Product not found',
                ];
            }

            $product->delete();

            return [
                'code' => 1,
                'data' => null,
            ];
        } catch (Throwable $th) {
            return [
                'code' => 0,
                'msg' => $th->getMessage(),
            ];
        }
    }
}
