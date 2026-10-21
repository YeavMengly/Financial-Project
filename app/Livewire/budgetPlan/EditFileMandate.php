<?php

namespace App\Livewire\BudgetPlan;

use App\Models\BudgetPlan\BudgetMandate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditFileMandate extends Component
{
    use WithFileUploads;

    public $att_id = 0;

    public $params = 0;
    public $attachments = " ";
    public $budgetMandateOldFile = " ";

    public function mount($params, $id)
    {
        $this->params = $params;
        $decodedId = decode_params($id);

        $budgetMandate = BudgetMandate::where("ministry_id", decode_params($params))
            ->where("id", $decodedId)->firstOrFail();

        $this->att_id = $budgetMandate->id;
        $this->budgetMandateOldFile = $budgetMandate->attachments;
    }

    public function render()
    {
        return view('livewire.budgetPlan.edit-file-mandate');
    }

    public function save()
    {

        $validated = $this->validate([
            'attachments'   => 'required|file|max:51200',
        ], [
            "attachments" => [
                "required" => "ជ្រើសរើស File ឯកសារ",
                "max" => "File ឯកសារត្រូវតែតូចជាងទំហំ 10MB"
            ]
        ], [
            "attachments" => __("forms.document.file")
        ]);

        $path_store = "uploads/mandate/" . date("Y-m-d");
        if (!File::exists($path_store)) {
            File::makeDirectory($path_store, 0777, true, true);
        }
        $last_file = $this->attachments->store($path_store, 'public');
        if (!empty($this->budgetMandateOldFile) && Storage::disk('public')->exists($this->budgetMandateOldFile)) {
            Storage::disk('public')->delete($this->budgetMandateOldFile);
        }

        DB::beginTransaction();

        try {
            $updateDoc = BudgetMandate::findOrFail($this->att_id);
            $updateDoc->update([
                "attachments" => $last_file
            ]);
            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('budgetMandate.index', [
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

            return redirect()->route('budgetMandate.index', [
                'params' => $this->params
            ]);
        }
    }
}
