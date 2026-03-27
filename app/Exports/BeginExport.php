<?php

namespace App\Exports;

use App\Models\Content\Account;
use App\Models\Content\AccountSub;
use App\Models\BeginCredit\BeginVoucher;
use App\Models\Content\Ministry;
use App\Models\Content\Chapter;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Http\Request;

class BeginExport
{
    protected $data;
    protected $ministryId;
    public function __construct($data, $ministryId)
    {
        $this->data = $data;
        $this->ministryId = $ministryId;
    }

    public function export(Request $request)
    {

        $params =  $request->params;
        $id = decode_params($params);

        $templatePath = public_path('templatevoucher.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $currentMonth = date('m');
        $currentYear = date('Y');

        $ministry = Ministry::where('id', $id)->first();

        $dateRangeText = 'ប្រចាំ​ ខែ ' . $currentMonth . ' ឆ្នាំ ' . $ministry->year;

        $row = 10;
        $sheet->getStyle("A{$row}:T{$row}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],

        ]);

        $sheet->setCellValue("A{$row}", $dateRangeText);
        $sheet->mergeCells("A{$row}:T{$row}");
        $row = 14;
        $grouped = $this->data
            ->sortBy(['chapter_id', 'account_id', 'account_sub_id', 'no'])
            ->groupBy('chapter_id')
            ->map(function ($chapterGroup) {
                return $chapterGroup->groupBy('account_id')
                    ->map(function ($accountGroup) {
                        return $accountGroup->groupBy('account_sub_id');
                    });
            });

        $chapterId = $this->data->pluck('chapter_id')->filter()->unique();
        $accountId = $this->data->pluck('account_id')->filter()->unique();
        $accountSubId     = $this->data->pluck('account_sub_id')->filter()->unique();

        $ministry = Ministry::where('id', $id)->first();
        $chapterMap = Chapter::where('ministry_id', $ministry->id)
            ->whereIn('no', $chapterId)
            ->get()
            ->keyBy('no');

        $accountMap = Account::where('ministry_id', $ministry->id)
            ->whereIn('no', $accountId)
            ->get()
            ->keyBy('no');

        $accountSubMap = AccountSub::where('ministry_id', $ministry->id)
            ->whereIn('no', $accountSubId)
            ->get()
            ->keyBy('no');

        // foreach ($grouped as $chapterNo => $accounts) {

        //     $chapter = $chapterMap->get($chapterNo);

        //     $chapterTotals = $this->initTotals();

        //     $chapterRow = $row;
        //     $sheet->setCellValue("A{$chapterRow}", $chapterNo);
        //     $sheet->setCellValue("E{$chapterRow}", $chapter?->name);
        //     $row++;

        //     foreach ($accounts as $accountNo => $subAccounts) {

        //         $account = $accountMap->get($accountNo);
        //         $accountTotals = $this->initTotals();

        //         $accountRow = $row;
        //         $sheet->setCellValue("B{$accountRow}", $accountNo);
        //         $sheet->setCellValue("E{$accountRow}", $account?->name);
        //         $row++;

        //         foreach ($subAccounts as $accountSubNo => $items) {

        //             $accountSub = $accountSubMap->get($accountSubNo);
        //             $subTotals = $this->initTotals();

        //             $subRow = $row;
        //             $sheet->setCellValue("C{$subRow}", $accountSubNo);
        //             $sheet->setCellValue("E{$subRow}", $accountSub?->name);
        //             $row++;

        //             foreach ($items as $item) {

        //                 $sheet->setCellValue("D{$row}", $item->no);
        //                 $sheet->setCellValue("E{$row}", $item->txtDescription);
        //                 $sheet->setCellValue("F{$row}", $item->fin_law);
        //                 $sheet->setCellValue("G{$row}", $item->current_loan);
        //                 $internal   = $item->loan_internal_increase   ?? 0;
        //                 $unexpected = $item->loan_unexpected_increase ?? 0;
        //                 $additional = $item->loan_additional_increase ?? 0;
        //                 $totalInc   = $item->loan_total_increase      ?? ($internal + $unexpected + $additional);
        //                 $decrease   = $item->loan_decrease            ?? 0;
        //                 $editorial  = $item->loan_editorial           ?? 0;

        //                 $sheet->setCellValue("H{$row}", $internal);
        //                 $sheet->setCellValue("I{$row}", $unexpected);
        //                 $sheet->setCellValue("J{$row}", $additional);
        //                 $sheet->setCellValue("K{$row}", $totalInc);
        //                 $sheet->setCellValue("L{$row}", $decrease);
        //                 $sheet->setCellValue("M{$row}", $editorial);

        //                 $sheet->setCellValue("N{$row}", $item->new_credit_status);
        //                 $sheet->setCellValue("O{$row}", $item->early_balance);
        //                 $sheet->setCellValue("P{$row}", $item->apply);
        //                 $sheet->setCellValue("Q{$row}", $item->deadline_balance);
        //                 $sheet->setCellValue("R{$row}", $item->credit);
        //                 $sheet->setCellValue("S{$row}", $item->law_average / 100);
        //                 $sheet->setCellValue("T{$row}", $item->law_correction / 100);
        //                 // $sheet->setCellValue("U{$row}", $item->agency_id);
        //                 $values = [
        //                     'fin_law'            => (float) $item->fin_law,
        //                     'current_loan'       => (float) $item->current_loan,
        //                     'internal_increase'  => (float) $internal,
        //                     'unexpected_increase' => (float) $unexpected,
        //                     'additional_increase' => (float) $additional,
        //                     'total_increase'     => (float) $totalInc,
        //                     'decrease'           => (float) $decrease,
        //                     'editorial'          => (float) $editorial,
        //                     'new_credit_status'  => (float) $item->new_credit_status,
        //                     'early_balance'      => (float) $item->early_balance,
        //                     'apply'              => (float) $item->apply,
        //                     'deadline_balance'   => (float) $item->deadline_balance,
        //                     'credit'             => (float) $item->credit,
        //                     'law_average'        => (float) $item->law_average / 100,
        //                     'law_correction'     => (float) $item->law_correction / 100,
        //                 ];
        //                 $this->addToTotals($subTotals,     $values);
        //                 $this->addToTotals($accountTotals, $values);
        //                 $this->addToTotals($chapterTotals, $values);

        //                 $row++;
        //             }
        //             $this->writeTotalsRow($sheet, $subRow, $subTotals);
        //         }
        //         $this->writeTotalsRow($sheet, $accountRow, $accountTotals);
        //     }
        //     $this->writeTotalsRow($sheet, $chapterRow, $chapterTotals);
        // }

        $totalsStyleArray = [
            'font' => [
                'bold' => true,
                'size' => 8
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => '00FF00',
                ],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        $fileName = 'template.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function initTotals(): array
    {
        return [
            'fin_law'            => 0,
            'current_loan'       => 0,
            'internal_increase'  => 0,
            'unexpected_increase' => 0,
            'additional_increase' => 0,
            'total_increase'     => 0,
            'decrease'           => 0,
            'editorial'          => 0,
            'new_credit_status'  => 0,
            'early_balance'      => 0,
            'apply'              => 0,
            'deadline_balance'   => 0,
            'credit'             => 0,
            'law_average'        => 0,
            'law_correction'     => 0,
        ];
    }

    private function addToTotals(array &$totals, array $values): void
    {
        foreach ($totals as $key => $v) {
            if (isset($values[$key])) {
                $totals[$key] += $values[$key];
            }
        }
    }

    private function writeTotalsRow(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, int $row, array $totals): void
    {
        $sheet->setCellValue("F{$row}", $totals['fin_law']);
        $sheet->setCellValue("G{$row}", $totals['current_loan']);
        $sheet->setCellValue("H{$row}", $totals['internal_increase']);
        $sheet->setCellValue("I{$row}", $totals['unexpected_increase']);
        $sheet->setCellValue("J{$row}", $totals['additional_increase']);
        $sheet->setCellValue("K{$row}", $totals['total_increase']);
        $sheet->setCellValue("L{$row}", $totals['decrease']);
        $sheet->setCellValue("M{$row}", $totals['editorial']);
        $sheet->setCellValue("N{$row}", $totals['new_credit_status']);
        $sheet->setCellValue("O{$row}", $totals['early_balance']);
        $sheet->setCellValue("P{$row}", $totals['apply']);
        $sheet->setCellValue("Q{$row}", $totals['deadline_balance']);
        $sheet->setCellValue("R{$row}", $totals['credit']);
        $sheet->setCellValue("S{$row}", $totals['law_average']);
        $sheet->setCellValue("T{$row}", $totals['law_correction']);
    }
}
