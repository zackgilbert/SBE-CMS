<?php /* Smarty version 2.6.25, created on 2009-11-20 16:49:00
         compiled from Installation/templates/displayJavascriptCode.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'Installation/templates/displayJavascriptCode.tpl', 33, false),)), $this); ?>

<?php echo '
<style>
code {
	background-color:#F0F7FF;
	border-color:#00008B;
	border-style:dashed dashed dashed solid;
	border-width:1px 1px 1px 5px;
	direction:ltr;
	display:block;
	font-size:80%;
	margin:2px 2px 20px;
	padding:4px;
	text-align:left;
}
</style>

<script>
$(document).ready( function(){
	$(\'code\').click( function(){ $(this).select(); });
});
</script>

'; ?>


<?php if (isset ( $this->_tpl_vars['displayfirstWebsiteSetupSuccess'] )): ?>

<span id="toFade" class="success">
	Website <?php echo $this->_tpl_vars['websiteName']; ?>
 created with success!
	<img src="themes/default/images/success_medium.png">
</span>
<?php endif; ?>
<h1><?php echo ((is_array($_tmp='Installation_JsTag')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h1>
<?php echo ((is_array($_tmp='Installation_JsTagHelp')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

<code>
<?php echo $this->_tpl_vars['javascriptTag']; ?>

</code>

<h1>Quick Help:</h1>
<ul>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "SitesManager/templates/JavascriptTagHelp.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<li>For medium and high traffic websites, check out the  <a target="_blank" href="http://piwik.org/docs/setup-auto-archiving/">How to setup auto archiving page</a> to make Piwik run really fast!</li>
<!-- <li>Link to help with the main blog engines wordpress/drupal/myspace/blogspot</li> -->
</ul>