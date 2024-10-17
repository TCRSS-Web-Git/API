<?php

namespace App\Http\Controllers;

use App\Actions\ReorderAnnualReport;
use App\Actions\SaveAnnualReport;
use App\Filters\AnnualReportFilter;
use App\Http\Requests\CreateOrUpdateAnnualReportRequest;
use App\Http\Requests\ReorderAnnualReportRequest;
use App\Http\Resources\AnnualReportResource;
use App\Models\AnnualReport;
use Illuminate\Support\Facades\Gate;

class AnnualReportController extends Controller
{
    /**
     * Get all product and services
     *
     * @group Product and services
     */
    public function index(AnnualReportFilter $filter)
    {
        $count = AnnualReport::count();

        return AnnualReportResource::collection(AnnualReport::with(['latestAudit.user'])->filter($filter)->paginate($this->getPerPage($count, $count)));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @group Product and services
     */
    public function store(CreateOrUpdateAnnualReportRequest $request, SaveAnnualReport $saveAnnualReport)
    {
        Gate::authorize('create', AnnualReport::class);

        $productAndService = $saveAnnualReport->execute(new AnnualReport, $request->validated());

        return new AnnualReportResource($productAndService);
    }

    /**
     * Display the specified resource.
     *
     * @group Product and services
     */
    public function show(AnnualReport $annualReport)
    {
        return new AnnualReportResource($annualReport);
    }

    /**
     * Update the specified resource in storage.
     *
     * @group Product and services
     */
    public function update(CreateOrUpdateAnnualReportRequest $request, AnnualReport $annualReport, SaveAnnualReport $saveAnnualReport)
    {
        Gate::authorize('update', $annualReport);

        $annualReport = $saveAnnualReport->execute($annualReport, $request->validated());

        return new AnnualReportResource($annualReport);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @group Product and services
     */
    public function destroy(AnnualReport $annualReport)
    {
        Gate::authorize('delete', $annualReport);

        $annualReport->delete();

        return response()->noContent();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @group Product and services
     */
    public function reorder(ReorderAnnualReportRequest $request, ReorderAnnualReport $reorderAnnualReport)
    {
        Gate::authorize('reorder', AnnualReport::class);

        $reorderAnnualReport->execute($request->validated());

        return response()->json();
    }
}
