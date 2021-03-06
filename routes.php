<?php
    /**
     * this file contains the routes used in RequestRouter
     * it is require_once()'d in that class
     *
     * to add a route, just add a line as follows:
     *     $this->addRoute(route_name, route_url, controller, method, callback);
     *         |               |            |         |           |
     * RequestRouter method    |            |         |           |
     *       unique name of your route       |         |           |
     *                      the url you want to map   |           |
     *                              the controller to map to      |
     *                                   the method in the controller to map to
     *                                              if the method to call is a post callback
     */

    // general routes
    $this->addRoute('main_page','', 'RoxController', 'index');

    // login routes
    $this->addRoute('login_helper','login/:url:#login-widget', 'LoginController', 'login_helper', true);
    $this->addRoute('logout', 'logout', 'LoginController', 'logOut');
    $this->addRoute('login', 'login*', 'LoginController', 'logIn');

    // group routes
    $this->addRoute('group_acceptinvitation','groups/:group_id:/acceptinvitation/:member_id:', 'GroupsController', 'acceptInvitation');
    $this->addRoute('group_acceptmember','groups/:group_id:/acceptmember/:member_id:', 'GroupsController', 'acceptMember');
    $this->addRoute('group_banmember','groups/:group_id:/banmember/:member_id:', 'GroupsController', 'banMember');
    $this->addRoute('group_addadmin','groups/:group_id:/addAdmin/:member_id:', 'GroupsController', 'addMemberAsAdmin');
    $this->addRoute('group_resignadmin','groups/:group_id:/resignAdmin', 'GroupsController', 'resignAsAdmin');
    $this->addRoute('group_declineinvitation','groups/:group_id:/declineinvitation/:member_id:', 'GroupsController', 'declineInvitation');
    $this->addRoute('group_delete','groups/:group_id:/delete', 'GroupsController', 'delete');
    $this->addRoute('group_deleted','groups/:group_id:/delete/true', 'GroupsController', 'delete');
    $this->addRoute('group_forum','groups/:group_id:/forum', 'GroupsController', 'forum');
    $this->addRoute('group_forum_thread','groups/:group_id:/forum/:thread:', 'GroupsController', 'forum');
    $this->addRoute('group_forum_action','groups/:group_id:/forum/:thread:/:action:', 'GroupsController', 'forum');
    $this->addRoute('group_groupsettings','groups/:group_id:/groupsettings', 'GroupsController', 'groupSettings');
    $this->addRoute('group_invitemember','groups/:group_id:/invitemember/:member_id:', 'GroupsController', 'inviteMember');
    $this->addRoute('group_invitemember_ajax','groups/:group_id:/invitememberajax/:member_id:', 'GroupsController', 'inviteMemberAjax');
    $this->addRoute('group_invitepage','groups/:group_id:/invitemember', 'GroupsController', 'inviteMembers');
    $this->addRoute('group_join','groups/:group_id:/join', 'GroupsController', 'join');
    $this->addRoute('group_joined','groups/:group_id:/join/true', 'GroupsController', 'joined', true);
    $this->addRoute('group_kickmember','groups/:group_id:/kickmember/:member_id:', 'GroupsController', 'kickMember');
    $this->addRoute('group_leave','groups/:group_id:/leave', 'GroupsController', 'leave');
    $this->addRoute('group_left','groups/:group_id:/leave/true', 'GroupsController', 'left');
    $this->addRoute('group_memberadministration','groups/:group_id:/memberadministration', 'GroupsController', 'memberAdministration');
    $this->addRoute('group_memberadministration_paged','groups/:group_id:/memberadministration/page/:page_number:', 'GroupsController', 'memberAdministration');
    $this->addRoute('group_members','groups/:group_id:/members', 'GroupsController', 'members');
    $this->addRoute('group_members_paged','groups/:group_id:/members/page/:page_number:', 'GroupsController', 'members');
    $this->addRoute('group_membersearch_ajax','groups/:group_id:/membersearchajax/:search_term:', 'GroupsController', 'memberSearchAjax');
    $this->addRoute('group_membersettings','groups/:group_id:/membersettings', 'GroupsController', 'memberSettings');
    $this->addRoute('group_start','groups/:group_id:', 'GroupsController', 'showGroup');
    $this->addRoute('group_wiki','groups/:group_id:/wiki', 'GroupsController', 'wiki');
    $this->addRoute('groups_featured','groups/featured', 'GroupsController', 'featured');
    $this->addRoute('groups_mygroups','groups/mygroups', 'GroupsController', 'myGroups');
    $this->addRoute('groups_new','groups/new', 'GroupsController', 'create');
    $this->addRoute('groups_overview','groups', 'GroupsController', 'index');
    $this->addRoute('groups_realimg','groups/realimg/:group_id:', 'GroupsController', 'realImg');
    $this->addRoute('groups_search','groups/search', 'GroupsController', 'search');
    $this->addRoute('groups_thumbimg','groups/thumbimg/:group_id:', 'GroupsController', 'thumbImg');
    
    // related groups routes
    $this->addRoute('relatedgroup_select','groups/:group_id:/selectrelatedgroup', 'RelatedGroupsController', 'selectRelatedGroup');
    $this->addRoute('relatedgroup_add','groups/:group_id:/addrelatedgroup/:related_id:', 'RelatedGroupsController', 'addRelatedGroup');
    $this->addRoute('relatedgroup_selectdelete','groups/:group_id:/selectdeleterelatedgroup', 'RelatedGroupsController', 'selectdeleteRelatedGroup');
    $this->addRoute('relatedgroup_delete','groups/:group_id:/deleterelatedgroup/:related_id:', 'RelatedGroupsController', 'deleteRelatedGroup');
    $this->addRoute('relatedgroup_log','groups/:group_id:/relatedgroupsettings', 'RelatedGroupsController', 'showRelatedGroupLog');

    // member app routes
    $this->addRoute('members_profile_retired', 'retired', 'MembersController', 'retired');

    // admin temporary vol page route
    $this->addRoute('admin_tempvolstart', 'volunteer', 'AdminController', 'tempVolStart');
    // admin app routes
    $this->addRoute('admin_main', 'admin', 'AdminController', 'index');
    // admin no rights routes
    $this->addRoute('admin_norights', 'admin/norights', 'AdminController', 'noRights');
    // admin debug routes
    $this->addRoute('admin_debug_logs', 'admin/debug/:log_type:', 'AdminController', 'debugLogs');
    // admin accepter routes
    $this->addRoute('admin_accepter', 'admin/accepter', 'AdminController', 'accepter');
    $this->addRoute('admin_accepter_search', 'admin/accepter/search', 'AdminController', 'accepterSearch');
    // admin comments routes
    $this->addRoute('admin_comments_overview', 'admin/comments', 'AdminController', 'commentsOverview');
    // admin spam routes
    $this->addRoute('admin_spam_overview', 'admin/spam', 'AdminController', 'spamOverview');

    // admin words routes
    $this->addRoute('admin_words_overview', 'admin/words', 'AdminController', 'wordsOverview');
    // admin rights routes
    $this->addRoute('admin_rights_overview', 'admin/rights', 'AdminController', 'rightsOverview');
    // admin activity routes
    $this->addRoute('admin_activity_overview', 'admin/activitylogs', 'AdminController', 'activityLogs');

    // admin massmailing
    $this->addRoute('admin_massmail', 'admin/massmail', 'AdminController', 'massmail');
    $this->addRoute('admin_massmail_create', 'admin/massmail/create', 'AdminController', 'massmailcreate');
    $this->addRoute('admin_massmail_details', 'admin/massmail/details/:id:', 'AdminController', 'massmaildetails');
    $this->addRoute('admin_massmail_details_mailing', 'admin/massmail/details/:id:/:type:', 'AdminController', 'massmaildetailsmailing');
    $this->addRoute('admin_massmail_details_mailing_pages', 'admin/massmail/details/:id:/:type:/page/:page:', 'AdminController', 'massmaildetailsmailing');
    $this->addRoute('admin_massmail_edit', 'admin/massmail/edit/:id:', 'AdminController', 'massmailedit');
    $this->addRoute('admin_massmail_enqueue', 'admin/massmail/enqueue/:id:', 'AdminController', 'massmailenqueue');
    $this->addRoute('admin_massmail_unqueue', 'admin/massmail/unqueue/:id:', 'AdminController', 'massmailunqueue');
    $this->addRoute('admin_massmail_getadminunits', 'admin/massmail/getadminunits/:countrycode:', 'AdminController', 'getadminunits');
    $this->addRoute('admin_massmail_getplaces', 'admin/massmail/getplaces/:countrycode:/:adminunit:', 'AdminController', 'getplaces');
    $this->addRoute('admin_massmail_trigger', 'admin/massmail/trigger/:id:', 'AdminController', 'massmailtrigger');
    $this->addRoute('admin_massmail_untrigger', 'admin/massmail/untrigger/:id:', 'AdminController', 'massmailuntrigger');
    
    // admin treasurer routes
    $this->addRoute('admin_treasurer_overview', 'admin/treasurer', 'AdminController', 'treasurerOverview');
    $this->addRoute('admin_treasurer_add_donation', 'admin/treasurer/add', 'AdminController', 'treasurerEditCreateDonation');
    $this->addRoute('admin_treasurer_edit_donation', 'admin/treasurer/edit/:id:', 'AdminController', 'treasurerEditCreateDonation');
    $this->addRoute('admin_treasurer_campaign_start', 'admin/treasurer/campaign/start', 'AdminController', 'treasurerStartDonationCampaign');
    $this->addRoute('admin_treasurer_campaign_stop', 'admin/treasurer/campaign/stop', 'AdminController', 'treasurerStopDonationCampaign');
    $this->addRoute('admin_treasurer_overview', 'admin/treasurer', 'AdminController', 'treasurerOverview');

    // Simple newsletter page
    $this->addRoute('newsletter', 'newsletter', 'NewsletterController', 'index');

    $this->addRoute('api_member','api/v1/member/:username:\.:format:', 'ApiController', 'memberAction');
    
    $this->addRoute('api_geonames','api/v1/geonames/:geonamesId:\.:format:', 'ApiController', 'geonamesAction');

    $this->addRoute('api_localize','api/v1/localize/:lang:/:code:\.:format:', 'ApiController', 'localizeAction');
    
    $this->addRoute('api_languages_list','api/v1/languages/list.:format:', 'ApiController', 'languagesAction');
    
    $this->addRoute('api_translations_list','api/v1/translations/:lang:\.:format:', 'ApiController', 'translationsAction');
    