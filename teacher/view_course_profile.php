<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("View Course Profile");
    $PAGE->set_heading("View Course Profile");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/view_course_profile.php');
   
    require_login();

  

class Blob {
 
    const DB_HOST = 'localhost';
    const DB_NAME = 'bitnami_moodle';
    const DB_USER = 'bn_moodle';
    const DB_PASSWORD = '274001b456';
    /**
     * Open the database connection
     */
    public function __construct() {
        // open database connection
        $conStr = sprintf("mysql:host=%s;dbname=%s;charset=utf8", self::DB_HOST, self::DB_NAME);
 
        try {
            $this->pdo = new PDO($conStr, self::DB_USER, self::DB_PASSWORD);
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

 if(isset($_GET['course']))
    {
        $course_id=$_GET['course'];
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

//displaying pdf
$blobObj = new Blob();
$a = $blobObj->selectBlob($id);
header("Content-Type:" . $a['mime']);
echo $a['data'];
