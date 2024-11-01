<?php
/**
 * @package Wp-Grooveshark
 * @author Ezequiel Prieto
 * @version 0.26
 */
/*
Plugin Name: Wp-Grooveshark
Plugin URI: http://www.ezequielprieto.com.ar/wp-grooveshark.html
Description: This plugin allows you to add a widget to your sidebar with your recent listened tracks and your favorite tracks on Grooveshark
Author: Ezequiel Prieto
Version: 0.26
Author URI: http://www.ezequielprieto.com.ar
*/


include_once(ABSPATH . WPINC . '/rss.php');

function activate(){
    add_option('recent_title' , 'Recent Listened on Grooveshark');
    add_option('favorites_title' , 'Favorites on Grooveshark');
    add_option('favorites_count' , 5);
    add_option('recent_count' , 5);
  }

function control_recent(){
  $recent_title = get_option('recent_title');
  $recent_count = get_option('recent_count');
  ?>
  <p><label>Title<input name="widget_name_option1"
type="text" value="<?php echo $recent_title; ?>" /></label></p>
  <p><label>Item count<input name="widget_name_option2"
type="text" value="<?php echo $recent_count; ?>" /></label></p>
  <?php
   if (isset($_POST['widget_name_option1'])){
    update_option('recent_title', attribute_escape($_POST['widget_name_option1']));
    update_option('recent_count', attribute_escape($_POST['widget_name_option2']));
  }
}

function grooveshark_recent() {
	$rss = fetch_rss('http://api.grooveshark.com/feeds/1.0/users/'.get_option('grooveshark_username').'/recent_listens.rss');
	$maxitems = get_option('recent_count');
	$items = array_slice($rss->items, 0, $maxitems);

	echo "<br /><h2>".get_option('recent_title')."</h2>";
	echo "<ul>";
	   foreach ( $items as $item ) :
		echo "<li><img src='".WP_PLUGIN_URL."/wp-grooveshark/grooveshark_icon.png'><a target='_blank' href='".$item['link']."' title='".$item['title']."'>".$item['title']."</a></li>";
	endforeach;
	echo "</ul>";
}

function control_favorites(){
  $recent_title = get_option('favorites_title');
  $recent_count = get_option('favorites_count');
  ?>
  <p><label>Title<input name="widget_name_option3"
type="text" value="<?php echo $recent_title; ?>" /></label></p>
  <p><label>Item count<input name="widget_name_option4"
type="text" value="<?php echo $recent_count; ?>" /></label></p>
  <?php
   if (isset($_POST['widget_name_option3'])){
    update_option('favorites_title', attribute_escape($_POST['widget_name_option3']));
    update_option('favorites_count', attribute_escape($_POST['widget_name_option4']));
  }
}

function grooveshark_favorites() {
    	$rss = fetch_rss('http://api.grooveshark.com/feeds/1.0/users/'.get_option('grooveshark_username').'/recent_favorite_songs.rss');
	$maxitems = get_option('favorites_count');
	$items = array_slice($rss->items, 0, $maxitems);

	echo "<br /><h2>".get_option('favorites_title')."</h2>";
	echo "<ul>";
	   foreach ( $items as $item ) :
		echo "<li><img src='".WP_PLUGIN_URL."/wp-grooveshark/grooveshark_icon.png'><a target='_blank' href='".$item['link']."' title='".$item['title']."'>".$item['title']."</a></li>";
	endforeach;
	echo "</ul>";
}


function init_wp_grooveshark()
{
	register_sidebar_widget("Wp-Grooveshark Recent Listened", "grooveshark_recent");
	register_sidebar_widget("Wp-Grooveshark Favorites", "grooveshark_favorites");
	register_widget_control('Wp-Grooveshark Recent Listened', 'control_recent');
	register_widget_control('Wp-Grooveshark Favorites', 'control_favorites');
}


function wpgrooveshark_create_menu() {

	//create new top-level menu
	add_options_page('WP-Grooveshark Plugin Settings', 'WP-Grooveshark Settings', 'administrator', __FILE__, 'wpgrooveshark_settings_page',plugins_url('/images/icon.png', __FILE__));

	//call register settings function
	add_action( 'admin_init', 'register_mysettings' );
}


function register_mysettings() {
	//register our settings
	register_setting( 'wpgrooveshark-settings-group', 'username' );
}

function wpgrooveshark_settings_page() {

    // variables for the field and option names 
    $opt_name = 'grooveshark_username';
    $hidden_field_name = 'mt_submit_hidden';
    $data_field_name = 'username';

    // Read in existing option value from database
    $opt_val = get_option( $opt_name );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val = $_POST[ $data_field_name ];

        // Save the posted value in the database
        update_option( $opt_name, $opt_val );

        // Put an options updated message on the screen

?>
<div class="updated"><p><strong><?php _e('Options saved.'); ?></strong></p></div>
<?php

    }

    // Now display the options editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>WP-Grooveshark Settings Page</h2>";

    // options form
    
    ?>
<p>This is an easy to use plugin, we only need your <a href="http://www.grooveshark.com/" target="_blank">Grooveshark</a> username, when you save it here, go to <strong>'Appearence -> Widgets'</strong> and add the widget to your sidebar. There you can customize the title of the widget and the number of items of your recent listened/favorite list to show.</p>

<p>Thanks for using my plugin, <a href="http://www.ezequielprieto.com.ar/" target="_blank">Ezequiel Prieto</a></p>
<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p>Grooveshark Username 
<input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="20">
</p><hr />

<p class="submit">
<input type="submit" name="Submit" value="Update data" />
</p>

</form>
</div>

<?php
}


add_action("plugins_loaded", "init_wp_grooveshark");
add_action('admin_menu', 'wpgrooveshark_create_menu');

/* 		The human charity and misery are unlimited... 		*/
?>
