<?php


class LastcommentsModel extends  RoxModelBase
{
    
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get last comments members have given each other.
     *
     * @param $limit Maximum number of results
     * @return array List of comments, empty if no results
     */
    public function GetLastComments($limit = 20) {
        $query = "
            SELECT
                m1.Username         AS UsernameFrom,
                m2.Username         AS UsernameTo,
                comments.updated,
                TextWhere,
                TextFree,
                comments.Quality,
                country1.iso_alpha2 AS IdCountryFrom,
                city1.geonameId     AS IdCityFrom,
                country1.name       AS CountryNameFrom,
                country2.iso_alpha2 AS IdCountryTo,
                city2.geonameId     AS IdCityTo,
                country2.name       AS CountryNameTo
            FROM
                comments,
                members            AS m1,
                members            AS m2,
                geonames_cache     AS city1,
                geonames_countries AS country1,
                geonames_cache     AS city2,
                geonames_countries AS country2
            WHERE
                m1.id = IdFromMember
                AND
                m2.id = IdToMember
                AND
                m1.Status = 'Active'
                AND
                m2.Status = 'Active'
                AND
                DisplayableInCommentOfTheMonth = 'Yes' 
                AND
                city1.geonameId = m1.IdCity
                AND
                country1.iso_alpha2 = city1.fk_countrycode
                AND
                city2.geonameId = m2.IdCity
                AND
                country2.iso_alpha2 = city2.fk_countrycode
            ORDER BY
                comments.id DESC
            LIMIT
                $limit
            ";
        $result = $this->bulkLookup($query);
        return $result;
    }

    /**
     * Find and return one group, using id
     *
     * @param int $group_id
     * @return mixed false or a Group entity
     */    
    public function findGroup($group_id)
    {
        $group = $this->createEntity('Group',$group_id);
        if ($group->isLoaded())
        {
            return $group;
        }
        else
        {
            return false;
        }
    }

    /**
     * creates a membership for a member and sets the status to invited
     *
     * @param object $group - Group entity
     * @param int $member_id - id of member to invite
     * @access public
     * @return bool
     */
    public function inviteMember($group, $member_id)
    {
        if (!$group->isLoaded() || !($member = $this->createEntity('Member', $member_id)))
        {
            return false;
        }
        return (bool) $this->createEntity('GroupMembership')->memberJoin($group, $member, 'Invited');
    }

    /**
     * searches for members, using username
     *
     * @param string $name
     * @access public
     * @return array
     */
    public function findMembersByName($group, $name)
    {
        $members = $this->createEntity('Member')->findByWhereMany("Username like '%" . $this->dao->escape($name) . "%'");
        $result = array();
        foreach ($members as $member)
        {
            if (!$member->getGroupMembership($group))
            {
                $result[] = $member;
            }
        }
        return $result;
    }

    /**
     * Find and return groups, using search terms from search page
     *
     * @param string $terms - search terms
     * @return mixed false or an array of Groups
     */    
    public function findGroups($terms = '', $page = 0, $order = '')
    {
    
        if (!empty($order))
        {
            switch ($order)
            {
                case "nameasc":
                    $order = 'Name ASC';
                    break;
                case "namedesc":
                    $order = 'Name DESC';
                    break;
                case "membersasc":
                    $order = '(SELECT SUM(IdMember) FROM membersgroups as mg WHERE IdGroup = groups.id) ASC, Name ASC';
                    break;
                case "membersdesc":
                    $order = '(SELECT SUM(IdMember) FROM membersgroups as mg WHERE IdGroup = groups.id) DESC, Name ASC';
                    break;
                case "createdasc":
                    $order = 'created ASC, Name ASC';
                    break;
                case "createddesc":
                    $order = 'created DESC, Name ASC';
                    break;
                case "category":
                default:
                    $order = 'created DESC, Name ASC';
                    break;
            }
        }
        else
        {
            $order = 'Name ASC';
        }
        
        $terms_array = explode(' ', $terms);

        $group = $this->createEntity('Group');
        $group->sql_order = $order;
        return $this->_group_list = $group->findBySearchTerms($terms_array, ($page * 10));
    }


