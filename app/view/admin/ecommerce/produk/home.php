<style>
h4.tbl-content {
	margin: 0;
	line-height: 1;
}
.tbl-content-category {
	margin: 0.5em 0;
	color: #9c9c9c;
	font-weight: bold;
	line-height: 1;
}
.tbl-product-properties {
	margin-top: 0.5em;
}
.img-responsive.img-icon {
	max-width: 64px;
	border-radius: 10px;
	border: 1px #acacac solid;
	margin-left: 0.5em;
}

#ifcdate_start, #ifcdate_end {
	background-color: #FFFFFF;
}

.dataTables_wrapper .dataTables_filter input::-webkit-search-cancel-button {
	-webkit-appearance: button !important;
	padding: 2px;
	margin-right: 5px;
}

table#drTable tr:hover {
	background-color: #EFBF65;
}
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">&nbsp;</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="" href="<?=base_url_admin('ecommerce/produk/upload/')?>" class="btn btn-info"><i class="fa fa-upload"></i> Upload Product</a>
				</div>
			</div>
		</div>
	</div>
	<!-- <div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6"></div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="" href="<?=base_url_admin('ecommerce/produk/tambah/'); ?>" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
				</div>
			</div>
		</div>
	</div> -->
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>E-Commerce</li>
		<li>Products</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">
		<div class="block-title">
			<h2><strong>Products</strong></h2>
		</div>
		<div class="row" style="margin-bottom: 1em;">
			<div class="col-md-2">
				<label for="ifcdate_min">From Order Date</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input id="ifcdate_start" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="From date"  />
				</div>
			</div>
			<div class="col-md-2">
				<label for="ifcdate_max">To Order Date</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input id="ifcdate_end" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="To date"  />
				</div>
			</div>
		</div>
		<div class="row" style="margin-bottom: 1em;">
			<div class="col-md-2">
				<label for="ifb_kategori_id">Category</label>
				<select id="ifb_kategori_id" class="form-control">
					<option value="">--view all--</option>
					<?php if(isset($kategori_list)){ foreach($kategori_list as $kt){ ?>
					<option value="<?=$kt->id?>"><?=$kt->nama?></option>
					<?php }} ?>
				</select>
			</div>
			<div class="col-md-2">
				<label for="ifprice_min">Price Range (Min)</label>
				<input id="ifprice_min" type="number" class="form-control" value="" />
			</div>
			<div class="col-md-2">
				<label for="ifprice_max">Price Range (Max)</label>
				<input id="ifprice_max" type="number" class="form-control" value="" />
			</div>
			<div class="col-md-2">
				<label for="ifb_kondisi_id">Condition</label>
				<select id="ifb_kondisi_id" class="form-control">
					<option value="">--view all--</option>
					<?php if(isset($kondisi_list)){ foreach($kondisi_list as $kl){ ?>
					<option value="<?=$kl->id?>"><?=$kl->nama?></option>
					<?php }} ?>
				</select>
			</div>
			<div class="col-md-2">
				<label for="fl_reset">&nbsp;</label>
				<button id="fl_reset" type="button" class="btn btn-warning btn-block"><i class="fa fa-reset"></i> Reset</button>
			</div>
		</div>
		<div class="row" style="margin-bottom: 1em;">
			<div class="col-md-2">
				<label for="if_courier_service">Courier Service</label>
				<select id="if_courier_service" class="form-control">
					<option value="">--view all--</option>
					<option value="qxpress">QXpress</option>

					<!-- by Donny Dennison - 15 september 2020 17:45
        			change name, image, etc from gogovan to gogox -->
					<!-- <option value="gogovan">Gogovan</option> -->
					<option value="gogox">Gogox</option>

					<!-- by Donny Dennison - 23 september 2020 15:42
					add direct delivery feature -->
					<option value="direct_delivery">Direct Delivery</option>
					
				</select>
			</div>

			<!-- by Donny Dennison - 8 february 2021 16:44
			add product type column in product menu -->
			<!-- <div class="col-md-3"> -->
			<div class="col-md-2">

				<label for="if_free_ship">Free Shipping</label>
				<select id="if_free_ship" class="form-control">
					<option value="">--view all--</option>
					<option value="1">Yes</option>
					<option value="0">No</option>
				</select>
			</div>

			<!-- by Donny Dennison - 8 february 2021 16:44
			add product type column in product menu -->
			<!-- <div class="col-md-3"> -->
			<div class="col-md-2">

				<label for="ifproduk_status">Status</label>
				<select id="ifproduk_status" class="form-control">
					<option value="">--view all--</option>
					<option value="publish_active">Published</option>
					<option value="draft_active">Draft</option>
					<option value="inactive">Inactive</option>
				</select>
			</div>
			
			<!-- by Donny Dennison - 8 february 2021 16:44
			add product type column in product menu -->
			<div class="col-md-2">
				<label for="ifproduk_type">Product Type</label>
				<select id="ifproduk_type" class="form-control">
					<option value="">--view all--</option>
					<!-- <option value="Protection">Protection</option> -->
					<option value="MeetUp">MeetUp</option>
					<option value="Automotive">Automotive</option>
					<option value="Free">Free</option>
					<!-- <option value="Motorcycle">Motor</option>
					<option value="Car">Mobil</option> -->
					<option value="Santa">Santa</option>
				</select>
			</div>

			<div class="col-md-2">
				<label for="bfilter">&nbsp;</label>
				<button id="bfilter" type="button" class="btn btn-info btn-block"><i class="fa fa-filter"></i> Filter</button>
			</div>
		</div>

		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th class="text-center">ID</th>
						<th>Image</th>
						<th>Seller</th>
						<th>Product</th>
						
						<!-- by Donny Dennison - 21 January 2021 17:44
        				add weight and dimension in product table cms -->
						<th>Weight</th>
						<th>Dimension (WxDxH)</th>

						<th>Price(Rp.)</th>
						<th>Create Date</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody style="background-color: #FFFFFF;">
				</tbody>
			</table>
		</div>
	</div>
	<!-- END Content -->
</div>
