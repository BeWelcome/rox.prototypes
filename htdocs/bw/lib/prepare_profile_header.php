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

function prepareProfileHeader($IdMember,$wherestatus="",$photorank=0) {

	global $_SYSHCVOL;
	if ($wherestatus == "")
		$wherestatus = " and (Status='Active' OR Status='NeedMore' OR Status='Pending')";

	if ((HasRight("Accepter"))or(HasRight("SafetyTeam"))) { // accepter right allow for reading member who are not yet active
  	   	$wherestatus = "";
	}


	$sQuery="select SQL_CACHE * from members where id=" . $IdMember . $wherestatus ;

	// Try to load the member
	$m=LoadRow($sQuery);

	if (!isset ($m->id)) {
	    $errcode = "ErrorNoSuchMember";
		DisplayError(ww($errcode, $IdMember));
		//		bw_error("ErrorMessage=".$ErrorMessage);
		exit (0);
	}

	// manage picture photorank (swithing from one picture to the other)

	$m->profilewarning = "";
	if ($m->Status != "Active") {
	    $m->profilewarning = "WARNING the status of " . $m->Username . " is " . $m->Status;
	}
	// Load photo data
	$photo = "";
	$phototext = "";

	//first try to load the image given by $photorank, and if that doesn't work, load #0
	$str = "select SQL_CACHE * from membersphotos where IdMember=" . $IdMember . " and SortOrder=" . $photorank;
	$rr = LoadRow($str);
	if (!isset ($rr->FilePath) and ($photorank > 0)) {
		$rr = LoadRow("select SQL_CACHE * from membersphotos where IdMember=" . $IdMember . " and SortOrder=0");
	}

	//if the load worked, then set info for the big picture, and for pic_sm2 - the small in the middle
	if (isset ($rr->FilePath) and (!empty($rr->FilePath))) {
		$photo = $rr->FilePath;
		$phototext = FindTrad($rr->Comment);
		$photorank = $rr->SortOrder;
		$m->IdPhoto = $rr->id;
		$m->photo = getthumb($_SYSHCVOL['IMAGEDIR'] . substr($photo,(strrpos($photo,"/"))),80,80);
		$m->pic_sm2 = $m->photo;
	} else { //if nothing was loaded, then set the default picture, i.e. ET
		$m->photo = 	  $Photo=DummyPict($m->Gender,$m->HideGender) ;
		$m->pic_sm1 = 	  $Photo=DummyPict($m->Gender,$m->HideGender) ;
		$m->pic_sm2 = 	  $Photo=DummyPict($m->Gender,$m->HideGender) ;
		$m->pic_sm3 = 	  $Photo=DummyPict($m->Gender,$m->HideGender) ;
		$photorank = 0;
		$phototext = ww("NoPictureProvided");
		$m->IdPhoto = 0; // this to avoid a notice error when searching for a $m->IdPhoto
	}
	
	if (empty($rr->FilePath)) {
	  $photo=DummyPict($m->Gender,$m->HideGender) ;
	  $m->photo = getthumb($_SYSHCVOL['IMAGEDIR'] . substr($photo,(strrpos($photo,"/"))),80,80);
	}

	//set the text and index for the big picture
	$m->photorank = $photorank;
	$m->phototext = $phototext;

	//check if any pictures were loaded - if not, don't touch the other small images
	if ($phototext != ww("NoPictureProvided")){

		//if something was loaded, grab all the images of the member
		$query = "SELECT SQL_CACHE * FROM membersphotos WHERE IdMember=" . $IdMember . " ORDER BY SortOrder ASC";
		$result = sql_query($query);

		$thepush = mysql_fetch_array($result);

		//put all the results into an array
		while ($thepush){
			$imagearray[] = $thepush;
			$thepush = mysql_fetch_array($result);
		}

		//check how many images there are, and set pic_sm1 & 3 accordingly
		switch(count($imagearray)){
			case 0: //should never happen, but just in case
			case 1: //for just one picture, set pic_sm1 & 3 the same as pic_sm2
				$m->pic_sm1 = $m->photo;
				$m->pic_sm3 = $m->photo;
				break;
			case 2: //for two pictures, set the two others to what pic_sm2 is not
				foreach ($imagearray as $imgarray){
					if ($imgarray['id']!=$m->IdPhoto){
						$m->pic_sm1 = getthumb($_SYSHCVOL['IMAGEDIR'] . substr($imgarray['FilePath'],(strrpos($imgarray['FilePath'],"/"))),80,80);
						$m->pic_sm3 = $m->pic_sm1;
					}
				}
				break;
			default:
				for ($i = 0; $i < count($imagearray); $i++ ){
					if ($imagearray[$i]['id']==$m->IdPhoto){
						if (isset($imagearray[$i-1])){
							$m->pic_sm1 = getthumb($_SYSHCVOL['IMAGEDIR'] . substr($imagearray[$i-1]['FilePath'],(strrpos($imagearray[$i-1]['FilePath'],"/"))),80,80);
						} else {
							$m->pic_sm1 = getthumb($_SYSHCVOL['IMAGEDIR'] . substr($imagearray[(count($imagearray)-1)]['FilePath'],(strrpos($imagearray[(count($imagearray)-1)]['FilePath'],"/"))),80,80);
						}
						if (isset($imagearray[$i+1])){
							$m->pic_sm3 = getthumb($_SYSHCVOL['IMAGEDIR'] . substr($imagearray[$i+1]['FilePath'],(strrpos($imagearray[$i+1]['FilePath'],"/"))),80,80);
						} else {
							$m->pic_sm3 = getthumb($_SYSHCVOL['IMAGEDIR'] . substr($imagearray[0]['FilePath'],(strrpos($imagearray[0]['FilePath'],"/"))),80,80);
						}
					}
				}
		}
	}

	$replaceto = (strpos($m->photo,$_SYSHCVOL['IMAGEDIR']) + strlen($_SYSHCVOL['IMAGEDIR']));

	$m->photo = $_SYSHCVOL['WWWIMAGEDIR'] . substr($m->photo,$replaceto);
	$m->pic_sm1 = $_SYSHCVOL['WWWIMAGEDIR'] . substr($m->pic_sm1,$replaceto);
	$m->pic_sm2 = $_SYSHCVOL['WWWIMAGEDIR'] . substr($m->pic_sm2,$replaceto);
	$m->pic_sm3 = $_SYSHCVOL['WWWIMAGEDIR'] . substr($m->pic_sm3,$replaceto);


	// Load geography
	if ($m->IdCity > 0) {
	    $rWhere = LoadRow("select SQL_CACHE cities.latitude as latitude,cities.longitude as longitude,cities.IdCountry as IdCountry,cities.Name as cityname,cities.id as IdCity,countries.Name as countryname,IdRegion,isoalpha2 from cities,countries where countries.id=cities.IdCountry and cities.id=" . $m->IdCity);
		if (!isset($rWhere->IdCountry)) {
			LogStr("in lib/prepare_profile_header.php member <b>".$m->Username."</b> (IdMember=#".$m->id.") (IdCity=#".$m->IdCity.") has no city record","Bug") ;  
		}
		$m->cityname = $rWhere->cityname;
		$m->countryname = $rWhere->countryname;

		$m->regionname=getregionname($rWhere->IdRegion) ;
		$m->IdRegion=$rWhere->IdRegion ;
		$m->IsoCountry=$rWhere->isoalpha2 ;
		$m->IdCountry=$rWhere->IdCountry ;
		$m->Latitude=$rWhere->latitude ;
		$m->Longitude=$rWhere->longitude ;
        }

	// Load nbcomments nbtrust
	$m->NbTrust = 0;
	$m->NbComment = 0;
	$rr = LoadRow("select SQL_CACHE count(*) as cnt from comments where IdToMember=" . $m->id . " and Quality='Good'");
	if (isset ($rr->cnt))
	    $m->NbTrust = $rr->cnt;
	$rr = LoadRow("select SQL_CACHE count(*) as cnt from comments where IdToMember=" . $m->id);
	if (isset ($rr->cnt))
	    $m->NbComment = $rr->cnt;

	if (($m->LastLogin == "11/30/99 00:00:00")or($m->LastLogin == "00/00/00 00:00:00"))
	    $m->LastLogin = ww("NeverLog");
	else
		$m->LastLogin = localdate($m->LastLogin,"%d/%m/%y %Hh%M");

	// Load Age
	$m->age = fage($m->BirthDate, $m->HideBirthDate);

	// Load full name
	$m->FullName = fFullName($m);

	// Load Address data
	$rr = LoadRow("select SQL_CACHE * from addresses where IdMember=" . $m->id, " and Rank=0 limit 1");
	if (isset ($rr->id)) {
	    $m->Address = PublicReadCrypted($rr->HouseNumber, "*") . " " . PublicReadCrypted($rr->StreetName, ww("MemberDontShowStreetName"));
		$m->Zip = PublicReadCrypted($rr->Zip, ww("ZipIsCrypted"));
		$m->IdGettingThere = FindTrad($rr->IdGettingThere);
	}
	else {
			LogStr("in lib/prepare_profile_header.php member <b>".$m->Username."</b> (IdMember=#".$m->id.") has no address record","Bug") ;  
	}
	
	$m->Trad = MOD_user::getTranslations($IdMember);
	$m->CountTrad = count($m->Trad);
	
	$Relations = array ();
	$m->IdContact=0; // there is no note
	$m->IdRelation=0; // there is no special relation
	$m->VerifiedMember="" ;
	if (IsLoggedIn("Pending")) {
		// Loads the v�rification level of the member (if any) 
		$str = "select * from verifiedmembers  where IdVerified=" . $IdMember . " order by Type desc limit 1";
		$rr = LoadRow($str);
		if (isset($rr->id)) {
			$m->VerifiedMember=ww("verifymembers_".$rr->Type) ;
		}


	// Try to load specialrelations and caracteristics belong to
	   $str = "select SQL_CACHE specialrelations.*,members.Username as Username,members.Gender as Gender,members.HideGender as HideGender,members.id as IdMember from specialrelations,members where IdOwner=".$IdMember." and specialrelations.Confirmed='Yes' and members.id=specialrelations.IdRelation and members.Status='Active'";
	   $qry = mysql_query($str);
	   while ($rr = mysql_fetch_object($qry)) {
		  if ((!IsLoggedIn("Pending")) and (!IsPublic($rr->IdMember))) continue; // Skip non public profile is is not logged

		  $rr->Comment=FindTrad($rr->Comment,true);
			$photo=LoadRow("select SQL_CACHE * from membersphotos where IdMember=" . $rr->IdRelation . " and SortOrder=0");
			if (isset($photo->FilePath)) {
				 $rr->photo=$photo->FilePath;
			}
			else {
				 $rr->photo="" ;
			} 
		  array_push($Relations, $rr);
	   }
	   // check if the member is in mycontacts
	   $rr=LoadRow("select SQL_CACHE * from mycontacts where IdMember=".$_SESSION["IdMember"]." and IdContact=".$IdMember);
	   if (isset($rr->id)) {
	   	  $m->IdContact=$rr->id; // The note id
	   }	

	   // check if wether this profile has a special relation with teh current member
	   $rr=LoadRow("select SQL_CACHE * from specialrelations where IdOwner=".$_SESSION["IdMember"]." and IdRelation=".$IdMember);
	   if (isset($rr->IdRelation)) {
	   	  $m->IdRelation=$rr->IdRelation; // there is no special relation
	   }	
	}
	$m->Relations=$Relations;
	
	// Check the number of Forum�Post this member has made
	// todo in future, test it according to reader visibility for this member posts
	$rr=LoadRow("select count(*) as cnt from forums_posts where IdWriter=".$IdMember) ;
	$m->NbForumPosts=$rr->cnt ;

  return($m);
} // end of prepareProfileHeader
?>
