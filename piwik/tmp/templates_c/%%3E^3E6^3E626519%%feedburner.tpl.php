<?php /* Smarty version 2.6.25, created on 2009-11-20 16:49:26
         compiled from ExampleFeedburner/templates/feedburner.tpl */ ?>

<script type="text/javascript">
	var idSite = <?php echo $this->_tpl_vars['idSite']; ?>
;

<?php echo '
	$(document).ready(function(){ 
	
	function getName()
	{
		return $("#feedburnerName").val();
	}
	function loadIframe()
	{
		var feedburnerName = getName();
		$("#feedburnerIframe").html(
			\'<iframe height=100px frameborder="0" marginheight="10" marginwidth="10" \\
				src="http://www.feedburner.com/fb/ticker/api-ticker2.jsp?uris=\'+feedburnerName+\'"></iframe>\');
	}
	
	$("#feedburnerSubmit").click( function(){
		var feedburnerName = getName();
		$.get(\'index.php?module=ExampleFeedburner&action=saveFeedburnerName&idSite=\'+idSite+\'&name=\'+feedburnerName);
		loadIframe();
		
	});
	
	loadIframe();
});
</script>
'; ?>
			
<span id="feedburnerIframe"></span>

<center>
<input id="feedburnerName" type="text" value="<?php echo $this->_tpl_vars['feedburnerFeedName']; ?>
">
<input id="feedburnerSubmit" type="submit" value="ok">
</center>
