	<footer id="footer" class="container-fluid">

		<div class="row gutters-10">
			<div class="col-12 order-1 col-md-6 col-lg-12 order-lg-1">
				<?php if (plogger_pagination_control() != '') { ?>
					<div id="pagination">
						<?php echo plogger_pagination_control(5); ?>
					</div><!-- /pagination -->
				<?php } ?>
			</div>

			<div class="col-12 order-2 col-md-6 col-lg-4 order-lg-4">
				<?php if (plogger_sort_control() != '') { ?>
					<div id="sort-control">
						<?php echo plogger_sort_control(); ?>
					</div><!-- /sort-control -->
				<?php } ?>
			</div>

			<div class="col-12 order-3 col-md-6 col-lg-4 order-lg-2">
				<?php if (generate_jump_menu() != '') { ?>
					<div id="navigation-container">
						<?php echo generate_jump_menu(); ?>
					</div><!-- /navigation-container -->
				<?php } ?>
			</div>

			<div class="col-12 order-4 col-md-6 col-lg-4 order-lg-3">
				<div id="search-container">
					<?php echo generate_search_box(); ?>
				</div><!-- /search-container -->
			</div>

			<div class="col-12 order-5 col-md-6 col-lg-4">
				<?php if (plogger_download_selected_button() != '') { ?>
					<div id="download-selected"><?php echo plogger_download_selected_button(); ?></div><!-- /download-selected -->
				<?php } ?>
			</div>
		</div>

		<div class="row gutters-10">
			<div class="col-10">
				<span class="link-back"><?php echo plogger_link_back(); ?>.</span>
				<span class="credit">
					<a href="http://xiphias.photography/" title="xiphias.photography"><?php echo plog_tr('Design by') ?> xiphias.photography</a>
				</span><!-- /credit -->
			</div>

			<div class="col-2 text-right">
				<?php if (plogger_rss_feed_button() != '') { ?>
					<div id="rss-tag-container"><?php echo plogger_rss_feed_button(); ?></div><!-- /rss-tag-container -->
				<?php } ?>
			</div>
		</div><!-- /row -->

	</footer><!-- /container-fluid -->

	<?php echo plogger_download_selected_form_end(); ?>

	</div><!-- /plog-wrapper -->