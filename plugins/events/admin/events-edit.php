	
	<div id="edit-header">
		
		<p class="section-path"><a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/">Back to All Events</a></p>
		
		<?php if (is_numeric($event->id)) : ?> 
		<h2 class="edit-title">Edit "<?= value('event[name]', $event->name()); ?>"</h2>
		<?php else : ?> 
		<h2 class="edit-title">Publish A New Event</h2>
		<?php endif; ?> 
		
		<?php if ($event->wasFound() && !is_string($event->deleted_at)) : ?> 	
			<div class="edit-delete">
				<a href="<?= LOCATION; ?>plugins/events/admin/events-delete/?id=<?= $event->id; ?>" onclick="return confirm('Are you sure you want to delete this event?');" title="Delete This Event" id="delete" class="delete-button" >
					<img src="<?= LOCATION; ?>admin/images/btn-delete.gif" alt="Delete This Event" />
				</a>
			</div>
		<?php endif; ?>
	
	</div>	

	<?php if (is_string($event->deleted_at)) : ?> 
			
		<div class="deleted-content">
			
			<p>This Event Has Been Deleted!</p>
			
		</div>
		
	<?php endif; ?> 
	
	<div class="event-edit">
	
	
		<form method="post" action="<?= LOCATION; ?>plugins/events/admin/events-save/" enctype="multipart/form-data">
			<div>
				<input type="hidden" name="required" value="event[category],event[name]"/>
				<input type="hidden" name="redirect[success]" value="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/events/<?= (isset($_GET['list'])) ? 'list/' : ''; ?>" />
				<input type="hidden" name="redirect[failure]" value="<?= $_SERVER['REQUEST_URI']; ?>" />
				<input type="hidden" name="event[sitemap_id]" value="<?= get_var('id'); ?>"/>
				<?php if ($event->id > 0) :	?> 
				<input type="hidden" name="event[id]" value="<?= $event->id; ?>"/>
				<?php endif; ?> 
			</div>

			<?php /*?><div class="event-edit-item">
				<label for="event-category">Event Category:</label>
				<select name="event[category]" id="event-category"<?php if (is_required('event[category]')) echo ' class="required"'; ?>>
					<option value="">Select Category</option>
					<?php foreach (get_event_categories($event->section()) as $category) : ?> 
					<option value="<?= $category['id']; ?>"<?= (value('event[category]', $event->event_category_id) == $category['id']) ? ' selected="selected"' : ''; ?>><?= valid($category['name']); ?></option>
					<?php endforeach; ?> 
				</select>
			</div>*/?>

			<div class="event-edit-item">
				<label for="event-name">Event Name:</label>
				<input type="text" name="event[name]" id="event-name" class="field-medium<?php if (is_required('event[name]')) echo ' required'; ?>" value="<?= value('event[name]', $event->name()); ?>"/>
			</div>

			<div class="event-edit-item">
				<label for="event-location" id="event-location-label">Event Location:</label>
				<span id="event-location-container">
					<select name="event[location]" id="event-location" class="venues<?php if (is_required('event[location]')) echo ' required'; ?>" onchange="checkLocation(this);">
						<option value="">Select Venue</option>
						<?php foreach (get_event_locations() as $venue) : ?> 
						<option value="<?= $venue['id']; ?>"<?= (value('event[location]', $event->location) == $venue['id']) ? ' selected="selected"' : ''; ?>><?= valid($venue['name']); ?></option>
						<?php endforeach; ?> 
						<option value="--">-- Add New Venue --</option>
					</select> or <a href="javascript:;" onclick="changeLocation(this, '<?= $event->location; ?>');" class="location-choose">Use a Custom Address</a>
				</span>
				<span id="event-location-new" style="display: none;">
 					
					<span class="event-venue-container">
						<label for="event-venue-name">Venue Name:</label>
						<input type="text" name="event[venue][name]" id="event-venue-name" class="field-medium<?php if (is_required('event[venue][name]')) echo ' required'; ?>" value="<?= value('event[venue][name]'); ?>"/> or <a href="javascript:;" onclick="cancelChangeLocation(this);" class="location-choose">Cancel</a>
					</span>
					
					<span class="event-venue-container">
						<label for="event-venue-address">Venue Address:</label>
						<textarea name="event[venue][address]" id="event-venue-address" cols="20" rows="2"<?php if (is_required('event[venue][address]')) echo ' class="required"'; ?>><?= value('event[venue][description]'); ?></textarea>
					</span>
					
					<span class="event-venue-container">
						<label for="event-venue-phone">Venue Phone #:</label>
						<input type="text" name="event[venue][phone]" id="event-venue-phone" class="field-medium<?php if (is_required('event[venue][phone]')) echo ' required'; ?>" value="<?= value('event[venue][phone]'); ?>"/>
 					</span>

					<span class="event-venue-container">
						<label for="event-venue-website">Venue Website:</label>
						<input type="text" name="event[venue][website_url]" id="event-venue-website" class="field-medium<?php if (is_required('event[venue][website_url]')) echo ' required'; ?>" value="<?= value('event[venue][website_url]'); ?>"/>
 					</span>
				</span>
				<span id="event-location-custom" style="display: none;">
					<input type="text" name="event[location]" class="field-medium" value="<?php if (($loc = value('event[location]', $event->location)) && !is_numeric($loc)) echo $loc; ?>" disabled="disabled"/> or <a href="javascript:;" onclick="cancelChangeLocation(this);" class="location-choose">Select a Venue</a>
				</span>
			</div>

			<div class="event-edit-item">

				<label>Event Date(s):</label>
				<div class="item-eventtime">

					<div>

						This event is 
						<!--<select name="event[date][occurrence]" id="event-date-occurrence" onchange="changeOccurrence(this);">
							<option value="once">occurs once</option>
							<option value="repeats"<?= (is_string($event->repeats_every)) ? ' selected="selected"': ''; ?>>repeats</option>
						</select>-->
						<input type="hidden" name="event[date][occurrence]" value="once"/>

						<span class="occurrence once">	
							<select onchange="(this.value == 'once') ? $('.once .multiple').hide().find('input, select').attr('disabled','disabled') : $('.once .multiple').show().find('input, select').removeAttr('disabled');">
								<option value="once">on</option>
								<option value="multiple">from</option>
							</select>						
							<span class="date-container"><input type="text" name="event[date][once][start_date]" value="<?= value('event[date][once][start_date]', ((is_string($event->start_date)) ? $event->start_date : TODAY)); ?>" class="datepicker field-date" /></span>						
							<span class="multiple">
								to <span class="date-container"><input type="text" name="event[date][once][end_date]" value="<?= value('event[date][once][end_date]', ((is_string($event->end_date)) ? $event->end_date : TODAY)); ?>" class="datepicker field-date" disabled="disabled" /></span>
							</span>							
						</span>

						<span class="occurrence repeats">
							every
							<select name="event[date][repeats][every]">
								<?php for ($i=1; $i<=30; $i++) : ?> 
								<option value="<?= $i; ?>"><?= $i; ?></option>
								<?php endfor; ?> 
							</select>
							<select name="event[date][repeats][frequency]" onchange="changeFrequency(this.value);">
								<option value="days">days</option>
								<option value="weeks">weeks</option>
								<option value="months">months</option>
								<option value="years">years</option>
							</select>
							
							<span class="frequency days"></span>

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
										<option value="day">day</option>
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
										<option value="day">day</option>
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
										<option value="day">day</option>
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
										<option value="day">day</option>
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
										<option value="day">day</option>
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
										<option value="day">day</option>
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

								starting on <span class="date-container"><input type="text" name="event[date][repeats][start_date]" value="<?= value('event[date][repeats][start_date]', ((is_string($event->start_date)) ? $event->start_date : TODAY)); ?>" class="datepicker field-date" /></span>
								and	
								<select onchange="(this.value == 'on') ? $('.ending').show().find('input').removeAttr('disabled') : $('.ending').hide().find('input').attr('disabled', 'disabled');">
									<option value="never">never ends</option>
									<option value="on">ends on</option>
								</select>						
								<span class="ending">
								 	<span class="date-container"><input type="text" name="event[date][repeats][ends_on]" value="<?= value('event[date][repeats][ends_on]', ((is_string($event->ends_on)) ? $event->ends_on : TODAY)); ?>" class="datepicker field-date" /></span>
								</span>

							</span>

						</span>

					</div>

				</div>

			</div>

			<div class="event-edit-item">
				<label for="event-hours">Event Hours:</label>
				<textarea name="event[hours]" id="event-hours" cols="20" rows="4"<?php if (is_required('event[hours]')) echo ' class="required"'; ?>><?= value('event[hours]', $event->hours); ?></textarea>
			</div>

			<div class="event-edit-item">
				<label for="event-price">Event Price:</label>
				<input type="text" name="event[price]" id="event-price" class="field-medium<?php if (is_required('event[price]')) echo ' required'; ?>" value="<?= value('event[price]', $event->price); ?>"/>
			</div>

			<div class="event-edit-item">
				<label for="event-age">Event Age:</label>
				<input type="text" name="event[age]" id="event-age" class="field-medium<?php if (is_required('event[age]')) echo ' required'; ?>" value="<?= value('event[age]', $event->age); ?>"/>
			</div>

			<div class="event-edit-item">
				<label for="event-contact">Contact Info:</label>
				<input type="text" name="event[contact]" id="event-contact" class="field-medium<?php if (is_required('event[contact]')) echo ' required'; ?>" value="<?= value('event[contact]', $event->contact); ?>"/>
				<p class="item-description">(Email or Phone #)</p>
			</div>

			<div class="event-edit-item">
				<label for="event-description">Additional Information:</label>
				<textarea name="event[description]" id="event-description" cols="20" rows="4"<?php if (is_required('event[description]')) echo ' class="required"'; ?>><?= value('event[description]', $event->description); ?></textarea>
				<p class="item-description">(Specific date exceptions or other important info.)</p>
			</div>

			<div class="event-edit-item">
				<label for="event-photo">Event Photo:</label>
				<?php if ($event->hasPhoto()) : ?> 
				<div>
					<img src="<?= $event->photo(100,100); ?>" alt="event photo"/> 
					<div><a href="javascript:;" onclick="deleteFile(this, 'uploads/events/<?= $event->id; ?>', 'event:<?= $event->id; ?>');">delete</a></div>
				</div>
				<input type="file" name="event[photo]" size="32" id="event-photo" class="file-upload" style="display:none;"/>
				<?php else : ?> 
				<input type="file" name="event[photo]" size="32" id="event-photo"<?php if (is_required('event[photo]')) echo ' class="required"'; ?> value="<?= value('event[description]'); ?>"/>
				<?php endif; ?> 
			</div>
			
			<?php if (is_string($event->published_at)) : ?> 
			<div class="event-edit-item">

				<label for="published_at">Published At:</label>
				<input type="text" id="published_at" name="events[published_at]" value="<?= $event->published_at; ?>" class="field-medium"/>

			</div>
			<?php endif; ?> 

			<?php if (is_string($event->deleted_at)) : ?> 
			<div class="event-edit-item">

				<label for="delete_at">Deleted At:</label>
				<input type="text" id="delete_at" name="events[delete_at]" value="<?= $event->deleted_at; ?>" class="field-medium"/>

			</div>
			<?php endif; ?>

			<div class="event-edit-item">

				<label for="comment_status">Comments:</label>
				<select id="comment_status" name="events[comment_status]" class="status">
				<?php foreach ($event->_comment_status_options as $key => $value) : ?> 
					<option value="<?= $key; ?>"<?= ($key == $event->comment_status) ? ' selected="selected"': ''; ?>><?= $value; ?></option>
				<?php endforeach; ?> 
				</select>		

			</div>

			<div class="edit-save">
				<?php if (!is_string($event->published_at)) : ?> 
				<input type="submit" name="publish-continue" value="Publish and Continue Editing" class="btn-submit" /> 
				<input type="submit" name="publish" value="Publish" class="btn-submit" /> 
				<?php endif; ?> 
				<input type="submit" name="continue" value="Save and Continue Editing" class="btn-submit"/> 
				<input type="submit" name="save" id="submit" value="Save" class="btn-submit" /> 
				or <a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/" class="cancel">Cancel</a>
			</div>

		</form>
	
	</div>
	
	<?php if ($event->wasFound() && !is_string($event->deleted_at)) : ?> 
	<div id="live-preview-container" style="margin-top: 30px;">
		
		<a href="javascript:;" onclick="$('#live-preview').toggle();">Toggle Live Preview</a>

		<object id="live-preview" type="text/html" data="<?= $event->link(); ?>" style="height: 400px; width: 100%; display: none; margin-top: 10px">
			<a href="<?= $event->link(); ?>">View actual event page.</a>	
		</object>
		
	</div>
	<?php endif; ?> 
	
	<script type="text/javascript" src="<?= LOCATION; ?>plugins/events/javascripts/jquery.datePicker.js"></script>
	<script type="text/javascript" src="<?= LOCATION; ?>plugins/events/javascripts/autosuggest.js"></script>
	<script type="text/javascript">
		
		function changeOccurrence(sel) {
			(sel.value == 'once') ? initOnce() : initRepeats();
		} // chanceOccurance

		function changeFrequency(val) {
			$('.' + val).show().find('input, select').removeAttr('disabled');
			$('.frequency:not(.' + val + '), .multiple, .ending').hide().find('input, select').attr('disabled','disabled'); 
		} // changeFrequency

		function initOnce() {
			$('.once').show().find('input, select').removeAttr('disabled');
			$('.repeats, .weeks, .months, .years, .multiple, .ending').hide().find('input, select').attr('disabled','disabled');
			$('.once').find('select').each(function(){ this.selectedIndex = 0; });
		} // initOnce

		function initRepeats() {
			$('.repeats, .days').show().find('input, select').removeAttr('disabled');
			$('.once, .weeks, .months, .years, .multiple, .ending').hide().find('input, select').attr('disabled','disabled');
			$('.repeats').find('select').each(function(){ this.selectedIndex = 0; });
			$('.eventtime-row .ending').show().find('select, input').removeAttr('disabled');
			$('.eventtime-row:last select').get(0).selectedIndex = 1;
		} // initRepeats
		
		function checkLocation(link, force) {
			if ((link.value == '--') || (force)) {
				$('#event-location-container').hide().find('select').attr('disabled', 'disabled');
				$('#event-location-new').show().find('input,select,textarea').val('').removeAttr('disabled');			
				$('#event-location-custom').hide().find('input').attr('value', '').attr('disabled', 'disabled');
				$('#event-location-label').hide();
				link.selectedIndex = 0;
			}
		} // checkLocation

		function changeLocation(link) {
			$('#event-location-container').hide().find('select').attr('disabled', 'disabled');
			$('#event-location-new').hide().find('input,select,textarea').val('').attr('disabled', 'disabled');			
			$('#event-location-custom').show().find('input').attr('value', '').removeAttr('disabled');
			$('#event-location-label').show();
		} // changeLocation

		function cancelChangeLocation(link) {
			$('#event-location-container').show().find('select').removeAttr('disabled');
			$('#event-location-new').hide().find('input,select,textarea').val('').attr('disabled', 'disabled');			
			$('#event-location-custom').hide().find('input').attr('value', '').attr('disabled', 'disabled');
			$('#event-location-label').show();
		} // cancelChangeLocation
		
		$(document).ready(function() {
			<?php if (is_string($event->location) && !empty($event->location) && !is_numeric($event->location)) : ?> 
				changeLocation($('#event-location-container').find('a').get(0));
			<?php elseif (value('event[venue][name]', '') != '') : ?> 
				checkLocation($('#event-location-container').find('select').get(0), true);
			<?php endif; ?> 
			<?php if (is_string($event->repeats_every) && !empty($event->repeats_every)) : ?> 
				initRepeats();
				$('.repeats select:eq(0)').find('option[value=<?= $event->repeat(0); ?>]').attr('selected', 'selected');
				$('.repeats select:eq(1)').find('option[value=<?= $event->repeat(1); ?>]').attr('selected', 'selected');
				changeFrequency('<?= $event->repeat(1); ?>');
				<?php if (strpos($event->repeat(2), ',') !== false) : list($date1, $date2) = trim_explode(',', $event->repeat(2)); $date2 = trim_explode(':', $date2); ?> 
					$('.repeats .<?= $event->repeat(1); ?> select:eq(0)').find('option[value=multiple]').attr('selected', 'selected'); 
					$('.repeats .<?= $event->repeat(1); ?> .multiple').show().find('select, input').removeAttr('disabled');
					$('.repeats .<?= $event->repeat(1); ?> .additional').hide().find('select, input').attr('disabled', 'disabled');
					<?php if ($event->repeat(1) == 'weeks') : ?> 
						$('.repeats .<?= $event->repeat(1); ?> .multiple select:eq(0)').find('option[value=<?= $date2[0]; ?>]').attr('selected', 'selected');
					<?php elseif ($event->repeat(1) == 'months') : ?> 
						$('.repeats .<?= $event->repeat(1); ?> .multiple select:eq(0)').find('option[value=<?= $date2[0]; ?>]').attr('selected', 'selected');
						$('.repeats .<?= $event->repeat(1); ?> .multiple select:eq(1)').find('option[value=<?= $date2[1]; ?>]').attr('selected', 'selected');			
					<?php elseif ($event->repeat(1) == 'years') : ?> 
						$('.repeats .<?= $event->repeat(1); ?> .multiple select:eq(0)').find('option[value=<?= $date2[1]; ?>]').attr('selected', 'selected');
						$('.repeats .<?= $event->repeat(1); ?> .multiple select:eq(1)').find('option[value=<?= $date2[2]; ?>]').attr('selected', 'selected');
						$('.repeats .<?= $event->repeat(1); ?> .multiple select:eq(2)').find('option[value=<?= $date2[0]; ?>]').attr('selected', 'selected');
					<?php endif; ?>
				<?php elseif (strpos($event->repeat(2), '+') !== false) : list($date1, $date2) = trim_explode('+', $event->repeat(2)); $date2 = trim_explode(':', $date2); ?> 
					<?php if ($event->repeat(1) == 'weeks') : ?> 
						$('.repeats .<?= $event->repeat(1); ?> .additional select:eq(0)').find('option[value=<?= $date2[0]; ?>]').attr('selected', 'selected');
					<?php elseif ($event->repeat(1) == 'months') : ?> 
						$('.repeats .<?= $event->repeat(1); ?> .additional select:eq(0)').find('option[value=<?= $date2[0]; ?>]').attr('selected', 'selected');
						$('.repeats .<?= $event->repeat(1); ?> .additional select:eq(1)').find('option[value=<?= $date2[1]; ?>]').attr('selected', 'selected');			
					<?php elseif ($event->repeat(1) == 'years') : ?> 
						$('.repeats .<?= $event->repeat(1); ?> .additional select:eq(0)').find('option[value=<?= $date2[1]; ?>]').attr('selected', 'selected');
						$('.repeats .<?= $event->repeat(1); ?> .additional select:eq(1)').find('option[value=<?= $date2[2]; ?>]').attr('selected', 'selected');
						$('.repeats .<?= $event->repeat(1); ?> .additional select:eq(2)').find('option[value=<?= $date2[0]; ?>]').attr('selected', 'selected');
					<?php endif; ?>
				<?php else : $date1 = $event->repeat(2); endif; $date1 = trim_explode(":", $date1); ?> 
				<?php if ($event->repeat(1) == 'weeks') : ?> 
					$('.repeats .<?= $event->repeat(1); ?> select:eq(1)').find('option[value=<?= $date1[0]; ?>]').attr('selected', 'selected');
				<?php elseif ($event->repeat(1) == 'months') : ?> 
					$('.repeats .<?= $event->repeat(1); ?> select:eq(1)').find('option[value=<?= $date1[0]; ?>]').attr('selected', 'selected');
					$('.repeats .<?= $event->repeat(1); ?> select:eq(2)').find('option[value=<?= $date1[1]; ?>]').attr('selected', 'selected');			
				<?php elseif ($event->repeat(1) == 'years') : ?> 
					$('.repeats .<?= $event->repeat(1); ?> select:eq(1)').find('option[value=<?= $date1[1]; ?>]').attr('selected', 'selected');
					$('.repeats .<?= $event->repeat(1); ?> select:eq(2)').find('option[value=<?= $date1[2]; ?>]').attr('selected', 'selected');
					$('.repeats .<?= $event->repeat(1); ?> select:eq(3)').find('option[value=<?= $date1[0]; ?>]').attr('selected', 'selected');
				<?php endif; ?> 
				<?php if (is_string($event->ends_on)) : ?>
				$('select option[value=on]').attr('selected', 'selected').end().find('.ending').show().find('input').removeAttr('disabled');
				<?php endif; ?>
			<?php else: ?> 
				initOnce();
				<?php if (is_string($event->end_date)) : ?>
				$('.once select:first option[value=multiple]').attr('selected', 'selected').end().find('.multiple').show().find('input').removeAttr('disabled');
				<?php endif; ?>
			<?php endif; ?> 
		});
	</script>
	