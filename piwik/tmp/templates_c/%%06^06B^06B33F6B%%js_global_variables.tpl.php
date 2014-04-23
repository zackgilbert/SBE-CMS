<?php /* Smarty version 2.6.25, created on 2009-11-20 16:49:22
         compiled from CoreHome/templates/js_global_variables.tpl */ ?>
<script type="text/javascript">
	var piwik = <?php echo '{}'; ?>
;
	piwik.token_auth = "<?php echo $this->_tpl_vars['token_auth']; ?>
";
	piwik.piwik_url = "<?php echo $this->_tpl_vars['piwikUrl']; ?>
";
	<?php if (isset ( $this->_tpl_vars['idSite'] )): ?>piwik.idSite = "<?php echo $this->_tpl_vars['idSite']; ?>
";<?php endif; ?>
	<?php if (isset ( $this->_tpl_vars['siteName'] )): ?>piwik.siteName = "<?php echo $this->_tpl_vars['siteName']; ?>
";<?php endif; ?>
	<?php if (isset ( $this->_tpl_vars['siteMainUrl'] )): ?>piwik.siteMainUrl = "<?php echo $this->_tpl_vars['siteMainUrl']; ?>
";<?php endif; ?>
	<?php if (isset ( $this->_tpl_vars['period'] )): ?>piwik.period = "<?php echo $this->_tpl_vars['period']; ?>
";<?php endif; ?>
	<?php if (isset ( $this->_tpl_vars['date'] )): ?>piwik.currentDateString = "<?php echo $this->_tpl_vars['date']; ?>
";<?php endif; ?>
	<?php if (isset ( $this->_tpl_vars['minDateYear'] )): ?>piwik.minDateYear = <?php echo $this->_tpl_vars['minDateYear']; ?>
;<?php endif; ?>
	<?php if (isset ( $this->_tpl_vars['minDateMonth'] )): ?>piwik.minDateMonth = parseInt("<?php echo $this->_tpl_vars['minDateMonth']; ?>
", 10);<?php endif; ?>
	<?php if (isset ( $this->_tpl_vars['minDateDay'] )): ?>piwik.minDateDay = parseInt("<?php echo $this->_tpl_vars['minDateDay']; ?>
", 10);<?php endif; ?>
</script>