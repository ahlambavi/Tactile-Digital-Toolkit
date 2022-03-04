@extends('layouts.app')

@section('content')
<div id="step1Courses">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include('courses.wizard.header')

            <div class="card">
                <div class="card-header wizard">
                    <h3 class="">
                        Course Learning Outcomes (CLOs)
                        <div style="float: right;">
                            <button id="cloHelp" style="border: none; background: none; outline: none;" data-bs-toggle="modal" href="#guideModal">
                                <i class="bi bi-question-circle" style="color:#002145;"></i>
                            </button>
                        </div>
                        <div class="text-left">
                            @include('layouts.guide')
                        </div>
                    </h3>
                    
                    <!-- Add CLO Modal: Bloom’s Taxonomy of Learning Modal -->
                    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="addLearningOutcomeModal" tabindex="-1" role="dialog"
                        aria-labelledby="addLearningOutcomeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addLearningOutcomeModalLabel"><i class="bi bi-pencil-fill btn-icon mr-2"></i> Course Learning Outcomes or Competencies
                                    </h5>
                                </div>

                                <div class="modal-body text-left">
                                        <form id="addCLOForm" class="needs-validation" novalidate>
                                            <div class="form-group row align-items-end">
                                                <div class="col-6">
                                                    <label for="l_outcome" class="form-label fs-6">
                                                        <span class="requiredField">* </span>
                                                        <b>Course Learning Outcome (CLO)</b>
                                                        <div><small class="form-text text-muted" style="font-size:12px"><a href="https://tips.uark.edu/using-blooms-taxonomy/" target="_blank"><b><i class="bi bi-box-arrow-up-right"></i> Click here</b></a> for tips to write effective CLOs.</small></div>
                                                    </label>
                
                                                    <textarea id="l_outcome" class="form-control" name="l_outcome" required autofocus placeholder="E.g. Develop..." style="resize:none"></textarea>
                                                    <div class="invalid-tooltip">
                                                        You must input a course learning outcome or competency.
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <label for="title" class="form-label fs-6">
                                                        <b>Short Phrase</b>
                                                        <div><small class="form-text text-muted" style="font-size:12px"><b><i class="bi bi-exclamation-circle-fill text-warning" data-bs-toggle="tooltip" data-bs-placement="left" title="Having a short phrase helps with visualizing your course summary at the end of the mapping process"></i> 50 character limit.</b></small></div>
                                                    </label>
                                                    <textarea id="title" class="form-control" name="title" autofocus placeholder="E.g Experimental Design..." maxlength="50" style="resize:none"></textarea> 
                                                </div>
                                                <div class="col-2">
                                                    <button id="addCLOBtn" type="submit" class="btn btn-primary col mb-1">Add</button>
                                                </div>
                                            </div>
                                        </form>

                                        <div class="row justify-content-center">
                                            <div class="col-8">
                                                <hr>
                                            </div>
                                        </div>                

                                        <div class="row m-1">
                                            <table id="addCLOTbl" class="table table-light table-borderless">
                                                <thead>
                                                    <tr class="table-primary">
                                                        <th class="text-left">Course Learning Outcomes or Competencies</th>
                                                        <th class="text-left">Short Phrase</th>
                                                        <th class="text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($l_outcomes as $index => $l_outcome)
                                                        <tr>
                                                            <td>
                                                                <textarea name="current_l_outcome[{{$l_outcome->l_outcome_id}}]" value="{{$l_outcome->l_outcome}}" id="l_outcome{{$l_outcome->l_outcome_id}}" 
                                                                class="form-control @error('l_outcome') is-invalid @enderror" form="saveCLOChanges" required>{{$l_outcome->l_outcome}}</textarea>
                                                            </td>
                                                            <td>
                                                                <textarea type="text" name="current_l_outcome_short_phrase[{{$l_outcome->l_outcome_id}}]" id="l_outcome_short_phrase{{$l_outcome->l_outcome_id}}"
                                                                class="form-control @error('clo_shortphrase') is-invalid @enderror"  form="saveCLOChanges">{{$l_outcome->clo_shortphrase}}</textarea>
                                                            </td>
                                                            <td class="text-center">
                                                                <i class="bi bi-x-circle-fill text-danger fs-4 btn" onclick="deleteCLO(this)"></i>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- <div>
                                            <button id="showbtn" class="btn btn-primary bg-primary" onclick="tips()">Show Tips For Writing CLOs</button>
                                        </div>
                                        <div id="blooms" style="display: none;">
                                            <p style="margin-top: 25px;margin-left:4px;margin-right:4px;">A well-written learning outcome states what students are expected to <span style="font-style: italic;">know, be able to do, or care about</span>, after successfully completing the course/program. Such statements begin with one measurable verb.</p>
                                            <p>The below are examples of verbs associated with different levels of Bloom’s Taxonomy of Learning.</p>
                                            <img class="img-fluid" src=" {{ asset('img/blooms-taxonomy-diagram.png') }}"/>
                                            <small>
                                                Source: Anderson, L. W., Krathwohl, D. R., & Bloom, B. S. (2001). A taxonomy for learning, teaching, and assessing: A revision of bloom's taxonomy of educational objectives (Abridged ed.). New York: Longman.
                                            </small>
                                        </div> -->

                                    </div>

                                    <form method="POST" id="saveCLOChanges" action="{{ action('LearningOutcomeController@store') }}">
                                    @csrf
                                        <div class="modal-footer">
                                            <input type="hidden" name="course_id" value="{{$course->course_id}}" form="saveCLOChanges">
                                            <button id="cancel" type="button" class="btn btn-secondary col-3" data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success col-3">Save Changes</button>
                                        </div>
                                    </form>

                            </div>
                        </div>
                    </div>
                    <!-- End of Add CLO Modal -->

                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-subtitle mb-2 lh-lg">
                                Input the <a href="https://ctl.ok.ubc.ca/teaching-development/classroom-practices/learning-outcomes/" target="_blank"><i class="bi bi-box-arrow-up-right"></i> course learning outcomes (CLOs)</a> or <a href="https://sph.uth.edu/content/uploads/2012/01/Competencies-and-Learning-Objectives.pdf" target="_blank"><i class="bi bi-box-arrow-up-right"></i> competencies</a> of the course individually.                    
                            </h6>
                        </div>
                    </div>
                    <div class="row mb-2 align-items-end">
                        <form method="POST" class="col-6" action="{{ action('LearningOutcomeController@import') }}" enctype="multipart/form-data">
                            <p>
                                <a href="{{asset('import_samples/import-clos-template.xlsx')}}" download><i class="bi bi-download mb-1"></i> import-clos-template.xlsx</a>
                                or
                                <a href="{{asset('import_samples/import-clos-template.csv')}}" download><i class="bi bi-download mb-1"></i> import-clos-template.csv</a>
                            </p>
                            @csrf
                            <div class="input-group">
                                <input type="hidden" name="course_id" value="{{$course->course_id}}">
                                <input type="file" name="upload" class="form-control" aria-label="Upload" required accept=".xlsx, .csv">
                                <button class="btn bg-primary text-white" type="submit" ><b>Import CLOs</b><i class="bi bi-box-arrow-in-down-left pl-2"></i></button>
                            </div>
                        </form>
                        <div class="col-6 text-right">
                            <button type="button" class="btn btn-primary btn-lg col-4 fs-5 bg-primary text-white"  data-toggle="modal" data-target="#addLearningOutcomeModal">
                                <b ><i class="bi bi-plus mr-2"></i>CLO</b>
                            </button>
                        </div>
                    </div>

                    <div id="clo">
                        <div class="row">
                            <div class="col">

                                    @if(count($l_outcomes)<1)
                                        <div class="alert alert-warning wizard">
                                            <i class="bi bi-exclamation-circle-fill"></i>There are no course learning outcomes set for this course.                    
                                        </div>
                                    @else
                                        <table class="table table-light table-bordered" >
                                            <tr class="table-primary">
                                                <th class="text-center">#</th>
                                                <th>Course Learning Outcomes or Competencies</th>
                                                <th class="text-center w-25">Actions</th>
                                            </tr>

                                                @foreach($l_outcomes as $index => $l_outcome)
                                                <tr>
                                                    <td class="text-center fw-bold" style="width:5%" >{{$index+1}}</td>                                                
                                                    <td>
                                                        <b>{{$l_outcome->clo_shortphrase}}</b><br>
                                                        {{$l_outcome->l_outcome}}
                                                    </td>
                                                    <td class="text-center align-middle">

                                                        <button type="button" style="width:60px;" class="btn btn-secondary btn-sm m-1" data-toggle="modal" data-target="#addLearningOutcomeModal">
                                                            Edit
                                                        </button>

                                                        <button style="width:60px;" type="button" class="btn btn-danger btn-sm btn btn-danger btn-sm m-1"
                                                        data-toggle="modal" data-target="#CLOdeleteConfirmation{{$l_outcome->l_outcome_id}}">
                                                            Delete
                                                        </button>

                                                        <!-- Bloom’s Taxonomy of Learning Modal 
                                                        <div class="modal fade" id="editLearningOutcomeModal{{$l_outcome->l_outcome_id}}" tabindex="-1" role="dialog"
                                                            aria-labelledby="editLearningOutcomeModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="editLearningOutcomeModalLabel">Edit Course Learning Outcome or Competency
                                                                        </h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>

                                                                    <form method="POST" action="{{ route('lo.update', $l_outcome->l_outcome_id) }}">
                                                                        @csrf
                                                                        {{method_field('PUT')}}
                                                                        <div class="modal-body">
                                                                            <div class="form-group row">
                                                                                <label for="l_outcome" class="col-md-4 col-form-label text-md-center">Course Learning Outcome (CLO) or Competency</label>

                                                                                <div class="col-md-8">
                                                                                    <textarea id="l_outcome" class="form-control" @error('l_outcome') is-invalid @enderror
                                                                                    rows="3" name="l_outcome" required autofocus placeholder="Develop...">{{$l_outcome->l_outcome}}</textarea>

                                                                                    @error('l_outcome')
                                                                                    <span class="invalid-feedback" role="alert">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                    @enderror

                                                                                    <small class="form-text text-muted">
                                                                                        <p>Add <strong>One CLO</strong> at a time. <a href="https://tips.uark.edu/using-blooms-taxonomy/" target="_blank"><strong><i class="bi bi-box-arrow-up-right"></i> Click here</strong></a>
                                                                                        for tips to write effective CLOs</p>
                                                                                    </small>

                                                                                </div>
                                                                            </div>

                                                                            <div class="form-group row">
                                                                                <label for="title" class="col-md-4 col-form-label text-md-right">Short Phrase</label>

                                                                                <div class="col-md-8">
                                                                                    <input id="title" type="text" class="form-control @error('title') is-invalid @enderror"
                                                                                    name="title" autofocus placeholder="Experiment..." value="{{$l_outcome->clo_shortphrase}}">

                                                                                    @error('title')
                                                                                    <span class="invalid-feedback" role="alert">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                    @enderror

                                                                                    <small class="form-text text-muted">
                                                                                        Having a short phrase helps with data visualization at the end of this process <strong>(4 words max)</strong>.
                                                                                    </small>
                                                                                </div>

                                                                                <div style="col-md-8; text-align: center; margin-top:20px">

                                                                                    <div>
                                                                                        <p style="margin-top: 25px;margin-left:4px;margin-right:4px;">A well-written learning outcome states what students are expected to <span style="font-style: italic;">know, be able to do, or care about</span>, after successfully completing the course/program. Such statements begin with one measurable verb.</p>
                                                                                        <p>The below are examples of verbs associated with different levels of Bloom’s Taxonomy of Learning.</p>
                                                                                    </div>

                                                                                    <img class="img-fluid" src=" {{ asset('img/blooms-taxonomy-diagram.png') }}"/>
                                                                                    <div class="flex-container">
                                                                                        <div class="box" style="background-color: #e8f4f8;">
                                                                                            <strong>REMEMBER</strong>
                                                                                            <p>Retrieve relevant knowledge from long-term memory</p>
                                                                                        </div>
                                                                                        <div class="box" style="background-color: #E6E6FA;">
                                                                                            <strong>UNDERSTAND</strong>
                                                                                            <p>Construct meaning from instructional messages</p>
                                                                                        </div>
                                                                                        <div class="box" style="background-color: #c1e1ec;">
                                                                                            <strong>APPLY</strong>
                                                                                            <p>Carry out or use a procedure in a given situation</p>
                                                                                        </div>
                                                                                        <div class="box" style="background-color: #ADD8E6;">
                                                                                            <strong>ANALYZE</strong>
                                                                                            <p>Break material into its constituent parts and determine how the parts relate</p>
                                                                                        </div>
                                                                                        <div class="box" style="background-color: #87CEEB;">
                                                                                            <strong>EVALUATE</strong>
                                                                                            <p>Make judgments based on criteria and standards</p>
                                                                                        </div>
                                                                                        <div class="box" style="background-color: #6495ED;">
                                                                                            <strong>CREATE</strong>
                                                                                            <p>Put elements together to form a coherent or functional whole</p>
                                                                                        </div>

                                                                                        <div class="box">
                                                                                            <p class="CLO_example">Example: define, describe, identify, list, locate, match, memorize, recall, recognize, reproduce, select, state</p>
                                                                                        </div>
                                                                                        <div class="box">
                                                                                            <p class="CLO_example">Example: classify，compare，discuss，distinguish，exemplify，explain，illustrate，infer，interpret，paraphrase，predict，summarize</p>
                                                                                        </div>
                                                                                        <div class="box">
                                                                                            <p class="CLO_example">Example: calculate，construct，demonstrate，dramatize，employ，execute，implement，manipulate，modify，simulate, solve</p>
                                                                                        </div>
                                                                                        <div class="box">
                                                                                            <p class="CLO_example">Example: attribute，categorize，classify，compare，correlate，deduce，differentiate，distinguish，organize</p>
                                                                                        </div>
                                                                                        <div class="box">
                                                                                            <p class="CLO_example">Example: assess，check，critique，decide，defend，judge，justify，persuade，recommend，support</p>
                                                                                        </div>
                                                                                        <div class="box">
                                                                                            <p class="CLO_example">Example: compile，compose，construct，design，develop，formulate，generate，hypothesize，integrate，modify, plan，produce</p>
                                                                                        </div>
                                                                                    </div>

                                                                                    <small>
                                                                                        Source: Anderson, L. W., Krathwohl, D. R., & Bloom, B. S. (2001). A taxonomy for learning, teaching, and assessing: A revision of bloom's taxonomy of educational objectives (Abridged ed.). New York: Longman.
                                                                                    </small>
                                                                                </div>

                                                                            </div>

                                                                            <input type="hidden" name="course_id" value="{{$course->course_id}}">

                                                                        </div>

                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary col-2 btn-sm" data-dismiss="modal">Close</button>
                                                                            <button type="submit" class="btn btn-primary col-2 btn-sm">Save</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div> -->

                                                        <!-- Delete Confirmation Modal -->
                                                        <div class="modal fade" id="CLOdeleteConfirmation{{$l_outcome->l_outcome_id}}" tabindex="-1" role="dialog" aria-labelledby="CLOdeleteConfirmation" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="CLOdeleteConfirmation">Delete Confirmation</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>

                                                                    <div class="modal-body text-left">
                                                                    Are you sure you want to delete {{$l_outcome->l_outcome}}
                                                                    </div>

                                                                    <form class="float-right ml-2" action="{{route('lo.destroy', $l_outcome->l_outcome_id)}}" method="POST">
                                                                        @csrf
                                                                        {{method_field('DELETE')}}
                                                                        <input type="hidden" name="course_id" value="{{$course->course_id}}">

                                                                        <div class="modal-footer">
                                                                            <button style="width:60px" type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                                                                            <button style="width:60px;" type="submit" class="btn btn-danger btn-sm ">Delete</button>
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </td>
                                                </tr>
                                                @endforeach
                                        </table>
                                    @endif      
                            </div>
                        </div>
                    </div>
                </div>

                <!-- card footer -->
                <div class="card-footer">
                    <div class="card-body mb-4">

                        <a href="{{route('courseWizard.step2', $course->course_id)}}">
                            <button class="btn btn-sm btn-primary col-3 float-right">Student Assessment Methods <i class="bi bi-arrow-right mr-2"></i></button>
                        </a>
                    </div>
                </div>            
            </div>
        </div>
    </div>
