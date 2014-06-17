<!-- HEADER  -->
<?php require_once('inc/header.inc'); ?>

<div class="main-container container">

  <header role="banner" id="page-header">
    <?php if (!empty($site_slogan)): ?>
      <p class="lead"><?php print $site_slogan; ?></p>
    <?php endif; ?>
  </header> <!-- /#page-header -->

  <div class="row">

    <?php if (!empty($page['sidebar_first'])): ?>
      <aside class="col-sm-3" role="complementary">
        <?php print render($page['sidebar_first']); ?>
      </aside>  <!-- /#sidebar-first -->
    <?php endif; ?>

    <section<?php print $content_column_class; ?>>
      <?php if (!empty($page['highlighted'])): ?>
        <div class="highlighted jumbotron"><?php print render($page['highlighted']); ?></div>
      <?php endif; ?>
      <?php print $messages; ?>
      <?php if (!empty($page['help'])): ?>
        <?php print render($page['help']); ?>
      <?php endif; ?>
      <?php if (!empty($action_links)): ?>
        <ul class="action-links"><?php print render($action_links); ?></ul>
      <?php endif; ?>
        <?php if(!empty($orders)) : ?>
            <table class="user-orders-table">
                <thead>
                    <tr>
                        <th>Номер</th>
                        <th>Сумма</th>
                        <th>Статус</th>
                        <th>Создан</th>
                        <th>Описание</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $class = 'odd';
                    foreach($orders as $order) :
                ?>
                        <tr class="<?=$class?>">
                            <td><?=$order['number']?></td>
                            <td><?=$order['sum']?></td>
                            <td>
                                <?php
                                    $status = '';
                                    switch ((int)$order['status']) {
                                        case OrderManager::PENDING:
                                            $status = 'Ожидает обработки';
                                            break;
                                        case OrderManager::APPROVED:
                                            $status = 'Подтвержден';
                                            break;
                                        case OrderManager::NOT_APPROVED:
                                            $status = 'Не подтвержден';
                                            break;
                                        case OrderManager::PACKING:
                                            $status = 'Упаковывается';
                                            break;
                                        case OrderManager::DELIVERING:
                                            $status = 'Доставляется';
                                            break;
                                        case OrderManager::DELIVERED:
                                            $status = 'Доставлен';
                                            break;
                                        case OrderManager::NOT_DELIVERED:
                                            $status = 'Не доставлен';
                                            break;
                                        case OrderManager::DELETED:
                                            $status = 'Отменен';
                                            break;
                                    }
                                ?>
                                <?=$status?>
                            </td>
                            <td><?=$order['created']?></td>
                            <td>
                                <a href="" class="order-details">Посмотреть детали заказа</a>
                            </td>
                        </tr>
                        <tr class="order-details-container" style="display: none;">
                            <td colspan="5">
                                <?php
                                    $order_manager = new OrderManager();
                                    $user = user_load($order['uid']);
                                    $order_user_name = !empty($user->field_full_name['und'][0]['value']) ? $user->field_full_name['und'][0]['value'] : $user->name;
                                    $order_items = $order_manager->getOrderItems($order['id']);
                                    $order_nodes = node_load_multiple(array_keys($order_items));
                                    $address = $order_manager->getOrderAddress($order['id']);

                                    $email = $address['email'];
                                    if(empty($email)) {
                                        $email = $user->mail;
                                    }
                                ?>
                                <div class="order-info">
                                    <div><b>Номер заказа: <?=$order['number']?></b></div>
                                    <div>Статус: <b><?=$status?></b></div>
                                    <div>Создан: <?=$order['created']?></div>
                                </div>

                                <fieldset class="form-wrapper"><legend><span class="fieldset-legend">Информация о заказе</span></legend>
                                    <div class="address-info">
                                        <div>Пользователь: <?=$order_user_name?></div>
                                        <div>Phone: <?=$address['phone']?></div>
                                        <div>E-mail: <?=$email?></div>
                                        <div>Address: <?php echo 'Город '.$address['city'].', ул. '.$address['street'].', д.'.$address['house'].', к.'.$address['building'].', кв.'.$address['flat']; ?></div>
                                        <?php if(!empty($order['comment'])) : ?>
                                            <div>Comment:</div>
                                            <div><?=$order['comment']?></div>
                                        <?php endif; ?>
                                    </div>
                                </fieldset>
                                <fieldset class="form-wrapper" id="edit-items"><legend><span class="fieldset-legend">Список заказанных вещей</span></legend>
                                    <table class="order-items-table">
                                        <thead>
                                            <tr>
                                                <th>Наименование</th>
                                                <th>Стоимость</th>
                                                <th>Количество</th>
                                                <th>Итого</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($order_items as $nid => $item) : ?>
                                                <tr>
                                                    <td><?=$order_nodes[$nid]->title?></td>
                                                    <td><?=$order_nodes[$nid]->field_price['und'][0]['value']?></td>
                                                    <td><?=$item['quantity']?></td>
                                                    <td><?=$order_nodes[$nid]->field_price['und'][0]['value']*$item['quantity']?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <div class="order-sum">
                                        <b>Сумма заказа: <?=$order['sum']?></b>
                                    </div>
                                </fieldset>
                            </td>
                        </tr>
                <?php
                        $class = $class == 'odd' ? 'even' : 'odd';
                    endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <?php if (!empty($page['sidebar_second'])): ?>
      <aside class="col-sm-3" role="complementary">
        <?php print render($page['sidebar_second']); ?>
      </aside>  <!-- /#sidebar-second -->
    <?php endif; ?>

  </div>
</div>

<!-- FOOTER -->
<?php require_once('inc/footer.inc'); ?>
