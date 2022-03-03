<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Contact extends CI_Controller {
	public function __construct($config="rest") {
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
		parent::__construct();
		$this->load->model('MainModel');
	}
	
	public function newContact(){
		$this->sendJson($this->MainModel->addContact($_POST['userID'],$_POST['contactNameToServer'],$_POST['contactMobileToServer']));
	}
	public function getAllContacts(){
	    $this->sendJson($this->MainModel->getAllContacts($_GET['userID']));
	}
	private function sendJson($data) {
		$this->output->set_header('Content-Type: application/json; charset=utf-8')->set_output(json_encode($data));
	}
}