    /**
     * Find all groups
     *
     * @access public
     * @return array Returns an array of Group entity objects
     */
    public function findAllGroups($offset = 0, $limit = 0)
    {
        if ($this->_group_list != 0)
        {
            return $this->_group_list;
        }

        $group = $this->createEntity('Group');
        $group->sql_order = 'created DESC, Name ASC';
        return $this->_group_list = $group->findAll($offset, $limit);
    }
    
    /**
     * Find all groups I am member of
     *
     * @access public
     * @return mixed Returns an array of Group entity objects or false if you're not logged in
     */
    public function getMyGroups()
    {
        if (!isset($_SESSION['IdMember']))
        {
            return array();
        }
        else
        {
            return $this->getGroupsForMember($_SESSION['IdMember']);
        }
    }
    
    /**
     * Find all groups $member_id is member of
     *
     * @access public
     * @return mixed Returns an array of Group entity objects or false if you're not logged in
     */
    public function getGroupsForMember($member_id)
    {
        if (!($member_id = intval($member_id)))
        {
            return false;
        }

        $member = $this->createEntity('Member')->findById($member_id);
        return $member->getGroups();

    }
    
    
    /**
     * remember the last visited groups, so 
     *
     * @param int $now_group_id id of the group you are visiting now
     */
    public function setGroupVisit($group_id)
    {
        if (
            (!isset($_SESSION['my_group_visits'])) ||
            (!$group_visits = unserialize($_SESSION['my_group_visits'])) ||
            (!is_array($group_visits))
        ) {
            $group_visits = array();
        }
        $group_visits[$group_id] = microtime(true);
        
        // sort by value, while preserving the keys
        asort($group_visits);
        $_SESSION['my_group_visits'] = serialize(array_slice($group_visits, 0, 5));
        // unset($_SESSION['my_group_visits']);
    }
    
    public function getLastVisited()
    {
        if (
            (!isset($_SESSION['my_group_visits'])) ||
            (!$group_visits = unserialize($_SESSION['my_group_visits'])) ||
            (!is_array($group_visits))
        ) {
            return array();
        } else {
            $groups = array();
            foreach($group_visits as $id => $time) {
                $groups[] = $this->findGroup($id);
            }
            return $groups;
        } 
    }

    /**
     * handles input checking for group creation
     *
     * @param array $input - Post vars
     * @access public
     * @return array
     */
    public function createGroup($input)
    {
        // check fields

        $problems = array();
        
        if (empty($input['Group_']))
        {
            // name is not set:
            $problems['Group_'] = true;
        }
        
        if (empty($input['GroupDesc_']))
        {
            // Description is not set.
            $problems['GroupDesc_'] = true;
        }
        
        if (!isset($input['Type']) || !in_array($input['Type'], array('NeedAcceptance', 'NeedInvitation','Public')))
        {
            $problems['Type'] = true;
        }

        if (!empty($_FILES['group_image']) && empty($problems) && $_FILES['group_image']['tmp_name'] != '')
        {
            if ($picture = $this->handleImageUpload())
            {
                $input['Picture'] = $picture;
            }
            else
            {
                $problems['image'] = true;
            }
        }
        
        if (!empty($problems))
        {
            $group_id = false;
        }
        else
        {
            $group = $this->createEntity('Group');
            if (!$group->createGroup($input))
            {
                $group_id = false;
                $problems['General'] = true;
            }
            else
            {
                $group->memberJoin($this->getLoggedInMember(), 'In');
                $group_id = $group->id;
                $group->setDescription($input['GroupDesc_']);
                
                if (!$group->setGroupOwner($this->getLoggedInMember()))
                {
                    // TODO: display error message and something about contacting admins
                    $problems['General'] = true;
                    $this->createEntity('Group', $group_id)->deleteGroup();
                    $group_id = false;
                }
            }
        }

        return array(
            'problems' => $problems,
            'group_id' => $group_id
        );
    }

