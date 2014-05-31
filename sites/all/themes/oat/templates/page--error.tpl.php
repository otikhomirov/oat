<!-- HEADER  -->
<?php require_once('includes/header.inc'); ?>

<!-- Show login form if user is not logged in -->
<?php if(!user_is_logged_in()) : ?>
  <?php if ($page['header']): ?>
    <div class="jumbotron text-center">
      <?php echo render($page['header']); ?>
    </div>
  <?php endif; ?>
<?php endif; ?>


<!-- MAIN CONTENT -->
<div id="main" role="main">
  <div class="container-block error-page">
      <div class="content">
        <h1 class="page-title">Høyt over mål!</h1>
        <div class="user-content ">
          <p>Beklager, vi finner ikke siden du søkte etter. Klikk her <a href="/"> for å komme tilbake til 12. mann</a>.</p>
        </div>
      </div>
  </div>
</div>

<!-- FOOTER -->
<?php require_once('includes/footer.inc'); ?>

