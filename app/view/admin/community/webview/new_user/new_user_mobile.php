<style>
	html {
		scroll-behavior: smooth;
	}
	body { 
		font-family: Poppins;
		background-color: #FFFFFF;
	}

	.content {
		margin-bottom: 40px;
	}

	.content ul {
		margin: 0 12px;
	}

	.content ul li i {
		font-size: 9px;
	}

	.content ul li i .fa .fa-circle::before {
		margin-top: -20px;
	}

	.tnc {
		background-color: #a6a2a2;
		border-radius: 10px;
		padding: 6px;
		margin-top: 20px;
		margin-right: 20px;
		position: absolute; 
		right: 0;
	}

	.tnc a {
		text-decoration: none;
		color: #FFFFFF;
	}	

	.header {
		margin: 0 16px;
		height: 340px;
	}

	.header img {
		max-width: 400px;
		width: 100%;
		/* center */
		display: block;
		margin: 0 auto;
	}

	.info { margin: 0 15px; }

	.info p { 
		text-align: center;
		font-size: 14px;
	}

	.section {
		margin: 0 20px;
		font-weight: 800;
	}

	.section h4 { 
		font-weight: 800;
		color: #FFFFFF;
		font-style: italic;
		display: inline-block;
		padding: 6px;
		border-radius: 5px;
		margin-top: -10px;
		font-size: 16px;
		width: 310px;
	}

	.text-bold { font-weight: bold; }

	.text-orange { color: orange; }

	.text-dark { color : #000000; }

	.text-white { color : #FFFFFF; }

	.text-italic { font-style: italic; }

	.background-orange { background-color: orange; }

	.background-default { background-color: #a6a2a2; }

	.text-center { text-align: center; }

	.text-justify { text-align: justify; }

	.text-left { text-align: left; }

	.day-section {
		display: flex;
		flex-direction: row;
		justify-content: center;
		align-items: center;
		/* display: flex; */
		width: 300px;
		border-radius: 10px;
		padding: 10px;
		margin-top: 10px;
		margin-bottom: 10px;
	}

	.day-content {
		margin: 0 12px 0 0;
		width: 160px;
		font-size: 20px;
	}

	.container {
		max-width: 800px;
		margin: 0 auto;
		padding: 0 14px;
	}

	.content-container {
		max-width: 800px;
		margin: 0 auto;
		padding: 0 14px;
		display: flex;
		/* justify-content: center; */
		flex-direction: column;
		align-items: flex-start;
	}

	@media only screen and (max-width: 400px) {
		.header {
			height: 300px;
		}
	}

	@media only screen and (max-width: 300px) {
		.header {
			height: 250px;
		}
	}

</style>
<div id="page-content text-dark" style="background-color: #FFFFFF !important;">
    <div class="row">
		<?php if($language_code == "id") { ?>

			<div class="container">
				<div class="tnc">
					<a href="<?php echo base_url_admin('community/event_newuser/eventdailymission/id') ?>" target="_blank">
						<i class="fa fa-info-circle" aria-hidden="true"></i><span style="margin-left: 3px;"> Petunjuk</span>
					</a>
				</div>
				<div class="header">
					<img src="<?=base_url(); ?>media/daily_mission_id.png" class="" />
				</div>
			</div>

			<?php if($list_day_newuser === 0) { ?>
				<div class="content-container">
					<div class="section">
						<h4 class="background-orange">UNTUK PENGGUNA BARU (Rp. 10.000)</h4>
					</div>
					<div class="content">
						<ul>
							<li>Selesaikan misi harian (penting untuk mengikuti urutan).</li>
							<li>Selesaikan misi 3 hari ini secara berurutan.</li>
							<p style="font-size: 14px; font-weight: 500; margin-bottom: -12px;">(Di <span class="text-bold">'Sekitar')</span></p>
							<div class="new-user-section">
								<div class="day-section background-orange text-white" style="margin-right: 10px;">
									<div class="text-center text-bold day-content">Day 1</div>
									<div class="text-left">Posting <span class="text-bold">foto</span> dan deskripsi dengan hashtag <span class="text-bold">#penggunabarusellon</span></div>
								</div>
								<div class="day-section background-orange text-white" style="margin-right: 10px;">
									<div class="text-center text-bold day-content">Day 2</div>
									<div class="text-left">Posting <span class="text-bold">video</span> & deskripsi dengan hashtag <span class="text-bold">#sellonmissions</span></div>
								</div>
								<div class="day-section background-orange text-white">
									<div class="text-center text-bold day-content">Day 3</div>
									<div class="text-left">Posting <span class="text-bold">foto</span> dan deskripsi dengan hashtag <span class="text-bold">#informasisekitar</span></div>
								</div>
							</div>
							<li>*Setelah menyelesaikan misi tiga hari, admin SellOn akan segera menghubungi Anda.</li>
						</ul>
					</div>
				</div>
			<?php } else { ?>
				<div class="content-container">
					<div class="section">
						<h4 class="background-orange">UNTUK PENGGUNA BARU (Rp. 10.000)</h4>
					</div>
					<div class="content">
						<ul>
							<li>Selesaikan misi harian (penting untuk mengikuti urutan).</li>
							<li>Selesaikan misi 3 hari ini secara berurutan.</li>
							<p style="font-size: 14px; font-weight: 500; margin-bottom: -12px;">(Di <span class="text-bold">'Sekitar')</span></p>
							<div class="new-user-section">
								<?php foreach ($list_day_newuser as $day_newuser) { ?>
									<div class="day-section <?= $day_newuser->day_1 != NULL ? 'background-default' : 'background-orange' ?> text-white" style="margin-right: 10px;">
										<div class="text-center text-bold day-content">Day 1</div>
										<div class="text-left">Posting <span class="text-bold">foto</span> dan deskripsi dengan hashtag <span class="text-bold">#penggunabarusellon</span></div>
									</div>
									<div class="day-section  <?=$day_newuser->day_2 != NULL ? 'background-default' : 'background-orange' ?> text-white" style="margin-right: 10px;">
										<div class="text-center text-bold day-content">Day 2</div>
										<div class="text-left">Posting <span class="text-bold">video</span> & deskripsi dengan hashtag <span class="text-bold">#sellonmissions</span></div>
									</div>
									<div class="day-section <?=$day_newuser->day_3 != NULL ? 'background-default' : 'background-orange' ?> text-white">
										<div class="text-center text-bold day-content">Day 3</div>
										<div class="text-left">Posting <span class="text-bold">foto</span> dan deskripsi dengan hashtag <span class="text-bold">#informasisekitar</span></div>
									</div>
								<?php } ?>
							</div>
							<li>*Setelah menyelesaikan misi tiga hari, admin SellOn akan segera menghubungi Anda.</li>
						</ul>
					</div>
				</div>
			<?php } ?>

		<?php } else if($language_code == "en") { ?>

			<div class="container">
				<div class="tnc">
					<a href="<?php echo base_url_admin('community/event_newuser/eventdailymission/') . $language_code ?>" target="_blank">
						<i class="fa fa-info-circle" aria-hidden="true"></i><span style="margin-left: 3px;"> Guide</span>
					</a>
				</div>
				<div class="header">
					<img src="<?=base_url(); ?>media/daily_mission_en.png" class="" />
				</div>
			</div>

			<?php if($list_day_newuser === 0) { ?>
				<div class="content-container">
					<div class="section">
						<h4 class="background-orange">FOR NEW USERS (Rp. 10.000)</h4>
					</div>
					<div class="content">
						<ul>
							<li>Perform the daily missions as suggested (The order is important).</li>
							<li>Please perform these missions 3 days in a row.</li>
							<p style="font-size: 14px; font-weight: 500; margin-bottom: -12px;">(On <span class="text-bold">'Near')</span></p>
							<div class="new-user-section">
								<div class="day-section background-orange text-white" style="margin-right: 10px;">
									<div class="text-center text-bold day-content">Day 1</div>
									<div class="text-left">Post <span class="text-bold">photos</span> and descriptions with hashtags <span class="text-bold">#penggunabarusellon</span></div>
								</div>
								<div class="day-section background-orange text-white" style="margin-right: 10px;">
									<div class="text-center text-bold day-content">Day 2</div>
									<div class="text-left">Post <span class="text-bold">videos</span> & descriptions with hashtags <span class="text-bold">#sellonmissions</span></div>
								</div>
								<div class="day-section background-orange text-white">
									<div class="text-center text-bold day-content">Day 3</div>
									<div class="text-left">Post <span class="text-bold">photos</span> and descriptions with hashtags <span class="text-bold">#informasisekitar</span></div>
								</div>
							</div>
							<li>*After completing the three-day missions, Sellon's admin will contact you soon.</li>
						</ul>
					</div>
				</div>
			<?php } else { ?>
				<div class="content-container">
					<div class="section">
						<h4 class="background-orange">FOR NEW USERS (Rp. 10.000)</h4>
					</div>
					<div class="content">
						<ul>
							<li>Perform the daily missions as suggested (The order is important).</li>
							<li>Please perform these missions 3 days in a row.</li>
							<p style="font-size: 14px; font-weight: 500; margin-bottom: -12px;">(On <span class="text-bold">'Near')</span></p>
							<div class="new-user-section">
								<?php foreach ($list_day_newuser as $day_newuser) { ?>
									<div class="day-section <?= $day_newuser->day_1 != NULL ? 'background-default' : 'background-orange' ?> text-white" style="margin-right: 10px;">
										<div class="text-center text-bold day-content">Day 1</div>
										<div class="text-left">Post <span class="text-bold">photos</span> and descriptions with hashtags <span class="text-bold">#penggunabarusellon</span></div>
									</div>
									<div class="day-section  <?=$day_newuser->day_2 != NULL ? 'background-default' : 'background-orange' ?> text-white" style="margin-right: 10px;">
										<div class="text-center text-bold day-content">Day 2</div>
										<div class="text-left">Post <span class="text-bold">video</span> videos</span> & descriptions with hashtags <span class="text-bold">#sellonmissions</span></div>
									</div>
									<div class="day-section <?=$day_newuser->day_3 != NULL ? 'background-default' : 'background-orange' ?> text-white">
										<div class="text-center text-bold day-content">Day 3</div>
										<div class="text-left">Post <span class="text-bold">photos</span> and descriptions with hashtags <span class="text-bold">#informasisekitar</span></div>
									</div>
								<?php } ?>
							</div>
							<li>*After completing the three-day missions, Sellon's admin will contact you soon.</li>
						</ul>
					</div>
				</div>
			<?php } ?>
		
		<?php } ?>
	</div>
</div>