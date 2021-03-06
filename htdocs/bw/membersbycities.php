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
	$url="../places" ;
//	die ("<a href='".$url."'>here</a><br>") ;
	header ("location: $url") ;
	LogStr("redirection of ".$url, "old BW");
	exit(0) ;

	require_once "lib/init.php";

$action = GetParam("action");

switch ($action) {
}

// prepare the countries list
$str = "select members.id as IdMember,members.Username as Username,countries.id as id,countries.Name as CountryName,regions.Name as RegionName,cities.Name as CityName,members.ProfileSummary  from countries,members,cities,regions where members.IdCity=cities.id and members.Status='Active' and cities.IdRegion=regions.id and countries.id=cities.IdCountry and cities.id=".GetParam("IdCity")."  and members.Status='Active' order by countries.id,regions.id,cities.id ";
$qry = mysql_query($str);
$TList = array ();
while ($rWhile = mysql_fetch_object($qry)) {
	if (!IsLoggedIn()) {
	   if (!IsPublic($rWhile->IdMember)) {
	   	  $rWhile->Username="not public profile";
		  continue ; // skip public profile, don't show them 
	   } 
	}
	$rWhile->ProfileSummary=FindTrad($rWhile->ProfileSummary,true);
   $photo=LoadRow("select SQL_CACHE * from membersphotos where IdMember=" . $rWhile->IdMember . " and SortOrder=0");
	if (isset($photo->FilePath)) $rWhile->photo=$photo->FilePath; 
	array_push($TList, $rWhile);
}

require_once "layout/membersbycities.php";
$where=LoadRow("select cities.Name as CityName,cities.id as IdCity,countries.Name as CountryName,regions.Name as RegionName,cities.IdRegion as IdRegion,countries.id as IdCountry from (countries,cities) left join regions on cities.IdRegion=regions.id where cities.id=".GetParam("IdCity")." and cities.IdCountry=countries.id"); 
DisplayCities($TList,$where); // call the layout with all countries
?>