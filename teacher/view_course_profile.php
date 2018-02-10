<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("View Course Profile");
    $PAGE->set_heading("View Course Profile");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/view_course_profile.php');
   
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }

    global $CFG;
    $x= $CFG->dbpass;

  

class Blob{
    
    const DB_HOST = 'localhost';
    const DB_NAME = 'bitnami_moodle';
    const DB_USER = 'bn_moodle';
    protected $DB_PASSWORD='';
 
    /**
     * Open the database connection
     */
    public function __construct($x) {
        //echo "$x";
        $DB_PASSWORD=$x;
        // open database connection
        $conStr = sprintf("mysql:host=%s;dbname=%s;charset=utf8", self::DB_HOST, self::DB_NAME);
 
        try {
            $this->pdo = new PDO($conStr, self::DB_USER, $DB_PASSWORD);
            //for prior PHP 5.3.6
            //$conn->exec("set names utf8");
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
 
public function selectBlob($id) {
 
        $sql = "SELECT mime,
                        data
                   FROM mdl_course_profile
                  WHERE id = :id;";
 
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array(":id" => $id));
        $stmt->bindColumn(1, $mime);
        $stmt->bindColumn(2, $data, PDO::PARAM_LOB);
 
        $stmt->fetch(PDO::FETCH_BOUND);
 
        return array("mime" => $mime,
            "data" => $data);
    }
    /**
     * close the database connection
     */
    public function __destruct() {
        // close the database connection
        $this->pdo = null;
    }

}

 if(!empty($_GET['course']))
    {
        $course_id=$_GET['course'];
        $coursecontext = context_course::instance($course_id);
        is_enrolled($coursecontext, $USER->id) || die($OUTPUT->header().'<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());
        
		$recs=$DB->get_records_sql('SELECT idnumber FROM mdl_course 
	 	WHERE id = ?', array($course_id));

		 foreach ($recs as $rec)
		 {
				$idnumber =  $rec->idnumber;
				//echo "$idnumber";	
		}
	

		 $recs=$DB->get_records_sql('SELECT * FROM mdl_course_profile 
	 	WHERE coursecode = ?', array($idnumber));

		 if($recs)
		 {
		 	foreach ($recs as $rec)
		 	{
				$id =  $rec->id;
				
			}
		}
		else
        {
            echo $OUTPUT->header();
			echo "<font color=red size=5>Course profile hasn't uploaded yet!</font>";
            echo $OUTPUT->footer();
        }
		
    }
    else{
        echo $OUTPUT->header();
        ?>
            <h2 style="color:red;"> Invalid Selection </h2>
            <a href="./teacher_courses.php">Back</a>
        <?php
        echo $OUTPUT->footer();
    }

//displaying pdf
$blobObj = new Blob($x);
$a = $blobObj->selectBlob($id);
header("Content-Type:" . $a['mime']);
echo $a['data'];
