<?php 
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Upload Sample");
    $PAGE->set_heading("Upload Sample");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/grading_form_quiz_selection.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();


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
        public function insertBlob($filePath, $mime) {
            $blob = fopen($filePath, 'rb');
    
            $sql = "INSERT INTO mdl_sample_solution (mime,data) VALUES(:mime,:data)";
            $stmt = $this->pdo->prepare($sql);
    
            $stmt->bindParam(':mime', $mime);
            $stmt->bindParam(':data', $blob, PDO::PARAM_LOB);
    
            return $stmt->execute();
        }


        public function selectBlob($id) {
    
            $sql = "SELECT mime,
                            data
                    FROM mdl_sample_solution
                    WHERE id = :id;";
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(":id" => $id));
            $stmt->bindColumn(1, $mime);
            $stmt->bindColumn(2, $data, PDO::PARAM_LOB);
    
            $stmt->fetch(PDO::FETCH_BOUND);
    
            return array("mime" => $mime,
                "data" => $data);
        }


         function updateBlob($id, $filePath, $mime) {
 
        $blob = fopen($filePath, 'rb');
        //echo "$id";
        $sql = "UPDATE mdl_sample_solution
                SET mime = :mime,
                    data = :data
                WHERE id = :id";
 
        $stmt = $this->pdo->prepare($sql);
 
        $stmt->bindParam(':mime', $mime);
        $stmt->bindParam(':data', $blob, PDO::PARAM_LOB);
        $stmt->bindParam(':id', $id);
 
        return $stmt->execute();
    }



        /**
         * close the database connection
         */
        public function __destruct() {
            // close the database connection
            $this->pdo = null;
        }
    }





    if(isset($_FILES['sampleSolution']) && isset($_POST['Upload']))
    {

        //  var_dump($_FILES['sampleSolution']);
        $file = $_FILES['sampleSolution']['name'];
        $file_loc = $_FILES['sampleSolution']['tmp_name'];
        $file_size = $_FILES['sampleSolution']['size'];
        $file_type = $_FILES['sampleSolution']['type'];

        $mod = $_POST["mod"];
        $instance = $_POST["instance"];
        $name = $_POST['name'];
        $desc = $_POST['description'];


       /* echo "$mod";
        echo "$instance<br/>";
        echo "$name<br/>";
        echo "$desc<br/>";*/

       // echo "$file $file_type";
       // echo "$type";

           // try {
                //$transaction = $DB->start_delegated_transaction();
            if ($file_type == "application/pdf")
             { 
                $record = new stdClass();
                $record->module = $mod;
                $record->instance = $instance;
                $record->name = $name;
                $record->description = $desc;

             $id = $DB->insert_record('sample_solution', $record);


             
                    $blobObj = new Blob($x,$dbh,$dbn,$dbu);
                    $blobObj->updateBlob($id,$file_loc,"application/pdf");
                    echo "<font color = green> File has been Uploaded successfully! </font>";
            }
                else
                    echo "<font color = red >Incorrect File Type. Only PDFs are allowed</font>";
       // } catch(Exception $e) {
               //     $transaction->rollback($e);
          //  }


    }





if(!empty($_GET['quiz']) && !empty($_GET['courseid']) && !empty($_GET['type']))
{
    $course_id=$_GET['courseid'];
    $coursecontext = context_course::instance($course_id);
    is_enrolled($coursecontext, $USER->id) || die('<h3>You are not enrolled in this course!</h3>'.$OUTPUT->footer());

    $quizId = $_GET['quiz'];
    $type = $_GET['type'];

    if ($type == "quiz")
    {
        $mod=-1;
    }
?>

      <form id="uploadSample" method="POST" enctype="multipart/form-data" class="mform">

       <div class="form-group row fitem ">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                    </span>
                    <label class="col-form-label d-inline" for="id_name">
                        Name
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
                            class="form-control"
                            name="name"
                            id="id_name"
                            size=""
                            required
                            maxlength="100">
                    <div class="form-control-feedback" id="id_error_name">
                    </div>
                </div>
            </div>


            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                    </span>
                    <label class="col-form-label d-inline" for="id_description">
                        Description
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="editor">
                    <div>
                        <div>
                            <textarea id="id_description" name="description" class="form-control" rows="4" cols="80" spellcheck="true" maxlength="500"></textarea>
                        </div>
                    </div>
                    <div class="form-control-feedback" id="id_error_description"  style="display: none;">
                    </div>
                </div>
            </div>



        <div class="form-group row fitem ">
                <div class="col-md-3">
                     <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                    </span>
                    <label class="col-form-label d-inline" for="quizQues">
                        Upload Sample Solution
                    </label>
                </div>
                <div class="col-md-9 form-inline felement">
                    <div class="btn btn-default btn-file">
                        <input  type="file" name="sampleSolution" id="sampleSolution" accept="application/pdf" placeholder="Only PDFs are allowed" required>
                    </div>
                    (Only PDFs are allowed)
                </div>
            </div>


            <input type="hidden" name="mod" value="<?php echo $mod; ?>">
            <input type="hidden" name="instance" value="<?php echo $quizId; ?>">

        <input class="btn btn-info" type="submit" name="Upload" value="Upload">
    </form>
    

<?php
}
else 
{
    echo "<font color=red size = 20px> Error </font>";
}