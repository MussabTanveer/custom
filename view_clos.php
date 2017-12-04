<?php
    require_once('../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("View OBE CLOs");
    $PAGE->set_heading("Course Learning Outcomes (CLOs)");
    $PAGE->set_url($CFG->wwwroot.'/custom/view_clos.php');
    
    echo $OUTPUT->header();
	require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());
	
	if(isset($_GET['fwid']))
	{
        $fw_id=$_GET['fwid'];

        $clos=$DB->get_records_sql('SELECT * FROM `mdl_competency` WHERE competencyframeworkid = ? AND idnumber LIKE "%%-%%%-clo%"', array($fw_id));
        
        if($clos){
            $i = 1;
            echo "<h3>Already Present CLOs In Framework</h3>";
            foreach ($clos as $records){
                $shortname1 = $records->shortname;
                $id=$records->id;
                echo "<div class='row'><div class='col-md-2 col-sm-4 col-xs-8'>$i. $shortname1</div> <div class='col-md-10 col-sm-8 col-xs-4'><a href='edit_clo.php?edit=$id&fwid=$fw_id' title='Edit'><img src='./img/icons/edit.png' /></a> <a href='delete_clo.php?delete=$id&fwid=$fw_id' title='Delete'><img src='./img/icons/delete.png' /></a></div></div>";//link to edit_plo.php 
                $i++;
            }
        }
        else{
            echo "<h3>No CLOs in framework found!</h3>";
        }
        echo $OUTPUT->footer();
	}
	else
	{?>
    	<h3 style="color:red;"> Invalid Selection </h3>
    	<a href="./select_frameworktoCLO.php">Back</a>
    	<?php
        echo $OUTPUT->footer();
    }?>
