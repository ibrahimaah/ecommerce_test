<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreProductRequest;
use App\Http\Requests\Api\UpdateProductRequest;
use App\Services\ProductService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ResponseTrait;

    public function __construct(protected ProductService $productService) {}

    /**
     * List products (with pagination)
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $page = $request->query('page', 1);

        $result = $this->productService->list((int)$perPage, (int)$page);

        return $this->handleServiceResponse($result, 'Products retrieved successfully');
    }

    /**
     * Create a new product
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        $result = $this->productService->create($data);

        return $this->handleServiceResponse($result, 'Product created successfully', 201, 500);
    }

    /**
     * Show a single product
     */
    public function show(int $id)
    {
        $result = $this->productService->find($id);

        return $this->handleServiceResponse($result, 'Product retrieved successfully');
    }

    /**
     * Update a product
     */ 
    public function update(UpdateProductRequest $request, int $id)
    {
        $data = $request->validated(); 
        
        $result = $this->productService->update($id, $data);

        return $this->handleServiceResponse($result, 'Product updated successfully');
    }

    /**
     * Delete a product
     */
    public function destroy(int $id)
    {
        $result = $this->productService->delete($id);

        return $this->handleServiceResponse($result, 'Product deleted successfully');
    }
}
