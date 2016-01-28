<?php 

// =========== CUSTOM CAROUSEL WIDGET ===========
class TAU_Carousel extends WP_Widget {

	function __construct() {
		parent::__construct(
			'carousel_widget', // Base ID
			__( 'Carousel', 'ttr-carousel' ), // Name
			array( 'description' => __( 'Bootstrap carousel. Displays photos of size (1170 x 352), from image category', 'ttr-carousel' ), ) // Args
		);
	}

	// Widget Front-End
	public function widget( $args, $instance ) {

		$imgs = TTR_carousel::get_items();
		$imgs_len = TTR_carousel::get_count();

		?>
			<!-- Top Carousel -->
			<div id="carousel-ctr" class="container-fluid">
				<div id="carousel-main" class="carousel slide" data-ride="carousel">
					<!-- Indicators -->
					<ol class="carousel-indicators">
						<?php 
							for ($i = 0; $i < $imgs_len ; $i++ ) {
								if ($i == 0) {
									echo '<li data-target="#carousel-main" data-slide-to="0" class="active"></li>';
								} else {
									echo '<li data-target="#carousel-main" data-slide-to="'.$i.'"></li>'; 
								}
							}
						?>
					</ol>

					<!-- Wrapper for slides -->
					<div class="carousel-inner" role="listbox">
						<?php 
							$img_class = 'item active';
							foreach ($imgs as $i) {
								echo "<div class=\"{$img_class}\"> <a href=\"{$i['page_link']}\"> ";
									echo "<span class='carousel-title'>".stripslashes($i['title'])."</span> ";
									echo wp_get_attachment_image( $i['img_id'], "carousel-thumb" );
								echo "</a> </div>";

								$img_class = 'item';
							}
						?>
					</div>
				</div>
			</div>
		<?php
	}

	public function form( $instance ) {
	}
	public function update( $new_instance, $old_instance ) {
	}
}
