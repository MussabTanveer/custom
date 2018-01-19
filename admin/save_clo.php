<?php
	require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Add OBE CLOs");
    $PAGE->set_heading("Add Course Learning Outcome (CLO)");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/admin/add_clo.php');


    $coursecode = trim($_POST["idnumber"]); $coursecode=strtoupper($coursecode);
	$frameworkid = $_POST["frameworkid"];



    if($_FILES['myfile']['size'] > 0)
    {

    	$revisions=$DB->get_records_sql('SELECT revision FROM `mdl_course_profile` where coursecode = ?', array($coursecode));

			  $rev=0;
			   if($revisions){
            foreach ($revisions as $revision){
				$rev = $revision->revision; 
            }
        }
        		$rev++;

     	$file_name = $_FILES["myfile"]['name'];
	 	$file_loc = $_FILES["myfile"]['tmp_name'];
		$file_size = $_FILES["myfile"]['size'];
		$file_type = $_FILES["myfile"]['type'];
		

		if ($file_type == "application/pdf")
		{   
			  $blobObj = new Blob();
			  //test insert pdf
			  $blobObj->insertBlob($file_loc,"application/pdf",$coursecode,$rev);
			  	echo "<font color = green>Course Profile Updated sucessfully!</font><br>";
		 }
		  else
			   echo "Incorrect File Type. Only PDFs are allowed";
    }
    	
    	
	for ($i=0; $i <count($_POST["shortname"]) ; $i++) {
		# code...
		$shortname=trim($_POST["shortname"][$i]); $shortname=strtoupper($shortname);
		$idnumber=$coursecode."-".$shortname; $idnumber=strtoupper($idnumber);
		$description=trim($_POST["description"][$i]);
		//echo $idnumber. "<br>";
		//echo $shortname . "<br>";
		//echo $description . "<br>";
		$time = time();

		if($shortname == "")
		{
			goto down;
		}


		$cloidnumbers=$DB->get_records_sql('SELECT * FROM  `mdl_competency` 
    		WHERE competencyframeworkid = ? AND idnumber = ?',
    		 array($frameworkid,$idnumber));

		if($cloidnumbers == NULL) 
		{
			$sql="INSERT INTO mdl_competency (shortname, description, descriptionformat, idnumber, competencyframeworkid, parentid, path, sortorder, timecreated, timemodified, usermodified) VALUES ('$shortname', '$description', 1, '$idnumber',$frameworkid ,-2, '/0/', 0, '$time', '$time', $USER->id)";
			$DB->execute($sql);
			echo "<font color = green>".$idnumber . " sucessfully defined</font><br>";
		}
		else
			echo "<font color = red>".$idnumber . " already exists</font><br>";
		down:
	}

?>


<?php
class Blob{
 
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
 
 /**
     * insert blob into the files table
     * @param string $filePath
     * @param string $mime mimetype
     * @return bool
     */
    public function insertBlob($filePath, $mime,$coursecode,$rev) {
        $blob = fopen($filePath, 'rb');
       // $coursecode=$SESSION->coursecode;
        //echo "$coursecode";
			
 
        $sql = "INSERT INTO mdl_course_profile (coursecode,mime,data,revision) VALUES('$coursecode',:mime,:data,'$rev')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':mime', $mime);
        $stmt->bindParam(':data', $blob, PDO::PARAM_LOB);
 
        return $stmt->execute();
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
?>
