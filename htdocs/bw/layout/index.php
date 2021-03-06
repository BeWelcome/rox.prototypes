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



/**
 * Functions displaying key areas of the BW page
 * 
 * @author		(unknown)
 * @copyright	2007, BeVolunteer
 * @license		http://opensource.org/licenses/gpl-license.php GNU General Public License Version 2
 * 
 */

require_once ("menus.php");

/**
 * Overall responsible for displaying the default BW page
 * 
 * Does an explicit exit after displaying the footer. 
 * 
 * @author		(unknown)
 * @param		
 * @return		
 * 
 */
function DisplayIndex() {
	global $title;
	$title = ww("IndexPageTitle"); // this for google to find us more easely
	require_once "header.php";

	Menu1("", $title); // Displays the top menu
	Menu2("index.php", ww('MainPage')); // Displays the second menu

	DisplayHeaderIndexPage ("BeWelcome");
?>
	
  
      <!-- MAIN right column -->
      <div id="col2" class="index"> 
          <div id="col2_content" class="clearfix">
		<div id="content"> 
<?php
	  if (IsLoggedIn()) {
	ShowAds(); // Show the Ads
	  }
	  else {
?>		
              <div class="info index"> 
<form name="login" method="POST" action="login.php">
<h3><?php echo ww("Login"); ?></h3>
<input type="hidden" name="action" value="login">
<input type="hidden" name="nextlink" value="main.php?action">
<p><?php echo ww("Username"); ?><br /><input name="Username" id="username" type="text" value=""><br /></p>
<p><?php echo ww("password"); ?><br /><input type="password" id="password" name="password"><br /></p>
<p><input type="submit" id="submit" value="<?php echo ww("IndexPageLoginSubmit"); ?>"></p>
<p><?php echo ww("IndexPageWord18"); ?></a>
</p>
</form>
<script type="text/javascript">document.login.Username.focus();</script>
<h3><?php  echo ww("SignupNow");?></h3>
<p><?php  echo ww("IndexPageWord17");?></p>
				</div>
<?php } ?>
			</div>
		  </div>
      </div>
      <!-- MAIN middle column -->
      <div id="col3" class="index"> 
	      <div id="col3_content" class="clearfix">
		  
<div class="subcolumns">
  <div class="c50l">
    <div class="subcl">
		<div id="content"> 
              <div class="info index" id=\"langbox\"> 
               <div class="floatbox"><img src="images/index_find.gif" alt="Find" />
			   <h3><?php  echo ww("IndexPageWord3");?></h3>
			   </div>
				<p><?php  echo ww("IndexPageWord4");?></p>

			  
<?php			  
			  
echo "\n<div class=\"floatbox\"><img src=\"images/index_meet.gif\" alt=\"Home\" />
			   <h3>".ww("IndexPageWord19")."</h3>
			   </div>\n"; 
echo "<p>".ww("ToChangeLanguageClickFlag")."</p>";

// Just add add the bottom the language switch trick
DisplayFlag("en","en.png","English");
DisplayFlag("fr","fr.png","French");
DisplayFlag("esp","esp.png","Espanol");
DisplayFlag("de","de.png","Deutsch");
DisplayFlag("it","it.png","Italian");
DisplayFlag("ru","ru.png","Russian");
DisplayFlag("espe","espe.png","Esperanto");
DisplayFlag("pl","pl.png","Polish");
DisplayFlag("tr","tr.png","Turkish");
DisplayFlag("lt","lt.png","Lithuanian");
DisplayFlag("nl","nl.png","Dutch");
DisplayFlag("dk","dk.png","Danish");
DisplayFlag("cat","cat.png","Catalan");
DisplayFlag("fi","fi.png","Finnish");
DisplayFlag("pt","pt.png","Portuguese");
DisplayFlag("hu","hu.png","Hungarian");

//if ($_SESSION['switchtrans']!='on') echo "<a href=\"",$langurl,"switchtrans=off\"><img border=0 height=10 src=\"images/showtransarray.gif\" alt=\"switch to translation mode\" width=16></a>&nbsp;";
if (isset($_SESSION['switchtrans'])
	&& $_SESSION['switchtrans'] == 'on') {
	//  echo "<a href=\"",$langurl,"switchtrans=off\"><img border=0 height=10 src=\"images/showtransarray.gif\" alt=\"remove translation mode\" width=16></a>&nbsp;";
	$pagetotranslate = $_SERVER['PHP_SELF'];
	if ($pagetotranslate { 0 }	== "/")
	   $pagetotranslate { 0 }= "_";
	echo "  <a href=\"admin/adminwords.php?showtransarray=1&pagetotranslate=" . $pagetotranslate . "\" target=new><img border=0 height=10 src=\"images/switchtrans.gif\" title=\"go to current translation list for " . $_SERVER['PHP_SELF'] . "\" width=16></a>\n";
}
?>			  
			    </div>
			  
            </div>
    </div>
  </div>

  <div class="c50r">
    <div class="subcr">
		<div id="content"> 
              <div class="info index"> 
               <div class="floatbox"><img src="images/index_home.gif" alt="Home" />
			   <h3><?php  echo ww("IndexPageWord9");?></h3>
			   </div>
				<p><?php  echo ww("IndexPageWord10");?></p>
               <div class="floatbox"><img src="images/index_meet.gif" alt="Home" />
			   <h3><?php  echo ww("IndexPageWord11");?></h3>
			   </div>
				<p><?php  echo ww("IndexPageWord12");?></p>
				
              </div>
          </div>
    </div>
  </div>
</div>

<!-- Next row -->

<div class="subcolumns">
  <div class="c50l">
    <div class="subcl">
		<div id="content"> 
              <div class="info index"> 
				<h3><?php  echo ww("IndexPageWord5");?></h3>
				<p><?php  echo ww("IndexPageWord6");?></p>
				<h3><?php  echo ww("IndexPageWord7");?></h3>
				<p><?php  echo ww("IndexPageWord8");?></p>
              </div>
            </div>
    </div>
  </div>

  <div class="c50r">
    <div class="subcr">
		<div id="content"> 
              <div class="info index"> 
			   <h3><?php  echo ww("IndexPageWord13");?></h3>
				<p><?php  echo ww("IndexPageWord14");?></p>
			   <h3><?php  echo ww("IndexPageWord15");?></h3>
				<p><?php  echo ww("IndexPageWord16");?></p>
              </div>
          </div>
    </div>
  </div>
</div>
            

<?php
	require_once "footer.php";
exit(0);

//	require_once "footer.php";

} // end of DisplayIndex


