<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class ChatModel extends CI_Model {
	public function __construct() {
		parent::__construct();
	}

   public function listChatsForUser($userID){
    $chats=$this->db->query("SELECT 
        c.id, 
        c.senderID, 
        s.username as senderName, 
        s.avatarURL as senderAvatarURL, 
        c.receiverID,  
        r.username as receiverName, 
        r.avatarURL as receiverAvatarURL, 
        c.chatMessage, 
        c.sentTime 
        FROM chats c 
        JOIN users s ON s.id=c.senderID 
        JOIN users r ON r.id=c.receiverID 
        WHERE c.senderID='$userID' OR c.receiverID='$userID' GROUP BY c.receiverID ORDER BY c.sentTime DESC")->result();
    $opponents=array();
    $opponentIDs=array();
    foreach ($chats as $row) {
        if ($row->senderID==$userID) { 
            if (!in_array($row->receiverID, $opponentIDs)) {
                array_push($opponents,$this->db->query("SELECT * FROM users WHERE id='$row->receiverID'")->first_row());
                array_push($opponentIDs, $row->receiverID);
            }
        }
        else{ 
            if (!in_array($row->senderID, $opponentIDs)) {
                array_push($opponents,$this->db->query("SELECT * FROM users WHERE id='$row->senderID'")->first_row());
                array_push($opponentIDs, $row->senderID);
            }
        }
    }
    return $opponents;
}

public function listChats($opponentIDToServer,$userIDToServer){
    return $this->db->query("SELECT * FROM chats WHERE (senderID='$userIDToServer' AND receiverID='$opponentIDToServer') OR (senderID='$opponentIDToServer' AND receiverID='$userIDToServer')")->result();
}

public function feedResource($myUserID,$opponentID,$targetFilePath,$caption){
    $caption=$this->db->escape_str($caption);
    if($this->db->query("INSERT INTO chats (senderID,receiverID,chatMessage,resourceURL) VALUES('$myUserID','$opponentID','$caption','$targetFilePath')")){
       return array("message"=>"Caption added","result"=>true);
   }
   else{
       return array("message"=>"Unable to send caption","result"=>false);
   }
}

public function feedChat($myUserID,$opponentID,$chatMessage){
    $chatMessage=$this->db->escape_str($chatMessage);
    if($this->db->query("INSERT INTO chats (senderID,receiverID,chatMessage) VALUES('$myUserID','$opponentID','$chatMessage')")){
       return array("message"=>"Text sent","result"=>true);
   }
   else{
       return array("message"=>"Unable to send text","result"=>false);
   }
}

}