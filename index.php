<?php
    require_once('../../config.php');
    $PAGE->set_pagelayout('redirect');
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/index.php');
    require_login();
    global $SESSION;

    $redirect_page1='./admin/report_admin.php';
    $redirect_page2='./teacher/teacher_courses.php';
    $redirect_page3='./student/report_student.php';
    $redirect_page4='./chairman/report_chairman.php';
    $redirect_page5='./itm/report_itm.php';
    $redirect_page6='./noneditingteacher/report_noneditingteacher.php';

    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id)); // for Chairman

    $rec2=$DB->get_records_sql('SELECT us.username from mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND r.shortname=? AND us.id=?',array('itm',$USER->id)); // for itm

    $rec3=$DB->get_records_sql('SELECT us.username from mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND r.shortname=? AND us.id=?',array('teacher',$USER->id)); //for non editing teacher
    
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
      redirect($redirect_page1); // admin
    }
    elseif($rec){
      $SESSION->oberole = "teacher";
      redirect($redirect_page2); // teacher
    }
    elseif($rec1){
      $SESSION->oberole = "chairman";
      redirect($redirect_page4); // chairman   
    }
    elseif($rec2){
      $SESSION->oberole = "itm";
      redirect($redirect_page5); // itm
    }
    elseif($rec3){
      $SESSION->oberole = "teacher";
      redirect($redirect_page6); // nonediting teacher
    }
    else{
      $SESSION->oberole = "student";
      redirect($redirect_page3); // student
    }
?>
