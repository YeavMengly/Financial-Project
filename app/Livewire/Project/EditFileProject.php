<?php

namespace App\Livewire\Project;

use App\Models\Material\Projects;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Throwable;

class EditFileProject extends Component
{
    use WithFileUploads;

    public $doc_id = 0;
    public $params = '';

    public $newDocumentFile = null;
    public $documentOldFile = '';

    public function mount($params, $id)
    {
        $this->params = $params;

        $id = decode_params($id);

        $project = Projects::where("id", $id)->first();

        abort_unless($project, 404);

        $this->doc_id = $project->id;
        $this->documentOldFile = $project->file;
    }

    public function render()
    {
        return view('livewire.project.edit-file-project');
    }

    public function save()
    {
        $this->validate([
            'newDocumentFile' => [
                'required',
                'file',
                'mimes:pdf',
                'max:51200',
            ],
        ], [
            'newDocumentFile.required' =>
            'ជ្រើសរើស File ឯកសារ',

            'newDocumentFile.file' =>
            'ឯកសារមិនត្រឹមត្រូវ',

            'newDocumentFile.mimes' =>
            'សូមជ្រើសរើសឯកសារ PDF ប៉ុណ្ណោះ',

            'newDocumentFile.max' =>
            'File ឯកសារត្រូវតែតូចជាង ឬស្មើ 50MB',
        ]);

        $disk = Storage::disk('local');

        $last_file = null;
        $oldFile = $this->documentOldFile;

        try {
            // 1. Store the new PDF in storage/app/uploads
            $path_store = 'uploads/project/' . now()->format('Y-m-d');

            $last_file = $this->newDocumentFile->store(
                $path_store,
                'local'
            );

            if (!$last_file) {
                throw new \RuntimeException(
                    'File upload failed.'
                );
            }

            // 2. Update the database transactionally
            DB::transaction(function () use ($last_file) {
                $project = Projects::findOrFail($this->doc_id);

                $project->update([
                    'file' => $last_file,
                ]);
            });

            // 3. Delete the old file after DB success.
            // Check both disks for files uploaded by the old code.
            if (!empty($oldFile) && $oldFile !== $last_file) {
                try {
                    $oldPath = ltrim($oldFile, '/');

                    if ($disk->exists($oldPath)) {
                        $disk->delete($oldPath);
                    } elseif (
                        Storage::disk('public')->exists($oldPath)
                    ) {
                        Storage::disk('public')->delete($oldPath);
                    }
                } catch (Throwable $deleteError) {
                    // The new file and DB update already succeeded.
                    Log::warning('Could not delete old project file', [
                        'project_id' => $this->doc_id,
                        'old_file' => $oldFile,
                        'message' => $deleteError->getMessage(),
                    ]);
                }
            }

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('project.index', [
                'params' => $this->params,
            ]);
        } catch (Throwable $e) {
            // 4. Remove the new file if the operation failed.
            if ($last_file && $disk->exists($last_file)) {
                $disk->delete($last_file);
            }

            Log::error('Project file upload error', [
                'project_id' => $this->doc_id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            flash()
                ->translate('kh')
                ->option('timeout', 2000)
                ->error(
                    'មិនអាចរក្សាទុកឯកសារបានទេ។ សូមព្យាយាមម្ដងទៀត។',
                    'បញ្ហា'
                )
                ->flash();

            return redirect()->route('project.index', [
                'params' => $this->params,
            ]);
        }
    }
}
