  <?php
    require_once('../config.php');
  $redirect_page='report_admin.php';
  $redirect_page1='report_teacher.php';
  $redirect_page2='report_student.php';
  
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
	  
	  header('Location:'.$redirect_page);
	  
	  
  }
  
  elseif($rec){
	  
	  //echo 'teacher';
    header('Location:'.$redirect_page1);
	  }
  
  
  
  
  else{
	  
	  //echo 'student';

     header('Location:'.$redirect_page2);

	  
	  }
  ?>