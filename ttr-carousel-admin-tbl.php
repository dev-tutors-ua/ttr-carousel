<?php
	// Add WP_List_Table
	if (!class_exists('WP_List_Table')) {
		require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
	}

	// Admin Tutor Table
	class ttr_carousel_tbl extends WP_List_Table {

		public function __construct() {
			
			parent::__construct(array(
				'singular' => __("Tutor", "sp"),
				'plural' => __("Tutors", "sp"),
				'ajax' => false
			));
		}

		/* DISPLAY METHODS */

		// Default Column Display Method
		public function column_default($item, $column_name) {
			return stripslashes($item[$column_name]);
		}

		public function column_title($item) {
			$title = stripslashes($item['title']);

			// Remove Item
			$url_del = sprintf("?page=%s&action=%s&id=%s", esc_attr($_REQUEST['page']), 'del-itm', $item['id']);
			$url_del = wp_nonce_url($url_del, 'del-item_'.$item['id']);

			$actions = [
				"edit" => sprintf("<a href=\"?page=%s&id=%s&type=%s\">".__("Edit","ttr-carousel")."</a>",$_REQUEST['page'], $item['id'], "edit"),
				"delete" => "<a href=\"{$url_del}\">".__("Delete","ttr-carousel")."</a>"
			];

			return $title.$this->row_actions($actions);
		}

		// Display Image Column
		public function column_img_id($item) {
			$url = wp_get_attachment_image_src($item['img_id'], "carousel-thumb")[0];
			return "<img src=\"{$url}\">";
		}

		// Display Link Column
		public function column_page_link($item) {
			return "<a href=\"{$item['page_link']}\" target=\"_blank\">{$item['page_link']}</a>";
		}

		// Checkbox Column Method
		public function column_cb($item) {
			return sprintf("<input type=\"checkbox\" name=\"bulk-sel[]\" value=\"%s\">", $item['id']);
		}

		// @returns column slugs and titles
		function get_columns() {
			$columns = [
				'cb' => "<input type=\"checkbox\" >",
				'title' => __("Title", "ttr-carousel"),
				'img_id' => __("Image", "ttr-carousel"),
				'page_link' => __("Hyperlink", "ttr-carousel")
			];
			return $columns;
		}

		// @returns row actions array
		public function get_bulk_actions() {
			$act = [
				'bulk-delete' => __('Delete',"ttr-carousel")
			];
			return $act;
		}

		// Prosses Requests
		public function process_bulk_action () {
			$post_action = $this->current_action();

			switch ($post_action) {
				case "add":
					$nonce = esc_attr($_REQUEST['_wpnonce']);
					if (!wp_verify_nonce($nonce, "add-carousel-item")) {
						die("Failed Security Check");
					} else {
						//TODO: Add Item
						TTR_carousel::add_item($_POST['title'], $_POST['img_id'], $_POST['hplink']);
					}
					break;
				case "upt":
					$nonce = esc_attr($_REQUEST['_wpnonce']);
					if (!wp_verify_nonce($nonce, "edit-carousel-item_".$_POST['id'])) {
						die("Invalid Info");
					} else {
						TTR_carousel::upt_item($_POST['id'], $_POST['title'], $_POST['img_id'], $_POST['hplink']);
					}
					break;
				case "del-itm":
					$nonce = esc_attr($_REQUEST['_wpnonce']);
					if (!wp_verify_nonce($nonce, 'del-item_'.$_GET['id'])) {
						die("Failed Security Check");
					} else {
						TTR_carousel::rm_item($_GET['id']);
					}
					break;
				case "bulk-delete":
					$del_ids = esc_sql($_POST['bulk-sel']);
					foreach ($del_ids as $i) {
						//TODO: Remove Item
						TTR_carousel::rm_item($i);
					}

					break;
			}
		}

		public function prepare_items() {
			/* Generate Headers */
			$this->_column_headers = array(
				$this->get_columns(), // (Array) Column Slugs and Titles
				[], // (Array) Hidden Fields
				[], // (Array) Sortable Columns
				'title' // (String) Slug of column which displays actions (edit, view, etc.)
			);

			// Write Bulk Action Prossesing
			$this->process_bulk_action();
			
			// Sets Pagination Data
			//$per_page = 10; //TODO make changable
			//$cur_page = $this->get_pagenum();
			
			//$this->set_pagination_args([
				//'total_items' => TTR_carousel::get_count() //TODO Add Item Count
				//'per_page' => $per_page
			//]);

			// Sets Tutors For Database
			//$this->items = TTR_carousel::get_items($per_page, $cur_page);
			$this->items = TTR_carousel::get_items();
		}
	}
	
?>
