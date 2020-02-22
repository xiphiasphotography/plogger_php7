<?php include('theme_functions.php'); ?>
<!--Output highest level container division-->

<div id="plog-wrapper">

	<header id="header" class="container-fluid">
		<div class="row gutters-10">
			<?php echo generate_header(); ?>

			<div id="breadcrumbs">
				<div id="slideshow">
					<?php echo plogger_slideshow_link(); ?>
					<?php echo plogger_print_button(); ?>

				</div><!-- /slideshow -->
				<?php echo generate_breadcrumb('<i class="fas fa-home"></i>', ' | '); ?>

			</div><!-- /breadcrumbs -->
		</div><!-- /row -->
	</header>

	<?php echo plogger_download_selected_form_start(); ?>