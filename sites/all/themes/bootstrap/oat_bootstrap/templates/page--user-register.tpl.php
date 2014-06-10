<h1 class="page-header">Регистрация</h1>

<div>
<?php print drupal_render($form['field_full_name']); ?>
<?php print drupal_render($form['account']['mail']); ?>
<?php print drupal_render($form['account']['pass']); ?>
</div>

<?php print drupal_render($form['actions']); ?>
<?php print drupal_render($form['form_build_id']); ?>
<?php print drupal_render($form['form_id']); ?>

<a class="" title="Already have an account ?" href="/login">Уже есть аккаунт?</a>
    