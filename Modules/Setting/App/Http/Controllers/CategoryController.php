<?php

namespace Modules\Setting\App\Http\Controllers;

use App\DataTables\CategoryDataTable;
use App\DataTables\CategorySubDataTable;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategorySub;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CategoryDataTable $dataTable)
    {
        return $dataTable->render('setting::category.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'txtCategory' => ['required'],
            'txtOrder' => ['required', 'integer', 'min:1'],
        ]);
        DB::beginTransaction();
        try {
            Category::create([
                'name' => $request->txtCategory,
                'order' => $request->txtOrder,
            ]);
            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();
            if ($request->submit == 'save') {
                return redirect()->route('category.index');
            }

            return redirect()->route('category.create');
        } catch (Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();

            return redirect()->route('category.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('setting::category.create');
    }

    public function edit($id)
    {
        $module = Category::where('id', $id)->first();

        return view('setting::category.edit')
            ->with('module', $module);
    }

    public function destroy($id)
    {
        $category = Category::where('id', $id)->first();
        $category->delete();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('category.index');
    }

    public function subIndex(CategorySubDataTable $dataTable, $cateId)
    {
        $module = Category::where('id', $cateId)->first();

        return $dataTable->with('cateId', $cateId)
            ->render('setting::category.sub.index', ['module' => $module]);
    }

    public function subCreate($cateId)
    {
        $module = Category::where('id', $cateId)->first();

        return view('setting::category.sub.create')->with('module', $module);
    }

    public function subStore(Request $request, $cateId)
    {
        $request->validate([
            'txtCategory' => ['required'],
            'txtOrder' => ['required', 'integer', 'min:0'],
        ]);
        DB::beginTransaction();
        try {
            CategorySub::create([
                'cate_id' => $cateId,
                'name' => $request->txtCategory,
                'order' => $request->txtOrder,
            ]);
            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();
            if ($request->submit == 'save') {
                return redirect()->route('category.sub.index', $cateId);
            }

            return redirect()->route('category.sub.create', $cateId);
        } catch (Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();

            return redirect()->route('category.sub.index', $cateId);
        }
    }

    public function subEdit($cateId, $id)
    {
        $module = Category::where('id', $cateId)->first();
        $sub = CategorySub::where('id', $id)->first();

        return view('setting::category.sub.edit')
            ->with('module', $module)
            ->with('sub', $sub);
    }

    public function subUpdate(Request $request, $cateId, $id)
    {
        $request->validate([
            'txtCategory' => ['required'],
            'txtOrder' => ['required', 'integer', 'min:0'],
        ]);

        DB::beginTransaction();
        try {
            CategorySub::where('id', $id)
                ->where('cate_id', $cateId)
                ->update([
                    'name' => $request->txtCategory,
                    'order' => $request->txtOrder,
                ]);
            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('category.sub.index', $cateId);
        } catch (Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();

            return redirect()->route('category.sub.index', $cateId);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'txtCategory' => ['required'],
            'txtOrder' => ['required', 'integer', 'min:0'],
        ]);

        DB::beginTransaction();
        try {
            $category = Category::findOrfail($id);
            $category->update([
                'name' => $request->txtCategory,
                'order' => $request->txtOrder,
            ]);
            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('category.index');
        } catch (Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();

            return redirect()->route('category.index');
        }
    }

    public function subDestroy($cateId, $id)
    {
        $categorySub = CategorySub::where('id', $id)
            ->where('cate_id', $cateId)->first();
        $categorySub->delete();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('category.sub.index', $cateId);
    }

    public function subRestore($cateId, $id)
    {
        CategorySub::withTrashed()->find($id)->restore();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('category.sub.index', $cateId);
    }

    public function restore($id)
    {
        Category::withTrashed()->find($id)->restore();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('category.index');
    }
}
