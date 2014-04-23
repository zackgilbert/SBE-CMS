<?php /* Smarty version 2.6.25, created on 2009-11-20 16:45:48
         compiled from Installation/templates/systemCheck.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'Installation/templates/systemCheck.tpl', 5, false),array('function', 'url', 'Installation/templates/systemCheck.tpl', 112, false),)), $this); ?>
<?php $this->assign('ok', "<img src='themes/default/images/ok.png' />"); ?>
<?php $this->assign('error', "<img src='themes/default/images/error.png' />"); ?>
<?php $this->assign('warning', "<img src='themes/default/images/warning.png' />"); ?>

<h1><?php echo ((is_array($_tmp='Installation_SystemCheck')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h1>


<table class="infosServer">
	<tr>
		<td class="label"><?php echo ((is_array($_tmp='Installation_SystemCheckPhp')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 &gt; <?php echo $this->_tpl_vars['infos']['phpVersion_minimum']; ?>
</td>
		<td><?php if ($this->_tpl_vars['infos']['phpVersion_ok']): ?><?php echo $this->_tpl_vars['ok']; ?>
<?php else: ?><?php echo $this->_tpl_vars['error']; ?>
<?php endif; ?></td>
	</tr><tr>
		<td class="label"><?php echo ((is_array($_tmp='Installation_SystemCheckPdo')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</td>
		<td><?php if ($this->_tpl_vars['infos']['pdo_ok']): ?><?php echo $this->_tpl_vars['ok']; ?>

		<?php else: ?><?php echo $this->_tpl_vars['error']; ?>
<?php endif; ?>	
		</td>
	</tr>  
	<tr>
		<td class="label"><?php echo ((is_array($_tmp='Installation_SystemCheckPdoMysql')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</td>
		<td><?php if ($this->_tpl_vars['infos']['pdo_mysql_ok']): ?><?php echo $this->_tpl_vars['ok']; ?>

		<?php else: ?><?php echo $this->_tpl_vars['error']; ?>

		<?php endif; ?>
		
		<?php if (! $this->_tpl_vars['infos']['pdo_mysql_ok'] || ! $this->_tpl_vars['infos']['pdo_ok']): ?>
			<p class="error" style="width:80%"><?php echo ((is_array($_tmp='Installation_SystemCheckPdoError')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

			<small>
			<br /><br />
			<?php echo ((is_array($_tmp='Installation_SystemCheckPdoHelp')) ? $this->_run_mod_handler('translate', true, $_tmp, "<br/><code>extension=php_pdo.dll</code><br /><code>extension=php_pdo_mysql.dll</code><br />", "<code>--with-pdo-mysql </code>", "<br/><code>extension=pdo.so</code><br /><code>extension=pdo_mysql.so</code><br />") : smarty_modifier_translate($_tmp, "<br/><code>extension=php_pdo.dll</code><br /><code>extension=php_pdo_mysql.dll</code><br />", "<code>--with-pdo-mysql </code>", "<br/><code>extension=pdo.so</code><br /><code>extension=pdo_mysql.so</code><br />")); ?>

			</small>
			</p>
		<?php endif; ?>
		
		</td>
	</tr>
	<tr>
		<td valign="top">
			<?php echo ((is_array($_tmp='Installation_SystemCheckWriteDirs')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

		</td>
		<td>
			<?php $_from = $this->_tpl_vars['infos']['directories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['dir'] => $this->_tpl_vars['bool']):
?>
				<?php if ($this->_tpl_vars['bool']): ?><?php echo $this->_tpl_vars['ok']; ?>
<?php else: ?>
				<span style="color:red"><?php echo $this->_tpl_vars['error']; ?>
</span><?php endif; ?> 
				<?php echo $this->_tpl_vars['dir']; ?>

				<br />				
			<?php endforeach; endif; unset($_from); ?>
		</td>
	</tr>
</table>

<?php if ($this->_tpl_vars['problemWithSomeDirectories']): ?>
	<br />
	<div class="error">
			<?php echo ((is_array($_tmp='Installation_SystemCheckWriteDirsHelp')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
:
	<?php $_from = $this->_tpl_vars['infos']['directories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['dir'] => $this->_tpl_vars['bool']):
?>
		<ul><?php if (! $this->_tpl_vars['bool']): ?>
			<li><pre>chmod a+w <?php echo $this->_tpl_vars['dir']; ?>
</pre></li>
		<?php endif; ?>
		</ul>
	<?php endforeach; endif; unset($_from); ?>
	</div>
	<br />
<?php endif; ?>
<h1>Optional</h1>
<table class="infos">
	<tr>
		<td class="label"><?php echo ((is_array($_tmp='Installation_SystemCheckMemoryLimit')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</td>
		<td>
			<?php echo $this->_tpl_vars['infos']['memoryCurrent']; ?>

			<?php if ($this->_tpl_vars['infos']['memory_ok']): ?><?php echo $this->_tpl_vars['ok']; ?>
<?php else: ?><?php echo $this->_tpl_vars['warning']; ?>
 
				<br /><i><?php echo ((is_array($_tmp='Installation_SystemCheckMemoryLimitHelp')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</i><?php endif; ?>	
		</td>
	</tr>
	<tr>
		<td class="label"><?php echo ((is_array($_tmp='Installation_SystemCheckGD')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</td>
		<td>
			<?php if ($this->_tpl_vars['infos']['gd_ok']): ?><?php echo $this->_tpl_vars['ok']; ?>
<?php else: ?><?php echo $this->_tpl_vars['warning']; ?>
 <br /><i><?php echo ((is_array($_tmp='Installation_SystemCheckGDHelp')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</i><?php endif; ?>
		</td>
	</tr>
	<tr>
		<td class="label"><?php echo ((is_array($_tmp='Installation_SystemCheckTimeLimit')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</td>
		<td><?php if ($this->_tpl_vars['infos']['setTimeLimit_ok']): ?><?php echo $this->_tpl_vars['ok']; ?>
<?php else: ?><?php echo $this->_tpl_vars['warning']; ?>

			<br /><i><?php echo ((is_array($_tmp='Installation_SystemCheckTimeLimitHelp')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</i><?php endif; ?></td>
	</tr>
	<tr>
		<td class="label"><?php echo ((is_array($_tmp='Installation_SystemCheckMail')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</td>
		<td><?php if ($this->_tpl_vars['infos']['mail_ok']): ?><?php echo $this->_tpl_vars['ok']; ?>
<?php else: ?><?php echo $this->_tpl_vars['warning']; ?>
<?php endif; ?></td>
	</tr>
</table>


<?php if (! $this->_tpl_vars['showNextStep']): ?>
<?php echo '
<style>
#legend {
	border:1px solid #A5A5A5;
	padding:5px;
	color:#727272;
	margin-top:30px;
}
</style>
'; ?>

<div id="legend"><small>
<b>Legend</b>
<br />
<?php echo $this->_tpl_vars['ok']; ?>
 <?php echo ((is_array($_tmp='General_Ok')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<br />
<?php echo $this->_tpl_vars['error']; ?>
 <?php echo ((is_array($_tmp='General_Error')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
: <?php echo ((is_array($_tmp='Installation_SystemCheckError')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 <br />
<?php echo $this->_tpl_vars['warning']; ?>
 <?php echo ((is_array($_tmp='General_Warning')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
: <?php echo ((is_array($_tmp='Installation_SystemCheckWarning')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 <br />
</small></div>


<p class="nextStep">
	<a href="<?php echo smarty_function_url(array(), $this);?>
"><?php echo ((is_array($_tmp='General_Refresh')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 &raquo;</a>
</p>
<?php endif; ?>