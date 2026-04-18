<?php
    session_start();
	
	$_server = (object) [
		"site_name" => "Hvatitation",
		"page_title" => "",
		"page_description" => "",
		"dev_mode" => false,
		
		"db" => (object) [
			"db_server" => "localhost",
			"db_user" => "root",
			"db_pass" => "",
			"db_name" => "hvatitation",
		],
		
		"ui" => (object) [
			"main" => "#e8ca94",
			"secondary" => "#bda477",
			"text" => "#333",
		],
		
		"webhook" => "",
		
		"recaptcha_public" => "",
		"recaptcha_private" => "",
	];
	
	try {
		$conn = mysqli_connect(
			$_server->db->db_server, 
			$_server->db->db_user, 
			$_server->db->db_pass, 
			$_server->db->db_name 
		);
	}
	
	catch(mysqli_sql_exception){
		if($_server->dev_mode == true) {
			echo "Error connecting to database. Below is the error reported by the server.<br>" . mysqli_connect_error();
		}
	}
?>