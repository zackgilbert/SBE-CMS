<?php 

	header('Content-Type: text/xml'); 
	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

	$content = get_stats('VisitTime.getVisitInformationPerLocalTime', '&period=year&date=today');

?>
<chart>
	<series>
		<?php foreach ($content as $key => $hour) : ?> 
		<value xid="<?= $key; ?>"><?= $hour['label']; ?></value>
		<?php endforeach; ?> 
	</series>
	<graphs>
		<graph gid="0" color="#<?= trim(request('color', '9FCD95'), '#'); ?>">
			<?php foreach ($content as $key => $hour) : ?> 
			<value xid="<?= $key; ?>"><?= $hour['nb_visits']; ?></value>
			<?php endforeach; ?> 
		</graph>
	</graphs>
</chart>
