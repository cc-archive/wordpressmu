
<?php $current_site = get_current_site(); ?>
<hr />
<div id="footer">
<!-- If you'd like to support WordPress, having the "powered by" link someone on your blog is the best way, it's our only promotion or advertising. -->
	<p>
		<?php bloginfo('name'); ?> is proudly powered by
		<a href="http://mu.wordpress.org/">WordPress MU</a> provided by <a href="http://<?php echo $current_site->domain . $current_site->path ?>"><?php echo $current_site->site_name ?></a>. 
		<br /><a href="<?php bloginfo('rss2_url'); ?>">Entries (RSS)</a>
		and <a href="<?php bloginfo('comments_rss2_url'); ?>">Comments (RSS)</a>.
		<!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->
	</p>
</div>
</div>

<!-- Gorgeous design by Michael Heilemann - http://binarybonsai.com/kubrick/ -->
<?php /* "Just what do you think you're doing Dave?" */ ?>

<a href="<?=licenseUri()?>">
<img src="<?php bloginfo('stylesheet_directory'); ?>/images/somerights20.png" alt="some rights reserved" /></a><br/>
Except where otherwise <a href="http://creativecommons.org/policies">noted</a>,
content on this site is<br/>
licensed under a <a rel="license" href="<?=licenseUri()?>">Creative Commons License</a>.
		<?php wp_footer(); ?>
</body>
</html>
