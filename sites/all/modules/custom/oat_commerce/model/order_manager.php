<?php
/**
 * Order manager
 */
class OrderManager {
    const PENDING = 1;
    const APPROVED = 2;
    const PACKING = 3;
    const DELIVERING = 4;
    const DELIVERED = 5;
    const DELETED = 6;
    const NOT_APPROVED = 7;
    const NOT_DELIVERED = 8;

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
     * @param $aid - (int) AddressID
     * @param $comment - (text) Order comment
     * */
    public function send($aid, $comment = '') {
        if($this->_sessionManager->isSessionExists()) {
            $this->save($aid, $comment);
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
    private function save($aid, $comment = '') {
        $items = $this->itemsInCart();
        if(!empty($items)) {
            global $user;
            $uid = (int)$user->uid;

            // Save order
            $orderId = db_insert('oat_order')
                ->fields(array('number', 'sum', 'comment', 'uid', 'aid', 'status'))
                ->values(array('', 0, $comment, $uid, $aid, 1,))
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
                ->fields(array('sum' => $sum, 'number' => 'ORD'.$orderId.date('Y')))
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

    /**
     * Get all user orders
     * @params
     *  $uid - (int) User ID
     * */
    public function getUserOrders($uid) {
        $items = array();
        $query = db_select('oat_order', 'tbl')->fields('tbl');
        $query->condition('uid', $uid);
        $query->orderBy('created', 'DESC');
        $objects = $query->execute();
        while ($record = $objects->fetchAssoc()) {
            $items[] = $record;
        }
        return $items;
    }

    /**
     * Remove order
     * @params
     *  $uid - (int) User ID
     *  $oid - (int) Order ID
     * */
    public function deleteOrder($oid) {
        db_update('oat_order')
            ->fields(array('status' => self::DELETED, 'deleted' => date('Y-m-d H:i:s', strtotime('now')), 'updated' => date('Y-m-d H:i:s', strtotime('now'))))
            ->condition('id', $oid)
            ->execute();
    }

    /**
     * Update order status
     * */
    public function updateOrderStatus($oid, $status) {
        db_update('oat_order')
            ->fields(array('status' => $status, 'updated' => date('Y-m-d H:i:s', strtotime('now'))))
            ->condition('id', $oid)
            ->execute();
    }

    /**
     * Get order info
     * @param $oid - (int) Order ID
     * @return array()
     * */
    public function getOrderById($oid) {
        $query = db_select('oat_order', 'tbl')->fields('tbl');
        $query->condition('id', $oid);
        $object = $query->execute();
        return $object->fetchAssoc();
    }

    /**
     * Get specific order items
     * @param $oid - (int) Order ID
     * @return array()
     * */
    function getOrderItems($oid) {
        $items = array();
        $query = db_select('oat_order_items', 'tbl')->fields('tbl');
        $query->condition('oid', $oid);
        $objects = $query->execute();
        while ($record = $objects->fetchAssoc()) {
           $items[$record['nid']] = $record;
        }
        return $items;
    }

    /**
     * Get order address info
     * @param $oid - (int) Order ID
     * @return array()
     * */
    function getOrderAddress($oid) {
        $query = db_select('oat_address', 'adr')->fields('adr');
        $query->leftJoin('oat_order', 'ord', 'ord.aid = adr.id');
        $query->condition('ord.id', $oid);
        $object = $query->execute();
        return $object->fetchAssoc();
    }
} 