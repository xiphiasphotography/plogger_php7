	<?php plogger_get_header(); ?>

	<main id="thumbnail-container" class="container-fluid collection">
		<div id="collection" class="row gutters-10">

			<?php if (plogger_has_albums()) : ?>

				<?php while (plogger_has_albums()) : plogger_load_album();
					// Set variables for the thumbnails
					$num_pictures = plogger_album_picture_count();
					$album_id = plogger_get_album_id();

					// Find thumbnail width/height
					$thumb_info = plogger_get_thumbnail_info();
					$thumb_width = (gettype($thumb_info) == 'array') ? $thumb_info['width'] : 0; // The width of the image. It is integer data type.
					$thumb_height = (gettype($thumb_info) == 'array') ? $thumb_info['height'] : 0; // The height of the image. It is an integer data type.

					$thumb_desc = plogger_get_album_description();
					$thumb_name = plogger_get_album_name();
				?>
					<div class="col-6 col-sm-4 col-md-3 col-xl-2">
						<div class="thumbcontainer">
							<a href="<?= plogger_get_album_url(); ?>" rel="internal" title="<?= $thumb_name; ?>" class="collection-image-link">
								<img src="<?= plogger_get_album_thumb(); ?>" class="photos" width="<?= $thumb_width; ?>" height="<?= $thumb_height; ?>" title="<?= $thumb_name; ?>" alt="<?= $thumb_name; ?>" />
							</a>
							<div class="checkbox"><?= plogger_download_checkbox($album_id, '<label for="checkbox_'.$album_id.'"><i class="fas fa-download"></i></label>'); ?></div>
							<div class="thumbcontent">
								<p class="collection-title"><?= $thumb_name; ?></p>
								<p class="description"><?= $thumb_desc; ?></p>
								<span class="meta-header">[<?= $num_pictures . ' ';
															echo ($num_pictures == 1) ? plog_tr('image') : plog_tr('images'); ?>]</span>
							</div>
						</div><!-- /thumbcontainer -->
					</div><!-- /col -->
				<?php endwhile; ?>

			<?php else : ?>

				<div class="col-12 gutters-10">
					<div id="no-pictures-msg">
						<h2><?= plog_tr('No Albums') ?></h2>
						<p><?= plog_tr('Sorry, but there are no images or albums in this collection yet.') ?></p>
					</div><!-- /no-pictures-msg -->
				</div><!-- /col -->

			<?php endif; ?>

		</div><!-- /row -->
	</main><!-- /container-fluid -->

	<?php plogger_get_footer(); ?>