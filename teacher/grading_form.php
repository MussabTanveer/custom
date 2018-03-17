<?php 
   require_once('../../../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Online Grading Form");
    $PAGE->set_heading("Online Grading Form");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/teacher/grading_form.php');
    
    require_login();
    if($SESSION->oberole != "teacher"){
        header('Location: ../index.php');
    }
    echo $OUTPUT->header();


$chunkSize=0;
 $stdids=array();
 $qids = array();   
?>
<html>
<head> 

</head>
 <body >

 
 <?php  
 $quizId= $_GET['quiz'];
 $courseId = $_GET['courseid'];
 //echo "$quizId";
 $quesnames=array(); 
    
    $ques=$DB->get_records_sql("SELECT * FROM mdl_manual_quiz_question  WHERE mquizid=$quizId");

//while($result=mysql_fetch_array($query))

    if($ques)
    {
        foreach ($ques as $q) {
            # code...
            $qname = $q->quesname;
            $id = $q->id;

        array_push ($quesnames,$qname);
        array_push ($qids,$id);
         }
    } 
   //var_dump($quesnames);
     //var_dump($qids);

    
    ?>
    <table border='10' cellpadding='15' id ="mytable">
    <tr>
    <th> Roll No </th>
    <?php
    foreach ($quesnames as $qname){
        $chunkSize++;
        ?><th> <?php echo $qname ; ?> </th>
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
        
      ///  while($result=mysql_fetch_array($query))
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
                    <td contenteditable='true'></td >
            
            <?php 
            }  ?> </tr> <?php
         
          }

        }
       // var_dump ($stdids);

        ?>
        
    
</table>
<br></br>
<button onclick=GetCellValues() class="btn btn-primary" align="center"/> Submit

</body>
</html>
<?php
  echo $OUTPUT->footer();
  ?>


<script> 
//var x=document.getElementById("mytable").innerHTML;

function GetCellValues() {
    var data=[];
    var chunkSize = <?php echo json_encode($chunkSize); ?>;
    var s = <?php echo json_encode($stdids); ?>;
    var q = <?php echo json_encode($qids); ?>;
    var quizid = <?php echo json_encode($quizId); ?>;
    //alert(s);
    var table = document.getElementById('mytable');
    
    for (var r = 0, n = table.rows.length; r < n; r++) {
        for (var c = 0, m = table.rows[r].cells.length; c < m; c++) {
           // alert(table.rows[r].cells[c].innerHTML);
            data.push(table.rows[r].cells[c].innerHTML);
        }
        //alert(data);
        //data = [];
    }
      window.location.href = "insertQuiz.php?w1=" + data + "&chunkSize=" + chunkSize + "&sid=" + s + "&qid=" + q + "&quizid=" + quizid;
}

//alert("hello");
</script>
