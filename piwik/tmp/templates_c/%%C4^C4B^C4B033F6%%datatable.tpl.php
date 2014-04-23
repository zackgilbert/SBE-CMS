<?php /* Smarty version 2.6.25, created on 2009-11-20 16:49:26
         compiled from CoreHome/templates/datatable.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'CoreHome/templates/datatable.tpl', 7, false),array('function', 'logoHtml', 'CoreHome/templates/datatable.tpl', 25, false),)), $this); ?>
<div id="<?php echo $this->_tpl_vars['properties']['uniqueId']; ?>
">
	<div class="<?php if (isset ( $this->_tpl_vars['javascriptVariablesToSet']['idSubtable'] ) && $this->_tpl_vars['javascriptVariablesToSet']['idSubtable'] != 0): ?>sub<?php endif; ?><?php if ($this->_tpl_vars['javascriptVariablesToSet']['viewDataTable'] == 'tableAllColumns'): ?>dataTableAllColumnsWrapper<?php elseif ($this->_tpl_vars['javascriptVariablesToSet']['viewDataTable'] == 'tableGoals'): ?>dataTableAllColumnsWrapper<?php else: ?>dataTableWrapper<?php endif; ?>">
	<?php if (isset ( $this->_tpl_vars['arrayDataTable']['result'] ) && $this->_tpl_vars['arrayDataTable']['result'] == 'error'): ?>
		<?php echo $this->_tpl_vars['arrayDataTable']['message']; ?>
 
	<?php else: ?>
		<?php if (count ( $this->_tpl_vars['arrayDataTable'] ) == 0): ?>
		<div id="emptyDatatable"><?php echo ((is_array($_tmp='CoreHome_TableNoData')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</div>
		<?php else: ?>
			<a name="<?php echo $this->_tpl_vars['properties']['uniqueId']; ?>
"></a>
			<table cellspacing="0" class="dataTable"> 
			<thead>
			<tr>
			<?php $_from = $this->_tpl_vars['dataTableColumns']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['column']):
?>
				<th class="sortable" id="<?php echo $this->_tpl_vars['column']; ?>
"><div id="thDIV"><?php echo $this->_tpl_vars['columnTranslations'][$this->_tpl_vars['column']]; ?>
</div></th>
			<?php endforeach; endif; unset($_from); ?>
			</tr>
			</thead>
			
			<tbody>
			<?php $_from = $this->_tpl_vars['arrayDataTable']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['row']):
?>
			<tr <?php if ($this->_tpl_vars['row']['idsubdatatable'] && $this->_tpl_vars['javascriptVariablesToSet']['controllerActionCalledWhenRequestSubTable'] != null): ?>class="subDataTable" id="<?php echo $this->_tpl_vars['row']['idsubdatatable']; ?>
"<?php endif; ?>>
<?php $_from = $this->_tpl_vars['dataTableColumns']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['column']):
?>
<td>
<?php if (! $this->_tpl_vars['row']['idsubdatatable'] && $this->_tpl_vars['column'] == 'label' && isset ( $this->_tpl_vars['row']['metadata']['url'] )): ?><span id="urlLink"><?php echo $this->_tpl_vars['row']['metadata']['url']; ?>
</span><?php endif; ?>
<?php if ($this->_tpl_vars['column'] == 'label'): ?><?php echo smarty_function_logoHtml(array('metadata' => $this->_tpl_vars['row']['metadata'],'alt' => $this->_tpl_vars['row']['columns']['label']), $this);?>
<?php endif; ?>
<?php if (isset ( $this->_tpl_vars['row']['columns'][$this->_tpl_vars['column']] )): ?><?php echo $this->_tpl_vars['row']['columns'][$this->_tpl_vars['column']]; ?>
<?php else: ?><?php echo $this->_tpl_vars['defaultWhenColumnValueNotDefined']; ?>
<?php endif; ?>
</td>
<?php endforeach; endif; unset($_from); ?>
			</tr>
			<?php endforeach; endif; unset($_from); ?>
			</tbody>
			</table>
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
	<?php endif; ?>
	</div>
</div>