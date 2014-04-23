
	<thead>
		<tr>
			<th colspan="7"><?= format_date(get_var('calendar'), "F Y"); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="weekday">S</td>
			<td class="weekday">M</td>
			<td class="weekday">T</td>
			<td class="weekday">W</td>
			<td class="weekday">T</td>
			<td class="weekday">F</td>
			<td class="weekday">S</td>
		</tr>
		<?php
			$year = (calendar('year')) ? calendar('year') : year();
			$month = (calendar('month')) ? calendar('month') : month();
			$day = (calendar('day')) ? calendar('day') : false;
			//$day = (get_var('calendar_day')) ? get_var('calendar_day') : false;
			$monthStartsOn = format_date(timestamp($year, $month, 1), 'w');
			$daysInMonth = daysInMonth($month, $year);
			$tempDay = 0;
			for ($week=0; $week<6; $week++) :
				if ($tempDay < $daysInMonth) :
		?>
			<tr>
				<?php
					for ($weekDay=0; $weekDay<7; $weekDay++) :
						if (($week!=0) || ($weekDay >= $monthStartsOn)) 
							($tempDay++);
						$tempHTML = (($tempDay < 1) || ($tempDay > $daysInMonth)) ? '&nbsp;' : $tempDay;
		
						$tempClass = 'day';
						if (($tempHTML == '&nbsp;') || ($year < year()) || (($year == year()) && ($month < month())) || (($year == year()) && ($month == month()) && (($tempDay < day()) || ($tempDay > $daysInMonth)))) 
							$tempClass .= " inactive";
						if (($year == year()) && ($month == month()) && ($tempDay == day())) 
							$tempClass .= " today";
						if ($tempDay === ((int)$day)) 
							$tempClass .= " current";
		
						if (strpos($tempClass, "day inactive") === false) 
							$tempHTML = '<a href="' . get_sitemap_section_url() . calendar('year') . '/' . calendar('month') . '/' . pad($tempDay) . '/">' . $tempHTML . '</a>';
	
				?>
				<td class="<?= $tempClass; ?>"><?= $tempHTML; ?></td>
				<?php
					endfor;
				?>
			</tr>
		<?php
				endif;
			endfor;
		?>
	</tbody>
	