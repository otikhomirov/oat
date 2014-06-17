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
            case (OrderManager::DELIVERING): $status = 'Delivering'; break;
            case (OrderManager::DELIVERED): $status = 'Delivered'; break;
            case (OrderManager::DELETED): $status = 'Deleted'; break;
            case (OrderManager::NOT_APPROVED): $status = 'Not approved'; break;
            case (OrderManager::NOT_DELIVERED): $status = 'Not delivered'; break;
            case (OrderManager::PENDING): $status = 'Pending'; break;
        }

        // Manage link
        $details_link = '<a href="/admin/commerce/orders/'.$order->id.'/edit">Order Details</a>';

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
            OrderManager::DELIVERING => t('Delivering'),
            OrderManager::DELIVERED => t('Delivered'),
            OrderManager::DELETED => t('Deleted'),
            OrderManager::NOT_APPROVED => t('Not approved'),
            OrderManager::NOT_DELIVERED => t('Not delivered'),
        ),
        '#multiple' => TRUE,
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

    // Filter: hide deleted. Enabled by default
    $form['filter']['hide_deleted'] = array(
        '#type' => 'radios',
        '#title' => 'Hide deleted orders',
        '#name' => 'hide_deleted',
        '#options' => array(
            '1' => t('Yes'),
            '0' => t('No')
        ),
        '#default_value' => !empty($form_state['input']['hide_deleted']) ? $form_state['input']['hide_deleted'] : 1,
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

    if (!empty($form_state['input']['filter_status']) && (count($form_state['input']['filter_status']) != 1 && $form_state['input']['filter_status'][0] != 0)) {
        $query->condition('status', $form_state['input']['filter_status'], 'IN');
    }
    if (!empty($form_state['input']['filter_created']['date'])) {
        $date_from = date('Y-m-d H:i:s', strtotime($form_state['input']['filter_created']['date']));
        $date_to = date('Y-m-d H:i:s', strtotime($form_state['input']['filter_created']['date']) + 60*60*24);
        $query->condition('created', array($date_from, $date_to), 'BETWEEN');
    }

    if (!isset($form_state['input']['hide_deleted']) || $form_state['input']['hide_deleted'] == 1) {
        $query->condition('status', OrderManager::DELETED, '<>');
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

/**
 * Manage order form
 * @param $form
 * @param $form_state
 * @param $oid - Order ID
 * @return array
 * */
function _admin_order_form($form, $form_state, $oid = NULL) {
    $form = array();
    // Check if order id is not empty
    if(!empty($oid)) {
        $order_manager = new OrderManager();
        $order = $order_manager->getOrderById($oid);
        $address = $order_manager->getOrderAddress($oid);

        // Get next status
        $next_status_id = 1;
        $next_status_text = 'Pending';
        $current_status_text = 'Pending';
        switch($order['status']) {
            case OrderManager::PENDING:
                $next_status_id = OrderManager::APPROVED;
                $next_status_text = 'Approved';
                $current_status_text = 'PENDING';
                break;
            case OrderManager::APPROVED:
                $next_status_id = OrderManager::PACKING;
                $next_status_text = 'Packing';
                $current_status_text = 'APPROVED';
                break;
            case OrderManager::PACKING:
                $next_status_id = OrderManager::DELIVERING;
                $next_status_text = 'Delivering';
                $current_status_text = 'PACKING';
                break;
            case OrderManager::DELIVERING:
                $next_status_id = OrderManager::DELIVERED;
                $next_status_text = 'Delivered';
                $current_status_text = 'DELIVERING';
                break;
            case OrderManager::DELIVERED:
                $current_status_text = 'DELIVERED';
                break;
            case OrderManager::NOT_DELIVERED:
                $current_status_text = 'NOT DELIVERED';
                break;
            case OrderManager::NOT_APPROVED:
                $current_status_text = 'NOT APPROVED';
                break;
            case OrderManager::DELETED:
                $current_status_text = 'DELETED';
                break;
        }

        $user_link = 'Anonymous';
        $email = $address['email'];
        if(!empty($order['uid'])) {
            $user = user_load($order['uid']);
            $order_user_name = !empty($user->field_full_name['und'][0]['value']) ? $user->field_full_name['und'][0]['value'] : $user->name;
            $user_link = '<a href="/user/'.$order['uid'].'" target="_blank">'.$order_user_name.'</a>';
            if(empty($email)) {
                $email = $user->mail;
            }
        }

        $form['order_id'] = array(
            '#type' => 'hidden',
            '#value' => $oid,
        );

        $form['status'] = array(
            '#type' => 'hidden',
            '#value' => $next_status_id,
        );

        $form['order'] = array(
            '#type' => 'markup',
            '#markup' => '
                <div class="order-info">
                    <div><b>Order Number: '.$order['number'].'</b></div>
                    <div>Status: <b>'.$current_status_text.'</b></div>
                    <div>Created: '.$order['created'].'</div>
                    <div>Last update: '.$order['updated'].'</div>
                </div>
            ',
        );

        $form['address'] = array(
            '#type' => 'fieldset',
            '#title' => t('Order delivary and contact info'),
        );

        $form['address']['address_info'] = array(
            '#type' => 'markup',
            '#markup' => '
                <div class="address-info">
                    <div>User: '.$user_link.'</div>
                    <div>Phone: '.$address['phone'].'</div>
                    <div>E-mail: '.$email.'</div>
                    <div>Address: Город '.$address['city'].', ул. '.$address['street'].', д.'.$address['house'].', к.'.$address['building'].', кв.'.$address['flat'].'</div>
                    <div>Comment:</div>
                    <div>'.$order['comment'].'</div>
                </div>
            ',
        );

        /*** Order items ***/
        $items = $order_manager->getOrderItems($oid);
        $nodes = node_load_multiple(array_keys($items));

        $form['items'] = array(
            '#type' => 'fieldset',
            '#title' => t('List of order items'),
        );
        $form['items']['list'] = array(
            '#title' => t('Order Items'),
            '#tree' => TRUE,
            '#theme' => 'oat_admin_order_items_form_theme',
            '#id' => 'table-of-items'
        );
        foreach($items as $nid => $item) {
            $form['items']['list'][$nid]['title'] = array(
                '#type' => 'markup',
                '#markup' => '<a href="/'.drupal_get_path_alias('node/'.$nid).'" target="_blank">'.(!empty($nodes[$nid]->title) ? $nodes[$nid]->title : '').'</a>',
            );
            $form['items']['list'][$nid]['quantity'] = array(
                '#type' => 'markup',
                '#markup' => !empty($item['quantity']) ? $item['quantity'] : 0,
            );
            $available = !empty($nodes[$nid]->field_count['und'][0]['value']) ? (int)$nodes[$nid]->field_count['und'][0]['value'] > (int)$item['quantity'] : false;
            $form['items']['list'][$nid]['available'] = array(
                '#type' => 'markup',
                '#markup' => $available ? 'Available' : 'Unavailable' . (!empty($nodes[$nid]->field_count['und'][0]['value']) ? ' (Only '.$nodes[$nid]->field_count['und'][0]['value'].' is available)' : ''),
            );
//            $form['items']['list'][$nid]['op'] = array(
//                '#type' => 'markup',
//                '#markup' => '<div><a href="" data-nid="'. $nid .'" data-oid="'. $oid .'" class="edit-row">' . t('Edit') . '</a></div>
//                              <div><a href="" data-nid="'. $nid .'" data-oid="'. $oid .'" class="remove-row">' . t('Remove') . '</a></div>',
//            );
        }
        $form['items']['cost'] = array(
            '#type' => 'markup',
            '#markup' => '
                <div class="order-sum">
                    <div><b>SUM: <span class="order-sum">'.$order['sum'].'</span></b></div>
                </div>
            ',
        );
        /*** End: Order items ***/

        // Action buttons
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#name' => 'change_status',
            '#value' => t('Change status to: '.$next_status_text)
        );
        $form['actions']['not_approved'] = array(
            '#type' => 'submit',
            '#name' => 'not_approved',
            '#value' => t('Not approved')
        );
        $form['actions']['not_delivered'] = array(
            '#type' => 'submit',
            '#name' => 'not_delivered',
            '#value' => t('Not delivered')
        );
        $form['actions']['delete'] = array(
            '#type' => 'submit',
            '#name' => 'delete',
            '#value' => t('Delete')
        );
    }

    $form['#submit'][] = '_admin_order_form_submit';

    return $form;
}

/**
 * Submit order form
 * */
function _admin_order_form_submit($form, $form_state) {
    $values = $form_state['values'];
    if(!empty($form_state['triggering_element']['#name'])) {
        $order_manager = new OrderManager();
        switch($form_state['triggering_element']['#name']) {
            case 'change_status':
                $order_manager->updateOrderStatus($values['order_id'], $values['status']);
                break;
            case 'not_approved':
                $order_manager->updateOrderStatus($values['order_id'], OrderManager::NOT_APPROVED);
                break;
            case 'not_delivered':
                $order_manager->updateOrderStatus($values['order_id'], OrderManager::NOT_DELIVERED);
                break;
            case 'delete':
                $order_manager->deleteOrder($values['order_id']);
                break;
            case 'update_order_items':
                break;
            default: break;
        }
    }
}

/**
 * Order items table theme
 * */
function theme_oat_admin_order_items_form_theme($variables) {
    $form_id = !empty($variables['element']['#id']) ? $variables['element']['#id'] : 'table-of-items';

    $element = $variables['element'];

    $header = array(
        'title' => t('Title'),
        'quantity' => t('Quantity'),
        'available' => t('Available'),
        'op' => t('Operations'),
    );

    $rows = array();

    foreach (element_children($element) as $key) {
        $rows[] = array(
            'data' => array(
                drupal_render($element[$key]['title']),
                drupal_render($element[$key]['quantity']),
                drupal_render($element[$key]['available']),
                drupal_render($element[$key]['op'])
            ),
        );
    }

    return theme('table', array(
        'header' => $header,
        'rows' => $rows,
        'attributes' => array('id' => $form_id),
    ));
}