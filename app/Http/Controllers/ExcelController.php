<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Excel;

class ExcelController extends Controller
{
    public function import()
    {
        $filePath = 'storage/exports/'.iconv('UTF-8', 'UTF-8', '学生成绩').'.xls';
        Excel::load($filePath, function($reader) {
            $data = $reader->all();
            dd($data);
        });
    }
}
