<?php


/**
 * verifymembers pages
 * This is the class for all pages displayed by verify members
 *
 * @package verifymembers
 * @author JeanYves
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 * @version $Id$
 */


/**
This page prepare the verification
**/
class VerifyMembersPage extends RoxPageView {
    /**
     * content of the middle column - this is the most important part
     */

    private $_error;
    
    public function __construct($error) {
        $this->_error = $error;
    }    

	  
    protected function column_col3()    {
	  	 
        // get the translation module
        $words = $this->getWords();
	 	 $errormessage=$this->_error ;
		 
        require TEMPLATE_DIR.'apps/verifymembers/showexplanation.php';    
    }
    
    /**
     * which item in the top menu should be activated when showing this page?
     * Let's use the 'getanswers' menu item for this one.
     * @return string name of the menu
     */
    protected function getTopmenuActiveItem() {
        return 'getanswers';
    }

    /**
     * configure the teaser (the content of the orange bar)
     */
    protected function teaserHeadline() {
	 	 $words = $this->getWords();
	 	 if (MOD_right::get()->hasRight("Verifier","ApprovedVerifier")) {
		 	echo $words->getFormatted("verifymembers_approvedverifier") ;
	     }
		 else {
		 	echo $words->getFormatted("verifymembers_teaser") ;
		 }
    }
    
    /**
     * configure the page title (what appears in your browser's title bar)
     * @return string the page title
     */
    protected function getPageTitle() {
        return 'Verify members page!';
    }
    
    /**
     * configure the sidebar
     */
    protected function leftSidebar()
    {
        echo 'verify members Sidebar';
    }
	 
} // end of VerifyMembersPage


?>