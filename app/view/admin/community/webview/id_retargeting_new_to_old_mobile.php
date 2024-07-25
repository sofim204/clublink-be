<style>
	body { 
		font-family: Poppins; 
		background-color: #FFFFFF;
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
		display: flex;
		justify-content: center;
		align-items: center;
		margin: 30px 50px -40px 50px;
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
	.hide {
		display: none;
	}

	.show {
		display: block;
	}

</style>
<div id="page-content text-dark" style="background-color: #FFFFFF !important;">
    <div class="row">
		<div class="col-sm-12">
			<div class="tnc">
				<a href="<?php echo base_url_admin('community/event_newuser/eventdailymission/') . $language_code ?>" target="_blank">
					<i class="fa fa-info-circle" aria-hidden="true"></i><span style="margin-left: 3px;"> Petunjuk</span>
				</a>
			</div>
			<div class="header">
				<img src="<?=base_url(); ?>media/daily_mission_id.png" class="img-responsive" />
			</div>

			<?php if($list_day_newuser === 0) { ?>
				<div class="new_user">
					<div class="section">
						<h4 class="background-orange">UNTUK PENGGUNA BARU (Rp. 10.000)</h4>
					</div>
					<div style="margin-right: 5px;">
						<ul>
							<li>Selesaikan misi harian (penting untuk mengikuti urutan).</li>
							<li>Selesaikan misi 3 hari ini secara berurutan.</li>
							<p style="font-size: 14px; font-weight: 500; margin-bottom: -12px;">(Di <span class="text-bold">'Sekitar')</span></p>
								<div class="new_user_section">
									<div class="day_section background-orange text-white" style="margin-right: 10px; margin-bottom: -4px;">
										<div class="text-center text-bold day-content" style="width: 180px !important;">Day 1</div>
										<div class="text-left">Posting <span class="text-bold">foto</span> dan deskripsi dengan hashtag <span class="text-bold">#penggunabarusellon</span></div>
									</div>
									<div class="day_section background-orange text-white" style="margin-right: 10px; margin-bottom: -4px;">
										<div class="text-center text-bold day-content">Day 2</div>
										<div class="text-left">Posting <span class="text-bold">video</span> & deskripsi dengan hashtag <span class="text-bold">#sellonmissions</span></div>
									</div>
									<div class="day_section background-orange text-white">
										<div class="text-center text-bold day-content">Day 3</div>
										<div class="text-left">Posting <span class="text-bold">foto</span> dan deskripsi dengan hashtag <span class="text-bold">#informasisekitar</span></div>
									</div>
								</div>
							<li>*Setelah menyelesaikan misi tiga hari, admin SellOn akan segera menghubungi Anda.</li>
						</ul>
					</div>
				</div>
			<?php } else { ?>
				<div class="new_user">
					<div class="section">
						<h4 class="background-orange">UNTUK PENGGUNA BARU (Rp. 10.000)</h4>
					</div>
					<div style="margin-right: 5px;">
						<ul>
							<li>Selesaikan misi harian (penting untuk mengikuti urutan).</li>
							<li>Selesaikan misi 3 hari ini secara berurutan.</li>
							<p style="font-size: 14px; font-weight: 500; margin-bottom: -12px;">(Di <span class="text-bold">'Sekitar')</span></p>
								<div class="new_user_section">
									<?php foreach ($list_day_newuser as $day_newuser) { ?>
										<div class="day_section <?= $day_newuser->day_1 != NULL ? 'background-default' : 'background-orange' ?> text-white" style="margin-right: 10px; margin-bottom: -4px;">
											<div class="text-center text-bold day-content" style="width: 180px !important;">Day 1</div>
											<div class="text-left">Posting <span class="text-bold">foto</span> dan deskripsi dengan hashtag <span class="text-bold">#penggunabarusellon</span></div>
										</div>
										<div class="day_section <?=$day_newuser->day_2 != NULL ? 'background-default' : 'background-orange' ?> text-white" style="margin-right: 10px; margin-bottom: -4px;">
											<div class="text-center text-bold day-content">Day 2</div>
											<div class="text-left">Posting <span class="text-bold">video</span> & deskripsi dengan hashtag <span class="text-bold">#sellonmissions</span></div>
										</div>
										<div class="day_section <?=$day_newuser->day_3 != NULL ? 'background-default' : 'background-orange' ?> text-white">
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

			<?php if($list_day_retargeting === 0) { ?>
				<div class="old_user ">
					<div class="section">
						<h4 class="background-purple">UNTUK PENGGUNA LAMA (Rp. 20.000)</h4>
					</div>
					<div style="margin-right: 5px;">
						<ul>
							<li>Selesaikan misi harian (penting untuk mengikuti urutan).</li>
							<li>Selesaikan misi 5 hari ini secara berurutan.</li>
							<p style="font-size: 14px; font-weight: 500; margin-bottom: -12px;">(Di <span class="text-bold">'Sekitar')</span></p>
								<div class="day_section background-purple text-white" style="margin-right: 10px; margin-bottom: -4px;">
									<div class="text-center text-bold day-content" style="width: 170px !important;">Day 1</div>
									<div class="text-left">Posting <span class="text-bold">foto</span> dan deskripsi dengan hashtag <span class="text-bold">#penggunasetia</span></div>
								</div>
								<div class="day_section background-purple text-white" style="margin-right: 10px; margin-bottom: -4px;">
									<div class="text-center text-bold day-content">Day 2</div> 
									<div class="text-left">Posting <span class="text-bold">video</span> & deskripsi dengan hashtag <span class="text-bold">#dailymissions</span></div>
								</div>
								<div class="day_section background-purple text-white" style="margin-right: 10px; margin-bottom: -4px;">
									<div class="text-center text-bold day-content">Day 3</div>
									<div class="text-left">Posting <span class="text-bold">foto</span> dan deskripsi dengan hashtag <span class="text-bold">#sellonkomunitas</span></div>
								</div>
								<div class="day_section background-purple text-white" style="margin-right: 10px; margin-bottom: -4px;">
									<div class="d-flex" style="margin-left: -40px;">
										<div class="text-center text-bold" style="width: 90px; font-size: 20px;">Day 4</div>
										<div class="text-left" style="margin-top: 5px;"><span class="text-bold">Bagikan</span> 3 postingan*</span></div>
									</div>
								</div>
								<div class="day_section background-purple text-white" style="width: 300px !important;">
									<div class="text-center text-bold day-content" style=" width: 160px !important;">Day 5</div>
									<div class="text-left"><span class="text-bold">Undang</span> teman menggunakan kode referal</span>
										<div class="guidance text-bold">Saya -> Misi Harian -> Undang</div>
									</div>
								</div>
							<li>*Setelah menyelesaikan misi lima hari, admin SellOn akan segera menghubungi Anda.</li>
						</ul>
					</div>
				</div>
			<?php } else if ($list_day_retargeting === 1) { ?>

			<?php } else { ?>
				<div class="old_user ">
					<div class="section">
						<h4 class="background-purple">UNTUK PENGGUNA LAMA (Rp. 20.000)</h4>
					</div>
					<div style="margin-right: 5px;">
						<ul>
							<li>Selesaikan misi harian (penting untuk mengikuti urutan).</li>
							<li>Selesaikan misi 5 hari ini secara berurutan.</li>
							<p style="font-size: 14px; font-weight: 500; margin-bottom: -12px;">(Di <span class="text-bold">'Sekitar')</span></p>
								<div class="new_user_section">
									<?php foreach ($list_day_retargeting as $day_retargeting) { ?>
										<div class="day_section <?=$day_retargeting->day_1 != NULL ? 'background-default' : 'background-purple' ?> text-white" style="margin-right: 10px; margin-bottom: -4px;">
											<div class="text-center text-bold day-content" style="width: 170px !important;">Day 1</div>
											<div class="text-left">Posting <span class="text-bold">foto</span> dan deskripsi dengan hashtag <span class="text-bold">#penggunasetia</span></div>
										</div>
										<div class="day_section <?=$day_retargeting->day_2 != NULL ? 'background-default' : 'background-purple' ?> text-white" style="margin-right: 10px; margin-bottom: -4px;">
											<div class="text-center text-bold day-content">Day 2</div>
											<div class="text-left">Posting <span class="text-bold">video</span> & deskripsi dengan hashtag <span class="text-bold">#dailymissions</span></div>
										</div>
										<div class="day_section <?=$day_retargeting->day_3 != NULL ? 'background-default' : 'background-purple' ?> text-white" style="margin-right: 10px; margin-bottom: -4px;">
											<div class="text-center text-bold day-content">Day 3</div>
											<div class="text-left">Posting <span class="text-bold">foto</span> dan deskripsi dengan hashtag <span class="text-bold">#sellonkomunitas</span></div>
										</div>
										<div class="day_section <?=$day_retargeting->day_4 != NULL ? 'background-default' : 'background-purple' ?> text-white" style="margin-right: 10px; margin-bottom: -4px;">
											<div class="d-flex" style="margin-left: -40px;">
												<div class="text-center text-bold" style="width: 90px; font-size: 20px;">Day 4</div>
												<div class="text-left" style="margin-top: 5px;"><span class="text-bold">Bagikan</span> 3 postingan*</span></div>
											</div>
										</div>
										<div class="day_section <?=$day_retargeting->day_5 != NULL ? 'background-default' : 'background-purple' ?> text-white" style="width: 300px !important;">
											<div class="text-center text-bold day-content" style=" width: 160px !important;">Day 5</div>
											<div class="text-left"><span class="text-bold">Undang</span> teman menggunakan kode referal</span>
												<div class="guidance text-bold">Saya -> Misi Harian -> Undang</div>
											</div>
										</div>
									<?php } ?>
								</div>
							<li>*Setelah menyelesaikan misi lima hari, admin SellOn akan segera menghubungi Anda.</li>
						</ul>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>