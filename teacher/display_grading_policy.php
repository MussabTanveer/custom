<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Display Grading Policy");
    $PAGE->set_heading("Display Grading Policy");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/display_grading_policy.php');
    echo $OUTPUT->header();
    require_login();


?>
 <style>
        h3{
            text-decoration: underline;
        }
        .wrapper{
            text-align: center;
        }
        .effect{
            font-size: 1.2em;
        }
    </style>
 
<?php
if(isset($_GET['course'])){

$course_id=$_GET['course'];


//echo $course_id;

$rec=$DB->get_recordset_sql(
	'SELECT percentage FROM mdl_grading_policy WHERE name = ? and courseid= ? ', array('mid-terms',$course_id) );
$rec1=$DB->get_recordset_sql(
	'SELECT percentage FROM mdl_grading_policy WHERE name = ? and courseid= ? ', array('finals',$course_id) );
$rec2=$DB->get_recordset_sql(
	'SELECT percentage FROM mdl_grading_policy WHERE name = ? and courseid= ? ', array('others',$course_id) );


?>
<div class="wrapper">


   <h3>Mid-terms</h3><br />
   <?php
    if($rec){
        foreach($rec as $midterm){

        	$mid=$midterm->percentage;

?>

<?php
if(!empty($mid))
                echo "<div class='row'><div class='col-md-2'></div><div class='col-md-8 effect'><b><i> $mid %</i></b></div><div class='col-md-2'></div></div><br />";

else
    echo "<div class='row'><div class='col-md-2'></div><div class='col-md-8'><p><b>Not available</b></p></div><div class='col-md-2'></div></div><br />";

}



}




?>
<h3>Finals</h3><br />
<?php
 if($rec1){
        foreach($rec1 as $final){

        	$final=$final->percentage;

?>

<?php
if(!empty($mid))
                echo "<div class='row'><div class='col-md-2'></div><div class='col-md-8 effect'><b><i> $final %</i></b></div><div class='col-md-2'></div></div><br />";
else
    echo "<div class='row'><div class='col-md-2'></div><div class='col-md-8'><p><b>Not available</b></p></div><div class='col-md-2'></div></div><br />";



}



}
?>

<h3>Others</h3><br />
<?php
 if($rec2){
        foreach($rec2 as $others){

        	$other=$others->percentage;

?>

<?php
if(!empty($other))
                echo "<div class='row'><div class='col-md-2'></div><div class='col-md-8 effect'><b><i> $other %</i></b></div><div class='col-md-2'></div></div><br />";

else
    echo "<div class='row'><div class='col-md-2'></div><div class='col-md-8'><p><b>Not available</b></p></div><div class='col-md-2'></div></div><br />";

}



}









}

?>
</div>

<?php

echo $OUTPUT->footer();

?>









