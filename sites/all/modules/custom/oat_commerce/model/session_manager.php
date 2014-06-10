<?php
/*
 * Session manager
 * */
class SessionManager {
    private $_sessionId;
    private $_cookieManager;
    private $_isAnonymous;

    public function __construct() {
        $this->_cookieManager = new CookieManager();
        $this->_isAnonymous = true;
    }

    /*
     * Get session ID
     * */
    public function getSessionId() {
        if(empty($this->_sessionId)) {
            $this->start();
        }
        return $this->_sessionId;
    }

    /*
     * Start user session
     */
    public function start() {
        $uid = 0;
        if(!$this->_cookieManager->isCookieExists()) {
            // session key prefix (UserId if user is logged in)
            $prefix = 'anon';
            if(user_is_logged_in()) {
                global $user;
                $uid = $user->uid;
                $prefix = $uid;
                $this->_isAnonymous = false;
            }

            $this->_cookieManager->setCookie($prefix);
            $this->save($uid);
        }

        $query = db_select('oat_session', 'tbl')->fields('tbl');
        $query->condition('cookie_id', $this->_cookieManager->getCookie());
        $objects = $query->execute();

        while ($record = $objects->fetchAssoc()) {
            $sessionId = $record['id'];
            $this->_sessionId = $sessionId;
            break;
        }
    }

    /*
     * Finish current session
     * */
    public function close() {
        $this->_cookieManager->removeCookie();
        $this->removeCurrentSession();
        // Remove saved address if user is anonymous
        if($this->_isAnonymous) {
            $addressManager = new AddressManager();
            $addressManager->removeSessionAddress($this->getSessionId());
        }
    }

    /*
     * Save session value
     * */
    private function save($uid) {
        db_insert('oat_session')
            ->fields(array('cookie_id', 'uid'))
            ->values(array($this->_cookieManager->getCookie(), $uid))
            ->execute();
    }

    /*
     * Remove session
     * */
    public function removeCurrentSession() {
        self::removeSessionsByIds($this->getSessionId());
    }

    /**
     * Remove sessions by ids
     * */
    public static function removeSessionsByIds($ids) {
        if(!empty($ids)) {
            if(!is_array($ids)) {
                $ids = array($ids);
            }

            db_delete('oat_session')->condition('id', $ids, 'IN')->execute();
        }
    }

    /**
     * Get ids of not closed sessions of anonymous users for the last days
     * */
    public static function getSessionsToRemove() {
        $query = db_select('oat_session', 'tbl')->fields('tbl');
        $date = strtotime('today - 1 day');
        $query->condition('created', date('Y-m-d H:i:s', $date), '<');
        $query->condition('uid', 0);
        $objects = $query->execute();
        $ids = array();
        while ($record = $objects->fetchAssoc()) {
            $ids[] = $record['id'];
        }
        return $ids;
    }

    /**
     * Check session if exists
     * */
    public function isSessionExists() {
        $query = db_select('oat_session', 'tbl')->fields('tbl');
        $query->condition('id', $this->getSessionId());
        $objects = $query->execute();
        return $objects->rowCount();
    }
}

/*
 * Cookie manager
 * */
class CookieManager {
    private $_cookie = null;
    /*
     * Set cookie
     * */
    public function setCookie($prefix) {
        $cookieKey = uniqid($prefix);
        user_cookie_save(array('oat_cart_cookie' => $cookieKey));
        $this->_cookie = $cookieKey;
    }

    /*
     * Check if cookie exists
     * */
    public function isCookieExists() {
        return !empty($_COOKIE['Drupal_visitor_oat_cart_cookie']) || !empty($this->_cookie);
    }

    /*
     * Get cookie value
     * */
    public function getCookie() {
        return !empty($_COOKIE['Drupal_visitor_oat_cart_cookie']) ? $_COOKIE['Drupal_visitor_oat_cart_cookie'] : $this->_cookie;
    }

    /*
     * Remove session
     * */
    public function removeCookie() {
        user_cookie_delete('oat_cart_cookie');
    }
}