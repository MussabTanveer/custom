<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("PLO Report");
    $PAGE->set_heading("PLO Report");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/plo_report.php');
    
    echo $OUTPUT->header();
    require_login();
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
    
    if(isset($_POST['plosid'])  )
    {
        $Ploid = $_POST['plosid'];
        echo "$Ploid";
        $batchID = $_POST['batchID'];
        echo "<br/>$batchID";

        // Report Header (Uni. name, Dept. name, course code and title)
        $un=$DB->get_records_sql('SELECT * FROM  `mdl_vision_mission` WHERE idnumber = ?', array("un"));
        if($un){
            foreach($un as $u){
                $uniName = $u->description;
            }
            $uniName = strip_tags($uniName); 
            echo "<h3 style='text-align:center'>".strtoupper($uniName)."</h3>";         
        }
        $dn=$DB->get_records_sql('SELECT * FROM  `mdl_vision_mission` WHERE idnumber = ?', array("dn"));
        if($dn){
            foreach($dn as $d){
                $deptName = $d->description;
            }
            $deptName = strip_tags($deptName); 
            echo "<h3 style='text-align:center'>".strtoupper($deptName)."</h3>";         
        }
         $batch=$DB->get_records_sql('SELECT * FROM  `mdl_batch` WHERE id = ?', array("$batchID"));
        if($batch)
        {
            foreach($batch as $b){
                $batchName = $b->name;
            }
            ?> <h4 style=text-align:center> Batch <?php echo $batchName; ?></h4>         
        <?php
        }

         $Plo=$DB->get_records_sql('SELECT * FROM  `mdl_competency` WHERE id = ?', array("$Ploid"));
        if($Plo){
            foreach($Plo as $p){
                $PloName = $p->idnumber;
            }
            ?> <h4 style=text-align:center> OBE <b><?php echo $PloName; ?></b> Assessment Sheet (Based on CLO Assessment)</h4>         
        <?php
        }
        
        


        echo $OUTPUT->footer();
    }
    else
    {   
        echo "<font color=red> Something went wrong :( </font>";
        echo $OUTPUT->footer();
    }
