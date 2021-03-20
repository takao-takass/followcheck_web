<?php
namespace App\Http\Controllers;

use App\DataModels\Code;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {

        $codes = Code::all();

        foreach ($codes as $code) {
            echo $code;
        }

        return response()
        ->view('test', $codes);
    }
}
