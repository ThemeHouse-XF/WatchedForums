<?php

/**
 *
 * @see XenForo_ControllerPublic_Watched
 */
class ThemeHouse_WatchedForums_Extend_XenForo_ControllerPublic_Watched extends XFCP_ThemeHouse_WatchedForums_Extend_XenForo_ControllerPublic_Watched
{

    /**
     *
     * @see XenForo_ControllerPublic_Watched::actionForums()
     */
    public function actionForums()
    {
        $response = parent::actionForums();

        if ($response instanceof XenForo_ControllerResponse_View) {
            $forumWatchModel = $this->_getForumWatchModel();

            $response->params['canViewThreadsInWatchedForums'] = $forumWatchModel->canViewThreadsInWatchedForums();
        }

        return $response;
    } /* END actionForums */

    public function actionForumsAdd()
    {
        $forumModel = $this->_getForumModel();

        /* @var $forumWatchModel XenForo_Model_ForumWatch */
        $forumWatchModel = $this->_getForumWatchModel();

        $input = $this->_input->filter(
            array(
                'node_ids' => array(
                    XenForo_Input::UINT,
                    'array' => true
                ),
                'available_node_ids' => array(
                    XenForo_Input::UINT,
                    'array' => true
                ),
                'send_alert' => XenForo_Input::BOOLEAN,
                'send_email' => XenForo_Input::BOOLEAN,
                'notify_on' => XenForo_Input::STRING,
                'override' => XenForo_Input::BOOLEAN
            ));

        $userId = XenForo_Visitor::getUserId();

        if ($this->isConfirmedPost()) {
            $fetchOptions = array(
                'watchUserId' => $userId
            );

            $forums = $forumModel->getForumsByIds($input['available_node_ids'], $fetchOptions);

            foreach ($forums as $forumId => $forum) {
                if (!$forumModel->canWatchForum($forum)) {
                    continue;
                }

                if ($forum['forum_is_watched']) {
                    if (!in_array($forumId, $input['node_ids'])) {
                        $forumWatchModel->setForumWatchState(XenForo_Visitor::getUserId(), $forumId, 'delete');
                        continue;
                    } elseif (!$input['override']) {
                        continue;
                    }
                }

                $notifyOn = $input['notify_on'];
                if ($notifyOn) {
                    if ($forum['allowed_watch_notifications'] == 'none') {
                        $notifyOn = '';
                    } elseif ($forum['allowed_watch_notifications'] == 'thread' && $notifyOn == 'message') {
                        $notifyOn = 'thread';
                    }
                }

                $sendAlert = $input['send_alert'];
                $sendEmail = $input['send_email'];

                if (in_array($forumId, $input['node_ids'])) {
                    $forumWatchModel->setForumWatchState(XenForo_Visitor::getUserId(), $forumId, $notifyOn, $sendAlert,
                        $sendEmail);
                }
            }

            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildPublicLink('watched/forums'));
        } else {
            /* @var $nodeModel XenForo_Model_Node */
            $nodeModel = $this->getModelFromCache('XenForo_Model_Node');

            $options = $nodeModel->getNodeOptionsArray($nodeModel->getViewableNodeList());

            $userForumWatches = $forumWatchModel->getUserForumWatchByUser($userId);

            foreach ($userForumWatches as $userForumWatch) {
                if (!empty($options[$userForumWatch['node_id']])) {
                    $options[$userForumWatch['node_id']]['selected'] = true;
                }
            }

            $viewParams = array(
                'options' => $options
            );

            return $this->responseView('ThemeHouse_WatchedForums_ViewPublic_Watched_ForumsAdd',
                'th_watch_forums_add_watchedforums', $viewParams);
        }
    } /* END actionForumsAdd */

    public function actionForumsManage()
    {
        $action = $this->_input->filterSingle('act', XenForo_Input::STRING);

        if ($this->isConfirmedPost()) {
            $this->_getForumWatchModel()->setForumWatchStateForAll(XenForo_Visitor::getUserId(), $action);

            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
                $this->getDynamicRedirect(XenForo_Link::buildPublicLink('watched/forums')));
        } else {
            $viewParams = array(
                'action' => $action
            );

            return $this->responseView('ThemeHouse_WatchedForums_ViewPublic_Watched_ForumsManage',
                'th_watch_forums_manage_watchedforums', $viewParams);
        }
    } /* END actionForumsManage */

    /**
     * List of all new watched forum threads.
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionForumsThreads()
    {
        $this->_assertCanViewThreadsInWatchedForums();

        /* @var $forumWatchModel XenForo_Model_ForumWatch */
        $forumWatchModel = $this->_getForumWatchModel();

        /* @var $threadWatchModel XenForo_Model_ThreadWatch */
        $threadWatchModel = $this->_getThreadWatchModel();

        $visitor = XenForo_Visitor::getInstance();

        $newThreads = $forumWatchModel->getThreadsWatchedByUser($visitor['user_id'], true,
            array(
                'join' => XenForo_Model_Thread::FETCH_FORUM | XenForo_Model_Thread::FETCH_USER,
                'readUserId' => $visitor['user_id'],
                'postCountUserId' => $visitor['user_id'],
                'permissionCombinationId' => $visitor['permission_combination_id'],
                'limit' => XenForo_Application::get('options')->discussionsPerPage
            ));
        $newThreads = $forumWatchModel->unserializePermissionsInList($newThreads, 'node_permission_cache');
        $newThreads = $threadWatchModel->getViewableThreadsFromList($newThreads);

        $newThreads = $this->_prepareWatchedThreads($newThreads);

        $viewParams = array(
            'newThreads' => $newThreads
        );

        return $this->responseView('ThemeHouse_WatchedForums_ViewPublic_Watched_Threads',
            'th_watch_threads_watchedforums', $viewParams);
    } /* END actionForumsThreads */

    /**
     * List of all watched forum threads.
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionForumsThreadsAll()
    {
        $this->_assertCanViewThreadsInWatchedForums();

        /* @var $forumWatchModel XenForo_Model_ForumWatch */
        $forumWatchModel = $this->_getForumWatchModel();

        /* @var $threadWatchModel XenForo_Model_ThreadWatch */
        $threadWatchModel = $this->_getThreadWatchModel();

        $visitor = XenForo_Visitor::getInstance();

        $page = $this->_input->filterSingle('page', XenForo_Input::UINT);
        $threadsPerPage = XenForo_Application::get('options')->discussionsPerPage;

        $threads = $forumWatchModel->getThreadsWatchedByUser($visitor['user_id'], false,
            array(
                'join' => XenForo_Model_Thread::FETCH_FORUM | XenForo_Model_Thread::FETCH_USER,
                'readUserId' => $visitor['user_id'],
                'postCountUserId' => $visitor['user_id'],
                'permissionCombinationId' => $visitor['permission_combination_id'],
                'perPage' => $threadsPerPage,
                'page' => $page
            ));
        $threads = $forumWatchModel->unserializePermissionsInList($threads, 'node_permission_cache');
        $threads = $threadWatchModel->getViewableThreadsFromList($threads);

        $threads = $this->_prepareWatchedThreads($threads);

        $totalThreads = $forumWatchModel->countThreadsWatchedByUser($visitor['user_id']);

        $this->canonicalizePageNumber($page, $threadsPerPage, $totalThreads, 'watched/threads/all');

        $viewParams = array(
            'threads' => $threads,
            'page' => $page,
            'threadsPerPage' => $threadsPerPage,
            'totalThreads' => $totalThreads
        );

        return $this->responseView('ThemeHouse_WatchedForums_ViewPublic_Watched_ThreadsAll',
            'th_watch_threads_all_watchedforums', $viewParams);
    } /* END actionForumsThreadsAll */

    /**
     * Asserts that the currently browsing user can view threads in watched
     * forums.
     */
    protected function _assertCanViewThreadsInWatchedForums()
    {
        if (!$this->_getForumWatchModel()->canViewThreadsInWatchedForums($errorPhraseKey)) {
            throw $this->getErrorOrNoPermissionResponseException($errorPhraseKey);
        }
    } /* END _assertCanViewThreadsInWatchedForums */

    /**
     *
     * @return XenForo_Model_Forum
     */
    protected function _getForumModel()
    {
        return $this->getModelFromCache('XenForo_Model_Forum');
    } /* END _getForumModel */
}