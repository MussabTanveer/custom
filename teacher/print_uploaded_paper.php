<?php 
   require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Print Quiz paper");
    $PAGE->set_heading("Print Quiz paper");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/print_quiz1.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
   // echo $OUTPUT->header();

    global $CFG;
    $x= $CFG->dbpass;
    $dbh = $CFG->dbhost;
    $dbn = $CFG->dbname;
    $dbu = $CFG->dbuser;
   
    class Blob{
    
     protected $DB_HOST = '';
    protected $DB_NAME = '';
    protected $DB_USER = '';
    protected $DB_PASSWORD='';
 
    /**
     * Open the database connection
     */
    public function __construct($x,$dbh,$dbn,$dbu) {

     $DB_HOST=$dbh;
      $DB_NAME = $dbn;
      $DB_USER = $dbu;
      $DB_PASSWORD=$x;
       
        // open database connection
        $conStr = sprintf("mysql:host=%s;dbname=%s;charset=utf8", $DB_HOST, $DB_NAME);
 
        try {
            $this->pdo = new PDO($conStr,  $DB_USER, $DB_PASSWORD);
            //for prior PHP 5.3.6
            //$conn->exec("set names utf8");
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
 
public function selectBlob($id) {
 
        $sql = "SELECT mime,
                        data
                   FROM mdl_manual_quiz
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

if(!empty($_GET['quiz']))
    {
        $quizid=$_GET['quiz'];
       
       //displaying pdf
        $blobObj = new Blob($x,$dbh,$dbn,$dbu);
        $a = $blobObj->selectBlob($quizid);
        header("Content-Type:" . $a['mime']);
        echo $a['data'];
    }