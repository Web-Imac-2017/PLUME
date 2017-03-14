<?php
	header('Access-Control-Allow-Origin:*');
	header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
	header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
	header('Content-Type: application/json;charset=utf-8');

	include "../../Models/ConversationModel.php";

    $id_conv = "";
    $pseudo = "";
    $data = array();

    $json = json_decode(file_get_contents('php://input'), true);
    if(!is_array($json)) $data = array("Error", "Error: POST.");
    else {
        if(isset($json['id']) && $json['id'] != ''
          && isset($json['pseudo']) && $json['pseudo'] != ''){
            $id_conv = $json['id'];
            $pseudo = $json['pseudo'];
            $data["messages"] = ConversationModel::getAllMessagesOfConv($id_conv);
            
            /*get pther user*/
            $id_user = UserModel::getUserId($pseudo);
            $data["users"] = ConversationModel::getOtherUsers($id_user, $id_conv);
            
            /*also add current user to the array of all users of conv*/
            $current_user = array();
            $current_user["pseudo"] = $pseudo;
            array_push($data["users"], $current_user);

            $data["id"] = $id_conv;
            
            /*add last message id*/
            if(!empty($data["messages"])){
                $last = count($data["messages"])-1;
                $data["last_message"] = $data[$last]['ID'];
            }
        }
        else{
            $data = array("Error", "Error: the conversation id doesn't exist.");
        }
    }

  echo json_encode($data);

?>