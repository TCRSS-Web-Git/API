<?php

namespace App\Http\Controllers;

use App\Actions\SaveProductAndService;
use App\Http\Requests\CreateOrUpdateProductAndServiceRequest;
use App\Http\Resources\ProductAndServiceResource;
use App\Models\ProductAndService;
use Illuminate\Support\Facades\Gate;

class ProductAndServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(ProductAndService $productAndService)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateOrUpdateProductAndServiceRequest $request, ProductAndService $productAndService, SaveProductAndService $saveProductAndService)
    {
        Gate::authorize('update', $productAndService);

        $productAndService = $saveProductAndService->execute($productAndService, $request->validated());

        return new ProductAndServiceResource($productAndService);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductAndService $productAndService)
    {
        Gate::authorize('delete', $productAndService);

        $productAndService->delete();

        return response()->noContent();
    }
}
