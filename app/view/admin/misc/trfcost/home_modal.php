<style>
	.text-left {
		text-align: left !important;
	}
</style>
<!-- modal tambah -->
<div id="modal_tambah" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Add</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftambah" action="<?=base_url_admin()?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
            <div class="form-group">
							<div class="col-md-6">
								<label class="" for="ia_bank_id_from" >From Bank</label>
								<div class="input-group">
									<input id="ia_bank_nama_from" type="text" class="form-control" placeholder="Search Bank" disabled />
									<span class="input-group-btn">
                    <button type="button" class="btn btn-default btn-cari-bank" data-modal="add" data-for="from"><i class="fa fa-search"></i></button>
                  </span>
								</div>
								<input id="ia_bank_id_from" name="a_bank_id_from" type="hidden" class="form-control" minlength="1" placeholder="" />
							</div>
							<div class="col-md-6">
								<label class="" for="ia_bank_id_to" >To Bank</label>
								<div class="input-group">
									<input id="ia_bank_nama_to" type="text" class="form-control" placeholder="Search Bank" disabled />
									<span class="input-group-btn">
                    <button type="button" class="btn btn-default btn-cari-bank" data-modal="add" data-for="to"><i class="fa fa-search"></i></button>
                  </span>
								</div>
								<input id="ia_bank_id_to" name="a_bank_id_to" type="hidden" class="form-control" minlength="1" placeholder="" />
							</div>
						</div>
						<input id="iutype" name="utype" type="hidden" value="nominal" />
            <div class="form-group">
							<div class="col-md-6">
								<label class="" for="i">Cost / Value</label>
								<input id="icost" name="cost" type="text" class="form-control" placeholder="Cost / Value" />
							</div>
            </div>
            <div class="form-group">
							<div class="col-md-6">
								<label class="" for="iis_active">Status</label>
								<select id="iis_active" name="is_active" class="form-control">
									<option value="1">Active</option>
									<option value="0">Inactive</option>
								</select>
							</div>
            </div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary">Save</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<!-- modal edit -->
<div id="modal_edit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Edit</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="fedit" action="<?=base_url_admin()?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
            <div class="form-group">
							<div class="col-md-6">
								<label class="" for="iea_bank_nama_from" >From Bank</label>
								<input id="iea_bank_nama_from" type="text" value="" class="form-control" disabled />
							</div>
							<div class="col-md-6">
								<label class="" for="iea_bank_nama_to" >To Bank</label>
								<input id="iea_bank_nama_to" type="text" value="" class="form-control" disabled />
							</div>
						</div>
						<input id="ieutype" name="utype" type="hidden" value="nominal" />
            <div class="form-group">
							<div class="col-md-6">
								<label class="" for="i">Cost / Value</label>
								<input id="iecost" name="cost" type="text" class="form-control" placeholder="Cost / Value" />
							</div>
							<div class="col-md-6">
								<label class="" for="iis_active">Status</label>
								<select id="ieis_active" name="is_active" class="form-control">
									<option value="1">Active</option>
									<option value="0">Inactive</option>
								</select>
							</div>
            </div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button id="bhapus" type="button" class="btn btn-sm btn-warning">Delete</button>
							<button type="submit" class="btn btn-sm btn-primary">Save</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<!-- modal option -->
<div id="modal_option" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Option</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 btn-group-vertical " style="text-align: left;">
						<a id="aedit" href="#" class="btn btn-info text-left"><i class="fa fa-pencil"></i> Edit</a>
						<button id="ahapus" type="button" class="btn btn-danger text-left"><i class="fa fa-trash-o"></i> Delete</button>
					</div>
				</div>
				<div class="row" style="margin-top: 1em; ">
					<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
					<div class="col-xs-12 btn-group-vertical">
						<button type="button" class="btn btn-default btn-block text-left" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
					</div>
				</div>
				<!-- END Modal Body -->
			</div>
		</div>
	</div>
</div>

<!-- modal bank cari -->
<div id="bank_cari_modal" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Search Bank</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">

				<form id="bank_cari_modal_form" method="post" action="" class="form-horizontal">
					<div class="form-group">
						<div class="input-group">
							<input id="bank_cari_modal_input" type="text" placeholder="enter keyword" class="form-control" required />
							<span class="input-group-btn">
                <button type="button" class="btn btn-default btn-cari-bank-do" data-for="utama"><i class="fa fa-search"></i></button>
              </span>
						</div>
					</div>
				</form>
				<div class="row">
					<div class="col-md-12">
						<table id="bank_cari_modal_table" class="table table-bordered table-stripped">
							<thead>
								<tr>
									<th>ID</th>
									<th>Nation Code</th>
									<th>Bank</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- END Modal Body -->

		</div>
	</div>
</div>
