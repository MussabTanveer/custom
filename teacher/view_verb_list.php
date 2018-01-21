<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("View Verb List");
    $PAGE->set_heading("View Verb List");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/view_verb_list.php');
   
    require_login();

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
                   FROM mdl_verb_list
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

$query=$DB->get_records_sql('SELECT DISTINCT r.shortname FROM  mdl_role r , 

          mdl_role_assignments ra 
         WHERE (ra.userid= ? AND ra.roleid=r.id AND r.shortname= ?)', array($USER->id, 'editingteacher'));

        if($query || is_siteadmin())
        {
            echo "Teacher";
        
        



		 $recs=$DB->get_records_sql('SELECT * FROM mdl_verb_list');

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
			echo "<font size =8, color=red>Verb List hasn't uploaded yet!</font>";
            
            echo $OUTPUT->footer();
        }
		
	

                //displaying pdf
            $blobObj = new Blob($x);
            $a = $blobObj->selectBlob($id);
            header("Content-Type:" . $a['mime']);
              echo $a['data'];
    }
    else
    {           
        echo $OUTPUT->header();
            
            echo "<font size =5> This page is for Admin OR Teachers Only!</font>";
             echo $OUTPUT->footer();

        }