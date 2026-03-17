<?php

namespace App\Exports\Duel;

use App\Models\Duel\DuelEntry;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DuelEntriesExport
{
    protected $data;
    protected $ministryId;

    public function __construct($data, $ministryId)
    {
        $this->data      = $data;
        $this->ministryId = $ministryId;
    }

    public function export(Request $request)
    {
        $params = $request->params;
        $id     = decode_params($params);

        // Use passed data or load by ministry_id
        $entries = DuelEntry::select(
            'duel_entries.*',
            'duel_types.name_km',
            'unit_types.name'
        )
            ->leftJoin('duel_types', 'duel_entries.item_name', '=', 'duel_types.id')
            ->leftJoin('unit_types', 'duel_entries.unit', '=', 'unit_types.id')

            ->where('duel_entries.ministry_id', $id)
            ->get();
        $first   = $entries->first();

        $templatePath = public_path('duel_entries_template.xlsx');
        $spreadsheet  = IOFactory::load($templatePath);
        $sheet        = $spreadsheet->getActiveSheet();

        /*
        |--------------------------------------------------------
        | Header (city + date + stock number)
        |--------------------------------------------------------
        */
        $currentDay   = date('d');
        $currentMonth = date('m');
        $currentYear  = date('Y');

        $dateRangeText = 'រាជធានីភ្នំពេញថ្ងៃទី ' . $currentDay .
            ' ខែ' . $currentMonth .
            ' ឆ្នាំ ' . $currentYear;

        // stock_number at A4
        if ($first) {
            $sheet->setCellValue(
                'A4',
                'លេខបញ្ចូលឃ្លាំង: ' . ($first->stock_number ?? '')
            );
        }

        // big centered date text at row 8
        $row = 8;
        $sheet->setCellValue("A{$row}", $dateRangeText);
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
                'size' => 9,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        /*
        |--------------------------------------------------------
        | Info block (company, date, refer, user, stock_name)
        |   C10 → company_name
        |   C11 → date_entry
        |   C12 → refer
        |   C13 → user_entry
        |   C14 → stock_name
        |--------------------------------------------------------
        */
        if ($first) {
            $sheet->setCellValue('C10', $first->company_name ?? '');

            $sheet->setCellValue(
                'C11',
                $first->date_entry
                    ? date('d/m/Y', strtotime($first->date_entry))
                    : ''
            );

            $sheet->setCellValue('C12', $first->refer ?? '');
            $sheet->setCellValue('C13', $first->user_entry ?? '');
            $sheet->setCellValue('C14', $first->stock_name ?? '');
        }

        /*
        |--------------------------------------------------------
        | Detail table
        |   Start at row 18
        |   Columns:
        |     A → ល.រ (index)
        |     B → item_name
        |     C → unit
        |     D → quantity
        |     E → price
        |     F → duel_total
        |     G → source
        |     H → note/blank
        |--------------------------------------------------------
        */
        $row       = 18;
        $totalDuel = 0;

        foreach ($entries as $index => $item) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $item->name_km);
            $sheet->setCellValue("C{$row}", $item->name);
            $sheet->setCellValue("D{$row}", $item->quantity);
            $sheet->setCellValue("E{$row}", $item->price);
            $sheet->setCellValue("F{$row}", $item->duel_total);
            $sheet->setCellValue("G{$row}", $item->source ?? '');
            $sheet->setCellValue("H{$row}", null);

            $totalDuel += (float) $item->duel_total;

            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                'font' => [
                    'size' => 9,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color'       => ['rgb' => '000000'],
                    ],
                ],
            ]);

            $row++;
        }

        /*
        |--------------------------------------------------------
        | Total row
        |   A–D merged → "សរុប"
        |   F → numeric total
        |--------------------------------------------------------
        */
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->setCellValue("A{$row}", 'សរុប');
        $sheet->setCellValue("F{$row}", $totalDuel);

        $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 9,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ]);

        /*
        |--------------------------------------------------------
        | Text line: បញ្ឈប់តារាងត្រឹម...
        |--------------------------------------------------------
        */
        $row++;
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->setCellValue(
            "A{$row}",
            'បញ្ឈប់តារាងត្រឹមទឹកប្រាក់ចំនួន ' .
                number_format($totalDuel) .
                ' រៀលប៉ុណ្ណោះ។'
        );

        $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 9,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        /*
        |--------------------------------------------------------
        | Signature titles
        |   A–B → ប្រធាននាយកដ្ឋាន
        |   C–D → ប្រធានការិយាល័យ
        |   E–F → អ្នកប្រគល់
        |   G–H → ឆ្មាំឃ្លាំង
        |--------------------------------------------------------
        */
        $row += 2;

        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->mergeCells("C{$row}:D{$row}");
        $sheet->mergeCells("E{$row}:F{$row}");
        $sheet->mergeCells("G{$row}:H{$row}");

        $sheet->setCellValue("A{$row}", 'ប្រធាននាយកដ្ឋាន ');
        $sheet->setCellValue("C{$row}", 'ប្រធានការិយាល័យ ');
        $sheet->setCellValue("E{$row}", 'អ្នកប្រគល់ ');
        $sheet->setCellValue("G{$row}", 'ឆ្មាំឃ្លាំង ');

        $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 9,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        /*
        |--------------------------------------------------------
        | Output file
        |--------------------------------------------------------
        */
        $fileName = 'duel_entries_template.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
