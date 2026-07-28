<?php

namespace App\Exports\Duel;

use App\Models\Duel\DuelRelease;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DuelReleaseExport
{
    protected $data;
    protected $ministryId;

    public function __construct($data, $ministryId)
    {
        $this->data       = $data;
        $this->ministryId = $ministryId;
    }

    public function export(Request $request)
    {
        $params = $request->params;
        $id     = decode_params($params);

        // Use passed data or load by ministry_id
        $release = $this->data ?: DuelRelease::where('ministry_id', $id)->get();

        $templatePath = public_path('duel_release_template.xlsx');
        $spreadsheet  = IOFactory::load($templatePath);
        $sheet        = $spreadsheet->getActiveSheet();

        /*
        |--------------------------------------------------------
        | Header (city + date)
        |--------------------------------------------------------
        */
        $currentDay   = date('d');
        $currentMonth = date('m');
        $currentYear  = date('Y');

        $dateRangeText = 'រាជធានីភ្នំពេញថ្ងៃទី ' . $currentDay .
            ' ខែ' . $currentMonth .
            ' ឆ្នាំ ' . $currentYear;

        $row = 8;
        $sheet->setCellValue("A{$row}", $dateRangeText);
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
            'font' => [
                'name'  => 'Khmer OS Battambang',
                'size'  => 9,
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        /*
        |--------------------------------------------------------
        | Detail Table (Data List)
        |--------------------------------------------------------
        */
        // $startRow = 11;
        // $row      = $startRow;

        // $totalEA = 0;
        // $totalDO = 0;
        // $totalMO = 0;

        // $groupedReleases = $release->groupBy(function ($item) {
        //     return $item->date_release . '_' . $item->receipt_number . '_' . $item->refer;
        // });

        // $index = 1;

        // foreach ($groupedReleases as $group) {
        //     $firstItem = $group->first();

        //     $sheet->setCellValue("A{$row}", $index);
        //     $sheet->setCellValue("B{$row}", $firstItem->date_release);
        //     $sheet->setCellValue("C{$row}", $firstItem->receipt_number);
        //     $sheet->setCellValue("D{$row}", $firstItem->refer);

        //     $typeMap = [
        //         1 => 'EA',
        //         2 => 'DO',
        //         3 => 'MO',
        //     ];

        //     $columnMap = [
        //         'EA' => ['E', 'F', 'G'],
        //         'DO' => ['H', 'I', 'J'],
        //         'MO' => ['K', 'L', 'M'],
        //     ];

        //     foreach ($group as $item) {
        //         $typeCode = $typeMap[$item->item_name] ?? 'EA';
        //         $cols     = $columnMap[$typeCode] ?? $columnMap['EA'];

        //         [$colTotal, $colReq, $colRemain] = $cols;

        //         $sheet->setCellValue("{$colTotal}{$row}", $item->quantity_total);
        //         $sheet->setCellValue("{$colReq}{$row}",   $item->quantity_request);
        //         $sheet->setCellValue("{$colRemain}{$row}", $item->duel_total);

        //         $sumValue = (float) $item->quantity_request;

        //         switch ($typeCode) {
        //             case 'EA':
        //                 $totalEA += $sumValue;
        //                 break;
        //             case 'DO':
        //                 $totalDO += $sumValue;
        //                 break;
        //             case 'MO':
        //                 $totalMO += $sumValue;
        //                 break;
        //         }
        //     }

        //     // Set explicit row height for comfortable line spacing
        //     $sheet->getRowDimension($row)->setRowHeight(22);

        //     // Base font, vertical alignment, and borders for the row
        //     $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
        //         'font' => [
        //             'name'  => 'Khmer OS Battambang',
        //             'size'  => 9,
        //             'color' => ['rgb' => '000000'],
        //         ],
        //         'alignment' => [
        //             'vertical' => Alignment::VERTICAL_CENTER,
        //         ],
        //         'borders' => [
        //             'allBorders' => [
        //                 'borderStyle' => Border::BORDER_THIN,
        //                 'color'       => ['rgb' => '000000'],
        //             ],
        //         ],
        //     ]);

        //     // Column-specific horizontal alignments:
        //     // Cols A-C: Centered (Index, Date, Receipt Number)
        //     $sheet->getStyle("A{$row}:C{$row}")->getAlignment()
        //         ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        //     // Col D: Left-aligned for notes/references (with wrap text enabled)
        //     $sheet->getStyle("D{$row}")->getAlignment()
        //         ->setHorizontal(Alignment::HORIZONTAL_LEFT)
        //         ->setWrapText(true);

        //     // Cols E-N: Right-aligned or Centered for numbers
        //     $sheet->getStyle("E{$row}:N{$row}")->getAlignment()
        //         ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        //     $row++;
        //     $index++;
        // }

        /*
        |--------------------------------------------------------
        | Column Setup
        |--------------------------------------------------------
        */
        // Set an explicit width for Column D so wrapping occurs predictably
        $sheet->getColumnDimension('D')->setWidth(30);

        /*
        |--------------------------------------------------------
        | Detail Table (Data List)
        |--------------------------------------------------------
        */
        $startRow = 11;
        $row      = $startRow;

        $totalEA = 0;
        $totalDO = 0;
        $totalMO = 0;

        $groupedReleases = $release->groupBy(function ($item) {
            return $item->date_release . '_' . $item->receipt_number . '_' . $item->refer;
        });

        $index = 1;

        foreach ($groupedReleases as $group) {
            $firstItem = $group->first();

            $sheet->setCellValue("A{$row}", $index);
            $sheet->setCellValue("B{$row}", $firstItem->date_release);
            $sheet->setCellValue("C{$row}", $firstItem->receipt_number);
            $sheet->setCellValue("D{$row}", $firstItem->refer);

            $typeMap = [
                1 => 'EA',
                2 => 'DO',
                3 => 'MO',
            ];

            $columnMap = [
                'EA' => ['E', 'F', 'G'],
                'DO' => ['H', 'I', 'J'],
                'MO' => ['K', 'L', 'M'],
            ];

            foreach ($group as $item) {
                $typeCode = $typeMap[$item->item_name] ?? 'EA';
                $cols     = $columnMap[$typeCode] ?? $columnMap['EA'];

                [$colTotal, $colReq, $colRemain] = $cols;

                $sheet->setCellValue("{$colTotal}{$row}", $item->quantity_total);
                $sheet->setCellValue("{$colReq}{$row}",   $item->quantity_request);
                $sheet->setCellValue("{$colRemain}{$row}", $item->duel_total);

                $sumValue = (float) $item->quantity_request;

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
            }

            // Let Excel auto-calculate row height based on wrapped text content
            $sheet->getRowDimension($row)->setRowHeight(-1);

            // Base font, vertical alignment, and borders for the row
            $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
                'font' => [
                    'name'  => 'Khmer OS Battambang',
                    'size'  => 9,
                    'color' => ['rgb' => '000000'],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => '000000'],
                    ],
                ],
            ]);

            // Column A-C: Centered
            $sheet->getStyle("A{$row}:C{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Column D: Left-aligned, wrap text enabled for multi-line support
            $sheet->getStyle("D{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                ->setWrapText(true);

            // Column E-N: Right-aligned for numbers
            $sheet->getStyle("E{$row}:N{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $row++;
            $index++;
        }

        /*
        |--------------------------------------------------------
        | Total Row
        |--------------------------------------------------------
        */
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->setCellValue("A{$row}", 'សរុប');

        $sheet->setCellValue("G{$row}", $totalEA);
        $sheet->setCellValue("J{$row}", $totalDO);
        $sheet->setCellValue("M{$row}", $totalMO);

        $sheet->getRowDimension($row)->setRowHeight(24);

        $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
            'font' => [
                'name' => 'Khmer OS Battambang',
                'bold' => true,
                'size' => 9,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Right-align the calculated totals
        $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("J{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("M{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        /*
        |--------------------------------------------------------
        | Signature Titles
        |--------------------------------------------------------
        */
        $row += 2;

        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->mergeCells("C{$row}:D{$row}");
        $sheet->mergeCells("E{$row}:F{$row}");
        $sheet->mergeCells("G{$row}:H{$row}");

        $sheet->setCellValue("A{$row}", 'ប្រធាននាយកដ្ឋាន');
        $sheet->setCellValue("C{$row}", 'ប្រធានការិយាល័យ');
        $sheet->setCellValue("E{$row}", 'អ្នកប្រគល់');
        $sheet->setCellValue("G{$row}", 'ឆ្មាំឃ្លាំង');

        $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
            'font' => [
                'name' => 'Khmer OS Battambang',
                'bold' => true,
                'size' => 9,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        /*
        |--------------------------------------------------------
        | Output Stream
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
