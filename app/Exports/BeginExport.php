<?php

namespace App\Exports;

use App\Models\BeginCredit\Account;
use App\Models\BeginCredit\BeginVoucher;
use App\Models\BeginCredit\Ministry;
use App\Models\Chapter;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Http\Request;

class BeginExport
{
    // protected $params; 
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function export(Request $request)
    {

        $params =  $request->params;
        $id = decode_params($params);


        // dd($id);


        $templatePath = public_path('template.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();
        // if (request('start_date') && request('end_date')) {
        //     $startDate = Carbon::parse(request('start_date'));
        //     $endDate = Carbon::parse(request('end_date'));
        //     $dateRangeText = convertToKhmerNumber($startDate->day) . ' ' . getKhmerMonth($startDate->month) . ' ' . convertToKhmerNumber($startDate->year) . ' ដល់ ' . convertToKhmerNumber($endDate->day) . ' ' . getKhmerMonth($endDate->month) . ' ' . convertToKhmerNumber($endDate->year);
        // } else {
        //     $currentMonth = date('n');
        //     $currentYear = date('Y');
        //     $dateRangeText = 'ប្រចាំ​ ខែ ' . getKhmerMonth($currentMonth) . ' ឆ្នាំ ' . convertToKhmerNumber($currentYear);
        // }

        $row = 10;
        $sheet->getStyle("A{$row}:T{$row}")->applyFromArray([
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

        // $sheet->setCellValue("A{$row}", $dateRangeText);
        $sheet->mergeCells("A{$row}:T{$row}");
        $row = 14;


        $ministry = Ministry::where('id', $id)->first();
        $chapterName = Chapter::where('ministry_id', $ministry->id)->pluck('name', 'no');
        $chapterNo = Chapter::where('ministry_id', $ministry->id)->pluck('no');
        $accountName = Account::where('ministry_id', $ministry->id)
            ->pluck('name', 'no');

        // dd($accountName);
        $begin = BeginVoucher::all();

        foreach ($chapterName as $chp => $chapterData) {
            $firstCode = true;

            $codeName = $chapterName ?? 'Unknown';

            // dd($codeName);


            $codeTotals = [
                'fin_law' => 0,
                'current_loan' => 0,
                'internal_increase' => 0,
                'unexpected_increase' => 0,
                'additional_increase' => 0,
                'decrease' => 0,
                'editorial' => 0,
                'new_credit_status' => 0,
                'early_balance' => 0,
                'apply' => 0,
                'deadline_balance' => 0,
                'credit' => 0,
                'law_average' => 0,  // Calculated later
                'law_correction' => 0, // Calculated later
            ];

            // dd($codeTotals);

            $sheet->getStyle('A' . $row . ':T' . $row)->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'B5F556', // Green background color
                    ]
                ]
            ]);

            // Calculate totals for each result in the sub-account key
            // foreach ($chapterData as $result) {
            //     $loan = $result->loans;
            //     $codeTotals['fin_law'] += (float)$result->fin_law;
            //     $codeTotals['current_loan'] += (float)$result->current_loan;

            //     if (!empty($loan)) {
            //         $codeTotals['internal_increase'] += (float)$loan->internal_increase;
            //         $codeTotals['unexpected_increase'] += (float)$loan->unexpected_increase;
            //         $codeTotals['additional_increase'] += (float)$loan->additional_increase;
            //         $codeTotals['decrease'] += (float)$loan->decrease;
            //         $codeTotals['editorial'] += (float)$loan->editorial;
            //         $codeTotals['new_credit_status'] += (float)$loan->new_credit_status;
            //         $codeTotals['early_balance'] += (float)$loan->early_balance;
            //         $codeTotals['apply'] += (float)$loan->apply;
            //         $codeTotals['deadline_balance'] += (float)$loan->deadline_balance;
            //         $codeTotals['credit'] += (float)$loan->credit;
            //         $codeTotals['law_average'] += (float)$loan->law_average;
            //         $codeTotals['law_correction'] += (float)$loan->law_correction;
            //     } else {
            //         $sheet->setCellValue('H' . $row, 0);
            //         $sheet->setCellValue('I' . $row, 0);
            //         $sheet->setCellValue('J' . $row, 0);
            //         $sheet->setCellValue('K' . $row, 0);
            //         $sheet->setCellValue('L' . $row, 0);
            //         $sheet->setCellValue('M' . $row, 0);
            //         $sheet->setCellValue('N' . $row, 0);
            //         $sheet->setCellValue('O' . $row, 0);
            //         $sheet->setCellValue('P' . $row, 0);
            //         $sheet->setCellValue('Q' . $row, 0);
            //         $sheet->setCellValue('R' . $row, 0);
            //         $sheet->setCellValue('S' . $row, 0);
            //         $sheet->setCellValue('T' . $row, 0);
            //     }
            //     $codeTotals['new_credit_status'] += (float)$result->new_credit_status;
            //     $codeTotals['early_balance'] += (float)$result->early_balance;
            //     $codeTotals['apply'] += (float)$result->apply;
            //     $codeTotals['deadline_balance'] += (float)$result->deadline_balance;
            //     $codeTotals['credit'] += (float)$result->credit;
            //     $codeTotals['law_average'] += (float)$result->law_average;
            //     $codeTotals['law_correction'] += (float)$result->law_correction;

            //     // Calculate Law Average and Law Correction
            //     if ($codeTotals['fin_law'] != 0) {
            //         $codeTotals['law_average'] = $codeTotals['deadline_balance'] / $codeTotals['fin_law'];
            //     } else {
            //         $subAccountTotals['law_average'] = 0;
            //     }

            //     if ($codeTotals['new_credit_status'] != 0) {
            //         $codeTotals['law_correction'] = $codeTotals['deadline_balance'] / $codeTotals['new_credit_status'];
            //     } else {
            //         $codeTotals['law_correction'] = 0;
            //     }
            // }


            if ($firstCode) {
                $sheet->setCellValue('A' . $row, $chp);
                $sheet->setCellValue('E' . $row, $codeName);
                $sheet->setCellValue('F' . $row, $codeTotals['fin_law']); // Set financial law total
                $sheet->setCellValue('G' . $row, $codeTotals['current_loan']); // Set current loan total
                $sheet->setCellValue('H' . $row, $codeTotals['internal_increase']); // Set internal increase
                $sheet->setCellValue('I' . $row, $codeTotals['unexpected_increase']); // Set unexpected increase
                $sheet->setCellValue('J' . $row, $codeTotals['additional_increase']); // Set additional increase
                $sheet->setCellValue('K' . $row, $codeTotals['internal_increase'] + $codeTotals['unexpected_increase'] + $codeTotals['additional_increase']);
                $sheet->setCellValue('L' . $row, $codeTotals['decrease']);
                $sheet->setCellValue('M' . $row, $codeTotals['editorial']);
                $sheet->setCellValue('N' . $row, $codeTotals['new_credit_status']);
                $sheet->setCellValue('O' . $row, $codeTotals['early_balance']);
                $sheet->setCellValue('P' . $row, $codeTotals['apply']);
                $sheet->setCellValue('Q' . $row, $codeTotals['deadline_balance']);
                $sheet->setCellValue('R' . $row, $codeTotals['credit']);
                $sheet->setCellValue('S' . $row, $codeTotals['law_average']);
                $sheet->setCellValue('T' . $row, $codeTotals['law_correction']);

                $firstCode = false;
                $row++;
            } else {
                // Hide repeated code and name
                $sheet->getStyle('A' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                $sheet->getStyle('E' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                $sheet->getStyle('F' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                $sheet->getStyle('G' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                $sheet->getStyle('H' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                $sheet->getStyle('I' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                $sheet->getStyle('J' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                $sheet->getStyle('K' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                $sheet->getStyle('L' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                $sheet->getStyle('M' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                $sheet->getStyle('N' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                $sheet->getStyle('O' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                $sheet->getStyle('P' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                $sheet->getStyle('Q' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                $sheet->getStyle('R' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                $sheet->getStyle('S' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                $sheet->getStyle('T' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                $sheet->getRowDimension($row)->setRowHeight(-1);
            }

            foreach ($accountName as $accountKeyId => $reportsByAccountKey) {
                $firstAccountKey = true; 

                $accountKeyName = $accountName ?? 'Unknown';
                $accountTotals = [
                    'fin_law' => 0,
                    'current_loan' => 0,
                    'internal_increase' => 0,
                    'unexpected_increase' => 0,
                    'additional_increase' => 0,
                    'decrease' => 0,
                    'editorial' => 0,
                    'new_credit_status' => 0,
                    'early_balance' => 0,
                    'apply' => 0,
                    'deadline_balance' => 0,
                    'credit' => 0,
                    'law_average' => 0,  
                    'law_correction' => 0, 
                ];

                // Calculate totals for each result in the sub-account key
                // foreach ($reportsByAccountKey as $result) {
                //     $loan = $result->loans;
                //     $accountTotals['fin_law'] += (float)$result->fin_law;
                //     $accountTotals['current_loan'] += (float)$result->current_loan;

                //     if (!empty($loan)) {
                //         $accountTotals['internal_increase'] += (float)$loan->internal_increase;
                //         $accountTotals['unexpected_increase'] += (float)$loan->unexpected_increase;
                //         $accountTotals['additional_increase'] += (float)$loan->additional_increase;
                //         $accountTotals['decrease'] += (float)$loan->decrease;
                //         $accountTotals['editorial'] += (float)$loan->editorial;
                //         $accountTotals['new_credit_status'] += (float)$loan->new_credit_status;
                //         $accountTotals['early_balance'] += (float)$loan->early_balance;
                //         $accountTotals['apply'] += (float)$loan->apply;
                //         $accountTotals['deadline_balance'] += (float)$loan->deadline_balance;
                //         $accountTotals['credit'] += (float)$loan->credit;
                //         $accountTotals['law_average'] += (float)$loan->law_average;
                //         $accountTotals['law_correction'] += (float)$loan->law_correction;
                //     } else {
                //         $sheet->setCellValue('H' . $row, 0);
                //         $sheet->setCellValue('I' . $row, 0);
                //         $sheet->setCellValue('J' . $row, 0);
                //         $sheet->setCellValue('K' . $row, 0);
                //         $sheet->setCellValue('L' . $row, 0);
                //         $sheet->setCellValue('M' . $row, 0);
                //         $sheet->setCellValue('N' . $row, 0);
                //         $sheet->setCellValue('O' . $row, 0);
                //         $sheet->setCellValue('P' . $row, 0);
                //         $sheet->setCellValue('Q' . $row, 0);
                //         $sheet->setCellValue('R' . $row, 0);
                //         $sheet->setCellValue('S' . $row, 0);
                //         $sheet->setCellValue('T' . $row, 0);
                //     }
                //     $accountTotals['new_credit_status'] += (float)$result->new_credit_status;
                //     $accountTotals['early_balance'] += (float)$result->early_balance;
                //     $accountTotals['apply'] += (float)$result->apply;
                //     $accountTotals['deadline_balance'] += (float)$result->deadline_balance;
                //     $accountTotals['credit'] += (float)$result->credit;
                //     $accountTotals['law_average'] += (float)$result->law_average;
                //     $accountTotals['law_correction'] += (float)$result->law_correction;

                //     // Calculate Law Average and Law Correction
                //     if ($accountTotals['fin_law'] != 0) {
                //         $accountTotals['law_average'] = $accountTotals['deadline_balance'] / $accountTotals['fin_law'];
                //     } else {
                //         $accountTotals['law_average'] = 0;
                //     }

                //     if ($accountTotals['new_credit_status'] != 0) {
                //         $accountTotals['law_correction'] = $accountTotals['deadline_balance'] / $accountTotals['new_credit_status'];
                //     } else {
                //         $accountTotals['law_correction'] = 0;
                //     }
                // }

                if ($firstAccountKey) {
                    $sheet->setCellValue('B' . $row, $accountKeyId);
                    $sheet->setCellValue('E' . $row, $accountKeyName);
                    $sheet->setCellValue('F' . $row, $accountTotals['fin_law']); 
                    $sheet->setCellValue('G' . $row, $accountTotals['current_loan']); 
                    $sheet->setCellValue('H' . $row, $accountTotals['internal_increase']); 
                    $sheet->setCellValue('I' . $row, $accountTotals['unexpected_increase']); 
                    $sheet->setCellValue('J' . $row, $accountTotals['additional_increase']);
                    $sheet->setCellValue('K' . $row, $accountTotals['internal_increase'] + $accountTotals['unexpected_increase'] + $accountTotals['additional_increase']); // Account Key Name
                    $sheet->setCellValue('L' . $row, $accountTotals['decrease']); 
                    $sheet->setCellValue('M' . $row, $accountTotals['editorial']); // Account Key Name
                    $sheet->setCellValue('N' . $row, $accountTotals['new_credit_status']); // Account Key Name
                    $sheet->setCellValue('O' . $row, $accountTotals['early_balance']); // Account Key Name
                    $sheet->setCellValue('P' . $row, $accountTotals['apply']); // Account Key Name
                    $sheet->setCellValue('Q' . $row, $accountTotals['deadline_balance']); // Account Key Name
                    $sheet->setCellValue('R' . $row, $accountTotals['credit']); // Account Key Name
                    $sheet->setCellValue('S' . $row, $accountTotals['law_average']); // Account Key Name
                    $sheet->setCellValue('T' . $row, $accountTotals['law_correction']); // Account Key Name

                    $firstAccountKey = false;
                    $row++;
                } else {
                    // Hide repeated account_key and name
                    $sheet->getStyle('B' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                    $sheet->getStyle('E' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                    $sheet->getStyle('F' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                    $sheet->getStyle('G' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                    $sheet->getStyle('H' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                    $sheet->getStyle('I' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                    $sheet->getStyle('J' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                    $sheet->getStyle('K' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                    $sheet->getStyle('L' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                    $sheet->getStyle('M' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                    $sheet->getStyle('N' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                    $sheet->getStyle('O' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                    $sheet->getStyle('P' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                    $sheet->getStyle('Q' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                    $sheet->getStyle('R' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                    $sheet->getStyle('S' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                    $sheet->getStyle('T' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                    // $sheet->getRowDimension($row)->setRowHeight(-1); 
                }

                // $groupedBySubAccountKey = $reportsByAccountKey->groupBy(function ($result) {
                //     return $result->subAccountKey->sub_account_key ?? 'Unknown';
                // });


                // foreach ($groupedBySubAccountKey as $subAccountKeyId => $reportsBySubAccountKey) {

                //     // Initialize totals for each Sub Account Key
                //     $firstSubAccountKey = true;
                //     $subAccountKeyName = $reportsBySubAccountKey->first()->subAccountKey->name_sub_account_key ?? 'Unknown';

                //     // Initialize total variables for sub-account calculations
                //     $subAccountTotals = [
                //         'fin_law' => 0,
                //         'current_loan' => 0,
                //         'internal_increase' => 0,
                //         'unexpected_increase' => 0,
                //         'additional_increase' => 0,
                //         'decrease' => 0,
                //         'editorial' => 0,
                //         'new_credit_status' => 0,
                //         'early_balance' => 0,
                //         'apply' => 0,
                //         'deadline_balance' => 0,
                //         'credit' => 0,
                //         'law_average' => 0,
                //         'law_correction' => 0
                //     ];

                //     // Calculate totals for each result in the sub-account key
                //     foreach ($reportsBySubAccountKey as $result) {
                //         $loan = $result->loans;
                //         $subAccountTotals['fin_law'] += (float)$result->fin_law;
                //         $subAccountTotals['current_loan'] += (float)$result->current_loan;

                //         if (!empty($loan)) {
                //             $subAccountTotals['internal_increase'] += (float)$loan->internal_increase;
                //             $subAccountTotals['unexpected_increase'] += (float)$loan->unexpected_increase;
                //             $subAccountTotals['additional_increase'] += (float)$loan->additional_increase;
                //             $subAccountTotals['decrease'] += (float)$loan->decrease;
                //             $subAccountTotals['editorial'] += (float)$loan->editorial;
                //             $subAccountTotals['new_credit_status'] += (float)$loan->new_credit_status;
                //             $subAccountTotals['early_balance'] += (float)$loan->early_balance;
                //             $subAccountTotals['apply'] += (float)$loan->apply;
                //             $subAccountTotals['deadline_balance'] += (float)$loan->deadline_balance;
                //             $subAccountTotals['credit'] += (float)$loan->credit;
                //             $subAccountTotals['law_average'] += (float)$loan->law_average;
                //             $subAccountTotals['law_correction'] += (float)$loan->law_correction;
                //         }
                //         $subAccountTotals['new_credit_status'] += (float)$result->new_credit_status;
                //         $subAccountTotals['early_balance'] += (float)$result->early_balance;
                //         $subAccountTotals['apply'] += (float)$result->apply;
                //         $subAccountTotals['deadline_balance'] += (float)$result->deadline_balance;
                //         $subAccountTotals['credit'] += (float)$result->credit;
                //         $subAccountTotals['law_average'] += (float)$result->law_average;
                //         $subAccountTotals['law_correction'] += (float)$result->law_correction;

                //         // Final values and calculations
                //         if ($subAccountTotals['fin_law'] != 0) {
                //             $subAccountTotals['law_average'] = $subAccountTotals['deadline_balance'] / $subAccountTotals['fin_law'];
                //         } else {
                //             $subAccountTotals['law_average'] = 0;
                //         }

                //         if ($subAccountTotals['new_credit_status'] != 0) {
                //             $subAccountTotals['law_correction'] = $subAccountTotals['deadline_balance'] / $subAccountTotals['new_credit_status'];
                //         } else {
                //             $subAccountTotals['law_correction'] = 0;
                //         }
                //     }

                //     if ($firstSubAccountKey) {
                //         // Set values for the Sub Account Key header row
                //         $sheet->setCellValue('C' . $row, $subAccountKeyId);
                //         $sheet->setCellValue('E' . $row, $subAccountKeyName);
                //         $sheet->setCellValue('F' . $row, $subAccountTotals['fin_law']);
                //         $sheet->setCellValue('G' . $row, $subAccountTotals['current_loan']);
                //         $sheet->setCellValue('H' . $row, $subAccountTotals['internal_increase']);
                //         $sheet->setCellValue('I' . $row, $subAccountTotals['unexpected_increase']);
                //         $sheet->setCellValue('J' . $row, $subAccountTotals['additional_increase']);
                //         $sheet->setCellValue('K' . $row, $subAccountTotals['internal_increase'] + $subAccountTotals['unexpected_increase'] + $subAccountTotals['additional_increase']);
                //         $sheet->setCellValue('L' . $row, $subAccountTotals['decrease']);
                //         $sheet->setCellValue('M' . $row, $subAccountTotals['editorial']);
                //         $sheet->setCellValue('N' . $row, $subAccountTotals['new_credit_status']);
                //         $sheet->setCellValue('O' . $row, $subAccountTotals['early_balance']);
                //         $sheet->setCellValue('P' . $row, $subAccountTotals['apply']);
                //         $sheet->setCellValue('Q' . $row, $subAccountTotals['deadline_balance']);
                //         $sheet->setCellValue('R' . $row, $subAccountTotals['credit']);
                //         $sheet->setCellValue('S' . $row, $subAccountTotals['law_average']);
                //         $sheet->setCellValue('T' . $row, $subAccountTotals['law_correction']);

                //         $firstSubAccountKey = false;
                //         $row++;
                //     }

                //     foreach ($reportsBySubAccountKey as $result) {
                //         // Set values for individual rows for each report under this Sub Account Key
                //         $loan = $result->loans;

                //         $sheet->setCellValue('D' . $row, $result->report_key);
                //         $sheet->setCellValue('E' . $row, $result->name_report_key);
                //         $sheet->setCellValue('F' . $row, $result->fin_law);
                //         $sheet->setCellValue('G' . $row, $result->current_loan);

                //         // Check for loan values and set cells accordingly
                //         if (!empty($loan)) {
                //             $sheet->setCellValue('H' . $row, $loan->internal_increase);
                //             $sheet->setCellValue('I' . $row, $loan->unexpected_increase);
                //             $sheet->setCellValue('J' . $row, $loan->additional_increase);
                //             $sheet->setCellValue('K' . $row, $loan->internal_increase + $loan->unexpected_increase + $loan->additional_increase);
                //             $sheet->setCellValue('L' . $row, $loan->decrease);
                //             $sheet->setCellValue('M' . $row, $loan->editorial);
                //             $sheet->setCellValue('N' . $row, $loan->new_credit_status);
                //             $sheet->setCellValue('O' . $row, $loan->early_balance);
                //             $sheet->setCellValue('P' . $row, $loan->apply);
                //             $sheet->setCellValue('Q' . $row, $loan->deadline_balance);
                //             $sheet->setCellValue('R' . $row, $loan->credit);
                //             $sheet->setCellValue('S' . $row, $loan->law_average);
                //             $sheet->setCellValue('T' . $row, $loan->law_correction);
                //         } else {
                //             $sheet->setCellValue('H' . $row, 0);
                //             $sheet->setCellValue('I' . $row, 0);
                //             $sheet->setCellValue('J' . $row, 0);
                //             $sheet->setCellValue('K' . $row, 0);
                //             $sheet->setCellValue('L' . $row, 0);
                //             $sheet->setCellValue('M' . $row, 0);
                //             $sheet->setCellValue('N' . $row, 0);
                //             $sheet->setCellValue('O' . $row, 0);
                //             $sheet->setCellValue('P' . $row, 0);
                //             $sheet->setCellValue('Q' . $row, 0);
                //             $sheet->setCellValue('R' . $row, 0);
                //             $sheet->setCellValue('S' . $row, 0);
                //             $sheet->setCellValue('T' . $row, 0);
                //         }
                //         $sheet->setCellValue('N' . $row, $result->new_credit_status);
                //         $sheet->setCellValue('O' . $row, $result->early_balance);
                //         $sheet->setCellValue('P' . $row, $result->apply);
                //         $sheet->setCellValue('Q' . $row, $result->deadline_balance);
                //         $sheet->setCellValue('R' . $row, $result->credit);

                //         // Final values and calculations
                //         if ($result->fin_law != 0) {
                //             $law_average = $result->deadline_balance / $result->fin_law;
                //         } else {
                //             $law_average = 0;
                //         }
                //         if ($result->new_credit_status != 0) {
                //             $law_correction = $result->deadline_balance / $result->new_credit_status;
                //         } else {
                //             $law_correction = 0;
                //         }
                //         $sheet->setCellValue('S' . $row, max(0, $law_average));
                //         $sheet->setCellValue('T' . $row, max(0, $law_correction));
                //         $row++;
                //     }
                // }
            }
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

        $fileName = 'template.xlsx';

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
