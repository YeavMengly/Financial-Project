<?php

namespace Modules\Content\App\Http\Controllers;

use App\DataTables\Content\EmployeeDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\Employee;
use App\Models\Content\Positions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(EmployeeDataTable $dataTable)
    {
        return $dataTable->render('content::content.employees.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $position  = Positions::all();
        return view('content::content.employees.create')
            ->with('position', $position);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'id_number' => [
                    'required',

                ],
                'account_number' => [
                    'required',

                ],
                'name_kh' => [
                    'required',

                ],
                'name_latin' => [
                    'required',
                ],

                'cboPosition' => [
                    'required',
                ],

            ],
            [
                'account_number' => [
                    'required',

                ],
                'name_kh' => [
                    'required',

                ],
                'name_latin' => [
                    'required',
                ],
            ]
            // 'status' => ['nullable', 'boolean'], // ✅ ADD
        );

        DB::beginTransaction();
        try {

            Employee::firstOrCreate([
                'id_number' => $request->id_number,
                'account_number' => $request->account_number,
                'name_kh' => $request->name_kh,
                'name_latin' => $request->name_latin,
                'position_id' => $request->cboPosition,
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return $request->submit == 'save'
                ? redirect()->route('employees.index',)
                : redirect()->route('employees.index',);
        }
        // catch (\Illuminate\Database\QueryException $e) {
        //     DB::rollBack();

        //     // If unique index blocked a duplicate, you can show friendly message
        //     flash()->translate('en')->option('timeout', 2000)
        //         ->error('This record already exists.', 'Duplicate')->flash();

        //     return redirect()->route('employees.index',);
        // } 
        catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()->translate('en')->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')->flash();

            return redirect()->route('employees.index',);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('content::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params)
    {
        $decoded = decode_params($params);

        $id = is_array($decoded) ? $decoded[0] : $decoded;

        $position = Positions::all();
        $module = Employee::findOrFail($id);

        return view('content::content.employees.edit', [
            'position' => $position,
            'module' => $module,
            'params' => $params,
        ]);
    }

    public function update(Request $request, $params): RedirectResponse
    {
        $request->validate([
            'id_number' => ['required'],
            'account_number' => ['required'],
            'name_kh' => ['required'],
            'name_latin' => ['required'],
            // 'status' => ['nullable', 'boolean'], // ✅ ADD
        ]);

        DB::beginTransaction();

        try {
            $decoded = decode_params($params);

            $id = is_array($decoded) ? $decoded[0] : $decoded;

            $employee = Employee::findOrFail($id);

            $employee->update([
                'id_number' => $request->id_number,
                'account_number' => $request->account_number,
                'name_kh' => $request->name_kh,
                'name_latin' => $request->name_latin,
                // 'status' => $request->has('status') ? 1 : 0,
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('employees.index');
        } catch (Exception $e) {

            DB::rollBack();

            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('employees.index');
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params)
    {
        $id = decode_params($params);
        $employee = Employee::where('id', $id)->first();
        $employee->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('employees.index', $params);
    }

    public function restore($params)
    {
        $id = decode_params($params);
        Employee::withTrashed()->whereKey($id)->restore();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('employees.index');
    }
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv,txt,xlsm,xlsb',
                'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv,text/plain,application/csv,text/comma-separated-values,application/excel,application/vnd.msexcel,application/octet-stream',
                'max:20480', // Allow up to 20MB
            ],
        ]);

        set_time_limit(0);
        ini_set('memory_limit', '512M');

        DB::beginTransaction();

        try {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());

            $sheets = in_array($extension, ['xlsx', 'xlsm'])
                ? $this->parseXLSX($file->getRealPath())
                : [$this->parseCSV($file->getRealPath())];

            $keywords = [
                'id_number'      => ['អត្តលេខ', 'លេខសំគាល់', 'id', 'id_number', 'code'],
                'account_number' => ['លេខគណនី', 'គណនី', 'account', 'bank', 'acc_no'],
                'name_kh'        => ['ឈ្មោះ - ខ្មែរ', 'ឈ្មោះខ្មែរ', 'ខ្មែរ', 'khmer', 'name_kh'],
                'name_latin'     => ['ឈ្មោះ - ឡាតាំង', 'ឈ្មោះឡាតាំង', 'ឡាតាំង', 'បារាំង', 'latin', 'english', 'name_en'],
            ];

            $batchData = [];

            foreach ($sheets as $rows) {
                $headerRowIndex = null;
                $headerMap = [];

                // 1. Locate Header Row
                foreach ($rows as $rowIndex => $rowCells) {
                    if ($rowIndex > 50) {
                        break;
                    }

                    $matches = [];
                    foreach ($rowCells as $colKey => $cellText) {
                        $cleanHeader = mb_strtolower(trim($cellText));

                        foreach ($keywords as $field => $synonyms) {
                            if (isset($matches[$field])) {
                                continue;
                            }

                            foreach ($synonyms as $synonym) {
                                if (str_contains($cleanHeader, mb_strtolower($synonym))) {
                                    $matches[$field] = $colKey;
                                    break 2;
                                }
                            }
                        }
                    }

                    if (count($matches) >= 2) {
                        $headerRowIndex = $rowIndex;
                        $headerMap = $matches;
                        break;
                    }
                }

                if ($headerRowIndex === null) {
                    continue;
                }

                // 2. Extract and import every row (allows duplicates)
                foreach ($rows as $index => $row) {
                    if ($index <= $headerRowIndex) {
                        continue;
                    }

                    $idNumber      = trim($row[$headerMap['id_number'] ?? ''] ?? '');
                    $nameKh        = trim($row[$headerMap['name_kh'] ?? ''] ?? '');
                    $nameLatin     = trim($row[$headerMap['name_latin'] ?? ''] ?? '');
                    $accountNumber = trim($row[$headerMap['account_number'] ?? ''] ?? '');

                    // Skip only if name and account number are completely empty
                    if (empty($nameKh) && empty($accountNumber)) {
                        continue;
                    }

                    // Append sequentially so duplicate entries/names are retained
                    $batchData[] = [
                        'id_number'      => !empty($idNumber) ? $idNumber : null,
                        'account_number' => !empty($accountNumber) ? $accountNumber : null,
                        'name_kh'        => !empty($nameKh) ? $nameKh : null,
                        'name_latin'     => !empty($nameLatin) ? $nameLatin : null,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];

                    // Bulk insert in chunks of 1000 records
                    if (count($batchData) >= 1000) {
                        Employee::insert($batchData);
                        $batchData = [];
                    }
                }
            }

            // Insert remaining batch records
            if (!empty($batchData)) {
                Employee::insert($batchData);
            }

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('Data imported successfully!', 'Successful')
                ->flash();

            return redirect()->route('employees.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Import Error: ' . $e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('employees.index');
        }
    }

    /**
     * Parse all XML worksheets inside an .xlsx file using SimpleXML
     */
    private function parseXLSX(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new Exception('Unable to open XLSX file.');
        }

        // Extract shared string table
        $sharedStrings = [];
        if (($index = $zip->locateName('xl/sharedStrings.xml')) !== false) {
            $xml = simplexml_load_string($zip->getFromIndex($index));
            foreach ($xml->si as $val) {
                $sharedStrings[] = (string) ($val->t ?? $val->r->t ?? '');
            }
        }

        // Get all sheet XML filenames inside the archive
        $sheetFiles = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            if (preg_match('/^xl\/worksheets\/sheet\d+\.xml$/i', $filename)) {
                $sheetFiles[] = $filename;
            }
        }
        natsort($sheetFiles);

        $allSheets = [];

        // Parse each worksheet tab
        foreach ($sheetFiles as $sheetFile) {
            $sheetXml = simplexml_load_string($zip->getFromName($sheetFile));
            $sheetRows = [];

            foreach ($sheetXml->sheetData->row as $row) {
                $rowIndex = (int) $row['r'] - 1;
                $rowData = [];

                foreach ($row->c as $cell) {
                    $cellRef = (string) $cell['r'];
                    $columnLetter = preg_replace('/[0-9]/', '', $cellRef);

                    $value = (string) $cell->v;

                    // Handle inline strings or shared strings
                    if (isset($cell->is->t)) {
                        $value = (string) $cell->is->t;
                    } elseif (isset($cell['t']) && (string) $cell['t'] === 's') {
                        $value = $sharedStrings[(int) $value] ?? '';
                    }

                    $rowData[$columnLetter] = $value;
                }

                $sheetRows[$rowIndex] = $rowData;
            }

            $allSheets[] = $sheetRows;
        }

        $zip->close();

        return $allSheets;
    }

    /**
     * Native CSV Parser Fallback
     */
    private function parseCSV(string $filePath): array
    {
        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $row = [];
                foreach ($data as $colIndex => $value) {
                    // Convert numeric index to Excel column letter (0 => A, 1 => B, etc.)
                    $colLetter = chr(65 + $colIndex);
                    $row[$colLetter] = $value;
                }
                $rows[] = $row;
            }
            fclose($handle);
        }
        return $rows;
    }
}
