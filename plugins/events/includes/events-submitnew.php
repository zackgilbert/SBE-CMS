<!--
<div class="event-submitnew-button"<?= (get('submit') === '') ? ' style="display:none;"' : ''; ?>>
	
	<a href="javascript:;" onclick="$('.event-submitnew').show(); $('.event-submitnew-button').hide();">
		<img src="../plugins/events/images/btn-event-submitnew.gif" alt="Submit New Event" title="Submit a New Event" />
	</a>
	
</div>

<div class="event-submitnew"<?= (get('submit') !== '') ? ' style="display:none;"' : ''; ?>>

	<h3 class="submitnew-title">Submit New Event</h3>
	
	<form method="post" action="<?= LOCATION; ?>plugins/events/scripts/events-save/">
		<div>
			<input type="hidden" name="required" value="event[category],event[name],event[contact]"/>
			<input type="hidden" name="redirect[failure]" value="<?= get_sitemap_section_url(); ?>?submit"/>
			<input type="hidden" name="redirect[success]" value="<?= get_sitemap_section_url(); ?>"/>
			<input type="hidden" name="event[sitemap_id]" value="<?= section_id(); ?>"/>
		</div>
		
		<div class="submitnew-item">
			<label for="event-category">Event Category:</label>
			<select name="event[category]" id="event-category" onchange="changeEventType(this);"<?php if (is_required('event[category]')) echo ' class="required"'; ?>>
				<option value="">Select Category</option>
				<?php foreach (get_event_categories() as $category) : ?> 
				<option value="<?= $category['id']; ?>"<?= (value('event[category]') == $category['id']) ? ' selected="selected"' : ''; ?>><?= valid($category['name']); ?></option>
				<?php endforeach; ?> 
			</select>
		</div>
		
		<div class="submitnew-item">
			<label for="event-name">Event Name:</label>
			<input type="text" name="event[name]" id="event-name" class="field-medium<?php if (is_required('event[name]')) echo ' required'; ?>" value="<?= value('event[name]'); ?>"/>
		</div>
		
		<?php /*?><div class="submitnew-item">
			<label for="event-location">Event Location:</label>
			<span id="event-location-container">
				<select name="event[location]" id="event-location" ?> class="venues<?php if (is_required('event[location]')) echo ' required'; ?>">
					<option value="">Select Venue</option>
					<?php foreach (get_event_locations() as $venue) : ?> 
					<option value="<?= $venue['id']; ?>"<?= (value('event[location]') == $venue['id']) ? ' selected="selected"' : ''; ?>><?= valid($venue['name']); ?></option>
					<?php endforeach; ?> 
				</select> or <a href="javascript:;" onclick="changeLocation(this, '');" class="location-choose">Use a Different Address</a>
			</span>
		</div>
		<?php if (value('event[location]') && !is_numeric(value('event[location]'))) : ?> 
		<script type="text/javascript">
			$(document).ready(function(){ 
				changeLocation($('#event-location-container').find('a').get(0), '<?= value("event[location]"); ?>'); 
			});
		</script>
		<?php endif; ?>
		<?php */ ?>
		<?php /* ?><div class="submitnew-item">
			<label for="event-location">Event Location:</label>
			<input type="hidden" name="event[location]" value="<?= value('event[location]'); ?>"/>
			<input type="text" id="event-location" name="search[location]" class="field-medium venues<?php if (is_required('search[location]')) echo ' required'; ?>" value="<?= value('search[location]'); ?>" />
		</div><?php */ ?> 
		
		<div class="submitnew-item">
			
			<label for="event-date-occurrence">Event Date(s):</label>
			
			<div class="item-eventtime">
				
				<div>
				
					This event 
					<select name="event[date][occurrence]" id="event-date-occurrence" onchange="changeOccurrence(this);">
						<option value="once">occurs once</option>
						<option value="repeats">repeats</option>
					</select>
					
					<span class="occurrence once">	
						<select onchange="(this.value == 'once') ? $('.once .multiple').hide().find('input, select').attr('disabled','disabled') : $('.once .multiple').show().find('input, select').removeAttr('disabled');">
							<option value="once">on</option>
							<option value="multiple">from</option>
						</select>						
						<span class="date-container"><input type="text" name="event[date][once][start_date]" value="<?= TODAY; ?>" class="datepicker field-date" /></span>
						<span class="multiple">
							to <span class="date-container"><input type="text" name="event[date][once][end_date]" value="<?= TODAY; ?>" class="datepicker field-date" disabled="disabled" /></span>
						</span>
					</span>
					
					<span class="occurrence repeats">
						every
						<select name="event[date][repeats][every]" onclick="changeFrequencyPluralization(this);">
							<?php for ($i=1; $i<=30; $i++) : ?> 
							<option value="<?= $i; ?>"><?= $i; ?></option>
							<?php endfor; ?> 
						</select>						
						<select name="event[date][repeats][frequency]" onchange="changeFrequency(this);">
							<option value="weeks">week</option>
							<option value="months">month</option>
							<option value="years">year</option>
						</select>
										
						<span class="frequency weeks">							
							
							<select onchange="(this.value == 'once') ? $('.weeks .multiple').hide().find('input, select').attr('disabled','disabled').end().end().find('.weeks .additional').show().find('input, select').removeAttr('disabled') : $('.weeks .multiple').show().find('input, select').removeAttr('disabled').end().end().find('.weeks .additional').hide().find('input, select').attr('disabled','disabled');">
								<option value="once">on</option>
								<option value="multiple">from</option>
							</select> 
							
							<span class="eventtime-row">
								
								<select name="event[date][repeats][weeks][start]">
									<?php foreach (weekdays() as $num => $name) : ?> 
									<option value="<?= $num; ?>"><?= $name; ?></option>
									<?php endforeach; ?> 
								</select>

								<span class="multiple">		
									to
									<select name="event[date][repeats][weeks][end]">
										<?php foreach (weekdays() as $num => $name) : ?> 
										<option value="<?= $num; ?>"><?= $name; ?></option>
										<?php endforeach; ?> 
									</select>							
								</span>

								<span class="additional">						
									and
									<select name="event[date][repeats][weeks][additional]">
										<option value="">&nbsp;</option>
										<?php foreach (weekdays() as $num => $name) : ?> 
										<option value="<?= $num; ?>"><?= $name; ?></option>
										<?php endforeach; ?> 
									</select>						
								</span>
								
							</span>
							
						</span>
						
						<span class="frequency months">						
							
							<select onchange="(this.value=='once') ? $('.months .multiple').hide().find('input, select').attr('disabled','disabled').end().end().find('.months .additional').show().find('input, select').removeAttr('disabled').end() : $('.months .multiple').show().find('input, select').removeAttr('disabled').end().end().find('.months .additional').hide().find('input, select').attr('disabled','disabled').end();">
								<option value="once">on</option>
								<option value="multiple">from</option>
							</select> 						
							
							<span class="eventtime-row">
								the 
								<select name="event[date][repeats][months][start][num]">
									<?php for ($i=1; $i<=31; $i++) : ?> 
									<option value="<?= $i; ?>"><?= $i . daySuffix($i); ?></option>
									<?php endfor; ?> 
									<option value="-2">second to last</option>
									<option value="-1">last</option>
								</select>						
								<select name="event[date][repeats][months][start][day]">
									<option>day</option>
									<?php foreach (weekdays() as $num => $name) : ?> 
									<option value="<?= $num; ?>"><?= $name; ?></option>
									<?php endforeach; ?> 						
								</select>
								of the month
							</span>
								
							<span class="multiple">							
								to the 
								<select name="event[date][repeats][months][end][num]">
									<?php for ($i=1; $i<=31; $i++) : ?> 
									<option value="<?= $i; ?>"><?= $i . daySuffix($i); ?></option>
									<?php endfor; ?> 
									<option value="-2">second to last</option>
									<option value="-1">last</option>
								</select>	
								<select name="event[date][repeats][months][end][day]">
									<option>day</option>
									<?php foreach (weekdays() as $num => $name) : ?> 
									<option value="<?= $num; ?>"><?= $name; ?></option>
									<?php endforeach; ?> 						
								</select>
								of the month							
							</span>
							
							<span class="additional">						
								and the 
								<select name="event[date][repeats][months][additional][num]">
									<option value="">&nbsp;</option>
									<?php for ($i=1; $i<=31; $i++) : ?> 
									<option value="<?= $i; ?>"><?= $i . daySuffix($i); ?></option>
									<?php endfor; ?> 
									<option value="-2">second to last</option>
									<option value="-1">last</option>
								</select>
								<select name="event[date][repeats][months][additional][day]">
									<option value="">&nbsp;</option>
									<option>day</option>
									<?php foreach (weekdays() as $num => $name) : ?> 
									<option value="<?= $num; ?>"><?= $name; ?></option>
									<?php endforeach; ?> 						
								</select>
								of the month						
							</span>	

						</span>

						<span class="frequency years">						
							<select onchange="(this.value=='once') ? $('.years .multiple').hide().find('input, select').attr('disabled','disabled').end().end().find('.years .additional').show().find('input, select').removeAttr('disabled').end() : $('.years .multiple').show().find('input, select').removeAttr('disabled').end().end().find('.years .additional').hide().find('input, select').attr('disabled','disabled').end();">
								<option value="once">on</option>
								<option value="multiple">from</option>
							</select>						
							
							<span class="eventtime-row">
							
								the 
								<select name="event[date][repeats][years][start][num]">
									<?php for ($i=1; $i<=31; $i++) : ?> 
									<option value="<?= $i; ?>"><?= $i . daySuffix($i); ?></option>
									<?php endfor; ?> 
									<option value="-2">second to last</option>
									<option value="-1">last</option>
								</select>						
								<select name="event[date][repeats][years][start][day]">
									<option>day</option>
									<?php foreach (weekdays() as $num => $name) : ?> 
									<option value="<?= $num; ?>"><?= $name; ?></option>
									<?php endforeach; ?> 						
								</select>						
								of 	
								<select name="event[date][repeats][years][start][month]">
									<?php foreach (months() as $num => $name) : ?> 
									<option value="<?= $num; ?>"><?= $name; ?></option>
									<?php endforeach; ?> 
								</select>
								
							</span>

							<span class="multiple">
								to the 
								<select name="event[date][repeats][years][end][num]">
									<?php for ($i=1; $i<=31; $i++) : ?> 
									<option value="<?= $i; ?>"><?= $i . daySuffix($i); ?></option>
									<?php endfor; ?> 
									<option value="-2">second to last</option>
									<option value="-1">last</option>
								</select>	
								<select name="event[date][repeats][years][end][day]">
									<option>day</option>
									<?php foreach (weekdays() as $num => $name) : ?> 
									<option value="<?= $num; ?>"><?= $name; ?></option>
									<?php endforeach; ?> 						
								</select>	
								of 	
								<select name="event[date][repeats][years][end][month]">
									<?php foreach (months() as $num => $name) : ?> 
									<option value="<?= $num; ?>"><?= $name; ?></option>
									<?php endforeach; ?> 
								</select>
							</span>

							<span class="additional">							
								and the 
								<select name="event[date][repeats][years][additional][num]">
									<option value="">&nbsp;</option>
									<?php for ($i=1; $i<=31; $i++) : ?> 
									<option value="<?= $i; ?>"><?= $i . daySuffix($i); ?></option>
									<?php endfor; ?> 
									<option value="-2">second to last</option>
									<option value="-1">last</option>
								</select>	
								<select name="event[date][repeats][years][additional][day]">
									<option value="">&nbsp;</option>
									<option>day</option>
									<?php foreach (weekdays() as $num => $name) : ?> 
									<option value="<?= $num; ?>"><?= $name; ?></option>
									<?php endforeach; ?> 						
								</select>	
								of 	
								<select name="event[date][repeats][years][additional][month]">
									<option value="">&nbsp;</option>
									<?php foreach (months() as $num => $name) : ?> 
									<option value="<?= $num; ?>"><?= $name; ?></option>
									<?php endforeach; ?> 
								</select>							
							</span>

						</span>

						<span class="eventtime-row">

							starting on <span class="date-container"><input type="text" name="event[date][repeats][start_date]" value="<?= TODAY; ?>" class="datepicker field-date" /></span>
							and						
							<select onchange="(this.value == 'on') ? $('.ending').show().find('input').removeAttr('disabled') : $('.ending').hide().find('input').attr('disabled', 'disabled');">
								<option value="never">never ends</option>
								<option value="on">ends on</option>
							</select>						
							<span class="ending">
							 	<span class="date-container"><input type="text" name="event[date][repeats][ends_on]" value="<?= TODAY; ?>" class="datepicker field-date" /></span>
							</span>

						</span>
						
					</span>
					
				</div>
				
			</div>
			
		</div>
		
		<div class="submitnew-item">
			<label for="event-hours">Event Hours:</label>
			<textarea name="event[hours]" id="event-hours" cols="20" rows="4"<?php if (is_required('event[hours]')) echo ' class="required"'; ?>><?= value('event[hours]'); ?></textarea>
		</div>
		
		<div class="submitnew-item">
			<label for="event-price">Event Price:</label>
			<input type="text" name="event[price]" id="event-price" class="field-medium<?php if (is_required('event[price]')) echo ' required'; ?>" value="<?= value('event[price]'); ?>"/>
		</div>
		
		<div class="submitnew-item">
			<label for="event-age">Event Age:</label>
			<select name="event[age]" id="event-age"<?php if (is_required('event[age]')) echo ' class="required"'; ?>>
				<option value="All Ages">All Ages</option>
				<option value="0-2">0-2</option>
				<option value="3-5">3-5</option>
				<option value="6-10">6-10</option>
				<option value="11-15">11-15</option>
				<option value="16+">16+</option>
			</select>
		</div>
		
		<div class="submitnew-item">
			<label for="event-contact">Contact Info:</label>
			<input type="text" name="event[contact]" id="event-contact" class="field-medium<?php if (is_required('event[contact]')) echo ' required'; ?>" value="<?= value('event[contact]'); ?>"/>
			<p class="item-description">(Email or Phone #)</p>
		</div>
		
		<div class="submitnew-item">
			<label for="event-description">Additional Information:</label>
			<textarea name="event[description]" id="event-description" cols="20" rows="4"<?php if (is_required('event[description]')) echo ' class="required"'; ?>><?= value('event[description]'); ?></textarea>
			<p class="item-description">(Specific date exceptions or other important info.)</p>
		</div>
		
		<div class="submitnew-item">
			<label for="event-photo">Event Photo:</label>
			<input type="file" name="event[photo]" size="32" id="event-photo"<?php if (is_required('event[photo]')) echo ' class="required"'; ?> value="<?= value('event[description]'); ?>"/>
		</div>
		
		<div class="submitnew-item-submit">			
			<a href="javascript:;" onclick="$('.event-submitnew form').get(0).submit();">
				<img src="<?= LOCATION; ?>plugins/events/images/btn-event-submitnew.gif" alt="Submit New Event" />
			</a>
			or <a href="javascript:;" onclick="$('.event-submitnew-button').show(); $('.event-submitnew').hide();" class="cancel-changes">Cancel</a>			
		</div>
		
	</form>
	
</div>
-->