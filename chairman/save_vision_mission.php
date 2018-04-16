<?php
	require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Vision & Mission");
    $PAGE->set_heading("Define Vision & Mission");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/save_vision_mission.php');
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
    
	$universityVision = trim($_POST["uv"]);
	$universityMission = trim($_POST["um"]);
	$departmentVision = trim($_POST["dv"]);
	$departmentMission = trim($_POST["dm"]);
  $departmentName = trim($_POST["departName"]);
  $UniversityName = trim($_POST["uniName"]);


	/*echo "$universityVision <br>";
	echo "$universityMission <br>";
	echo "$departmentVision <br>";
	echo "$departmentMission <br> ";*/
  global $CFG;
    $dbp= $CFG->dbpass;
    $dbh = $CFG->dbhost;
    $dbn = $CFG->dbname;
    $dbu = $CFG->dbuser;

    if($departmentName != "")
        {

           $revisions=$DB->get_records_sql('SELECT revision FROM `mdl_vision_mission` where idnumber = ?', array('dn'));

              $rev=0;
               if($revisions){
            foreach ($revisions as $revision){
                $rev = $revision->revision; 
            }
        }
                $rev++;

            $record = new stdclass();
            $record->name='department name';
            $record->idnumber = 'dn';
            $record->description=$departmentName;
            $record->revision=$rev;
            $insert = $DB->insert_record('vision_mission', $record);

        }


     if($UniversityName != "")
        {

           $revisions=$DB->get_records_sql('SELECT revision FROM `mdl_vision_mission` where idnumber = ?', array('un'));

              $rev=0;
               if($revisions){
            foreach ($revisions as $revision){
                $rev = $revision->revision; 
            }
        }
                $rev++;

            $record = new stdclass();
            $record->name='university name';
            $record->idnumber = 'un';
            $record->description=$UniversityName;
            $record->revision=$rev;
            $insert = $DB->insert_record('vision_mission', $record);
        }

  

	if($universityVision != "")
        {

           $revisions=$DB->get_records_sql('SELECT revision FROM `mdl_vision_mission` where idnumber = ?', array('uv'));

              $rev=0;
               if($revisions){
            foreach ($revisions as $revision){
                $rev = $revision->revision; 
            }
        }
                $rev++;

            $record = new stdclass();
            $record->name='university vision';
            $record->idnumber = 'uv';
            $record->description=$universityVision;
            $record->revision=$rev;
            $insert = $DB->insert_record('vision_mission', $record);
        }


        if($universityMission != "")
        {

         $revisions=$DB->get_records_sql('SELECT revision FROM `mdl_vision_mission` where idnumber = ?', array('um'));

              $rev=0;
               if($revisions){
            foreach ($revisions as $revision){
                $rev = $revision->revision; 
            }
        }
                $rev++;

            $record = new stdclass();
            $record->name='university mission';
            $record->idnumber = 'um';
            $record->description=$universityMission;
            $record->revision=$rev;
            $insert = $DB->insert_record('vision_mission', $record);

        }


        if($departmentVision != "")
        {

        $revisions=$DB->get_records_sql('SELECT revision FROM `mdl_vision_mission` where idnumber = ?', array('dv'));

              $rev=0;
               if($revisions){
            foreach ($revisions as $revision){
                $rev = $revision->revision; 
            }
        }
                $rev++;

            $record = new stdclass();
            $record->name='department vision';
            $record->idnumber = 'dv';
            $record->description=$departmentVision;
            $record->revision=$rev;
            $insert = $DB->insert_record('vision_mission', $record);
         }


         if($departmentMission != "")
        {
          $revisions=$DB->get_records_sql('SELECT revision FROM `mdl_vision_mission` where idnumber = ?', array('dm'));

              $rev=0;
               if($revisions){
            foreach ($revisions as $revision){
                $rev = $revision->revision; 
            }
        }
                $rev++;

            $record = new stdclass();
            $record->name='department mission';
            $record->idnumber = 'dm';
            $record->description=$departmentMission;
            $record->revision=$rev;
            $insert = $DB->insert_record('vision_mission', $record);

        }

?>
