<!-- HEADER  -->
<?php require_once('includes/header.inc'); ?>

<!-- Show login form if user is not logged in -->
<?php if (!user_is_logged_in()) : ?>
    <?php if ($page['header']): ?>
        <div class="jumbotron text-center">
            <?php echo render($page['header']); ?>
        </div>
    <?php endif; ?>
<?php endif; ?>


<!-- User menu (FS, Matches, Rewards) -->
<?php if ($page['user-menu']): ?>
    <section  class="sub-nav">
        <nav class="container-block">
            <?php echo render($page['user-menu']); ?>
        </nav>
    </section>
<?php endif; ?>

<!-- MAIN CONTENT -->
<div id="main" role="main">
    <div class="container-block">
        <?php if (isset($head_title)): ?>
            <h1 class="page-title"><?php echo $head_title; ?></h1>
        <?php endif; ?>

        <?php if ($messages): ?>
            <?php print $messages; ?>
        <?php endif; ?>
    
        <?php echo render($page['content']); ?>
        <?php if (!user_is_logged_in()) : ?>
            <?php if ($page['login']): ?>
                <?php echo render($page['login']); ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- FOOTER -->
<?php require_once('includes/footer.inc'); ?>


