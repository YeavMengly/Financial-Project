<?php

namespace App\Exports\Material;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

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

        // ==========================================
        // 1. HEADER & PROJECT INFORMATION
        // ==========================================
        $rowNum = 7;
        $stockDisplayNumber = $this->selectedProject
            ? ('  លេខបញ្ចូលឃ្លាំង' . ($this->selectedProject->stock_number ?? ''))
            : ' ';

        $sheet->setCellValue("A{$rowNum}", $stockDisplayNumber);
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
        $stockDisplayName = $this->selectedProject
            ? ($this->selectedProject->stock_name ?? '')
            : 'គម្រោងទាំងអស់';

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
        if ($this->selectedProject) {
            $rawNote = $this->selectedProject->note ?? '';
            $cleanNote = html_entity_decode($rawNote, ENT_QUOTES | ENT_HTML5, 'UTF-8');
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
        $sheet->mergeCells("C{$rowInfor}:J{$rowInfor}");
        $sheet->getStyle("B{$rowInfor}")->applyFromArray($labelStyle);
        $sheet->getStyle("C{$rowInfor}:J{$rowInfor}")->applyFromArray($valueStyle);

        // --- ជួរទី ១១: តាមបណ្ណបញ្ចូលឃ្លាំង ---
        $rowInfor++;
        $sheet->setCellValue("B{$rowInfor}", 'តាមបណ្ណបញ្ចូលឃ្លាំង');
        $warehouseVoucher = $this->selectedProject ? ('៖ ' . ($this->selectedProject->warehouse_voucher ?? '')) : ' ';
        $sheet->setCellValue("C{$rowInfor}", $warehouseVoucher);
        $sheet->mergeCells("C{$rowInfor}:J{$rowInfor}");
        $sheet->getStyle("B{$rowInfor}")->applyFromArray($labelStyle);
        $sheet->getStyle("C{$rowInfor}:J{$rowInfor}")->applyFromArray($valueStyle);

        // --- ជួរទី ១២: យោង ---
        $rowInfor++;
        $sheet->setCellValue("B{$rowInfor}", 'យោង');
        if ($this->selectedProject && !empty($this->selectedProject->refer)) {
            $cleanRefer = trim(str_replace(["\r", "\n"], ' ', $this->selectedProject->refer));
            $refer = '៖ ' . $cleanRefer;
        } else {
            $refer = ' ';
        }
        $sheet->setCellValue("C{$rowInfor}", $refer);
        $sheet->mergeCells("C{$rowInfor}:J{$rowInfor}");
        $sheet->getStyle("B{$rowInfor}")->applyFromArray($labelStyle);
        $sheet->getStyle("C{$rowInfor}:J{$rowInfor}")->applyFromArray($valueStyle);

        // --- ជួរទី ១៣: អ្នកបញ្ចូល ---
        $rowInfor++;
        $sheet->setCellValue("B{$rowInfor}", 'អ្នកបញ្ចូល');
        $userText = $this->selectedProject ? ('៖ ' . ($this->selectedProject->user_entry ?? '')) : ' ';
        $sheet->setCellValue("C{$rowInfor}", $userText);
        $sheet->mergeCells("C{$rowInfor}:J{$rowInfor}");
        $sheet->getStyle("B{$rowInfor}")->applyFromArray($labelStyle);
        $sheet->getStyle("C{$rowInfor}:J{$rowInfor}")->applyFromArray($valueStyle);

        // --- ជួរទី ១៤: បញ្ចូលទៅឃ្លាំង ---
        $rowInfor++;
        $sheet->setCellValue("B{$rowInfor}", 'បញ្ចូលទៅឃ្លាំង');
        $warehouseText = $this->selectedProject ? ('៖ ' . ($this->selectedProject->warehouse_owner ?? '')) : ' ';
        $sheet->setCellValue("C{$rowInfor}", $warehouseText);
        $sheet->mergeCells("C{$rowInfor}:J{$rowInfor}");
        $sheet->getStyle("B{$rowInfor}")->applyFromArray($labelStyle);
        $sheet->getStyle("C{$rowInfor}:J{$rowInfor}")->applyFromArray($valueStyle);

        // ==========================================
        // 2. DATA ROWS (Starting at Row 18)
        // ==========================================
        $row = 18;
        $no = 1;
        $totalAmount = 0;

        $lastSubProject = null;
        $lastCategory = null;
        $lastSubCategory = null;
        $lastDetail = null;

        // 1. Pre-calculate distinct child counts for each parent level
        // (so we only print a subtotal when a parent has MORE than 1 sub-group)
        $uniqueSubProjects = $this->data
            ->pluck('project_sub_name')
            ->map(fn($name) => trim((string) $name))
            ->filter()
            ->unique();

        $hasMultipleSubProjects = $uniqueSubProjects->count() > 1;

        $categoriesPerSubProject = [];
        $subCategoriesPerCategory = [];
        $detailsPerSubCategory = [];

        foreach ($this->data as $item) {
            $sp  = trim((string) ($item->project_sub_name ?? ''));
            $cat = trim((string) ($item->category_id ?? ''));
            $sub = trim((string) ($item->category_sub_id ?? ''));
            $det = trim((string) ($item->detail ?? ''));

            if ($cat !== '') {
                $categoriesPerSubProject[$sp][$cat] = true;
            }
            if ($sub !== '') {
                $subCategoriesPerCategory["{$sp}|{$cat}"][$sub] = true;
            }
            if ($det !== '') {
                $detailsPerSubCategory["{$sp}|{$cat}|{$sub}"][$det] = true;
            }
        }

        // Helper closure to print a total row
        $writeTotalRow = function (string $label, float $amount) use ($sheet, &$row) {
            $sheet->setCellValue("A{$row}", $label);
            $sheet->mergeCells("A{$row}:E{$row}");
            $sheet->setCellValue("F{$row}", number_format($amount));
            $sheet->setCellValue("G{$row}", "");
            $sheet->setCellValue("H{$row}", "");

            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'font' => [
                    'name' => 'Khmer OS Muol Light',
                    'size' => 9,
                    'bold' => true,
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
            $sheet->getStyle("F{$row}")->getFont()->setName('Times New Roman');
            $row++;
        };

        $subProjectTotal  = 0;
        $categoryTotal    = 0;
        $subCategoryTotal = 0;
        $detailTotal      = 0;

        foreach ($this->data as $item) {
            $subProjectName  = trim((string) ($item->project_sub_name ?? ''));
            $categoryName    = trim((string) ($item->category_id ?? ''));
            $subCategoryName = trim((string) ($item->category_sub_id ?? ''));
            $detailName      = trim((string) ($item->detail ?? ''));

            $subProjectChanged  = ($subProjectName !== '' && $subProjectName !== $lastSubProject);
            $categoryChanged    = $subProjectChanged || ($categoryName !== '' && $categoryName !== $lastCategory);
            $subCategoryChanged = $categoryChanged || ($subCategoryName !== '' && $subCategoryName !== $lastSubCategory);
            $detailChanged      = $subCategoryChanged || ($detailName !== '' && $detailName !== $lastDetail);

            // --- CLOSE PREVIOUS GROUPS (Bottom-up: Detail -> SubCategory -> Category -> SubProject) ---
            if ($detailChanged && $lastDetail !== null) {
                $parentKey = ($lastSubProject ?? '') . '|' . ($lastCategory ?? '') . '|' . ($lastSubCategory ?? '');
                if (count($detailsPerSubCategory[$parentKey] ?? []) > 1) {
                    $writeTotalRow("សរុប", $detailTotal);
                }
                $detailTotal = 0;
                $lastDetail = null;
            }

            if ($subCategoryChanged && $lastSubCategory !== null) {
                $parentKey = ($lastSubProject ?? '') . '|' . ($lastCategory ?? '');
                if (count($subCategoriesPerCategory[$parentKey] ?? []) > 1) {
                    $writeTotalRow("សរុប", $subCategoryTotal);
                }
                $subCategoryTotal = 0;
                $lastSubCategory = null;
            }

            if ($categoryChanged && $lastCategory !== null) {
                $parentKey = ($lastSubProject ?? '');
                if (count($categoriesPerSubProject[$parentKey] ?? []) > 1) {
                    $writeTotalRow("សរុប", $categoryTotal);
                }
                $categoryTotal = 0;
                $lastCategory = null;
            }

            if ($subProjectChanged && $lastSubProject !== null) {
                if ($hasMultipleSubProjects) {
                    $writeTotalRow("សរុប", $subProjectTotal);
                }
                $subProjectTotal = 0;
                $lastSubProject = null;
            }

            // --- 1. SUB PROJECT HEADER ---
            if ($subProjectName !== '' && $subProjectName !== $lastSubProject) {
                // $accountSub = $this->toKhmerNumber(trim((string) ($item->sub_account ?? $item->account_sub_id ?? '')));
                // $program    = $this->toKhmerNumber(trim((string) ($item->sub_program ?? $item->program_id ?? '')));
                // $programSub = $this->toKhmerNumber(trim((string) ($item->sub_program_sub ?? $item->program_sub_id ?? '')));
                // $cluster    = $this->toKhmerNumber(trim((string) ($item->sub_cluster ?? $item->cluster_id ?? '')));

                // $extraParts = array_filter([
                //     $accountSub !== '' ? "អនុគណនី {$accountSub}" : '',
                //     $program    !== '' ? "កម្មវិធីទី{$program}" : '',
                //     $programSub !== '' ? "អនុកម្មវិធីទី{$programSub}" : '',
                //     $cluster    !== '' ? "ចង្កោមសកម្មភាព{$cluster}" : '',
                // ]);

                $fullSubProjectHeader = $subProjectName;
                if (!empty($extraParts)) {
                    $fullSubProjectHeader .= ' ' . implode(' ', $extraParts);
                }

                $sheet->setCellValue("A{$row}", $this->formatKhmerEnglishText($fullSubProjectHeader));
                $sheet->mergeCells("A{$row}:I{$row}");
                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(-1);

                $lastSubProject = $subProjectName;
                $row++;
            }

            // --- 2. CATEGORY HEADER ---
            if ($categoryName !== '' && $categoryName !== $lastCategory) {
                $sheet->setCellValue("A{$row}", $this->formatKhmerEnglishText($categoryName));
                $sheet->mergeCells("A{$row}:I{$row}");
                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);
                $lastCategory = $categoryName;
                $row++;
            }

            // --- 3. SUB CATEGORY HEADER ---
            if ($subCategoryName !== '' && $subCategoryName !== $lastSubCategory) {
                $sheet->setCellValue("A{$row}", $this->formatKhmerEnglishText($subCategoryName));
                $sheet->mergeCells("A{$row}:I{$row}");
                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);
                $lastSubCategory = $subCategoryName;
                $row++;
            }

            // --- 4. DETAIL HEADER ---
            if ($detailName !== '' && $detailName !== $lastDetail) {
                $sheet->setCellValue("A{$row}", $this->formatKhmerEnglishText($detailName));
                $sheet->mergeCells("A{$row}:I{$row}");
                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);
                $lastDetail = $detailName;
                $row++;
            }

            // --- 5. ACTUAL ITEM ROW ---
            $itemTotal = (float) ($item->total_price ?? 0);
            $detailTotal      += $itemTotal;
            $subCategoryTotal += $itemTotal;
            $categoryTotal    += $itemTotal;
            $subProjectTotal  += $itemTotal;
            $totalAmount      += $itemTotal;

            $sheet->setCellValue("A{$row}", $no);
            $sheet->setCellValue("B{$row}", $this->formatKhmerEnglishText($item->p_name ?? '', 9, false, 'Khmer OS Battambang'));
            $sheet->setCellValue("C{$row}", $item->unit ?? '');
            $sheet->setCellValue("D{$row}", $item->qty ?? 0);
            $sheet->setCellValue("E{$row}", $item->price ? number_format($item->price) : 0);
            $sheet->setCellValue("F{$row}", $itemTotal ? number_format($itemTotal) : 0);
            $sheet->setCellValue("G{$row}", $item->source ?? '');
            $sheet->setCellValue("H{$row}", $item->p_year ?? '');

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
        // CLOSE FINAL REMAINING GROUPS AFTER LOOP
        // ==========================================
        if ($lastDetail !== null) {
            $parentKey = ($lastSubProject ?? '') . '|' . ($lastCategory ?? '') . '|' . ($lastSubCategory ?? '');
            if (count($detailsPerSubCategory[$parentKey] ?? []) > 1) {
                $writeTotalRow("សរុប", $detailTotal);
            }
        }

        if ($lastSubCategory !== null) {
            $parentKey = ($lastSubProject ?? '') . '|' . ($lastCategory ?? '');
            if (count($subCategoriesPerCategory[$parentKey] ?? []) > 1) {
                $writeTotalRow("សរុប", $subCategoryTotal);
            }
        }

        if ($lastCategory !== null) {
            $parentKey = ($lastSubProject ?? '');
            if (count($categoriesPerSubProject[$parentKey] ?? []) > 1) {
                $writeTotalRow("សរុប", $categoryTotal);
            }
        }

        if ($hasMultipleSubProjects && $lastSubProject !== null) {
            $writeTotalRow("សរុប", $subProjectTotal);
        }

        // ==========================================
        // GRAND TOTAL ROW (Always shown at bottom)
        // ==========================================
        $hasAnySubtotals = $hasMultipleSubProjects
            || collect($categoriesPerSubProject)->contains(fn($c) => count($c) > 1)
            || collect($subCategoriesPerCategory)->contains(fn($s) => count($s) > 1)
            || collect($detailsPerSubCategory)->contains(fn($d) => count($d) > 1);

        $writeTotalRow($hasAnySubtotals ? "សរុបរួម" : "សរុប", $totalAmount);

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
    private function formatKhmerEnglishText(string $text, int $size = 9, bool $bold = true, string $khmerFont = 'Khmer OS Muol Light'): RichText
    {
        $richText = new RichText();
        $parts = preg_split('/([\x{1780}-\x{17FF}\x{19E0}-\x{19FF}]+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        foreach ($parts as $part) {
            $run = $richText->createTextRun($part);
            $isKhmer = preg_match('/[\x{1780}-\x{17FF}\x{19E0}-\x{19FF}]/u', $part);

            $run->getFont()
                ->setName($isKhmer ? $khmerFont : 'Times New Roman')
                ->setSize($size)
                ->setBold($bold)
                ->getColor()->setRGB('000000');
        }

        return $richText;
    }
    // private function toKhmerNumber(string $text): string
    // {
    //     $arabic = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    //     $khmer  = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];

    //     return str_replace($arabic, $khmer, $text);
    // }
}
