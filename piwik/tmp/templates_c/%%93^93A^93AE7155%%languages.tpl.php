<?php /* Smarty version 2.6.25, created on 2009-11-20 16:49:22
         compiled from LanguagesManager/templates/languages.tpl */ ?>
<span class="topBarElem" style="padding-right:70px">
	<span id="languageSelection" style="display:none;position:absolute">
		<form action="index.php?module=LanguagesManager&action=saveLanguage" method="get">
		<select name="language">
			<option value="<?php echo $this->_tpl_vars['currentLanguageCode']; ?>
"><?php echo $this->_tpl_vars['currentLanguageName']; ?>
</option>
			<?php $_from = $this->_tpl_vars['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['language']):
?>
			<option value="<?php echo $this->_tpl_vars['language']['code']; ?>
"><?php echo $this->_tpl_vars['language']['name']; ?>
</option>
			<?php endforeach; endif; unset($_from); ?>
		</select>
		<input type="submit" value="go"/>
		</form>
	</span>
	
	<?php echo '<script language="javascript">
	$(document).ready(function() {
		$("#languageSelection").fdd2div({CssClassName:"formDiv"});
		$("#languageSelection").show();
		$("#languageSelection ul").hide();
	});</script>
	'; ?>

</span>