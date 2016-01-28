<?php

/*
 * Plugin Name: Tutor Carousel
 * Description: Image Carousel
 * Version: 1.0.4
 * Author: Fedor Bobylev
 * Author URI: http://techblogogy.tk/
 * GitHub Plugin URI: https://github.com/dev-tutors-ua/ttr-carousel
 * GitHub Branch: master
 *
 */

require_once (plugin_dir_path(__FILE__)."ttr-carousel-admin.php");
require_once (plugin_dir_path(__FILE__)."ttr-carousel-admin-tbl.php");

require_once (plugin_dir_path(__FILE__)."ttr-carousel-widget.php");

register_activation_hook(__FILE__, 'TTR_carousel::setup_db');
add_action('admin_menu','TTR_carousel::setup_menu');

add_action('init','TTR_carousel::init');
add_action('widgets_init', 'TTR_carousel::setup_widgets');
add_action('plugins_loaded', 'TTR_carousel::add_txt_domain');

class TTR_carousel {
	
	public static function setup_db() {
		global $wpdb, $tbl_name;

		$table_name = $wpdb->prefix."carousel";
		$charset = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			title varchar(50) NOT NULL,
			img_id int(11) NOT NULL,
			page_link varchar(200) NOT NULL,
			position int(11) NOT NULL,
			PRIMARY KEY id (id)
		) $charset";

		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	public static function add_txt_domain() {
		load_plugin_textdomain( 'ttr-carousel', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
	}

	public static function init() {
		add_image_size('carousel-thumb', 1170, 352, True);
	}

	public static function setup_widgets() {
		register_widget('TAU_Carousel');
	}

	public static function setup_menu() {
		add_object_page(__("Carousel","ttr-carousel"), __("Carousel","ttr-carousel"), "manage_options", "ttr-carousel", "ttr_carousel_admin_render");
	}

	public static function get_items() {
		global $wpdb;
		
		$sql = "SELECT * FROM {$wpdb->prefix}carousel";
		return $wpdb->get_results($sql, ARRAY_A);
	
	}

	public static function get_item($id) {
		global $wpdb;

		$id = esc_attr($id);
		$sql = "SELECT * FROM {$wpdb->prefix}carousel WHERE id={$id}";

		return $wpdb->get_row($sql, ARRAY_A);
	}

	public static function get_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}carousel";
		return $wpdb->get_var($sql);
	}

	public static function rm_item($id) {
		global $wpdb;
		$wpdb->delete("{$wpdb->prefix}carousel", array('id' => $id), array('%d'));
	}

	public static function add_item($title, $img_id, $hplink) {
		global $wpdb;

		$wpdb->insert(
			$wpdb->prefix."carousel",
			array(
				'title' => $title,
				'img_id' => $img_id,
				'page_link' => $hplink
			),
			array(
				'%s','%d','%s'
			)
		);
	}

	public static function upt_item($id, $title, $img_id, $hplink) {
		global $wpdb;

		$wpdb->update(
			$wpdb->prefix."carousel",
			array(
				'title' => $title,
				'img_id' => $img_id,
				'page_link' => $hplink
			),
			array(
				'id' => $id
			),
			array(
				'%s','%d','%s'
			)
		);
	}
}

?>
