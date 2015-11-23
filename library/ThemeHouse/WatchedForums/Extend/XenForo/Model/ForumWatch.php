<?php

/**
 *
 * @see XenForo_Model_ForumWatch
 */
class ThemeHouse_WatchedForums_Extend_XenForo_Model_ForumWatch extends XFCP_ThemeHouse_WatchedForums_Extend_XenForo_Model_ForumWatch
{

    /**
     * Get the threads watched by a specific user.
     *
     * @param integer $userId
     * @param boolean $newOnly If true, only gets unread threads.
     * @param array $fetchOptions Thread fetch options (uses all valid for
     * XenForo_Model_Thread).
     *
     * @return array Format: [thread_id] => info
     */
    public function getThreadsWatchedByUser($userId, $newOnly, array $fetchOptions = array())
    {
        $fetchOptions['readUserId'] = $userId;
        $fetchOptions['includeForumReadDate'] = true;

        $joinOptions = $this->_getThreadModel()->prepareThreadFetchOptions($fetchOptions);
        $limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

        if ($newOnly) {
            $cutoff = XenForo_Application::$time - (XenForo_Application::get('options')->readMarkingDataLifetime * 86400);
            $newOnlyClause = '
				AND thread.last_post_date > ' . $cutoff . '
				AND thread.last_post_date > COALESCE(thread_read.thread_read_date, 0)
				AND thread.last_post_date > COALESCE(forum_read.forum_read_date, 0)
			';
        } else {
            $newOnlyClause = '';
        }

        return $this->fetchAllKeyed(
            $this->limitQueryResults(
                '
				SELECT thread.*
					' . $joinOptions['selectFields'] . '
				FROM xf_forum_watch AS forum_watch
				INNER JOIN xf_thread AS thread ON
					(thread.node_id = forum_watch.node_id)
				' . $joinOptions['joinTables'] . '
				WHERE forum_watch.user_id = ?
					AND thread.discussion_state = \'visible\'
					' . $newOnlyClause . '
				ORDER BY thread.last_post_date DESC
			', $limitOptions['limit'], $limitOptions['offset']), 'thread_id',
            $userId);
    } /* END getThreadsWatchedByUser */

    /**
     * Gets the total number of threads a user is watching.
     *
     * @param integer $userId
     *
     * @return integer
     */
    public function countThreadsWatchedByUser($userId)
    {
        return $this->_getDb()->fetchOne(
            '
			SELECT COUNT(*)
			FROM xf_forum_watch AS forum_watch
			INNER JOIN xf_thread AS thread ON
				(thread.node_id = forum_watch.node_id)
			WHERE forum_watch.user_id = ?
				AND thread.discussion_state = \'visible\'
		', $userId);
    } /* END countThreadsWatchedByUser */

    /**
     * Determines if a user can view the lists of threads in watched forums
     *
     * @param string $errorPhraseKey
     * @param array|null $viewingUser
     *
     * @return boolean
     */
    public function canViewThreadsInWatchedForums(&$errorPhraseKey = '', array $viewingUser = null)
    {
        $this->standardizeViewingUserReference($viewingUser);

        if (!$viewingUser['user_id'] ||
             !XenForo_Permission::hasPermission($viewingUser['permissions'], 'general', 'viewThreadsWatchedForums')) {
            return false;
        }

        return true;
    } /* END canViewThreadsInWatchedForums */
}