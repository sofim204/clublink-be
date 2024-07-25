<div id="jtv-mobile-menu" style="background-color: #ffffff;position:fixed;">
	<?php if($this->user_login){ ?>
		<div class="row" style="padding: 1em;background-color: #f5f5f5;">
			<div class="col-xs-4">
				<a href="<?=base_url('account/dashboard/')?>">
					<img src="<?=base_url('media/user/default.png')?>" class="img-responsive" />
				</a>
			</div>
			<div class="col-xs-8">
				<p style="font-size: 15px;">
					<a href="<?=base_url('account/dashboard/')?>">
						<strong><?php echo $sess->user->fnama; ?></strong><br />
						<span style=" font-size:13px; color: #a0a0a0;">Member</span>
					</a>
				</p>
			</div>
		</div>
	<?php }else{ ?>
		<div class="row" style="padding: 1em;background-color: #f5f5f5;">
			<div class="col-xs-4">
				<a href="<?=base_url('login/')?>">
					<img src="<?=base_url('media/user/login.png')?>" class="img-responsive" />
				</a>
			</div>
			<div class="col-xs-8">
				<p style="font-size: 15px;">
					<a href="<?=base_url('login/')?>">
						<strong>Login</strong><br />
						<span style=" font-size:13px; color: #a0a0a0;">Member</span>
					</a>
				</p>
			</div>
		</div>
	<?php } ?>
	<ul style="background-color: #f5f5f5;">
		<li class="">
			<a href="<?=base_url()?>" title="Homepage" class="">Home</a>
		</li>
		<?php if(isset($menu_mobile)){ foreach($menu_mobile as $m1){ ?>
			<?php $url = $m1->url; if($m1->url_type == 'internal') $url = base_url($m1->url); ?>
			<?php $m1_child = count($m1->childs); ?>
			<?php //if($m1_child>0 || $m1->url == '#') $url = '#'; ?>
			<?php if($m1_child>0){ ?>
				<li class="">
					<a href="<?=$url?>" title="<?=$m1->nama?>" class="">
						<i class="<?=$m1->icon_class?>"></i>
						<?=$m1->nama?>
					</a>
					<ul class="">
						<?php foreach($m1->childs as $m2){ ?>
							<?php $url = $m2->url; if($m2->url_type == 'internal') $url = base_url($m2->url); ?>
							<?php if($m2->url == '#') $url = '#'; ?>
							<?php if(count($m2->childs)>0){ ?>
								<li class="">
									<a href="<?=$url?>">
										<?=$m2->nama?>
									</a>
									<ul class="">
										<?php foreach($m2->childs as $m3){ ?>
											<?php $url = $m3->url; if($m3->url_type == 'internal') $url = base_url($m3->url); ?>
											<?php if($m3->url == '#') $url = '#'; ?>
											<li class="">
												<a href="<?=$url?>">
													<?=$m3->nama?>
												</a>
											</li>
										<?php } ?>
									</ul>
								</li>
							<?php }else{ ?>
								<li class="">
									<a href="<?=$url?>">
										<?=$m2->nama?>
									</a>
								</li>
							<?php } ?>
						<?php } ?>
					</ul>
				</li>
			<?php }else{ ?>
				<li class="">
					<a href="<?=$url?>" title="Menuju <?=$m1->nama?>" class="" >
						<i class="<?=$m1->icon_class?>" aria-hidden="true"></i>
						<?=$m1->nama?>
					</a>
				</li>
			<?php } ?>
		<?php }} ?>
		<?php if($this->user_login){ ?>
			<li class="">
				<a href="<?=base_url('logout')?>" title="Keluar / Logout" class="" style="color: #a0a0a0; font-weight: normal;" >
					<i class="fa fa-sign-out" ></i>
					Logout
				</a>
			</li>
		<?php } ?>
	</ul>
</div>
