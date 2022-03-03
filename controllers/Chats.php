<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Chats extends CI_Controller {
	public function __construct($config="rest") {
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
		parent::__construct();
		$this->load->model('MainModel');
		$this->load->model('ChatModel');
	}

	public function uploadresourceforchat(){
		if(!$_FILES) { $this->sendJson(array("message"=>"No File Selected","result"=>false)); }
		else if(empty($_POST['userID'])||empty($_POST['opponentID'])){
			$this->sendJson(array("message"=>"Logout and Login again..!","result"=>false));
		}
		else{
			$userID=$_POST['userID'];
			$opponentID=$_POST['opponentID'];
			$caption=$_POST['caption'];
			$result=$this->MainModel->getUserData($userID);
			if (!empty($result)) {
				$uploadedResource=$_FILES["fileToUpload"]["name"];
				$resourceExtension=strtolower(pathinfo($uploadedResource,PATHINFO_EXTENSION));
				$tm=time();
				$targetFileName="chatResource_".$userID."_".$opponentID."_".$tm.".".$resourceExtension;
				$targetFilePath="public/images/chatAttachments/".$targetFileName;
				if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],$targetFilePath)){ 
					$this->sendJson($this->ChatModel->feedResource($userID,$opponentID,$targetFilePath,$caption));
				}
				else{
					$this->sendJson(array("message"=>"Sorry; Unable to upload the file!","result"=>false));
				}
			}
			else{ $this->sendJson(array("message"=>"Sorry; The user not found!","result"=>false)); }				
		}
	}


	public function listChatsForUser(){
		$this->sendJson($this->ChatModel->listChatsForUser($_GET['userID']));
	}

	public function getChatsBetween(){
		$this->sendJson($this->ChatModel->listChats($_GET['opponentIDToServer'],$_GET['userIDToServer']));
	}

    public function feedChat(){
        $this->sendJson($this->ChatModel->feedChat($_POST['myUserID'],$_POST['opponentID'],$_POST['chatMessage']));
    }

	private function sendJson($data) {
		$this->output->set_header('Content-Type: application/json; charset=utf-8')->set_output(json_encode($data));
	}
}