    /**
     * update membership settings for a given member and group
     *
     * @param int $member_id
     * @param int $group_id
     * @param string $acceptgroupmail
     * @param string $comment
     * @return bool
     * @access public
     */
    public function updateMembershipSettings($member_id, $group_id, $acceptgroupmail, $comment)
    {
        $group = $this->createEntity('Group', $group_id);
        $member = $this->createEntity('Member', $member_id);
        if (!($membership = $this->createEntity('GroupMembership')->getMembership($group, $member)))
        {
            return false;
        }

        return $membership->updateMembership(strtolower($acceptgroupmail), $comment);
    }

    /**
     * checks if a the current logged member can access the groups admin page
     *
     * @param object $group - group entity
     * @access public
     * @return bool
     */
    public function canAccessGroupAdmin($group)
    {
        if (!is_object($group) || !$group->isPKSet())
        {
            return false;
        }

        if (!$this->getLoggedInMember()->hasPrivilege('GroupsController', 'GroupSettings', $group))
        {
            return false;
        }
        return true;
    }

    /**
     * checks if a the current logged member can delete groups
     *
     * @param object $group - group entity
     * @access public
     * @return bool
     */
    public function canAccessGroupDelete($group)
    {
        if (!is_object($group) || !$group->isPKSet())
        {
            return false;
        }

        if (!$this->getLoggedInMember()->hasPrivilege('GroupsController', 'GroupDelete', $group))
        {
            return false;
        }
        return true;
    }


    /**
     * handles a user joining a group
     *
     * @param object $member - member entity of the user joining
     * @param object $group - group entity for the group joined
     * @return bool
     * @access public
     */
    public function joinGroup($member, $group)
    {
        if (!is_object($group) || !$group->isLoaded() || !is_object($member) || !$member->isLoaded() || $group->Type == 'NeedInvitation')
        {
            return false;
        }
        $status = (($group->Type == 'NeedAcceptance') ? 'WantToBeIn' : 'In');
        $result = (bool) $this->createEntity('GroupMembership')->memberJoin($group, $member, $status);
        if ($result && $status == 'WantToBeIn')
        {
            $this->notifyGroupAdmin($group, $member);
        }
        return $result;
    }

    /**
     * handles a user leaving a group
     *
     * @param object $member - member entity of the user joining
     * @param object $group - group entity for the group joined
     * @return bool
     * @access public
     */
    public function leaveGroup($member, $group)
    {
        if (!is_object($group) || !$group->isLoaded() || !is_object($member) || !$member->isLoaded())
        {
            return false;
        }

        if ($group->isGroupOwner($member))
        {
            return false;
        }

        return (bool) $this->createEntity('GroupMembership')->memberLeave($group, $member);
    }

    /**
     * handles deleting groups
     *
     * @param object $group - group entity to be deleted
     * @return bool
     * @access public
     */
    public function deleteGroup($group)
    {
        if (!is_object($group) || !$group->isLoaded())
        {
            return false;
        }
        return $group->deleteGroup();
    }

    /**
     * update group settings for a given group
     *
     * @param object $group - group entity
     * @param string $description - description of the group
     * @param string $type - how public the group is
     * @param string $visible_posts - if the posts of the group should be visible or not
     * @return bool
     * @access public
     */
    public function updateGroupSettings($group, $description, $type, $visible_posts)
    {
        if (!is_object($group) || !$group->isLoaded())
        {
            return false;
        }

        $picture = '';
        if (!empty($_FILES['group_image']) && !empty($_FILES['group_image']['tmp_name']))
        {
            if (!$picture = $this->handleImageUpload())
            {
                return false;
            }
        }

        return $group->updateSettings($description, $type, $visible_posts, $picture);
    }

    /**
     * takes care of a group image being uploaded
     *
     * @access private
     * @return mixed - false on fail or the name of the uploaded image
     */
    private function handleImageUpload()
    {
        if ($_FILES['group_image']['error'] != 0)
        {
            return false;
        }
        else
        {
            $dir = new PDataDir('groups');
            $img = new MOD_images_Image($_FILES['group_image']['tmp_name']);
            $new_name = $img->getHash();
            
            if (filesize($_FILES['group_image']['tmp_name']) > (500*1024) || !($dir->fileExists($new_name) || $dir->copyTo($_FILES['group_image']['tmp_name'], $new_name)))
            {
                return false;
            }
            else
            {
                // yup, hackish way of resizing an image
                // feel free to add a resize function to MOD_images_Image and change this bit
                // or create an image entity with all needed functionality in ONE place
                // ... in my dreams ...
                $img->createThumb($dir->dirName(), $new_name, 300, 300, true);
                $img->createThumb($dir->dirName(), 'thumb', 100, 100);
                return $new_name;
            }
        }
    }


