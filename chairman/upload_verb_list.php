<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Upload Verb List");
    $PAGE->set_heading("Upload Verb List");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/upload_verb_list.php');
    
    echo $OUTPUT->header();
	require_login();
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());

    ?>

    <form id="uploadVerb" method="POST" enctype="multipart/form-data" class="mform">

         <div class="btn btn-default btn-file">
            
            <input  type="file" name="verbList" id="verbList" placeholder="Only PDFs are allowed">
        </div>
            <input class="btn btn-info" type="submit" name="Upload" value="Upload">
        

    </form>

    <?php
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
            $this->pdo = new PDO($conStr, $DB_USER, $DB_PASSWORD);
            //for prior PHP 5.3.6
            //$conn->exec("set names utf8");
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
 
 /**
     * insert blob into the files table
     * @param string $filePath
     * @param string $mime mimetype
     * @return bool
     */
    public function insertBlob($filePath, $mime,$rev) {
        $blob = fopen($filePath, 'rb');
 
        $sql = "INSERT INTO mdl_verb_list (mime,data,revision) VALUES(:mime,:data,'$rev')";
        $stmt = $this->pdo->prepare($sql);
 
        $stmt->bindParam(':mime', $mime);
        $stmt->bindParam(':data', $blob, PDO::PARAM_LOB);
 
        return $stmt->execute();
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

if(isset($_POST['Upload']))
{

   
    $file = $_FILES['verbList']['name'];
    $file_loc = $_FILES['verbList']['tmp_name'];
    $file_size = $_FILES['verbList']['size'];
    $file_type = $_FILES['verbList']['type'];

   
     $revisions=$DB->get_records_sql('SELECT * FROM mdl_verb_list');
           $rev=0;
               if($revisions){
            foreach ($revisions as $revision){
                $rev = $revision->id; 
            }
        }
        $rev++;
       
    if ($file_type == "application/pdf")
       { 
           $blobObj = new Blob($x,$dbh,$dbn,$dbu);
              $blobObj->insertBlob($file_loc,"application/pdf",$rev);
              echo "<font color = green> File has been Uploaded successfully! </font>";
        }
        else
            echo "<font color = red >Incorrect File Type. Only PDFs are allowed</font>";
      
}
   echo $OUTPUT->footer();
 ?>