			<header class="jtv-header-v2">
				<div class="header-container">
					<nav>
						<div class="container-fluid">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12 jtv-logo-block">
									<div class="logo">
										<a href="<?php echo base_url(); ?>" title="<?=$this->site_name?>"><img alt="<?=$this->site_name?> Logo" src="<?php echo base_url('assets/img/'); ?>Logo_Shafira.png"></a>
									</div>
									<!-- Navbar -->

									<div class="nav-inner">
										<div class="mm-toggle-wrap hidden-lg hidden-md">
											<div class="mm-toggle"> <i class="fa fa-bars"></i><span class="mm-label hidden">Menu</span> </div>
										</div>
										<ul id="nav" class="hidden-xs hidden-sm">
											<?php if(isset($menu_main)){ foreach($menu_main as $m1){ ?>
												<?php $url = $m1->url; if($m1->url_type == 'internal') $url = base_url($m1->url); ?>
												<?php if($m1->url == '#') $url = '#'; ?>
												<?php if(count($m1->childs)>0){ ?>
													<li class="level-a drop-menu">
														<a href="<?=$url?>" title="<?=$m1->nama?>" class="">
															<span>
																<i class="<?=$m1->icon_class?>"></i>
																<?=$m1->nama?>
																<i class="fa fa-angle-down"></i>
															</span>
														</a>
														<ul class="level-b">
														<?php foreach($m1->childs as $m2){ ?>
															<?php $url = $m2->url; if($m2->url_type == 'internal') $url = base_url($m2->url); ?>
															<?php if($m2->url == '#') $url = '#'; ?>
															<?php if(count($m2->childs)>0){ ?>
															<li class="level-b">
																<a href="<?=$url?>">
																	<span>
																		<?=$m2->nama?>
																		<i class="fa fa-angle-down"></i>
																	</span>
																</a>
																<ul class="level-c">
																	<?php foreach($m2->childs as $m3){ ?>
																	<?php $url = $m3->url; if($m3->url_type == 'internal') $url = base_url($m3->url); ?>
																	<?php if($m3->url == '#') $url = '#'; ?>
																	<li class="level-c">
																		<a href="<?=$url?>">
																			<span>
																				<?=$m3->nama?>
																			</span>
																		</a>
																	</li>
																	<?php } ?>
																</ul>
															</li>
															<?php }else{ ?>
															<li class="level-b">
																<a href="<?=$url?>">
																	<span>
																		<?=$m2->nama?>
																	</span>
																</a>
															</li>
															<?php } ?>
														<?php } ?>
														</ul>
													</li>
												<?php }else{ ?>
													<li class="level-a drop-menu">
														<a href="<?=$url?>" title="Menuju <?=$m1->nama?>" class="" >
															<span>
																<i class="<?=$m1->icon_class?>" aria-hidden="true"></i>
																<?=$m1->nama?>
															</span>
														</a>
													</li>
												<?php } ?>
											<?php }} ?>
										</ul>

										<!-- top notif -->
										<?php //$this->getThemeElement("page/html/top_notif",$__forward); ?>
										<!-- top notif -->

										<!-- top user -->
										<?php $this->getThemeElement("page/html/top_user",$__forward); ?>
										<!-- top user -->

										<!-- top cart -->
										<?php $this->getThemeElement("page/html/top_cart",$__forward); ?>
										<!-- top cart -->

										<!-- top search -->
										<?php $this->getThemeElement("page/html/top_search",$__forward); ?>
										<!-- top search -->

									<!-- end nav -->
								</div>
								</div>
							</div>
						</div>
						<div class="container searchbardiv" id="formsearch">
							<form role="search" method="get" id="searchform" action="<?php echo base_url("produk/"); ?>"  >
								<div class="input-group">
									<input type="text" id="searchbox" class="form-control" placeholder="search in our store" name="keyword" id="iskeyword">
									<div class="input-group-btn">
										<button class="btn btn-default"  id="searchsubmit"  type="submit">
											<i class="fa fa-search fa-2x"></i>
										</button>
									</div>
								</div>
							</form>
						</div>
					</nav>

			</header>
