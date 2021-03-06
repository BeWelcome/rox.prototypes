<?php

class DonateView extends PAppView
{
    /**
     * Loading Simple Teaser - just needs defined title
     *
     * @param void
     */
    private $_model;
    
    public function __construct(DonateModel &$model) {
        $this->_model =& $model;
    }

    public function ShowSimpleTeaser($title)
    {
        require TEMPLATE_DIR.'apps/rox/teaser_simple.php';
    }

    public function donate($sub = false,$TDonationArray = false, $error = false)
    {
        if ($sub == 'list' && $TDonationArray) {
            require 'templates/donate_list.php';
        } else require 'templates/donate.php';
    }
    
    public function donateBar($TDonationArray = false)
    {
         $Stat=$this->_model->getStatForDonations() ;
        require 'templates/userbar_donate.php';
    }

    public function submenu($sub) {
//       $Stat=$this->_model->getStatForDonations() ;
        require 'templates/submenu_donate.php';
    }
}

?>
