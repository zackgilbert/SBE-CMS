
	<div id="content-subnav">
		<h2 class="bands-title">Bands</h2>
		<ul class="bands">
			<?php foreach (get_bands() as $b) : ?> 
			<li<?= ($band->id === $b->id) ? ' class="selected"' : ''; ?>><a href="<?= $b->link(); ?>"><?= $b->name(); ?></a></li>
			<?php endforeach; ?> 
		</ul>
	</div>
	
	<div id="content-band">
		
		<img src="<?= $band->thumb(175, 105); ?>" alt="<?= $band->name(); ?>" class="band-logo"/>
		
		<h1 class="band-name"><?= $band->name(); ?></h1>
		
		<p class="band-bio"><?= $band->biography(); ?></p>	
		
		
		<div class="band-audio">
			<h3 class="audio-title">Audio Samples</h3>
			<?php if ($band->hasAudio()) : ?> 
				<?php foreach ($band->audio() as $audio) : ?> 
					<ol class="tracks">
						<li>
							<?php /*?><p class="audio-track-title"><?= valid($audio['title']); ?></p><?php */?>
							<p class="audio-track-description">
								<?= valid($audio['description']); ?> 
							</p>
							<div id="audio-<?= $audio['id']; ?>" class="audio-track">
								<div id="audio-<?= $audio['id']; ?>-player">
									<script type="text/javascript">
										AudioPlayer.embed("audio-<?= $audio['id']; ?>-player", {  
											soundFile: "<?= $audio['location']; ?>",
											titles: "<?= $audio['description']; ?>",  
											artists: "<?= $audio['title']; ?>",  
											autostart: "no"  
										});  
									</script>						
								</div>
							</div>
						</li>							
					</ol>
				<?php endforeach; ?>
			<?php else : ?> 
				<p class="no-items">No Audio Tracks</p>
			<?php endif; ?> 
		</div>
		
		<div class="band-photo-column">
			<h3 class="content-type-title">Band Photos</h3>
			<?php if ($band->hasPhotos()) : ?> 
				<?php foreach ($band->photos() as $photo) : ?> 
					<div class="photozoom">
						<a href="<?= $photo['location']; ?>" rel="band-photos" title="<?= htmlentities2($photo['title']); ?>" onclick="return false;"><img src="<?= add_photo_info($photo['location'], 150, 150, '1:1'); ?>" alt="<?= $photo['title']; ?>"/></a>
						<h4 class="photo-title"><?= $photo['title']; ?></h4>
						<?php if ($photo['description']) : ?> 
						<p class="photo-description">
							<?= valid($photo['description']); ?> 
						</p>
						<?php endif; ?> 
					</div>
				<?php endforeach; ?> 
			<?php else:  ?>
				<p class="no-items">No Photos</p>
			<?php endif; ?> 
		</div>	
		 
		<div class="band-video-column">
			<h3 class="content-type-title">Band Videos</h3>
			<?php if ($band->hasVideos()) : ?>
				<?php foreach ($band->videos() as $video) : ?> 
					<div id="video-<?= $video['id']; ?>" class="video-item">
						<div class="video-item-embed">&nbsp;</div>
						<script type="text/javascript">
							$("#video-<?= $video['id']; ?> div:first").flashembed({
								src:'<?= valid(get_video_url($video["location"])); ?>',
								height:250,
								width:300,
								wmode:'opaque',
							});
						</script>
						<h4 class="video-title"><?= valid($video['title']); ?></h4>
						<?php if ($video['description']) : ?> 
						<p class="video-description">
							<?= valid($video['description']); ?> 
						</p>
						<?php endif; ?> 
					</div>
				<?php endforeach; ?> 
			<?php else:  ?>
				<p class="no-items">No Videos</p>
			<?php endif; ?> 
		</div>
		
		
	</div>