    /**
     * sends headers, reads out a thumbnail image and then exits
     *
     * @param int $id - id of group to get thumbnail for
     * @access public
     */
    public function thumbImg($id)
    {
        if (!($group = $this->createEntity('Group')->findById($id)) || !$group->Picture)
        {
            PPHP::PExit();
        }

        $dir = new PDataDir('groups');

        if (!$dir->fileExists('thumb' . $group->Picture) || ($dir->file_Size('thumb' . $group->Picture) == 0))
        {
            PPHP::PExit();
        }
        $img = new MOD_images_Image($dir->dirName() . '/thumb' . $group->Picture);

        header('Content-type: '.$img->getMimetype());
        $dir->readFile('thumb' . $group->Picture);
        PPHP::PExit();            
    }

    /**
     * sends headers, reads out an image and then exits
     *
     * @param int $id - id of group to get thumbnail for
     * @access public
     */
    public function realImg($id)
    {
        if (!($group = $this->createEntity('Group')->findById($id)) || !$group->Picture)
        {
            PPHP::PExit();
        }

        $dir = new PDataDir('groups');

        if (!$dir->fileExists($group->Picture) || ($dir->file_Size($group->Picture) == 0))
        {
            PPHP::PExit();
        }
        $img = new MOD_images_Image($dir->dirName() . '/' . $group->Picture);

        header('Content-type: '.$img->getMimetype());
        $dir->readFile($group->Picture);
        PPHP::PExit();            
    }

    /**
     * bans a member from a group
     *
     * @param object $group - group entity
     * @param int $member_id
     * @return bool
     * @access public
     */
    public function banGroupMember($group, $member_id, $ban = false)
    {
        if (!is_object($group) || !$group->isPKSet() || !($member = $this->createEntity('Member')->findById($member_id)))
        {
            return false;
        }

        $membership = $this->createEntity('GroupMembership')->getMembership($group, $member);
        if ($ban)
        {
            return $membership->updateStatus('Kicked');
        }
        else
        {
            return $membership->memberLeave($group, $member);
        }
    }

    /**
     * accepts a member into a group
     *
     * @param object $group - group entity
     * @param int $member_id
     * @return bool
     * @access public
     */
    public function acceptGroupMember($group, $member_id)
    {
        if (!is_object($group) || !$group->isPKSet() || !($member = $this->createEntity('Member')->findById($member_id)))
        {
            return false;
        }

        if (($membership = $this->createEntity('GroupMembership')->findByWhere("IdGroup = '" . $group->getPKValue() . "' AND IdMember = '" . $member->getPKValue() . "'")) && $membership->Status == 'WantToBeIn')
        {
            $note = $this->createEntity('Note');
            $note->IdMember = $member->getPKValue();
            $note->IdRelMember = $group->getGroupOwner()->getPKValue();
            $note->Type = 'message';
            $note->Link = "/groups/{$group->getPKValue()}";
            $note->WordCode = '';
            $note->FreeText = $this->getWords()->get('GroupsAcceptedIntoGroup', $group->Name);
            $note->insert();
            return $membership->updateStatus('In');
        }
        return false;
    }