</div>

<script type="application/javascript">
    
    $(document).ready(function () {
        // Enables functionality of tool tips
        $('[data-bs-toggle="tooltip"]').tooltip({html:true});


        $('#addCLOForm').submit(function (event) {
            // prevent default form submission handling
            event.preventDefault();
            event.stopPropagation();
            // check if input fields contain data
            if ($('#l_outcome').val().length != 0) {
                addCLO();
                // reset form 
                $(this).trigger('reset');
                $(this).removeClass('was-validated');
            } else {
                // mark form as validated
                $(this).addClass('was-validated');
            }
            // readjust modal's position 
            document.querySelector('#addLearningOutcomeModal').handleUpdate();
        });

        $('#cancel').click(function(event) {
            $('#addCLOTbl tbody').html(`
                @foreach($l_outcomes as $index => $l_outcome)
                    <tr>
                        <td>
                            <textarea name="current_l_outcome[{{$l_outcome->l_outcome_id}}]" value="{{$l_outcome->l_outcome}}" id="l_outcome{{$l_outcome->l_outcome_id}}" 
                            class="form-control @error('l_outcome') is-invalid @enderror" form="saveCLOChanges" required>{{$l_outcome->l_outcome}}</textarea>
                        </td>
                        <td>
                            <textarea type="text" name="current_l_outcome_short_phrase[{{$l_outcome->l_outcome_id}}]" id="l_outcome_short_phrase{{$l_outcome->l_outcome_id}}"
                            class="form-control @error('clo_shortphrase') is-invalid @enderror" form="saveCLOChanges">{{$l_outcome->clo_shortphrase}}</textarea>
                        </td>
                        <td class="text-center">
                            <i class="bi bi-x-circle-fill text-danger fs-4 btn" onclick="deleteCLO(this)"></i>
                        </td>
                    </tr>
                @endforeach 
            `);
        });
    });

    function tips() {
        var x = document.getElementById('blooms');
        if (x.style.display === "none") {
            x.style.display = "block";
            document.querySelector("#showbtn").innerHTML = "Hide Tips For Writing CLOs";
        } else {
            x.style.display = "none";
            document.querySelector("#showbtn").innerHTML = "Show Tips For Writing CLOs";
        }
    }

    function deleteCLO(submitter) {
            console.log(submitter);
            $(submitter).parents('tr').remove();
    }

    function addCLO() {
        // prepend assessment method to the table
        $('#addCLOTbl tbody').prepend(`
            <tr>
                <td>
                    <textarea name="new_l_outcomes[]" value="${$('#l_outcome').val()}" class="form-control @error('l_outcome') is-invalid @enderror" form="saveCLOChanges" required>${$('#l_outcome').val()}</textarea>
                </td>
                <td>
                    <textarea type="text" name="new_short_phrases[]" class="form-control @error('clo_shortphrase') is-invalid @enderror" form="saveCLOChanges">${$('#title').val()}</textarea>
                </td>
                <td class="text-center">
                    <i class="bi bi-x-circle-fill text-danger fs-4 btn" onclick="deleteCLO(this)"></i>
                </td>
            </tr>        
        `);
    }
</script>
@endsection
