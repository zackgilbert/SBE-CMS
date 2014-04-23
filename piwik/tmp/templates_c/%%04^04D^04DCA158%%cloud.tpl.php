<?php /* Smarty version 2.6.25, created on 2009-11-20 16:49:26
         compiled from CoreHome/templates/cloud.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'CoreHome/templates/cloud.tpl', 53, false),)), $this); ?>
<div id="<?php echo $this->_tpl_vars['properties']['uniqueId']; ?>
">
<?php echo '
<style>

#tagCloud{
	width:100%;
}
img {
	border:0;
}
.word a {
	text-decoration:none;
}
.word {
	padding: 4px 4px 4px 4px;
}
.valueIsZero {
	text-decoration: line-through;
}
span.size0, span.size0 a {
	color: #344971;
	font-size: 28px;
}
span.size1, span.size1 a {
	color: #344971;
	font-size: 24px;
}
span.size2, span.size2 a {
	color: #4B74AD;
	font-size:20px;
}
span.size3, span.size3 a {
	color: #A3A8B6;
	font-size: 16px;
}
span.size4, span.size4 a {
	color: #A3A8B6;
	font-size: 15px;
}
span.size5, span.size5 a {
	color: #A3A8B6;
	font-size: 14px;
}
span.size6, span.size6 a {
	color: #A3A8B6;
	font-size: 11px;
}
</style>
'; ?>


<div id="tagCloud">
<?php if (count ( $this->_tpl_vars['cloudValues'] ) == 0): ?>
	<div id="emptyDatatable"><?php echo ((is_array($_tmp='General_NoDataForTagCloud')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</div>
<?php else: ?>
	<?php $_from = $this->_tpl_vars['cloudValues']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['word'] => $this->_tpl_vars['value']):
?>
	<span title="<?php echo $this->_tpl_vars['value']['word']; ?>
 (<?php echo $this->_tpl_vars['value']['value']; ?>
 <?php echo $this->_tpl_vars['columnTranslation']; ?>
)" class="word size<?php echo $this->_tpl_vars['value']['size']; ?>
  <?php if ($this->_tpl_vars['value']['value'] == 0): ?>valueIsZero<?php endif; ?>">
	<?php if (false !== $this->_tpl_vars['labelMetadata'][$this->_tpl_vars['value']['word']]['url']): ?><a href="<?php echo $this->_tpl_vars['labelMetadata'][$this->_tpl_vars['value']['word']]['url']; ?>
" target="_blank"><?php endif; ?>
	<?php if (false !== $this->_tpl_vars['labelMetadata'][$this->_tpl_vars['value']['word']]['logo']): ?><img src="<?php echo $this->_tpl_vars['labelMetadata'][$this->_tpl_vars['value']['word']]['logo']; ?>
" width="<?php echo $this->_tpl_vars['value']['logoWidth']; ?>
"><?php else: ?>
	<?php echo $this->_tpl_vars['value']['wordTruncated']; ?>
<?php endif; ?><?php if (false !== $this->_tpl_vars['labelMetadata'][$this->_tpl_vars['value']['word']]['url']): ?></a><?php endif; ?></span>
	<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['properties']['show_footer']): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "CoreHome/templates/datatable_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "CoreHome/templates/datatable_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
</div>