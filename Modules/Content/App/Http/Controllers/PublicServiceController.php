<?php

namespace Modules\Content\App\Http\Controllers;

use App\DataTables\Content\PublicServiceDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\PublicService;
use App\Models\Revenue\PublicServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublicServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PublicServiceDataTable $dataTable)
    {
        return $dataTable->render('content::content.publicServices.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $publicServiceCategory = PublicServiceCategory::where('service_is_ministry', '=', 1)->get();

        return view('content::content.publicServices.create')
            ->with('publicServiceCategory', $publicServiceCategory);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'cboCate'              => ['required', 'exists:public_service_categories,id'],
            'service_code'          => ['required', 'string', 'max:255'],
            // 'service_prefix_name'   => ['nullable', 'string', 'max:255'],
            'service_name'          => ['required', 'string', 'max:255'],
            'service_price'         => ['required', 'numeric', 'min:0'],
            'max_service_time'      => ['required', 'integer', 'min:0'],
            'expired'               => ['required', 'in:0,1'],
            'percentage_national'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'percentage_mlvt'      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'percentage_interior'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'percentage_mef'       => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        DB::beginTransaction();

        try {
            PublicService::create([
                'categories_id' => $validated['cboCate'],
                ''
            ]);

            DB::commit(); // Commit the transaction

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('publicServices.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('publicServices.index');
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('content::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('content::publicServices.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
