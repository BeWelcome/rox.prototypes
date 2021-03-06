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
$words = new MOD_words();
?>
<div id="tour">
    <h1><?php echo $words->get('tour_share')?></h1>

    <h2><?php echo $words->getFormatted('tour_share_title1')?></h2>
    <div class="floatbox" style="margin-top: 20px">
        <img src="images/tour/share4_small.jpg" class="framed float_left" style="margin-bottom: 20px; margin-right: 20px;" alt="share" />
        <p><?php echo $words->getFormatted('tour_share_text1')?></p>
        
        <h2><?php echo $words->getFormatted('tour_share_title2')?></h2>
        <p><?php echo $words->getFormatted('tour_share_text2')?></p>
        
        <h2><?php echo $words->getFormatted('tour_share_title3')?></h2>
        <p><?php echo $words->getFormatted('tour_share_text3')?></p>
        
        <h2><a class="bigbutton" href="tour/meet" onclick="this.blur();" style="margin-bottom: 20px"><span><?php echo $words->getFormatted('tour_goNext')?> &raquo;</span></a> <?php echo $words->getFormatted('tour_meet')?></h2>
    </div>
</div>
