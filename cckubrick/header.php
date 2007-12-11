<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title>
	<?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> 
	<?php wp_title(' '); ?>
	<?php if(wp_title(' ', false)) { echo ' &mdash; '; } ?> 
	<?php bloginfo('name'); ?>
</title>

<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<script src="http://www.creativecommons.org/includes/icommons.js" type="text/javascript"></script>

<!--[if IE]>
   <style type="text/css">
    img { behavior: url("<?php bloginfo('stylesheet_directory'); ?>/pngie.htc"); }
   </style>	
  <![endif]-->

<?php wp_head(); ?>
</head>
<body>
<div id="page">


<div id="header">
	<div id="headerimg">
	  <a href="<?php echo get_option('home'); ?>/">
	  <?php
	  $header_image = cc_header_image();
	  if (strlen($header_image) > 2) {
	  ?>
	  <img src="<?= $header_image ?>" alt="[ <? bloginfo('name'); ?> ]" class="ccheader"/></a>
	  <?php
	  } else {	  
	  ?>
		<img src="<?php bloginfo('stylesheet_directory'); ?>/images/cc-logo.png" alt="[ (cc) ]" class="cclogo"/></a>
		<?php if (!($descr = get_bloginfo('description'))) { ?>
		<h1 class="nodesc"><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
		<?php } else { ?>
		<h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
		<div class="description"><?= $descr ?></div>
		<?php } } ?>
	</div>
</div>
<? if (cc_show_worldwide()) { ?>
<div id="cctools">
  <div class="tool options">
    <select name="sortby" onchange="orderby(this)">
      <option value="">Select a jurisdiction</option>
      <script type="text/javascript" src="http://www.creativecommons.org/includes/jurisdictions.js"></script>
    </select>
  </div>
  <div class="tool">
    <a href="http://creativecommons.org/license/<?php 
    $pieces = explode('.', parse_url(get_bloginfo('url'), PHP_URL_HOST));
    $output = (strlen($pieces[0]) == 2) ? ("?jurisdiction=".$pieces[0]) : (""); 
    echo $output; 
    	?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/publish.png"/> License Your Work</a>
  </div>
  <div class="tool">  
    <a href="http://search.creativecommons.org/"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/find.png"/> Find CC Licensed Work</a>
  </div>
</div>
<? } ?>
<hr />
