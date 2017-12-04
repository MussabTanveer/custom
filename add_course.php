<link rel="stylesheet" href="./css/datepicker/wbn-datepicker.css">
<script src="./script/jquery/jquery-3.2.1.js"></script>
<?php
    require_once('../config.php');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title("Add Courses");
    $PAGE->set_heading("Add Courses");
    $PAGE->set_url($CFG->wwwroot.'/custom/add_course.php');
    
    echo $OUTPUT->header();
	require_login();
    is_siteadmin() || die('<h2>This page is for site admins only!</h2>'.$OUTPUT->footer());
	
	if((isset($_POST['submit']) && isset( $_POST['fwid'])) || (isset($SESSION->fid11) && $SESSION->fid11 != "xyz") || isset($_POST['save']))
    {
		if(isset($_POST['submit']) || (isset($SESSION->fid11) && $SESSION->fid11 != "xyz")){
			if(isset($SESSION->fid11) && $SESSION->fid11 != "xyz")
			{
				$fw_id=$SESSION->fid11;
				$SESSION->fid1 = "xyz";
			}
			else
				$fw_id=$_POST['fwid'];
			$rec=$DB->get_records_sql('SELECT shortname from mdl_competency_framework WHERE id=?', array($fw_id));
			if($rec){
				foreach ($rec as $records){
					$fw_shortname = $records->shortname;
				}
			}
		}
	
		if(isset($_POST['save'])){
			$shortname=trim($_POST['shortname']);
			$description=trim($_POST['description']);
			$idnumber=trim($_POST['idnumber']); $idnumber=strtoupper($idnumber);
			$fw_id=$_POST['fid'];
			$fw_shortname=$_POST['fname'];
			$time = time();
			
			if(empty($shortname) || empty($idnumber))
			{
				if(empty($shortname))
				{
					$msg1="<font color='red'>-Please enter PEO name</font>";
				}
				if(empty($idnumber))
				{
					$msg2="<font color='red'>-Please enter ID number</font>";
				}
			}
			elseif(substr($idnumber,0,4) != 'PEO-')
			{
				$msg2="<font color='red'>-The ID number must start with PEO-</font>";
			}
			else{
				//echo $shortname;
				//echo $description;
				//echo $idnumber;
				$check=$DB->get_records_sql('SELECT * from mdl_competency WHERE idnumber=? AND competencyframeworkid=?', array($idnumber, $fw_id));
				if(count($check)){
					$msg2="<font color='red'>-Please enter UNIQUE ID number</font>";
				}
				else{
					$sql="INSERT INTO mdl_competency (shortname, description, descriptionformat, idnumber, competencyframeworkid, path, sortorder, timecreated, timemodified, usermodified) VALUES ('$shortname', '$description', 1, '$idnumber', '$fw_id', '/0/', 0, '$time', '$time', $USER->id)";
					$DB->execute($sql);
					$msg3 = "<font color='green'><b>PEO successfully defined!</b></font><br /><p><b>Add another below.</b></p>";
				}
			}
		}
		
		if(isset($msg3)){
			echo $msg3;
		}
		
		?>
		<br />
		<h3>Add New Course</h3>
		<form method='post' action="" class="mform">
			
            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                        <a class="btn btn-link p-a-0" role="button" data-container="body" data-toggle="popover" data-placement="right"
                        data-content="&lt;div class=&quot;no-overflow&quot;&gt;&lt;p&gt;The full name of the course is displayed at the top of each page in the course and in the list of courses.&lt;/p&gt;&lt;/div&gt; "
                        data-html="true" tabindex="0" data-trigger="focus">
                        <i class="icon fa fa-question-circle text-info fa-fw " aria-hidden="true" title="Help with Course full name" aria-label="Help with Course full name"></i>
                        </a>
                    </span>
                    <label class="col-form-label d-inline" for="id_fullname">
                        Course full name
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
                            class="form-control "
                            name="fullname"
                            id="id_fullname"
                            value=""
                            required
                            size="50"
                            maxlength="254" type="text" >
                    <div class="form-control-feedback" id="id_error_fullname"  style="display: none;">
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
                        <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                        <a class="btn btn-link p-a-0" role="button" data-container="body" data-toggle="popover" data-placement="right"
                        data-content="&lt;div class=&quot;no-overflow&quot;&gt;&lt;p&gt;The short name of the course is displayed in the navigation and is used in the subject line of course email messages.&lt;/p&gt;&lt;/div&gt;"
                        data-html="true" tabindex="0" data-trigger="focus">
                        <i class="icon fa fa-question-circle text-info fa-fw " aria-hidden="true" title="Help with Course short name" aria-label="Help with Course short name"></i>
                        </a>
                    </span>
                    <label class="col-form-label d-inline" for="id_shortname">
                        Course short name
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
                            class="form-control "
                            name="shortname"
                            id="id_shortname"
                            value=""
                            required
                            size="20"
                            maxlength="100" type="text">
                    <div class="form-control-feedback" id="id_error_shortname" style="display: none;">
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
                    <abbr class="initialism text-danger" title="Required"><i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required" aria-label="Required"></i></abbr>
                        <a class="btn btn-link p-a-0" role="button" data-container="body" data-toggle="popover" data-placement="right"
                        data-content="&lt;div class=&quot;no-overflow&quot;&gt;&lt;p&gt;The ID number of a course is only used when matching the course against external systems and is not displayed anywhere on the site. If the course has an official code name it may be entered, otherwise the field can be left blank.&lt;/p&gt;&lt;/div&gt; "
                        data-html="true" tabindex="0" data-trigger="focus">
                        <i class="icon fa fa-question-circle text-info fa-fw " aria-hidden="true" title="Help with Course ID number" aria-label="Help with Course ID number"></i>
                        </a>
                    </span>
                    <label class="col-form-label d-inline" for="id_idnumber">
                        Course code
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
                            class="form-control "
                            name="idnumber"
                            id="id_idnumber"
                            value=""
                            required
                            size="10"
                            maxlength="100" type="text" >
                    <div class="form-control-feedback" id="id_error_idnumber"  style="display: none;">
                    <?php
					if(isset($msg2)){
						echo $msg2;
					}
					?>
                    </div>
                </div>
            </div>

            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <a class="btn btn-link p-a-0" role="button" data-container="body" data-toggle="popover" data-placement="right"
                        data-content="&lt;div class=&quot;no-overflow&quot;&gt;&lt;p&gt;This setting determines the start of the first week for a course in weekly format. It also determines the earliest date that logs of course activities are available for. If the course is reset and the course start date changed, all dates in the course will be moved in relation to the new start date.&lt;/p&gt;&lt;/div&gt; "
                        data-html="true" tabindex="0" data-trigger="focus">
                        <i class="icon fa fa-question-circle text-info fa-fw " aria-hidden="true" title="Help with Course start date" aria-label="Help with Course start date"></i>
                        </a>
                    </span>
                    <label for="id_startdate">
                        Course start date
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
                        class="form-control wbn-datepicker"
                        name="startdate"
                        id="id_startdate"
                        size="27"
                        maxlength="100" >
                    <div class="form-control-feedback" id="id_error_idnumber"  style="display: none;">
                    </div>
                </div>
            </div>

            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <a class="btn btn-link p-a-0" role="button"
                        data-container="body" data-toggle="popover"
                        data-placement="right" data-content="&lt;div class=&quot;no-overflow&quot;&gt;&lt;p&gt;The course end date is only used for reports. Users can still enter the course after the end date.&lt;/p&gt;
                        &lt;/div&gt; "
                        data-html="true" tabindex="0" data-trigger="focus">
                        <i class="icon fa fa-question-circle text-info fa-fw " aria-hidden="true" title="Help with Course end date" aria-label="Help with Course end date"></i>
                        </a>
                    </span>
                    <label for="id_enddate">
                        Course end date
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="text">
                    <input type="text"
                        class="form-control wbn-datepicker"
                        name="enddate"
                        id="id_enddate"
                        data-start-src="id_startdate"
                        size="27"
                        maxlength="100" >
                    <div class="form-control-feedback" id="id_error_idnumber"  style="display: none;">
                    </div>
                </div>
            </div>
            
            <!--
            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <a class="btn btn-link p-a-0" role="button" data-container="body" data-toggle="popover" data-placement="right"
                        data-content="&lt;div class=&quot;no-overflow&quot;&gt;&lt;p&gt;This setting determines the start of the first week for a course in weekly format. It also determines the earliest date that logs of course activities are available for. If the course is reset and the course start date changed, all dates in the course will be moved in relation to the new start date.&lt;/p&gt;&lt;/div&gt; "
                        data-html="true" tabindex="0" data-trigger="focus">
                        <i class="icon fa fa-question-circle text-info fa-fw " aria-hidden="true" title="Help with Course start date" aria-label="Help with Course start date"></i>
                        </a>
                    </span>
                    <label class="col-form-label d-inline" for="id_startdate">
                        Course start date
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="date_selector">
                    <span class="fdate_selector">
                        <div class="form-group  fitem">
                        <label class="col-form-label sr-only" for="id_startdate_day">
                            Day
                        </label>
                        <span data-fieldtype="select">
                        <select class="custom-select"
                            name="startdate[day]"
                            id="id_startdate_day">
                            <option value="1" selected >1</option>
                            <option value="2"   >2</option>
                            <option value="3"   >3</option>
                            <option value="4"   >4</option>
                            <option value="5"   >5</option>
                            <option value="6"   >6</option>
                            <option value="7"   >7</option>
                            <option value="8"   >8</option>
                            <option value="9"   >9</option>
                            <option value="10"   >10</option>
                            <option value="11"   >11</option>
                            <option value="12"   >12</option>
                            <option value="13"   >13</option>
                            <option value="14"   >14</option>
                            <option value="15"   >15</option>
                            <option value="16"   >16</option>
                            <option value="17"   >17</option>
                            <option value="18"   >18</option>
                            <option value="19"   >19</option>
                            <option value="20"   >20</option>
                            <option value="21"   >21</option>
                            <option value="22"   >22</option>
                            <option value="23"   >23</option>
                            <option value="24"   >24</option>
                            <option value="25"   >25</option>
                            <option value="26"   >26</option>
                            <option value="27"   >27</option>
                            <option value="28"   >28</option>
                            <option value="29"   >29</option>
                            <option value="30"   >30</option>
                            <option value="31"   >31</option>
                        </select>
                        </span>
                        <div class="form-control-feedback" id="id_error_startdate[day]"  style="display: none;">
                            
                        </div>
                        </div>
                        &nbsp;
                        <div class="form-group  fitem  ">
                        <label class="col-form-label sr-only" for="id_startdate_month">
                            Month
                        </label>
                        <span data-fieldtype="select">
                        <select class="custom-select"
                            name="startdate[month]"
                            id="id_startdate_month">
                            <option value="1" selected >January</option>
                            <option value="2"   >February</option>
                            <option value="3"   >March</option>
                            <option value="4"   >April</option>
                            <option value="5"   >May</option>
                            <option value="6"   >June</option>
                            <option value="7"   >July</option>
                            <option value="8"   >August</option>
                            <option value="9"   >September</option>
                            <option value="10"  >October</option>
                            <option value="11"  >November</option>
                            <option value="12"  >December</option>
                        </select>
                        </span>
                        <div class="form-control-feedback" id="id_error_startdate[month]"  style="display: none;">
                            
                        </div>
                    </div>
                        &nbsp;
                    <div class="form-group fitem">
                    <label class="col-form-label sr-only" for="id_startdate_year">
                        Year
                    </label>
                    <span data-fieldtype="select">
                    <select class="custom-select"
                        name="startdate[year]"
                        id="id_startdate_year">
                        <option value="1980"   >1980</option>
                        <option value="1981"   >1981</option>
                        <option value="1982"   >1982</option>
                        <option value="1983"   >1983</option>
                        <option value="1984"   >1984</option>
                        <option value="1985"   >1985</option>
                        <option value="1986"   >1986</option>
                        <option value="1987"   >1987</option>
                        <option value="1988"   >1988</option>
                        <option value="1989"   >1989</option>
                        <option value="1990"   >1990</option>
                        <option value="1991"   >1991</option>
                        <option value="1992"   >1992</option>
                        <option value="1993"   >1993</option>
                        <option value="1994"   >1994</option>
                        <option value="1995"   >1995</option>
                        <option value="1996"   >1996</option>
                        <option value="1997"   >1997</option>
                        <option value="1998"   >1998</option>
                        <option value="1999"   >1999</option>
                        <option value="2000"   >2000</option>
                        <option value="2001"   >2001</option>
                        <option value="2002"   >2002</option>
                        <option value="2003"   >2003</option>
                        <option value="2004"   >2004</option>
                        <option value="2005"   >2005</option>
                        <option value="2006"   >2006</option>
                        <option value="2007"   >2007</option>
                        <option value="2008"   >2008</option>
                        <option value="2009"   >2009</option>
                        <option value="2010"   >2010</option>
                        <option value="2011"   >2011</option>
                        <option value="2012"   >2012</option>
                        <option value="2013"   >2013</option>
                        <option value="2014"   >2014</option>
                        <option value="2015"   >2015</option>
                        <option value="2016"   >2016</option>
                        <option value="2017" selected >2017</option>
                        <option value="2018"   >2018</option>
                        <option value="2019"   >2019</option>
                        <option value="2020"   >2020</option>
                        <option value="2021"   >2021</option>
                        <option value="2022"   >2022</option>
                        <option value="2023"   >2023</option>
                        <option value="2024"   >2024</option>
                        <option value="2025"   >2025</option>
                        <option value="2026"   >2026</option>
                        <option value="2027"   >2027</option>
                        <option value="2028"   >2028</option>
                        <option value="2029"   >2029</option>
                        <option value="2030"   >2030</option>
                        <option value="2031"   >2031</option>
                        <option value="2032"   >2032</option>
                        <option value="2033"   >2033</option>
                        <option value="2034"   >2034</option>
                        <option value="2035"   >2035</option>
                        <option value="2036"   >2036</option>
                        <option value="2037"   >2037</option>
                        <option value="2038"   >2038</option>
                        <option value="2039"   >2039</option>
                        <option value="2040"   >2040</option>
                        <option value="2041"   >2041</option>
                        <option value="2042"   >2042</option>
                        <option value="2043"   >2043</option>
                        <option value="2044"   >2044</option>
                        <option value="2045"   >2045</option>
                        <option value="2046"   >2046</option>
                        <option value="2047"   >2047</option>
                        <option value="2048"   >2048</option>
                        <option value="2049"   >2049</option>
                        <option value="2050"   >2050</option>
                        <option value="2050"   >2051</option>
                        <option value="2050"   >2052</option>
                        <option value="2050"   >2053</option>
                        <option value="2050"   >2054</option>
                        <option value="2050"   >2055</option>
                        <option value="2050"   >2056</option>
                        <option value="2050"   >2057</option>
                        <option value="2050"   >2058</option>
                        <option value="2050"   >2059</option>
                        <option value="2050"   >2060</option>
                        <option value="2050"   >2061</option>
                        <option value="2050"   >2062</option>
                        <option value="2050"   >2063</option>
                        <option value="2050"   >2064</option>
                        <option value="2050"   >2065</option>
                        <option value="2050"   >2066</option>
                        <option value="2050"   >2067</option>
                        <option value="2050"   >2068</option>
                        <option value="2050"   >2069</option>
                        <option value="2050"   >2070</option>
                        <option value="2050"   >2071</option>
                        <option value="2050"   >2072</option>
                        <option value="2050"   >2073</option>
                        <option value="2050"   >2074</option>
                        <option value="2050"   >2075</option>
                        <option value="2050"   >2076</option>
                        <option value="2050"   >2077</option>
                        <option value="2050"   >2078</option>
                        <option value="2050"   >2079</option>
                        <option value="2050"   >2080</option>
                    </select>
                    </span>
                    <div class="form-control-feedback" id="id_error_startdate[year]"  style="display: none;">
                        
                    </div>
                </div>
                    &nbsp;
                        <a class="visibleifjs" name="startdate[calendar]" href="#" id="id_startdate_calendar"><i class="icon fa fa-calendar fa-fw " aria-hidden="true" title="Calendar" aria-label="Calendar"></i></a>
                    </span>
                    <div class="form-control-feedback" id="id_error_"  style="display: none;">
                        
                    </div>
                </div>
            </div>
            -->
            
            <!--
            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                    <a class="btn btn-link p-a-0" role="button"
                    data-container="body" data-toggle="popover"
                    data-placement="right" data-content="&lt;div class=&quot;no-overflow&quot;&gt;&lt;p&gt;The course end date is only used for reports. Users can still enter the course after the end date.&lt;/p&gt;
                    &lt;/div&gt; "
                    data-html="true" tabindex="0" data-trigger="focus">
                    <i class="icon fa fa-question-circle text-info fa-fw " aria-hidden="true" title="Help with Course end date" aria-label="Help with Course end date"></i>
                    </a>
                    </span>
                    <label class="col-form-label d-inline " for="id_enddate">
                        Course end date
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="date_selector">
                    <span class="fdate_selector">
                        <div class="form-group  fitem  ">
                <label class="col-form-label sr-only" for="id_enddate_day">
                    Day 
                </label>
                <span data-fieldtype="select">
                <select class="custom-select"
                    name="enddate[day]"
                    id="id_enddate_day">
                    <option value="1"   >1</option>
                    <option value="2"   >2</option>
                    <option value="3"   >3</option>
                    <option value="4"   >4</option>
                    <option value="5"   >5</option>
                    <option value="6"   >6</option>
                    <option value="7"   >7</option>
                    <option value="8"   >8</option>
                    <option value="9"   >9</option>
                    <option value="10"   >10</option>
                    <option value="11"   >11</option>
                    <option value="12"   >12</option>
                    <option value="13"   >13</option>
                    <option value="14"   >14</option>
                    <option value="15"   >15</option>
                    <option value="16"   >16</option>
                    <option value="17"   >17</option>
                    <option value="18"   >18</option>
                    <option value="19"   >19</option>
                    <option value="20"   >20</option>
                    <option value="21"   >21</option>
                    <option value="22"   >22</option>
                    <option value="23"   >23</option>
                    <option value="24"   >24</option>
                    <option value="25"   >25</option>
                    <option value="26"   >26</option>
                    <option value="27"   >27</option>
                    <option value="28"   >28</option>
                    <option value="29"   >29</option>
                    <option value="30"   >30</option>
                    <option value="31" selected >31</option>
                </select>
                </span>
                <div class="form-control-feedback" id="id_error_enddate[day]"  style="display: none;">
                    
                </div>
            </div>
                        &nbsp;
                        <div class="form-group  fitem  ">
                <label class="col-form-label sr-only" for="id_enddate_month">
                    Month
                </label>
                <span data-fieldtype="select">
                <select class="custom-select"
                    name="enddate[month]"
                    id="id_enddate_month">
                    <option value="1"   >January</option>
                    <option value="2"   >February</option>
                    <option value="3"   >March</option>
                    <option value="4"   >April</option>
                    <option value="5"   >May</option>
                    <option value="6"   >June</option>
                    <option value="7"   >July</option>
                    <option value="8"   >August</option>
                    <option value="9"   >September</option>
                    <option value="10"  >October</option>
                    <option value="11"  >November</option>
                    <option value="12" selected >December</option>
                </select>
                </span>
                <div class="form-control-feedback" id="id_error_enddate[month]"  style="display: none;">
                    
                </div>
            </div>
                        &nbsp;
                        <div class="form-group  fitem  ">
                <label class="col-form-label sr-only" for="id_enddate_year">
                    Year
                </label>
                <span data-fieldtype="select">
                <select class="custom-select"
                    name="enddate[year]"
                    id="id_enddate_year">
                    <option value="1980"   >1980</option>
                    <option value="1981"   >1981</option>
                    <option value="1982"   >1982</option>
                    <option value="1983"   >1983</option>
                    <option value="1984"   >1984</option>
                    <option value="1985"   >1985</option>
                    <option value="1986"   >1986</option>
                    <option value="1987"   >1987</option>
                    <option value="1988"   >1988</option>
                    <option value="1989"   >1989</option>
                    <option value="1990"   >1990</option>
                    <option value="1991"   >1991</option>
                    <option value="1992"   >1992</option>
                    <option value="1993"   >1993</option>
                    <option value="1994"   >1994</option>
                    <option value="1995"   >1995</option>
                    <option value="1996"   >1996</option>
                    <option value="1997"   >1997</option>
                    <option value="1998"   >1998</option>
                    <option value="1999"   >1999</option>
                    <option value="2000"   >2000</option>
                    <option value="2001"   >2001</option>
                    <option value="2002"   >2002</option>
                    <option value="2003"   >2003</option>
                    <option value="2004"   >2004</option>
                    <option value="2005"   >2005</option>
                    <option value="2006"   >2006</option>
                    <option value="2007"   >2007</option>
                    <option value="2008"   >2008</option>
                    <option value="2009"   >2009</option>
                    <option value="2010"   >2010</option>
                    <option value="2011"   >2011</option>
                    <option value="2012"   >2012</option>
                    <option value="2013"   >2013</option>
                    <option value="2014"   >2014</option>
                    <option value="2015"   >2015</option>
                    <option value="2016"   >2016</option>
                    <option value="2017" selected >2017</option>
                    <option value="2018"   >2018</option>
                    <option value="2019"   >2019</option>
                    <option value="2020"   >2020</option>
                    <option value="2021"   >2021</option>
                    <option value="2022"   >2022</option>
                    <option value="2023"   >2023</option>
                    <option value="2024"   >2024</option>
                    <option value="2025"   >2025</option>
                    <option value="2026"   >2026</option>
                    <option value="2027"   >2027</option>
                    <option value="2028"   >2028</option>
                    <option value="2029"   >2029</option>
                    <option value="2030"   >2030</option>
                    <option value="2031"   >2031</option>
                    <option value="2032"   >2032</option>
                    <option value="2033"   >2033</option>
                    <option value="2034"   >2034</option>
                    <option value="2035"   >2035</option>
                    <option value="2036"   >2036</option>
                    <option value="2037"   >2037</option>
                    <option value="2038"   >2038</option>
                    <option value="2039"   >2039</option>
                    <option value="2040"   >2040</option>
                    <option value="2041"   >2041</option>
                    <option value="2042"   >2042</option>
                    <option value="2043"   >2043</option>
                    <option value="2044"   >2044</option>
                    <option value="2045"   >2045</option>
                    <option value="2046"   >2046</option>
                    <option value="2047"   >2047</option>
                    <option value="2048"   >2048</option>
                    <option value="2049"   >2049</option>
                    <option value="2050"   >2050</option>
                    <option value="2050"   >2051</option>
                    <option value="2050"   >2052</option>
                    <option value="2050"   >2053</option>
                    <option value="2050"   >2054</option>
                    <option value="2050"   >2055</option>
                    <option value="2050"   >2056</option>
                    <option value="2050"   >2057</option>
                    <option value="2050"   >2058</option>
                    <option value="2050"   >2059</option>
                    <option value="2050"   >2060</option>
                    <option value="2050"   >2061</option>
                    <option value="2050"   >2062</option>
                    <option value="2050"   >2063</option>
                    <option value="2050"   >2064</option>
                    <option value="2050"   >2065</option>
                    <option value="2050"   >2066</option>
                    <option value="2050"   >2067</option>
                    <option value="2050"   >2068</option>
                    <option value="2050"   >2069</option>
                    <option value="2050"   >2070</option>
                    <option value="2050"   >2071</option>
                    <option value="2050"   >2072</option>
                    <option value="2050"   >2073</option>
                    <option value="2050"   >2074</option>
                    <option value="2050"   >2075</option>
                    <option value="2050"   >2076</option>
                    <option value="2050"   >2077</option>
                    <option value="2050"   >2078</option>
                    <option value="2050"   >2079</option>
                    <option value="2050"   >2080</option>
                </select>
                </span>
                <div class="form-control-feedback" id="id_error_enddate[year]"  style="display: none;">
                    
                </div>
            </div>
                    &nbsp;
                    <a class="visibleifjs" name="enddate[calendar]" href="#" id="id_enddate_calendar"><i class="icon fa fa-calendar fa-fw " aria-hidden="true" title="Calendar" aria-label="Calendar"></i></a>
                    </span>
                    <div class="form-control-feedback" id="id_error_"  style="display: none;">
                        
                    </div>
                </div>
            </div>
            -->

			<div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <a class="btn btn-link p-a-0" role="button"
                        data-container="body" data-toggle="popover"
                        data-placement="right" data-content="&lt;div class=&quot;no-overflow&quot;&gt;&lt;p&gt;The course summary is displayed in the list of courses. A course search searches course summary text in addition to course names.&lt;/p&gt;&lt;/div&gt; "
                        data-html="true" tabindex="0" data-trigger="focus">
                        <i class="icon fa fa-question-circle text-info fa-fw " aria-hidden="true" title="Help with Course summary" aria-label="Help with Course summary"></i>
                        </a>
                    </span>
                    <label class="col-form-label d-inline " for="id_summary_editor">
                        Course summary
                    </label>
                </div>
                <div class="col-md-9 form-inline felement" data-fieldtype="editor">
                    <div>
                        <textarea id="id_summary_editor" name="summary_editor[text]" class="form-control" rows="5" cols="80" spellcheck="true" ></textarea>
                    </div>
                    <div class="form-control-feedback" id="id_error_summary_editor"  style="display: none;">
                    </div>
                </div>
            </div>
			
			<input type="hidden" name="fname" value="<?php echo $fw_shortname; ?>"/>
			<input type="hidden" name="fid" value="<?php echo $fw_id; ?>"/>
			<input class="btn btn-info" type="submit" name="save" value="Save"/>
		</form>
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
		<br />
		<div class="fdescription required">There are required fields in this form marked <i class="icon fa fa-exclamation-circle text-danger fa-fw " aria-hidden="true" title="Required field" aria-label="Required field"></i>.</div>
						
		<?php 
            echo $OUTPUT->footer();
        ?>
        
            <script src="./script/datepicker/wbn-datepicker.min.js"></script>
            <script type="text/javascript">
              $(function () {
                $('.wbn-datepicker').datepicker()
          
                var $jsDatepicker = $('#value-specified-js').datepicker()
                $jsDatepicker.val('2017-05-30')
              })
            </script>
        <?php
	}
    else
    {?>
        <h3 style="color:red;"> Invalid Selection </h3>
        <a href="./select_frameworktoCourse.php">Back</a>
    	<?php
        echo $OUTPUT->footer();
    }?>
