	<?php plogger_get_header(); ?>

	<main id="thumbnail-container" class="clearfix">
		<div id="search" class="row gutters-10">

			<?php if (plogger_has_pictures()) : ?>

				<?php while (plogger_has_pictures()) : plogger_load_picture();
					// Find thumbnail width/height
					$thumb_info = plogger_get_thumbnail_info();
					$thumb_width = $thumb_info['width']; // The width of the image. It is integer data type.
					$thumb_height = $thumb_info['height']; // The height of the image. It is an integer data type.
				?>
					<div class="col-6 col-sm-4 col-md-3 col-xl-2">
						<div class="thumbnail">
							<a href="<?php echo plogger_get_picture_url(); ?>"><img id="thumb-<?php echo plogger_get_picture_id(); ?>" class="photos" src="<?php echo plogger_get_picture_thumb(); ?>" width="<?php echo $thumb_width; ?>" height="<?php echo $thumb_height; ?>" title="<?php echo plogger_get_picture_caption(); ?>" alt="<?php echo plogger_get_picture_caption(); ?>" /></a>
							<div class="checkbox"><?php echo plogger_download_checkbox(plogger_get_picture_id()); ?></div>
							<p style="width: <?php echo $thumb_width; ?>px;"><?php echo plogger_get_picture_caption(); ?></p>
						</div><!-- /thumbnail -->
					</div><!-- /col -->
				<?php endwhile; ?>

			<?php else : ?>

				<div class="col-12">
					<div id="no-pictures-msg">
						<h2><?php echo plog_tr('Search Results') ?></h2>
						<p><?php echo plog_tr('Sorry, but there are no images that matched your search terms.') ?></p>
					</div><!-- /no-pictures-msg -->
				</div><!-- /col -->

			<?php endif; ?>

		</div><!-- /row -->
	</main><!-- /container-fluid -->

	<?php plogger_get_footer(); ?>