<?php
require_once "../lib/init.php";
$title = "Words management";
require_once "../layout/menus.php";

function CheckRLang( $rlang )
{
	if (empty($rlang))
	{
		print_r($rlang);
		bw_error("rlang is empty.");
	}
	if (!isset($rlang->IdLanguage)||$rlang->IdLanguage<0)
	{
		print_r($rlang);echo "<br>" ;
		bw_error(" CheckRLang rlang->IdLanguage empty");
	}
	if (empty($rlang->ShortCode))
	{
		print_r($rlang);
		bw_error("rlang->ShortCode empty");
	}
}

MustLogIn(); // Need to be logged

$lang = $_SESSION['lang']; // save session language
$_SESSION['lang'] = CV_def_lang;
$_SESSION['IdLanguage'] = 0; // force english for menu

require_once "../layout/header.php";

Menu1("", "Admin Words"); // Displays the top menu

Menu2("main.php", "Admin Words"); // Displays the second menu

DisplayHeaderShortUserContent($title);

$scope = RightScope('Words');
$RightLevel = HasRight('Words',$lang); // Check the rights

$scope = RightScope('Words');

$_SESSION['lang'] = $lang; // restore session language
$rr = LoadRow("select * from languages where ShortCode='" . $lang . "'");
$ShortCode = $rr->ShortCode;
$_SESSION['IdLanguage'] = $IdLanguage = $rr->id;

echo "          <div class=\"info highlight\">\n";
echo "            <h2>Your current language is ", " #", $rr->id, "(", $rr->EnglishName, ",", $rr->ShortCode, ") your scope is for $scope </h2>\n";
echo "            <p>\n";
echo "                &nbsp;&nbsp;<a href=".bwlink("admin/adminwords.php").">Admin word</a>\n";
echo "                &nbsp;&nbsp;<a href=".bwlink("admin/adminwords.php?ShowLanguageStatus=". $rr->id)."> All in ", $rr->EnglishName, "</a>\n";
echo "                &nbsp;&nbsp;<a href=".bwlink("admin/adminwords.php?onlymissing&ShowLanguageStatus=". $rr->id)."> Only missing in ", $rr->EnglishName, "</a>\n";
echo "                &nbsp;&nbsp;<a href=".bwlink("admin/adminwords.php?onlyobsolete&ShowLanguageStatus=". $rr->id)."> Only obsolete in ", $rr->EnglishName, "</a>\n";
echo "                &nbsp;&nbsp;<a href=".bwlink("admin/adminwords.php?showstats").">Show stats</a>\n";
echo "            </p>\n";
$Sentence = "";
$code = "";
if (isset ($_GET['code']))
	$code = $_GET['code'];
if (isset ($_GET['Sentence']))
	$Sentence = $_GET['Sentence'];
if ((isset ($_GET['id'])) and ($_GET['id'] != ""))
	$id = $_GET['id'];
if (isset ($_GET['lang']))
	$lang = $_GET['lang'];

if (isset ($_POST['code']))
	$code = $_POST['code'];
if (isset ($_POST['Sentence']))
	$Sentence = $_POST['Sentence'];
if ((isset ($_POST['id'])) and ($_POST['id'] != ""))
	$id = $_POST['id'];
if (isset ($_POST['lang']))
	$lang = $_POST['lang'];

// if it was a show translation on page request
if (isset ($_GET['showstats'])) {
    $rr=LoadRow("select count(*) as cnt from words where IdLanguage=0 and donottranslate!='yes'");
  	$cnt=$rr->cnt;
  	$str="select count(*) as cnt,EnglishName from words,languages where languages.id=words.IdLanguage and donottranslate!='yes' group by words.IdLanguage order by cnt DESC";
  	$qry=sql_query($str);
	echo "<table>\n";
  	while ($rr=mysql_fetch_object($qry)) {
	      echo "<tr><td>",$rr->EnglishName,"</td><td>\n";
    	  printf("%01.1f", ($rr->cnt / $cnt) * 100);
		  echo  "% achieved</td>\n";
  	}
	echo "</table>\n";
}

