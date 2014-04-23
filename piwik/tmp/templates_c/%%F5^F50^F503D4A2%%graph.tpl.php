<?php /* Smarty version 2.6.25, created on 2009-11-20 16:49:26
         compiled from CoreHome/templates/graph.tpl */ ?>
<div id="<?php echo $this->_tpl_vars['properties']['uniqueId']; ?>
">
	<div class="<?php if ($this->_tpl_vars['graphType'] == 'evolution'): ?>dataTableGraphEvolutionWrapper<?php else: ?>dataTableGraphWrapper<?php endif; ?>">

	<?php echo $this->_tpl_vars['jsInvocationTag']; ?>

	<?php if ($this->_tpl_vars['properties']['show_footer']): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "CoreHome/templates/datatable_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "CoreHome/templates/datatable_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>
	
	</div>
</div>