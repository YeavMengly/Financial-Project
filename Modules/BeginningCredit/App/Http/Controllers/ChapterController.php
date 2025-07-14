<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\ChapterDataTable;
use App\Http\Controllers\Controller;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\DB;

class ChapterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ChapterDataTable $dataTable)
    {
        $chapter =  Chapter::all();

        // dd($chapter);

        return $dataTable->render('beginningcredit::chapter.index', ['chapter' => $chapter]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('beginningcredit::chapter.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'chapterNumber' => 'required|unique:chapters,chapterNumber',
            'txtChapter' => 'required|unique:chapters,txtChapter',
        ]);

        DB::beginTransaction();
        try {
            Chapter::create([
                'chapterNumber' => $request->chapterNumber,
                'txtChapter' => $request->txtChapter,
            ]);

            DB::commit(); // Commit the transaction

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('chapter.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('chapter.index');
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('chapter.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params)
    {
        $id = decode_params($params);
        $data = Chapter::where('id', $id)->first();

        return view('beginningcredit::chapter.edit')->with('data', $data)->with('params', $params);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params)
    {
        $request->validate([
            'chapterNumber' => 'required',
            'txtChapter' => 'required',
        ]);

        $id  = decode_params($params);
        $chapter = Chapter::where('id', $id)->first();

        $chapter->update([
            'chapterNumber' => $request->chapterNumber,
            'txtChapter' => $request->txtChapter,
        ]);

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('success_msg', 'successful')
            ->flash();

        return redirect()->route('chapter.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params)
    {
        $id = decode_params($params);
        $chapterNumber = Chapter::where('id', $id)->first();
        $chapterNumber->delete();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();
        return redirect()->route('chapter.index');
    }
}
