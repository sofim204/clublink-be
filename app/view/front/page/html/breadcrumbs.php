
			<div class="breadcrumbs">
				<div class="container">
					<div class="row">
						<div class="col-xs-12">
							<ul>
								<?php 
								if(isset($this->breadcrumbs)){ 
									echo '<li class="home"> <a href="'.base_url().'" title="Kembali ke beranda">Beranda</a> <span>/</span> </li>';
									$max = count($this->breadcrumbs);
									$i=0;
									foreach($this->breadcrumbs as $bc){
										//print_r($bc);
										//die();
										$i++;
										if($i == $max){
											echo  '<li> <strong>'.$bc->name.'</strong> </li>';
										}else{
											echo '<li> <a href="'.$bc->url.'" title="'.$bc->title.'">'.$bc->name.'</a> <span>/</span> </li>';
										}
									}
								}else{ 
								?>
								<li class="home"> <a href="index.html" title="Go to Home Page">Home</a> <span>/</span> </li>
								<li> <a href="shop_grid.html" title="">Clutches</a> <span>/ </span> </li>
								<li> <a href="shop_grid.html" title="">Bucket Bag</a> <span>/</span> </li>
								<li> <strong>Smartphones</strong> </li>
								<?php } ?>
							</ul>
						</div>
					</div>
				</div>
			</div>