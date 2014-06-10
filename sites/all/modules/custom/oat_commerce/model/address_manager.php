<?php
/*
 * Addresses manager
 * */
class AddressManager {
    /**
     * Save new address
     * @params
     *  $uid - (int) User ID
     *  $sid - (int) Session ID
     *  $addressData - (array) Address information
     * */
    public function saveAddress($uid, $sid, array $addressData) {
        $addressId = 0;

        if(!empty($uid) && !empty($addressData)) {
            $addressId = db_insert('oat_address')
                ->fields(array('uid', 'sid', 'city', 'street', 'house', 'building', 'flat', 'phone'))
                ->values(array($uid, $sid, $addressData['city'], $addressData['street'], $addressData['house'], $addressData['building'], $addressData['flat'], $addressData['phone']))
                ->execute();
        }

        return $addressId;
    }

    /**
     * Remove user address
     * @params
     *  $aid - (int) Address ID
     * */
    public function removeAddress($aid) {
        db_delete('oat_address')
            ->condition('aid', $aid)
            ->execute();
    }

    /**
     * Remove session address
     * @params
     *  $sid - (int) Session ID
     * */
    public function removeSessionAddress($sid) {
        db_delete('oat_address')
            ->condition('sid', $sid)
            ->execute();
    }

    /**
     * Get user addresses ordered by date DESC
     * @params
     *  $uid - (int) User ID
     * */
    public function getUserAddresses($uid) {
        $addresses = array();

        $query = db_select('oat_address', 'tbl')->fields('tbl');
        $query->condition('uid', $uid);
        $query->orderBy('created', 'DESC');
        $objects = $query->execute();
        while ($record = $objects->fetchAssoc()) {
            $addresses[] = $record;
        }

        return $addresses;
    }

    /**
     * Get user address
     * @params
     *  $uid - (int) User ID
     *  $aid - (int) Address ID
     * */
    public function getUserAddress($uid, $aid) {
        $addresses = array();

        $query = db_select('oat_address', 'tbl')->fields('tbl');
        $query->condition('uid', $uid);
        $query->condition('id', $aid);
        $objects = $query->execute();
        while ($record = $objects->fetchAssoc()) {
            $addresses = $record;
        }

        return $addresses;
    }

    /**
     * Check if address is valid
     * @params
     *  $addressData - (array) Address information
     * */
    public function isValidAddress(array $addressData) {
        if( empty($addressData) || empty($addressData['city']) || empty($addressData['street']) ||
            empty($addressData['house']) || empty($addressData['phone'])) {
            return false;
        }
        return true;
    }
} 