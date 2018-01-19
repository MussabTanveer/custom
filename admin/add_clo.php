


<?php
require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Add OBE CLOs");
    $PAGE->set_heading("Add Course Learning Outcome (CLO)");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/admin/add_clo.php');
   echo $OUTPUT->header();
	require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());
	

    ?>

<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/jquery/jquery-2.1.3.js"></script>
<script type="text/javascript" >

$(document).ready(function(){
 $("#button").click(function (event) {
	 //var formdata = $("form").serialize();
	$.ajax({
		type : "POST",
		url : "save_clo.php",
		data : new FormData($("#cloForm")[0]),
		contentType : false,
		processData : false,
		success : function(feedback){
			$("#msg").html(feedback);
		}
	});
	return false;

});
});
</script>
<?php    
	if((isset($_POST['submit']) && isset( $_POST['frameworkid'])) || (isset($SESSION->fid3) && $SESSION->fid3 != "xyz") || isset($_POST['save']) || isset($_POST['return']))
	{
		if(isset($_POST['submit']) || (isset($SESSION->fid3) && $SESSION->fid3 != "xyz")){
			if(isset($SESSION->fid3) && $SESSION->fid3 != "xyz")
			{
				$frameworkid=$SESSION->fid3;
				$SESSION->fid3 = "xyz";
			}
			else
				$frameworkid=$_POST['frameworkid'];
			$rec=$DB->get_records_sql('SELECT shortname from mdl_competency_framework WHERE id=?', array($frameworkid));
			if($rec){
				foreach ($rec as $records){
				$framework_shortname = $records->shortname;
				}
			}
		}

		elseif(isset($_POST['return'])) {

			$coursecode = trim($_POST["idnumber"]); $coursecode=strtoupper($coursecode);
			


			$frameworkid = $_POST["frameworkid"];
			
			for ($i=0; $i <count($_POST["shortname"]) ; $i++) {
				# code...
				$shortname=trim($_POST["shortname"][$i]);  $shortname=strtoupper($shortname);
				$idnumber=$coursecode."-".$shortname; $idnumber=strtoupper($idnumber);
				$description=trim($_POST["description"][$i]);
				//echo $idnumber. "<br>";
				$time = time();
				$cloidnumbers=$DB->get_records_sql('SELECT * FROM  `mdl_competency` 
					WHERE competencyframeworkid = ? AND idnumber = ?',
					array($frameworkid,$idnumber));
				
				if($cloidnumbers == NULL) 
					{
					$sql="INSERT INTO mdl_competency (shortname, description, descriptionformat, idnumber, competencyframeworkid, parentid, path, sortorder, timecreated, timemodified, usermodified) VALUES ('$shortname', '$description', 1,     '$idnumber',$frameworkid ,-2, '/0/', 0, '$time', '$time', $USER->id)";
					$DB->execute($sql);
				}
				else 
				{//echo $idnumber . "already exists<br>";
				
				}

			}

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
			    $file = rand(1000,100000)."-".$_FILES['myfile']['name'];
			    $file_loc = $_FILES['myfile']['tmp_name'];
			    $file_size = $_FILES['myfile']['size'];
			    $file_type = $_FILES['myfile']['type'];
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


			$redirect_page1='../index.php';
			redirect($redirect_page1); 
		}

		$clos=$DB->get_records_sql('SELECT * FROM `mdl_competency` WHERE competencyframeworkid = ? AND idnumber LIKE "%%-%%%-clo%" ORDER BY idnumber', array($frameworkid));
        
        if($clos){
			$clocourses = array(); $clonames = array();
            foreach ($clos as $records){
				$clocourse = $records->idnumber; $clocourse = substr($clocourse,0,6);
				$cloname = $records->shortname;
				array_push($clocourses, $clocourse); // array of clo course codes
				array_push($clonames, $cloname); // array of clo names
				//echo "$clocourse   $cloname <br>";
            }
        }

		if(isset($msg3)){
			echo $msg3;
		}

		echo "<div class='row'><div class='col-md-6'><a href='view_clos.php?fwid=$frameworkid'><h3>View Already Present CLOs</h3></a></div><div id='list' class='col-md-6'></div></div>";
		?>
		<br />

		<p id="msg">
		

		</p>
		
		<h3>Add New CLO</h3>
		<form method='post' action="" class="mform" id="cloForm" enctype="multipart/form-data" >
			
			<div class="form-group row fitem ">
				<div class="col-md-3">
					<label class="col-form-label d-inline" for="id_clo">
						OBE framework
					</label>
				</div>
				<div class="col-md-9 form-inline felement">
					<?php echo $framework_shortname; ?>
				</div>
			</div>

			<div class="form-group row fitem">
				<div class="col-md-3">
					<span class="pull-xs-right text-nowrap">
						<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
					</span>
					<label class="col-form-label d-inline" for="id_idnumber">
						Course code
					</label>
				</div>
				<div class="col-md-9 form-inline felement" data-fieldtype="text">
					<input type="text"
							class="form-control"
							name="idnumber"
							id="id_idnumber"
							size=""
							pattern="[a-zA-Z]{2}-[0-9]{3}"
							title="eg. CS-304"
							required
							placeholder="eg. CS-304"
							maxlength="100" type="text" > (eg. CS-304)
					<div class="form-control-feedback" id="id_error_idnumber">


						<div>
					    <label>Choose File</label>
					    <input type="file" name="myfile" id="file">
					</div>			

					<?php
					if(isset($msg2)){
						echo $msg2;
					}
					?>
					</div>
				</div>
			</div>
			
			<div id="dynamicInput">
			<div class="row">
				<div class="col-md-3"><b>Enter CLO</b></div>
				<div class="col-md-9"></div>
			</div>

			<div class="form-group row fitem ">
				<div class="col-md-3">
					<span class="pull-xs-right text-nowrap">
						<abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
					</span>
					<label class="col-form-label d-inline" for="id_shortname">
						Name
					</label>
				</div>
				<div class="col-md-9 form-inline felement" data-fieldtype="text">
					<input type="text"
							class="form-control"
							name="shortname[]"
							id="id_shortname"
							size=""
							pattern="[c/C][l/L][o/O]-[0-9]{1,}"
							title="eg. CLO-12"
							required
							placeholder="eg. CLO-12"
							maxlength="100" type="text" > (eg. CLO-12)
					<div class="form-control-feedback" id="id_error_shortname">
					<?php
					if(isset($msg1)){
						echo $msg1;
					}
					?>
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
							<textarea id="id_description" name="description[]" class="form-control" rows="4" cols="80" spellcheck="true" ></textarea>
						</div>
					</div>
					<div class="form-control-feedback" id="id_error_description"  style="display: none;">
					</div>
				</div>
			</div>
			</div>

			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-9">
					<input class="btn btn-success" type="button" value="Add another CLO" onClick="addInput('dynamicInput');">
				</div>
			</div>
			<br />
			
			<input type="hidden" name="framework_shortname" value="<?php echo $framework_shortname; ?>"/>
			<input type="hidden" name="frameworkid" value="<?php echo $frameworkid; ?>"/>
			<button class="btn btn-info" type="submit"  name="save" id="button"/> Save and continue </button>
			<input class="btn btn-info" type="submit" name="return" value="Save and return"/>
            <a class="btn btn-default" type="submit" href="./select_frameworktoCLO.php">Cancel</a>

		</form>
		<br />
		<div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>

		
		<?php
		if(isset($_POST['save']) && !isset($msg3)){
		?>
		<script>
			document.getElementById("id_shortname").value = <?php echo json_encode($shortname); ?>;
			document.getElementById("id_description").value = <?php echo json_encode($description); ?>;
			document.getElementById("id_idnumber").value = <?php echo json_encode($idnumber); ?>;
		</script>
		<?php
		}
		?>
		
		<script>
		// script to create dynamic list of clos on course code input
		var clonames = <?php echo json_encode($clonames); ?>;
		var clocourses = <?php echo json_encode($clocourses); ?>;
		$(document).ready(function(){
			$("#id_idnumber").change(function(){
				var n = $('#id_idnumber').val().toUpperCase();
				var cnames = ""; var flag = 0;
				for (var i = 0; i < clonames.length; ++i) {
					if(clocourses[i] == n){
						flag = 1;
						cnames += clonames[i] + "<br />";
					}
				}
				if(flag == 0){
					cnames = "<font color='red'>-No CLOs found!</font>";
				}
				$("#list").html("<font color='green'><b>Present CLOs for " + n + ":</font></b><br />" + cnames);
			});
		});
		</script>
		
		<script>
			// script to add name and desc fields to form
			//var counter = 1;
			function addInput(divName){
				var newdiv = document.createElement('div');
				newdiv.innerHTML = '<div class="row"><div class="col-md-3"><b>Enter CLO</b></div><div class="col-md-9"></div></div>';
				document.getElementById(divName).appendChild(newdiv);

				var newdiv1 = document.createElement('div');
				newdiv1.innerHTML = '<div class="form-group row fitem "><div class="col-md-3"><span class="pull-xs-right text-nowrap"><abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr></span><label class="col-form-label d-inline" for="id_shortname">Name</label></div><div class="col-md-9 form-inline felement" data-fieldtype="text"><input type="text" class="form-control" name="shortname[]" id="id_shortname" size="" pattern="[c/C][l/L][o/O]-[0-9]{1,}" title="eg. CLO-12" required placeholder="eg. CLO-12" maxlength="100" type="text" > (eg. CLO-12)<div class="form-control-feedback" id="id_error_shortname"><?php if(isset($msg1)){echo $msg1;} ?></div></div></div>';
				document.getElementById(divName).appendChild(newdiv1);

				var newdiv2 = document.createElement('div');
				newdiv2.innerHTML = '<div class="form-group row fitem"><div class="col-md-3"><span class="pull-xs-right text-nowrap"></span><label class="col-form-label d-inline" for="id_description">Description</label></div><div class="col-md-9 form-inline felement" data-fieldtype="editor"><div><div><textarea id="id_description" name="description[]" class="form-control" rows="4" cols="80" spellcheck="true" ></textarea></div></div><div class="form-control-feedback" id="id_error_description"  style="display: none;"></div></div></div>';
				document.getElementById(divName).appendChild(newdiv2);
				counter++;
			}
		</script>	
		
		<?php 
			echo $OUTPUT->footer();
    
	}
	else
	{?>
		<h3 style="color:red;"> Invalid Selection </h3>
    	<a href="./select_frameworktoCLO.php">Back</a>
    	<?php
        echo $OUTPUT->footer();
    }?>

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
