<?php 

	header('Content-Type: text/xml'); 
	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

	$content = get_stats('VisitFrequency.get', "&period=month&date=" . substr(yearsFromNow(-1, TODAY), 0, 10) . "," . TODAY);
	
	/*foreach ($content as $month => $numbers) {
		echo $month . ";" . ((isset($numbers['nb_visits_returning'])) ? $numbers['nb_visits_returning'] : 0) . "\n";
	}*/
	
?>
<chart>
	<series>
		<?php $i=0; foreach ($content as $month => $numbers) : ?> 
		<value xid="<?= $i; ?>"><?= format_date($month."-01", 'M. y'); ?></value>
		<?php $i++; endforeach; ?> 
	</series>
	<graphs>
		<graph gid="0" line_width="2" fill_alpha="10" fill_color="#6cb35d" color="#<?= trim(request('color', '9FCD95'), '#'); ?>" color_hover="#<?= trim(request('color_hover', '38627A'), '#'); ?>">
			<?php $i=0; foreach ($content as $month => $numbers) : ?> 
			<value xid="<?= $i; ?>" bullet="round" description="Visitor<?= (isset($numbers['nb_visits_returning']) && ($numbers['nb_visits_returning'] == 1)) ? '' : 's'; ?> for <?= format_date($month."-01", 'M. y'); ?>"><?= ((isset($numbers['nb_visits_returning'])) ? $numbers['nb_visits_returning'] : 0); ?></value>
			<?php $i++; endforeach; ?> 
		</graph>
	</graphs>
</chart>
