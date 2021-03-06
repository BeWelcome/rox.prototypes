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
write to the Free Software Foundation, Inc., 59 Temple PlaceSuite 330, 
Boston, MA  02111-1307, USA.

*/
/** 
 * @author matthias (globetrotter_tt)
 * @author Fake51
 */
 
echo <<<HTML
<form class="yform full" method="post" action="">
    {$this->getCallbackOutput('MembersController','retireProfile')}
    <p>{$words->getFormatted ('retire_explanation',$this->member->Username)}</p>
    <div class="type-text">
        <label for="explain">{$words->getFormatted ('retire_membercanexplain')}</label>
        <textarea name="explanation" id="explain" cols="65" rows="6"></textarea>
    </div>
    <p>
        <input type="checkbox" name="Complete_retire" 
            onclick="return confirm ('{$words->getFormatted ('retire_WarningConfirmWithdraw')}')" />
        {$words->getFormatted ('retire_fulltickbox')}
    </p>
    <p class="center">
        <input type="submit"
            onclick="return confirm ('{$words->getFormatted ('retire_WarningConfirmRetire')}')" />
    </p>
</form>
HTML;
