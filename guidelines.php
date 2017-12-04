<?php
    require_once('../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Admin Guidelines");
    $PAGE->set_heading("Admin Guidelines");
    $PAGE->set_url($CFG->wwwroot.'/custom/guidelines.php');
    
    //echo $OUTPUT->header();
	require_login();
    is_siteadmin() || die($OUTPUT->header().'<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="../theme/boost/pix/favicon" />
	
	<link href='css/guideline/droid-serif-open-sans.css' rel='stylesheet' type='text/css'>

	<link rel="stylesheet" href="css/guideline/reset.css"> <!-- CSS reset -->
	<link rel="stylesheet" href="css/guideline/style.css"> <!-- Resource style -->
	<script src="script/guideline/modernizr.js"></script> <!-- Modernizr -->
  	
	<title>Admin Guidelines</title>
</head>
<body>
	<header>
		<h1>"Admin Guidelines"</h1>
	</header>

	<section id="cd-timeline" class="cd-container">
		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-location">
				<img src="img/guideline/circle.svg" alt="">
			</div> <!-- cd-timeline-img -->
			<div class="cd-timeline-content">
				<h2>Step 1: Create OBE Framework</h2>
				<p>Create an Outcome Based Education framework which will contain all the Programme Educational Objectives PEOs, Programme Learning Outcomes PLOs and Course Learning Outcomes CLOs. Name and ID number fields in the form are required.</p>
				<a href="./add_framework.php" target="_blank" class="cd-read-more">Proceed</a>
				<span class="cd-date"></span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->
		
		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-picture">
				<img src="img/guideline/circle.svg" alt="">
			</div> <!-- cd-timeline-img -->
			<div class="cd-timeline-content">
				<h2>Step 2: Define PEOs</h2>
				<p>Define Programme Educational Objectives PEOs which will be added to the framework. Name and ID number fields in the form are required. ID number MUST be in the form PEO-number (PEO-3).</p>
				<a href="./select_frameworktoPEO.php" target="_blank" class="cd-read-more">Proceed</a>
				<span class="cd-date"></span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->

		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-movie">
				<img src="img/guideline/circle.svg" alt="">
			</div> <!-- cd-timeline-img -->
			<div class="cd-timeline-content">
				<h2>Step 3: Define PLOs</h2>
				<p>Define Programme Learning Outcomes PLOs which will be added to the framework. Name and ID number fields in the form are required. ID number MUST be in the form PLO-number (PLO-5).</p>
				<a href="./select_frameworktoPLO.php" target="_blank" class="cd-read-more">Proceed</a>
				<span class="cd-date"></span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->

		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-location">
				<img src="img/guideline/circle.svg" alt="">
			</div> <!-- cd-timeline-img -->
			<div class="cd-timeline-content">
				<h2>Step 4: Map PLOs to PEOs</h2>
				<p>Map Programme Learning Outcomes PLOs to Programme Educational Objectives PEOs by selecting the right PEO for each PLO.</p>
				<a href="./select_framework.php" target="_blank" class="cd-read-more">Proceed</a>
				<span class="cd-date"></span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->

		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-picture">
				<img src="img/guideline/circle.svg" alt="">
			</div> <!-- cd-timeline-img -->
			<div class="cd-timeline-content">
				<h2>Step 5: Define CLOs</h2>
				<p>Define Course Learning Outcomes CLOs which will be added to the framework. Name and ID number fields in the form are required. ID number MUST be in the form course-code-CLO-number (CS-304-CLO-2).</p>
				<a href="./select_frameworktoCLO.php" target="_blank" class="cd-read-more">Proceed</a>
				<span class="cd-date"></span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->

		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-movie">
				<img src="img/guideline/circle.svg" alt="">
			</div> <!-- cd-timeline-img -->
			<div class="cd-timeline-content">
				<h2>Step 6: Map CLOs to PLOs</h2>
				<p>Map Course Learning Outcomes CLOs to Programme Learning Outcomes PLOs by selecting the right PLO for each CLO.</p>
				<a href="./select_framework-2.php" target="_blank" class="cd-read-more">Proceed</a>
				<span class="cd-date"></span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->

		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-location">
				<img src="img/guideline/circle.svg" alt="">
			</div> <!-- cd-timeline-img -->
			<div class="cd-timeline-content">
				<h2>Step 7: Map PLOs to Taxonomy Domains</h2>
				<p>Map Programme Learning Outcomes PLOs to Taxonomy Domains by selecting a single or multiple Domains for each PLO.</p>
				<a href="./display_outcome_framework-2.php" target="_blank" class="cd-read-more">Proceed</a>
				<span class="cd-date"></span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->
		
		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-picture">
				<img src="img/guideline/circle.svg" alt="">
			</div> <!-- cd-timeline-img -->
			<div class="cd-timeline-content">
				<h2>Step 8: Map CLOs to Taxonomy Levels</h2>
				<p>Map Course Learning Outcomes CLOs to Taxonomy Levels by selecting right Level for each CLO.</p>
				<a href="./display_outcome_framework-3.php" target="_blank" class="cd-read-more">Proceed</a>
				<span class="cd-date"></span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->

		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-movie">
				<img src="img/guideline/circle.svg" alt="">
			</div> <!-- cd-timeline-img -->
			<div class="cd-timeline-content">
				<h2>Step 9: View Mapping of OBE Framework</h2>
				<p>View mapping of PLOs to PEOs and mapping of Courses to PLOs.</p>
				<a href="./display_outcome_framework.php" target="_blank" class="cd-read-more">Proceed</a>
				<span class="cd-date"></span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->

		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-location">
				<img src="img/guideline/circle.svg" alt="">
			</div> <!-- cd-timeline-img -->
			<div class="cd-timeline-content">
				<h2>Step 10: View Mapping of Bloom's Taxonomy</h2>
				<p>View mapping of CLOs to Taxonomy Levels and mapping of PLOs to Taxonomy Domains.</p>
				<a href="./display_outcome_framework-4.php" target="_blank" class="cd-read-more">Proceed</a>
				<span class="cd-date"></span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->

		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-picture">
				<img src="img/guideline/circle.svg" alt="">
			</div> <!-- cd-timeline-img -->
			<div class="cd-timeline-content">
				<h2>Step 11: Create Courses</h2>
				<p>Add a new course. Course full name, short name, ID number (course-code), start and end dates are required.</p>
				<a href="../course/edit.php?category=1&returnto=guidelines" target="_blank" class="cd-read-more">Proceed</a>
				<span class="cd-date"></span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->

		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-movie">
				<img src="img/guideline/circle.svg" alt="">
			</div> <!-- cd-timeline-img -->
			<div class="cd-timeline-content">
				<h2>Step 12: Add CLOs to Courses</h2>
				<p>Add CLOs to courses.</p>
				<a href="./select_course.php" target="_blank" class="cd-read-more">Proceed</a>
				<span class="cd-date"></span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->

		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-location">
				<img src="img/guideline/circle.svg" alt="">
			</div> <!-- cd-timeline-img -->
			<div class="cd-timeline-content">
				<h2>Step 13: Add a new User</h2>
				<p>Add a new user. User first name, surname, username, password and e-mail address are required.</p>
				<a href="../user/editadvanced.php?id=-1" target="_blank" class="cd-read-more">Proceed</a>
				<span class="cd-date"></span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->

		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-picture">
				<img src="img/guideline/circle.svg" alt="">
			</div> <!-- cd-timeline-img -->
			<div class="cd-timeline-content">
				<h2>Step 14: View all Users</h2>
				<p>View all users present in database.</p>
				<a href="../admin/user.php" target="_blank" class="cd-read-more">Proceed</a>
				<span class="cd-date"></span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->

		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-movie">
				<img src="img/guideline/circle.svg" alt="">
			</div> <!-- cd-timeline-img -->
			<div class="cd-timeline-content">
				<h2>Step 15: Add/Edit/View Cohorts</h2>
				<p>Cohorts are user groups. The purpose of cohorts is to enable all members of the cohort to be easily enrolled in a course.</p>
				<a href="../cohort/index.php" target="_blank" class="cd-read-more">Proceed</a>
				<span class="cd-date"></span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->

		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-location">
				<img src="img/guideline/circle.svg" alt="">
			</div> <!-- cd-timeline-img -->
			<div class="cd-timeline-content">
				<h2>Step 16: Enrol/Unenrol Users</h2>
				<p>Enrol/Unenrol users in course. User roles include Student, Teacher, Manager, etc.</p>
				<a href="./select_course_enrol.php" target="_blank" class="cd-read-more">Proceed</a>
				<span class="cd-date"></span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->
		
		<div class="cd-timeline-block">
			<div class="cd-timeline-img cd-picture">
				<img src="img/guideline/circle.svg" alt="">
			</div> <!-- cd-timeline-img -->
			<div class="cd-timeline-content">
				<h2>Final Step</h2>
				<p>This is the content of the last step</p>
				<a href="#0" target="_blank" class="cd-read-more">Proceed</a>
				<span class="cd-date"></span>
			</div> <!-- cd-timeline-content -->
		</div> <!-- cd-timeline-block -->

	</section> <!-- cd-timeline -->

<script src="script/jquery/jquery-3.2.1.min.js"></script>
<script src="script/guideline/main.js"></script> <!-- Resource jQuery -->
</body>
</html>
