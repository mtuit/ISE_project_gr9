<?php
include '../includes/database_functions.php';
session_start();
$monkeys =$_GET["q"];
$monkeys = ltrim($monkeys, "@");
$monkeys = ltrim($monkeys, "[");
$monkeys = rtrim($monkeys, "]");
$array= array();
$array= explode( '][', $monkeys );
$currentResearch=$_session['onderzoek']

db_open();

foreach ($array as $monkey) {
		
	db_query("insert into aapInOnderzoek(aap_id, onderzoek_id) values(". $monkey . ",". $currentResearch . ")");
	
}

db_close();



?>