<?php

/**
 * The main php for showing messages to the user
 *
 * This is the main php used for 
 *
 * @package Messaging
 * @author JY (original page), Fake51 (new version) & Wukk (layout for new version)
 *
 */

require_once "lib/init.php";
require_once "layout/error.php";
require_once "layout/mymessages.php";


MustLogIn();	// test if user is logged in, if not then forward to the current page


$noscript=GetParam("actiontodo");	//if the user doesn't use javascript, $_POST['actiontodo'] should be 'none'
if ($noscript=="none"){			//in that case, the user should have specified an action using the radio buttons
	$actionToDo=GetParam("noscriptaction");		//so fetch that action
} else {
	$actionToDo=$noscript;
}


$from = GetParam("from",0);			//what message to start displaying from, for paged view of messages
$action = GetParam("action","Received");	//action will be empty if user just went to the inbox
						//so, default should be the received messages
$messageArray = array();			//array used to contain the array of messages to show the user
$MessageOrder = GetParam("msgsortorder");	//grab the sort order from the $_GET if it's there
if ($MessageOrder != "") {			//and if it is, then store it
	$_SESSION['MessagesSortOrder'] = $MessageOrder;
}

if (isset($_SESSION['MessagesSortOrder'])){	//check if there is a stored sort order - if so, retrieve it
	$MessageOrder = $_SESSION['MessagesSortOrder'];
} else {
	$MessageOrder = "cD";		//if not, go with a default
}

switch ($MessageOrder){
	case "cD":				//case created DESC
		$MessageOrder = "m1.created DESC";
		break;
	case "cA":				//case created ASC
		$MessageOrder = "m1.created ASC";
		break;
	case "UD":				//case Username DESC
		$MessageOrder = "members.Username DESC";
		break;
	case "UA":				//case Username DESC
		$MessageOrder = "members.Username ASC";
		break;
	case "RD":				//case RepliedTo DESC
		$MessageOrder = "m2.IdParent DESC";
		break;
	case "RA":				//case RepliedTo DESC
		$MessageOrder = "m2.IdParent ASC";
		break;
	default:
		$MessageOrder = "m1.created DESC";
		break;
}


$ShowSingleMsg = 0;		//to test whether we show a msg-listing, or a single message

