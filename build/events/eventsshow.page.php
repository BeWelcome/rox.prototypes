<?php
/*
Copyright (c) 2007-2009 BeVolunteer

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
     * @author shevek
     */

    /**
     * base class for all events pages
     *
     * @package Apps
     * @subpackage Events
     */
class EventsShowPage extends PageWithActiveSkin
{
    protected $event;
    
    public function __construct(Event $showevent)
    {
        $this->event = $showevent;
    }
    
  protected function column_col3()
    {
        $words = $this->getWords();
        require 'templates/eventshow.php';
    }

    protected function leftSidebar()
    {
        $layoutkit = $this->layoutkit;
        $words = $layoutkit->getWords();
        require 'templates/sidebar.php';
    }
    

    protected function getPageTitle() {
        $words = $this->getWords();
        return $words->getBuffered('Events') . ' - BeWelcome';
    }

    protected function teaserContent()
    {
        $layoutkit = $this->layoutkit;
        $words = $layoutkit->getWords();

        require 'templates/teaser.php';
    }
    
    protected function getTopmenuActiveItem()
    {
        return 'events';
    }
    
    protected function getStylesheets() {
       $stylesheets = parent::getStylesheets();
       $stylesheets[] = 'styles/css/minimal/screen/custom/forums.css';
       return $stylesheets;
    }

}
