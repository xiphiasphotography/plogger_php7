<?php plogger_get_header(); ?>

<div id="thumbnail-container" class="container-fluid clearfix">

	<?php if (plogger_has_pictures()) : ?>
		
		<div class="row slides">
			<?php while (plogger_has_pictures()) : plogger_load_picture();
				// Find thumbnail width/height
				$thumb_info = plogger_get_thumbnail_info();
				$thumb_width = $thumb_info['width']; // The width of the image. It is integer data type.
				$thumb_height = $thumb_info['height']; // The height of the image. It is an integer data type.
			?>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<div class="thumbnail">
						<a href="<?php echo plogger_get_picture_url(); ?>"><img id="thumb-<?php echo plogger_get_picture_id(); ?>" class="photos" src="<?php echo plogger_get_picture_thumb(); ?>" width="<?php echo $thumb_width; ?>" height="<?php echo $thumb_height; ?>" title="<?php echo plogger_get_picture_caption('clean'); ?>" alt="<?php echo plogger_get_picture_caption('clean'); ?>" /></a>
						<div class="checkbox"><?php echo plogger_download_checkbox(plogger_get_picture_id()); ?></div>
						<p style="width: <?php echo $thumb_width; ?>px;"><?php echo plogger_get_picture_caption() ?></p>
					</div><!-- /thumbnail -->
				</div><!-- /col -->
			<?php endwhile; ?>
		</div><!-- /row -->

	<?php else : ?>

		<div class="row">
			<div class="col-12">
				<div id="no-pictures-msg">
					<h2><?php echo plog_tr('No Images') ?></h2>
					<p><?php echo plog_tr('Sorry, but there are no images in this album yet.') ?></p>
				</div><!-- /no-pictures-msg -->
			</div><!-- /col -->
		</div><!-- /row -->

	<?php endif; ?>
</div><!-- /container-fluid clearfix -->

<?php plogger_get_footer(); ?>