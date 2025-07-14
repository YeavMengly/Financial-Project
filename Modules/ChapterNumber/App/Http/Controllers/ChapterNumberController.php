<?php

namespace Modules\ChapterNumber\App\Http\Controllers;

use App\DataTables\ChapterNumberDataTable;
use App\Http\Controllers\Controller;
use App\Models\ChapterNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\DB;

class ChapterNumberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ChapterNumberDataTable $dataTable)
    {
        $chapterNumber = ChapterNumber::all();
        return $dataTable->render('chapternumber::index', ['chapterNumber' => $chapterNumber]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('chapternumber::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'chapterNumber' => 'required|unique:chapter_numbers,chapterNumber',
            'txtChapter' => 'required|unique:chapter_numbers,txtChapter',
        ]);

        try {
            ChapterNumber::create([
                'chapterNumber' => $request->chapterNumber,
                'txtChapter' => $request->txtChapter,
            ]);
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();
            if ($request->submit == 'save') {
                return redirect()->route('chapternumber.index');
            }
        } catch (Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();

            return redirect()->route('chapternumber.index');
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('chapternumber::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params)
    {
        $id = decode_params($params);
        $data = ChapterNumber::where('id', $id)->first();

        return view('chapternumber::edit')->with('data', $data)->with('params', $params);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params)
    {
        //
        $request->validate([
            'chapterNumber' => 'required|unique:chapter_numbers,chapterNumber',
            'txtChapter' => 'required|unique:chapter_numbers,txtChapter',
        ]);

        $id  = decode_params($params);
        $chapterNumber = ChapterNumber::where('id', $id)->first();

        $chapterNumber->update(['code', 'name']);
        return redirect()->route('chapternumber.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params)
    {
        //
        $id = decode_params($params);
        $chapterNumber = ChapterNumber::where('id', $id)->first();
        $chapterNumber->delete();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();
        return redirect()->route('chapternumber.index');
    }
}
