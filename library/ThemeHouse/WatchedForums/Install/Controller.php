<?php

class ThemeHouse_WatchedForums_Install_Controller extends ThemeHouse_Install
{

    protected $_resourceManagerUrl = 'http://xenforo.com/community/resources/watched-forums.3818/';

    protected $_minVersionId = 1020000;

    protected $_minVersionString = '1.2.0';

    protected function _getPermissionEntries()
    {
        return array(
            'general' => array(
                'viewThreadsWatchedForums' => array(
                    'permission_group_id' => 'forum', /* END 'permission_group_id' */
                    'permission_id' => 'viewContent', /* END 'permission_id' */
                ), /* END 'viewThreadsWatchedForums' */
            ), /* END 'general' */
            'forum' => array(
                'watchForum' => array(
                    'permission_group_id' => 'general', /* END 'permission_group_id' */
                    'permission_id' => 'viewContent', /* END 'permission_id' */
                ), /* END 'watchForum' */
            ), /* END 'forum' */
        );
    } /* END _getPermissionEntries */
}