<?php /* Smarty version 2.6.25, created on 2009-11-20 16:45:45
         compiled from Installation/templates/allSteps.tpl */ ?>
<ul>
<?php $_from = $this->_tpl_vars['allStepsTitle']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['stepId'] => $this->_tpl_vars['stepName']):
?>
	<?php if ($this->_tpl_vars['currentStepId'] > $this->_tpl_vars['stepId']): ?>
	<li class="pastStep"><?php echo $this->_tpl_vars['stepName']; ?>
</li>
	<?php elseif ($this->_tpl_vars['currentStepId'] == $this->_tpl_vars['stepId']): ?>
	<li class="actualStep"><?php echo $this->_tpl_vars['stepName']; ?>
</li>
	<?php else: ?>
	<li class="futureStep"><?php echo $this->_tpl_vars['stepName']; ?>
</li>
	<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
</ul>