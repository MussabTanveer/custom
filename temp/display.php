<style>
table, th, td{
    border: 2px solid #000;
    padding: 5px;
}
</style>

<?php

require_once('../../../config.php');
    //echo "hello1";
    //$query = mysql_query("SELECT * FROM `mdl_competency_framework`");
    /*$query = $DB->get_record_sql('SELECT * FROM {competency_framework}');
    if($query === FALSE) { 
        echo "ERRRRROR";
        die(mysql_error()); // TODO: better error handling
    }
    echo $query[1]->id;
    
    while($result=mysql_fetch_array($query))
    {
        echo "hello2";
        echo $result['id'];
        echo $result['shortname'];
    }
    */
    $foo = $DB->get_records('competency_framework',array());
    //$foo = get_records('competency_framework','id',1);

    // using foreach loops
    foreach($foo as $object) {
        $ids[] = $object->id;
        $snames[] = $object->shortname;
    }
    foreach($ids as $id){
        echo $id;
    }
    foreach($snames as $sname){
        echo $sname;
    }
    echo "<br>";
    print_r($foo[1]->id);
    echo ($foo[1]->shortname);

    echo "<br>";
    /* EXAMPLE:
    $rec=$DB->get_records_sql('SELECT * FROM  `mdl_schedule`');
    $table = new html_table();
    $table->head = array('Date','Time', 'Session' , 'Venue', 'Trainer','Category', 'Course', 'Link');
    foreach ($rec as $records) {
        $id = $records->id;
        $scheduledatet = $records->scheduledate;
        $scheduletime = $records->scheduletime;
        $session = $records->session;
        $venue = $records->venue;
        $trainer = $records->trainer;
        $category = $records->category;
        $course = $records->course;
        $link = $records->link;
        $table->data[] = array($scheduledatet, $scheduletime, $session,$venue,$trainer,$category,$course,'<a href="'.$link.'">View</a>');
    }
    echo html_writer::table($table);
    */

    // Dispaly all users
    echo "<br>ALL USERS";
    $rec=$DB->get_records_sql('SELECT * FROM  `mdl_user`');
    $table = new html_table();
    $table->head = array('ID','UserName', 'Password' , 'FirstName', 'LastName','Email');
    foreach ($rec as $records) {
        $id = $records->id;
        $uname = $records->username;
        $pwd = $records->password;
        $fname = $records->firstname;
        $lname = $records->lastname;
        $email = $records->email;
        $table->data[] = array($id, $uname, $pwd, $fname, $lname, $email);
    }
    echo html_writer::table($table);

    echo "<br>Current User ID: ";
    echo $USER->id;
    //echo "\nCurrent Course ID: ";
    //echo $COURSE->id;

    echo "<br><br>ALL COURSES";
    // Dispaly all courses
    $rec=$DB->get_records_sql('SELECT * FROM  `mdl_course` WHERE startdate != ? AND visible = ?', array( 0 , 1 ));
    $table = new html_table();
    $table->head = array('ID','FullName', 'ShortName' , 'IDNumber');
    foreach ($rec as $records) {
        $id = $records->id;
        $fname = $records->fullname;
        $sname = $records->shortname;
        $idnum = $records->idnumber;
        $table->data[] = array($id, $fname, $sname, $idnum);
    }
    echo html_writer::table($table);

    echo "<br><br>ALL QUIZZES OF ALL COURSES";
    // Dispaly all quizzes
    $rec=$DB->get_records_sql('SELECT * FROM  `mdl_quiz` WHERE timeopen != ?', array( 0));
    $table = new html_table();
    $table->head = array('ID', 'Course', 'Name', 'Intro');
    foreach ($rec as $records) {
        $id = $records->id;
        $courseid = $records->course;
        $name = $records->name;
        $intro = $records->intro;
        $table->data[] = array($id, $courseid, $name, $intro);
    }
    echo html_writer::table($table);

    //Get list of courses for given editingteacher
    /*
    SELECT usr.firstname, usr.lastname, c.id, c.fullname, c.shortname, c.idnumber, r.shortname

    FROM mdl_course c

    INNER JOIN mdl_context cx ON c.id = cx.instanceid

    AND cx.contextlevel = '50'

    INNER JOIN mdl_role_assignments ra ON cx.id = ra.contextid

    INNER JOIN mdl_role r ON ra.roleid = r.id

    INNER JOIN mdl_user usr ON ra.userid = usr.id

    WHERE r.shortname = 'editingteacher'

    AND usr.id = 3

    ORDER BY usr.firstname, c.shortname 
    */

    echo "<br><br>ALL COURSES OF A USER WHERE HIS ROLE IS EDITINGTEACHER";
    //Get list of courses for given editingteacher
    $rec=$DB->get_records_sql('SELECT c.id, c.fullname, c.shortname, c.idnumber
    
        FROM mdl_course c
    
        INNER JOIN mdl_context cx ON c.id = cx.instanceid
    
        AND cx.contextlevel = ?
    
        INNER JOIN mdl_role_assignments ra ON cx.id = ra.contextid
    
        INNER JOIN mdl_role r ON ra.roleid = r.id
    
        INNER JOIN mdl_user usr ON ra.userid = usr.id
    
        WHERE r.shortname = ?
    
        AND usr.id = ?', array('50', 'editingteacher', $USER->id));
    
    $table = new html_table();
    $table->head = array('CourseID', 'CourseFullName', 'CourseShortName', 'CourseCode');
    foreach ($rec as $records) {
        $cid = $records->id;
        $cfname = $records->fullname;
        $csname = $records->shortname;
        $cidnum = $records->idnumber;
        $table->data[] = array($cid, $cfname, $csname, $cidnum);
    }
    echo html_writer::table($table);


?>