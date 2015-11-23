<?php

class ThemeHouse_WatchedForums_Listener_FileHealthCheck
{

    public static function fileHealthCheck(XenForo_ControllerAdmin_Abstract $controller, array &$hashes)
    {
        $hashes = array_merge($hashes,
            array(
                'library/ThemeHouse/WatchedForums/Extend/XenForo/ControllerPublic/Watched.php' => '1956431a38aa23833275fa6e8a844020',
                'library/ThemeHouse/WatchedForums/Extend/XenForo/Model/Forum.php' => '51fa42139ca77cd8ac6ef0b1e189ce1c',
                'library/ThemeHouse/WatchedForums/Extend/XenForo/Model/ForumWatch.php' => '7480d4843ff7e3739a8bb7359ead3687',
                'library/ThemeHouse/WatchedForums/Install/Controller.php' => 'b87c11d8f08736bdd99ad50051b1951a',
                'library/ThemeHouse/WatchedForums/Listener/LoadClass.php' => '6436e6b2f4d9a8d264e8d463a097c68f',
                'library/ThemeHouse/Install.php' => '18f1441e00e3742460174ab197bec0b7',
                'library/ThemeHouse/Install/20151109.php' => '2e3f16d685652ea2fa82ba11b69204f4',
                'library/ThemeHouse/Deferred.php' => 'ebab3e432fe2f42520de0e36f7f45d88',
                'library/ThemeHouse/Deferred/20150106.php' => 'a311d9aa6f9a0412eeba878417ba7ede',
                'library/ThemeHouse/Listener/ControllerPreDispatch.php' => 'fdebb2d5347398d3974a6f27eb11a3cd',
                'library/ThemeHouse/Listener/ControllerPreDispatch/20150911.php' => 'f2aadc0bd188ad127e363f417b4d23a9',
                'library/ThemeHouse/Listener/InitDependencies.php' => '8f59aaa8ffe56231c4aa47cf2c65f2b0',
                'library/ThemeHouse/Listener/InitDependencies/20150212.php' => 'f04c9dc8fa289895c06c1bcba5d27293',
                'library/ThemeHouse/Listener/LoadClass.php' => '5cad77e1862641ddc2dd693b1aa68a50',
                'library/ThemeHouse/Listener/LoadClass/20150518.php' => 'f4d0d30ba5e5dc51cda07141c39939e3',
            ));
    }
}