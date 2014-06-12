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
     * Returns array of items in cart
     * */
    public function itemsInCart() {
        $items = array();
        $query = db_select('oat_cart', 'tbl')->fields('tbl');
        $query->condition('sid', $this->_sessionManager->getSessionId());
        $objects = $query->execute();
        while ($record = $objects->fetchAssoc()) {
            $items[$record['nid']] = $record;
        }
        return $items;
    }

    /*
     * Add item to cart
     * */
    public function addToCart($nid, $quantity) {
        if(!in_array($nid, array_keys($this->itemsInCart()))) {
            return db_insert('oat_cart')
                ->fields(array('sid', 'nid', 'quantity'))
                ->values(array($this->_sessionManager->getSessionId(), $nid, $quantity))
                ->execute();
        }
        return -1;
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
            drupal_goto(drupal_get_path_alias('node/'.CART_PAGE_ID));
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
            global $user;
            $uid = (int)$user->uid;

            // Save order
            $orderId = db_insert('oat_order')
                ->fields(array('number', 'sum', 'uid', 'aid', 'status'))
                ->values(array('ORD'.uniqid(), 0, $uid, $aid, 0))
                ->execute();

            $sum = 0;
            // Save order items
            if(!empty($orderId)) {
                $nodes = node_load_multiple(array_keys($items));

                $query = db_insert('oat_order_items')->fields(array('oid', 'nid', 'quantity'));
                foreach ($items as $nid => $item) {
                    $query->values(array($orderId, $nid, $item['quantity']));
                    $sum += (int)$this->getOrderItemCost($nodes[$nid], $item['quantity']);
                }
                $query->execute();
            }

            // Update order sum
            db_update('oat_order')
                ->fields(array('sum' => $sum))
                ->condition('id', $orderId)
                ->execute();
        }
    }

    /**
     * Return cost of order item
     * */
    public static function getOrderItemCost($node, $quantity) {
        // TODO: Apply discounters
        return (int)$node->field_price['und'][0]['value'] * $quantity;
    }
} 