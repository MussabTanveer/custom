    <?php

    // Dispaly all semesters
    $rec=$DB->get_records_sql('SELECT * FROM mdl_semester ORDER BY id DESC');
		
    if($rec){
        ?>
        <form method="post" action="<?php echo $action ?>" id="form_check">
        <?php
        $serialno = 0;
        $table = new html_table();
        $table->head = array('S. No.','Name', 'Year' , 'Start Date', 'End Date', 'Select');
        foreach ($rec as $records) {
			$serialno++;
            $id = $records->id;
            $name = $records->name;
            $year = $records->year;
            $startdate = $records->startdate; $startdate = date('d-m-y', $startdate);
            $enddate = $records->enddate; $enddate = date('d-m-y', $enddate);
            $table->data[] = array($serialno, $name, $year, $startdate, $enddate, '<input type="radio" value="'.$id.'" name="semesterid">');
        }
        echo html_writer::table($table);
        ?>
        <input type='submit' value='NEXT' name='submit' class="btn btn-primary">
        </form>
        <br />
        <p id="msg"></p>

        <script>
        $('#form_check').on('submit', function (e) {
            if ($("input[type=radio]:checked").length === 0) {
                e.preventDefault();
                $("#msg").html("<font color='red'>Select any one course!</font>");
                return false;
            }
        });
        </script>
        <?php
    }
    else{
        echo "<h3>No courses found!</h3>";
    }
?>
