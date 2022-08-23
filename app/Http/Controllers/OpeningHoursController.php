<?php

namespace App\Http\Controllers;

use App\Models\OpeningHours;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OpeningHoursController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $times = OpeningHours::latest()->first(['opens_at', 'closes_at']);
        $arr = [
            'opens_at' => date('Y-m-d' . " " . $times->opens_at),
            'closes_at' => date('Y-m-d' . " " . $times->closes_at)
        ];
        return $arr;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OpeningHours  $openingHours
     * @return \Illuminate\Http\Response
     */
    public function show(OpeningHours $openingHours)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OpeningHours  $openingHours
     * @return \Illuminate\Http\Response
     */
    public function edit(OpeningHours $openingHours)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OpeningHours  $openingHours
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OpeningHours $openingHours)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OpeningHours  $openingHours
     * @return \Illuminate\Http\Response
     */
    public function destroy(OpeningHours $openingHours)
    {
        //
    }
}
