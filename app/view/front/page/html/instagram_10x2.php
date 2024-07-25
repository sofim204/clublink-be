<section class="instagram-style">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h2 class="custom-font" style="text-align:left;">Find us on Instagram</h2>
			</div>
		</div>
	  <div class="row">
			<?php $i=0; foreach($ig_list as $igl){ ?>
	      <div class="col-md-2 <?php if($i%5 == 0) echo 'col-md-offset-1'; ?>" style="background-image: url(<?=$igl->ig_media?>); background-size: cover;">
					<a href="<?=$igl->ig_link?>" target="_blank">
						<div style="height: 180px;">&nbsp;</div>
					</a>
				</div>
			<?php $i++; } ?>
	  </div>
	</div>
</section>
