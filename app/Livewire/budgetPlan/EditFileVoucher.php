<?php

namespace App\Livewire\BudgetPlan;

use App\Models\BudgetPlan\BudgetVoucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditFileVoucher extends Component
{
    use WithFileUploads;

    public $att_id = 0;
    
    public $params;
    public $documentTitle;
    public $attachments;
    public $budgetVoucherOldFile;

    public function mount($params, $id)
    {
        $this->params = $params;
        $decodedId = decode_params($id);

        $budgetMandate = BudgetVoucher::where("ministry_id", decode_params($params))
            ->where("id", $decodedId)
            ->firstOrFail();

        $this->att_id = $budgetMandate->id;
        $this->budgetVoucherOldFile = $budgetMandate->attachments;
    }

    public function render()
    {
        return view('livewire.budgetPlan.edit-file-voucher');
    }

    public function save()
    {
        $this->validate([
            'attachments' => 'required|file|max:51200', // 50MB max
        ], [
            "attachments.required" => "ជ្រើសរើស File ឯកសារ",
            "attachments.max" => "File ឯកសារត្រូវតែតូចជាងទំហំ 50MB"
        ], [
            "attachments" => __("forms.document.file")
        ]);

        $path_store = "uploads/voucher/" . date("Y-m-d");
        if (!File::exists($path_store)) {
            File::makeDirectory($path_store, 0777, true, true);
        }
        $last_file = $this->attachments->store($path_store, 'public');
        if (!empty($this->budgetVoucherOldFile) && Storage::disk('public')->exists($this->budgetVoucherOldFile)) {
            Storage::disk('public')->delete($this->budgetVoucherOldFile);
        }

        DB::beginTransaction();

        try {

            $updateDoc = BudgetVoucher::findOrFail($this->att_id);
            $updateDoc->update([
                "attachments" => $last_file
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('budgetVoucher.index', [
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

            return redirect()->route('budgetVoucher.index', [
                'params' => $this->params
            ]);
        }
    }
}
