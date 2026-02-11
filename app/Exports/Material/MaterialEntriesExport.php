<?php

namespace App\Exports\Material;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Http\Request;

class MaterialEntriesExport
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

        $templatePath = public_path('material_entries_template.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $currentMonth = date('m');
        $currentYear = date('Y');
        $dateRangeText = 'ប្រចាំ​ ខែ ' . $currentMonth . ' ឆ្នាំ ' . $currentYear;

        $row = 8;
        $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
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
        $sheet->mergeCells("A{$row}:H{$row}");
        $row = 11;

        // foreach ($this->data as $index => $item) {

        //     $sheet->setCellValue("A{$row}", $index + 1);
        //     $sheet->setCellValue("B{$row}", $item->title_entity);
        //     $sheet->setCellValue("C{$row}", $item->location_number_use);
        //     $sheet->setCellValue("D{$row}", $item->invoice);
        //     $sheet->setCellValue("E{$row}", date('d/m/Y', strtotime($item->date)));
        //     $start = date('d/m/Y', strtotime($item->use_start));
        //     $end   = date('d/m/Y', strtotime($item->use_end));
        //     $sheet->setCellValue("F{$row}", "{$start} - {$end}");
        //     $sheet->setCellValue("G{$row}", $item->kilo);
        //     $sheet->setCellValue("H{$row}", "$item->cost_total, រៀល");

        //     $row++;
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

        $fileName = 'material_entries_template.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    // private function initTotals(): array
    // {
    //     return [
    //         'fin_law'            => 0,
    //         'current_loan'       => 0,
    //         'internal_increase'  => 0,
    //         'unexpected_increase' => 0,
    //         'additional_increase' => 0,
    //         'total_increase'     => 0,
    //         'decrease'           => 0,
    //         'editorial'          => 0,
    //         'new_credit_status'  => 0,
    //         'early_balance'      => 0,
    //         'apply'              => 0,
    //         'deadline_balance'   => 0,
    //         'credit'             => 0,
    //         'law_average'        => 0,
    //         'law_correction'     => 0,
    //     ];
    // }

    // private function addToTotals(array &$totals, array $values): void
    // {
    //     foreach ($totals as $key => $v) {
    //         if (isset($values[$key])) {
    //             $totals[$key] += $values[$key];
    //         }
    //     }
    // }

    // private function writeTotalsRow(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, int $row, array $totals): void
    // {
    //     $sheet->setCellValue("F{$row}", $totals['fin_law']);
    //     $sheet->setCellValue("G{$row}", $totals['current_loan']);
    //     $sheet->setCellValue("H{$row}", $totals['internal_increase']);
    //     $sheet->setCellValue("I{$row}", $totals['unexpected_increase']);
    //     $sheet->setCellValue("J{$row}", $totals['additional_increase']);
    //     $sheet->setCellValue("K{$row}", $totals['total_increase']);
    //     $sheet->setCellValue("L{$row}", $totals['decrease']);
    //     $sheet->setCellValue("M{$row}", $totals['editorial']);
    //     $sheet->setCellValue("N{$row}", $totals['new_credit_status']);
    //     $sheet->setCellValue("O{$row}", $totals['early_balance']);
    //     $sheet->setCellValue("P{$row}", $totals['apply']);
    //     $sheet->setCellValue("Q{$row}", $totals['deadline_balance']);
    //     $sheet->setCellValue("R{$row}", $totals['credit']);
    //     $sheet->setCellValue("S{$row}", $totals['law_average']);
    //     $sheet->setCellValue("T{$row}", $totals['law_correction']);
    // }
}
