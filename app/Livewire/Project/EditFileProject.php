<?php

namespace App\Livewire\Project;

use App\Models\Material\Projects;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditFileProject extends Component
{
    use WithFileUploads;

    public $doc_id = 0;
    public $params = "";

    // Separate property purely for the new file upload
    public $newDocumentFile = null;
    public $documentOldFile = "";

    public function mount($id)
    {
        $id = decode_params($id);
        $project = Projects::where("id", $id)->first();
        if ($project) {
            $this->doc_id = $project->id;
            $this->documentOldFile = $project->file;
        }
    }

    public function render()
    {
        return view('livewire.project.edit-file-project');
    }

    public function save()
    {
        $this->validate([
            'newDocumentFile' => 'required|file|max:51200',
        ], [
            'newDocumentFile.required' => 'ជ្រើសរើស File ឯកសារ',
            'newDocumentFile.max' => 'File ឯកសារត្រូវតែតូចជាងទំហំ 50MB',
            'newDocumentFile.file' => 'ឯកសារមិនត្រឹមត្រូវ',
        ]);

        DB::beginTransaction();

        try {

            $path_store = 'uploads/project/' . date('Y-m-d');

            // Get extension only
            // $extension = strtolower(
            //     $this->newDocumentFile->getClientOriginalExtension()
            // );

            // // Generate a SHORT safe filename
            // $shortFileName = uniqid('', true) . '.' . $extension;

            // Store with short filename
            $last_file = $this->newDocumentFile->store(
                $path_store,

                'public'
            );

            if (!$last_file) {
                throw new \Exception('File upload failed.');
            }

            // Update database
            $updateDoc = Projects::findOrFail($this->doc_id);

            $oldFile = $this->documentOldFile;

            $updateDoc->update([
                'file' => $last_file,
            ]);

            // Delete old file AFTER database update succeeds
            if (!empty($oldFile)) {
                $oldPath = ltrim($oldFile, '/');

                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('project.index', [
                'params' => $this->params,
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Project file upload error', [
                'message' => $e->getMessage(),
            ]);

            // Delete newly uploaded file if DB update failed
            if (
                !empty($last_file) &&
                Storage::disk('public')->exists($last_file)
            ) {

                Storage::disk('public')->delete($last_file);
            }

            flash()
                ->translate('kh')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('project.index', [
                'params' => $this->params,
            ]);
        }
    }
}
