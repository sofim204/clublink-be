<style>
	body { font-family: Poppins; }

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
		display: flex;
		justify-content: center;
		align-items: center;
		margin: 20px 50px -25px 50px;
	}

	.info { margin: 0 15px; }

	.info p { 
		text-align: center;
		font-size: 14px;
	}

	.section {
		margin-left: 20px;
		font-weight: 800;
		margin-right: 20px;
	}

	.section h4 { 
		font-weight: 800;
		color: #FFFFFF;
		font-style: italic;
		display: inline-block;
		padding: 6px;
		border-radius: 10px;
		margin-top: -10px;
		font-size: 16px;
	}

	.text-bold { font-weight: bold; }

	.text-orange { color: orange; }

	.text-dark { color : #000000; }

	.text-white { color : #FFFFFF; }

	.text-italic { font-style: italic; }

	.background-orange { background-color: orange; }

	.background-purple { background-color: purple; }

	.background-default { background-color: #a6a2a2; }

	.text-center { text-align: center; }

	.text-justify { text-align: justify; }

	.text-left { text-align: left; }

	.new_user_section {
		display: flex;
		flex-direction: row;
		flex-wrap: wrap;
	}

	.day_section {
		display: flex;
		/* flex-direction: row; */
		justify-content: center;
		align-items: center;
		/* display: flex; */
		width: 270px;
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

	.guidance {
		margin-top: 5px;
		background-color: #FFFFFF;
		color: #000000;
		font-size: 12px;
		font-weight: 500;
		padding: 5px;
		display: inline-block;
		border-radius: 10px;
		font-weight: bold;
	}
</style>
<div id="page-content" style="background-color: #FFFFFF !important;">
    <div class="row text-dark">
		<div class="col-sm-12">
			<div class="tnc">
				<a href="<?php echo base_url_admin('community/event_newuser/eventdailymission/') . $language_code ?>" target="_blank">
					<i class="fa fa-info-circle" aria-hidden="true"></i><span style="margin-left: 3px;"> Guide</span>
				</a>
			</div>
			<div class="header">
				<img src="<?=base_url(); ?>media/daily_mission_en.png" class="img-responsive" />
			</div>
			<div class="section">
				<h4 class="background-orange">FOR NEW USERS (Rp. 10.000)</h4>
			</div>
			<div style="margin-right: 5px;">
				<ul>
					<li>Perform the daily missions as suggested (The order is important).</li>
					<li>Please perform these missions 3 days in a row.</li>
					<p style="font-size: 14px; font-weight: 500; margin-bottom: -12px;">(On <span class="text-bold">'Near'</span>)</p>
					<div class="new_user_section text-white">
						<div class="day_section background-orange" style="margin-right: 10px;">
							<div class="text-center text-bold day-content">Day 1</div>
							<div class="text-left">Post <span class="text-bold">photos</span> and descriptions with hashtags <span class="text-bold">#penggunabarusellon</span></div>
						</div>
						<div class="day_section background-orange" style="margin-right: 10px;">
							<div class="text-center text-bold day-content">Day 2</div>
							<div class="text-left">Post <span class="text-bold">videos</span> & descriptions with hashtags <span class="text-bold">#sellonmissions</span></div>
						</div>
						<div class="day_section background-orange">
							<div class="text-center text-bold day-content">Day 3</div>
							<div class="text-left">Post <span class="text-bold">photos</span> and descriptions with hashtags <span class="text-bold">#informasisekitar</span></div>
						</div>
					</div>
					<li>*After completing the three-day missions, Sellon's admin will contact you soon.</li>
				</ul>
			</div>
			<div class="section" style="margin-top: 10px;">
				<h4 class="background-purple">FOR OLD USERS (Rp. 20.000)</h4>
			</div>
			<div style="margin-right: 5px; margin-bottom: 30px;">
				<ul>
					<li>Perform the daily missions as suggested (The order is important).</li>
					<li>Please perform these missions 5 days in a row.</li>
					<p style="font-size: 14px; font-weight: 500; margin-bottom: -12px;">(On <span class="text-bold">'Near'</span>)</p>
					<div class="new_user_section text-white">
						<div class="day_section background-purple" style="margin-right: 10px;">
							<div class="text-center text-bold day-content">Day 1</div>
							<div class="text-left">Post <span class="text-bold">photos</span> and descriptions with hashtags <span class="text-bold">#penggunasetia</span></div>
						</div>
						<div class="day_section background-purple" style="margin-right: 10px;">
							<div class="text-center text-bold day-content">Day 2</div>
							<div class="text-left">Post <span class="text-bold">videos</span> & descriptions with hashtags <span class="text-bold">#dailymissions</span></div>
						</div>
						<div class="day_section background-purple" style="margin-right: 10px;">
							<div class="text-center text-bold day-content">Day 3</div>
							<div class="text-left">Post <span class="text-bold">photos</span> and descriptions with hashtags <span class="text-bold">#sellonkomunitas</span></div>
						</div>
						<div class="day_section background-purple text-white" style="margin-right: 10px;">
							<div class="d-flex" style="margin-left: -90px;">
								<div class="text-center text-bold" style="width: 84px; font-size: 20px;">Day 4</div>
								<div class="text-left" style="margin-top: 5px;"><span class="text-bold">Share</span> 3 posts*</span></div>
							</div>
						</div>
						<div class="day_section background-purple" style="width: 320px !important;">
							<div class="text-center text-bold day-content" style="width: 130px;">Day 5</div>
							<div class="text-left" style="margin-left: -4px;"><span class="text-bold">Invite</span> friends using referral code</span>
							<div class="guidance text-bold">My -> Today's Mission -> Invite</div>
							</div>
						</div>
					</div>
					<li>*After completing the five-day missions, Sellon's admin will contact you soon.</li>
				</ul>
			</div>
		</div>
	</div>
</div>