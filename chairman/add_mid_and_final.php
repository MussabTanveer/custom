<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Assign Weightage");
    $PAGE->set_heading("Assign Weightage");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/add_mid_and_final.php');
    
	require_login();
    if($SESSION->oberole != "chairman"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();
	/*
	
$recm=$DB->get_records_sql('Select * from mdl_grading_policy WHERE name="mid term"
	');

foreach($recm as $recordm){

//$courseidm=$recordm->courseid;


$sqlm="INSERT INTO mdl_grading_policy (percentage) VALUES ('20')";
$DB->execute($sqlm);
}


$recf=$DB->get_records_sql('Select *  from mdl_grading_policy WHERE name="final exam" ');


//$courseidf=$recordf->courseid;
foreach($recf as $recordf){
$sqlf="INSERT INTO mdl_grading_policy (percentage) VALUES ('60')";
$DB->execute($sqlf);

}
*/

$rec1=$DB->get_records_sql('Select SUM(percentage) as totalpercentage from mdl_grading_policy');
foreach($rec1 as $record1){

$totalpercentage=$record1->totalpercentage;

}

echo $totalpercentage;
if($totalpercentage > 100){
redirect('grading_policy_full.php');

}

if(isset($_POST['save'])){




if(isset($_POST["midterm"])){

$midterm = trim($_POST["midterm"]);


}

if(isset($_POST["final"])){

$final = trim($_POST["final"]);
}

if(isset($_POST["sessional"])){
$sessional=trim($_POST["sessional"]);
}
//echo $sessional;


$sum=$midterm+$final+$sessional;

if($sum < 100){


echo "<font color = red>The sum of the below entered Weightages should be 100%!</font><br />";

}

else{

$rec=$DB->get_records_sql('Select id from mdl_course WHERE shortname NOT LIKE "CIS"');

if($rec){
foreach($rec as $record){

$courseid=$record->id;

$sql1="INSERT INTO mdl_grading_policy (courseid,name,percentage) VALUES ('$courseid','mid term','$midterm')";
$DB->execute($sql1);

$sql2="INSERT INTO mdl_grading_policy (courseid,name,percentage) VALUES ('$courseid','final exam','$final')";
$DB->execute($sql2);
}

$msgP = "<font color = green>Weightage successfully assigned!</font><br />";

}


else{


	echo "<font color = red>No Courses Found!</font><br />";
}

}

}

elseif(isset($_POST['return'])){


if(isset($_POST["midterm"])){

$midterm = trim($_POST["midterm"]);


}

if(isset($_POST["final"])){

$final = trim($_POST["final"]);
}

if(isset($_POST["sessional"])){
$sessional=trim($_POST["sessional"]);
}
//echo $midterm;

$sum=$midterm+$final+$sessional;

if($sum < 100){


echo "<font color = red>The sum of the below entered Weightages should be 100%!</font><br />";

}

else{


$rec=$DB->get_records_sql('Select id from mdl_course WHERE shortname NOT LIKE "CIS"');

if($rec){
foreach($rec as $record){

$courseid=$record->id;

$sql1="INSERT INTO mdl_grading_policy (courseid,name,percentage) VALUES ('$courseid','mid term','$midterm')";
$DB->execute($sql1);

$sql2="INSERT INTO mdl_grading_policy (courseid,name,percentage) VALUES ('$courseid','final exam','$final')";
$DB->execute($sql2);
}

$msgP = "<font color = green>Weightage successfully assigned!</font><br />";

}


else{


	echo "<font color = red>No Courses Found!</font><br />";
}
redirect('report_chairman.php');

}

}






if(isset($msgP)){


	echo $msgP;
}




?>


<form method='post' action="" class="mform" id="cloForm">
     <div class="form-group row fitem ">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                    </span>
                    <label class="col-form-label d-inline" for="id_name">
                        Midterm
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
                            class="form-control"
                            name="midterm"
                            id="id_name"
                            size=""
                            required
                            maxlength="100">
                            %
                    <div class="form-control-feedback" id="id_error_name">
                    </div>
                </div>
            </div>

<div class="form-group row fitem ">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                    </span>
                    <label class="col-form-label d-inline" for="id_name">
                        Final
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
                            class="form-control"
                            name="final"
                            id="id_name"
                            size=""
                            required
                            maxlength="100">
                            %
                    <div class="form-control-feedback" id="id_error_name">
                    </div>
                </div>
            </div>
        



<div class="form-group row fitem ">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                    </span>
                    <label class="col-form-label d-inline" for="id_name">
                        Sessional Activities
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
                            class="form-control"
                            name="sessional"
                            id="id_name"
                            size=""
                            required
                            maxlength="100">
                            %
                            (Note: To be Assigned individually by the teacher)
                    <div class="form-control-feedback" id="id_error_name">
                    </div>
                </div>
            </div>







<input class="btn btn-info" type="submit" name="save" value="Save and continue"/>
		<input class="btn btn-info" type="submit" name="return" value="Save and return"/>
		<a class="btn btn-default" type="submit" <?php echo "href='./report_chairman.php'" ?>>Cancel</a>
		</form>




<?php
echo $OUTPUT->footer();

?>