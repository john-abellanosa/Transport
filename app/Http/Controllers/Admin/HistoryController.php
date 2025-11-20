<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\History;  

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month'); // optional filter
        $year  = $request->input('year');

        $query = History::query();

        if ($month) {
            // whereMonth expects numeric month '01'..'12' or integer 1..12
            $query->whereMonth('schedule', ltrim($month, '0'));
        }
        if ($year) {
            $query->whereYear('schedule', $year);
        }

        $histories = $query->orderBy('schedule', 'desc')->get();

        // pass the variable to the view
        return view('admin.pages.history', compact('histories'));
    }
}
