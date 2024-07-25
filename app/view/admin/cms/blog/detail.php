<style>
	.bordered {
		border: 1px #ededed solid;
	}
	.mp1 {
		padding: 1em;
	}
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">
				<div class="btn-group">
					<a id="" href="<?=base_url_admin('ecommerce/produk/'); ?>" class="btn btn-default"><i class="fa fa-chevron-left"></i> Kembali</a>
				</div>
			</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="" href="<?=base_url_admin('ecommerce/produk/edit/'.$produk->id); ?>" class="btn btn-info"><i class="fa fa-edit"></i> Edit</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>E-Commerce</li>
		<li>Produk</li>
		<li><?=$produk->nama?></li>
	</ul>
	<!-- END Static Layout Header -->
	
	<!-- Content -->
	<!-- Main Row -->
	<div class="row">
		<div class="col-md-8">
			<!-- Course Widget -->
			<div class="widget">
				<div class="widget-advanced">
					<!-- Widget Header -->
					<div class="widget-header text-center themed-background-dark">
						<h3 class="widget-content-light">
							<?=strtoupper($produk->nama)?><br>
							<small><?php echo ucfirst($produk->jenis); ?></small>
						</h3>
					</div>
					<!-- END Widget Header -->
					
					<!-- Widget Main -->
					<div class="widget-main">
						<a href="#" class="widget-image-container animation-fadeIn">
							<span class="widget-icon themed-background"><i class="fa fa-globe"></i></span>
            </a>
						<a href="#" class="btn btn-sm btn-default pull-left">
							SKU: <?=$produk->sku?>
						</a>
						<hr>
						<!-- Lesson Content -->
						<h3 class="sub-header">Deskripsi</h3>
						<?php echo $produk->deskripsi ?>
						
						<!-- END Lesson Content -->
					</div>
					<!-- END Widget Main -->
					
				</div>
			</div>
			
			<div class="block">
				<!-- Share Title -->
				<div class="block-title">
					<h2><strong>Gambar</strong></h2>
				</div>
				<!-- END Share Title -->
				
				<!-- Share Content -->
				<div class="block-section text-center">
					<div class="row">
						<?php foreach($produk->fotos as $foto){ ?>
						<div class="col-md-4 mp1">
							<div class="bordered">
							<img src="<?php echo base_url($foto->url); ?>" class="img-responsive" />
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
				<!-- END Share Content -->
			</div>
			
			<!-- END Course Widget -->
		</div>
		<div class="col-md-4">
			<!-- About Block -->
			<div class="block">
				<!-- About Content -->
				<div class="block-section">
					<img src="<?php echo base_url($produk->foto); ?>" class="img-responsive" />
				</div>
				<!-- END About Content -->
			</div>
			<!-- END About Block -->
			
			
			<?php if(!empty($produk->diskon_harga) && (strtotime($produk->diskon_expired) >= strtotime('now')) ){ ?>
			<!-- Harga Diskon Block -->
			<div class="block">
				<div class="block-title">
					<h2><strong>Diskon</strong></h2>
				</div>
				<div class="block-section text-center">
					<h4 style="color: #c4c4c4; "><s>Rp <?php echo number_format($produk->harga_jual,2,',','.'); ?></s></h4>
					<h3>Rp <?php echo number_format($produk->diskon_harga,2,',','.'); ?></h3>
				</div>
			</div>
			<!-- END Harga Diskon Block -->
			<?php } ?>
			
			<!-- Harga Block -->
			<div class="block">
				<div class="block-title">
					<h2><strong>Harga</strong></h2>
				</div>
				<div class="block-section text-center">
					<h3>Rp <?php echo number_format($produk->harga_jual,2,',','.'); ?></h3>
				</div>
			</div>
			<!-- END Harga Block -->
			
			<!-- Your Account Block -->
			<div class="block" style="display:none;">
				<div class="block-title">
					<h2><strong>BPOM</strong></h2>
				</div>
				<div class="block-section text-center">
					<?php if(strlen($produk->bpom)>1){ ?>
					<h4>POM</h4>
					<?php }else{ ?> 
					<h4>NON-POM</h4>
					<?php } ?>
				</div>
			</div>
			<!-- END Your Account Block -->
			
			<!-- Your Account Block -->
			<div class="block">
				<div class="block-title">
					<h2><strong>Berat</strong></h2>
				</div>
				<div class="block-section text-center">
					<h4><?php echo number_format($produk->berat,2,',','.'); ?> Gr</h4>
				</div>
			</div>
			<!-- END Your Account Block -->
			
			<!-- Your Account Block -->
			<div class="block">
				<div class="block-title">
					<h2><strong>Stok</strong></h2>
				</div>
				<div class="block-section text-center">
					<h4><?php echo number_format($produk->stok,0,',','.'); ?></h4>
				</div>
			</div>
			<!-- END Your Account Block -->
			
		</div>
	</div>
	<!-- END Main Row -->
</div>	