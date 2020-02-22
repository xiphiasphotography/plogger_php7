<?php echo plogger_generate_seo_meta_tags(); ?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="<?= THEME_URL ?>fontawesome.min.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?= THEME_URL ?>gallery.css" media="screen" />
<script type="text/javascript" src="<?= THEME_URL ?>dynamics.js"></script>
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "WebSite",
  "url": "<?= GALLERY_URL ?>",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "<?= GALLERY_URL ?>?searchterms={search_term_string}&level=search",
    "query-input": "required name=search_term_string"
  }
}
</script>