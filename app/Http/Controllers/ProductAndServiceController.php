<?php

namespace App\Http\Controllers;

use App\Actions\ReorderProductAndService;
use App\Actions\SaveProductAndService;
use App\Enums\ProductAndServiceStatus;
use App\Filters\ProductAndServiceFilter;
use App\Http\Requests\CreateOrUpdateProductAndServiceRequest;
use App\Http\Requests\ReorderProductAndServiceRequest;
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
        if (request()->routeIs('public.products-and-services.index')) {
            request()->query->add(['status' => ProductAndServiceStatus::PUBLISHED->value]);
        }

        $count = ProductAndService::count();

        return ProductAndServiceResource::collection(ProductAndService::with(['latestAudit.user'])->filter($filter)->paginate($this->getPerPage($count, $count)));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @group Product and services
     */
    public function store(CreateOrUpdateProductAndServiceRequest $request, SaveProductAndService $saveProductAndService)
    {
        Gate::authorize('create', ProductAndService::class);

        $productAndService = $saveProductAndService->execute(new ProductAndService, $request->validated());

        return new ProductAndServiceResource($productAndService);
    }

    /**
     * Display the specified resource.
     *
     * @group Product and services
     */
    public function show(ProductAndService $productsAndService)
    {
        return new ProductAndServiceResource($productsAndService);
    }

    /**
     * Update the specified resource in storage.
     *
     * @group Product and services
     */
    public function update(CreateOrUpdateProductAndServiceRequest $request, ProductAndService $productsAndService, SaveProductAndService $saveProductAndService)
    {
        Gate::authorize('update', $productsAndService);

        $productsAndService = $saveProductAndService->execute($productsAndService, $request->validated());

        return new ProductAndServiceResource($productsAndService);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @group Product and services
     */
    public function destroy(ProductAndService $productsAndService)
    {
        Gate::authorize('delete', $productsAndService);

        $productsAndService->delete();

        return response()->noContent();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @group Product and services
     */
    public function reorder(ReorderProductAndServiceRequest $request, ReorderProductAndService $reorderProductAndService)
    {
        Gate::authorize('reorder', ProductAndService::class);

        $reorderProductAndService->execute($request->validated());

        return response()->json();
    }
}
