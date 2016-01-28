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
								<img id="thumb_img" style="display:block;" src="<?php $img_dat=wp_get_attachment_image_src($img_id, "carousel-thumb"); echo $img_id[0]; ?>">
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
?>
	<div class="warp">
		<h1><?php _e("Carousel","ttr-carousel"); ?> <a href="<?php echo $add_page; ?>" class="btn-default btn-head"><?php _e("Add New","ttr-carousel"); ?></a></h1>
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

function ttr_carousel_admin_render() {
	
	ttr_db_admin_style();
	ttr_carousel_admin_style();

	if (!isset($_GET["type"])) $_GET["type"] = "list";
	switch ($_GET["type"]) {
		case "add":
			ttr_carousel_admin_form();
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
