<?php

namespace App\Http\Controllers;

use App\Models\ProgramLearningOutcome;
use App\Models\LearningOutcome;
use App\Models\Course;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StandardsOutcomeMapController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
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
        $this->validate($request, [
            'map' => 'required',
            ]);

        $outcomeMap = $request->input('map');
        foreach ($outcomeMap as $cloId => $standardToScaleIds) {
            foreach (array_keys($standardToScaleIds) as $standardId) {
                DB::table('standards_outcome_maps')->updateOrInsert(
                    ['standard_id' => $standardId, 'l_outcome_id' => $cloId],
                    ['standard_scale_id' => $outcomeMap[$cloId][$standardId]]
                );
            }
        }

        return redirect()->back()->with('success', 'Your answers have been saved successfully.');
    }
}
