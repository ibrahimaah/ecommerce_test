<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOrderRequest;
use App\Services\OrderService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ResponseTrait;

    public function __construct(protected OrderService $orderService) {}

    /**
     * Create a new order
     */
    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        $userId = $request->user()->id;

        $result = $this->orderService->create($data, $userId);

        return $this->handleServiceResponse($result, 'Order created successfully', 201);
    }

    /**
     * Get user's orders
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $result = $this->orderService->getUserOrders($userId);

        return $this->handleServiceResponse($result, 'Orders retrieved successfully');
    }

    /**
     * Get single order details
     */
    public function show(Request $request, int $id)
    {
        $userId = $request->user()->id;
        $result = $this->orderService->getOrderDetails($id, $userId);

        return $this->handleServiceResponse($result, 'Order details retrieved successfully');
    }
}
