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
        $publicServiceCategory = PublicServiceCategory::where('service_is_ministry', '=', 1)
            ->where('parent', '=', 0)->get();

        return $dataTable->render(
            'content::content.publicServices.index',
            [
                'publicServiceCategory' =>
                $publicServiceCategory
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Retrieve all categories
        $allCategories = PublicServiceCategory::orderBy('code')->get();

        // Get main ministry categories (parent = 0)
        $mainCategories = $allCategories
            ->where('parent', 0)
            ->where('service_is_ministry', 1);

        // Build hierarchical category list
        $publicServiceCategory = collect();

        $buildTree = function ($categories, $level = 0) use (
            &$buildTree,
            $allCategories,
            $publicServiceCategory
        ) {
            foreach ($categories as $category) {

                $publicServiceCategory->push([
                    'id'   => $category->id,
                    'code' => $category->code,
                    'name' => $category->service_cate_name,
                    'level' => $level,
                ]);

                // Find children using parent ID
                $children = $allCategories
                    ->where('parent', $category->id)
                    ->sortBy('code', SORT_NATURAL);

                if ($children->isNotEmpty()) {
                    $buildTree($children, $level + 1);
                }
            }
        };

        $buildTree($mainCategories->sortBy('code', SORT_NATURAL));

        return view('content::content.publicServices.create')
            ->with('publicServiceCategory', $publicServiceCategory);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cboCate'             => 'required',
            'service_code'        => 'required',
            'service_name'        => 'required',
            'service_price'       => 'required',
            'max_service_time'    => 'required',
            'expired'             => 'required',
            'percentage_national'  => 'nullable|numeric',
            'percentage_mlvt'      => 'nullable|numeric',
            'percentage_interior'  => 'nullable|numeric',
            'percentage_mef'       => 'nullable|numeric',
        ]);

        DB::beginTransaction();

        try {

            $servicePrefix = PublicServiceCategory::where('id', $validated['cboCate'])->first();

            PublicService::create([
                'categories_id'       => $validated['cboCate'],
                'service_code'        => $validated['service_code'],
                'service_prefix_name'        => $servicePrefix->service_cate_name ?? null,
                'service_name'        => strip_tags($validated['service_name']),
                'service_price'       => $validated['service_price'],
                'max_service_time'    => $validated['max_service_time'],
                'expired'             => $validated['expired'],

                // Revenue Distribution
                'percentage_national' => $validated['percentage_national'] ?? 0,
                'percentage_mlvt'     => $validated['percentage_mlvt'] ?? 0,
                'percentage_interior' => $validated['percentage_interior'] ?? 0,
                'percentage_mef'      => $validated['percentage_mef'] ?? 0,
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('publicServices.index');
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Public Service Store Error: ' . $e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()
                ->back()
                ->withInput();
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
    public function edit($params)
    {

        $id =  decode_params($params);
        $publicService = PublicService::where('id', $id)->first();

        // dd($publicService);
        // Retrieve all categories
        $allCategories = PublicServiceCategory::orderBy('code')->get();

        // Get main ministry categories
        $mainCategories = $allCategories
            ->where('parent', 0)
            ->where('service_is_ministry', 1);

        // Build hierarchical category list
        $publicServiceCategory = collect();

        $buildTree = function ($categories, $level = 0) use (
            &$buildTree,
            $allCategories,
            $publicServiceCategory
        ) {
            foreach ($categories as $category) {

                $publicServiceCategory->push([
                    'id'    => $category->id,
                    'code'  => $category->code,
                    'name'  => $category->service_cate_name,
                    'level' => $level,
                ]);

                $children = $allCategories
                    ->where('parent', $category->id)
                    ->sortBy('code', SORT_NATURAL);

                if ($children->isNotEmpty()) {
                    $buildTree($children, $level + 1);
                }
            }
        };

        $buildTree($mainCategories->sortBy('code', SORT_NATURAL));

        return view('content::content.publicServices.edit')
            ->with('params', $params)
            ->with('publicService', $publicService)
            ->with('publicServiceCategory', $publicServiceCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params)
    {
        $validated = $request->validate([
            'cboCate'              => 'required|exists:public_service_categories,id',
            'service_code'         => 'required|string|max:255',
            'service_name'         => 'required|string',
            'service_price'        => 'required',
            'max_service_time'     => 'required',
            'expired'              => 'required',

            'percentage_national'  => 'nullable|numeric|min:0|max:100',
            'percentage_mlvt'      => 'nullable|numeric|min:0|max:100',
            'percentage_interior'  => 'nullable|numeric|min:0|max:100',
            'percentage_mef'       => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();

        try {

            $publicService = PublicService::where('id', decode_params($params))->first();

            // Retrieve selected category
            $servicePrefix = PublicServiceCategory::findOrFail(
                $validated['cboCate']
            );

            // Update existing service
            $publicService->update([
                'categories_id'       => $validated['cboCate'],
                'service_code'        => $validated['service_code'],
                'service_prefix_name' => $servicePrefix->service_cate_name,
                'service_name'        => strip_tags($validated['service_name']),
                'service_price'       => $validated['service_price'],
                'max_service_time'    => $validated['max_service_time'],
                'expired'             => $validated['expired'],

                // Revenue Distribution
                'percentage_national' => $validated['percentage_national'] ?? 0,
                'percentage_mlvt'     => $validated['percentage_mlvt'] ?? 0,
                'percentage_interior' => $validated['percentage_interior'] ?? 0,
                'percentage_mef'      => $validated['percentage_mef'] ?? 0,
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('publicServices.index');
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Public Service Update Error: ' . $e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()
                ->back()
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params)
    {
        DB::beginTransaction();

        try {

            // Find existing public service
            $publicService = PublicService::where('id', decode_params($params))->first();

            // Delete record
            $publicService->delete();

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('publicServices.index');
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Public Service Destroy Error: ' . $e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->back();
        }
    }
}
