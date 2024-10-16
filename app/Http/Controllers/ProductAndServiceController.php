<?php

namespace App\Http\Controllers;

use App\Filters\ProductAndServiceFilter;
use App\Http\Requests\StoreProductAndServiceRequest;
use App\Http\Requests\UpdateProductAndServiceRequest;
use App\Http\Resources\ProductAndServiceResource;
use App\Models\ProductAndService;

class ProductAndServiceController extends Controller
{
    /**
     * Get all product and services
     *
     * @group Product and services
     */
    public function index(ProductAndServiceFilter $filter)
    {
        $count = ProductAndService::count();

        return ProductAndServiceResource::collection(ProductAndService::with(['latestAudit.user'])->filter($filter)->paginate($this->getPerPage($count, $count)));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductAndServiceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductAndService $productAndService)
    {
        return new ProductAndServiceResource($productAndService);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductAndService $productAndService)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductAndServiceRequest $request, ProductAndService $productAndService)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductAndService $productAndService)
    {
        //
    }
}
