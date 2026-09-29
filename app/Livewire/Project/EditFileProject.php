<?php

namespace App\Livewire\Project;

use App\Models\Material\Projects;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditFileProject extends Component
{
    use WithFileUploads;

    public $doc_id = 0;
    public $params = "";
    public $documentFile = "";
    public $documentOldFile = "";

    public function mount($id)
    {
        $id = decode_params($id);
        $project = Projects::where("id", $id)->first();
        $this->doc_id = $project->id;
        $this->documentOldFile = $project->file;
    }

    public function render()
    {
        return view('livewire.project.edit-file-project');
    }

    public function save()
    {
        $validated = $this->validate([
            'documentFile' => 'required|file|max:51200',
        ], [
            "documentFile" => [
                "required" => "ជ្រើសរើស File ឯកសារ",
                "max" => "File ឯកសារត្រូវតែតូចជាងទំហំ 10MB"
            ]
        ], [
            "documentFile" => __("forms.document.file")
        ]);

        $path_store = "uploads/project/" . date("Y-m-d");
        if (!File::exists($path_store)) {
            File::makeDirectory($path_store, 0777, true, true);
        }
        $last_file = $this->documentFile->store($path_store, 'public');
        if (!empty($this->documentOldFile) && Storage::disk('public')->exists($this->documentOldFile)) {
            Storage::disk('public')->delete($this->documentOldFile);
        }

        DB::beginTransaction();

        try {
            // $last_file = $this->documentFile->store($path_store, 'uploads');

            // if (!empty($this->documentOldFile) && trim($this->documentOldFile) !== '') {
            //     $oldFileClean = trim($this->documentOldFile);
            //     if (Storage::disk('uploads')->exists($oldFileClean)) {
            //         Storage::disk('uploads')->delete($oldFileClean);
            //     }
            // }

            $updateDoc = Projects::findOrFail($this->doc_id);
            $updateDoc->update([
                "file" => $last_file
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('project.index', [
                'params' => $this->params
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('kh')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('project.index', [
                'params' => $this->params
            ]);
        }
    }
}
