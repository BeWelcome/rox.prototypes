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

switch (GetParam("action")) {

}

$limitcount=GetParam("limitcount",10); // Number of records per page
$start_rec=GetParam("start_rec",0); // Number of records per page

if (IsLoggedIn()) {
	$str = "select SQL_CACHE members.*,cities.Name as cityname,IdRegion,countries.Name as countryname,membersphotos.FilePath as photo,membersphotos.Comment from cities,countries,members left join membersphotos on membersphotos.IdMember=members.id and membersphotos.SortOrder=0 where countries.id=cities.IdCountry and cities.id=members.IdCity and status='Active' GROUP BY members.id order by members.LastLogin desc  limit $start_rec,".$limitcount;
	$rtot=LoadRow("select SQL_CACHE count(*) as cnt from members where status='Active'");
} else { // if not logged in, only use public profile
	$str = "select SQL_CACHE members.*,cities.Name as cityname,IdRegion,countries.Name as countryname,membersphotos.FilePath as photo,membersphotos.Comment from cities,countries,memberspublicprofiles,members left join membersphotos on membersphotos.IdMember=members.id and membersphotos.SortOrder=0 where countries.id=cities.IdCountry and cities.id=members.IdCity and status='Active' and memberspublicprofiles.IdMember=members.id GROUP BY members.id order by members.LastLogin desc  limit $start_rec,".$limitcount." /* in members.php not logged in */"; 
	$rtot=LoadRow("select SQL_CACHE count(*) as cnt from  members,memberspublicprofiles where status='Active' and memberspublicprofiles.IdMember=members.id");
}

$TData = array ();
$qry = sql_query($str);

if (!$qry) {
	LogStr("error in members.php with:<br>".$str,"Bug") ;
}
// MAU counting the max to reach TODO probable bug to fix (need additional query ?)
$maxpos=$rtot->cnt ;

while ($rr = mysql_fetch_object($qry)) {
	if ($rr->Comment > 0) {
		$rr->phototext = FindTrad($rr->Comment);
	} else {
		$rr->phototext = "no comment";
	}

	if ($rr->ProfileSummary > 0) {
		$rr->ProfileSummary = FindTrad($rr->ProfileSummary,true);
	} else {
		$rr->ProfileSummary = "";
	}

   $rr->regionname=getregionname($rr->IdRegion) ;
	
	array_push($TData, $rr);
}

require_once "layout/members.php";
DisplayMembers($TData,$maxpos);
?>