switch ($action) {		//preliminary switch for the message handling
	case "ViewMsg" :	//this is the case for viewing a single message
		$MsgPageTitle = ww("ShowMessage");
		$MsgToView = GetParam("msg");
		if (empty($MsgToView)){		//this will break if trying to view msg = 0, but that should never happen
			break;			//and if it does, it doesn't matter much that it breaks, as that
		}				//msg is a test msg

		$action = GetParam("menutab");
		if (empty($action)){
			break;
		}

		//update the WhenFirstRead column of the msg, making sure to only update messages TO the member
		$query = "UPDATE messages SET WhenFirstRead=now() WHERE id='$MsgToView' AND IdReceiver='" . $_SESSION["IdMember"] . "'";
		$result= sql_query($query);

		LogStr("Has read message #" . $MsgToView . "readmessage");
		EvaluateMyEvents(); // in order to keep update Not read message counter

		$ShowSingleMsg++;
		break;
	case "MultiMsg":	//this is the case for handling multiple messages
		$action = GetParam("menutab");	//set $action for the next switch - it should equal the folder
		if (empty($action)){	//the user came from ... if it's not set, something went wrong
			break;
		}

		if (isset($_POST["message-mark"])){	//get the set of messages to work with
			$messages = ($_POST["message-mark"]);	//it's an array, so the GetParam would choke on it
			if (!is_array($messages)){		//hence, do it manually for now
				break;			//if it's not an array, something's screwed
			}
		} else {				//and that's the case as well, if the variable is not set
			break;				//so break
		}

		if (empty($actionToDo)){		//with the messages to be set ... otherwise we're still
			break;				//left wondering
		}

		switch($actionToDo){
			case "delmsg":	// this is the case for deleting messages
				foreach($messages as $msg){
					if (is_numeric($msg)){
						$oldmsg = LoadRow("SELECT DeleteRequest, IdSender, IdReceiver FROM messages WHERE id = '$msg'");
						if ($oldmsg->IdSender==$_SESSION["IdMember"]) {
						    	$DeleteRequest="senderdeleted";
						}
						if ($oldmsg->IdReceiver==$_SESSION["IdMember"]) {
							if ($DeleteRequest!="") {
								$DeleteRequest.=",receiverdeleted";
							} else {
				    				$DeleteRequest="receiverdeleted";
							}
						}
						if ($oldmsg->DeleteRequest!=""){
							$DeleteRequest.="," . $oldmsg->DeleteRequest;
						}

						$query="UPDATE messages SET DeleteRequest='$DeleteRequest' WHERE id='$msg'";
						sql_query($query);
						LogStr("Request to delete message #$msg in Tab: $action del message");

					}
				}
				EvaluateMyEvents(); // Recompute nb mail to read
				break;
			case "notspam":	// this is the case for marking messages as "not spam"
				foreach($messages as $msg){
					if (is_numeric($msg)){
						$oldmsg = LoadRow("SELECT messages.SpamInfo,Username FROM messages,members WHERE messages.IdSender=members.id and messages.id='$msg'");
						if ($oldmsg->Username) {
							$tt = explode(",", $oldmsg->SpamInfo);
							$SpamInfo = "NotSpam";
							for ($ii = 0; $ii < count($tt); $ii++) {
								if ($tt[$ii] == "NotSpam"){	// if it is already set, make sure it's the only property set
									break;
								} elseif ($tt[$ii] == "SpamSayMember"){ // pass by spammarks
									continue;
								} elseif ($tt[$ii] == "SpamSayChecker"){ // pass by spammarks
									continue;
								} else {
									$SpamInfo .= "," . $tt[$ii];
								}
							}
							$query = "UPDATE messages SET SpamInfo='$SpamInfo', InFolder='Normal' WHERE id='$msg' and messages.IdReceiver='" . $_SESSION["IdMember"] . "'";
							sql_query($query);
							LogStr("Remove spam mark (".$oldmsg->SpamInfo.") a message from " . $oldmsg->Username . " MesId=#$msg Remove Mark Spam");
						}
					}
				}
				break;
			case "isspam":	// this is the case for marking messages as spam
				foreach($messages as $msg){
					if (is_numeric($msg)){
						$oldmsg = LoadRow("SELECT messages.SpamInfo,Username FROM messages,members WHERE messages.IdSender=members.id and messages.id='$msg'");
						if ($oldmsg->Username) {
							$tt = explode(",", $oldmsg->SpamInfo);
							$SpamInfo = "SpamSayMember";
							for ($ii = 0; $ii < count($tt); $ii++) {
								if ($tt[$ii] == "SpamSayMember"){	// if it is already set, don't set it again
									continue;
								} elseif ($tt[$ii] == "NotSpam"){	 // make sure not spam isn't set
									continue;
								} else {
									$SpamInfo .= "," . $tt[$ii];
								}
							}
							$query = "UPDATE messages SET SpamInfo='$SpamInfo', InFolder='Spam' WHERE id='$msg' and messages.IdReceiver='" . $_SESSION["IdMember"] . "'";
							sql_query($query);
							LogStr("Remove spam mark (".$oldmsg->SpamInfo.") a message from " . $oldmsg->Username . " MesId=#$msg Remove Mark Spam");
							LogStr("Mark as spam a message from " . $oldmsg->Username . " MesId=#$msg Mark Spam");

						}
					}
				}
				break;
		}
}


