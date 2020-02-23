	<?php plogger_get_header(); ?>

	<main id="thumbnail-container" class="container-fluid">
		<div id="search" class="row gutters-10">

			<?php if (plogger_has_pictures()) : ?>

				<?php while (plogger_has_pictures()) : plogger_load_picture();
					// Set variables for the thumbnails
					$capt = plogger_get_picture_caption();
					$date = plogger_get_picture_date();
					$date_plus = $date . "<br /><br /><br />";
					$title_capt = str_ireplace("<br />", "", plogger_get_picture_description());
					$picture_id = plogger_get_picture_id();

					// Find thumbnail width/height
					$thumb_info = plogger_get_thumbnail_info();
					$thumb_width = $thumb_info['width']; // The width of the image. It is integer data type.
					$thumb_height = $thumb_info['height']; // The height of the image. It is an integer data type.
				?>
					<div class="col-6 col-sm-4 col-md-3 col-xl-2">
						<div class="thumbcontainer">
							<a href="<?= plogger_get_picture_url(); ?>" title="<?= ($fill_date == 'true' && isset($date) && $title_capt == '&nbsp;') ? $date_plus : plogger_get_picture_description(); ?>">
								<img src="<?= plogger_get_picture_thumb(); ?>" id="thumb-<?= plogger_get_picture_id(); ?>" class="photos" width="<?= $thumb_width; ?>px" height="<?= $thumb_height; ?>px" title="<?= ($fill_date == 'true' && isset($date) && $title_capt == '&nbsp;') ? $date : $title_capt; ?>" alt="<?= ($fill_date == 'true' && isset($date) && $capt == '&nbsp;') ? $date : $capt; ?>" />
							</a>
							<div class="checkbox"><?= plogger_download_checkbox($picture_id, '<label for="checkbox_'.$picture_id.'"><i class="fas fa-download"></i></label>'); ?></div>
							<div class="thumbcontent">
								<p class="collection-title"><?= $picture_caption ?></p>
								<p class="description"><?= str_ireplace($picture_caption . '<br />', '', plogger_get_picture_description()); ?></p>
							</div>
						</div><!-- /thumbcontainer -->
					</div><!-- /col -->
				<?php endwhile; ?>

			<?php else : ?>

				<div class="col-12">
					<div id="no-pictures-msg">
						<h2><?= plog_tr('Search Results') ?></h2>
						<p><?= plog_tr('Sorry, but there are no images that matched your search terms.') ?></p>
					</div><!-- /no-pictures-msg -->
				</div><!-- /col -->

			<?php endif; ?>

		</div><!-- /row -->
	</main><!-- /container-fluid -->

	<?php plogger_get_footer(); ?>