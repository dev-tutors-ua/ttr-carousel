<?php

function ttr_carousel_admin_style () {
?>
	<style>
		table img {
			width: 100%;
		}
		.form-edit img {
			display: none;

			width: 50%;

			margin-top: 5px;
		}
		.warp {
			margin-right: 20px;
		}
		.form-edit label {
			display: inline-block;

			margin-top: 8px;
			width: 100%;
			height: 100%;
			
			text-align: right;
			vertical-align: text-top;
			font-weight: bold;
		}
		.form-edit input, .form-edit select, .form-edit textarea {
			width: 400px;
		}
		.form-edit textarea {
			height: 200px;
			text-align: left;
		}
		.btn-default {
			background-color: #e0e0e0;

			margin-top: 10px;
			padding: 8px;

			border-radius: 5px;
			border: none;

			color: #2a73aa;
			font-size: 12pt;
			font-weight: bold;
		}
		.btn-default:hover {
			background-color: #2a73aa;
			color: #fff;

			cursor: pointer;
		}
		.btn-head {
			font-size: 10pt;
			text-decoration: none;
		}

		#c-items {
			width: 400px
		}
		#c-items > li {
			background-color: #fff;
			border: 1px solid #eee;
			padding: 10px;
		}
		#c-items > li:hover {
			cursor: move;
		}
		#c-items > li > img{
			width: 100%;
		}
	</style>
<?php
}

// WORDPRESS Media Browser
function add_media_brw() {
	wp_enqueue_media();
}
add_action('admin_enqueue_scripts','add_media_brw');

function ttr_carousel_admin_form($id=-1,$title="",$img_id=-1,$hplink="") {
	$lst_page = get_site_url()."/wp-admin/admin.php?page=ttr-carousel";
?>	
	<div class="warp">
		<h1><?php _e("Item Editor","ttr-carousel"); ?></h1>
		<div id="tutor-form">
			<form method="post" action="<?php echo $lst_page; ?>">
				
				<table class="form-edit">
					<tr>
						<td><label for="title"><?php _e("Title","ttr-carousel") ?>:</label></td>
						<td><input type="text" name="title" id="title" value="<?php echo $title; ?>"></td>
					</tr>
					<tr>
						<td><label for="img_id"><?php _e("Image","ttr-carousel") ?> (1170x352 px):</label></td>
						<input type="hidden" name="img_id" id="img_id" value="<?php echo $img_id; ?>">

						<td>
							<button id="add_media" class="btn-default" type="button"><?php _e("Media","ttr-carousel"); ?></button>
							<br>
							<?php if ($img_id == -1) { ?>
								<img id="thumb_img" src="">
							<?php } else { ?>
								<img id="thumb_img" style="display:block;" src="<?php $img_dat=wp_get_attachment_image_src($img_id, "carousel-thumb"); echo $img_dat[0]; ?>">
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td><label for="hplink"><?php _e("Hyperlink","ttr-carousel"); ?>:</label></td>
						<td><input type="text" name="hplink" id="hplink" value="<?php echo $hplink; ?>"></td>
					</tr>
				</table>
			
				<!-- WORDPRESS MEDIA Browser-->

				<script>
					jQuery(document).ready(function ($) {
						var frame;

						$("#add_media").on("click", function () {
							if (frame) {
								frame.open();
								return;
							}

							frame = wp.media({
								title: '<?php _e("Upload or Select Image","ttr-carousel"); ?>',
								button: {
									text: '<?php _e("Select","ttr-carousel"); ?>'
								},
								multiple: false
							});

							frame.on('select', function () {
								var img_dat = frame.state().get('selection').first().toJSON();
								console.log(img_dat);

								$("#img_id").val(img_dat.id);
								$("#thumb_img").attr("src", img_dat.url);
								$("#thumb_img").show();
							});

							frame.open();
						});
					});
				</script>
				
				<!-- ./WORDPRESS MEDIA TEST -->

				<?php if ($id == -1) { ?>
					<?php wp_nonce_field('add-carousel-item') ?>
					<button id="add_tutor" class="btn-default" name="action" value="add" type="submit"><?php _e("Add","ttr-carousel"); ?></button>
				<?php } else { ?>
					<?php wp_nonce_field('edit-carousel-item_'.$id) ?>
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<button id="add_tutor" class="btn-default" name="action" value="upt" type="submit"><?php _e("Updated","ttr-carousel"); ?></button>
				<?php } ?>
			</form>
		</div>
	</div>
<?php
}

function ttr_carousel_admin_table() {
	$crs_tbl = new ttr_carousel_tbl();
	$add_page = get_site_url()."/wp-admin/admin.php?page=ttr-carousel&type=add";
	$pos_page = get_site_url()."/wp-admin/admin.php?page=ttr-carousel&type=pos";
?>
	<div class="warp">
		<h1><?php _e("Carousel","ttr-carousel"); ?></h1>
		<h4>
			<a href="<?php echo $add_page; ?>"><?php _e("Add New","ttr-carousel"); ?></a> | 
			<a href="<?php echo $pos_page; ?>"><?php _e("Arrange","ttr-carousel"); ?></a>
		</h4>
		<div class="metabox-sortables">
			<form method="post">
				<?php 
					$crs_tbl->prepare_items();
					$crs_tbl->display();
				?>
			</form>
		</div>
	</div>
<?php
}

function ttr_carousel_admin_position_editor() {

	if (isset($_POST['action']) && $_POST['action'] == "upt") {
		TTR_carousel::arrange_items($_POST['items']);
	}
	
	$items = TTR_carousel::get_items();
?>
	<div class="warp">
		<h1><?php _e("Arrange Elements","ttr-carousel"); ?></h1>

		<div class="item-editor">
			<form method="post">
				<button class="btn-default" name="action" value="upt" type="submit"><?php _e("Update","ttr-db"); ?></button>
				<ul id="c-items">
					<?php foreach($items as $i) { $url = wp_get_attachment_image_src($i['img_id'], "carousel-thumb"); ?>
						<li>
							<img src="<?php echo $url[0]; ?>">
							<input type="hidden" name="items[]" value="<?php echo $i['id'] ?>">
						</li>
					<?php } ?>
				</ul>
				<button class="btn-default" name="action" value="upt" type="submit"><?php _e("Update","ttr-db"); ?></button>
			</form>
		</div>
	</div>
	
	<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script>
		jQuery(document).ready(function ($) {
			$("#c-items").sortable();
			$("#c-items").disableSelection();
		});
	</script>
<?php
}

function ttr_carousel_admin_render() {
	
	ttr_carousel_admin_style();

	if (!isset($_GET["type"])) $_GET["type"] = "list";
	switch ($_GET["type"]) {
		case "add":
			ttr_carousel_admin_form();
			break;
		case "pos":
			ttr_carousel_admin_position_editor();
			break;
		case "edit":
			$itm = TTR_carousel::get_item($_GET['id']);
			ttr_carousel_admin_form($itm['id'], stripslashes($itm['title']), $itm['img_id'], $itm['page_link']);
			break;
		default: 
			ttr_carousel_admin_table();
			break;
	}
}

?>
