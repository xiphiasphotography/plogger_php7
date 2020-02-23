	<?php plogger_get_header(); ?>

	<main id="thumbnail-container" class="container-fluid">
		<div id="collections" class="row gutters-10">

			<?php if (plogger_has_collections()) : ?>

				<?php while (plogger_has_collections()) : plogger_load_collection();
					// Find thumbnail width/height
					$thumb_info = plogger_get_thumbnail_info();
					$thumb_width = $thumb_info['width']; // The width of the image. It is integer data type.
					$thumb_height = $thumb_info['height']; // The height of the image. It is an integer data type.
					$thumb_desc = plogger_get_collection_description();
					$thumb_name = plogger_get_collection_name();
					$num_albums = plogger_collection_album_count();
				?>
					<div class="col-6 col-sm-4 col-md-3 col-xl-2">
						<div class="thumbcontainer">
							<a href="<?= plogger_get_collection_url(); ?>" rel="internal" title="<?= $thumb_name; ?>" class="collection-image-link">
								<img src="<?= plogger_get_collection_thumb(); ?>" class="photos" width="<?= $thumb_width; ?>" height="<?= $thumb_height; ?>" title="<?= $thumb_name; ?>" alt="<?= $thumb_name; ?>" />
							</a>
							<div class="checkbox"><?= plogger_download_checkbox(plogger_get_collection_id()); ?></div>
							<div class="thumbcontent">
								<p class="collection-title"><?= $thumb_name; ?></p>
								<p class="description"><?= $thumb_desc; ?></p>
								<span class="meta-header">[<?= $num_albums . ' ';
															echo ($num_albums == 1) ? plog_tr('album') : plog_tr('albums'); ?>]</span>
							</div>
						</div><!-- /thumbcontainer -->
					</div><!-- /col -->
				<?php endwhile; ?>

			<?php else : ?>

				<div class="col-12">
					<div id="no-pictures-msg">
						<h2><?= plog_tr('No Images') ?></h2>
						<p><?= plog_tr('Sorry, but there are no images in this gallery yet.') ?></p>
					</div><!-- /no-pictures-msg -->
				</div><!-- /col -->

			<?php endif; ?>

		</div><!-- /row -->
	</main><!-- /container-fluid -->

	<?php plogger_get_footer(); ?>