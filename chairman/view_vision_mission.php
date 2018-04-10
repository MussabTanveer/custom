<?php
    require_once('../../../config.php');

    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title("Vision & Mission");
    $PAGE->set_heading("Vision & Mission Statements");
    $PAGE->set_url($CFG->wwwroot.'/local/ned_obe/chairman/view_vision_mission.php');

    echo $OUTPUT->header();
    require_login();
    $rec1=$DB->get_records_sql('SELECT us.username FROM mdl_user us, mdl_role r,mdl_role_assignments ra   WHERE us.id=ra.userid AND r.id=ra.roleid AND  r.shortname=? AND us.id=? ',array('chairman',$USER->id));
    $rec1 || die('<h2>This page is for Chairperson only!</h2>'.$OUTPUT->footer());
    ?>
    <style>
        h3{
            text-decoration: underline;
        }
        .wrapper{
            text-align: center;
        }
        .effect{
            font-size: 1.2em;
        }
    </style>
    <script src="../script/jquery/jquery-3.2.1.js"></script>
    <script src="../script/jquery/jquery-ui.js"></script>
    <script>
        $(document).ready(function(){
            $("h3").animate({color: "#0072A4"}, 1500);
            $(".effect").animate({color: "green"}, 1500);
            $(".effect").animate({color: "#373A3C"}, 1500);
        });
    </script>
    <?php

    $uv=$DB->get_records_sql('SELECT id FROM  `mdl_vision_mission` WHERE idnumber = ?', array("uv"));
    $um=$DB->get_records_sql('SELECT id FROM  `mdl_vision_mission` WHERE idnumber = ?', array("um"));
    $dv=$DB->get_records_sql('SELECT id FROM  `mdl_vision_mission` WHERE idnumber = ?', array("dv"));
    $dm=$DB->get_records_sql('SELECT id FROM  `mdl_vision_mission` WHERE idnumber = ?', array("dm"));
    $dn=$DB->get_records_sql('SELECT id FROM  `mdl_vision_mission` WHERE idnumber = ?', array("dn"));
    $un=$DB->get_records_sql('SELECT id FROM  `mdl_vision_mission` WHERE idnumber = ?', array("un"));
    

    ?>
    <div class="wrapper">
    <?php

     if($un){

        foreach($un as $u){
            $id = $u->id;

        }

         $un=$DB->get_records_sql('SELECT description FROM  `mdl_vision_mission` WHERE id = ?', array($id));

        foreach($un as $u){
            $desc = $u->description;
        }
            ?>
            <h3>Name of University</h3><br />
            <?php
            if(!empty($desc))
                echo "<div class='row'><div class='col-md-2'></div><div class='col-md-8 effect'><b><i> $desc </i></b></div><div class='col-md-2'></div></div><br />";
            else
                echo "<div class='row'><div class='col-md-2'></div><div class='col-md-8'><p><b>Not available</b></p></div><div class='col-md-2'></div></div><br />";
        
    }


     if($dn){

        foreach($dn as $u){
            $id = $u->id;

        }

         $dn=$DB->get_records_sql('SELECT description FROM  `mdl_vision_mission` WHERE id = ?', array($id));

        foreach($dn as $u){
            $desc = $u->description;
        }
            ?>
            <h3>Name of Department</h3><br />
            <?php
            if(!empty($desc))
                echo "<div class='row'><div class='col-md-2'></div><div class='col-md-8 effect'><b><i> $desc </i></b></div><div class='col-md-2'></div></div><br />";
            else
                echo "<div class='row'><div class='col-md-2'></div><div class='col-md-8'><p><b>Not available</b></p></div><div class='col-md-2'></div></div><br />";
        
    }


    if($uv){

        foreach($uv as $u){
            $id = $u->id;

        }

         $uv=$DB->get_records_sql('SELECT description FROM  `mdl_vision_mission` WHERE id = ?', array($id));

        foreach($uv as $u){
            $desc = $u->description;
        }
            ?>
            <h3>Vision of University</h3><br />
            <?php
            if(!empty($desc))
                echo "<div class='row'><div class='col-md-2'></div><div class='col-md-8 effect'><b><i> $desc </i></b></div><div class='col-md-2'></div></div><br />";
            else
                echo "<div class='row'><div class='col-md-2'></div><div class='col-md-8'><p><b>Not available</b></p></div><div class='col-md-2'></div></div><br />";
        
    }
    if($um){

         foreach($um as $u){
            $id = $u->id;

        }

        $um=$DB->get_records_sql('SELECT description FROM  `mdl_vision_mission` WHERE id = ?', array($id));



        foreach($um as $u){
            $desc = $u->description;
        }
            ?>
            <h3>Mission of University</h3><br />
            <?php
            if(!empty($desc))
                echo "<div class='row'><div class='col-md-2'></div><div class='col-md-8 effect'><b><i> $desc </i></b></div><div class='col-md-2'></div></div><br />";
            else
                echo "<div class='row'><div class='col-md-2'></div><div class='col-md-8'><p><b>Not available</b></p></div><div class='col-md-2'></div></div><br />";
        
    }
    if($dv){

             foreach($dv as $u){
            $id = $u->id;

        }

        $dv=$DB->get_records_sql('SELECT description FROM  `mdl_vision_mission` WHERE id = ?', array($id));

        foreach($dv as $u){
            $desc = $u->description;
        }
            ?>
            <h3>Vision of Department</h3><br />
            <?php
            if(!empty($desc))
                echo "<div class='row'><div class='col-md-2'></div><div class='col-md-8 effect'><b><i> $desc </i></b></div><div class='col-md-2'></div></div><br />";
            else
                echo "<div class='row'><div class='col-md-2'></div><div class='col-md-8'><p><b>Not available</b></p></div><div class='col-md-2'></div></div><br />";
        
    }
    if($dm){


          foreach($dm as $u){
            $id = $u->id;

        }

        $dm=$DB->get_records_sql('SELECT description FROM  `mdl_vision_mission` WHERE id = ?', array($id));

        foreach($dm as $u){
            $desc = $u->description;
        }
            ?>
            <h3>Mission of Department</h3><br />
            <?php
            if(!empty($desc))
                echo "<div class='row'><div class='col-md-2'></div><div class='col-md-8 effect'><b><i> $desc </i></b></div><div class='col-md-2'></div></div><br />";
            else
                echo "<div class='row'><div class='col-md-2'></div><div class='col-md-8'><p><b>Not available</b></p></div><div class='col-md-2'></div></div><br />";
        
    }
    ?>
    </div>
    

    <?php
    echo $OUTPUT->footer();
?>