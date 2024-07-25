<style>
	body { font-family: Poppins; }

	.header {
		display: flex;
		justify-content: center;
		align-items: center;
		text-decoration: underline;
		font-weight: 700;
		margin: 0 14px;
	}

	.header h2 {
		font-weight: bold;
		text-align: center;
	}

	.section {
		margin-left: 20px;
		font-weight: 800;
		font-size: 16px;
	}

	.section h4 { font-weight: 600; }

	.text-dark {
		color: #000000;
	}

	.footer p {
		font-style: italic;
		text-align: center;
	}
</style>

<div id="page-content" style="background-color: #FFFFFF !important;">
    <div class="row text-dark" style="margin-right: 3px;">
		<?php if($language_code == "id") { ?>
			<div class="col-sm-12">
				<div class="header">
					<h2>SYARAT DAN KETENTUAN EVENT DAILY MISSION</h2>
				</div>
				<div class="section">
					<h4>UNTUK PENGGUNA BARU: </h4>
				</div>
				<div class="content">
					<ol>
						<li>Setiap perangkat hanya dapat digunakan untuk satu akun.</li>
						<li>Pengguna baru termasuk pengguna baru yang di invite melalui kode referal selama periode event 6 November 2023 - 30 November 2023.</li>
						<li>Wajib verifikasi nomor telepon sebelum menjalankan misi pada akun anda karena pulsa akan dikirim ke nomor telepon yang terdaftar pada akun.</li>
						<li>Pastikan nomor telepon yang didaftarakan aktif & dapat di isi pulsa (bukan prabayar/cdma).</li>
						<li>1 nomor telepon berlaku untuk 1x pengisian pulsa. *nomor yang belum pernah digunakan di SellOn.</li>
						<li>Terdapat 3 misi harian yang berbeda yang harus diselesaikan setiap harinya.</li>
						<li>Misi harus diselesaikan secara berurutan setiap harinya. Misalnya, jika pada hari kedua Anda tidak menyelesaikan misi, maka pada hari berikutnya misi akan kembali dimulai dari hari pertama.</li>
						<li>Setelah menyelesaikan misi event, tunggu selama 1-2 hari (Senin - jumat disaat jam kerja) untuk proses verifikasi.</li>
						<li>Setelah proses verifikasi selesai, pulsa sebesar Rp10.000 akan dikirimkan ke nomor yang terdaftar pada akun Anda setelah proses verifikasi selesai.</li>
						<li>Tidak berlaku untuk akun yang terindikasi melakukan spam.</li>
					</ol>
				</div>
				<div class="footer" style="margin-bottom: 40px;">
					<a href="<?php echo base_url_admin('community/event_newuser/eventdailymissionfullguide/id') ?>" target="_blank">
						<span style="margin-left: 30px;"> Selengkapnya</span>
					</a>
					<p>Untuk semua pertanyaan mengenai Sellon<br />
						bisa langsung menghubungi melalui<br />
						WhatsApp ke No Customer Support : +65 8856 2024 <br /> & Email : <a href="mailto:support@sellon.net">support@sellon.net
					</p>
				</div>
			</div>
		<?php } else if($language_code == "en") { ?>
			<div class="col-sm-12">
				<div class="header">
					<h2>TERMS AND CONDITION EVENT DAILY MISSION</h2>
				</div>
				<div class="section">
					<h4>FOR NEW USER: </h4>
				</div>
				<div class="content">
					<ol>
						<li>Each device can only be used for one account.</li>
						<li>New users include new users invited via referral code during the event period 6 November 2023 - 30 November 2023.</li>
						<li>You must verify your telephone number before carrying out a mission on your account because credit will be sent to the telephone number registered on your account.</li>
						<li>Make sure the registered telephone number is active & can be topped up (not prepaid/cdma).</li>
						<li>1 Telephone number is valid for 1x top up. *number must never been used on SellOn.</li>
						<li>There are 3 different daily missions that must be completed every day.</li>
						<li>Missions must be completed sequentially every day. For example, if on the second day you do not complete the mission, then on the next day the mission will start again from the first day.</li>
						<li>After completing the event mission, wait 1-2 days (Monday-Friday during working hours) for the verification process.</li>
						<li>After the verification process is complete, credit of IDR 10,000 will be sent to the number registered in your account after the verification process is complete.</li>
						<li>Does not apply to accounts that are indicated to be spamming.</li>
					</ol>
				</div>
				<div class="footer" style="margin-bottom: 40px;">
					<a href="<?php echo base_url_admin('community/event_newuser/eventdailymissionfullguide/en') ?>" target="_blank">
						<span style="margin-left: 30px;"> More</span>
					</a>
					<p>For all questions regarding Sellon<br />
						can contact directly via<br />
						WhatsApp to No Customer Support : +65 8856 2024 <br /> & Email : <a href="mailto:support@sellon.net">support@sellon.net
					</p>
				</div>
			</div>
		<?php } ?>
	</div>
</div>