<?php
    require_once('../../config.php');
    $PAGE->set_pagelayout('redirect');
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/index.php');
    require_login();
    $redirect_page1='./admin/report_admin.php';
    $redirect_page2='./teacher/report_teacher.php';
    $redirect_page3='./student/report_student.php';
    $redirect_page4='./chairman/report_chairman.php';
  

    $rec1=$DB->get_records_sql('SELECT us.username from mdl_user us, mdl_role r WHERE r.shortname=?',array('chairman'));
    $rec=$DB->get_records_sql('SELECT c.id, c.fullname, c.shortname, c.idnumber
  
      FROM mdl_course c
  
      INNER JOIN mdl_context cx ON c.id = cx.instanceid
  
      AND cx.contextlevel = ?
  
      INNER JOIN mdl_role_assignments ra ON cx.id = ra.contextid
  
      INNER JOIN mdl_role r ON ra.roleid = r.id
  
      INNER JOIN mdl_user usr ON ra.userid = usr.id
  
      WHERE r.shortname = ?
  
      AND usr.id = ?', array('50', 'editingteacher', $USER->id));
  
    if(is_siteadmin()){
      //echo 'admin';
      redirect($redirect_page1);
    }
    elseif($rec){
      //echo 'teacher';
      redirect($redirect_page2);
    }
    elseif($rec1){
     
redirect($redirect_page4); #chairman
       
    }
    else{
      //echo 'student';
      redirect($redirect_page3);	  
	  }
?>
