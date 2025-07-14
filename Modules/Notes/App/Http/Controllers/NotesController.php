<?php

namespace Modules\Notes\App\Http\Controllers;

use App\DataTables\NotesDataTable;
use App\Http\Controllers\Controller;
use App\Models\Notes;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotesController extends Controller
{
    public function index(NotesDataTable $dataTable)
    {
        return $dataTable->render('notes::index');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'txtTitle' => ['required'],
            'txtDescription' => ['required'],
        ]);
        DB::beginTransaction();
        try {
            Notes::create([
                'title' => $request->txtTitle,
                'description' => $request->txtDescription,
                'status' => 'todo',
                'is_archived' => 1,
            ]);
            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();
            if ($request->submit == 'save') {
                return redirect()->route('notes.index');
            }

            return redirect()->route('notes.create');
        } catch (Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();

            return redirect()->route('notes.index');
        }
    }

    public function create()
    {
        return view('notes::create');
    }

    public function edit($params)
    {
        $id = decode_params($params);
        $data = Notes::where('id', $id)->first();

        return view('notes::edit')
            ->with('data', $data)
            ->with('params', $params);
    }

    public function update(Request $request, $params)
    {
        $request->validate([
            'txtTitle' => ['required'],
            'txtDescription' => ['required'],
            'cboTaks' => '',
        ]);
        DB::beginTransaction();
        try {
            $id = decode_params($params);
            $notes = Notes::where('id', $id)->first();
            $notes->update([
                'title' => $request->txtTitle,
                'description' => $request->txtDescription,
                'status' => 'todo',
                'is_archived' => ($request->cboTaks == '3') ? '2' : '1',
            ]);
            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('notes.index');
        } catch (Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();

            return redirect()->route('notes.index');
        }
    }

    public function destroy($params)
    {
        $id = decode_params($params);
        $notes = Notes::where('id', $id)->first();
        $notes->delete();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('notes.index');
    }

    public function restore($params)
    {
        $id = decode_params($params);
        $notes = Notes::withTrashed()->where('id', $id)->first();
        $notes->restore();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('notes.index');
    }
}
