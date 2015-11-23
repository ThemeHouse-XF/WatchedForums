<?php

class ThemeHouse_WatchedForums_Listener_LoadClass extends ThemeHouse_Listener_LoadClass
{

    protected function _getExtendedClasses()
    {
        return array(
            'ThemeHouse_WatchedForums' => array(
                'model' => array(
                    'XenForo_Model_ForumWatch',
                    'XenForo_Model_Forum'
                ), /* END 'model' */
                'controller' => array(
                    'XenForo_ControllerPublic_Watched'
                ), /* END 'controller' */
            ), /* END 'ThemeHouse_WatchedForums' */
        );
    } /* END _getExtendedClasses */

    public static function loadClassModel($class, array &$extend)
    {
        $extend = self::createAndRun('ThemeHouse_WatchedForums_Listener_LoadClass', $class, $extend, 'model');
    } /* END loadClassModel */

    public static function loadClassController($class, array &$extend)
    {
        $extend = self::createAndRun('ThemeHouse_WatchedForums_Listener_LoadClass', $class, $extend, 'controller');
    } /* END loadClassController */
}