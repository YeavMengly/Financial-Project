<?php

namespace App\Livewire\BudgetPlan;

use App\Models\BudgetPlan\BudgetVoucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class payment extends Component
{
    use WithFileUploads;

    public $att_id = 0;
    public $params = 0;
    public $attachments = " ";
    public $budgetVoucherOldFile = " ";

    public function mount($id)
    {
        $id = decode_params($id);
        $budgetMandate = BudgetVoucher::where("id", $id)->first();
        //    dd( $id, $params, decode_params($params));

        $this->att_id = $budgetMandate->id;
        $this->budgetVoucherOldFile = $budgetMandate->attachments;
    }

    public function render()
    {
        return view('livewire.budgetPlan.payment');
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
        $path_store = "uploads/budgetPlan/payment/" . date("Y-m-d");
        // delete old file
        if (!File::exists($path_store)) {
            File::makeDirectory($path_store, 0777, true, true);
        }
        $last_file = $this->attachments->store($path_store);
        if (!empty($this->budgetVoucherOldFile) && File::exists($this->budgetVoucherOldFile)) {
            File::delete($this->budgetVoucherOldFile);
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
