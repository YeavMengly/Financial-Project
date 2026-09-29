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

        $templatePath = storage_path('excel/template/template_annual_report.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle("A1:EX4")->applyFromArray([
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
            '10207' => 'T',
            '103' => 'U',
            '10301' => 'V',
            '10302' => 'W',
            '10303' => 'X',
            '10304' => 'Y',
            '10305' => 'Z',
            '104' => 'AA',
            '10401' => 'AB',
            '10402' => 'AC',
            '10403' => 'AD',
            '10404' => 'AE',
            '10405' => 'AF',
            '105' => 'AG',
            '10501' => 'AH',
            '10502' => 'AI',
            '10503' => 'AJ',
            '10504' => 'AK',
            '10505' => 'AL',
            '10506' => 'AM',
            '106' => 'AN',
            '10601' => 'AO',
            '10602' => 'AP',
            '10603' => 'AQ',
            '10604' => 'AR',
            '10605' => 'AS',

            '201' => 'AT',
            '20101' => 'AU',
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
            //  F    1
            //  G    101
            'H' => '10101',
            'I' => '10102',
            'J' => '10103',
            'K' => '10104',
            'L' => '10105',
            //  M    102
            'N' => '10201',
            'O' => '10202',
            'P' => '10203',
            'Q' => '10204',
            'R' => '10205',
            'S' => '10206',
            'T' => '10207',
            //  U   103
            'V' => '10301',
            'W' => '10302',
            'X' => '10303',
            'Y' => '10304',
            'Z' => '10305',
            //  AA  104
            'AB' => '10401',
            'AC' => '10402',
            'AD' => '10403',
            'AE' => '10404',
            'AF' => '10405',
            //  AG  105
            'AH' => '10501',
            'AI' => '10502',
            'AJ' => '10503',
            'AK' => '10504',
            'AL' => '10505',
            'AM' => '10506',
            //  AN  106
            'AO' => '10601',
            'AP' => '10602',
            'AQ' => '10603',
            'AR' => '10604',
            'AS' => '10605',

            // PROGRAM 2
            //  AT  2
            //  AU  201
            'AV' => '20101',
            'AW' => '20102',
            'AX' => '20103',
            'AY' => '20104',
            'AZ' => '20105',
            'BA' => '20106',
            'BB' => '20107',
            //  BC  202
            'BD' => '20201',
            'BE' => '20202',
            'BF' => '20203',
            'BG' => '20204',
            'BH' => '20205',
            'BI' => '20206',
            'BJ' => '20207',
            //  BK  203
            'BL' => '20301',
            'BM' => '20302',
            'BN' => '20303',
            'BO' => '20304',
            'BP' => '20305',
            'BQ' => '20306',
            'BR' => '20307',
            //  BS  204
            'BT' => '20401',
            'BU' => '20402',
            'BV' => '20403',
            'BW' => '20404',
            'BX' => '20405',
            //  BY  205
            'BZ' => '20501',
            'CA' => '20502',
            'CB' => '20503',
            'CC' => '20504',
            'CD' => '20505',

            // PROGRAM 3
            //  CE  3
            //  CF  301
            'CG' => '30101',
            'CH' => '30102',
            'CI' => '30103',
            'CJ' => '30104',
            'CK' => '30105',
            //  CL  302
            'CM' => '30201',
            'CN' => '30202',
            'CO' => '30203',
            'CP' => '30204',
            'CQ' => '30205',
            'CR' => '30206',
            //  CS  303
            'CT' => '30301',
            'CU' => '30302',
            'CV' => '30303',
            'CW' => '30304',
            'CX' => '30305',
            //  CY  304
            'CZ' => '30401',
            'DA' => '30402',
            'DB' => '30403',
            'DC' => '30404',
            'DD' => '30405',
            //  DE  305
            'DF' => '30501',
            'DG' => '30502',
            'DH' => '30503',
            'DI' => '30504',
            'DJ' => '30505',
            'DK' => '30506',
            'DL' => '30507',
            'DM' => '30508',

            //  Program 4
            //  DN  4
            //  DO  405
            'DP' => '40506',

            // PROGRAM 5
            //  DQ  5
            //  DR  501
            'DS' => '50101',
            'DT' => '50102',
            'DU' => '50103',
            'DV' => '50104',
            'DW' => '50105',
            'DX' => '50106',
            'DY' => '50107',
            //  DZ  502
            'EA' => '50201',
            'EB' => '50202',
            'EC' => '50203',
            'ED' => '50204',
            'EE' => '50205',
            'EF' => '50206',
            //  EG  503
            'EH' => '50301',
            'EI' => '50302',
            'EJ' => '50303',
            'EK' => '50304',
            'EL' => '50305',
            //  EM  504
            'EN' => '50401',
            'EO' => '50402',
            'EP' => '50403',
            'EQ' => '50404',
            'ER' => '50405',
            //  ES  505
            'ET' => '50501',
            'EU' => '50502',
            'EV' => '50503',
            'EW' => '50504',
            'EX' => '50505'
        ];

        $summaryTotalColumns = [
            // Totals
            'E',
            // Program1
            'F',
            'G',
            'M',
            'U',
            'AA',
            'AG',
            'AN',
            // Program2
            'AT',
            'AU',
            'BC',
            'BK',
            'BS',
            'BY',
            // Program3
            'CE',
            'CF',
            'CL',
            'CS',
            'CY',
            'DE',
            // Program4
            'DN',
            'DO',
            // Program5
            'DQ',
            'DR',
            'DZ',
            'EG',
            'EM',
            'ES'
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
            $sheet->getStyle("A{$row}:EX{$row}")->applyFromArray([
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
                $sheet->getStyle("A{$row}:EX{$row}")->applyFromArray([
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
                    $sheet->getStyle("A{$row}:EX{$row}")->applyFromArray([
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
                        $sheet->getStyle("A{$row}:EX{$row}")->applyFromArray([
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
                                        // $rowValues[$key] += (float) $item->fin_law;
                                        $rowValues[$key] += ( $item->fin_law) / 1000000;
                                    }
                                }
                            }
                        }
                        // Program TOTAL
                        $sheet->setCellValue("E{$row}", "=SUM(F{$row}, AT{$row}, CE{$row}, DN{$row}, DQ{$row})");

                        /**
                         * WRITE CURRENT ROW (Account Sub Data)
                         */
                        // Program ID 1
                        $sheet->setCellValue("F{$row}", "=SUM(G{$row}, M{$row}, U{$row}, AA{$row}, AG{$row}, AN{$row})");
                        $sheet->setCellValue("G{$row}", "=SUM(H{$row}:L{$row})");
                        $sheet->setCellValue("H{$row}", $rowValues['10101'] ?? 0);
                        $sheet->setCellValue("I{$row}", $rowValues['10102'] ?? 0);
                        $sheet->setCellValue("J{$row}", $rowValues['10103'] ?? 0);
                        $sheet->setCellValue("K{$row}", $rowValues['10104'] ?? 0);
                        $sheet->setCellValue("L{$row}", $rowValues['10105'] ?? 0);

                        $sheet->setCellValue("M{$row}", "=SUM(N{$row}:T{$row})");
                        $sheet->setCellValue("N{$row}", $rowValues['10201'] ?? 0);
                        $sheet->setCellValue("O{$row}", $rowValues['10202'] ?? 0);
                        $sheet->setCellValue("P{$row}", $rowValues['10203'] ?? 0);
                        $sheet->setCellValue("Q{$row}", $rowValues['10204'] ?? 0);
                        $sheet->setCellValue("R{$row}", $rowValues['10205'] ?? 0);
                        $sheet->setCellValue("S{$row}", $rowValues['10206'] ?? 0);
                        $sheet->setCellValue("T{$row}", $rowValues['10207'] ?? 0);

                        $sheet->setCellValue("U{$row}", "=SUM(V{$row}:Z{$row})");
                        $sheet->setCellValue("V{$row}", $rowValues['10301'] ?? 0);
                        $sheet->setCellValue("W{$row}", $rowValues['10302'] ?? 0);
                        $sheet->setCellValue("X{$row}", $rowValues['10303'] ?? 0);
                        $sheet->setCellValue("Y{$row}", $rowValues['10304'] ?? 0);
                        $sheet->setCellValue("Z{$row}", $rowValues['10305'] ?? 0);

                        $sheet->setCellValue("AA{$row}", "=SUM(AB{$row}:AF{$row})");
                        $sheet->setCellValue("AB{$row}", $rowValues['10401'] ?? 0);
                        $sheet->setCellValue("AC{$row}", $rowValues['10402'] ?? 0);
                        $sheet->setCellValue("AD{$row}", $rowValues['10403'] ?? 0);
                        $sheet->setCellValue("AE{$row}", $rowValues['10404'] ?? 0);
                        $sheet->setCellValue("AF{$row}", $rowValues['10405'] ?? 0);

                        $sheet->setCellValue("AG{$row}", "=SUM(AH{$row}:AM{$row})");
                        $sheet->setCellValue("AH{$row}", $rowValues['10501'] ?? 0);
                        $sheet->setCellValue("AI{$row}", $rowValues['10502'] ?? 0);
                        $sheet->setCellValue("AJ{$row}", $rowValues['10503'] ?? 0);
                        $sheet->setCellValue("AK{$row}", $rowValues['10504'] ?? 0);
                        $sheet->setCellValue("AL{$row}", $rowValues['10505'] ?? 0);
                        $sheet->setCellValue("AM{$row}", $rowValues['10506'] ?? 0);

                        $sheet->setCellValue("AN{$row}", "=SUM(AO{$row}:AS{$row})");
                        $sheet->setCellValue("AO{$row}", $rowValues['10601'] ?? 0);
                        $sheet->setCellValue("AP{$row}", $rowValues['10602'] ?? 0);
                        $sheet->setCellValue("AQ{$row}", $rowValues['10603'] ?? 0);
                        $sheet->setCellValue("AR{$row}", $rowValues['10604'] ?? 0);
                        $sheet->setCellValue("AS{$row}", $rowValues['10605'] ?? 0);

                        // Program ID 2
                        $sheet->setCellValue("AT{$row}", "=SUM(AU{$row}, BC{$row}, BK{$row}, BS{$row}, BY{$row})");
                        $sheet->setCellValue("AU{$row}", "=SUM(AV{$row}:BB{$row})");
                        $sheet->setCellValue("AV{$row}", $rowValues['20101'] ?? 0);
                        $sheet->setCellValue("AW{$row}", $rowValues['20102'] ?? 0);
                        $sheet->setCellValue("AX{$row}", $rowValues['20103'] ?? 0);
                        $sheet->setCellValue("AY{$row}", $rowValues['20104'] ?? 0);
                        $sheet->setCellValue("AZ{$row}", $rowValues['20105'] ?? 0);
                        $sheet->setCellValue("BA{$row}", $rowValues['20106'] ?? 0);
                        $sheet->setCellValue("BB{$row}", $rowValues['20107'] ?? 0);

                        $sheet->setCellValue("BC{$row}", "=SUM(BD{$row}:BJ{$row})");
                        $sheet->setCellValue("BD{$row}", $rowValues['20201'] ?? 0);
                        $sheet->setCellValue("BE{$row}", $rowValues['20202'] ?? 0);
                        $sheet->setCellValue("BF{$row}", $rowValues['20203'] ?? 0);
                        $sheet->setCellValue("BG{$row}", $rowValues['20204'] ?? 0);
                        $sheet->setCellValue("BH{$row}", $rowValues['20205'] ?? 0);
                        $sheet->setCellValue("BI{$row}", $rowValues['20206'] ?? 0);
                        $sheet->setCellValue("BJ{$row}", $rowValues['20207'] ?? 0);

                        $sheet->setCellValue("BK{$row}", "=SUM(BL{$row}:BR{$row})");
                        $sheet->setCellValue("BL{$row}", $rowValues['20301'] ?? 0);
                        $sheet->setCellValue("BM{$row}", $rowValues['20302'] ?? 0);
                        $sheet->setCellValue("BN{$row}", $rowValues['20303'] ?? 0);
                        $sheet->setCellValue("BO{$row}", $rowValues['20304'] ?? 0);
                        $sheet->setCellValue("BP{$row}", $rowValues['20305'] ?? 0);
                        $sheet->setCellValue("BQ{$row}", $rowValues['20306'] ?? 0);
                        $sheet->setCellValue("BR{$row}", $rowValues['20307'] ?? 0);

                        $sheet->setCellValue("BS{$row}", "=SUM(BT{$row}:BX{$row})");
                        $sheet->setCellValue("BT{$row}", $rowValues['20401'] ?? 0);
                        $sheet->setCellValue("BU{$row}", $rowValues['20402'] ?? 0);
                        $sheet->setCellValue("BV{$row}", $rowValues['20403'] ?? 0);
                        $sheet->setCellValue("BW{$row}", $rowValues['20404'] ?? 0);
                        $sheet->setCellValue("BX{$row}", $rowValues['20405'] ?? 0);

                        $sheet->setCellValue("BY{$row}", "=SUM(BZ{$row}:CD{$row})");
                        $sheet->setCellValue("BZ{$row}", $rowValues['20501'] ?? 0);
                        $sheet->setCellValue("CA{$row}", $rowValues['20502'] ?? 0);
                        $sheet->setCellValue("CB{$row}", $rowValues['20503'] ?? 0);
                        $sheet->setCellValue("CC{$row}", $rowValues['20504'] ?? 0);
                        $sheet->setCellValue("CD{$row}", $rowValues['20505'] ?? 0);

                        // Program ID 3
                        $sheet->setCellValue("CE{$row}", "=SUM(CF{$row}, CL{$row}, CS{$row}, CY{$row}, DE{$row})");
                        $sheet->setCellValue("CF{$row}", "=SUM(CG{$row}:CK{$row})");
                        $sheet->setCellValue("CG{$row}", $rowValues['30101'] ?? 0);
                        $sheet->setCellValue("CH{$row}", $rowValues['30102'] ?? 0);
                        $sheet->setCellValue("CI{$row}", $rowValues['30103'] ?? 0);
                        $sheet->setCellValue("CJ{$row}", $rowValues['30104'] ?? 0);
                        $sheet->setCellValue("CK{$row}", $rowValues['30105'] ?? 0);

                        $sheet->setCellValue("CL{$row}", "=SUM(CM{$row}:CR{$row})");
                        $sheet->setCellValue("CM{$row}", $rowValues['30201'] ?? 0);
                        $sheet->setCellValue("CN{$row}", $rowValues['30202'] ?? 0);
                        $sheet->setCellValue("CO{$row}", $rowValues['30203'] ?? 0);
                        $sheet->setCellValue("CP{$row}", $rowValues['30204'] ?? 0);
                        $sheet->setCellValue("CQ{$row}", $rowValues['30205'] ?? 0);
                        $sheet->setCellValue("CR{$row}", $rowValues['30206'] ?? 0);

                        $sheet->setCellValue("CS{$row}", "=SUM(CT{$row}:CX{$row})");
                        $sheet->setCellValue("CT{$row}", $rowValues['30301'] ?? 0);
                        $sheet->setCellValue("CU{$row}", $rowValues['30302'] ?? 0);
                        $sheet->setCellValue("CV{$row}", $rowValues['30303'] ?? 0);
                        $sheet->setCellValue("CW{$row}", $rowValues['30304'] ?? 0);
                        $sheet->setCellValue("CX{$row}", $rowValues['30305'] ?? 0);

                        $sheet->setCellValue("CY{$row}", "=SUM(CZ{$row}:DD{$row})");
                        $sheet->setCellValue("CZ{$row}", $rowValues['30401'] ?? 0);
                        $sheet->setCellValue("DA{$row}", $rowValues['30402'] ?? 0);
                        $sheet->setCellValue("DB{$row}", $rowValues['30403'] ?? 0);
                        $sheet->setCellValue("DC{$row}", $rowValues['30404'] ?? 0);
                        $sheet->setCellValue("DD{$row}", $rowValues['30405'] ?? 0);

                        $sheet->setCellValue("DE{$row}", "=SUM(DF{$row}:DM{$row})");
                        $sheet->setCellValue("DF{$row}", $rowValues['30501'] ?? 0);
                        $sheet->setCellValue("DG{$row}", $rowValues['30502'] ?? 0);
                        $sheet->setCellValue("DH{$row}", $rowValues['30503'] ?? 0);
                        $sheet->setCellValue("DI{$row}", $rowValues['30504'] ?? 0);
                        $sheet->setCellValue("DJ{$row}", $rowValues['30505'] ?? 0);
                        $sheet->setCellValue("DK{$row}", $rowValues['30506'] ?? 0);
                        $sheet->setCellValue("DL{$row}", $rowValues['30507'] ?? 0);
                        $sheet->setCellValue("DM{$row}", $rowValues['30508'] ?? 0);

                        // Program ID 4
                        $sheet->setCellValue("DN{$row}", "=SUM(DO{$row})");
                        $sheet->setCellValue("DO{$row}", "=SUM(DP{$row})");
                        $sheet->setCellValue("DP{$row}", $rowValues['40506'] ?? 0);

                        // Program ID 5
                        $sheet->setCellValue("DQ{$row}", "=SUM(DR{$row}, DZ{$row}, EG{$row}, EM{$row}, ES{$row})");
                        $sheet->setCellValue("DR{$row}", "=SUM(DS{$row}:DY{$row})");
                        $sheet->setCellValue("DS{$row}", $rowValues['50101'] ?? 0);
                        $sheet->setCellValue("DT{$row}", $rowValues['50102'] ?? 0);
                        $sheet->setCellValue("DU{$row}", $rowValues['50103'] ?? 0);
                        $sheet->setCellValue("DV{$row}", $rowValues['50104'] ?? 0);
                        $sheet->setCellValue("DW{$row}", $rowValues['50105'] ?? 0);
                        $sheet->setCellValue("DX{$row}", $rowValues['50106'] ?? 0);
                        $sheet->setCellValue("DY{$row}", $rowValues['50107'] ?? 0);

                        $sheet->setCellValue("DZ{$row}", "=SUM(EA{$row}:EF{$row})");
                        $sheet->setCellValue("EA{$row}", $rowValues['50201'] ?? 0);
                        $sheet->setCellValue("EB{$row}", $rowValues['50202'] ?? 0);
                        $sheet->setCellValue("EC{$row}", $rowValues['50203'] ?? 0);
                        $sheet->setCellValue("ED{$row}", $rowValues['50204'] ?? 0);
                        $sheet->setCellValue("EE{$row}", $rowValues['50205'] ?? 0);
                        $sheet->setCellValue("EF{$row}", $rowValues['50206'] ?? 0);

                        $sheet->setCellValue("EG{$row}", "=SUM(EH{$row}:EL{$row})");
                        $sheet->setCellValue("EH{$row}", $rowValues['50301'] ?? 0);
                        $sheet->setCellValue("EI{$row}", $rowValues['50302'] ?? 0);
                        $sheet->setCellValue("EJ{$row}", $rowValues['50303'] ?? 0);
                        $sheet->setCellValue("EK{$row}", $rowValues['50304'] ?? 0);
                        $sheet->setCellValue("EL{$row}", $rowValues['50305'] ?? 0);

                        $sheet->setCellValue("EM{$row}", "=SUM(EN{$row}:ER{$row})");
                        $sheet->setCellValue("EN{$row}", $rowValues['50401'] ?? 0);
                        $sheet->setCellValue("EO{$row}", $rowValues['50402'] ?? 0);
                        $sheet->setCellValue("EP{$row}", $rowValues['50403'] ?? 0);
                        $sheet->setCellValue("EQ{$row}", $rowValues['50404'] ?? 0);
                        $sheet->setCellValue("ER{$row}", $rowValues['50405'] ?? 0);

                        $sheet->setCellValue("ES{$row}", "=SUM(ET{$row}:EX{$row})");
                        $sheet->setCellValue("ET{$row}", $rowValues['50501'] ?? 0);
                        $sheet->setCellValue("EU{$row}", $rowValues['50502'] ?? 0);
                        $sheet->setCellValue("EV{$row}", $rowValues['50503'] ?? 0);
                        $sheet->setCellValue("EW{$row}", $rowValues['50504'] ?? 0);
                        $sheet->setCellValue("EX{$row}", $rowValues['50505'] ?? 0);

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
