<?php
/**
 * Created by PhpStorm.
 * User: oleg.tikhomirov
 * Date: 6/9/14
 * Time: 4:56 PM
 */

namespace model;

/*
 * Session manager
 * */
class SessionManager {
    private $_sessionId;
    private $_cookieManager;

    public function __construct() {
        $this->_cookieManager = new CookieManager();
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
            }

            $this->_cookieManager->setCookie($prefix);
            $this->save($uid);
        }

        $query = db_select('oat_session', 'tbl')->fields('tbl');
        $query->condition('cookie_id', $this->_cookieManager->getCookie());
        $objects = $query->execute();

        $sessionId = 0;
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
        $this->remove();
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
    private function remove() {
        $sessionId = $this->getSessionId();

        if(!empty($sessionId)) {
            db_delete('oat_session')->condition('id', $sessionId)->execute();
        }
    }
}

/*
 * Cookie manager
 * */
class CookieManager {
    /*
     * Set cookie
     * */
    public function setCookie($prefix) {
        $cookieKey = uniqid($prefix);
        user_cookie_save(array('oat_cart_cookie' => $cookieKey));
    }

    /*
     * Check if cookie exists
     * */
    public function isCookieExists() {
        return !empty($_COOKIE['Drupal_visitor_oat_cart_cookie']);
    }

    /*
     * Get cookie value
     * */
    public function getCookie() {
        return $_COOKIE['Drupal_visitor_oat_cart_cookie'];
    }

    /*
     * Remove session
     * */
    public function removeCookie() {
        user_cookie_delete('oat_cart_cookie');
    }
}