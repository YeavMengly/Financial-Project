<?php

namespace App\Exports;

use App\Models\BeginCredit\Account;
use App\Models\BeginCredit\AccountSub;
use App\Models\BeginCredit\BeginVoucher;
use App\Models\BeginCredit\Ministry;
use App\Models\Chapter;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Http\Request;

class ReportBook
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

        $templatePath = public_path('template.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $currentMonth = date('m');
        $currentYear = date('Y');
        $dateRangeText = 'ប្រចាំ​ ខែ ' . $currentMonth . ' ឆ្នាំ ' . $currentYear;

        $row = 10;
        $col = 'D'; // start column for items
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

        $codeTotals = [
            'fin_law' => 0,
            'current_loan' => 0,
            'internal_increase' => 0,
            'unexpected_increase' => 0,
            'additional_increase' => 0,
            'decrease' => 0,
            'editorial' => 0,
            'new_credit_status' => 0,
            'early_balance' => 0,
            'apply' => 0,
            'deadline_balance' => 0,
            'credit' => 0,
            'law_average' => 0,
            'law_correction' => 0,
        ];

        // foreach ($grouped as $chapterNo => $accounts) {
        //     $chapter = $chapterMap->get($chapterNo);

        //     if ($chapter) {
        //         $sheet->setCellValue("A{$row}", $chapter->no);
        //         $sheet->setCellValue("E{$row}", $chapter->name);
        //     } else {
        //         $sheet->setCellValue("A{$row}", $chapterId);
        //         $sheet->setCellValue("E{$row}", '');
        //     }

        //     $sheet->setCellValue("A{$row}", $chapterNo);
        //     $sheet->setCellValue('E' . $row, $chapter->name);
        //     $sheet->setCellValue('F' . $row, $codeTotals['fin_law']);
        //     // $sheet->setCellValue('G' . $row, $codeTotals['current_loan']);
        //     // $sheet->setCellValue('H' . $row, $codeTotals['internal_increase']);
        //     // $sheet->setCellValue('I' . $row, $codeTotals['unexpected_increase']);
        //     // $sheet->setCellValue('J' . $row, $codeTotals['additional_increase']);
        //     // $sheet->setCellValue('K' . $row, $codeTotals['internal_increase'] + $codeTotals['unexpected_increase'] + $codeTotals['additional_increase']);
        //     // $sheet->setCellValue('L' . $row, $codeTotals['decrease']);
        //     // $sheet->setCellValue('M' . $row, $codeTotals['editorial']);
        //     // $sheet->setCellValue('N' . $row, $codeTotals['new_credit_status']);
        //     // $sheet->setCellValue('O' . $row, $codeTotals['early_balance']);
        //     // $sheet->setCellValue('P' . $row, $codeTotals['apply']);
        //     // $sheet->setCellValue('Q' . $row, $codeTotals['deadline_balance']);
        //     // $sheet->setCellValue('R' . $row, $codeTotals['credit']);
        //     // $sheet->setCellValue('S' . $row, $codeTotals['law_average']);
        //     // $sheet->setCellValue('T' . $row, $codeTotals['law_correction']);

        //     $row++;

        //     foreach ($accounts as $accountNo => $subAccounts) {

        //         $account = $accountMap->get($accountNo);

        //         $sheet->setCellValue("B{$row}", $accountNo);
        //         $sheet->setCellValue("E{$row}", $account->name);

        //         $row++;

        //         foreach ($subAccounts as $accountSubNo => $items) {

        //             $accountSub = $accountSubMap->get($accountSubNo);
        //             $sheet->setCellValue("C{$row}", $accountSubNo);
        //             $sheet->setCellValue("E{$row}", $accountSub->name);

        //             $row++;

        //             foreach ($items as $item) {


        //                 $sheet->setCellValue("D{$row}", $item->no);
        //                 $sheet->setCellValue("E{$row}", $item->txtDescription);
        //                 $sheet->setCellValue("F{$row}", $item->fin_law);
        //                 // $sheet->setCellValue("G{$row}", $item->current_loan);
        //                 // $sheet->setCellValue("N{$row}", $item->new_credit_status);
        //                 // $sheet->setCellValue("O{$row}", $item->apply);
        //                 // $sheet->setCellValue("P{$row}", $item->deadline_balance);
        //                 // $sheet->setCellValue("Q{$row}", $item->early_balance);
        //                 // $sheet->setCellValue("R{$row}", $item->credit);
        //                 // $sheet->setCellValue("S{$row}", $item->law_average);
        //                 // $sheet->setCellValue("T{$row}", $item->law_correction);
        //                 // $sheet->setCellValue("U{$row}", $item->agency_id);

        //                 $row++;
        //             }
        //         }
        //     }
        // }

        foreach ($grouped as $chapterNo => $accounts) {

            // === CHAPTER ROW ===
            $chapter = $chapterMap->get($chapterNo);

            $sheet->setCellValue("A{$row}", $chapterNo);
            $sheet->setCellValue("E{$row}", $chapter ? $chapter->name : '');
            $sheet->setCellValue("F{$row}", $codeTotals['fin_law']);
            $row++;

            // === ACCOUNT ROWS ===
            foreach ($accounts as $accountNo => $subAccounts) {

                $account = $accountMap->get($accountNo);

                $sheet->setCellValue("B{$row}", $accountNo);
                $sheet->setCellValue("E{$row}", $account ? $account->name : '');
                $row++;

                // === SUB-ACCOUNT ROWS ===
                foreach ($subAccounts as $accountSubNo => $items) {

                    $accountSub = $accountSubMap->get($accountSubNo);

                    $sheet->setCellValue("C{$row}", $accountSubNo);
                    $sheet->setCellValue("E{$row}", $accountSub ? $accountSub->name : '');
                    $row++;

                    // === ITEM ROWS ===
                    foreach ($items as $item) {

                        $sheet->setCellValue("D{$row}", $item->no);
                        $sheet->setCellValue("E{$row}", $item->txtDescription);
                        $sheet->setCellValue("F{$row}", $item->fin_law);

                        // If you want to restore more columns, uncomment:
                        // $sheet->setCellValue("G{$row}", $item->current_loan);
                        // $sheet->setCellValue("N{$row}", $item->new_credit_status);
                        // $sheet->setCellValue("O{$row}", $item->apply);
                        // $sheet->setCellValue("P{$row}", $item->deadline_balance);

                        $row++;

                        // $sheet->setCellValue($col . $row, $item->no);
                        // $col++;

                        // $sheet->setCellValue($col . $row, $item->fin_law);
                        // $col++;
                    }
                }
            }
        }

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
}
