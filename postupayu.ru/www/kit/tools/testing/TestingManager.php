<?php

class TestingManager {
    /*
     * Синглтон
     */

    /** @return TestingResultDB */
    public function getTestingResults($idTesting) {
        if (AuthManager::isAuthorized()) {
            return TestingBean::inst()->getTestingResult($idTesting, AuthManager::getUserId());
        }
        return null;
    }

    public function updateTestingResults($idTesting, $time, array $tasks) {
        TestingBean::inst()->updateTestingResults($idTesting, AuthManager::getUserId(), (int) $time, $tasks);
    }

    public function dropTestingResults($idTestingRes) {
        TestingBean::inst()->dropTestingResults($idTestingRes, AuthManager::getUserId());
    }

    private static $instance = NULL;

    /** @return TestingManager */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new TestingManager();
        }
        return self::$instance;
    }

}

?>
