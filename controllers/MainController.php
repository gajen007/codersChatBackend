<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class MainController extends CI_Controller {
	public function __construct($config="rest") {
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
		parent::__construct();
		$this->load->model('MainModel');
	}

	public function getUserData(){
		$this->sendJson($this->MainModel->getUserData($_GET['myUserID']));
	}

    public function index(){
        echo "backend";
    }
    
    public function login(){
		$this->sendJson($this->MainModel->login($_POST['unToServer'],$_POST['pwToServer']));
	}
    
    public function signUp(){
		$this->sendJson($this->MainModel->signUp($_POST['unToServer'],$_POST['pwToServer'],$_POST['emailToServer']));
	}

	public function updateUserProfile(){
		if(!$_FILES) { $this->sendJson(array("message"=>"No File Selected","result"=>false)); }
		else{
			$result=$this->MainModel->getUserData($_POST['userID']);
			if (!empty($result)) {
				$upload_path = './public/images/userAvatars/'.$_POST['userID'].'/';
				if (!file_exists($upload_path)) { mkdir($upload_path, 0777, true); }
				if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'],$upload_path.$_FILES['fileToUpload']['name'])) {
					$this->sendJson($this->MainModel->updateUserAvatar($_POST['userID'],$_POST['userdisplayName'],$_POST['usermobileNo'],$upload_path.$_FILES['fileToUpload']['name']));
				} else { $this->sendJson(array("message"=>"Upload Error","result"=>false)); }
			} else{ $this->sendJson(array("message"=>"Sorry; THe user not found!","result"=>false)); }
		}
	}
    
	private function sendJson($data) {
		$this->output->set_header('Content-Type: application/json; charset=utf-8')->set_output(json_encode($data));
	}
}
