<?php
/**
 * Order manager
 */
class OrderManager {
    private $_sessionManager;

    public function __construct() {
        $this->_sessionManager = new SessionManager();
    }

    /*
     * Get number of items in cart
     * */
    public function getNumberOfItemsInCart() {
        return count($this->itemsInCart());
    }

    /*
     * Init cart with previous items
     * */
    private function itemsInCart() {
        $items = array();
        $query = db_select('oat_cart', 'tbl')->fields('tbl');
        $query->condition('sid', $this->_sessionManager->getSessionId());
        $objects = $query->execute();
        while ($record = $objects->fetchAssoc()) {
            $items = $record;
        }
        return $items;
    }

    /*
     * Add item to cart
     * */
    public function addToCart($nid, $quantity) {
        db_insert('oat_cart')
            ->fields(array('sid', 'nid', 'quantity'))
            ->values(array($this->_sessionManager->getSessionId(), $nid, $quantity))
            ->execute();
    }

    /*
     * Remove item from cart
     * */
    public function removeFromCart($nid) {
        db_delete('oat_cart')
            ->condition('sid', $this->_sessionManager->getSessionId())
            ->condition('nid', $nid)
            ->execute();
    }

    /*
     * Change quantity in cart
     * */
    public function changeQuantity($nid, $quantity = 0) {
        if(empty($quantity)) {
            $this->removeFromCart($nid);
        } else {
            db_update('oat_cart')
                ->fields(array('quantity' => $quantity))
                ->condition('sid', $this->_sessionManager->getSessionId())
                ->condition('nid', $nid)
                ->execute();
        }
    }

    /**
     * Send order, clear session
     * @params
     *  $aid - (int) AddressID
     * */
    public function send($aid) {
        if($this->_sessionManager->isSessionExists()) {
            $this->save($aid);
            $this->clearCart();
            $this->_sessionManager->close();
        } else {
            drupal_set_message('Your session has been expired!');
            drupal_goto(CART_PAGE);
        }
    }

    /*
     * Clear cart
     * */
    private function clearCart() {
        db_delete('oat_cart')
            ->condition('sid', $this->_sessionManager->getSessionId())
            ->execute();
    }

    /*
     * Save order
     * */
    private function save($aid) {
        $items = $this->itemsInCart();
        if(!empty($items)) {
            $uid = 0;
            if(user_is_logged_in()) {
                global $user;
                $uid = $user->uid;
            }

            // Save order
            $orderId = db_insert('oat_order')
                ->fields(array('number', 'uid', 'aid', 'status'))
                ->values(array(uniqid(), $uid, $aid, 0))
                ->execute();

            // Save order items
            if(!empty($orderId)) {
                foreach ($items as $item) {
                    db_insert('oat_order_items')
                        ->fields(array('oid', 'nid', 'quantity'))
                        ->values(array($orderId, $item['nid'], $item['quantity']))
                        ->execute();
                }
            }
        }
    }
} 