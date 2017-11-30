<style>
table, th, td{
    border: 2px solid #000;
    padding: 5px;
}
</style>

<?php

    require_once('../config.php');
    
    /*
    Queries related to competencies:
    1. Display all Competency Frameworks
    2. Get a view of all competencies in framework
    3. Get a view of PEOs
    4. Get a view of PLOs
    5. Get a view of COs
    6. Get a view of SE course competencies
    7. Get a view of SE Quiz 1 competencies
    */

    echo "<br><br>COMPETENCY FRAMEWORKS";
    // Display all CFs
    $rec=$DB->get_records_sql('SELECT * FROM  `mdl_competency_framework`');
    $table = new html_table();
    $table->head = array('ID','ShortName', 'IDNumber');
    foreach ($rec as $records) {
        $id = $records->id;
        $sname = $records->shortname;
        $idnum = $records->idnumber;
        $table->data[] = array($id, $sname, $idnum);
    }
    echo html_writer::table($table);


    
    
    echo "<br><br>COMPETENCIES OF COMPETENCY FRAMEWORK";
    //Get a view of competencies
    $rec=$DB->get_records_sql('SELECT *
    
        FROM mdl_competency

        WHERE competencyframeworkid = ?', array(2));
    
    $table = new html_table();
    $table->head = array('ID','ShortName', 'IDNumber', 'CompetencyFrameworkID', 'ParentID');
    foreach ($rec as $records) {
        $id = $records->id;
        $sname = $records->shortname;
        $idnum = $records->idnumber;
        $cfid = $records->competencyframeworkid;
        $pid = $records->parentid;
        $table->data[] = array($id, $sname, $idnum, $cfid, $pid);
    }
    echo html_writer::table($table);


    
    echo "<br><br>PEOs";
    //Get a view of PEOs
    $rec=$DB->get_records_sql("SELECT *
    
        FROM mdl_competency

        WHERE competencyframeworkid = ? AND idnumber LIKE 'peo%'", array(2));
    
    $table = new html_table();
    $table->head = array('ID','ShortName', 'IDNumber', 'CompetencyFrameworkID', 'ParentID');
    foreach ($rec as $records) {
        $id = $records->id;
        $sname = $records->shortname;
        $idnum = $records->idnumber;
        $cfid = $records->competencyframeworkid;
        $pid = $records->parentid;
        $table->data[] = array($id, $sname, $idnum, $cfid, $pid);
    }
    echo html_writer::table($table);



    echo "<br><br>PLOs";
    //Get a view of PLOs
    $rec=$DB->get_records_sql("SELECT *
    
        FROM mdl_competency

        WHERE competencyframeworkid = ? AND idnumber LIKE 'plo%'", array(2));
    
    $table = new html_table();
    $table->head = array('ID','ShortName', 'IDNumber', 'CompetencyFrameworkID', 'ParentID');
    foreach ($rec as $records) {
        $id = $records->id;
        $sname = $records->shortname;
        $idnum = $records->idnumber;
        $cfid = $records->competencyframeworkid;
        $pid = $records->parentid;
        $table->data[] = array($id, $sname, $idnum, $cfid, $pid);
    }
    echo html_writer::table($table);



    echo "<br><br>COs";
    //Get a view of COs
    $rec=$DB->get_records_sql("SELECT *
    
        FROM mdl_competency

        WHERE competencyframeworkid = ? AND idnumber NOT LIKE 'plo%' AND idnumber NOT LIKE 'peo%'", array(2));
    
    $table = new html_table();
    $table->head = array('ID','ShortName', 'IDNumber', 'CompetencyFrameworkID', 'ParentID');
    foreach ($rec as $records) {
        $id = $records->id;
        $sname = $records->shortname;
        $idnum = $records->idnumber;
        $cfid = $records->competencyframeworkid;
        $pid = $records->parentid;
        $table->data[] = array($id, $sname, $idnum, $cfid, $pid);
    }
    echo html_writer::table($table);



    echo "<br><br>View Course Competencies of SE";
    //Get a view of SE comp
    $rec=$DB->get_records_sql("SELECT c.shortname
    
        FROM mdl_competency_coursecomp cc, mdl_competency c

        WHERE cc.courseid = ? AND cc.competencyid = c.id",
        
        array(4));
    
    $table = new html_table();
    $table->head = array('Competencies');
    foreach ($rec as $records) {
        $sname = $records->shortname;
        $table->data[] = array($sname);
    }
    echo html_writer::table($table);




    echo "<br><br>View Quiz 1 Competencies of SE";
    //Get a view of SE Quiz 1 comp
    $rec=$DB->get_records_sql("SELECT c.shortname
    
        FROM mdl_modules m, mdl_course_modules cm, mdl_competency_modulecomp mc, mdl_competency c

        WHERE cm.course = ? AND m.id = cm.module AND m.name LIKE 'quiz' AND instance = ? AND cm.id = mc.cmid AND mc.competencyid = c.id",
        
        array(4, 2)); // 4 course id, 16 for quiz module, 2 quiz id
    
    $table = new html_table();
    $table->head = array('Competencies');
    foreach ($rec as $records) {
        $sname = $records->shortname;
        $table->data[] = array($sname);
    }
    echo html_writer::table($table);


    /*
    echo "<br><br>Course Modules";
    //Course Modules of SE
    global $DB;
    $courseid = 4;
    $course = $DB->get_record('course', array('id' => $courseid));
    $info = get_fast_modinfo($course);
    print_object($info);
    */

    $data = array();
    array_push($data,1,2,3,4,5);
    $arrlength = count($data);
    for($x = 0; $x < $arrlength; $x++) {
        echo $data[$x];
        echo "<br>";
    }

    echo "<br>";
    $data2 = array();
    for($x = 0; $x < 2; $x++) {
        $data2[$x] = 0;
    }
    $data2[0]++;
    $data2[1]++;
    $data2[0]++;
    $data2[1]++;
    $arrlength = count($data2);
    for($x = 0; $x < $arrlength; $x++) {
        echo $data2[$x];
        echo "<br>";
    }

    echo "<br>";
    $cars = array("Volvo", "BMW", "Toyota");
    $arrlength = count($cars);
    
    for($x = 0; $x < $arrlength; $x++) {
        echo $cars[$x];
        echo "<br>";
    }


?>

<script type="text/javascript">
    var a = <?php echo json_encode($data); ?>;
    //alert(a);
    console.log(a);
</script>