switch ($action) {		//the main switch for the messages - this decides what happens, based upon $action
	case "Draft":
		$pageTitle = ww("MessagesDraft");	//get proper title of page

		if (substr($MessageOrder,0,11) == "m2.IdParent"){	//check if member tries to sort by replied to
			$MessageOrder = "m1.created DESC";		//that won't be available for Draft
		}

 		$query = "SELECT SQL_CACHE m1.id AS IdMess, Username, Message, m1.created, m1.IdReceiver as OtherUserID FROM members, messages AS m1 WHERE m1.IdSender='" . $_SESSION["IdMember"] . "' and members.id=m1.IdReceiver and m1.Status='Draft' and (not FIND_IN_SET('senderdeleted',m1.DeleteRequest)) ORDER BY " . $MessageOrder;
		$result = sql_query($query);
		while ($rWhile = mysql_fetch_array($result)) {	//grab everything from the query
			$messageArray[] = $rWhile;		//and store it here
		}

		$msgAction = "";
		$menutab = $action;
		break;

	case "Spam":
		$pageTitle = ww("PageSpamFolderTitle");	//get proper title of page

		$query = "SELECT SQL_CACHE m1.id AS IdMess, m1.Spaminfo, members.Username, m1.WhenFirstRead, m1.Message, m1.created, m2.IdParent, m1.IdSender as OtherUserID FROM members,messages AS m1 LEFT JOIN messages AS m2 ON m2.IdParent=m1.id AND m2.IdReceiver=m1.IdSender WHERE m1.IdReceiver='" . $_SESSION["IdMember"] . "' and members.id=m1.IdSender and m1.Status='Sent' and m1.SpamInfo!='NotSpam' and (not FIND_IN_SET('receiverdeleted',m1.DeleteRequest)) ORDER BY " . $MessageOrder;
		$result = sql_query($query);
		while ($rWhile = mysql_fetch_array($result)) {	//grab everything from the query
			$messageArray[] = $rWhile;		//and store it here
		}


		$msgAction = "notspam";
		$menutab = $action;
		break;
	case "Sent":
		$pageTitle = ww("MessagesThatIHaveSent");	//get proper title of page

		if (substr($MessageOrder,0,11) == "m2.IdParent"){	//check if member tries to sort by replied to
			$MessageOrder = "m1.created DESC";		//that won't be available for Draft
		}

 		$query = "SELECT SQL_CACHE m1.id AS IdMess, Username, Message, m1.created, m1.IdReceiver as OtherUserID FROM members, messages AS m1 WHERE m1.IdSender='" . $_SESSION["IdMember"] . "' and members.id=m1.IdReceiver and m1.Status!='Draft' and (not FIND_IN_SET('senderdeleted',m1.DeleteRequest)) ORDER BY " . $MessageOrder;
		$result = sql_query($query);
		while ($rWhile = mysql_fetch_array($result)) {	//grab everything from the query
			$messageArray[] = $rWhile;		//and store it here
		}

		$msgAction = "";
		$menutab = $action;
		break;

	case "Received" :	//this case amounts to the inbox - it also doubles as default
	default:
		$pageTitle = ww("MessagesThatIHaveReceived");	//get proper title of page

		$query = "SELECT SQL_CACHE m1.id AS IdMess, members.Username, m1.WhenFirstRead, m1.Message, m1.created, m2.IdParent, m1.IdSender as OtherUserID FROM members,messages AS m1 LEFT JOIN messages AS m2 ON m2.IdParent=m1.id AND m2.IdReceiver=m1.IdSender WHERE m1.IdReceiver='" . $_SESSION["IdMember"] . "' and members.id=m1.IdSender and m1.Status='Sent' and m1.SpamInfo='NotSpam' and (not FIND_IN_SET('receiverdeleted',m1.DeleteRequest)) ORDER BY " . $MessageOrder;
		$result = sql_query($query);
		while ($rWhile = mysql_fetch_array($result)) {	//grab everything from the query
			$messageArray[] = $rWhile;		//and store it here
		}

		$msgAction = "isspam";
		$menutab = 'Received';
		break;
}


for ($i = 0; $i < count($messageArray); $i++){
	if ($messageArray[$i]['IdMess'] == $MsgToView){
		$ShowSingleMsg++;	//set it back to empty to show that we found the relevant msg
		$MsgToView = $i;
		break;
	}
}

if ($ShowSingleMsg == 2){	//final test to determine action
	$query = "SELECT SQL_CACHE Accomodation, BirthDate, HideBirthDate, FilePath FROM members LEFT JOIN membersphotos ON members.id = membersphotos.IdMember WHERE members.id = '" . $messageArray[$MsgToView]['OtherUserID'] . "'";
	$result = sql_query($query);
	$ExtraDetails = mysql_fetch_array($result);
	$Comments = LoadRow("SELECT SQL_CACHE count(id) as Num FROM comments WHERE IdToMember='" . $messageArray[$MsgToView]['OtherUserID'] . "'");
	$ExtraDetails['NumComments'] = $Comments->Num;

	DisplayAMessage($messageArray, $MsgPageTitle, $menutab, $msgAction, $MsgToView, $ExtraDetails);	//display a single message
} else {
	DisplayMessages($messageArray, $pageTitle, $menutab, $msgAction, $MessageOrder, $from);	//display the messages
}


?>