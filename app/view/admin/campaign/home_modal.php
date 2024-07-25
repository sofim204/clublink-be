<!-- by Muhammad Sofi 13 January 2022 16:11 | remodel on sponsored menu -->
<style>
	#iedate, #ieedate {
		background-color: #FFFFFF;
	}
</style>


<div id="modal_options" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header modal-header-title text-center">
				<h2 class="modal-title"><strong>Options</strong></h2>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="aEditSponsored" href="#" class="btn btn-primary text-center"><i class="fa fa-edit"></i> Edit</a>
					</div>
				</div>
				<div class="row" style="margin-bottom: 6px;"></div>
				<div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="aEditSponsoredPicture" href="#" class="btn btn-warning text-center"><i class="fa fa-edit"></i> Add Sponsored Picture</a>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="aChangeSponsoredPicture" href="#" class="btn btn-warning text-center"><i class="fa fa-edit"></i> Change Sponsored Picture</a>
					</div>
				</div>
				<div class="row" style="margin-bottom: 6px;"></div> 
				<div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="bhapus_modal" href="javascript:void(0);" class="btn btn-danger text-center"><i class="fa fa-trash-o"></i> Delete</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- modal tambah -->
<div id="modal_tambah" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Add New Sponsored</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftambah"  method="post" enctype="multipart/form-data"  class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
								<input type="hidden" name="id" id="ieid" value="" />
								<label class="control-label" for="ijudul">Title *</label>
								<input id="ijudul" type="text" name="judul" class="form-control" minlength="1" placeholder="Sponsored Title" autocomplete="off" required />
							</div>
							<!-- <div class="col-md-12">
								<label class="control-label" for="iteks">Text</label>
								<div id="editor">
									<textarea id="iteks" name="teks" class="form-control" rows="5"></textarea>
								</div>
							</div> -->
						</div>
						<!-- <div class="form-group" style="display: none;">
							<div class="col-md-4">
								<label class="" for="iutype">Link Type</label>
								<select id="iutype" name="utype" class="form-control" required>
									<option value="internal" data-pre="#search#{keyword}">Find Product</option>
									<option value="internal" data-pre="#produk-kategori#{id_kategori}">Category Product</option>
									<option value="internal" data-pre="#produk-detail#{id_produk}">Detail Product</option>
									--comment here  <option value="internal" data-pre="#url#<?=base_url()?>">Url Internal</option>  comment here--
									<option value="external" data-pre="#url#{url}">Url</option>
								</select>
							</div>
							<div class="col-md-8">
								<label class="" for="iurl">Link Target</label>
								<input id="iurl" type="text" name="url" class="form-control"  placeholder="link target default: #" required value="#" />
							</div>
						</div> -->
						<!-- <div class="form-group">
							<div class="">
								<div style="text-align: center;"><img id="original-Img_thumb" src="" class="img-responsive" alt="" style="display: none;"></div>
							</div>
							<div class="col-md-12">
								<div style="text-align: center;"><img id="upload-Preview_thumb" src="" class="img-responsive" alt=""></div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label class="" for="ifilethumb">Sponsored Thumbnail (1249 x 543) px *</label>
								<input id="ifilethumb" type="file" name="thumb" class="form-control" placeholder="Sponsored Thumbnail" accept=".jpg, .jpeg, .png" required />
							</div>
							<div class="col-md-6">&nbsp;</div>
						</div> -->
						<div class="form-group">
							<div class="">
								<div style="text-align: center;"><img id="original-Img" src="" class="img-responsive" alt="" style="display: none;"></div>
							</div>
							<div class="col-md-12">
								<div style="text-align: center;"><img id="upload-Preview" src="" class="img-responsive" alt=""></div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5">
								<label class="" for="ifile">Sponsored Picture (1249 x 543) px *</label>
								<input id="ifile" type="file" name="gambar" class="form-control" placeholder="Sponsored Picture" accept=".jpg, .jpeg, .png, .gif" required />
							</div>
							<div class="col-md-3">
								<label class="" for="iedate">Due Date *</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<!-- <input id="iedate" type="text" name="edate" class="form-control due-date" data-date-format="yyyy-mm-dd" value="<?=date("Y-m-d", strtotime("+10 days"))?>"  placeholder="" autocomplete="off" required /> -->
									<input id="iedate" type="text" name="edate" class="form-control input-datepicker duedate" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" autocomplete="off" readonly />
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3">
								<label class="" for="itype_sponsored">Type</label>
								<select id="itype_sponsored" name="type_sponsored" class="form-control">
									<option value="original">Original</option>
									<option value="shop">Shop</option>
									<option value="product">Product</option>
								</select>
							</div>
							<div class="col-md-4" id="row_seller_shop" style="display: none;">
								<label for="select_seller_shop" >Seller Shop</label>
								<!-- <select id="select_seller_shop" name="seller_id" class="form-control"></select> -->
								<select id="select_seller_shop" class="form-control"></select>
								<input id="select_seller_shop_value" type="hidden" name="seller_id" />
							</div>
							<div class="col-md-4" id="row_seller_shop_product" style="display: none;">
								<label for="select_seller_shop_product" >Seller Shop</label>
								<!-- <select id="select_seller_shop_product" name="seller_id" class="form-control"></select> -->
								<select id="select_seller_shop_product" class="form-control"></select>
								<input id="select_seller_shop_product_value" type="hidden" />
							</div>
							<div class="col-md-4" id="row_product_detail" style="display: none;">
								<label for="select_product_detail">Product Detail</label>
								<!-- <select id="select_product_detail" name="product_id" class="form-control"></select> -->
								<select id="select_product_detail"  class="form-control"></select>
								<input id="select_product_detail_value" type="hidden" name="product_id" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<!-- //by Donny Dennison - 2 july 2021 9:37 
        							//move-campaign-to-sponsored
								<label class="" for="ipriority">Priority (0-5)</label> -->
								<label class="" for="ipriority">Priority (1-100)</label>
								<select id="ipriority" name="priority" class="form-control" required>
									
									<!-- //by Donny Dennison - 2 july 2021 9:37 
        							//move-campaign-to-sponsored
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option> -->
									<?php for ($i=1; $i <= 100; $i++) { ?>	
										<option value="<?= $i ?>"><?= $i ?></option>
									<?php  } ?>

								</select>
							</div>
							<div class="col-md-6">
								<label class="" for="iis_active">Status</label>
								<select id="iis_active" name="is_active" class="form-control" required>
									<option value="1">Active</option>
									<option value="0">Not Active</option>
								</select>
							</div>
						</div>
						<!-- <div class="form-group" style="display: none;">
							<div class="col-md-6">
								<label class="" for="itopbar">Top Bar</label>
								<select id="itopbar" name="top_bar" class="form-control" required>
									<option value="1">Available</option>
									<option value="0">Not Available</option>
								</select>
							</div>
						</div> -->
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>
<!-- modal edit -->
<div id="modal_edit" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Edit Sponsored</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftedit"  method="post" enctype="multipart/form-data"  class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="iejudul">Title *</label>
								<input id="iejudul" type="text" name="judul" class="form-control" minlength="1" placeholder="Promo Title" required />
							</div>
							<!-- <div class="col-md-12">
								<label class="control-label" for="ieteks">Text</label>
								<div id="editor1">
									<textarea id="ieteks" name="teks" class="form-control" rows="5"></textarea>
								</div>
							</div> -->
						</div>
						<!-- <div class="form-group">
							<div class="col-md-4">
								<label class="" for="ieutype">Link Type</label>
								<select id="ieutype" name="utype" class="form-control" required>
									<option value="internal" data-pre="#cari#{keyword}">Find Product</option>
									<option value="internal" data-pre="#produk-kategori#{id_kategori}">Category Product</option>
									<option value="internal" data-pre="#produk-detail#{id_produk}">Detail Product</option>
									--comment here <option value="internal" data-pre="#url#<?=base_url()?>">Url Internal</option> comment here--
									<option value="external" data-pre="#url#{url}">Url</option>
								</select>
							</div>
							<div class="col-md-8">
								<label class="" for="ieurl">Link Target</label>
								<input id="ieurl" type="text" name="url" class="form-control"  placeholder="link target" required value="#" />
							</div>
						</div> -->
						<div class="form-group">
							<div class="col-md-12">
								<div style="text-align: center;"><img id="imageDisplay" src="" class="img-responsive" alt=""></div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label class="" for="iefile">Sponsored Picture (1249 x 543) px *</label>
								<input id="iefile" type="file" name="gambar" class="form-control" placeholder="Picture" accept=".jpg, .jpeg, .png, .gif" />
							</div>
							<div class="col-md-6">
								<label class="" for="ieedate">Due Date *</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<!-- <input id="ieedate" type="text" name="edate" class="form-control due-date" data-date-format="yyyy-mm-dd"  placeholder="" required /> -->
									<input id="ieedate" type="text" name="edate" class="form-control input-datepicker duedate" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" autocomplete="off" readonly />
								</div>
							</div>
						</div>
						<!-- current seller shop or product detail -->
						<div class="form-group">
							<div class="col-md-4">
								<label for="ieselect_seller_shop_value_edit">Seller Shop ID</label>
								<input id="ieselect_seller_shop_value_edit" type="text" style="background-color:#fafafa" name="seller_id" readonly />
							</div>
							<div class="col-md-3"></div>
							<div class="col-md-6">
								<label for="ieselect_product_detail_value_edit">Product Detail ID</label>
								<input id="ieselect_product_detail_value_edit" type="text" style="background-color:#fafafa" name="product_id" readonly />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3">
								<label class="" for="itype_sponsored_edit">Type</label>
								<select id="itype_sponsored_edit" name="type_sponsored" class="form-control">
									<!-- <option value="">==Choose==</option> -->
									<option value="original">Original</option>
									<option value="shop">Shop</option>
									<option value="product">Product</option>
								</select>
							</div>
							<div class="col-md-4" id="row_seller_shop_edit" style="display: none;">
								<label for="select_seller_shop_edit" >Seller Shop</label>
								<!-- <select id="select_seller_shop_edit"  class="form-control"></select> -->
								<select id="select_seller_shop_edit" class="form-control"></select>
								<input id="select_seller_shop_value_edit" type="text" style="background-color:#fafafa" readonly />
							</div>
							<div class="col-md-4" id="row_seller_shop_product_edit" style="display: none;">
								<label for="select_seller_shop_product_edit" >Seller Shop</label>
								<!-- <select id="select_seller_shop_product_edit"  class="form-control"></select> -->
								<select id="select_seller_shop_product_edit" class="form-control"></select>
								<input id="select_seller_shop_product_value_edit" type="text" style="background-color:#fafafa" readonly />
							</div>
							<div class="col-md-4" id="row_product_detail_edit" style="display: none;">
								<label for="select_product_detail_edit">Product Detail</label>
								<!-- <select id="select_product_detail_edit" class="form-control"></select> -->
								<select id="select_product_detail_edit"  class="form-control"></select>
								<input id="select_product_detail_value_edit" type="text" style="background-color:#fafafa" readonly />
							</div>
						</div>
						<div class="form-group">
							<!-- <div class="col-md-6">
								<label class="" for="iepriority">Priority (0-5)</label>
								<select id="iepriority" name="priority" class="form-control" required>
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
								</select>
							</div> -->
							<div class="col-md-6">
								<label class="" for="iepriority">Priority (1-100)</label>
								<select id="iepriority" name="priority" class="form-control" required>
									<?php for ($i=1; $i <= 100; $i++) { ?>	
										<option value="<?= $i ?>"><?= $i ?></option>
									<?php  } ?>
								</select>
							</div>
							<div class="col-md-6">
								<label class="" for="ieis_active">Status</label>
								<select id="ieis_active" name="is_active" class="form-control" required>
									<option value="1">Active</option>
									<option value="0">Not Active</option>
								</select>
							</div>
							<!-- <div class="col-md-6">
								<label class="" for="ietopbar">Top Bar</label>
								<select id="ietopbar" name="top_bar" class="form-control" required>
									<option value="1">Available</option>
									<option value="0">Not Available</option>
								</select>
							</div> -->
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
              				<button id="bhapus" type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i> Delete</button>
							<button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<div id="modal_add_sponsored_picture" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Add Sponsored Picture</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="fadd_sponsored_picture" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
								<label class="" for="igambar_sponsored">Choose Picture File *</label>  
								<input id="igambar_sponsored" type="file" name="gambar_sponsored" class="form-control" accept=".jpg, .jpeg, .png, .gif" required />
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-upload"></i> Upload</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>