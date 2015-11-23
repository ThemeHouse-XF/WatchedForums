<?php

/**
 *
 * @see XenForo_Model_Forum
 */
class ThemeHouse_WatchedForums_Extend_XenForo_Model_Forum extends XFCP_ThemeHouse_WatchedForums_Extend_XenForo_Model_Forum
{

    /**
     * Determines if the forum can be watched with the given permissions.
     * This does not check forum viewing permissions.
     *
     * @param array $forum
     * @param string $errorPhraseKey
     * @param array|null $nodePermissions
     * @param array|null $viewingUser
     *
     * @return boolean
     */
    public function canWatchForum(array $forum, &$errorPhraseKey = '', array $nodePermissions = null,
        array $viewingUser = null)
    {
        $this->standardizeViewingUserReferenceForNode($forum['node_id'], $viewingUser, $nodePermissions);

        if (!parent::canWatchForum($forum, $errorPhraseKey, $nodePermissions, $viewingUser)) {
            return false;
        }

        if (!empty($forum['forum_is_watched'])) {
            return true;
        }

        return XenForo_Permission::hasContentPermission($nodePermissions, 'watchForum');
    } /* END canWatchForum */
}