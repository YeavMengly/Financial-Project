<?php

namespace Modules\Content\App\Http\Controllers;

use App\DataTables\AnnualOpen\InitialChapterDataTable;
use App\DataTables\Content\ChapterDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\Ministry;
use App\Models\Content\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\DB;

class ChapterController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function getIndex(InitialChapterDataTable $dataTable)
    {
        return $dataTable->render('content::content.chapters.initialChapter.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ChapterDataTable $dataTable, $params)
    {
        $id  = decode_params($params);
        $module = Ministry::where('id', $id)->first();

        return $dataTable->render('content::content.chapters.index', [
            'params' => $params,
            'module' => $module
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $module = Ministry::where('id', decode_params($params))->first();

        return view('content::content.chapters.create')
            ->with('params', $params)
            ->with('module', $module);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        $request->validate([
            'no' => 'required',
            'name' => 'required',
        ]);

        $id = decode_params($params);

        DB::beginTransaction();

        try {
            $ministry = Ministry::where('id', $id)->first();

            Chapter::create([
                'ministry_id' => $ministry->id,
                'no' => $request->no,
                'name' => $request->name,
            ]);

            DB::commit(); // Commit the transaction

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('chapters.index', $params);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('chapters.index', $params);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params, $id)
    {
        $ministry = Ministry::where('id', decode_params($params))->first();
        $chapter = Chapter::where('id', decode_params($id))
            ->where('ministry_id', $ministry->id)->first();

        return view('content::content.chapters.edit')
            ->with('chapter', $chapter)
            ->with('params', $params)
            ->with('ministry', $ministry);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $id)
    {
        $validateData = $request->validate([
            'no' => ['required'],
            'name' => ['required'],
        ]);

        DB::beginTransaction();

        try {

            $chapter = Chapter::findOrfail($id);

            $chapter->update([
                'no' => $validateData['no'],
                'name' => $validateData['name'],
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('chapters.index', $params);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('chapters.index', $params);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params, $id)
    {
        $id = decode_params($id);
        $chapter = Chapter::where('id', $id)->first();
        $chapter->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('chapters.index', $params);
    }

    public function restore($params, $id)
    {
        $pid = decode_params($id);

        Chapter::withTrashed()->whereKey($pid)->restore();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('chapters.index', $params);
    }
}
