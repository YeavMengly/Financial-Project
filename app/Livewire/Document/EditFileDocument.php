<?php

namespace App\Livewire\Document;

use App\Models\Document;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditFileDocument extends Component
{
    use WithFileUploads;
    public $doc_id = 0;

    public $documentTitle = "";

    public $documentFile = "";
    public $documentOldFile = "";

    public function mount($params) {
        $id = decode_params($params);
        $document = Document::where("id", $id)->first();
        $this->doc_id = $document->id;
        $this->documentTitle = $document->title;
        $this->documentOldFile = $document->fileName;
    }
    public function render()
    {
        return view('livewire.document.edit-file-document');
    }

    public function save() {
        $validated = $this->validate([
            'documentFile'   => 'required|file|max:51200',
        ],[
            "documentFile" => [
                "required" => "ជ្រើសរើស File ឯកសារ",
                "max" => "File ឯកសារត្រូវតែតូចជាងទំហំ 10MB"
            ]
        ],[
            "documentFile" => __("forms.document.file")
        ]);
        $path_store = "uploads/document/".date("Y-m-d");
        if(!File::exists($path_store)) {
            File::makeDirectory($path_store, 0777, true, true);
        }
        $last_file = $this->documentFile->store($path_store);
        unlink($this->documentOldFile);
        DB::beginTransaction();
        try {
            $updateDoc = Document::findOrfail($this->doc_id);
            $updateDoc->update([
                "fileName" => $last_file
            ]);
            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();
            return redirect()->route('document.index');
        } catch (\Exception $e) {
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
