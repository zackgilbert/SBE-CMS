<?php if (file_exists(ABSPATH . "piwik/config/config.ini.php") && (strpos($_SERVER['REQUEST_URI'], "admin/pages/versions") === false)) : ?>

<!-- Piwik -->
<script type="text/javascript">
var pkBaseURL = "<?= LOCATION; ?>piwik/";
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 1);
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
</script>
<!-- End Piwik Tag -->
<?php endif; ?>