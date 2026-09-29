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
        $khmerMonths  = ['មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'];
        $khmerNumbers = ['0' => '០', '1' => '១', '2' => '២', '3' => '៣', '4' => '៤', '5' => '៥', '6' => '៦', '7' => '៧', '8' => '៨', '9' => '៩'];

        $currentMonth = $khmerMonths[date('n') - 1];
        $currentYear  = strtr(date('Y'), $khmerNumbers);
        $currentDay   = strtr(date('d'), $khmerNumbers);

        if ($this->startDate && $this->endDate) {
            $start = strtotime($this->startDate);
            $end   = strtotime($this->endDate);

            $startDay   = strtr(date('d', $start), $khmerNumbers);
            $startMonth = $khmerMonths[date('n', $start) - 1];
            $startYear  = strtr(date('Y', $start), $khmerNumbers);

            $endDay   = strtr(date('d', $end), $khmerNumbers);
            $endMonth = $khmerMonths[date('n', $end) - 1];
            $endYear  = strtr(date('Y', $end), $khmerNumbers);

            $dateRangeText = "រាជធានីភ្នំពេញចាប់ពីថ្ងៃទី {$startDay} ខែ {$startMonth} ឆ្នាំ {$startYear} ដល់ថ្ងៃទី {$endDay} ខែ {$endMonth} ឆ្នាំ {$endYear}";
        } else {
            $dateRangeText = "រាជធានីភ្នំពេញថ្ងៃទី {$currentDay} ខែ {$currentMonth} ឆ្នាំ {$currentYear}";
        }

        $row = 8; // Header row on line 7
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
    | Column Setup & Dynamic Mappings
    |--------------------------------------------------------
    */
        $sheet->getColumnDimension('D')->setWidth(30);

        $typeMap = [1 => 'EA', 2 => 'DO', 3 => 'MO'];
        $columnMap = [
            'EA' => ['E', 'F', 'G'],
            'DO' => ['H', 'I', 'J'],
            'MO' => ['K', 'L', 'M'],
        ];

        $totalEA = 0;
        $totalDO = 0;
        $totalMO = 0;
        $runningBalance = [];

        /*
    |--------------------------------------------------------
    | Opening Stock Row
    |--------------------------------------------------------
    */
        /*
|--------------------------------------------------------
| Opening Stock Row
|--------------------------------------------------------
*/
        $row = 11;

        // Set opening stock text in column C
        $sheet->setCellValue("C{$row}", "ស្តុកដើមគ្រា");

        // Populate starting quantities for EA, DO, MO
        foreach ($release->unique('item_name') as $item) {
            if (!isset($typeMap[$item->item_name])) {
                continue;
            }

            $typeCode   = $typeMap[$item->item_name];
            $initialQty = (float) $item->opening_quantity;

            [$colTotal,, $colRemain] = $columnMap[$typeCode];

            $sheet->setCellValue("{$colTotal}{$row}", $initialQty);
            $sheet->setCellValue("{$colRemain}{$row}", $initialQty);

            $runningBalance[$typeCode] = $initialQty;
        }

        // Format number display for quantity columns
        $sheet->getStyle("E{$row}:M{$row}")
            ->getNumberFormat()
            ->setFormatCode('#,##0');
        $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
            'font' => [
                'name' => 'Khmer OS Battambang',
                'size' => 9,
            ],

        ]);
        // Apply identical font, center alignment, and thin borders across columns A to N
        $sheet->getStyle("E{$row}:N{$row}")->applyFromArray([
            'font' => [
                'name' => 'Khmer OS Battambang',
                'size' => 9,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Auto-adjust row height
        $sheet->getRowDimension($row)->setRowHeight(-1);

        // Move to next row for detail entries
        $row++;

        /*
    |--------------------------------------------------------
    | Detail Table (Data List)
    |--------------------------------------------------------
    */
        $index = 1; // Initialize receipt counter

        $groupedReleases = $release
            ->sortBy([
                ['date_release', 'asc'],
                ['receipt_number', 'asc'],
            ])
            ->groupBy(function ($item) {
                return $item->date_release . '_' . $item->receipt_number . '_' . $item->refer;
            });

        foreach ($groupedReleases as $group) {
            $firstItem = $group->first();

            $sheet->setCellValue("A{$row}", $index);
            $sheet->setCellValue("B{$row}", $firstItem->date_release);
            $sheet->setCellValue("C{$row}", $firstItem->receipt_number);
            $sheet->setCellValue("D{$row}", $firstItem->refer);

            foreach ($group as $item) {
                if (!isset($typeMap[$item->item_name])) {
                    continue;
                }

                $typeCode = $typeMap[$item->item_name];
                [$colTotal, $colReq, $colRemain] = $columnMap[$typeCode];

                $quantityTotal   = $runningBalance[$typeCode] ?? 0;
                $quantityRequest = (float) $item->quantity_request;
                $duelTotal       = $quantityTotal - $quantityRequest;

                $sheet->setCellValue("{$colTotal}{$row}", $quantityTotal);
                $sheet->setCellValue("{$colReq}{$row}", $quantityRequest);
                $sheet->setCellValue("{$colRemain}{$row}", $duelTotal);

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
            $sheet->getStyle("E{$row}:M{$row}")
                ->getNumberFormat()
                ->setFormatCode('#,##0');

            // Apply style to full row A:N including borders
            $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
                'font' => [
                    'name' => 'Khmer OS Battambang',
                    'size' => 9,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                    'wrapText'   => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => '000000'],
                    ],
                ],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(-1);

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

        // Right-align calculated totals
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
