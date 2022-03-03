<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class MainModel extends CI_Model {
	public function __construct() {
		parent::__construct();
	}

	public function updateUserAvatar($userID,$displayName,$email,$imagePath){
		if ($this->db->query("UPDATE users SET email='$email', username='$displayName', avatarURL='$imagePath' WHERE id='$userID'")){ return array("message"=>"Avatar changed successfully!","result"=>true); }
		else{ return array("message"=>"This is already existed","result"=>false); }
	}

	public function getUserData($myUserID){
		return $this->db->query("SELECT id, username, email, avatarURL FROM users WHERE id='$myUserID'")->first_row();
	}

    public function getAllContacts($userID){
        $userName=$this->db->query("SELECT * FROM users WHERE id='$userID'")->first_row()->username;
        return $this->db->query("SELECT c.contactName, c.email, u.id as contactUserID FROM contacts c JOIN users u ON u.email=c.email WHERE c.username='$userName'")->result();
    }
	
	public function addContact($userID,$contactName,$email){
	    $userName=$this->db->query("SELECT * FROM users WHERE id='$userID'")->first_row()->username;
	    if($this->db->query("SELECT * FROM users WHERE email='$email'")->num_rows()==0){
	        if($this->db->query("SELECT * FROM contacts WHERE email=''")->num_rows()==0){
	            if($this->db->query("INSERT INTO contacts(username, contactName, email) VALUES ('$userName','$contactName','$email')")){
	                if($this->db->query("INSERT INTO users (username,password,email) VALUES('$contactName','blank','$email') ")){
	                    return array("message"=>"Contact Added","result"=>false);
	                }
	                else{ return array("message"=>"Database Error","result"=>false); }
    	        }
    	        else{ return array("message"=>"Database Error","result"=>false); }
	        }
	        else{
                return array("message"=>"This is already existed","result"=>false);
	        }
	    }
	    else{
	        $contactUsername=$this->db->query("SELECT * FROM users WHERE email='$email'")->first_row()->username;
	        if($this->db->query("SELECT * FROM contacts WHERE email=''")->num_rows()==0){
	            if($this->db->query("INSERT INTO contacts(username, contactName, email) VALUES ('$userName','$contactName','$email')")){
	                return array("message"=>"Contact Added","result"=>false);
	            }
	            else{ return array("message"=>"Database Error","result"=>false); }	            
	        }
	        else{
                return array("message"=>"This is already existed","result"=>false);
	        }
	    }
	}

    public function login($username,$password){
    	$encoded=md5($password);
	    if($this->db->query("SELECT * FROM users WHERE email='$username' AND password='$encoded'")->num_rows()==1){
            $userID=$this->db->query("SELECT * FROM users WHERE email='$username' AND password='$encoded'")->first_row()->id;
            return array("message"=>"Logged In","result"=>true,"userID"=>$userID);
	    }
	    else{
		    return array("message"=>"Incorrect Username and / or Password","result"=>false);
	    }
	}
    
    

	public function signUp($username,$password,$email){
		$encoded=md5($password);
	    if($this->db->query("SELECT * FROM users WHERE username='$username' AND email='$email'")->num_rows()==0){
            if ($this->db->query("INSERT INTO users (username, password, email) VALUES ('$username','$encoded','".$email."')")) { return array("message"=>"Signed Up Successfully","result"=>true); }
		    else{ return array("message"=>"Database Error; Please Try again","result"=>false); }	        
	    }
	    else{
		    return array("message"=>"Logged In","result"=>true);
	    }
	}
}