// If it was a find word request
if ((isset ($_POST['DOACTION'])) and ($_POST['DOACTION'] == 'Find')) {
	if (!empty($_POST['lang'])) {
		 $rlang = LoadRow("select id as IdLanguage,ShortCode from languages where ShortCode='" . $_POST['lang'] . "'");
		 CheckRLang( $rlang );
	}
	$where = "";
	
	if (!empty($_POST['code'])) {
		if ($where != "")
			$where = $where . " and ";
		$where .= " code like '%" . $_POST['code'] . "%'";
	}
	
	if (!empty($_POST['lang'])) {
		if ($where != "")
			$where = $where . " and ";
		$where .= " IdLanguage =" . $rlang->IdLanguage;
	}
	
	if (!empty($_POST['Sentence'])) {
		if ($where != "")
			$where = $where . " and ";
		$where .= " Sentence like '%" . $_POST['Sentence'] . "%'";
	}

	$str = "select * from words where" . $where . " order by id desc";
	$qry = sql_query($str) or die("error " . $str);
	echo "\n<table cellspacing=4>\n";
	$coutfind = 0;
	while ($rr = mysql_fetch_object($qry)) {
		if ($countfind == 0)
			echo "<tr align=left><th>code / Sentence</th><th>Desc</th>\n";
		$countfind++;
		$rEnglish=LoadRow("select * from words where code='".$rr->code."' and IdLanguage=0");
		echo "<tr align=left style=\"font-size:11px;\"><td width=\"50%\"><a href=\"" . $_SERVER['PHP_SELF'] . "?idword=$rr->id\" style=\"font-size:12px;\">",$rr->code," (#",$rr->id,")</a>";
		echo " ",LanguageName($rr->IdLanguage);
		echo "<br>";
		echo "$rr->Sentence</td>";
		echo "<td style=\"font-size:9px; color:gray;\">", $rEnglish->Description,"</td>\n";
	}
	echo "</table>\n";
	if ($countfind == 0)
		echo "<h3><font color=red>", $where, " Not found</font></h3>\n";
   require_once "../layout/footer.php";
	exit(0);
} // end of Find


if ($RightLevel < 1) {
	echo "          <div class=\"info highlight\">\n";
	echo "<h2>This Need the sufficient <b>Words</b> rights for lang=<b>$lang</b> your scope is : $scope</h2>";
	echo "</div>" ;
   require_once "../layout/footer.php";
	exit (0);
}

// if it was a show translation on page request
if (isset ($_GET['showtransarray'])) {

	$count = count($_SESSION['TranslationArray']);
	echo "\n<table cellpadding=3 width=100%><tr bgcolor=#ffccff><th colspan=3 align=center>";
	echo "\nTranslation list for <b>" . $_GET['pagetotranslate'] . "</b>";
	echo "\n</th>";
	echo "\n<tr  bgcolor=#ffccff><th  bgcolor=#ccff99>code</th><th  bgcolor=#ccffff>english</th><th bgcolor=#ffffcc>", $rr->EnglishName, "<a href=".bwlink("admin/adminwords.php?ShowLanguageStatus=$IdLanguage")."> All</a></th>";
	for ($ii = 0; $ii < $count; $ii++) {
		echo "<tr>";
		echo "<td bgcolor=#ccff99>", $_SESSION['TranslationArray'][$ii], "</td>";
		if (is_numeric($_SESSION['TranslationArray'][$ii])) {
			 $rword = LoadRow("select Sentence,updated,donottranslate from words where id='" . $_SESSION['TranslationArray'][$ii] . "' and IdLanguage=0");
		}
		else {
			 $rword = LoadRow("select Sentence,updated,donottranslate from words where code='" . $_SESSION['TranslationArray'][$ii] . "' and IdLanguage=0");
		}
		echo "<td bgcolor=#ccffff>";
		if (isset ($rword->Sentence)) {
			echo $rword->Sentence;
		}
		//		echo "<br><a href=admin/adminwords.php?code=",$_SESSION['TranslationArray'][$ii],"&IdLanguage=0>edit</a>";
		echo "</td>";
		$rr = LoadRow("select id as idword,updated,Sentence from words where code='" . $_SESSION['TranslationArray'][$ii] . "' and IdLanguage=" . $IdLanguage);
		if (isset ($rr->idword)) {
			if (strtotime($rword->updated) > strtotime($rr->updated)) { // if obsolete
				echo "<td bgcolor=#ffccff>";
				if (isset ($rr->Sentence))
					echo $rr->Sentence;
				echo "<br><a href=\"".bwlink("admin/adminwords.php?code=". $_SESSION['TranslationArray'][$ii]. "&idword=". $rr->idword). "\">edit</a> ";
				echo "\n<table  style=\"display:inline\"><tr><td bgcolor=#ff3333>obsolete</td></tr></table>\n";
			} else {
				echo "<td bgcolor=#ffffcc>";
				if (isset ($rr->Sentence))
					echo $rr->Sentence;
				echo "<br><a href=\"".bwlink("admin/adminwords.php?code=". $_SESSION['TranslationArray'][$ii]. "&idword=". $rr->idword). "\">edit</a> ";
			}
		} else {
			echo "<td bgcolor=white align=center>";
			if ($rword->donottranslate=="no") {
			   echo "<br><a href=\"".bwlink("admin/adminwords.php?code=". $_SESSION['TranslationArray'][$ii]. "&IdLanguage=". $IdLanguage). "\">";
			   echo "\nADD\n";
			   echo "</a>";
			}
			else {
			    echo "<b>not translatable</b>" ;
			}
		}
		echo "</td></tr>";
	}

	echo "</table>\n";
} // end if it was a show translation on page request