    /**
     * creates a message for the invited member
     *
     * @param object $group
     * @param int $member_id
     * @param object $from - member entity
     * @access public
     */
    public function sendInvitation($group, $member_id, $from)
    {
        if ($group->isLoaded() && ($member = $this->createEntity('Member', $member_id)) && $from->isLoaded())
        {
            $msg = $this->createEntity('Message');
            $msg->MessageType = 'MemberToMember';
            $msg->updated = $msg->created = $msg->DateSent = date('Y-m-d H:i:s');
            $msg->IdParent = 0;
            $msg->IdReceiver = $member->getPKValue();
            $msg->IdSender = $from->getPKValue();
            $msg->SendConfirmation = 'No';
            $msg->Status = 'ToSend';
            $msg->Message = "Hi {$member->Username}<br/><br/>You&apos;ve been invited to the group {$group->Name}. If you would like to join the group, click the following link: <a href='http://{$_SERVER['SERVER_NAME']}/groups/{$group->getPKValue()}/acceptinvitation/{$member->getPKValue()}'>http://{$_SERVER['SERVER_NAME']}/groups/{$group->getPKValue()}/acceptinvitation/{$member->getPKValue()}</a>.<br/>If you wish to decline the invitation, please click this link instead: <a href='http://{$_SERVER['SERVER_NAME']}/groups/{$group->getPKValue()}/declineinvitation/{$member->getPKValue()}'>http://{$_SERVER['SERVER_NAME']}/groups/{$group->getPKValue()}/declineinvitation/{$member->getPKValue()}</a><br/><br/>Have a great time<br/>BeWelcome";
            $msg->InFolder = 'Normal';
            $msg->JoinMemberPict = 'no';
            $msg->insert();

            $note = $this->createEntity('Note');
            $note->IdMember = $member->getPKValue();
            $note->IdRelMember = $from->getPKValue();
            $note->Type = 'message';
            $note->Link = "/groups/{$group->getPKValue()}";
            $note->WordCode = '';
            $note->FreeText = $this->getWords()->get('GroupsInvitedNote', $groups->Name);
            $note->insert();
        }
    }

    /**
     * changes a membership from invited to in
     *
     * @param object $group
     * @param int $member_id
     * @access public
     * @return bool
     */
    public function memberAcceptedInvitation($group, $member_id)
    {
        if (!$group->isLoaded() || !($member = $this->createEntity('Member', $member_id)) || !($logged_in = $this->getLoggedInMember()) || $logged_in->getPKValue() != $member->getPKValue())
        {
            return false;
        }
        if ($membership = $this->createEntity('GroupMembership')->findByWhere("IdGroup = '{$group->getPKValue()}' AND IdMember = '{$member->getPKValue()}' AND Status = 'Invited'"))
        {
            return $membership->updateStatus('In');
        }
        else
        {
            return false;
        }
    }

    /**
     * deletes a membership
     *
     * @param object $group
     * @param int $member_id
     * @access public
     * @return bool
     */
    public function memberDeclinedInvitation($group, $member_id)
    {
        if (!$group->isLoaded() || !($member = $this->createEntity('Member', $member_id)) || !($logged_in = $this->getLoggedInMember()) || $logged_in->getPKValue() != $member->getPKValue())
        {
            return false;
        }
        if ($membership = $this->createEntity('GroupMembership')->findByWhere("IdGroup = '{$group->getPKValue()}' AND IdMember = '{$member->getPKValue()}' AND Status = 'Invited'"))
        {
            return $membership->delete();
        }
        else
        {
            return false;
        }
    }

    /**
     * creates a message for the group admin, that a new member wants to join
     *
     * @param object $group
     * @param object $member - member entity
     * @access public
     */
    public function notifyGroupAdmin($group, $member)
    {
        if ($group->isLoaded() && $member->isLoaded() && ($admin = $group->getGroupOwner()))
        {
            $msg = $this->createEntity('Message');
            $msg->MessageType = 'MemberToMember';
            $msg->updated = $msg->created = $msg->DateSent = date('Y-m-d H:i:s');
            $msg->IdParent = 0;
            $msg->IdReceiver = $admin->getPKValue();
            $msg->IdSender = 0;
            $msg->SendConfirmation = 'No';
            $msg->Status = 'ToSend';
            $msg->Message = "Hi {$admin->Username}<br/><br/>{$member->Username} wants to join the group {$group->Name}. To administrate the group members click the following link: <a href='http://{$_SERVER['SERVER_NAME']}/groups/{$group->getPKValue()}/memberadministration'>group member administration</a>.<br/><br/>Have a great time<br/>BeWelcome";
            $msg->InFolder = 'Normal';
            $msg->JoinMemberPict = 'no';
            $msg->insert();
        }
    }
}
