<?php

namespace App\Exports\Material;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MaterialEntriesExport
{
    protected $data;
    protected $ministryId;
    protected $projectId;
    protected $selectedProject;

    public function __construct($data, $ministryId, $projectId = null, $selectedProject = null)
    {
        $this->data = $data;
        $this->ministryId = $ministryId;
        $this->projectId = $projectId;
        $this->selectedProject = $selectedProject;
    }

    public function export(Request $request)
    {
        $params = $request->params;
        $id = decode_params($params);

        $templatePath = storage_path('app/excel/template/material_entries_template.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rowNum = 7;
        $stockDisplayNumber = '';

        if ($this->selectedProject) {
            $stockDisplayNumber = ('  លេខបញ្ចូលឃ្លាំង' . $this->selectedProject->stock_number ?? '');
        } else {
            $stockDisplayNumber = ' ';
        }
        $sheet->setCellValue("A{$rowNum}", $stockDisplayNumber);
        //   $sheet->mergeCells("A{$rowNum}:C{$rowNum}");
        $sheet->getStyle("A{$rowNum}:C{$rowNum}")->applyFromArray([
            'font' => [
                'name'  => 'Khmer OS Battambang',
                'size'  => 9,
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_GENERAL,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => false,
            ],
        ]);

        $rowName = 8;
        $stockDisplayName = '';

        if ($this->selectedProject) {
            $stockDisplayName = ($this->selectedProject->stock_name ?? '');
        } else {
            $stockDisplayName = 'គម្រោងទាំងអស់';
        }

        $sheet->setCellValue("D{$rowName}", $stockDisplayName);
        $sheet->mergeCells("D{$rowName}:I{$rowName}");
        $sheet->getRowDimension($rowName)->setRowHeight(40);

        $sheet->getStyle("D{$rowName}:I{$rowName}")->applyFromArray([
            'font' => [
                'name'  => 'Khmer OS Muol Light',
                'size'  => 9,
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);
        $rowNote = 9;
        $stockDisplayNote = '';

        if ($this->selectedProject) {
            $rawNote = $this->selectedProject->note ?? '';

            // 🛠️ ប្រើ html_entity_decode ដើម្បីបំប្លែង &nbsp; ឱ្យទៅជាការដកឃ្លាធម្មតាវិញ
            $cleanNote = html_entity_decode($rawNote, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // លុបសញ្ញាចុះបន្ទាត់លាក់ បើមិនចង់ឱ្យវាធ្លាក់ចុះខុសទម្រង់
            $stockDisplayNote = trim(str_replace(["\r", "\n"], ' ', $cleanNote));
        } else {
            $stockDisplayNote = ' ';
        }

        $sheet->setCellValue("D{$rowNote}", $stockDisplayNote);
        $sheet->mergeCells("D{$rowNote}:I{$rowNote}");
        $sheet->getRowDimension($rowNote)->setRowHeight(25);

        $sheet->getStyle("D{$rowNote}:I{$rowNote}")->applyFromArray([
            'font' => [
                'name'  => 'Khmer OS Battambang',
                'size'  => 9,
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => false,
            ],
        ]);
        $rowInfor = 10;

        // កំណត់ Style សម្រាប់ស្លាក (ក្រឡា B)
        $labelStyle = [
            'font' => [
                'name'  => 'Khmer OS Muol Light',
                'size'  => 9,
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ];

        // កំណត់ Style សម្រាប់ក្រឡា C ដល់ I (ដែលបាន Merge ចូលគ្នា)
        $valueStyle = [
            'font' => [
                'name'  => 'Khmer OS Battambang',
                'size'  => 9,
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ];

        // --- ជួរទី ១០: ឈ្មោះក្រុមហ៊ុន ---
        $sheet->setCellValue("B{$rowInfor}", 'បញ្ចូលពី');
        $companyText = $this->selectedProject ? ('៖ ' . ($this->selectedProject->company_name ?? '')) : ' ';
        $sheet->setCellValue("C{$rowInfor}", $companyText);
        $sheet->mergeCells("C{$rowInfor}:J{$rowInfor}"); // Merge C ដល់ J

        $sheet->getStyle("B{$rowInfor}")->applyFromArray($labelStyle);
        $sheet->getStyle("C{$rowInfor}:J{$rowInfor}")->applyFromArray($valueStyle);


        // --- ជួរទី ១១: តាមបណ្ណបញ្ចូលឃ្លាំង ---
        $rowInfor++;
        $sheet->setCellValue("B{$rowInfor}", 'តាមបណ្ណបញ្ចូលឃ្លាំង');
        $warehouseVoucher = $this->selectedProject ? ('៖ ' . ($this->selectedProject->warehouse_voucher ?? '')) : ' ';
        $sheet->setCellValue("C{$rowInfor}", $warehouseVoucher);
        $sheet->mergeCells("C{$rowInfor}:J{$rowInfor}"); // Merge C ដល់ J

        $sheet->getStyle("B{$rowInfor}")->applyFromArray($labelStyle);
        $sheet->getStyle("C{$rowInfor}:J{$rowInfor}")->applyFromArray($valueStyle);

        // --- ជួរទី ១២: យោង ---
        $rowInfor++;
        $sheet->setCellValue("B{$rowInfor}", 'យោង');

        // 🛠️ ប្រើ str_replace ដើម្បីលុបការចុះបន្ទាត់លាក់ចេញពី Database ឱ្យវាស្ថិតក្នុងបន្ទាត់តែមួយ
        if ($this->selectedProject && !empty($this->selectedProject->refer)) {
            $cleanRefer = trim(str_replace(["\r", "\n"], ' ', $this->selectedProject->refer));
            $refer = '៖ ' . $cleanRefer;
        } else {
            $refer = ' ';
        }
        $sheet->setCellValue("C{$rowInfor}", $refer);
        $sheet->mergeCells("C{$rowInfor}:J{$rowInfor}"); // Merge ពី C ដល់ J

        $sheet->getStyle("B{$rowInfor}")->applyFromArray($labelStyle);
        $sheet->getStyle("C{$rowInfor}:J{$rowInfor}")->applyFromArray($valueStyle);


        // --- ជួរទី ១៣: អ្នកបញ្ចូល ---
        $rowInfor++;
        $sheet->setCellValue("B{$rowInfor}", 'អ្នកបញ្ចូល');
        $userText = $this->selectedProject ? ('៖ ' . ($this->selectedProject->user_entry ?? '')) : ' ';
        $sheet->setCellValue("C{$rowInfor}", $userText);
        $sheet->mergeCells("C{$rowInfor}:J{$rowInfor}"); // Merge C ដល់ J

        $sheet->getStyle("B{$rowInfor}")->applyFromArray($labelStyle);
        $sheet->getStyle("C{$rowInfor}:J{$rowInfor}")->applyFromArray($valueStyle);


        // --- ជួរទី ១៤: បញ្ចូលទៅឃ្លាំង ---
        $rowInfor++;
        $sheet->setCellValue("B{$rowInfor}", 'បញ្ចូលទៅឃ្លាំង');
        $warehouseText = $this->selectedProject ? ('៖ ' . ($this->selectedProject->warehouse_owner ?? '')) : ' ';
        $sheet->setCellValue("C{$rowInfor}", $warehouseText);
        $sheet->mergeCells("C{$rowInfor}:J{$rowInfor}"); // Merge C ដល់ J

        $sheet->getStyle("B{$rowInfor}")->applyFromArray($labelStyle);
        $sheet->getStyle("C{$rowInfor}:J{$rowInfor}")->applyFromArray($valueStyle);
        // ==========================================
        // 2. DATE RANGE HEADER (Row 8)
        // ==========================================
        // $currentMonth = date('m');
        // $currentYear = date('Y');
        // // $dateRangeText = 'ប្រចាំ​ ខែ ' . $currentMonth . ' ឆ្នាំ ' . $currentYear;

        // $row = 13;
        // $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
        //     'font' => [
        //         'bold'  => true,
        //         'color' => ['rgb' => '000000'],
        //         'size'  => 12,
        //     ],
        //     'alignment' => [
        //         'horizontal' => Alignment::HORIZONTAL_CENTER,
        //         'vertical'   => Alignment::VERTICAL_CENTER,
        //     ],
        //     'borders' => [
        //         'allBorders' => [
        //             'borderStyle' => Border::BORDER_THIN,
        //             'color'       => ['rgb' => '000000'],
        //         ],
        //     ],
        // ]);

        // // $sheet->setCellValue("A{$row}", $dateRangeText);
        // $sheet->mergeCells("A{$row}:H{$row}");

        // ==========================================
        // 3. DATA ROWS (Starting at Row 11)
        // ==========================================
        $row = 18;
        $no = 1;
        $totalAmount = 0;

        foreach ($this->data as $index => $item) {
            $sheet->setCellValue("A{$row}", $no);
            $sheet->setCellValue("B{$row}", $item->p_name ?? '');
            $sheet->setCellValue("C{$row}", $item->unit ?? '');
            $sheet->setCellValue("D{$row}", $item->qty ?? 0);
            $sheet->setCellValue("E{$row}", $item->price ? number_format($item->price) : 0);
            $sheet->setCellValue("F{$row}", $item->total_price ? number_format($item->total_price) : 0);
            $sheet->setCellValue("G{$row}", $item->source ?? '');
            $sheet->setCellValue("H{$row}", $item->p_year ?? '');

            // $totalQty += ($item->qty ?? 0);
            $totalAmount += ($item->total_price ?? 0);

            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
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
            $no++;
            $row++;
        }

        // ==========================================
        // 4. TOTAL SUMMARY ROW
        // ==========================================
        $sheet->setCellValue("A{$row}", "សរុប");
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("E{$row}", "");
        $sheet->setCellValue("F{$row}", number_format($totalAmount));
        $sheet->setCellValue("G{$row}", "");
        $sheet->setCellValue("H{$row}", "");

        $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
            'font' => [
                'name' => 'Khmer OS Battambang',
                // 'bold' => true,
                'size' => 9,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            // 'fill' => [
            //     'fillType' => Fill::FILL_SOLID,
            //     'startColor' => ['rgb' => 'E2E8F0'],
            // ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ]);

        $fileName = 'material_entries_export.xlsx';

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
