<?php
	$logo_site = '';
	if(isset($this->skin_admin_logo)){
		if(strlen($this->skin_admin_logo)>5){
			$logo_site = base_url($this->skin_admin_logo);
		}
	}

	if(!isset($this->current_page)) $this->current_page = "";
	if(!isset($this->current_parent)) $this->current_parent = "";
	$current_page = $this->current_page;
	$current_parent = $this->current_parent;
	$parent = array();
	foreach($sess->admin->menus->left as $key=>$v){
		$parent[$v->identifier] = 0;
		if(count($v->childs)>0){
			foreach($v->childs as $f){
				if($current_page==$f->identifier){
					$current_page = $v->identifier;
					$parent[$v->identifier] = 1;
				}
			}
		}
	}
	$admin_foto = '';
	if(isset($sess->admin->foto))$admin_foto = $sess->admin->foto;
	if(empty($admin_foto)) $admin_foto = 'media/pengguna/default.png';
	$admin_foto = base_url($admin_foto);
?>
<div id="sidebar">
	<!-- Wrapper for scrolling functionality -->
	<div id="sidebar-scroll">
		<!-- Sidebar Content -->
		<div class="sidebar-content">
			<!-- Brand -->
			<a href="<?=base_url_admin()?>" class="sidebar-brand">
				<?php if(!empty($logo_site)){ ?>
				<img src="<?=$logo_site?>" style="width: 75%;" />
				<?php }else{ ?>
				<h4 class=""><?=$this->site_name?></h4>
				<?php } ?>
			</a>
			<!-- END Brand -->

			<!-- User Info -->
			<div class="sidebar-section sidebar-user clearfix sidebar-nav-mini-hide">
				<div class="sidebar-user-avatar">
					<a href="<?=base_url_admin('profil/')?>">
						<img src="<?=$admin_foto?>" alt="avatar"  onerror="this.onerror=null;this.src='<?=base_url()?>media/pengguna/default.png'" />
					</a>
				</div>
				<div class="sidebar-user-name">
					<?php if(isset($sess->admin->nama)){ ?>
						<?=$sess->admin->nama?>
					<?php } else if(isset($sess->admin->username)){ ?>
						<?=$sess->admin->username?>
					<?php } ?>

				</div>
				<div class="sidebar-user-links">
					<a href="<?=base_url_admin('profil/')?>" data-toggle="tooltip" data-placement="bottom" title="Profile"><i class="gi gi-user"></i></a>
					<a href="<?=base_url_admin("logout")?>" data-toggle="tooltip" data-placement="bottom" title="Logout"><i class="gi gi-exit"></i></a>
				</div>
			</div>
			<!-- END User Info -->

			<!-- Sidebar Navigation -->
			<ul class="sidebar-nav">
				<?php foreach($sess->admin->menus->left as $key=>$v){ ?>
					<?php if(count($v->childs)>0){ ?>
						<li class="<?php if($parent[$v->identifier]==1) echo 'active'?>">
							<a href="#" class="sidebar-nav-menu ">
								<i class="fa fa-angle-left sidebar-nav-indicator sidebar-nav-mini-hide"></i>
								<i class="<?=$v->fa_icon?> sidebar-nav-icon"></i>
								<span class="sidebar-nav-mini-hide"><?=$v->name?></span>
							</a>
							<ul class="">
							<?php foreach($v->childs as $f){ ?>
								<?php if($f->utype=="external"){ ?>
								<li>
									<a href="<?=$f->path?>" class="<?php if($this->current_page==$f->identifier) echo 'active'?>">
										<?=$f->name?>
									</a>
								</li>
								<?php } else { ?>
								<li >
									<a href="<?=base_url_admin($f->path)?>" class="<?php if($this->current_page==$f->identifier) echo 'active'?>">
										<?=$f->name?>
									</a>
								</li>
								<?php } ?>
							<?php } ?>
							</ul>
						</li>
					<?php }else{ ?>
					<li class="<?php if($current_page==$key) echo 'active'?>"><a href="<?=base_url_admin($v->path)?>" class="<?php if($current_page==$key) echo 'active'?>"><i class="<?=$v->fa_icon?>"></i> <span><?=$v->name?></span></a></li>
					<?php } ?>
				<?php } ?>
			</ul>
			<!-- END Sidebar Navigation -->
		</div>
		<!-- END Sidebar Content -->
	</div>
	<!-- END Wrapper for scrolling functionality -->
</div>
