<?php 
	session_start();
	
	header('Access-Control-Allow-Origin:*');
	header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
	header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
	header('Content-Type: application/json;charset=utf-8');

	include "../../Models/UserModel.php";

	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{	
		$json = json_decode(file_get_contents('php://input'), true);
		if(!is_array($json)) $data = array("Error", "Error: POST.");

		if(isset($json['pseudo'])) {
			$data = UserModel::getUserState($json['pseudo']);
		}
		else {
			$data = [0];
		}
	}
	else $data = array("Error", "Error: POST.");

  echo json_encode($data);

?>