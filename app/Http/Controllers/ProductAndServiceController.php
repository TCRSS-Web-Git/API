<?php

namespace App\Http\Controllers;

use App\Actions\SaveProductAndService;
use App\Filters\ProductAndServiceFilter;
use App\Http\Requests\CreateOrUpdateProductAndServiceRequest;
use App\Http\Resources\ProductAndServiceResource;
use App\Models\ProductAndService;
use Illuminate\Support\Facades\Gate;

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
    public function store(CreateOrUpdateProductAndServiceRequest $request, SaveProductAndService $saveProductAndService)
    {
        Gate::authorize('create', ProductAndService::class);

        $productAndService = $saveProductAndService->execute(new ProductAndService, $request->validated());

        return new ProductAndServiceResource($productAndService);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductAndService $productsAndService)
    {
        return new ProductAndServiceResource($productsAndService);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateOrUpdateProductAndServiceRequest $request, ProductAndService $productsAndService, SaveProductAndService $saveProductAndService)
    {
        Gate::authorize('update', $productsAndService);

        $productsAndService = $saveProductAndService->execute($productsAndService, $request->validated());

        return new ProductAndServiceResource($productsAndService);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductAndService $productsAndService)
    {
        Gate::authorize('delete', $productsAndService);

        $productsAndService->delete();

        return response()->noContent();
    }
}