function DisplayIndexLogged($Username) {
	global $title;
	$title = ww('IndexPage');

	require_once "header.php";

	Menu1("", $title); // Displays the top menu
	Menu2("", ww('MainPage')); // Displays the second menu

	echo "<br><br><br><br><br><br><br><br><center><table width=\"60%\">";
	echo "\n<tr><td colspan=2 align=center>";
	echo "Hello <b>",$Username,"</b>\n";
	echo "</td>";
	echo "</table>\n";
	echo "</center>\n";

	echo "</center>\n";
	require_once "footer.php";
}

function DisplayNotLogged() {
	global $title;
	$title = ww('IndexPage');

	require_once "header.php";

	Menu1("", $title); // Displays the top menu
	Menu2("", $title); // Displays the second menu

    DisplayHeaderIndexPage($title);

	echo "<center><table width=\"60%\">";
	echo "\n<tr><td colspan=2>";
	echo ww("AboutUsText");
	echo "</td>";
	echo "\n<tr align=center><td>";
	echo "<a href=\"login.php\">",ww("Login"),"</a>";
	echo "</td>";
	echo "<td>";
	echo " <a href=\"signup.php\">",ww("Signup"),"</a>";
	echo "</td>\n";
	echo "</table>\n";
	echo "</center>\n";
	require_once "footer.php";
}
?>
