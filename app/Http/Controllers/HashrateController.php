<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HashrateController extends Controller
{
    public function index()
    {
        return view('layouts.pages.hashrate');
    }

    public function data()
    {
        $hashrate = DB::table('blocks')->select([
            DB::raw("date_trunc('hour', created_at) AS date"),
            DB::raw("avg(hashrate) as hashrate"),
        ])->groupBy('date')
            ->orderBy('date')
            ->whereBetween('created_at', [now()->subDays(100), now()])
            ->get()
            ->map(function ($diff) {
                return [
                    'x' => $diff->date,
                    'y' => $diff->hashrate,
                ];
            });

        return response()->json([
            'datasets' => [
                [
                    'label' => 'Hashrate',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'data' => $hashrate,
                ],
            ]
        ]);
    }
}
