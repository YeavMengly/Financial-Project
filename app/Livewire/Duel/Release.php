<?php

namespace App\Livewire\Duel;

use App\Models\Duel\DuelRelease;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class Release extends Component
{
    use WithFileUploads;

    public $att_id = 0;
    public $params = 0;
    public $file = " ";
    public $DuelReleaseOldFile = " ";

    public function mount($id)
    {
        $id = decode_params($id);
        $DuelRelease = DuelRelease::where("id", $id)->first();
        $this->att_id = $DuelRelease->id;
        $this->DuelReleaseOldFile = $DuelRelease->file;
    }

    public function render()
    {
        return view('livewire.duel.duelRelease');
    }

    public function save()
    {

        $validated = $this->validate([
            'file'   => 'required|file|max:51200',
        ], [
            "file" => [
                "required" => "ជ្រើសរើស File ឯកសារ",
                "max" => "File ឯកសារត្រូវតែតូចជាងទំហំ 10MB"
            ]
        ], [
            "file" => __("forms.document.file")
        ]);
        $path_store = "uploads/duel/duelRelease/" . date("Y-m-d");
        // delete old file
        if (!File::exists($path_store)) {
            File::makeDirectory($path_store, 0777, true, true);
        }
        $last_file = $this->file->store($path_store);
        if (!empty($this->DuelReleaseOldFile) && File::exists($this->DuelReleaseOldFile)) {
            File::delete($this->DuelReleaseOldFile);
        }
        DB::beginTransaction();

        try {
            $updateDoc = DuelRelease::findOrFail($this->att_id);
            $updateDoc->update([
                "file" => $last_file
            ]);
            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('duelRelease.index', [
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

            return redirect()->route('duelRelease.index', [
                'params' => $this->params
            ]);
        }
    }
}
