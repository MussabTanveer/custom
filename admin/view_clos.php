<script src="../script/sweet-alert/sweetalert.min.js"></script>
<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("View OBE CLOs");
    $PAGE->set_heading("Course Learning Outcomes (CLOs)");
    $PAGE->set_url($CFG->wwwroot.'/custom/view_clos.php');
    
    echo $OUTPUT->header();
	require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());
	
	if(isset($_GET['fwid']))
	{
        $fw_id=$_GET['fwid'];
    
        /* delete code */
        if(isset($_GET['delete']))
        {
            $id_d=$_GET['delete'];
            $check=$DB->get_records_sql('SELECT * from mdl_competency_coursecomp where competencyid=?',array($id_d));
            if($check){
                $delmsg = "<font color='red'><b>The CLO cannot be deleted! Remove the mapping before CLO deletion.</b></font><br />";
                ?>
				<script>
				swal("Alert", "The CLO cannot be deleted! Remove the mapping before CLO deletion.", "info");
				</script>
				<?php
            }
            else
            {
                $sql_delete="DELETE from mdl_competency where id=$id_d";
                $DB->execute($sql_delete);
                $delmsg = "<font color='green'><b>CLO has been deleted!</b></font><br />";
                ?>
				<script>
				swal("CLO has been deleted!", {
						icon: "success",
						});
				</script>
				<?php
            }
        }
        /* /delete code */

       $clos=$DB->get_records_sql('SELECT * FROM `mdl_competency` WHERE competencyframeworkid = ? AND idnumber LIKE "%%-%%%-clo%" ORDER BY idnumber', array($fw_id));
        
        if($clos){
            $i = 1;
            echo "<h3>Already Present CLOs In Framework</h3>";
            foreach ($clos as $records){
                $shortname1 = $records->shortname;
                $id=$records->id;
                $cidnum = $records->idnumber;
                echo "<div class='row'>
                        <div class='col-md-2 col-sm-4 col-xs-8'>$i. $cidnum</div>
                        <div class='col-md-10 col-sm-8 col-xs-4'>
                            <a href='edit_clo.php?edit=$id&fwid=$fw_id' title='Edit'><img src='../img/icons/edit.png' /></a>
                            <a href='view_clos.php?delete=$id&fwid=$fw_id' onClick=\"return confirm('Delete CLO?')\" title='Delete'><img src='../img/icons/delete.png' /></a>
                        </div>
                      </div>";//link to edit_clo.php and delete
                $i++;
            }
        }
        else{
            echo "<h3>No CLOs in framework found!</h3>";
        }

        /*
        if(isset($delmsg)){
            echo $delmsg;
        }
        */

        echo $OUTPUT->footer();
	}
	else
	{?>
    	<h3 style="color:red;"> Invalid Selection </h3>
    	<a href="./select_frameworktoCLO.php">Back</a>
    	<?php
        echo $OUTPUT->footer();
    }?>
