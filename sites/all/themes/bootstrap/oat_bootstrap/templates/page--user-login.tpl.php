<h1>Войти</h1>

<div>
<?php print drupal_render($form['name']); ?>
<?php print drupal_render($form['pass']); ?>
<?php print drupal_render($form['remember_me']); ?>
</div>

<?php print drupal_render($form['actions']); ?>
<?php print drupal_render($form['form_build_id']); ?>
<?php print drupal_render($form['form_id']); ?>

<a class="" title="Don't have an account ?" href="/user/register">Еще не зарегестрированны?</a>
<a class="forgotten-password" title="Forgotten password" href="/user/password">Забыли пароль?</a>
