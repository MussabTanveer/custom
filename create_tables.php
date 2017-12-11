<?php
	
  $link = mysqli_connect("localhost", "bn_moodle", "274001b456", "bitnami_moodle");
  // Check connection
    if($link === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());

    }
    
     
  // Creating table mdl_taxonomy_domain

   $sql = "CREATE TABLE mdl_taxonomy_domain (
         id INT(100) NOT NULL AUTO_INCREMENT,
         name VARCHAR(100) NOT NULL,
         description VARCHAR(100) NOT NULL,
         PRIMARY KEY (id)
        ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8";
    
    if(mysqli_query($link, $sql)){
        echo "Table mdl_taxonomy_domain created successfully.<br>";


        $sql="INSERT INTO `mdl_taxonomy_domain` (`id`, `name`, `description`) VALUES
            (1, 'cognitive', 'thinking,knowledge'),
            (2, 'psychomotor', 'doing,skills'),
            (3, 'affective', 'feelings, attitudes')";

    if(mysqli_query($link, $sql)){
        echo "values inserted into mdl_taxonomy_domain<br>";

    }
    else
        echo "cannot insert values to mdl_taxonomy_domain<br>";



    } else{

        echo "cannot create table mdl_taxonomy_domain " . mysqli_error($link) . "<br>";
    }



    // Creating table mdl_taxonomy_levels

     
      $sql= "CREATE TABLE `mdl_taxonomy_levels` (
         `id` INT(100) NOT NULL AUTO_INCREMENT,
         `domainid` INT(100) NOT NULL,
         `name` VARCHAR(100) NOT NULL,
         `description` VARCHAR(100) NOT NULL,
         `level` VARCHAR(100) NOT NULL,
         PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8";

    if(mysqli_query($link, $sql)){
        echo "Table mdl_taxonomy_levels created successfully.<br>";


        $sql="INSERT INTO `mdl_taxonomy_levels` (`id`, `domainid`, `name`, `description`, `level`) VALUES
            (1, 1, 'remember', 'Remembers previously learned material ', 'c1'),
            (2, 1, 'understand', 'Grasps the meaning of material (lowest level of understanding)', 'c2'),
            (3, 1, 'apply', 'Uses learning in new and concrete situations (higher level of understanding)', 'c3'),
            (4, 1, 'analyze', 'Understands both the content and structure of material', 'c4'),
            (5, 1, 'evaluate', 'Judges the value of material for a given purpose ', 'c5'),
            (6, 1, 'create', 'Formulate new structures form existing knowledge and skills', 'c6'),
            (7, 2, 'perception', 'Senses cues that guide motor activity', 'p1'),
            (8, 2, 'set', 'Is mentally, emotionally and physically ready to act', 'p2'),
            (9, 2, 'guided response', 'Imitates and practices skills, often in discrete stops', 'p3'),
            (10, 2, 'mechanism', 'Performs acts with increasing efficiency, confidence and proficiency', 'p4'),
            (11, 2, 'complete overt response', 'Performs automatically', 'p5'),
            (12, 2, 'adaption', 'Adapt skill sets to meet a problem situation', 'p6'),
            (13, 2, 'origination', 'Creates new patterns for specific situations', 'p7'),
            (14, 3, 'receiving', 'Selectively attends to stimuli', 'a1'),
            (15, 3, 'responding', 'Responds to stimuli', 'a2'),
            (16, 3, 'valuing', 'Attaches value or worth to something', 'a3'),
            (17, 3, 'organization', 'Conceptualizes the value and resolves conflict between it and other values', 'a4'),
            (18, 3, 'internalizing', 'Integrates the value into a value system that controls behaviour', 'a5')";

    if(mysqli_query($link, $sql)){
        echo "values inserted into mdl_taxonomy_levels<br>";

    }
    else
        echo "cannot insert values to mdl_taxonomy_levels<br>";
    


    } else{

        echo "cannot create table mdl_taxonomy_levels " . mysqli_error($link) . "<br>";
    }



      // Creating table mdl_taxonomy_plo_domain


   $sql= "CREATE TABLE `mdl_taxonomy_plo_domain` (
         `id` INT(11) NOT NULL AUTO_INCREMENT,
         `frameworkid` INT(11) NOT NULL,
         `ploid` INT(11) NOT NULL,
         `domainid` INT(11) NOT NULL,
         PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

         if(mysqli_query($link, $sql)){
          echo "Table mdl_taxonomy_plo_domain created successfully.<br>";
    }       
     else
     {
        echo "cannot create table mdl_taxonomy_plo_domain " . mysqli_error($link) . "<br>";
    }


 // Creating table mdl_taxonomy_clo_level

   $sql= "CREATE TABLE `mdl_taxonomy_clo_level` (
         `id` INT(11) NOT NULL AUTO_INCREMENT,
         `frameworkid` INT(11) NOT NULL,
         `cloid` INT(11) NOT NULL,
         `levelid` INT(11) NOT NULL,
         PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

    if(mysqli_query($link, $sql)){
          echo "Table mdl_taxonomy_clo_level created successfully.<br>";
    }       
     else
     {
        echo "cannot create table mdl_taxonomy_clo_level " . mysqli_error($link) . "<br>";
    }


     // Creating table mdl_consolidated_report

    $sql= "CREATE TABLE `mdl_consolidated_report` (
         `id` INT(11) NOT NULL AUTO_INCREMENT,
         `course` INT(11) NOT NULL,
         `module` INT(11) NOT NULL,
         `instance` INT(11) NOT NULL,
         `cloid` INT(11) NOT NULL,
         `pass` float NOT NULL,
         `fail` float NOT NULL,
         PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8";

    if(mysqli_query($link, $sql)){
          echo "Table mdl_consolidated_report created successfully.<br>";
    }       
     else
     {
        echo "cannot create table mdl_consolidated_report " . mysqli_error($link). "<br>";
    }

    

    // Creating table mdl_consolidated_report_student
    
    $sql= "CREATE TABLE `mdl_consolidated_report_student` (
         `id` INT(11) NOT NULL AUTO_INCREMENT,
         `course` INT(11) NOT NULL,
         `userid` INT(11) NOT NULL,
         `module` INT(11) NOT NULL,
         `instance` INT(11) NOT NULL,
         `cloid` INT(11) NOT NULL,
         `status` INT(11) NOT NULL,
         PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8";

    if(mysqli_query($link, $sql)){
          echo "Table mdl_consolidated_report_student created successfully.<br>";
    }       
     else
     {
        echo "cannot create table mdl_consolidated_report_student " . mysqli_error($link). "<br>";
    }

    
    //Alter mdl_question to add competencyid column
    $sql="ALTER TABLE `mdl_question`
              ADD `competencyid` INT(11) DEFAULT NULL"; 


    if(mysqli_query($link, $sql)){
          echo "Table mdl_question altered.<br>";
    }       
     else
     {
        echo "cannot alter table mdl_question <br>";
    }
    // Close connection
    mysqli_close($link);

    ?>


    

