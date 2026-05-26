<?php

namespace App\Exports;

use App\Models\Content\Account;
use App\Models\Content\AccountSub;
use App\Models\BeginCredit\BeginVoucher;
use App\Models\Content\Ministry;
use App\Models\Content\Chapter;
use App\Models\Content\Type;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Http\Request;

class AnnualReport
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

        $templatePath = public_path('template_annual_report.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // $currentMonth = date('m');
        // $currentYear = date('Y');
        // $dateRangeText = 'ប្រចាំ​ ខែ ' . $currentMonth . ' ឆ្នាំ ' . $currentYear;

        // $row = 6;
        $col = 'E'; // start column for items
        // $sheet->getStyle("A{$row}:ER{$row}")->applyFromArray([
        //     'font' => [
        //         'bold' => true,
        //         'color' => ['rgb' => '000000'],
        //         'size' => 12,
        //     ],
        //     'alignment' => [
        //         'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        //         'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        //     ],
        //     'borders' => [
        //         'allBorders' => [
        //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        //             'color' => ['rgb' => '000000'],
        //         ],
        //     ],

        // ]);

        // $sheet->setCellValue("A{$row}", $dateRangeText);
        // $sheet->mergeCells("A{$row}:ER{$row}");
        $row = 7;
        $grouped = $this->data
            ->sortBy(['type_id', 'chapter_id', 'account_id', 'account_sub_id'])
            ->groupBy('type_id')
            ->map(function ($typeGroup) {
                return $typeGroup->groupBy('chapter_id')
                    ->map(function ($chapterGroup) {
                        return $chapterGroup->groupBy('account_id')
                            ->map(function ($accountGroup) {
                                return $accountGroup->groupBy('account_sub_id');
                            });
                    });
            });
// dd($grouped);
        $typeId = $this->data->pluck('type_id')->filter()->unique();
        $chapterId = $this->data->pluck('chapter_id')->filter()->unique();
        $accountId = $this->data->pluck('account_id')->filter()->unique();
        $accountSubId     = $this->data->pluck('account_sub_id')->filter()->unique();

        $ministry = Ministry::where('id', $id)->first();
        // $typeMap = Type::whereIn('number_type', $typeId)
        //     ->get()
        //     ->keyBy('number_type')
        $typeMap = Type::all();
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
        foreach ($grouped as $typeNo => $chapters) {

            // TYPE ROW
            $type = $typeMap->get($typeNo);
// dd($type);
            // $sheet->setCellValue("A{$row}", $typeNo);
            $sheet->setCellValue("A{$row}", $type ? $type->number_type : '');

            $sheet->getStyle("A{$row}")
                ->getFont()
                ->setBold(true);

            $row++;

            // CHAPTERS
            foreach ($chapters as $chapterNo => $accounts) {

                $chapter = $chapterMap->get($chapterNo);

                $sheet->setCellValue("A{$row}", $chapterNo);
                $sheet->setCellValue("D{$row}", $chapter ? $chapter->name : '');

                $sheet->getStyle("A{$row}:D{$row}")
                    ->getFont()
                    ->setBold(true);

                $row++;

                // ACCOUNTS
                foreach ($accounts as $accountNo => $subAccounts) {

                    $account = $accountMap->get($accountNo);

                    $sheet->setCellValue("B{$row}", $accountNo);
                    $sheet->setCellValue("D{$row}", $account ? $account->name : '');

                    $row++;

                    // ACCOUNT SUB
                    foreach ($subAccounts as $accountSubNo => $items) {

                        $accountSub = $accountSubMap->get($accountSubNo);

                        $sheet->setCellValue("C{$row}", $accountSubNo);
                        $sheet->setCellValue("D{$row}", $accountSub ? $accountSub->name : '');

                        $row++;

                        // ITEMS
                        // foreach ($items as $item) {

                        //     $sheet->setCellValue("D{$row}", $item->no);
                        //     $sheet->setCellValue("E{$row}", $item->txtDescription);

                        //     $row++;
                        // }

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

            $fileName = 'template_annual_report.xlsx';

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
}
