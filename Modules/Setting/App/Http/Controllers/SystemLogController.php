<?php

namespace Modules\Setting\App\Http\Controllers;

use App\DataTables\SystemLogDataTable;
use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
    public function index(SystemLogDataTable $dataTable)
    {
        return $dataTable->render('setting::system.index');
    }

    public function detail(Request $request)
    {
        $logs = SystemLog::where('id', $request->log_id)->select('properties', 'event', 'description')->first();
        $properties = json_decode($logs->properties, true);
        $trs = '';
        $text = '';
        if ($logs->description == 'updated') {

            $trs .= '<tr class="tr_bg">
                <td colspan="2">CURRENT</td>
                <td colspan="2">OLD</td>
            </tr>';
        } else {
            $trs .= '<tr  class="tr_bg">
            <td colspan="2">CURRENT</td>
            <td colspan="2"></td>
            </tr>';
        }
        if (! empty($properties['attributes'])) {
            foreach ($properties['attributes'] as $title => $value) {
                if ($title != 'permissions') {
                    $old = '';
                    if (! empty($properties['old'])) {
                        $old = $properties['old'][$title];
                    }
                    $text = $title;
                    $tesxt = 'Short_name';
                    $pos = strpos($tesxt, '_');
                    $titleArr = [];
                    if ($pos) {
                        $titleArr = explode('_', $title);
                        $text = '';
                        foreach ($titleArr as $a) {
                            $text .= ''.$a.' ';
                        }
                    }
                    $trs .= '<tr>
                            <td class="">'.ucfirst($text).' </td>
                            <td class="">: '.$value.'</td>';
                    if ($logs->description == 'updated') {
                        $trs .= '<td class="text-warning">   '.ucfirst($text).' </td>
                                        <td class="text-warning">: '.$old.'</td>';
                    }
                    $trs .= '</tr>';
                }
            }
        }
        if (! empty($properties['old'])) {
            foreach ($properties['old'] as $title => $value) {
                if ($title != 'permissions') {
                    $text = $title;
                    $tesxt = 'Short_name';
                    $pos = strpos($tesxt, '_');
                    $titleArr = [];
                    if ($pos) {
                        $titleArr = explode('_', $title);
                        $text = '';
                        foreach ($titleArr as $a) {
                            $text .= ''.$a.' ';
                        }
                    }
                    $trs .= '<tr>
                        <td class="">'.ucfirst($text).' </td>
                        <td class="">: '.$value.'</td>';
                    $trs .= '</tr>';
                }

            }
        }
        $data['trs'] = $trs;
        $data['event'] = ucfirst($logs->description);

        return response()->json($data);
    }
}
