<?php

namespace Modules\Document\App\Http\Controllers;

use App\DataTables\DocumentDataTable;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategorySub;
use App\Models\Document;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(DocumentDataTable $dataTable)
    {
        $category = Category::orderBy('order', 'ASC')->get();

        return $dataTable->render('document::index', ['category' => $category]);
    }

    public function getByCategoryId(Request $request)
    {
        echo '<option value="">ជ្រើសរើស អនុប្រភេទ</option>';
        if ($request->cate_id != '') {
            $data = CategorySub::select('id', 'name')->where('cate_id', $request->cate_id)->get();
            foreach ($data as $d) {
                echo '<option value="' . $d->id . '">' . $d->name . '</option>';
            }
        }
    }

    public function destroy($params)
    {
        $id = decode_params($params);
        $document = Document::where('id', $id)->first();
        $document->delete();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('document.index');
    }

    public function restore($params)
    {
        $id = decode_params($params);
        $document = Document::withTrashed()->where('id', $id)->first();
        $document->restore();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('document.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cboCategory' => ['required'],
            'cboSub' => ['required'],
            'txtTitle' => 'required',
            'txtYear' => 'required|numeric|min:1',
            'txtDescription' => 'required',
            'documentFile' => 'required|file|max:51200',
        ]);

        if ($request->file('documentFile')->isValid()) {
            DB::beginTransaction();
            try {
                $path_store = 'uploads/document/' . date('Y-m-d');
                if (! File::exists($path_store)) {
                    File::makeDirectory($path_store, 0777, true, true);
                }
                $filePath = $request->file('documentFile')->store($path_store);

                Document::create([
                    'user_id' => auth()->user()->id,
                    'cate_id' => $request->cboCategory,
                    'sub_id' => $request->cboSub,
                    'year' => $request->txtYear,
                    'title' => $request->txtTitle,
                    'description' => $request->txtDescription,
                    'fileName' => $filePath,
                ]);

                DB::commit();
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->success('success_msg', 'successful')
                    ->flash();

                return redirect()->route('document.index');
            } catch (Exception $e) {
                DB::rollBack();
                $bug = $e->getMessage();
                Log::error($bug);
                flash()
                    ->translate('kh')
                    ->option('timeout', 2000)
                    ->error($bug, 'បញ្ហា')
                    ->flash();

                return redirect()->route('document.index');
            }
        } else {
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('File upload failed', 'បញ្ហា')
                ->flash();

            return redirect()->route('document.index');
        }
    }

    public function create()
    {
        $category = Category::orderBy('order', 'ASC')->get();

        return view('document::create')->with('category', $category);
    }

    public function edit($params)
    {
        $id = decode_params($params);
        $document = Document::where('id', $id)->first();
        $category = Category::orderBy('order', 'ASC')->get();
        $categorySub = CategorySub::where('cate_id', $document->cate_id)->orderBy('order', 'ASC')->get();

        return view('document::edit')
            ->with('params', $params)
            ->with('document', $document)
            ->with('category', $category)
            ->with('categorySub', $categorySub);
    }

    public function update(Request $request, $params)
    {
        $request->validate([
            'cboCategory' => ['required'],
            'cboSub' => ['required'],
            'txtTitle' => 'required',
            'txtYear' => 'required|numeric|min:1',
            'txtDescription' => 'required',
        ]);
        $id = decode_params($params);
        DB::beginTransaction();
        try {
            $updateDoc = Document::where('id', $id)->first();
            $updateDoc->update([
                'cate_id' => $request->cboCategory,
                'sub_id' => $request->cboSub,
                'year' => $request->txtYear,
                'title' => $request->txtTitle,
                'description' => $request->txtDescription,
            ]);
            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('document.index');
        } catch (Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('kh')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();

            return redirect()->route('document.index');
        }
    }
}
