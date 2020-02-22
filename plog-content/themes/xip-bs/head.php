<?php echo plogger_generate_seo_meta_tags(); ?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="<?php echo THEME_URL ?>gallery.css" media="screen" />
<script type="text/javascript" src="<?php echo THEME_URL ?>dynamics.js"></script>
<?php 
	global $config;
	if (file_exists(THEME_DIR.'/header.php')) {
		$search_url = $config['gallery_url'].THEME_DIR.'/search.php');
	} else {
		$search_url = $config['gallery_url'].$config['basedir'].'plog-content/themes/default/search.php');
	}
?>
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "WebSite",
  "url": "<?= plogger_get_picture_url() ?>",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "<?= $search_url ?>?searchterms={search_term_string}&level=search",
    "query-input": "required name=search_term_string"
  }
}
</script>