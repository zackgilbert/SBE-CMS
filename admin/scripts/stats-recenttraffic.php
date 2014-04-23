<?php 

	header('Content-Type: text/xml'); 
	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

	$content = get_stats('VisitsSummary.get', "&period=day&date=" . substr(daysFromNow(-30, TODAY), 0, 10) . "," . TODAY);
	
	/*foreach ($content as $month => $numbers) {
		echo $month . ";" .. "\n";
	}
	die();*/
	
?>
<chart>
	<series>
		<?php $i=0; foreach ($content as $day => $numbers) : ?> 
		<value xid="<?= $i; ?>"><?= format_date($day, 'M. j'); ?></value>
		<?php $i++; endforeach; ?> 
	</series>
	<graphs>
		<graph gid="0" line_width="2" fill_alpha="10" fill_color="#6cb35d" color="#<?= trim(request('color', '9FCD95'), '#'); ?>" color_hover="#<?= trim(request('color_hover', '38627A'), '#'); ?>">
			<?php $i=0; foreach ($content as $day => $numbers) : ?> 
			<value xid="<?= $i; ?>" bullet="round" description="Visitor<?= (isset($numbers['nb_visits']) && ($numbers['nb_visits'] == 1)) ? '' : 's'; ?> for <?= format_date($day, 'M. j'); ?>"><?= ((isset($numbers['nb_visits'])) ? $numbers['nb_visits'] : 0); ?></value>
			<?php $i++; endforeach; ?> 
		</graph>
	</graphs>
</chart>
