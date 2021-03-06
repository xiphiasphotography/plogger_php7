<?php include('theme_functions.php'); ?>
<!--Output highest level container division-->

<div id="plog-wrapper">

	<?php echo plogger_download_selected_form_start(); ?>

	<header id="header" class="container-fluid">
		<div class="row gutters-10">
			<div class="col-12">
				<?php echo generate_header(); ?>
			</div>

			<nav id="breadcrumbs" class="col">
				<?php echo generate_breadcrumb('<i class="fas fa-home"></i>', ''); ?>
			</nav><!-- /breadcrumbs -->

			<div id="slideshow" class="col-auto text-right">
				<?php echo plogger_slideshow_link(); ?>
				<?php echo plogger_print_button(); ?>
			</div><!-- /slideshow -->
		</div><!-- /row -->
	</header>