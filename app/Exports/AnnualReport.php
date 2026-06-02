<?php

namespace App\Exports;

use App\Models\Content\Account;
use App\Models\Content\AccountSub;
use App\Models\BeginCredit\BeginVoucher;
use App\Models\Content\Ministry;
use App\Models\Content\Chapter;
use App\Models\Content\Cluster;
use App\Models\Content\Program;
use App\Models\Content\ProgramSub;
use App\Models\Content\Type;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Http\Request;

class AnnualReport
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

        $templatePath = public_path('template_annual_report.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle("A1:ER4")->applyFromArray([
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
        $columnMap = [
            '101' => 'G',
            '10101' => 'H',
            '10102' => 'I',
            '10103' => 'J',
            '10104' => 'K',
            '10105' => 'L',
            '102' => 'M',
            '10201' => 'N',
            '10202' => 'O',
            '10203' => 'P',
            '10204' => 'Q',
            '10205' => 'R',
            '10206' => 'S',
            '103' => 'T',
            '10301' => 'U',
            '10302' => 'V',
            '10303' => 'W',
            '10304' => 'X',
            '10305' => 'Y',
            '104' => 'Z',
            '10401' => 'AA',
            '10402' => 'AB',
            '10403' => 'AC',
            '10404' => 'AD',
            '10405' => 'AE',
            '105' => 'AF',
            '10501' => 'AG',
            '10502' => 'AH',
            '10503' => 'AI',
            '10504' => 'AJ',
            '10505' => 'AK',
            '10506' => 'AL',
            '106' => 'AM',
            '10601' => 'AN',
            '10602' => 'AO',
            '10603' => 'AP',
            '10604' => 'AQ',
            '10605' => 'AR',
            '201' => 'AS',
            '20101' => 'AT',
        ];
        $row = 6;
        $col = 'E';
        $grouped = $this->data
            ->sortBy([
                'type_id',
                'chapter_id',
                'account_id',
                'account_sub_id',
                'program_id',
                'program_sub_id',
                'cluster_id'
            ])
            ->groupBy('type_id')
            ->sortKeys()
            ->map(function ($typeGroup) {

                return $typeGroup
                    ->groupBy('chapter_id')
                    ->sortKeys()
                    ->map(function ($chapterGroup) {

                        return $chapterGroup
                            ->groupBy('account_id')
                            ->sortKeys()
                            ->map(function ($accountGroup) {

                                return $accountGroup
                                    ->groupBy('account_sub_id')
                                    ->sortKeys()
                                    ->map(function ($programGroup) {

                                        return $programGroup
                                            ->groupBy('program_id')
                                            ->sortKeys()
                                            ->map(function ($programSubGroup) {

                                                return $programSubGroup
                                                    ->groupBy('program_sub_id')
                                                    ->sortKeys()
                                                    ->map(function ($clusterGroup) {

                                                        return $clusterGroup
                                                            ->groupBy('cluster_id')
                                                            ->sortKeys();
                                                    });
                                            });
                                    });
                            });
                    });
            });

        $chapterId = $this->data->pluck('chapter_id')->filter()->unique();
        $accountId = $this->data->pluck('account_id')->filter()->unique();
        $accountSubId     = $this->data->pluck('account_sub_id')->filter()->unique();
        $ministry = Ministry::where('id', $id)->first();
        $typeMap = Type::all()->keyBy('id');

        $chapterMap = Chapter::where('ministry_id', $ministry->id)
            ->whereIn('no', $chapterId)
            ->get()
            ->keyBy('no');

        $accountMap = Account::where('ministry_id', $ministry->id)
            ->whereIn('no', $accountId)
            ->get()
            ->keyBy('no');

        $accountSubMap = AccountSub::where('ministry_id', $ministry->id)
            ->whereIn('no', $accountSubId)
            ->get()
            ->keyBy('no');

        $excelColumnMapping = [
            // PROGRAM 1
            'H' => '10101',
            'I' => '10102',
            'J' => '10103',
            'K' => '10104',
            'L' => '10105',
            'N' => '10201',
            'O' => '10202',
            'P' => '10203',
            'Q' => '10204',
            'R' => '10205',
            'S' => '10206',
            'U' => '10301',
            'V' => '10302',
            'W' => '10303',
            'X' => '10304',
            'Y' => '10305',
            'AA' => '10401',
            'AB' => '10402',
            'AC' => '10403',
            'AD' => '10404',
            'AE' => '10405',
            'AG' => '10501',
            'AH' => '10502',
            'AI' => '10503',
            'AJ' => '10504',
            'AK' => '10505',
            'AL' => '10506',
            'AN' => '10601',
            'AO' => '10602',
            'AP' => '10603',
            'AQ' => '10604',
            'AR' => '10605',

            // PROGRAM 2
            'AU' => '20101',
            'AV' => '20102',
            'AW' => '20103',
            'AX' => '20104',
            'AY' => '20105',
            'AZ' => '20106',
            'BB' => '20201',
            'BC' => '20202',
            'BD' => '20203',
            'BE' => '20204',
            'BF' => '20205',
            'BG' => '20206',
            'BH' => '20207',
            'BJ' => '20301',
            'BK' => '20302',
            'BL' => '20303',
            'BM' => '20304',
            'BN' => '20305',
            'BO' => '20306',
            'BP' => '20307',
            'BR' => '20401',
            'BS' => '20402',
            'BT' => '20403',
            'BU' => '20404',
            'BV' => '20405',
            'BX' => '20501',
            'BY' => '20502',
            'BZ' => '20503',
            'CA' => '20504',
            'CB' => '20505',

            // PROGRAM 3
            'CE' => '30101',
            'CF' => '30102',
            'CG' => '30103',
            'CH' => '30104',
            'CI' => '30105',
            'CK' => '30201',
            'CL' => '30202',
            'CM' => '30203',
            'CN' => '30204',
            'CO' => '30205',
            'CP' => '30206',
            'CR' => '30301',
            'CS' => '30302',
            'CT' => '30303',
            'CU' => '30304',
            'CV' => '30305', // Fixed CQ bug to CU
            'CX' => '30401',
            'CY' => '30402',
            'CZ' => '30403',
            'DA' => '30404',
            'DB' => '30405',
            'DD' => '30501',
            'DE' => '30502',
            'DF' => '30503',
            'DG' => '30504',
            'DH' => '30505',
            'DI' => '30506',
            'DJ' => '30507',

            // PROGRAM 5
            'DM' => '50101',
            'DN' => '50102',
            'DO' => '50103',
            'DP' => '50104',
            'DQ' => '50105',
            'DR' => '50106',
            'DS' => '50107',
            'DU' => '50201',
            'DV' => '50202',
            'DW' => '50203',
            'DX' => '50204',
            'DY' => '50205',
            'DZ' => '50206',
            'EB' => '50301',
            'EC' => '50302',
            'ED' => '50303',
            'EE' => '50304',
            'EF' => '50305',
            'EH' => '50401',
            'EI' => '50402',
            'EJ' => '50403',
            'EK' => '50404',
            'EL' => '50405',
            'EN' => '50501',
            'EO' => '50502',
            'EP' => '50503',
            'EQ' => '50504',
            'ER' => '50505'
        ];
        $summaryTotalColumns = [
            'E',
            'F',
            'G',
            'M',
            'T',
            'Z',
            'AF',
            'AM',
            'AS',
            'AT',
            'BA',
            'BI',
            'BQ',
            'BW',
            'CC',
            'CD',
            'CJ',
            'CQ',
            'CW',
            'DC',
            'DK',
            'DL',
            'DT',
            'EA',
            'EG',
            'EM'
        ];

        $allActiveColumns = array_merge($summaryTotalColumns, array_keys($excelColumnMapping));
        $allTypeRowHeaders = [];

        $row = 7;
        foreach ($grouped as $typeNo => $chapters) {
            $type = $typeMap->get($typeNo);
            $typeRowHeader = $row;
            $sheet->setCellValue("A{$row}", $type ? $type->number_type . ' ' . $type->name : '');
            $sheet->mergeCells("A{$row}:D{$row}");
            $sheet->getStyle("A{$row}:D{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:ER{$row}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '000000'],
                    'size' => 12,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],

            ]);
            $row++;

            $typeRows = [];

            // CHAPTERS
            foreach ($chapters as $chapterNo => $accounts) {
                $chapter = $chapterMap->get($chapterNo);
                $chapterRowHeader = $row;
                $typeToChaptersMap[$typeNo][] = $chapterRowHeader;
                $sheet->setCellValue("A{$row}", $chapterNo);
                $sheet->setCellValue("D{$row}", $chapter ? $chapter->name : '');
                $sheet->getStyle("A{$row}:ER{$row}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '000000'],
                        'size' => 12,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],

                ]);
                $row++;
                $chapterRows = [];

                // ACCOUNTS
                foreach ($accounts as $accountNo => $subAccounts) {
                    $account = $accountMap->get($accountNo);
                    $accountRowHeader = $row;
                    $chapterToAccountsMap[$chapterNo][] = $accountRowHeader;
                    $sheet->setCellValue("B{$row}", $accountNo);
                    $sheet->setCellValue("D{$row}", $account ? $account->name : '');
                    $sheet->getStyle("A{$row}:ER{$row}")->applyFromArray([
                        'font' => [
                            'color' => ['rgb' => '000000'],
                            'size' => 12,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],

                    ]);
                    $row++;
                    $accountRows = [];

                    // ACCOUNT SUB
                    foreach ($subAccounts as $accountSubNo => $programs) {
                        $accountSub = $accountSubMap->get($accountSubNo);
                        $accountToSubsMap[$accountNo][] = $row;
                        $sheet->setCellValue("C{$row}", $accountSubNo);
                        $sheet->setCellValue("D{$row}", $accountSub ? $accountSub->name : '');
                        $sheet->getStyle("A{$row}:ER{$row}")->applyFromArray([
                            'font' => [
                                'color' => ['rgb' => '000000'],
                                'size' => 12,
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                    'color' => ['rgb' => '000000'],
                                ],
                            ],

                        ]);
                        $accountRows[] = $row;
                        $rowValues = [];

                        foreach ($columnMap as $key => $col) {
                            $rowValues[$key] = 0;
                        }
                        foreach ($programs as $programSub) {
                            foreach ($programSub as $clusters) {
                                foreach ($clusters as $items) {
                                    foreach ($items as $item) {
                                        if ($item->account_sub_id != $accountSubNo) {
                                            continue;
                                        }
                                        $key = substr(preg_replace('/\D/', '', $item->no), -5);
                                        if (!isset($rowValues[$key])) {
                                            $rowValues[$key] = 0;
                                        }
                                        $rowValues[$key] += (float) $item->fin_law;
                                    }
                                }
                            }
                        }
                        // Program TOTAL
                        $sheet->setCellValue("E{$row}", "=SUM(F{$row}, AS{$row}, CC{$row}, DK{$row})");

                        /**
                         * WRITE CURRENT ROW (Account Sub Data)
                         */
                        // Program ID 1
                        $sheet->setCellValue("F{$row}", "=SUM(G{$row}, M{$row}, T{$row}, Z{$row}, AF{$row}, AM{$row})");
                        $sheet->setCellValue("G{$row}", "=SUM(H{$row}:L{$row})");
                        $sheet->setCellValue("H{$row}", $rowValues['10101'] ?? 0);
                        $sheet->setCellValue("I{$row}", $rowValues['10102'] ?? 0);
                        $sheet->setCellValue("J{$row}", $rowValues['10103'] ?? 0);
                        $sheet->setCellValue("K{$row}", $rowValues['10104'] ?? 0);
                        $sheet->setCellValue("L{$row}", $rowValues['10105'] ?? 0);

                        $sheet->setCellValue("M{$row}", "=SUM(N{$row}:S{$row})");
                        $sheet->setCellValue("N{$row}", $rowValues['10201'] ?? 0);
                        $sheet->setCellValue("O{$row}", $rowValues['10202'] ?? 0);
                        $sheet->setCellValue("P{$row}", $rowValues['10203'] ?? 0);
                        $sheet->setCellValue("Q{$row}", $rowValues['10204'] ?? 0);
                        $sheet->setCellValue("R{$row}", $rowValues['10205'] ?? 0);
                        $sheet->setCellValue("S{$row}", $rowValues['10206'] ?? 0);

                        $sheet->setCellValue("T{$row}", "=SUM(U{$row}:Y{$row})");
                        $sheet->setCellValue("U{$row}", $rowValues['10301'] ?? 0);
                        $sheet->setCellValue("V{$row}", $rowValues['10302'] ?? 0);
                        $sheet->setCellValue("W{$row}", $rowValues['10303'] ?? 0);
                        $sheet->setCellValue("X{$row}", $rowValues['10304'] ?? 0);
                        $sheet->setCellValue("Y{$row}", $rowValues['10305'] ?? 0);

                        $sheet->setCellValue("Z{$row}", "=SUM(AA{$row}:AE{$row})");
                        $sheet->setCellValue("AA{$row}", $rowValues['10401'] ?? 0);
                        $sheet->setCellValue("AB{$row}", $rowValues['10402'] ?? 0);
                        $sheet->setCellValue("AC{$row}", $rowValues['10403'] ?? 0);
                        $sheet->setCellValue("AD{$row}", $rowValues['10404'] ?? 0);
                        $sheet->setCellValue("AE{$row}", $rowValues['10405'] ?? 0);

                        $sheet->setCellValue("AF{$row}", "=SUM(AG{$row}:AL{$row})");
                        $sheet->setCellValue("AG{$row}", $rowValues['10501'] ?? 0);
                        $sheet->setCellValue("AH{$row}", $rowValues['10502'] ?? 0);
                        $sheet->setCellValue("AI{$row}", $rowValues['10503'] ?? 0);
                        $sheet->setCellValue("AJ{$row}", $rowValues['10504'] ?? 0);
                        $sheet->setCellValue("AK{$row}", $rowValues['10505'] ?? 0);
                        $sheet->setCellValue("AL{$row}", $rowValues['10506'] ?? 0);

                        $sheet->setCellValue("AM{$row}", "=SUM(AN{$row}:AR{$row})");
                        $sheet->setCellValue("AN{$row}", $rowValues['10601'] ?? 0);
                        $sheet->setCellValue("AO{$row}", $rowValues['10602'] ?? 0);
                        $sheet->setCellValue("AP{$row}", $rowValues['10603'] ?? 0);
                        $sheet->setCellValue("AQ{$row}", $rowValues['10604'] ?? 0);
                        $sheet->setCellValue("AR{$row}", $rowValues['10605'] ?? 0);

                        // Program ID 2
                        $sheet->setCellValue("AS{$row}", "=SUM(AT{$row}, BA{$row}, BI{$row}, BQ{$row}, BW{$row})");
                        $sheet->setCellValue("AT{$row}", "=SUM(AU{$row}:AZ{$row})");
                        $sheet->setCellValue("AU{$row}", $rowValues['20101'] ?? 0);
                        $sheet->setCellValue("AV{$row}", $rowValues['20102'] ?? 0);
                        $sheet->setCellValue("AW{$row}", $rowValues['20103'] ?? 0);
                        $sheet->setCellValue("AX{$row}", $rowValues['20104'] ?? 0);
                        $sheet->setCellValue("AY{$row}", $rowValues['20105'] ?? 0);
                        $sheet->setCellValue("AZ{$row}", $rowValues['20106'] ?? 0);

                        $sheet->setCellValue("BA{$row}", "=SUM(BB{$row}:BH{$row})");
                        $sheet->setCellValue("BB{$row}", $rowValues['20201'] ?? 0);
                        $sheet->setCellValue("BC{$row}", $rowValues['20202'] ?? 0);
                        $sheet->setCellValue("BD{$row}", $rowValues['20203'] ?? 0);
                        $sheet->setCellValue("BE{$row}", $rowValues['20204'] ?? 0);
                        $sheet->setCellValue("BF{$row}", $rowValues['20205'] ?? 0);
                        $sheet->setCellValue("BG{$row}", $rowValues['20206'] ?? 0);
                        $sheet->setCellValue("BH{$row}", $rowValues['20207'] ?? 0);

                        $sheet->setCellValue("BI{$row}", "=SUM(BJ{$row}:BP{$row})");
                        $sheet->setCellValue("BJ{$row}", $rowValues['20301'] ?? 0);
                        $sheet->setCellValue("BK{$row}", $rowValues['20302'] ?? 0);
                        $sheet->setCellValue("BL{$row}", $rowValues['20303'] ?? 0);
                        $sheet->setCellValue("BM{$row}", $rowValues['20304'] ?? 0);
                        $sheet->setCellValue("BN{$row}", $rowValues['20305'] ?? 0);
                        $sheet->setCellValue("BO{$row}", $rowValues['20306'] ?? 0);
                        $sheet->setCellValue("BP{$row}", $rowValues['20307'] ?? 0);

                        $sheet->setCellValue("BQ{$row}", "=SUM(BR{$row}:BV{$row})");
                        $sheet->setCellValue("BR{$row}", $rowValues['20401'] ?? 0);
                        $sheet->setCellValue("BS{$row}", $rowValues['20402'] ?? 0);
                        $sheet->setCellValue("BT{$row}", $rowValues['20403'] ?? 0);
                        $sheet->setCellValue("BU{$row}", $rowValues['20404'] ?? 0);
                        $sheet->setCellValue("BV{$row}", $rowValues['20405'] ?? 0);

                        $sheet->setCellValue("BW{$row}", "=SUM(BX{$row}:CB{$row})");
                        $sheet->setCellValue("BX{$row}", $rowValues['20501'] ?? 0);
                        $sheet->setCellValue("BY{$row}", $rowValues['20502'] ?? 0);
                        $sheet->setCellValue("BZ{$row}", $rowValues['20503'] ?? 0);
                        $sheet->setCellValue("CA{$row}", $rowValues['20504'] ?? 0);
                        $sheet->setCellValue("CB{$row}", $rowValues['20505'] ?? 0);

                        // Program ID 3
                        $sheet->setCellValue("CC{$row}", "=SUM(CD{$row}, CJ{$row}, CQ{$row}, CW{$row}, DC{$row})");
                        $sheet->setCellValue("CD{$row}", "=SUM(CE{$row}:CI{$row})");
                        $sheet->setCellValue("CE{$row}", $rowValues['30101'] ?? 0);
                        $sheet->setCellValue("CF{$row}", $rowValues['30102'] ?? 0);
                        $sheet->setCellValue("CG{$row}", $rowValues['30103'] ?? 0);
                        $sheet->setCellValue("CH{$row}", $rowValues['30104'] ?? 0);
                        $sheet->setCellValue("CI{$row}", $rowValues['30105'] ?? 0);

                        $sheet->setCellValue("CJ{$row}", "=SUM(CK{$row}:CP{$row})");
                        $sheet->setCellValue("CK{$row}", $rowValues['30201'] ?? 0);
                        $sheet->setCellValue("CL{$row}", $rowValues['30202'] ?? 0);
                        $sheet->setCellValue("CM{$row}", $rowValues['30203'] ?? 0);
                        $sheet->setCellValue("CN{$row}", $rowValues['30204'] ?? 0);
                        $sheet->setCellValue("CO{$row}", $rowValues['30205'] ?? 0);
                        $sheet->setCellValue("CP{$row}", $rowValues['30206'] ?? 0);

                        $sheet->setCellValue("CQ{$row}", "=SUM(CR{$row}:CV{$row})");
                        $sheet->setCellValue("CR{$row}", $rowValues['30301'] ?? 0);
                        $sheet->setCellValue("CS{$row}", $rowValues['30302'] ?? 0);
                        $sheet->setCellValue("CT{$row}", $rowValues['30303'] ?? 0);
                        $sheet->setCellValue("CQ{$row}", $rowValues['30304'] ?? 0);
                        $sheet->setCellValue("CV{$row}", $rowValues['30305'] ?? 0);

                        $sheet->setCellValue("CW{$row}", "=SUM(CX{$row}:DB{$row})");
                        $sheet->setCellValue("CX{$row}", $rowValues['30401'] ?? 0);
                        $sheet->setCellValue("CY{$row}", $rowValues['30402'] ?? 0);
                        $sheet->setCellValue("CZ{$row}", $rowValues['30403'] ?? 0);
                        $sheet->setCellValue("DA{$row}", $rowValues['30404'] ?? 0);
                        $sheet->setCellValue("DB{$row}", $rowValues['30405'] ?? 0);

                        $sheet->setCellValue("DC{$row}", "=SUM(DD{$row}:DJ{$row})");
                        $sheet->setCellValue("DD{$row}", $rowValues['30501'] ?? 0);
                        $sheet->setCellValue("DE{$row}", $rowValues['30502'] ?? 0);
                        $sheet->setCellValue("DF{$row}", $rowValues['30503'] ?? 0);
                        $sheet->setCellValue("DG{$row}", $rowValues['30504'] ?? 0);
                        $sheet->setCellValue("DH{$row}", $rowValues['30505'] ?? 0);
                        $sheet->setCellValue("DI{$row}", $rowValues['30506'] ?? 0);
                        $sheet->setCellValue("DJ{$row}", $rowValues['30507'] ?? 0);

                        // Program ID 5
                        $sheet->setCellValue("DK{$row}", "=SUM(DL{$row}, DT{$row}, EA{$row}, EG{$row}, EM{$row})");
                        $sheet->setCellValue("DL{$row}", "=SUM(DM{$row}:DS{$row})");
                        $sheet->setCellValue("DM{$row}", $rowValues['50101'] ?? 0);
                        $sheet->setCellValue("DN{$row}", $rowValues['50102'] ?? 0);
                        $sheet->setCellValue("DO{$row}", $rowValues['50103'] ?? 0);
                        $sheet->setCellValue("DP{$row}", $rowValues['50104'] ?? 0);
                        $sheet->setCellValue("DQ{$row}", $rowValues['50105'] ?? 0);
                        $sheet->setCellValue("DR{$row}", $rowValues['50106'] ?? 0);
                        $sheet->setCellValue("DS{$row}", $rowValues['50107'] ?? 0);

                        $sheet->setCellValue("DT{$row}", "=SUM(DU{$row}:DZ{$row})");
                        $sheet->setCellValue("DU{$row}", $rowValues['50201'] ?? 0);
                        $sheet->setCellValue("DV{$row}", $rowValues['50202'] ?? 0);
                        $sheet->setCellValue("DW{$row}", $rowValues['50203'] ?? 0);
                        $sheet->setCellValue("DX{$row}", $rowValues['50204'] ?? 0);
                        $sheet->setCellValue("DY{$row}", $rowValues['50205'] ?? 0);
                        $sheet->setCellValue("DZ{$row}", $rowValues['50206'] ?? 0);

                        $sheet->setCellValue("EA{$row}", "=SUM(EB{$row}:EF{$row})");
                        $sheet->setCellValue("EB{$row}", $rowValues['50301'] ?? 0);
                        $sheet->setCellValue("EC{$row}", $rowValues['50302'] ?? 0);
                        $sheet->setCellValue("ED{$row}", $rowValues['50303'] ?? 0);
                        $sheet->setCellValue("EE{$row}", $rowValues['50304'] ?? 0);
                        $sheet->setCellValue("EF{$row}", $rowValues['50305'] ?? 0);

                        $sheet->setCellValue("EG{$row}", "=SUM(EH{$row}:EL{$row})");
                        $sheet->setCellValue("EH{$row}", $rowValues['50401'] ?? 0);
                        $sheet->setCellValue("EI{$row}", $rowValues['50402'] ?? 0);
                        $sheet->setCellValue("EJ{$row}", $rowValues['50403'] ?? 0);
                        $sheet->setCellValue("EK{$row}", $rowValues['50404'] ?? 0);
                        $sheet->setCellValue("EL{$row}", $rowValues['50405'] ?? 0);

                        $sheet->setCellValue("EM{$row}", "=SUM(EN{$row}:ER{$row})");
                        $sheet->setCellValue("EN{$row}", $rowValues['50501'] ?? 0);
                        $sheet->setCellValue("EO{$row}", $rowValues['50502'] ?? 0);
                        $sheet->setCellValue("EP{$row}", $rowValues['50503'] ?? 0);
                        $sheet->setCellValue("EQ{$row}", $rowValues['50504'] ?? 0);
                        $sheet->setCellValue("ER{$row}", $rowValues['50505'] ?? 0);

                        // Populate remaining columns if dynamic mapping is used
                        foreach ($excelColumnMapping as $colLetter => $dbKey) {
                            $sheet->setCellValue("{$colLetter}{$row}", $rowValues[$dbKey] ?? 0);
                        }
                        $row++;
                    }

                    // ==========================================
                    // 4. WRITE TOTALS ON THE ACCOUNT HEADER ROW
                    // ==========================================
                    if (!empty($accountRows)) {
                        $startRange = min($accountRows);
                        $endRange = max($accountRows);
                        foreach ($allActiveColumns as $col) {
                            $sheet->setCellValue("{$col}{$accountRowHeader}", "=SUM({$col}{$startRange}:{$col}{$endRange})");
                        }
                    }
                    $chapterRows[] = $accountRowHeader;
                }
                /**
                 * 5. WRITE TOTALS ON THE CHAPTER HEADER ROW
                 */
                if (!empty($chapterRows)) {
                    foreach ($allActiveColumns as $col) {
                        $formulaParts = [];
                        foreach ($chapterRows as $targetAccRow) {
                            $formulaParts[] = "{$col}{$targetAccRow}";
                        }
                        $sheet->setCellValue("{$col}{$chapterRowHeader}", "=SUM(" . implode(',', $formulaParts) . ")");
                    }
                }
                $typeRows[] = $chapterRowHeader;
            }
            /**
             * 6. WRITE TOTALS ON THE TYPE HEADER ROW
             */
            if (!empty($typeRows)) {
                foreach ($allActiveColumns as $col) {
                    $formulaParts = [];
                    foreach ($typeRows as $targetTypeRow) {
                        $formulaParts[] = "{$col}{$targetTypeRow}";
                    }
                    $sheet->setCellValue("{$col}{$typeRowHeader}", "=SUM(" . implode(',', $formulaParts) . ")");
                }
            }
        }


        $fileName = 'template_annual_report.xlsx';

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
