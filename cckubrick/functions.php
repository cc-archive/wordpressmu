<?php

if ( function_exists('register_sidebars') )
	register_sidebars(1);

if (get_option('cc_header_show_worldwide') == NULL) {
   add_option('cc_header_show_worldwide', true);
}

/* theme options page */
add_action ('admin_menu', 'cc_theme_menu');

function cc_header_image() {
  return stripslashes (get_option ('cc_header_image'));
}

function cc_show_worldwide() {
  return get_option('cc_header_show_worldwide');
}

function cc_theme_menu() {
  add_theme_page('Customize CC Kubrick', 'Customize CC Kubrick', 5, basename(__FILE__), 'cc_theme_options');
}

/* TODO: Make this a file upload instead of location reference. */
function cc_theme_options() {
  
  if ($_POST['cc_header_image']) {
    update_option ('cc_header_image', $_POST['cc_header_image']);
    $message = "Header image location updated!";
  }

  if ($_POST['cc_show_worldwide_submitted']) {

    if ($_POST['cc_show_worldwide']) {
       update_option('cc_header_show_worldwide', true);
    } else {
       update_option('cc_header_show_worldwide', false);
    }

    $message = "Toolbar display updated.";
  }


  // display feedback that something happened
  if ($message) {
    ?>
    <div class="wrap"><?= $message ?></div>
    <?php    
  }
  ?>
  
  <div class="wrap">
   <h2>Header Image Location (optional)</h2>
   <p><small>Add path, or URL, to an image to be used in place of blog title for each page. Will use default blog title if left blank.</small></p>
   <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" name="blurb" method="post" accept-charset="utf-8">
    <input type="text" name="cc_header_image" size="45" value="<?= cc_header_image() ?>" id="cc_header_image">
    <p><input type="submit" value="Update &rarr;" /></p>
   </form>
  </div>

  <div class="wrap">
   <h2>Header Toolbar</h2>
   <p>Display the header toolbar (including worldwide, publish and find links) ?</p>
   <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" name="i18n" method="post" accept-charset="utf-8">
    <input type="checkbox" name="cc_show_worldwide" size="45" id="cc_show_worldwide" <?=cc_show_worldwide()?'checked':''?> />
    <input type="hidden" name="cc_show_worldwide_submitted" value="true" />
    <p><input type="submit" value="Update &rarr;" /></p>
   </form>
  </div>

  <?php
}


?>
