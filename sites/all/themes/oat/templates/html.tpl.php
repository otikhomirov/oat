
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>"<?php print $rdf_namespaces; ?>> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>"<?php print $rdf_namespaces; ?>> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9 ie8" lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>"<?php print $rdf_namespaces; ?>> <![endif]-->
<!--[if IE 9]>         <html class="no-js ie9" lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>"<?php print $rdf_namespaces; ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>"<?php print $rdf_namespaces; ?>> <!--<![endif]-->
<head>
<?php echo $head; ?>
<title><?php echo $head_title; ?></title>
<?php echo $styles; ?>
<?php echo $scripts; ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

<meta name="apple-mobile-web-app-capable" content="yes" > 
<link rel="apple-touch-icon" href="<?=$theme_address?>images/152x152x_facebookicon.png" >

<!--[if lt IE 9]>
  <script src="/sites/all/themes/cmore/js/vendor/excanvas.min.js" /></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.2/html5shiv.js"></script>
  <script src="//s3.amazonaws.com/nwapi/nwmatcher/nwmatcher-1.2.5-min.js"></script>
  <script src="//html5base.googlecode.com/svn-history/r38/trunk/js/selectivizr-1.0.3b.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.1.0/respond.min.js"></script>
<![endif]-->


</head>
<body class="<?php echo $classes; ?>"<?php echo $attributes; ?>>
    <div id="fb-root"></div>
    <?php echo $page; ?>
</body>
</html>