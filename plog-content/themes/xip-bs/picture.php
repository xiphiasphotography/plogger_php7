<?php plogger_get_header(); ?>

	<main id="big-picture-container" class="container-fluid picture">
		<?php if (plogger_has_pictures()) : 
			while(plogger_has_pictures()) : 
				plogger_load_picture(); // Equivalent to the WordPress loop
				// Find thumbnail width/height
				$thumb_info = plogger_get_thumbnail_info();
				$thumb_width = $thumb_info['width']; // The width of the image. It is integer data type.
				$thumb_height = $thumb_info['height']; // The height of the image. It is an integer data type.
		?>
				<div class="row justify-content-md-between">
					<div class="col-6 order-2 col-md order-md-1">
						<div id="nav-link-img-prev"><?php echo plogger_get_prev_picture_link('<i class="fas fa-chevron-left"></i>'); ?></div>
					</div>

					<div class="col-12 order-1 col-md order-md-2">
						<h1 class="picture-title"><?php echo plogger_get_picture_caption(); ?></h1>
					</div>

					<div class="col-6 order-3 col-md">
						<div id="nav-link-img-next"><?php echo plogger_get_next_picture_link('<i class="fas fa-chevron-right"></i>'); ?></div>
					</div>
				</div>
		
				<div class="row no-gutters">
					<div class="col-12">
						<div id="picture-holder">
							<a accesskey="v" href="<?php echo plogger_get_source_picture_url(); ?>"><img class="photos-large" src="<?php echo plogger_get_picture_thumb(THUMB_LARGE); ?>" width="<?php echo $thumb_width; ?>" height="<?php echo $thumb_height; ?>" title="<?php echo plogger_get_picture_caption('clean'); ?>" alt="<?php echo plogger_get_picture_caption('clean'); ?>" /></a>
						</div><!-- /picture-holder -->
					</div>

					<div class="col-12">
						<h2 id="picture-description"><?php echo plogger_get_picture_description(); ?></h2>
						<h3 class="date"><?php echo plogger_get_picture_date(); ?></h3>
						<?php if (showEXIF()) { ?>
							<div id="exif-toggle"><?php echo plogger_get_detail_link('View image details', '<i class="fas fa-chevron-down"></i>'); ?></div>
							<div id="exif-toggle-container">
								<?php echo generate_exif_table(plogger_get_picture_id()); ?>
							</div><!-- /exif-toggle-container -->
						<?php } ?>
					</div>

					<div class="col-12">
						<?php if (plogger_get_thumbnail_nav() != '') { 
							echo plogger_get_thumbnail_nav();
						} ?>
					</div>
				</div>

				<div class="row no-gutters justify-content-center">
					<div class="col-auto align-self-center">
						<?php echo plogger_display_comments(); ?>
					</div>
				</div>
			<?php endwhile; ?>
		<?php else : ?>
			<div class="row justify-content-center">
				<div class="col">
					<div id="no-pictures-msg">
						<h2><?php echo plog_tr('Not Found') ?></h2>
						<p><?php echo plog_tr('Sorry, but the image that you requested does not exist.') ?></p>
					</div><!-- /no-pictures-msg -->
				</div>
			</div>
		<?php endif; ?>
	</main><!-- /big-picture-container -->

<?php plogger_get_footer(); ?>
