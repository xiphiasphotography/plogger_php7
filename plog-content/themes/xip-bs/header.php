<?php include('theme_functions.php'); ?>
<!--Output highest level container division-->

<div id="plog-wrapper">

	<header id="header" class="container-fluid">
		<div class="row gutters-10">
			<div class="col-12">
				<?php echo generate_header(); ?>
			</div>
			<div class="col-9">
				<div id="breadcrumbs">
					<?php echo generate_breadcrumb('<i class="fas fa-home"></i>', ' | '); ?>
				</div><!-- /breadcrumbs -->

				<div id="slideshow" class="col-3">
					<?php echo plogger_slideshow_link(); ?>
					<?php echo plogger_print_button(); ?>
				</div><!-- /slideshow -->
			</div><!-- /row -->
	</header>

	<?php echo plogger_download_selected_form_start(); ?>