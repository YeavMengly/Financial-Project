<?php

namespace App\Exports\Duel;

use App\Models\Duel\DuelRelease;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DuelReleaseExport
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
        $release = $this->data ?: DuelRelease::where('ministry_id', $id)->get();
        $first   = $release->first();

        $templatePath = public_path('duel_release_template.xlsx');
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
        // if ($first) {
        //     $sheet->setCellValue(
        //         'A7',
        //         'ការិយាល័យផ្គត់ផ្គង់' .
        //             ($first->stock_number ?? '')
        //     );
        // }

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
        // if ($first) {
        //     $sheet->setCellValue('C10', $first->company_name ?? '');

        //     $sheet->setCellValue(
        //         'C11',
        //         $first->date_entry
        //             ? date('d/m/Y', strtotime($first->date_entry))
        //             : ''
        //     );

        //     $sheet->setCellValue('C12', $first->refer ?? '');
        //     $sheet->setCellValue('C13', $first->user_entry ?? '');
        //     $sheet->setCellValue('C14', $first->stock_name ?? '');
        // }

        /*
        |--------------------------------------------------------
        | Detail table
        |   Start at row 11 (matches your template)
        |   Columns:
        |     A → ល.រ (index)
        |     B → date_release
        |     C → receipt_number
        |     D → refer
        |     E–G → EA  (បរិមាណដើម, ចេញ, សមតុល្យ)
        |     H–J → DO
        |     K–M → MO
        |--------------------------------------------------------
        */
        $row = 11;

        // totals per type (if you want to show them later)
        $totalEA = 0;
        $totalDO = 0;
        $totalMO = 0;

        foreach ($release as $index => $item) {

            // 1) Common columns
            $sheet->setCellValue("A{$row}", $index + 1);                 // ល.រ
            $sheet->setCellValue("B{$row}", $item->date_release);        // កាលបរិច្ឆេទ
            $sheet->setCellValue("C{$row}", $item->receipt_number);      // លេខលិខិត
            $sheet->setCellValue("D{$row}", $item->refer);               // លេខយោង / កំណត់ចំណាំ

            // 2) Decide which block to fill: EA, DO, or MO
            //    👉 Adjust this to your actual column / relationship
            //    e.g. $typeCode = $item->duelType->code; or $item->type; etc.
            $typeMap = [
                1 => 'EA',
                2 => 'DO',
                3 => 'MO'
            ];

            $typeCode = $typeMap[$item->item_name] ?? 'EA';

            // map type → columns
            $columnMap = [
                'EA' => ['E', 'F', 'G'],
                'DO' => ['H', 'I', 'J'],
                'MO' => ['K', 'L', 'M'],
            ];

            $cols = $columnMap[$typeCode] ?? $columnMap['EA'];

            [$colTotal, $colReq, $colRemain] = $cols;
            // default to EA if unknown
            $cols = $columnMap[$typeCode] ?? $columnMap['EA'];

            [$colTotal, $colReq, $colRemain] = $cols;

            // 3) Put the numbers in the correct 3 columns
            $sheet->setCellValue("{$colTotal}{$row}", $item->quantity_total);
            $sheet->setCellValue("{$colReq}{$row}",   $item->quantity_request);
            $sheet->setCellValue("{$colRemain}{$row}", $item->duel_total);

            // 4) Sum per type (optional – used in footer)
            $sumValue = (float) $item->duel_total;

            switch ($typeCode) {
                case 'EA':
                    $totalEA += $sumValue;
                    break;

                case 'DO':
                    $totalDO += $sumValue;
                    break;

                case 'MO':
                    $totalMO += $sumValue;
                    break;
            }

            // 5) Apply border + alignment for the whole detail row
            $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
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
        // Totals row
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->setCellValue("A{$row}", 'សរុប');

        // Example: show grand total of EA in col G, DO in col J, MO in col M
        $sheet->setCellValue("G{$row}", $totalEA);
        $sheet->setCellValue("J{$row}", $totalDO);
        $sheet->setCellValue("M{$row}", $totalMO);

        $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
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
        $fileName = 'duel_release_template.xlsx';

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
