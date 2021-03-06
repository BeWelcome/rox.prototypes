<?php
/*

Copyright (c) 2007 BeVolunteer

This file is part of BW Rox.

BW Rox is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

BW Rox is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/> or 
write to the Free Software Foundation, Inc., 59 Temple Place - Suite 330, 
Boston, MA  02111-1307, USA.

*/
require_once "lib/init.php";
require_once "layout/error.php";
require_once "lib/FunctionsLogin.php";
require_once "layout/myphotos.php";
require_once "lib/prepare_profile_header.php";


// test if is logged, if not logged and forward to the current page
if (GetStrParam("PictForMember","")!="") {
    $SortPict=GetParam("PictNum",0)	;			  
    $Photo=LoadRow("SELECT membersphotos.*,Username from membersphotos,members,memberspublicprofiles where members.id=".IdMember(GetStrParam("PictForMember"))." and members.id=memberspublicprofiles.IdMember and members.id=membersphotos.IdMember and membersphotos.SortOrder=".$SortPict);
    
    if (!isset($Photo->id)) {
        $Photo=LoadRow("SELECT membersphotos.*,Username from membersphotos,members,memberspublicprofiles where members.id=".IdMember("admin")." and members.id=memberspublicprofiles.IdMember and members.id=membersphotos.IdMember and membersphotos.SortOrder=0");
    }
    $fpath = $Photo->FilePath;
    echo $_SYSHCVOL['SiteName'].$fpath;
    exit(0);
} 

// test if is logged, if not logged and forward to the current page
// exeption for the people at confirm signup state
if (IsLoggedIn("Pending,NeedMore") ) {
        // if there is a IdMember in session (this can because of a memebr in pending state
        $m = prepareProfileHeader($_SESSION['IdMember']," and (Status='Pending' or Status='Active'  or Status='ActiveHidden' or Status='NeedMore')"); // pending members can edit their profile
}
else {
        MustLogIn();
}

// Find parameters
$IdMember = $_SESSION['IdMember'];
if ((IsAdmin())or(CanTranslate(IdMember(GetStrParam("cid", $_SESSION['IdMember']))))) { // admin or CanTranslate can alter other profiles 
	$IdMember = IdMember(GetStrParam("cid", $_SESSION['IdMember']));
}

// manage picture photorank (swithing from one picture to the other)
$photorank = GetParam("photorank", 0);

// recomputes and updates the databases SortOrder fields
// and returns the users' rows of memberphotos
function fix_sort_order() {
  global $IdMember;

  // First recompute order of pictures
  $TData = array ();
  $str = "select * from membersphotos where membersphotos.IdMember=" . $IdMember . " order by SortOrder asc";
  $qry = sql_query($str);
  for ($i=0; $rr = mysql_fetch_object($qry); $i ++) { // Fix Sort numbers
    if ($rr->SortOrder != $i) {
      $str = "update membersphotos set SortOrder=" . $i . " where id=" . $rr->id;
      //				echo "str=$str<br />";
      sql_query($str);
      $rr->SortOrder = $i;
    }

    array_push($TData, $rr);
  }

  return $TData;
}

