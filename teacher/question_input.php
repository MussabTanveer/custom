<?php
$ques = $_POST["ques"];
$output=shell_exec("python classifier.py $ques");
echo $output;
?>