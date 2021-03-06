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
     * @author crumbking
     */

    /** 
     * Treasurer management page
     * 
     * @package Apps
     * @subpackage Admin
     */

class AdminTreasurerPage extends AdminBasePage
{
    public function __construct(AdminModel $model) {
        $this->model = $model;
        $this->member = $model->getLoggedInMember();
        $this->donations = $model->getRecentDonations();
        $stats = $model->getStatForDonations();
        $this->donatedInCampaign = $stats->YearDonation;
        if (!$this->donatedInCampaign) {
            $this->donatedInCampaign = 0;
        }
        // Check if a donation campaign is currently running
        $this->campaign = ($model->getDonationCampaignStatus() == 1);
        list($amount, $date) = $this->model->getDonationCampaignValues();
        $this->neededPerYear = $amount;
        $this->campaignStartDate = $date;
    }
    
    public function teaserHeadline()
    {
        return "<a href='admin'>{$this->words->get('AdminTools')}</a> &raquo; {$this->words->get('AdminTreasurer')}";
    }
}
