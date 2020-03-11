	<?php plogger_get_header(); ?>

	<main id="thumbnail-container" class="container-fluid album">
		<div id="album" class="row gutters-10">

			<?php if (plogger_has_pictures()) : ?>

				<?php while (plogger_has_pictures()) : plogger_load_picture();
					// Set variables for the thumbnails
					$fill_date = 'true'; // When the value of "$fill_date" is set to 'true', the theme will use the picture's date as a description if no description otherwise exists
					$capt = plogger_get_picture_caption();
					$date = plogger_get_picture_date();
					$picture_id = plogger_get_picture_id();

					// Find thumbnail width/height
					$thumb_info = plogger_get_thumbnail_info();
					$thumb_width = $thumb_info['width']; // The width of the image. It is integer data type.
					$thumb_height = $thumb_info['height']; // The height of the image. It is an integer data type.

					$thumb_desc = ($fill_date == 'true' && isset($date) && $capt == '&nbsp;') ? '' : plogger_get_picture_description();
					$thumb_name = ($fill_date == 'true' && isset($date) && $capt == '&nbsp;') ? $date : $capt;
				?>
					<div class="col-6 col-sm-4 col-md-3 col-xl-2">
						<div class="thumbcontainer">
							<a href="<?= plogger_get_picture_url(); ?>" rel="internal" title="<?= $thumb_desc; ?>">
								<img src="<?= plogger_get_picture_thumb(); ?>" id="thumb-<?= $picture_id; ?>" class="photos" width="<?= $thumb_width; ?>" height="<?= $thumb_height; ?>" title="<?= $thumb_name; ?>" alt="<?= $thumb_name; ?>" />
							</a>
							<div class="checkbox"><?= plogger_download_checkbox($picture_id, '<label for="checkbox_'.$picture_id.'"><i class="fas fa-download"></i></label>'); ?></div>
							<div class="thumbcontent">
								<p class="collection-title"><?= $thumb_name ?></p>
								<p class="description"><?= str_ireplace($capt . '<br />', '', $thumb_desc); ?></p>
							</div>
						</div><!-- /thumbnailcontainer -->
					</div><!-- /col -->
				<?php endwhile; ?>

			<?php else : ?>

				<div class="col-12">
					<div id="no-pictures-msg">
						<h2><?= plog_tr('No Images') ?></h2>
						<p><?= plog_tr('Sorry, but there are no images in this album yet.') ?></p>
					</div><!-- /no-pictures-msg -->
				</div><!-- /col -->

			<?php endif; ?>

		</div><!-- /row -->
	</main><!-- /container-fluid -->

	<?php plogger_get_footer(); ?>