switch (GetParam("action")) {
	case "update" :
		break;
	case "viewphoto" :
		$Photo=LoadRow("select membersphotos.*,Username from membersphotos,members where members.id=membersphotos.IdMember and membersphotos.id=".GetParam("IdPhoto"));
		$Photo->Comment=FindTrad($Photo->Comment);
		DisplayPhoto($Photo);
		exit(0);
	
	case "moveup" :
		// First recompute order of pictures
	        $TData = fix_sort_order();
		$iPos = GetParam("iPos");
		if ($iPos > 0) { // if not to the up picture
			$str = "update membersphotos set SortOrder=" . $TData[$iPos -1]->SortOrder . " where id=" . $TData[$iPos]->id . " and IdMember=" . $IdMember;
			sql_query($str);
			$str = "update membersphotos set SortOrder=" . $TData[$iPos]->SortOrder . " where id=" . $TData[$iPos -1]->id . " and IdMember=" . $IdMember;
			sql_query($str);
			$TData[$ii]->SortOrder = $ii;
		}
		break;

	case "movedown" :
	        $TData = fix_sort_order();
		$iPos = GetParam("iPos");
		if ($iPos < count($TData) - 1) { // if it is not already the last picture
			$str = "update membersphotos set SortOrder=" . $TData[$iPos +1]->SortOrder . " where id=" . $TData[$iPos]->id . " and IdMember=" . $IdMember;
			sql_query($str);
			$str = "update membersphotos set SortOrder=" . $TData[$iPos]->SortOrder . " where id=" . $TData[$iPos +1]->id . " and IdMember=" . $IdMember;
			sql_query($str);
		}
		break;

	case "deletephoto" :
		$str = "delete from membersphotos where IdMember=" . $IdMember . " and id=" . GetParam("IdPhoto");
		//			echo "str=$str<br />";
		sql_query($str);
		LogStr("delete picture #" . GetParam("IdPhoto"), "update profile");
		fix_sort_order();

		break;

	case "UpLoadPicture";
		if ($_FILES[userfile][error] != "") {
			echo "error ", $_FILES[userfile][error], "<br />";
		}

		LogStr("Upload of file <i>" . $_FILES[userfile][name] . "</i> " . $_FILES[userfile][size] . " bytes", "upload photo");
		$filename = $_FILES[userfile][name];
		$ext = strtolower(strrchr($filename, ".")); //everything after last occurrence of .

		// test format of file
		if (($ext != ".jpg") and ($ext != ".png")) {
			$errcode = "ErrorBadPictureFormat";
			@ unlink($HTTP_POST_FILES[userfile][tmp_name]); // delete erroneous file
			DisplayError(ww($errcode, $ext));
			exit (0);
		}

		// test size of file

		if ($_FILES[userfile][size] >= $_SYSHCVOL['UploadPictMaxSize']) {
			$errcode = "ErrorPictureToBig";
			@ unlink($_FILES[userfile][tmp_name]); // delete erroneous file
			DisplayError(ww($errcode, ($_SYSHCVOL['UploadPictMaxSize'] / 1024)));
			exit (0);
		}

		// Compute a real name for this file
		$fname = fUsername($IdMember) . "_" . time() . $ext; // a uniqe name each time !;

		//			echo "fname=",$fname,"<br />";

		if (@copy($_FILES[userfile][tmp_name], $_SYSHCVOL['IMAGEDIR'] ."/". $fname)) { // try to copy file with its real name
			$str = "insert into membersphotos(FilePath,IdMember,created,SortOrder,Comment) values('" . "/memberphotos/" . $fname . "'," . $IdMember . ",now(),-1," . NewInsertInMTrad(GetStrParam("Comment"),"membersphotos.Comment",0) . ")";
			sql_query($str);
			fix_sort_order();
		} else {
			echo "failed to copy " . $_FILES[userfile][tmp_name] . " to " . $_SYSHCVOL['IMAGEDIR'] . $fname;
		}

		//		  echo "Comment=",GetParam("Comment"),"<br />";
		break;

	case "updatecomment";
		$rr = LoadRow("select Comment,id from membersphotos where IdMember=" . $IdMember . " and id=" . GetParam("IdPhoto"));
		NewReplaceInMTrad(GetStrParam("Comment"),"membersphotos.Comment",$rr->id, $rr->Comment, $IdMember);
		LogStr("Updating comment for picture #" . $rr->id, "update profile");
		break;

}

$TData = array ();
// Try to load groups and caracteristics where the member belong to
$str = "select * from membersphotos  where membersphotos.IdMember=" . $IdMember . " order by SortOrder asc";
$qry = sql_query($str);
$TData = array ();
while ($rr = mysql_fetch_object($qry)) {
	array_push($TData, $rr);
}

$m = prepareProfileHeader($IdMember," and (Status='Active' or Status='Pending' or Status='ActiveHidden' or Status='NeedMore')"); // pending members can edit their profile 

DisplayMyPhotos($m,$TData, $lastaction);
?>
