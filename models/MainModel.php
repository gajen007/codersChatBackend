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

    public function getAllContactsAddedByThisUser($userID){
        return $this->db->query("SELECT c.id as relationid,	c.useridOfContact, c.contactName, u.avatarURL FROM contacts c JOIN users u ON u.id=c.useridOfContact WHERE c.addedByUsedID='$userID'")->result();
    }
	
    public function addContact($userID,$contactName,$email){
    	$dummy=md5("dummy");
	    if($this->db->query("SELECT * FROM users WHERE email='$email'")->num_rows()==0){// newly added contact is not using the app yet
	    	if ($this->db->query("INSERT INTO users (username, password, email) VALUES ('$contactName','$dummy','$email')")) {
	    		$userIDofContact=$this->db->query("SELECT * FROM users WHERE email='$email'")->first_row()->id;
	    		if($this->db->query("SELECT * FROM contacts WHERE addedByUsedID='$userID' AND useridOfContact='$userIDofContact'")->num_rows()==0){
	    			if($this->db->query("INSERT INTO contacts(useridOfContact, contactName,addedByUsedID) VALUES ('$userIDofContact','$contactName','$userID')")){
	    				return array("message"=>"Contact Added","result"=>false);
	    			}
	    			else{ return array("message"=>"Database Error","result"=>false); }	            
	    		}
	    		else{
	    			return array("message"=>"This Contact is already existed","result"=>false);
	    		}
	    	}
	    	else{
	    		return array("message"=>"Unable to add new-contact!","result"=>false);
	    	}
	    }
	    else{
	    	$userIDofContact=$this->db->query("SELECT * FROM users WHERE email='$email'")->first_row()->id;
	    	if($this->db->query("SELECT * FROM contacts WHERE addedByUsedID='$userID' AND useridOfContact='$userIDofContact'")->num_rows()==0){
	    		if($this->db->query("INSERT INTO contacts(useridOfContact, contactName, email) VALUES ('$useridOfContact','$contactName','$email')")){
	    			return array("message"=>"Contact Added","result"=>false);
	    		}
	    		else{ return array("message"=>"Database Error","result"=>false); }	            
	    	}
	    	else{
	    		return array("message"=>"This Contact is already existed","result"=>false);
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
		if ($this->db->query("SELECT * FROM users WHERE email='$email'")->num_rows()==0) {
			if ($this->db->query("INSERT INTO users (username, password, email) VALUES ('$username','$encoded','".$email."')")) { return array("message"=>"Signed Up Successfully","result"=>true); }
				else{ return array("message"=>"Database Error; Please Try again","result"=>false); }	        
			}
			else{
				$userIDofContact=$this->db->query("SELECT * FROM users WHERE email='$email'")->first_row()->id;
				if ($this->db->query("UPDATE users SET password='$encoded', username='$username' WHERE id='$userIDofContact'")) { return array("message"=>"Signed Up Successfully","result"=>true); }
				else{ return array("message"=>"Database Error; Please Try again","result"=>false); }	        
			}
		}
}