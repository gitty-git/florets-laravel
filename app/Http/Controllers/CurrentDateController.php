<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class CurrentDateController extends Controller
{
    public function index()
    {
        // return Carbon::now();
        date_default_timezone_set('Asia/Yekaterinburg');
        $date = date('m/d/Y h:i:s a', time());
        return $date;
    }
}
