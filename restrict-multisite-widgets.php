<?php
/*
Plugin Name: Restrict Multisite Widgets
Description: Replicates some of the per-site theme toggling functionality for widgets. Affects all sites activated on.
Version: 1.1.4
Author: Adam Harley
Author URI: http://adamharley.co.uk
Plugin URI: http://adamharley.co.uk/wordpress-plugins/restrict-multisite-widgets/
*/


if ( !class_exists( 'RestrictAllowedWidgets' ) ) {
class RestrictAllowedWidgets {

var $single_widgets = array();

function RestrictAllowedWidgets() {
	add_action( 'widgets_init', array(&$this,'filter'), 99 );
	add_action( 'wp_loaded', array(&$this,'filter_single') );
	add_action( 'wp_register_sidebar_widget', array(&$this,'register') );
	add_action( 'wp_unregister_sidebar_widget', array(&$this,'unregister') );

	if ( ! function_exists( 'is_plugin_active_for_network' ) )
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

	if ( function_exists('is_network_admin') && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) )
		add_action( 'network_admin_menu', array(&$this,'setup_admin') );
	else
		add_action( 'admin_menu', array(&$this,'setup_admin') );
}


function filter() {
	if ( current_user_can( 'manage_network_themes' ) ) // Disable restrictions for network admins
		return;

	$allowed_widgets = get_site_option( 'allowedwidgets' );

	if ( !is_array( $allowed_widgets ) )
		return $widgets;

	global $wp_widget_factory;

	foreach( array_keys( $wp_widget_factory->widgets ) as $widget_key ) { // Multi widgets
		if ( !isset( $allowed_widgets[ $widget_key ] ) )
			unregister_widget( $widget_key );
	}
}


function filter_single() {
	if ( current_user_can( 'manage_network_themes' ) ) // Disable restrictions for network admins
		return;

	$allowed_widgets = get_site_option( 'allowedwidgets' );

	if ( !is_array( $allowed_widgets ) )
		return $widgets;

	foreach( array_keys( $this->single_widgets ) as $widget_key ) { // Single widgets
		if ( !isset( $allowed_widgets[ $widget_key ] ) )
			wp_unregister_sidebar_widget( $widget_key );
	}
}


function register( $widget ) { // Register single instance widgets internally since they don't use WP_Widget_Factory
	if ( isset( $widget['params'][0] ) && ! is_array( $widget['params'][0] ) ) {
		$this->single_widgets[ $widget['id'] ] = $widget['name'];
	}
	return $widget;
}


function unregister( $widget_id ) { // Unregister single instance widget
	unset( $this->single_widgets[ $widget_id ] );
}


function setup_admin() {
	if ( function_exists('is_network_admin') )
		$page = add_theme_page( __( 'Restricted Widgets', 'restrict-multisite-widgets' ), __( 'Widget Restrictions', 'restrict-multisite-widgets' ), 'manage_network_themes', 'ms-widgets', array(&$this,'admin') );
	else
		$page = add_submenu_page( 'ms-admin.php', __( 'Restricted Widgets', 'restrict-multisite-widgets' ), __('Widgets', 'restrict-multisite-widgets' ), 'manage_network_themes', 'ms-widgets', array(&$this,'admin') );

	add_contextual_help( $page,
		'<p>' . __( 'This screen enables and disables the inclusion of widgets available to choose in the Widgets menu for each site.', 'restrict-multisite-widgets' ) . '</p>' .
		'<p>' . __( 'If the network admin disables a widget that is in use, it will be removed from any sites using it.', 'restrict-multisite-widgets' ) . '</p>'
	);
}


function admin() {
	global $wp_widget_factory;

	if ( isset( $_POST['widget'] ) ) {
		$widget_states = array();
		foreach( (array)$_POST['widget'] as $widget => $widget_state ) {
			if ( $widget_state == 'enabled' )
				$widget_states[ $widget ] = 1;
			else
				unset( $widget_states[ $widget ] );
		}
		$updated = update_site_option( 'allowedwidgets', $widget_states );
	}

	if ( isset($updated) && $updated ) {
		?>
		<div id="message" class="updated"><p><?php _e( 'Site widgets saved.', 'restrict-multisite-widgets' ) ?></p></div>
		<?php
	}

	$allowed_widgets = get_site_option( 'allowedwidgets' );
?>
<div class="wrap">
	<form method="post">
		<?php screen_icon('themes') ?>
		<h2><?php _e( 'Restricted Widgets', 'restrict-multisite-widgets' ) ?></h2>
		<p><?php _e( 'Widgets must be enabled for your network before they will be available to individual sites.', 'restrict-multisite-widgets' ) ?></p>
		<p class="submit">
			<input type="submit" value="<?php _e( 'Apply Changes', 'restrict-multisite-widgets' ) ?>" /></p>
		<table class="widefat">
			<thead>
				<tr>
					<th style="width:15%;"><?php _e( 'Enable', 'restrict-multisite-widgets' ) ?></th>
					<th style="width:25%;"><?php _e( 'Widget', 'restrict-multisite-widgets' ) ?></th>
					<th style="width:60%;"><?php _e( 'Description', 'restrict-multisite-widgets' ) ?></th>
				</tr>
			</thead>
			<tbody id="widgets">
			<?php
			$total_widget_count = $allowed_widgets_count = 0;
			$class = '';
			foreach ( array_merge( (array)$wp_widget_factory->widgets, $this->single_widgets ) as $key => $widget ) {
				$total_widget_count++;
				$widget_key = esc_html( $key );
				$class = ( 'alt' == $class ) ? '' : 'alt';
				$class1 = $enabled = $disabled = '';
				$enabled = $disabled = false;

				if ( isset( $allowed_widgets[$widget_key] ) == true ) {
					$enabled = true;
					$allowed_widgets_count++;
					$class1 = 'active';
				}
				else
					$disabled = true;
				?>
				<tr valign="top" class="<?php echo $class . ' ' . $class1; ?>">
					<td>
						<label><input name="widget[<?php echo $widget_key ?>]" type="radio" id="enabled_<?php echo $widget_key ?>" value="enabled" <?php checked( $enabled ) ?> /> <?php _e( 'Yes', 'restrict-multisite-widgets' ) ?></label>
						&nbsp;&nbsp;&nbsp;
						<label><input name="widget[<?php echo $widget_key ?>]" type="radio" id="disabled_<?php echo $widget_key ?>" value="disabled" <?php checked( $disabled ) ?> /> <?php _e( 'No', 'restrict-multisite-widgets' ) ?></label>
					</td>
					<th scope="row" style="text-align:left;"><?php echo ( is_object( $widget ) ) ? esc_html( strip_tags( $widget->name ) ) : $widget ?></th>
					<td><?php if ( isset( $widget->widget_options['description'] ) ) echo esc_html( $widget->widget_options['description'] ) ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" value="<?php _e( 'Apply Changes', 'restrict-multisite-widgets' ) ?>" />
		</p>
	</form>

	<h3><?php _e( 'Total', 'restrict-multisite-widgets' ) ?></h3>
	<p>
		<?php printf( __( 'Widgets Installed: %d', 'restrict-multisite-widgets' ), $total_widget_count ) ?>
		<br />
		<?php printf( __( 'Widgets Enabled: %d', 'restrict-multisite-widgets' ), $allowed_widgets_count ) ?>
	</p>
</div>
<?php
}

}
}


if ( is_admin() )
	new RestrictAllowedWidgets;