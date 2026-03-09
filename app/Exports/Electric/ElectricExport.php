<?php

namespace App\Exports\Electric;

use App\Models\BeginCredit\Account;
use App\Models\BeginCredit\AccountSub;
use App\Models\BeginCredit\BeginVoucher;
use App\Models\BeginCredit\Ministry;
use App\Models\Chapter;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Http\Request;

class ElectricExport
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

        $templatePath = public_path('electric_template.xlsx');
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


        foreach ($this->data as $index => $item) {

            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $item->title_entity);
            $sheet->setCellValue("C{$row}", $item->location_number_use);

            $sheet->setCellValue("D{$row}", date('d/m/Y', strtotime($item->date)));
            $start = date('d/m/Y', strtotime($item->use_start));
            $end   = date('d/m/Y', strtotime($item->use_end));
            $sheet->setCellValue("E{$row}", "{$start} - {$end}");
            $sheet->setCellValue("F{$row}", $item->kilo);
            $sheet->setCellValue("G{$row}", $item->reactive_energy);
            $sheet->setCellValue("H{$row}", "$item->cost_total, រៀល");

            $row++;
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

        $fileName = 'electric_template.xlsx';

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
