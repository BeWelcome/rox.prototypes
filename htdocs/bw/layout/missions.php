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


require_once ("menus.php");

function DisplayMissions() {
	global $title;
	$title = ww('MissionsPage');
	require_once "header.php";
	Menu1("missions.php", ww('MissionsPage')); // Displays the top menu
	Menu2("aboutus.php", ww('GetAnswers')); // Displays the second menu

	echo "\n";
	echo "    <div id=\"main\">\n";
	echo "      <div id=\"teaser_bg\">\n";
	echo "      <div id=\"teaser\">\n";
	echo "        <h1>", $title, " </h1>\n";
	echo "      </div>\n";

	// menugetanswers("missions.php" . $menutab, $title);
	menugetanswers("missions.php", $title);
	
	echo "      </div>\n";
//	ShowLeftColumn($MenuAction)  ; // Show the Actions
	ShowAds(); // Show the Ads

	// Content with just two columns
	echo "\n";
	echo "      <div id=\"col3\" class=\"twocolumns\">\n";
	echo "        <div id=\"col3_content\" class=\"clearfix\">\n";

	echo "<div class=\"info\">\n";
	echo "<h3>", ww("OurMission"),"</h3>";
	echo "<q>",ww("OurMissionQuote"),"</q>";
	echo "<p>",ww("OurMissionText"),"</p>";
	echo "<h3>", ww("OurAim"),"</h3>";
	echo "<p>",ww("OurAimText"),"</p>";
	echo "</div>\n";	
	
	require_once "footer.php";
}
?>