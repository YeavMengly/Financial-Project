<?php

namespace App\Exports\Mission;

use App\Models\Content\Ministry;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Facades\File;

class MissionExport
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

        $params =  $request->params;
        $id = decode_params($params);

        $templatePath = storage_path('app/excel/template/template_mission.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $khmerMonths = ['មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'];
        $currentMonth =  $khmerMonths[date('n') - 1];
        $khmerNumbers = ['0' => '០', '1' => '១', '2' => '២', '3' => '៣', '4' => '៤', '5' => '៥', '6' => '៦', '7' => '៧', '8' => '៨', '9' => '៩'];
        $currentYear = strtr(date('Y'), $khmerNumbers);

        // if ($this->startDate && $this->endDate) {

        //     $start = strtotime($this->startDate);
        //     $end = strtotime($this->endDate);

        //     $startDay = strtr(date('d', $start), $khmerNumbers);
        //     $startMonth = $khmerMonths[date('n', $start) - 1];
        //     $startYear = strtr(date('Y', $start), $khmerNumbers);

        //     $endDay = strtr(date('d', $end), $khmerNumbers);
        //     $endMonth = $khmerMonths[date('n', $end) - 1];
        //     $endYear = strtr(date('Y', $end), $khmerNumbers);

        //     $dateRangeText = "ចាប់ពីថ្ងៃទី {$startDay} ខែ {$startMonth} ឆ្នាំ {$startYear} ដល់ថ្ងៃទី {$endDay} ខែ {$endMonth} ឆ្នាំ {$endYear}";
        // } else {
        //     $dateRangeText = "ប្រចាំ ខែ {$currentMonth} ឆ្នាំ {$currentYear}";
        // }

        $missionTotalStyle = [
            'font' => [
                'bold' => true,
                'size' => 9,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFE2F0D9',
                ],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => [
                        'argb' => 'FF000000',
                    ],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $grandTotalStyle = [
            'font' => [
                'bold' => true,
                'size' => 9,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFE699',
                ],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => [
                        'argb' => 'FF000000',
                    ],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        // Font A:J
        $sheet->getStyle('A:J')
            ->getFont()
            ->setName('Khmer OS Siemreap')
            ->setSize(9);


        // Alignment A:U
        $sheet->getStyle('A:U')
            ->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            )
            ->setVertical(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            )
            ->setWrapText(true);

        $row = 5;
        $data = $this->data;

        $ministry = Ministry::where('id', $id)->first();


        $missionNumber = 1;

        foreach ($data as $missionId => $employees) {

            $mission = $employees->first();
            $missionStartRow = $row;

            // Employee index starts from 1 for each mission
            $employeeIndex = 1;

            /*
            |--------------------------------------------------------------------------
            | Employee rows
            |--------------------------------------------------------------------------
            */
            foreach ($employees as $employee) {

                // Skip if no employee
                if (!$employee->employee_id) {
                    continue;
                }

                // Example: 1.1, 1.2, 1.3
                $index = $missionNumber . '.' . $employeeIndex;

                $sheet->setCellValue(
                    "A{$row}",
                    $index
                );

                $sheet->setCellValue(
                    "B{$row}",
                    $employee->name_kh ?? '-'
                );

                $sheet->setCellValue(
                    "C{$row}",
                    $employee->position_name ?? '-'
                );

                $sheet->setCellValue(
                    "D{$row}",
                    $employee->level_name ?? '-'
                );

                // Mission information
                $sheet->setCellValue("E{$row}", $employee->legal_number ?? '-');
                $sheet->setCellValue(
                    "F{$row}",
                    !empty($employee->legal_date)
                        ? Carbon::parse($employee->legal_date)->format('d-M-y')
                        : '-'
                );
                $sheet->setCellValue("G{$row}", $employee->description ?? '-');
                $sheet->setCellValue("H{$row}", $employee->province_name ?? '-');


                $sheet->setCellValue(
                    "I{$row}",
                    !empty($employee->start_date)
                        ? Carbon::parse($employee->start_date)->format('d-M-y')
                        : '-'
                );

                $sheet->setCellValue(
                    "J{$row}",
                    !empty($employee->end_date)
                        ? Carbon::parse($employee->end_date)->format('d-M-y')
                        : '-'
                );

                $sheet->setCellValue(
                    "K{$row}",
                    $employee->days_count ?? '-'
                );

                $sheet->setCellValue(
                    "L{$row}",
                    $employee->nights_count ?? '-'
                );

                $sheet->setCellValue(
                    "M{$row}",
                    number_format($employee->travel_allowance ?? 0)
                );

                $sheet->setCellValue(
                    "N{$row}",
                    number_format($employee->pocket_money ?? 0)
                );

                $sheet->setCellValue(
                    "O{$row}",
                    number_format($employee->total_pocket_money ?? 0)
                );

                $sheet->setCellValue(
                    "P{$row}",
                    number_format($employee->meal_money ?? 0)
                );

                $sheet->setCellValue(
                    "Q{$row}",
                    number_format($employee->total_meal_money ?? 0)
                );

                $sheet->setCellValue(
                    "R{$row}",
                    number_format($employee->accommodation_money ?? 0)
                );

                $sheet->setCellValue(
                    "S{$row}",
                    number_format($employee->total_accommodation_money ?? 0)
                );

                $sheet->setCellValue(
                    "T{$row}",
                    ''
                );

                $sheet->setCellValue(
                    "U{$row}",
                    number_format($employee->total ?? 0)
                );

                $row++;

                // Increase employee index
                $employeeIndex++;
            }
            $missionEndRow = $row - 1;
            if ($missionEndRow > $missionStartRow) {

                $sheet->mergeCells("E{$missionStartRow}:E{$missionEndRow}");
                $sheet->mergeCells("F{$missionStartRow}:F{$missionEndRow}");
                $sheet->mergeCells("G{$missionStartRow}:G{$missionEndRow}");
                $sheet->mergeCells("H{$missionStartRow}:H{$missionEndRow}");

                $sheet->getStyle("E{$missionStartRow}:H{$missionEndRow}")
                    ->getAlignment()
                    ->setHorizontal(
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                    )
                    ->setVertical(
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                    )
                    ->setWrapText(true);
            }

            /*
            |--------------------------------------------------------------------------
            | Mission Total
            |--------------------------------------------------------------------------
            */

            // Your existing total code...
            $missionTravel_allowance = $employees->sum(function ($employee) {
                return (float) ($employee->travel_allowance ?? 0);
            });

            $missionPocket_money = $employees->sum(function ($employee) {
                return (float) ($employee->pocket_money ?? 0);
            });

            $missionTotal_pocket_money = $employees->sum(function ($employee) {
                return (float) ($employee->total_pocket_money ?? 0);
            });

            $missionMeal_money = $employees->sum(function ($employee) {
                return (float) ($employee->meal_money ?? 0);
            });

            $missionTotal_meal_money = $employees->sum(function ($employee) {
                return (float) ($employee->total_meal_money ?? 0);
            });

            $missionAccommodation_money = $employees->sum(function ($employee) {
                return (float) ($employee->accommodation_money ?? 0);
            });

            $missionTotal_accommodation_money = $employees->sum(function ($employee) {
                return (float) ($employee->total_accommodation_money ?? 0);
            });

            $missionTotal = $employees->sum(function ($employee) {
                return (float) ($employee->total ?? 0);
            });

            $sheet->mergeCells("A{$row}:L{$row}");

            $sheet->setCellValue(
                "A{$row}",
                'សរុបរួម'
            );

            $sheet->getStyle("A{$row}:L{$row}")
                ->getAlignment()
                ->setHorizontal(
                    \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                )
                ->setVertical(
                    \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                );

            $sheet->setCellValue("M{$row}", number_format($missionTravel_allowance));
            $sheet->setCellValue("N{$row}", number_format($missionPocket_money));
            $sheet->setCellValue("O{$row}", number_format($missionTotal_pocket_money));
            $sheet->setCellValue("P{$row}", number_format($missionMeal_money));
            $sheet->setCellValue("Q{$row}", number_format($missionTotal_meal_money));
            $sheet->setCellValue("R{$row}", number_format($missionAccommodation_money));
            $sheet->setCellValue("S{$row}", number_format($missionTotal_accommodation_money));
            $sheet->setCellValue("T{$row}", '');
            $sheet->setCellValue("U{$row}", number_format($missionTotal));

            $sheet->getStyle("A{$row}:U{$row}")
                ->applyFromArray($missionTotalStyle);

            $row++;

            // Next mission
            $missionNumber++;
        }
        $exportDirectory = storage_path('app/excel/export');

        if (!File::exists($exportDirectory)) {
            File::makeDirectory($exportDirectory, 0755, true);
        }

        $fileName = 'mission.xlsx';

        $outputPath = $exportDirectory . '/' . $fileName;

        $writer = new Xlsx($spreadsheet);
        $writer->save($outputPath);

        return response()->download(
            $outputPath,
            $fileName,
            [
                'Content-Type' =>
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );


        // $fileName = 'template_mission.xlsx';

        // return response()->streamDownload(function () use ($spreadsheet) {
        //     $writer = new Xlsx($spreadsheet);
        //     $writer->save('php://output');
        // }, $fileName, [
        //     'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        //     'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        //     'Cache-Control' => 'max-age=0',
        // ]);
        // 1. Define temporary file path on disk
        // $fileName = 'budget_report_' . time() . '.xlsx';
        // $directory = 'app/exports';
        // $fullDirectoryPath = storage_path($directory);

        // if (!file_exists($fullDirectoryPath)) {
        //     mkdir($fullDirectoryPath, 0755, true);
        // }

        // $filePath = $fullDirectoryPath . '/' . $fileName;

        // // 2. Save the spreadsheet to the temporary file path
        // $writer = new Xlsx($spreadsheet);
        // $writer->save($filePath);

        // // 3. Return download response and automatically delete the file from disk after sending
        // return response()->download($filePath, $fileName, [
        //     'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        //     'Cache-Control' => 'max-age=0',
        // ])->deleteFileAfterSend(true);
    }
}
