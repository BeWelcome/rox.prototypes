<?php


class PersonalStartpage extends RoxPageView
{
    protected function getTopmenuActiveItem() {
        return 'main';
    }
    
    protected function getStylesheets() {
        $stylesheets = parent::getStylesheets();
        $stylesheets[] = 'styles/css/minimal/screen/basemod_minimal_col3.css';
        return $stylesheets;
    }
    
    /*
     * The idea was that stylesheetpatches was for MSIE
     */
    protected function getStylesheetPatches()
    {
        //$stylesheet_patches = parent::getStylesheetPatches();
        $stylesheet_patches[] = 'styles/css/minimal/patches/patch_3col.css';
        return $stylesheet_patches;
    }

    protected function teaserContent()
    {
        $this->__call('teaserContent', array());
    }

    protected function getPageTitle() {
        $words = new MOD_words();
        if (isset($_SESSION['Username'])) {
            return $words->getSilent('HelloUsername',$_SESSION['Username']) . ' | BeWelcome';
        } else {
            // this should not happen actually!
            return $words->getSilent('WelcomeGuest');
        }
    }

    protected function column_col1()
    {

    }
}


class MailboxWidget_Personalstart extends MailboxWidget_Received
{
    protected function getMessages()
    {
        if (!isset($_SESSION['IdMember'])) {
            // not logged in - no messages
            return array();
        } else {
            $member_id = $_SESSION['IdMember'];
            $sort_string = '(case when unixtime_whenfirstread = 0 then 1 else 0 end) desc, unixtime_datesent desc, senderusername desc';
            return $this->model->filteredMailbox('messages.IdReceiver = '.$member_id.' AND messages.Status = "Sent" AND messages.InFolder = "Normal" AND DeleteRequest != "receiverdeleted"',$sort_string);
        }
    }
	
    protected function showItems()
    {
        // don't need a table - a simple list is enough.
        $this->showItems_list();
    }

    protected function showListItem($message, $i_row)
    {
        $words = new MOD_words();
        extract(get_object_vars($message));
        $readstyle = '';
        if ($message->unixtime_WhenFirstRead == false) $readstyle = 'message unread';
        //print_r($message);
        ?>
        
        <div class="floatbox">
            <a class="float_right" href="messages/<?=$id?>/reply" >
                <img src="images/icons/email_go.png" alt="Reply" />
            </a>
            <?= MOD_layoutbits::PIC_30_30($senderUsername,'','float_left framed')?>
            <a href="messages/<?=$id?>" class="<?=$readstyle?>">
        
            <?php
            /* Remove XHTML linebreak tags. */
            $Message = str_replace("<br />"," ",$Message);
            $Message = str_replace("<br/>"," ",$Message);
            /* Remove HTML 4.01 linebreak tags. */
            $Message = str_replace("<br>"," ",$Message);
            if (strlen($Message) >= 61) echo substr($Message, 0, 58).'... ';
            else echo $Message;
            ?>
        
            </a><br />
            <span class="small grey" title="<?=date('d. m. Y',$message->unixtime_created)?>"><?=$words->get('from')?> <a href="members/<?=$senderUsername?>"><?=$senderUsername?>: </a>
            <?=MOD_layoutbits::ago($message->unixtime_created);?></span>
        </div>
        
        <?php
    }


    protected function showBetweenListItems($prev_item, $item, $i_row)
    {
        echo '<hr style="border-color: #dddddd" />';
    }
}

class NotifyMemberWidget_Personalstart extends NotifyMemberWidget
{
    // currently no modifications here
}


?>
