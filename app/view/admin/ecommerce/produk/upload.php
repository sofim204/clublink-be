<style>
	.btn-back {
        width: 85px;
        cursor: pointer;
        background: #F9F5F5;
        border: 1px solid #999;
        outline: none;
		color: #222121;
        transition: .3s ease;
    }

    .btn-back:hover {
        transition: .3s ease;
        background: #DD8A0D;
        border: 1px solid transparent;
        color:#FFF;
    }
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-12">
				<div class="btn-group">
					<a id="aback" href="<?=base_url_admin('ecommerce/produk/'); ?>" class="btn btn-back"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>E-Commerce</li>
		<li><a href="<?=base_url("ecommerce/produk/")?>">Product</a></li>
		<li>Upload</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<form id="fupload" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="" onsubmit="return false;">
		<div class="block full row">
			<div class="block-title">
				<h2><strong>Upload Product</strong></h2>
			</div>			
			<div class="form-group">
				<div class="col-md-4">
					<label class="" for="iseller">Email User *</label>
					<!-- <input id="iseller" type="email" name="email" class="form-control" minlength="1" placeholder="Email user" required /> -->
					<div class="input-group">
						<!-- <input type="email" id="example-input2-group2" name="example-input2-group2" class="form-control" placeholder="Email"> -->
						<input id="iseller" type="email" name="email" class="form-control" minlength="1" placeholder="Email user" required />
						<span class="input-group-btn">
							<button type="button" id="bcheck_email" class="btn btn-info">Check</button>
						</span>
					</div>
				</div>
				<div class="col-md-4">
					<label class="" for="ifupload">File Excel *</label>
					<input id="ifupload" type="file" name="file_xls" class="form-control" placeholder="" accept=".xlsx" required/>
				</div>
			</div>
		</div>	

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Action</strong></h2>
			</div>
			<div class="form-group form-actions">
				<div class="col-xs-12 text-right">
					<button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
				</div>
			</div>
		</div>

	</form>
</div>