// Show a whole language status
if (isset ($_GET['ShowLanguageStatus'])) {


	$onlymissing = false;
	$onlyobsolete = false;
	if (isset ($_GET['onlymissing'])) {
		$onlymissing = true;
	} else if (isset ($_GET['onlyobsolete'])) {
		$onlyobsolete = true;
	} else {
		$r1e = LoadRow("select count(*) as cnt from words where IdLanguage=0  and donottranslate!='yes'");
		$rXX = LoadRow("select count(*) as cnt from words where IdLanguage=" . $IdLanguage);
		$PercentAchieved = sprintf("%01.1f", ($rXX->cnt / $r1e->cnt) * 100) . "% achieved";
	}

	$IdLanguage = $_GET['ShowLanguageStatus'];
	$ssrlang="select *,id as IdLanguage from languages where id=" . $IdLanguage ;
//	echo "\$ssrlang=",$ssrlang,"<br>" ; ;
	$rlang = LoadRow($ssrlang);
	CheckRLang( $rlang );
	
	echo "\n<table cellpadding=3 width=100%><tr bgcolor=#ffccff><th colspan=3 align=center>\n";
	echo "Translation list for <b>" . $rlang->EnglishName . "</b> " . $PercentAchieved;
	echo "</th>";
	echo "<tr  bgcolor=#ffccff><th  bgcolor=#ccff99>code</th><th  bgcolor=#ccffff>english</th><th bgcolor=#ffffcc>", $rlang->EnglishName, "</th>";
	$qryEnglish = sql_query("select * from words where IdLanguage=0");
	while ($rEnglish = mysql_fetch_object($qryEnglish)) {
		$rr = LoadRow("select id as idword,updated,Sentence,IdMember from words where code='" . $rEnglish->code . "' and IdLanguage=" . $IdLanguage);
		$rword = LoadRow("select Sentence,updated,donottranslate from words where id=" . $rEnglish->id);
		if (((isset ($rr->idword)) and ($onlymissing)) or ($rEnglish->donottranslate=='yes'))
			continue;
		if ($onlyobsolete) {
		   if (!isset ($rr->idword)) continue; // skip non existing words
		   if (strtotime($rword->updated) <= strtotime($rr->updated))			continue; // skip non obsolete words
		}

		echo "<tr>\n";
		echo "<td bgcolor=#ccff99>", $rEnglish->code;
		if (HasRight("Grep")) {
			echo " <a href=\"".bwlink("admin/admingrep.php?action=grep&submit=find&s2=ww&s1=" . $rEnglish->code . "&scope=layout/*;*;lib/*")."\">grep</a>";
		}
		echo "\n<br><table  style=\"display:inline;\"><tr><td style=\"color:#3300ff;\">Last update ",fSince($rEnglish->updated)," ",fUserName($rEnglish->IdMember),"</td></table>\n";
		if ($rEnglish->Description != "") {
			echo "<p style=\"font-size:11px; color:gray;\">", $rEnglish->Description, "</p>\n";
		}
		if (IsAdmin()) {
		   if ($rEnglish->donnottranslate=="yes") {
		   	  echo "<b>not translatable</b>" ;
		   }
		   else {
		   	  echo " translatable" ;
		   }
		}
		echo "</td>\n";
		echo "<td bgcolor=#ccffff>";
		if (isset ($rword->Sentence)) {
			echo $rword->Sentence;
		}
		echo "</td>\n";
		if (isset ($rr->idword)) {
			if (strtotime($rword->updated) > strtotime($rr->updated)) { // if obsolete
				echo "<td bgcolor=#ffccff>";
				if (isset ($rr->Sentence))
					echo $rr->Sentence;
				echo "<br><a href=\"".bwlink("admin/adminwords.php?code=". $rEnglish->code. "&idword=". $rr->idword). "\">edit</a> ";
				echo "\n<table  style=\"display:inline\"><tr><td bgcolor=#ff3333>obsolete</td></table>\n";
				echo "\n<table  style=\"display:inline;color:#3300ff;\"><tr><td>Last update ",fSince($rr->updated)," ",fUserName($rr->IdMember),"</td></table>\n";
			} else {
				echo "<td bgcolor=#ffffcc>";
				if (isset ($rr->Sentence))
					echo $rr->Sentence;
				echo "<br><a href=\"".bwlink("admin/adminwords.php?code=". $rEnglish->code. "&idword=". $rr->idword). "\">edit</a> ";
				echo "\n<table  style=\"display:inline;color:#3300ff;\"><tr><td>Last update ",fSince($rr->updated)," ",fUserName($rr->IdMember),"</td></table>\n";
			}
		} else {
			echo "<td bgcolor=white align=center>";
			echo "<br><a href=\"".bwlink("admin/adminwords.php?code=". $rEnglish->code. "&IdLanguage=". $IdLanguage). "\">";
			echo "\nADD\n";
			echo "</a>\n";
		}
		echo "</td>\n";
	}

	echo "</table>\n";
} // end of show a whole language

