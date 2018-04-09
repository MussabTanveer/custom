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

    $mysqli = new mysqli($dbh, $dbu, $dbp, $dbn);
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            echo "EXIT";
            exit();
          }

	      $universityVision=$mysqli->real_escape_string($universityVision);
        $universityMission = $mysqli->real_escape_string($universityMission);
        $departmentVision = $mysqli->real_escape_string($departmentVision);
        $departmentMission = $mysqli->real_escape_string($departmentMission);
        $departmentName = $mysqli->real_escape_string($departmentName);
        $UniversityName =  $mysqli->real_escape_string($UniversityName);


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


              $sql="INSERT INTO  mdl_vision_mission (name,idnumber,description,revision) VALUES ('department name','dn','$departmentName','$rev')";

                $DB->execute($sql);
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


              $sql="INSERT INTO  mdl_vision_mission (name,idnumber,description,revision) VALUES ('university name','un','$UniversityName','$rev')";

                $DB->execute($sql);
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


              $sql="INSERT INTO  mdl_vision_mission (name,idnumber,description,revision) VALUES ('university vision','uv','$universityVision','$rev')";

                $DB->execute($sql);
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

        $sql="INSERT INTO  mdl_vision_mission (name,idnumber,description,revision) VALUES ('university mission','um','$universityMission','$rev')";
        $DB->execute($sql);

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


        $sql="INSERT INTO  mdl_vision_mission (name,idnumber,description,revision) VALUES ('department vision','dv','$departmentVision','$rev')";
        $DB->execute($sql);
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

        $sql="INSERT INTO  mdl_vision_mission (name,idnumber,description,revision) VALUES ('department mission','dm','$departmentMission','$rev')";
            
        $DB->execute($sql);

        }

?>
