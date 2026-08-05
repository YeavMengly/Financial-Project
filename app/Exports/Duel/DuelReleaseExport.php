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
    protected $startDate;
    protected $endDate;

    public function __construct($data, $ministryId, $startDate = null, $endDate = null)
    {
        $this->data = $data;
        $this->ministryId = $ministryId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function export(Request $request)
    {
        $params = $request->params;
        $id     = decode_params($params);

        // Use passed data or load by ministry_id
        $release = $this->data ?: DuelRelease::where('ministry_id', $id)->get();

        $templatePath = storage_path('excel/template/duel_release_template.xlsx');
        $spreadsheet  = IOFactory::load($templatePath);
        $sheet        = $spreadsheet->getActiveSheet();

        /*
        |--------------------------------------------------------
        | Header (city + date)
        |--------------------------------------------------------
        */
        $khmerMonths = ['មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'];
        $currentMonth =  $khmerMonths[date('n') - 1];
        $khmerNumbers = ['0' => '០', '1' => '១', '2' => '២', '3' => '៣', '4' => '៤', '5' => '៥', '6' => '៦', '7' => '៧', '8' => '៨', '9' => '៩'];
        $currentYear = strtr(date('Y'), $khmerNumbers);
        $currentDay = strtr(date('d'), $khmerNumbers);

        if ($this->startDate && $this->endDate) {

            $start = strtotime($this->startDate);
            $end = strtotime($this->endDate);

            $startDay = strtr(date('d', $start), $khmerNumbers);
            $startMonth = $khmerMonths[date('n', $start) - 1];
            $startYear = strtr(date('Y', $start), $khmerNumbers);

            $endDay = strtr(date('d', $end), $khmerNumbers);
            $endMonth = $khmerMonths[date('n', $end) - 1];
            $endYear = strtr(date('Y', $end), $khmerNumbers);

            $dateRangeText = "រាជធានីភ្នំពេញចាប់ពីថ្ងៃទី {$startDay} ខែ {$startMonth} ឆ្នាំ {$startYear} ដល់ថ្ងៃទី {$endDay} ខែ {$endMonth} ឆ្នាំ {$endYear}";
        } else {
            $dateRangeText = "រាជធានីភ្នំពេញថ្ងៃទី {$currentDay} ខែ {$currentMonth} ឆ្នាំ {$currentYear}";
        }

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
        $startRow = 10;
        $row      = $startRow;

        $totalEA = 0;
        $totalDO = 0;
        $totalMO = 0;
        $stockGroups = $release->groupBy('stock_number');

        $groupedReleases = $release->groupBy(function ($item) {
            return $item->date_release . '_' . $item->receipt_number . '_' . $item->refer;
        });
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
        $index = 1;

        foreach ($stockGroups as $stockNumber => $stockGroup) {

            // Print stock header once
            $row++;
            $runningBalance = [];

            foreach ($stockGroup->unique('item_name') as $item) {

                $typeCode = $typeMap[$item->item_name];
                [$colTotal,, $colRemain] = $columnMap[$typeCode];

                $initialQty = (float) $item->opening_quantity;

                $sheet->setCellValue("C{$row}", " ស្តុកដើមគ្រា ");
                $sheet->setCellValue("{$colTotal}{$row}", $initialQty);
                $sheet->setCellValue("{$colRemain}{$row}", $initialQty);
                $sheet->getStyle("E{$row}:N{$row}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');
                // Save the initial balance for this type
                $runningBalance[$typeCode] = $initialQty;
            }
            $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                'font' => [
                    'name' => 'Khmer OS Battambang',
                    'size' => 9,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText'   => true,
                ],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(-1);
            $sheet->getStyle("E{$row}:N{$row}")->applyFromArray([
                'font' => [
                    'name' => 'Khmer OS Battambang',
                    'size' => 9,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText'   => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color'       => ['rgb' => '000000'],
                    ],
                ],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(-1);
            $row++;
            // Receipts for this stock only
            $groupedReleases = $stockGroup
                ->sortBy([
                    ['date_release', 'asc'],
                    ['receipt_number', 'asc'],
                ])
                ->groupBy(function ($item) {
                    return $item->date_release . '_' .
                        $item->receipt_number . '_' .
                        $item->refer;
                });

            foreach ($groupedReleases as $group) {

                $firstItem = $group->first();

                $sheet->setCellValue("A{$row}", $index);
                $sheet->setCellValue("B{$row}", $firstItem->date_release);
                $sheet->setCellValue("C{$row}", $firstItem->receipt_number);
                $sheet->setCellValue("D{$row}", $firstItem->refer);

                foreach ($group as $item) {

                    $typeCode = $typeMap[$item->item_name];
                    [$colTotal, $colReq, $colRemain] = $columnMap[$typeCode];

                    // Previous balance
                    $quantityTotal = $runningBalance[$typeCode];

                    $quantityRequest = (float) $item->quantity_request;

                    // New balance
                    $duelTotal = $quantityTotal - $quantityRequest;

                    $sheet->setCellValue("{$colTotal}{$row}", $quantityTotal);
                    $sheet->setCellValue("{$colReq}{$row}", $quantityRequest);
                    $sheet->setCellValue("{$colRemain}{$row}", $duelTotal);

                    // Save for the next receipt
                    $runningBalance[$typeCode] = $duelTotal;

                    switch ($typeCode) {
                        case 'EA':
                            $totalEA += $quantityRequest;
                            break;
                        case 'DO':
                            $totalDO += $quantityRequest;
                            break;
                        case 'MO':
                            $totalMO += $quantityRequest;
                            break;
                    }
                }
                // Apply style to this receipt row
                $sheet->getStyle("E{$row}:N{$row}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');
                $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
                    'font' => [
                        'name' => 'Khmer OS Battambang',
                        'size' => 9,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color'       => ['rgb' => '000000'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(-1);

                $row++;
                $index++;
            }
        }
        /*
        |--------------------------------------------------------
        | Total Row
        |--------------------------------------------------------
        */
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->setCellValue("A{$row}", 'សរុប');

        $sheet->setCellValue("F{$row}", $totalEA);
        $sheet->setCellValue("I{$row}", $totalDO);
        $sheet->setCellValue("L{$row}", $totalMO);

        $sheet->setCellValue("G{$row}", $runningBalance['EA'] ?? 0);
        $sheet->setCellValue("J{$row}", $runningBalance['DO'] ?? 0);
        $sheet->setCellValue("M{$row}", $runningBalance['MO'] ?? 0);

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