if ((isset ($_POST['DOACTION'])) and ($_POST['DOACTION'] == 'Delete')) {
	$rlang = LoadRow("select id as IdLanguage,ShortCode,EnglishName from languages where ShortCode='" . $_POST['lang'] . "'");
	CheckRLang( $rlang );

	echo "request delete for $code<br>";
	if (isset ($_POST['idword'])) {
		$rToDelete = LoadRow("select * from words where id=" . $_POST['idword']);
	} else {
		$rToDelete = LoadRow("select * from words where IdLanguage=" . $rlang->IdLanguage . " and code='" . $code . "'");
	}
	if (isset ($rToDelete->id)) {
		$str = "delete from words where id=" . $rToDelete->id;
		sql_query($str);
		$ss = "word #" . $rToDelete->id . " (" . $rToDelete->code . ") deleted";
		echo "<h2>", $ss, "</h3>\n";
		LogStr($ss, "AdminWord");
	}
} // end of delete


// If it was a request for insert or update
if ((isset ($_POST['DOACTION'])) and ($_POST['DOACTION'] == "submit") and ($_POST['Sentence'] != "") and ($_POST['lang'] != "")) {
	if (isset ($_POST['lang'])) {
		if (is_numeric($_POST['lang']))
			$rlang = LoadRow("select id as IdLanguage ,ShortCode from languages where id=" . $_POST['lang']);
		else
			$rlang = LoadRow("select id as IdLanguage ,ShortCode from languages where ShortCode='" . $_POST['lang'] . "'");
	} else {
		$rlang = LoadRow("select id as IdLanguage ,ShortCode from languages where id='" . $_SESSION['IdLanguage'] . "'");
	}
	
	CheckRLang( $rlang );
		
	$rw = LoadRow("select * from words where IdLanguage=" . $rlang->IdLanguage . " and code='" . $_POST['code'] . "'");
	if ($rw)
		$id = $rw->id;

	if ((HasRight("Words", $_POST['lang'])) or (HasRight("Words", "\"All\""))) { // If has rights for updating/inserting in this language

		if ((isset ($id)) and ($id > 0)) { // Update case
			$rw = LoadRow("select * from words where id=" . $id);

			MakeRevision($id, "words"); // create revision

			$descuptade = "";
			if (isset ($_POST['Description'])) { // if there is a description present it
				$descupdate = ",Description='" . addslashes($_POST['Description']) . "'";
			}
			if (isset($_POST["donottranslate"])) {
			  $donottranslate="donottranslate='".$_POST["donottranslate"]."',";
			}
			$str = "update words set ".$donottranslate."code='" . $_POST['code'] . "',ShortCode='" . $rlang->ShortCode . "'" . $descupdate . ",IdLanguage=" . $rlang->IdLanguage . ",Sentence='" . addslashes($_POST['Sentence']) . "',updated=now(),IdMember=".$_SESSION['IdMember']." where id=$id";
			$qry = sql_query($str);
			if ($qry) {
				echo "update of <b>$code</b> successful<br>";
				LogStr("updating " . $code . " in " . $rlang->ShortCode, "AdminWord");

			} else {
				echo "failed for <b>$str</b><br>";
			}
		} // end of Update case
		else { // Insert case
			if (($code == "") or ($Sentence == "")) {
				echo "<h2><font color=red>can't insert if they are empty fields</font></h2>";
			} else {
				$str = "insert into words(code,ShortCode,IdLanguage,Sentence,updated,IdMember,created) values('" . $code . "','" . $rlang->ShortCode . "'," . $rlang->IdLanguage . ",'" . addslashes($Sentence) . "',now(),".$_SESSION['IdMember'].",now())";
				$qry = sql_query($str);
				$IdLastWord=mysql_insert_id();
				if ($qry) {
					echo "<b>$code</b> added successfully  (IdWord=#$IdLastWord)<br>";
					LogStr("inserting " . $code . " in " . $rlang->ShortCode, "AdminWord");
					if (($RightLevel>=10)and (!empty($_POST["Description"])) and ($IdLanguage==0)) {
					   $str = "update words set Description='".addslashes($_POST["Description"])."' where id=".$IdLastWord;
					   sql_query($str);
					}
				} else {
					echo "failed for <font color=red><b>$str</b></font><br>";
				}
			}
		} // end of insert case
	} // end of if has rights for updating/inserting in this language
	else {
		echo "You miss Right Scope for <b>", "\"" . $_POST['lang'] . "\"", "</b><br>\n";
	}
}

