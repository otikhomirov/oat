<?php
/**
 * Build admin orders form
 * @param $form
 * @param $form_state
 * @return array
 */
function _admin_view_orders_form($form, &$form_state) {
    // Sortable header
    $header = array(
        array('data' => t('Number'), 'field' => 'number'),
        array('data' => t('User'), 'field' => 'uid'),
        array('data' => t('Sum'), 'field' => 'sum'),
        array('data' => t('Status'), 'field' => 'status'),
        array('data' => t('Created'), 'field' => 'created'),
        array('data' => t('Updated'), 'field' => 'updated'),
        array('data' => t('Manage'))
    );

    $result = _build_orders_query($form_state);

    $unique_users = array();
    $rows = array();

    // Looping for filling the table rows
    foreach ($result as $order) {
        $user_name = 'Anonymous';
        $uid = 0;
        if(!empty($order->uid)) {
            if(!in_array($order->uid, array_keys($unique_users))) {
                $unique_users[$order->uid] = user_load($order->uid);
            }
            $user = $unique_users[$order->uid];
            $uid = $order->uid;
            $user_name = !empty($user->field_full_name['und'][0]['value']) ? $user->field_full_name['und'][0]['value'] : $user->name;
        }

        if(!empty($uid)) {
            $user_name = '<a href="/user/'.$uid.'" target="_blank">'.$user_name.'</a>';
        }

        //Status
        $status = 'Pending';
        switch($order->status) {
            case (OrderManager::APPROVED): $status = 'Approved'; break;
            case (OrderManager::PACKING): $status = 'Packing'; break;
            case (OrderManager::DELIVERED): $status = 'Delivered'; break;
            case (OrderManager::DELETED): $status = 'Deleted'; break;
            case (OrderManager::NOT_APPROVED): $status = 'Not approved'; break;
            case (OrderManager::NOT_DELIVERED): $status = 'Not delivered'; break;
            case (OrderManager::PENDING): $status = 'Pending'; break;
        }

        // Manage link
        $details_link = '<a href="/admin/commerce/orders/'.$order->uid.'/edit">Order Details</a>';

        // Order row
        $rows[] = array(
            $order->number,
            $user_name,
            $order->sum,
            $status,
            $order->created,
            $order->updated,
            $details_link
        );
    }

    $form = array();

    $form['filter'] = array(
        '#type' => 'fieldset',
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
        '#title' => t('Filter options')
    );

    // Fill users filter
    $users = array(-1 => '', 0 => 'Anonymous');
    foreach($unique_users as $uid => $user) {
        $users[$uid] = !empty($user->field_full_name['und'][0]['value']) ? $user->field_full_name['und'][0]['value'] : $user->name;;
    }

    $form['filter']['filter_user'] = array(
        '#type' => 'select',
        '#title' => t('User'),
        '#options' => $users,
        '#default_value' => !empty($form_state['input']['filter_user']) ? $form_state['input']['filter_user'] : -1
    );

    // Filter by status
    $form['filter']['filter_status'] = array(
        '#type' => 'select',
        '#title' => t('Status'),
        '#options' => array(
            0 => t(''),
            OrderManager::PENDING => t('Pending'),
            OrderManager::APPROVED => t('Approved'),
            OrderManager::PACKING => t('Packing'),
            OrderManager::DELIVERED => t('Delivered'),
            OrderManager::DELETED => t('Deleted'),
            OrderManager::NOT_APPROVED => t('Not approved'),
            OrderManager::NOT_DELIVERED => t('Not delivered'),
        ),
        '#default_value' => !empty($form_state['input']['filter_status']) ? $form_state['input']['filter_status'] : 0
    );

    // Filter by date
    $form['filter']['filter_created'] = array(
        '#type' => 'date_popup',
        '#date_timezone' => date_default_timezone(),
        '#date_format' => 'Y-m-d',
        '#date_year_range' => '-3:+3',
        '#title' => 'Date created',
        '#default_value' => !empty($form_state['input']['filter_created']['date']) ? $form_state['input']['filter_created']['date'] : '',
    );
    $form['filter']['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Filter'),
    );
    $form['filter']['reset_button'] = array(
        '#type' => 'submit',
        '#value' => t('Reset'),
    );

    $form['table'] = array(
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#empty' => t('There are no orders in your system.')
    );

    $form['pager'] = array('#markup' => theme('pager'));

  return $form;
}

function _admin_view_orders_form_submit($form, &$form_state) {
  if($form_state['triggering_element']['#value'] == 'Reset') {
    $form_state['input']['filter_user'] = -1;
    $form_state['input']['filter_status'] = 0;
    $form_state['input']['filter_created']['date'] = '';
  }
  $form_state['rebuild'] = TRUE;
}

/**
 * Build orders query and get result
 * @param $form_state
 * @return DatabaseStatementInterface|null
 * */
function _build_orders_query($form_state) {
    // Request
    $query = db_select('oat_order', 'p')
        ->fields('p')
        ->extend('TableSort')->extend('PagerDefault')->limit(20);

    // Filters condition
    if (isset($form_state['input']['filter_user']) && $form_state['input']['filter_user'] != -1) {
        $query->condition('uid', (int)$form_state['input']['filter_user']);
    }
    if (!empty($form_state['input']['filter_status'])) {
        $query->condition('status', $form_state['input']['filter_status']);
    }
    if (!empty($form_state['input']['filter_created']['date'])) {
        $date_from = date('Y-m-d H:i:s', strtotime($form_state['input']['filter_created']['date']));
        $date_to = date('Y-m-d H:i:s', strtotime($form_state['input']['filter_created']['date']) + 60*60*24);
        $query->condition('created', array($date_from, $date_to), 'BETWEEN');
    }

    // Sorting
    if(isset($_GET['sort']) && isset($_GET['order'])) {
        $sort = $_GET['sort'];
        // Which column will be sorted
        switch($_GET['order']){
            case 'Number':
                $order = 'number';
                break;
            case 'User':
                $order = 'uid';
                break;
            case 'Sum':
                $order = 'sum';
                break;
            case 'Status':
                $order = 'status';
                break;
            case 'Created':
                $order = 'created';
                break;
            case 'Updated':
                $order = 'updated';
                break;
            default:
                $order = 'created';
        }
    } else {
        // Default sort
        $sort = 'DESC';
        $order = ' created';
    }
    // Set order by
    $query->orderBy($order, $sort);
    // Execute query
    return $query->execute();
}