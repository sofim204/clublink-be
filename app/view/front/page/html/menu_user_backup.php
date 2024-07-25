
					
					<div class="block block-tags">
						<div class="block-content text-center">
							<img src="http://via.placeholder.com/128x128" style="width: 128px; height: 128px; border-radius: 50%;" />
						</div>
						<div class="block-title text-center">
							<?php echo $sess->user->fnama; ?>
							<p class="block-subtitle" style="font-weight: normal; text-transform: lowecase;margin:0;">FREE member</p>
						</div>
						
						<div class="block-content">
							<dl id="narrow-by-list">
								<dt class="even">Menu</dt>
								<dd class="even">
									<ol>
										<li> <a href="<?php echo base_url('account/profile'); ?>" title="Lihat halaman profil">Profil</a></li>
										<li> <a href="<?php echo base_url('account/upgrade'); ?>" title="Lihat halaman Upgrade">Upgrade</a></li>
										<li> <a href="<?php echo base_url('account/panduan'); ?>" title="Lihat halaman Panduan Membeli dan Berjualan">Panduan</a></li>
										<li> <a href="<?php echo base_url('account/tiket'); ?>" title="Lihat halaman Tiket Dukungan">Tiket</a> (0) </li>
										<li> <a href="<?php echo base_url('account/history_transaksi'); ?>" title="Lihat halaman Catatan Transaksi">Transaksi</a> (0) </li>
										<li> <a href="<?php echo base_url('account/deposit'); ?>" title="Lihat halaman Catatan Deposit">Deposit</a> (8) </li>
										<li> <a href="<?php echo base_url('account/poin'); ?>" title="Lihat halaman Catatan Poin">Poin</a> (5) </li>
										<li> <a href="<?php echo base_url('account/wishlist'); ?>" title="Lihat halaman Wishlist">Wishlist</a> (5) </li>
										<li> <a href="<?php echo base_url('account/address'); ?>" title="Kelola Buku Alamat">Buku Alamat</a> (5) </li>
										<li> <a href="<?php echo base_url('logout/'); ?>" title="Logout dari akun <?php echo $sess->user->fnama; ?>">Logout</a> (5) </li>
									</ol>
								</dd>
							</dl>
						</div>
					</div>
					