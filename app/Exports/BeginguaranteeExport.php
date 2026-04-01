<?php

namespace App\Exports;

use App\Models\Content\Account;
use App\Models\Content\AccountSub;
use App\Models\Content\Ministry;
use App\Models\Content\Chapter;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Http\Request;

class BeginguaranteeExport
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

        $templatePath = public_path('template_guarantee.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $currentMonth = date('m');
        $currentYear = date('Y');
        $dateRangeText = 'ប្រចាំ​ ខែ ' . $currentMonth;

        $row = 10;
        $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
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
        $sheet->mergeCells("A{$row}:I{$row}");
        $row = 14;
        $grouped = $this->data
            ->sortBy(['account_sub_id', 'no'])
            ->groupBy('account_sub_id');

        $ministry = Ministry::where('id', $id)->first();

        $accountSubId = $this->data->pluck('account_sub_id')->filter()->unique();

        $accountSubMap = AccountSub::where('ministry_id', $ministry->id)
            ->whereIn('no', $accountSubId)
            ->get()
            ->keyBy('no');

        foreach ($grouped as $accountSubNo => $items) {

            $accountSub = $accountSubMap->get($accountSubNo);
            $subTotals = $this->initTotals();

            $subRow = $row;
            $sheet->setCellValue("B{$subRow}", $accountSubNo);
            $sheet->setCellValue("D{$subRow}", $accountSub?->name);
            $row++;

            foreach ($items as $item) {
                $sheet->setCellValue("C{$row}", $item->no);
                $sheet->setCellValue("D{$row}", $item->txtDescription);
                $sheet->setCellValue("E{$row}", $item->fin_law);
                $sheet->setCellValue("F{$row}", $item->new_credit_status);
                // $sheet->setCellValue("G{$row}", $item->early_balance);
                $sheet->setCellValue("G{$row}", $item->apply);

                $values = [
                    'fin_law'            => (float) $item->fin_law,
                    'new_credit_status'  => (float) $item->new_credit_status,
                    'early_balance'      => (float) $item->early_balance,
                    'apply'              => (float) $item->apply,
                ];

                // ⚠️ IMPORTANT
                $subTotals = $this->addToTotals($subTotals, $values);

                $row++;
            }

            $this->writeTotalsRow($sheet, $subRow, $subTotals);
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

        $fileName = 'template_guarantee.xlsx';

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
            'new_credit_status'  => 0,
            'early_balance'      => 0,
            'apply'              => 0,
        ];
    }

    private function addToTotals(array $totals, array $values): array
    {
        foreach ($values as $key => $value) {
            if (!isset($totals[$key])) {
                $totals[$key] = 0;
            }

            $totals[$key] += $value;
        }

        return $totals; // ✅ VERY IMPORTANT
    }

    private function writeTotalsRow(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, int $row, array $totals): void
    {
        $sheet->setCellValue("E{$row}", $totals['fin_law']);
        $sheet->setCellValue("F{$row}", $totals['new_credit_status']);
        // $sheet->setCellValue("G{$row}", $totals['early_balance']);
        $sheet->setCellValue("G{$row}", $totals['apply']);
    }
}
