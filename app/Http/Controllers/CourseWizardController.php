<?php

namespace App\Http\Controllers;

use App\Http\Controllers\OptionalPriorities as ControllersOptionalPriorities;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\CourseUser;
use App\Models\User;
use App\Models\ProgramLearningOutcome;
use App\Models\Course;
use App\Models\LearningOutcome;
use App\Models\CourseOptionalPriorities;
use App\Models\OutcomeMap;
use App\Models\AssessmentMethod;
use App\Models\Custom_assessment_methods;
use App\Models\Custom_learning_activities;
use App\Models\OutcomeAssessment;
use App\Models\LearningActivity;
use App\Models\OptionalPriorities;
use App\Models\MappingScale;
use App\Models\OptionalPriorityCategories;
use App\Models\OptionalPrioritySubcategories;
use App\Models\PLOCategory;
use Illuminate\Support\Facades\Auth;
use App\Models\Standard;
use App\Models\StandardScale;
use App\Models\StandardsOutcomeMap;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseWizardController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('hasAccess');
    }

    public function step1($course_id, Request $request)
    {
        $isEditor = false;
        if ($request->isEditor) {
            $isEditor = true;
        }
        $isViewer = false;
        if ($request->isViewer) {
            return redirect()->route('courseWizard.step7', $course_id);
        }
        //for header
        $user = User::where('id',Auth::id())->first();
        // returns a collection of courses associated with users 
        $myCourses = $user->courses;
        $courseUsers = array();
        foreach ($myCourses as $course) {
            $coursesUsers = $course->users()->get();
            $courseUsers[$course->course_id] = $coursesUsers;
        }
        $oAct = LearningActivity::join('outcome_activities','learning_activities.l_activity_id','=','outcome_activities.l_activity_id')
                                ->join('learning_outcomes', 'outcome_activities.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('outcome_activities.l_activity_id','learning_activities.l_activity','outcome_activities.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('learning_activities.course_id','=',$course_id)->count();
        $oAss = AssessmentMethod::join('outcome_assessments','assessment_methods.a_method_id','=','outcome_assessments.a_method_id')
                                ->join('learning_outcomes', 'outcome_assessments.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('assessment_methods.a_method_id','assessment_methods.a_method','outcome_assessments.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('assessment_methods.course_id','=',$course_id)->count();
        $outcomeMapsCount = ProgramLearningOutcome::join('outcome_maps','program_learning_outcomes.pl_outcome_id','=','outcome_maps.pl_outcome_id')
                                ->join('learning_outcomes', 'outcome_maps.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('outcome_maps.map_scale_value','outcome_maps.pl_outcome_id','program_learning_outcomes.pl_outcome','outcome_maps.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('learning_outcomes.course_id','=',$course_id)->count();
        $standardsOutcomeMapCount = Standard::join('standards_outcome_maps', 'standards.standard_id', '=', 'standards_outcome_maps.standard_id')
                                ->join('learning_outcomes', 'standards_outcome_maps.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->join('standard_scales', 'standards_outcome_maps.standard_scale_id', '=', 'standard_scales.standard_scale_id')
                                ->select('standards_outcome_maps.standard_scale_id','standards_outcome_maps.standard_id','standards.s_outcome','standards_outcome_maps.l_outcome_id', 'learning_outcomes.l_outcome', 'standard_scales.abbreviation')
                                ->where('learning_outcomes.course_id','=',$course_id)->count();

        //
        $l_outcomes = LearningOutcome::where('course_id', $course_id)->get();
        $course =  Course::where('course_id', $course_id)->first();

        return view('courses.wizard.step1')->with('l_outcomes', $l_outcomes)->with('course', $course)->with('courseUsers', $courseUsers)->with('user', $user)->with('oAct', $oAct)->with('oAss', $oAss)->with('outcomeMapsCount', $outcomeMapsCount)
        ->with('isEditor', $isEditor)->with('isViewer', $isViewer)->with('standardsOutcomeMapCount', $standardsOutcomeMapCount);

    }

    public function step2($course_id, Request $request)
    {
        $isEditor = false;
        if ($request->isEditor) {
            $isEditor = true;
        }
        $isViewer = false;
        if ($request->isViewer) {
            return redirect()->route('courseWizard.step7', $course_id);        
        }

        //for header
        $user = User::where('id',Auth::id())->first();
        // returns a collection of courses associated with users 
        $myCourses = $user->courses;
        $courseUsers = array();
        foreach ($myCourses as $course) {
            $coursesUsers = $course->users()->get();
            $courseUsers[$course->course_id] = $coursesUsers;
        }
        $oAct = LearningActivity::join('outcome_activities','learning_activities.l_activity_id','=','outcome_activities.l_activity_id')
                                ->join('learning_outcomes', 'outcome_activities.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('outcome_activities.l_activity_id','learning_activities.l_activity','outcome_activities.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('learning_activities.course_id','=',$course_id)->count();
        $oAss = AssessmentMethod::join('outcome_assessments','assessment_methods.a_method_id','=','outcome_assessments.a_method_id')
                                ->join('learning_outcomes', 'outcome_assessments.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('assessment_methods.a_method_id','assessment_methods.a_method','outcome_assessments.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('assessment_methods.course_id','=',$course_id)->count();
        $outcomeMapsCount = ProgramLearningOutcome::join('outcome_maps','program_learning_outcomes.pl_outcome_id','=','outcome_maps.pl_outcome_id')
                                ->join('learning_outcomes', 'outcome_maps.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('outcome_maps.map_scale_value','outcome_maps.pl_outcome_id','program_learning_outcomes.pl_outcome','outcome_maps.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('learning_outcomes.course_id','=',$course_id)->count();
        $standardsOutcomeMapCount = Standard::join('standards_outcome_maps', 'standards.standard_id', '=', 'standards_outcome_maps.standard_id')
                                ->join('learning_outcomes', 'standards_outcome_maps.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->join('standard_scales', 'standards_outcome_maps.standard_scale_id', '=', 'standard_scales.standard_scale_id')
                                ->select('standards_outcome_maps.standard_scale_id','standards_outcome_maps.standard_id','standards.s_outcome','standards_outcome_maps.l_outcome_id', 'learning_outcomes.l_outcome', 'standard_scales.abbreviation')
                                ->where('learning_outcomes.course_id','=',$course_id)->count();

        //
        $a_methods = AssessmentMethod::where('course_id', $course_id)->get();
        $custom_methods = Custom_assessment_methods::select('custom_methods')->get();
        $totalWeight = AssessmentMethod::where('course_id', $course_id)->sum('weight');
        $course =  Course::where('course_id', $course_id)->first();

        return view('courses.wizard.step2')->with('a_methods', $a_methods)->with('course', $course)->with("totalWeight", $totalWeight)->with('courseUsers', $courseUsers)
        ->with('user', $user)->with('custom_methods',$custom_methods)->with('oAct', $oAct)->with('oAss', $oAss)->with('outcomeMapsCount', $outcomeMapsCount)
        ->with('isEditor', $isEditor)->with('isViewer', $isViewer)->with('standardsOutcomeMapCount', $standardsOutcomeMapCount);


    }

    public function step3($course_id, Request $request)
    {
        $isEditor = false;
        if ($request->isEditor) {
            $isEditor = true;
        }
        $isViewer = false;
        if ($request->isViewer) {
            return redirect()->route('courseWizard.step7', $course_id);
        }
        //for header
        $user = User::where('id',Auth::id())->first();
        // returns a collection of courses associated with users 
        $myCourses = $user->courses;
        $courseUsers = array();
        foreach ($myCourses as $course) {
            $coursesUsers = $course->users()->get();
            $courseUsers[$course->course_id] = $coursesUsers;
        }
        $oAct = LearningActivity::join('outcome_activities','learning_activities.l_activity_id','=','outcome_activities.l_activity_id')
                                ->join('learning_outcomes', 'outcome_activities.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('outcome_activities.l_activity_id','learning_activities.l_activity','outcome_activities.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('learning_activities.course_id','=',$course_id)->count();
        $oAss = AssessmentMethod::join('outcome_assessments','assessment_methods.a_method_id','=','outcome_assessments.a_method_id')
                                ->join('learning_outcomes', 'outcome_assessments.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('assessment_methods.a_method_id','assessment_methods.a_method','outcome_assessments.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('assessment_methods.course_id','=',$course_id)->count();
        $outcomeMapsCount = ProgramLearningOutcome::join('outcome_maps','program_learning_outcomes.pl_outcome_id','=','outcome_maps.pl_outcome_id')
                                ->join('learning_outcomes', 'outcome_maps.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('outcome_maps.map_scale_value','outcome_maps.pl_outcome_id','program_learning_outcomes.pl_outcome','outcome_maps.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('learning_outcomes.course_id','=',$course_id)->count();
        $standardsOutcomeMapCount = Standard::join('standards_outcome_maps', 'standards.standard_id', '=', 'standards_outcome_maps.standard_id')
                                ->join('learning_outcomes', 'standards_outcome_maps.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->join('standard_scales', 'standards_outcome_maps.standard_scale_id', '=', 'standard_scales.standard_scale_id')
                                ->select('standards_outcome_maps.standard_scale_id','standards_outcome_maps.standard_id','standards.s_outcome','standards_outcome_maps.l_outcome_id', 'learning_outcomes.l_outcome', 'standard_scales.abbreviation')
                                ->where('learning_outcomes.course_id','=',$course_id)->count();
        //

        $l_activities = LearningActivity::where('course_id', $course_id)->get();
        $custom_activities = Custom_learning_activities::select('custom_activities')->get();
        $course =  Course::where('course_id', $course_id)->first();

        return view('courses.wizard.step3')->with('l_activities', $l_activities)->with('course', $course)->with('courseUsers', $courseUsers)->with('user', $user)
        ->with('custom_activities',$custom_activities)->with('oAct', $oAct)->with('oAss', $oAss)->with('outcomeMapsCount', $outcomeMapsCount)
        ->with('isEditor', $isEditor)->with('isViewer', $isViewer)->with('standardsOutcomeMapCount', $standardsOutcomeMapCount);

    }

    public function step4($course_id, Request $request)
    {
        $isEditor = false;
        if ($request->isEditor) {
            $isEditor = true;
        }
        $isViewer = false;
        if ($request->isViewer) {
            return redirect()->route('courseWizard.step7', $course_id);
        }
        //for header
        $user = User::where('id',Auth::id())->first();
        // returns a collection of courses associated with users 
        $myCourses = $user->courses;
        $courseUsers = array();
        foreach ($myCourses as $course) {
            $coursesUsers = $course->users()->get();
            $courseUsers[$course->course_id] = $coursesUsers;
        }
        $oAct = LearningActivity::join('outcome_activities','learning_activities.l_activity_id','=','outcome_activities.l_activity_id')
                                ->join('learning_outcomes', 'outcome_activities.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('outcome_activities.l_activity_id','learning_activities.l_activity','outcome_activities.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('learning_activities.course_id','=',$course_id)->count();
        $oAss = AssessmentMethod::join('outcome_assessments','assessment_methods.a_method_id','=','outcome_assessments.a_method_id')
                                ->join('learning_outcomes', 'outcome_assessments.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('assessment_methods.a_method_id','assessment_methods.a_method','outcome_assessments.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('assessment_methods.course_id','=',$course_id)->count();
        $outcomeMapsCount = ProgramLearningOutcome::join('outcome_maps','program_learning_outcomes.pl_outcome_id','=','outcome_maps.pl_outcome_id')
                                ->join('learning_outcomes', 'outcome_maps.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('outcome_maps.map_scale_value','outcome_maps.pl_outcome_id','program_learning_outcomes.pl_outcome','outcome_maps.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('learning_outcomes.course_id','=',$course_id)->count();
        $standardsOutcomeMapCount = Standard::join('standards_outcome_maps', 'standards.standard_id', '=', 'standards_outcome_maps.standard_id')
                                ->join('learning_outcomes', 'standards_outcome_maps.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->join('standard_scales', 'standards_outcome_maps.standard_scale_id', '=', 'standard_scales.standard_scale_id')
                                ->select('standards_outcome_maps.standard_scale_id','standards_outcome_maps.standard_id','standards.s_outcome','standards_outcome_maps.l_outcome_id', 'learning_outcomes.l_outcome', 'standard_scales.abbreviation')
                                ->where('learning_outcomes.course_id','=',$course_id)->count();

        //
        $l_outcomes = LearningOutcome::where('course_id', $course_id)->get();
        $course =  Course::where('course_id', $course_id)->first();
        $l_activities = LearningActivity::where('course_id', $course_id)->get();
        $a_methods = AssessmentMethod::where('course_id', $course_id)->get();

        return view('courses.wizard.step4')->with('l_outcomes', $l_outcomes)->with('course', $course)->with('l_activities', $l_activities)->with('a_methods', $a_methods)
        ->with('courseUsers', $courseUsers)->with('user', $user)->with('oAct', $oAct)->with('oAss', $oAss)->with('outcomeMapsCount', $outcomeMapsCount)
        ->with('isEditor', $isEditor)->with('isViewer', $isViewer)->with('standardsOutcomeMapCount', $standardsOutcomeMapCount);
    }

    // Program Outcome Mapping
    public function step5($course_id, Request $request)
    {
        $isEditor = false;
        if ($request->isEditor) {
            $isEditor = true;
        }
        $isViewer = false;
        if ($request->isViewer) {
            return redirect()->route('courseWizard.step7', $course_id);
        }
        // for header
        $user = User::where('id',Auth::id())->first();
        // returns a collection of courses associated with users 
        $myCourses = $user->courses;
        $courseUsers = array();
        foreach ($myCourses as $course) {
            $coursesUsers = $course->users()->get();
            $courseUsers[$course->course_id] = $coursesUsers;
        }
        $course = Course::find($course_id);
        $oAct = LearningActivity::join('outcome_activities','learning_activities.l_activity_id','=','outcome_activities.l_activity_id')
                                ->join('learning_outcomes', 'outcome_activities.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('outcome_activities.l_activity_id','learning_activities.l_activity','outcome_activities.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('learning_activities.course_id','=',$course_id)->count();
        $oAss = AssessmentMethod::join('outcome_assessments','assessment_methods.a_method_id','=','outcome_assessments.a_method_id')
                                ->join('learning_outcomes', 'outcome_assessments.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('assessment_methods.a_method_id','assessment_methods.a_method','outcome_assessments.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('assessment_methods.course_id','=',$course_id)->count();
        $outcomeMapsCount = ProgramLearningOutcome::join('outcome_maps','program_learning_outcomes.pl_outcome_id','=','outcome_maps.pl_outcome_id')
                                ->join('learning_outcomes', 'outcome_maps.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('outcome_maps.map_scale_id','outcome_maps.pl_outcome_id','program_learning_outcomes.pl_outcome','outcome_maps.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('learning_outcomes.course_id','=',$course_id)->count();
        $standardsOutcomeMapCount = Standard::join('standards_outcome_maps', 'standards.standard_id', '=', 'standards_outcome_maps.standard_id')
                                ->join('learning_outcomes', 'standards_outcome_maps.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->join('standard_scales', 'standards_outcome_maps.standard_scale_id', '=', 'standard_scales.standard_scale_id')
                                ->select('standards_outcome_maps.standard_scale_id','standards_outcome_maps.standard_id','standards.s_outcome','standards_outcome_maps.l_outcome_id', 'learning_outcomes.l_outcome', 'standard_scales.abbreviation')
                                ->where('learning_outcomes.course_id','=',$course_id)->count();

        return view('courses.wizard.step5')->with('course', $course)->with('user', $user)->with('oAct', $oAct)->with('oAss', $oAss)->with('outcomeMapsCount', $outcomeMapsCount)
        ->with('isEditor', $isEditor)->with('isViewer', $isViewer)->with('courseUsers', $courseUsers)->with('standardsOutcomeMapCount', $standardsOutcomeMapCount);
    }

    public function step6($course_id, Request $request)
    {
        $isEditor = false;
        if ($request->isEditor) {
            $isEditor = true;
        }
        $isViewer = false;
        if ($request->isViewer) {
            return redirect()->route('courseWizard.step7', $course_id);
        }
        // for header
        $user = User::where('id',Auth::id())->first();
        // returns a collection of courses associated with users 
        $myCourses = $user->courses;
        $courseUsers = array();
        foreach ($myCourses as $course) {
            $coursesUsers = $course->users()->get();
            $courseUsers[$course->course_id] = $coursesUsers;
        }
        $course = Course::find($course_id);
        $oAct = LearningActivity::join('outcome_activities','learning_activities.l_activity_id','=','outcome_activities.l_activity_id')
                                ->join('learning_outcomes', 'outcome_activities.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('outcome_activities.l_activity_id','learning_activities.l_activity','outcome_activities.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('learning_activities.course_id','=',$course_id)->count();
        $oAss = AssessmentMethod::join('outcome_assessments','assessment_methods.a_method_id','=','outcome_assessments.a_method_id')
                                ->join('learning_outcomes', 'outcome_assessments.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('assessment_methods.a_method_id','assessment_methods.a_method','outcome_assessments.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('assessment_methods.course_id','=',$course_id)->count();
        $outcomeMapsCount = ProgramLearningOutcome::join('outcome_maps','program_learning_outcomes.pl_outcome_id','=','outcome_maps.pl_outcome_id')
                                ->join('learning_outcomes', 'outcome_maps.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('outcome_maps.map_scale_value','outcome_maps.pl_outcome_id','program_learning_outcomes.pl_outcome','outcome_maps.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('learning_outcomes.course_id','=',$course_id)->count();
        $standardsOutcomeMapCount = Standard::join('standards_outcome_maps', 'standards.standard_id', '=', 'standards_outcome_maps.standard_id')
                                ->join('learning_outcomes', 'standards_outcome_maps.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->join('standard_scales', 'standards_outcome_maps.standard_scale_id', '=', 'standard_scales.standard_scale_id')
                                ->select('standards_outcome_maps.standard_scale_id','standards_outcome_maps.standard_id','standards.s_outcome','standards_outcome_maps.l_outcome_id', 'learning_outcomes.l_outcome', 'standard_scales.abbreviation')
                                ->where('learning_outcomes.course_id','=',$course_id)->count();

        // get learning outcomes for a course
        $l_outcomes = LearningOutcome::where('course_id', $course_id)->get();
        // get Standards and strategic outcomes for a course
        $standard_outcomes = Standard::where('standard_category_id', $course->standard_category_id)->get();
        // get mapping scales associated with course
        $mappingScales = StandardScale::where('scale_category_id', $course->scale_category_id)->get();

        $optionalPriorityCategories = OptionalPriorityCategories::all();
        $optionalPrioritySubcategories = OptionalPrioritySubcategories::all();
        $optionalPriories = OptionalPriorities::all();
        //dd($optionalPriorityCategories, $optionalPrioritySubcategories, $optionalPriories);
        $opStored = CourseOptionalPriorities::where('course_id', $course_id)->pluck('op_id')->toArray();

        //get optional priorities for each subcategory
        $number_of_optional_priority_subcats = 6;
        $optional_priorities = array();
        for ($i = 1; $i <= $number_of_optional_priority_subcats; $i++) {
            $optional_priorities[] = OptionalPriorities::where('subcat_id', $i)->pluck('optional_priority')->toArray();
        }
        


        //retrieve descriptions for the optional priorities which belong to the course being edited
        $course_optional_priorities_op_ids = CourseOptionalPriorities::where('course_id', $course_id)->pluck('op_id');
        $course_optional_priorities_descriptions = OptionalPriorities::whereIn('op_id', $course_optional_priorities_op_ids)->pluck('optional_priority')->toArray();

        return view('courses.wizard.step6')->with('l_outcomes', $l_outcomes)->with('course', $course)->with('mappingScales', $mappingScales)->with('courseUsers', $courseUsers)->with('user', $user)
                                        ->with('oAct', $oAct)->with('oAss', $oAss)->with('outcomeMapsCount', $outcomeMapsCount)
                                        ->with('bc_labour_market',$optional_priorities[1])->with('shaping_ubc',$optional_priorities[2])->with('ubc_mandate_letters',$optional_priorities[0])->with('okanagan_2040_outlook',$optional_priorities[3])
                                        ->with('ubc_indigenous_plan',$optional_priorities[4])->with('ubc_climate_priorities',$optional_priorities[5])->with('optional_PLOs',$course_optional_priorities_descriptions)
                                        ->with('standard_outcomes', $standard_outcomes)->with('isEditor', $isEditor)->with('isViewer', $isViewer)->with('courseUsers', $courseUsers)
                                        ->with('optionalPriorityCategories', $optionalPriorityCategories)->with('optionalPrioritySubcategories', $optionalPrioritySubcategories)->with('optionalPriories', $optionalPriories)
                                        ->with('opStored', $opStored)->with('standardsOutcomeMapCount', $standardsOutcomeMapCount);
    }
    
    public function step7($course_id, Request $request)
    {
        $isEditor = false;
        if ($request->isEditor) {
            $isEditor = true;
        }
        $isViewer = false;
        if ($request->isViewer) {
            $isViewer = true;
        }
        //for header
        $user = User::where('id',Auth::id())->first();
        // returns a collection of courses associated with users 
        $myCourses = $user->courses;
        $courseUsers = array();
        foreach ($myCourses as $course) {
            $coursesUsers = $course->users()->get();
            $courseUsers[$course->course_id] = $coursesUsers;
        }
        $course =  Course::find($course_id);
        $oActCount = LearningActivity::join('outcome_activities','learning_activities.l_activity_id','=','outcome_activities.l_activity_id')
                                ->join('learning_outcomes', 'outcome_activities.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('outcome_activities.l_activity_id','learning_activities.l_activity','outcome_activities.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('learning_activities.course_id','=',$course_id)->count();
        $oAssCount = AssessmentMethod::join('outcome_assessments','assessment_methods.a_method_id','=','outcome_assessments.a_method_id')
                                ->join('learning_outcomes', 'outcome_assessments.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('assessment_methods.a_method_id','assessment_methods.a_method','outcome_assessments.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('assessment_methods.course_id','=',$course_id)->count();
        $outcomeMapsCount = ProgramLearningOutcome::join('outcome_maps','program_learning_outcomes.pl_outcome_id','=','outcome_maps.pl_outcome_id')
                                ->join('learning_outcomes', 'outcome_maps.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('outcome_maps.map_scale_value','outcome_maps.pl_outcome_id','program_learning_outcomes.pl_outcome','outcome_maps.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('learning_outcomes.course_id','=',$course_id)->count();
        $standardsOutcomeMapCount = Standard::join('standards_outcome_maps', 'standards.standard_id', '=', 'standards_outcome_maps.standard_id')
                                ->join('learning_outcomes', 'standards_outcome_maps.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->join('standard_scales', 'standards_outcome_maps.standard_scale_id', '=', 'standard_scales.standard_scale_id')
                                ->select('standards_outcome_maps.standard_scale_id','standards_outcome_maps.standard_id','standards.s_outcome','standards_outcome_maps.l_outcome_id', 'learning_outcomes.l_outcome', 'standard_scales.abbreviation')
                                ->where('learning_outcomes.course_id','=',$course_id)->count();

        // get all the programs this course belongs to
        $coursePrograms = $course->programs;
        // get the PLOs for each program
        $programsLearningOutcomes = array();

        $coursePrograms->map(function($courseProgram, $key) {
            $courseProgram->push(0, 'num_plos_categorized');
            $courseProgram->programLearningOutcomes->each(function($plo, $key) use ($courseProgram) {
                if (isset($plo->category)) {
                    $courseProgram->num_plos_categorized++;
                }
            });            
        });

        foreach ($coursePrograms as $courseProgram) {
            $programsLearningOutcomes[$courseProgram->program_id] = $courseProgram->programLearningOutcomes;
        }
        // courseProgramsOutcomeMaps[$program_id][$plo][$clo] = mapping scale
        $courseProgramsOutcomeMaps = array();
        foreach ($programsLearningOutcomes as $programId => $programLearningOutcomes) {
            foreach ($programLearningOutcomes as $programLearningOutcome) {
                $outcomeMaps = $programLearningOutcome->learningOutcomes->where('course_id', $course_id);
                foreach($outcomeMaps as $outcomeMap){
                    $courseProgramsOutcomeMaps[$programId][$programLearningOutcome->pl_outcome_id][$outcomeMap->l_outcome_id] = MappingScale::find($outcomeMap->pivot->map_scale_id);
                } 
            }
        }

        // get standards outcome map
        $standardsOutcomeMap = Standard::join('standards_outcome_maps', 'standards.standard_id', '=', 'standards_outcome_maps.standard_id')
                                ->join('learning_outcomes', 'standards_outcome_maps.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->join('standard_scales', 'standards_outcome_maps.standard_scale_id', '=', 'standard_scales.standard_scale_id')
                                ->select('standards_outcome_maps.standard_scale_id','standards_outcome_maps.standard_id','standards.s_outcome','standards_outcome_maps.l_outcome_id', 'learning_outcomes.l_outcome', 'standard_scales.abbreviation')
                                ->where('learning_outcomes.course_id','=',$course_id)->get();
        
        $outcomeActivities = LearningActivity::join('outcome_activities','learning_activities.l_activity_id','=','outcome_activities.l_activity_id')
                                ->join('learning_outcomes', 'outcome_activities.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('outcome_activities.l_activity_id','learning_activities.l_activity','outcome_activities.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('learning_activities.course_id','=',$course_id)->get();

        $outcomeAssessments = AssessmentMethod::join('outcome_assessments','assessment_methods.a_method_id','=','outcome_assessments.a_method_id')
                                ->join('learning_outcomes', 'outcome_assessments.l_outcome_id', '=', 'learning_outcomes.l_outcome_id' )
                                ->select('assessment_methods.a_method_id','assessment_methods.a_method','outcome_assessments.l_outcome_id', 'learning_outcomes.l_outcome')
                                ->where('assessment_methods.course_id','=',$course_id)->get();

        $assessmentMethodsTotal = 0;
        foreach ($course->assessmentMethods as $a_method) {
            $assessmentMethodsTotal += $a_method->weight;
        }

        // get subcategories for optional priorities
        $optionalPriorities = $course->optionalPriorities;
        $optionalSubcategories = array();
        foreach ($optionalPriorities as $optionalPriority) {
            $optionalSubcategories[$optionalPriority->subcat_id] = $optionalPriority->optionalPrioritySubcategory;
        }

        return view('courses.wizard.step7')->with('course', $course)->with('outcomeActivities', $outcomeActivities)->with('outcomeAssessments', $outcomeAssessments)->with('user', $user)->with('oAct', $oActCount)
        ->with('oAss', $oAssCount)->with('outcomeMapsCount', $outcomeMapsCount)->with('courseProgramsOutcomeMaps', $courseProgramsOutcomeMaps)->with('assessmentMethodsTotal', $assessmentMethodsTotal)
        ->with('standardsOutcomeMap', $standardsOutcomeMap)->with('isEditor', $isEditor)->with('isViewer', $isViewer)->with('courseUsers', $courseUsers)->with('optionalSubcategories', $optionalSubcategories)
        ->with('standardsOutcomeMapCount', $standardsOutcomeMapCount);
    }

}
