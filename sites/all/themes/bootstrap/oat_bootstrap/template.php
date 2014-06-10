<?php
/**
 * @file template.php
 */

/*
 * Common variables
 * */
function _oat_bootstrap_var($var_name, $new_val = NULL) {
    $vars = & drupal_static(__FUNCTION__, array());
    // If a new value has been passed
    if ($new_val) {
        $vars[$var_name] = $new_val;
    }
    return isset($vars[$var_name]) ? $vars[$var_name] : NULL;
}

/*
 * Hook_css_alter
 * Remove redundant css from front-end
 * */
function oat_bootstrap_css_alter(&$css) {
    $remove_css = array('modules/system/system.menus.css',
        'modules/system/system.theme.css',
        'modules/comment/comment.css',
        'sites/all/modules/contrib/date/date_api/date.css',
        'sites/all/modules/contrib/date/date_popup/themes/datepicker.1.7.css',
        'modules/field/theme/field.css',
        'modules/node/node.css',
        'modules/search/search.css',
        'modules/user/user.css',
        'sites/all/modules/contrib/views/css/views.css',
        'sites/all/modules/contrib/ckeditor/ckeditor.css',
        'sites/all/modules/contrib/ctools/css/ctools.css',
        'sites/all/modules/contrib/admin_menu/admin_menu.css',
        'sites/all/modules/contrib/admin_menu/admin_menu.uid1.css',
        'sites/all/modules/contrib/admin_menu/admin_menu_toolbar/admin_menu_toolbar.css',
        'modules/shortcut/shortcut.css');

    foreach ($remove_css as $rem) {
        if (isset($css[$rem])) {
            unset($css[$rem]);
        }
    }
}

/*
 * Hook_js_alter
 * Remove redundant js from front-end
 * */
function oat_bootstrap_js_alter(&$javascript) {
    foreach ($javascript as $key => $script) {
        if (strpos($key, '/admin_menu/') !== false) {
            unset($javascript[$key]);
        }
    }
}

/*
 * Theme basis
 * */
function oat_bootstrap_theme() {
    $items = array();
    $items['user_login'] = array(
        'render element' => 'form',
        'path' => drupal_get_path('theme', 'oat_bootstrap') . '/templates',
        'template' => 'page--user-login',
    );

    $items['user_register_form'] = array(
        'render element' => 'form',
        'path' => drupal_get_path('theme', 'oat_bootstrap') . '/templates',
        'template' => 'page--user-register',
    );

    $items['user_pass'] = array(
        'render element' => 'form',
        'path' => drupal_get_path('theme', 'oat_bootstrap') . '/templates',
        'template' => 'page--user-forgot',
    );
    return $items;
}

/**
 * Hook_preprocess_page
 * */
function oat_bootstrap_preprocess_page(&$variables) {
    if (function_exists("http_response_code")) {
        $response_code = http_response_code();
        /* Check page status */
        switch ($response_code) {
            case '403':
            case '404':
            case '500':
            case '503':
                $variables['theme_hook_suggestions'][] = 'page__error';
                break;
            default: break;
        }
    } else {
        $status = drupal_get_http_header("status");
        if (!empty($status) && ($status == "404 Not Found" || $status == "403 Forbidden")) {
            $variables['theme_hook_suggestions'][] = 'page__error';
        }
    }

    $address = base_path() . path_to_theme() . '/';
    global $base_root;
    $variables['theme_address'] = _oat_bootstrap_var('theme_address', $base_root . $address);
}

/*
 * Hook_preprocess_node
 * */
function oat_bootstrap_preprocess_node(&$variables) {
    $variables['theme_address'] = _oat_bootstrap_var('theme_address');

    $node = $variables['node'];
    switch ($node->type) {
        case 'page':
            $form_type = null;
            /* Check if cart page */
            if ($node->nid == CART_PAGE_ID) {
                $_form = drupal_get_form('oat_commerce_cart_form');
                $variables['oat_commerce_cart_form'] = drupal_render($_form);
                $variables['messages'] = theme('status_messages');
                $form_type = CART_PAGE_ID;
            }

            /* Check if address page */
            if ($node->nid == ADDRESS_PAGE_ID) {
                $_form = drupal_get_form('oat_commerce_address_form');
                $variables['oat_commerce_address_form'] = drupal_render($_form);
                $variables['messages'] = theme('status_messages');
                $form_type = ADDRESS_PAGE_ID;
            }

            /* Check if select address page */
            if ($node->nid == SELECT_ADDRESS_PAGE_ID) {
                $_form = drupal_get_form('oat_commerce_select_address_form');
                $variables['oat_commerce_select_address_form'] = drupal_render($_form);
                $variables['messages'] = theme('status_messages');
                $form_type = SELECT_ADDRESS_PAGE_ID;
            }
            $variables['form_type'] = $form_type;
            break;

        default:
            break;
    }
}

/**
 * Hook_preprocess_user_profile
 */
function oat_bootstrap_preprocess_user_profile(&$variables) {
    $variables['theme_address'] = _oat_bootstrap_var('theme_address');
}