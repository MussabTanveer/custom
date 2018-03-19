<script src="../script/jquery/jquery-3.2.1.js"></script>
<script src="../script/table2excel/jquery.table2excel.min.js"></script>
<?php
    require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("View Result");
    $PAGE->set_heading("View Result");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/view_quiz_result1.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();


    $stdids=array();
    $qids = array();   
    $quizId= $_GET['quiz'];
    $courseId = $_GET['courseid'];
    //echo "$quizId";
    $quesnames=array(); 
    $quesmarks=array(); 
        
    $ques=$DB->get_records_sql("SELECT * FROM mdl_manual_quiz_question  WHERE mquizid=$quizId");

    $obtMarksq=$DB->get_records_sql("SELECT * FROM mdl_manual_quiz_attempt  WHERE quizid=$quizId");

    //while($result=mysql_fetch_array($query))

    $obtMarks=array();
    $cloids=array();

    if($obtMarksq)
    {
        foreach ($obtMarksq as $omark) {
            # code...
            $userid = $omark->userid;
            //$id = $omark->id;
            $obtmark=$omark->obtmark;
           

        array_push ($obtMarks,$obtmark);
        
       // array_push ($qids,$id);
      // array_push ($quesmarks,$maxmark);
         }
    }
    else
    {
    echo "<font color=red>the selected quiz has not been graded yet</font>";
        goto down;
    }

  
    //var_dump($obtMarks);
    if($ques)
    {
        foreach ($ques as $q) {
            # code...
            $qname = $q->quesname;
            $id = $q->id;
            $maxmark=$q->maxmark; 
            $cloid=$q->cloid;

       
        array_push ($cloids,$cloid);
        array_push ($quesnames,$qname);
        array_push ($qids,$id);
        array_push ($quesmarks,$maxmark);
         }
    } 
    //var_dump($quesnames);
    //var_dump($qids);
   // var_dump($quesmarks);
  //var_dump($cloids);
  $cloShortNames=array();

 

    foreach ($cloids as $cloid) {

         $clos= $DB->get_records_sql("SELECT * FROM mdl_competency WHERE id = ?",array($cloid));
         foreach ($clos as $clo) 
             
           $shortname=$clo->shortname;
       
        array_push ($cloShortNames,$shortname);
       
         }
  //  var_dump($cloShortNames);

    
    ?>
    <table border='10' cellpadding='15' id ="mytable">
    <tr>
    <th> Seat No. </th>
    <?php
    $marksIndex=0;
    foreach ($quesnames as $qname){
        
        ?><th> <?php echo $qname ." [$quesmarks[$marksIndex]]"; echo "<br>"; echo $cloShortNames[$marksIndex] ; 
        $marksIndex++ ?> </th>
        <?php
    }
    ?>
    </tr>
    <?php
    //echo "$chunkSize";
    
    
      $users=$DB->get_records_sql("SELECT u.id AS sid, u.username AS seatnum, u.firstname, u.lastname
        FROM mdl_role_assignments ra, mdl_user u, mdl_course c, mdl_context cxt
        WHERE ra.userid = u.id
        AND ra.contextid = cxt.id
        AND cxt.contextlevel = 50
        AND cxt.instanceid = c.id
        AND c.id = $courseId
        AND (roleid=5)");
        
      //  while($result=mysql_fetch_array($query))
       $obtmarksIndex=0;
      if($users)
        {
            foreach ($users as $user ) {
                # code...
            
            ?>
            
            <tr>
                <td> <?php echo $user->seatnum; array_push ($stdids,$user->sid); ?> </td>
                
                <?php
                    //var_dump($stdids);
               
                    foreach ($quesnames as $qname){
                ?>
                    <td ><?php echo $obtMarks[$obtmarksIndex]; 
                    $obtmarksIndex++; ?></td >
            
            <?php 
            }  ?> </tr> <?php
         
          }

        }
        // var_dump ($stdids);

        ?>
        
    
</table>
<br />
<button id="myButton" class="btn btn-success">Export to Excel</button>
<!-- Export html Table to xls -->
<script type="text/javascript" >
    $(document).ready(function(e){
        $("#myButton").click(function(e){ 
            $("#mytable").table2excel({
                name: "file name",
                filename: "quiz_result",
                fileext: ".xls"
            });
        });
    });
</script>

<?php

down:

    echo $OUTPUT->footer();