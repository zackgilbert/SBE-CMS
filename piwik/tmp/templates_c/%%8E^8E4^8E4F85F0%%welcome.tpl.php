<?php /* Smarty version 2.6.25, created on 2009-11-20 16:45:45
         compiled from Installation/templates/welcome.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'Installation/templates/welcome.tpl', 1, false),)), $this); ?>
<h1><?php echo ((is_array($_tmp='Installation_Welcome')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h1>

<?php echo ((is_array($_tmp='Installation_WelcomeHelp')) ? $this->_run_mod_handler('translate', true, $_tmp, $this->_tpl_vars['totalNumberOfSteps']) : smarty_modifier_translate($_tmp, $this->_tpl_vars['totalNumberOfSteps'])); ?>