if (isset ($_GET['idword']))
	$idword = $_GET['idword'];

$SentenceEnglish = "";
if ((isset ($idword)) and ($idword > 0)) {
	$rr = LoadRow("select * from words where id=" . $idword);
	$code = $rr->code;
	$lang = $rr->ShortCode;
	$Sentence = $rr->Sentence;
}
if ($code != "") {
	$rEnglish = LoadRow("select Sentence,Description,donottranslate from words where code='" . $code . "' and IdLanguage=0");
	if (isset ($rEnglish->Sentence)) {
		$SentenceEnglish = "<i>" . str_replace("\n","<br>",htmlentities($rEnglish->Sentence)) . "</i><br>";
		if ($rEnglish->Description != "") {
			$SentenceEnglish .= "<table><tr><td>" . str_replace("\n","<br>",$rEnglish->Description) . "</td></table>";
		}

	}
}

echo "            <form method=\"post\">\n";
echo "              <table class=\"admin\" border=\"0\">\n";
echo "                <tr>\n";
echo "                  <td class=\"label\">Code: </td>\n";
echo "                  <td><input name=\"code\" value=\"$code\">";
if (isset ($_GET['idword']))
	echo " (idword=$idword)";
if ($RightLevel >= 10) { // Level 10 allow to change/set description
    echo "&nbsp;&nbsp; Translatable <select name=\"donottranslate\">";
    echo "<option value=\"no\"";
    if ($rEnglish->donottranslate=="no") echo " selected";
    echo ">translatable</option>\n";
    echo "<option value=\"yes\"";
    if ($rEnglish->donottranslate=="yes") echo " selected";
    echo ">not translatable</option>\n";
    echo "</select>\n";
}
echo "</td>\n";
echo "                </tr>\n";
$NbRow=4;
if ($RightLevel >= 10) { // Level 10 allow to change/set description
	if ($lang == CV_def_lang) {
    echo "                <tr>\n";
	  echo "                  <td class=\"label\">Description: </td>\n";
   	echo "                  <td>\n", $SentenceEnglish;
	  echo "                    <textarea name=\"Description\" cols=\"60\" rows=\"4\">", $rEnglish->Description, "</textarea>\n";
		echo "                  </td>\n";
		echo "                </tr>\n";
	} 
}
else {
  if ($rEnglish->donottranslate=="yes") echo "  <tr>\n      <td colspan=2 bgcolor=#ffff33>Do not translate</td>";
}

echo "                <tr>\n";
echo "                  <td class=\"label\">Sentence: </td>\n";
echo "                  <td>", $SentenceEnglish,"";
$NbRows=3*((substr_count($SentenceEnglish, '\n')+substr_count($SentenceEnglish, '<br>')+substr_count($SentenceEnglish, '<br />'))+1);
echo "    <textarea name=Sentence cols=80 rows=",$NbRows,">", $Sentence, "</textarea></td>\n";
echo "                </tr>\n";
echo "                <tr>\n";
echo "                  <td class=\"label\">Language: </td>\n";
echo "                  <td><input name=\"lang\" value=\"$lang\"></td>\n";
echo "                </tr>\n";
echo "                <tr>\n";
echo "                  <td colspan=\"2\" align=\"center\">\n";
echo "                    <input type=\"submit\" id=\"submit\" name=\"DOACTION\" value='submit'>\n";
echo "                    <input type=submit id=submit name=DOACTION value='Find'>\n";
echo "                    <input type=\"submit\" id=\"submit\" name=\"DOACTION\" value=\"Delete\" onclick=\"confirm('Do you confirm this delete ?');\">\n";
echo "                  </td>\n";
echo "                </tr>\n";

echo "              </table>\n";

echo "            </form>\n";

echo "          </div>\n";

require_once "../layout/footer.php";
?>