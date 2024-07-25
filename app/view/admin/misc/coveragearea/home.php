<style>
	/* refer to https://stackoverflow.com/questions/60149994/how-to-add-a-x-to-clear-input-field 
		by Muhammad Sofi 27 December 2021 18:00 | Add x button to clear search box 
	*/

	table {
		width: 100%;
	}

	th, td {
		padding: 8px;
		text-align: left;
		border-bottom: 1px solid #ddd;
	}

	tr:hover {background-color: #DADDFC;}
	tr.selected  {
		background-color: #6A72C8;
		color: #ffffff;
	}

	.dataTables_wrapper .dataTables_filter input::-webkit-search-cancel-button {
		-webkit-appearance: button !important;
		padding: 2px;
		margin-right: 5px;
	}
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Misc</li>
		<li>Coverage Area</li>
	</ul>

	<div class="block full">
		<div class="block-title">
			<h2><strong>Coverage Area</strong></h2>
		</div>
		<div class="block-section">
			<form id="form_add_data" action="" method="post" onsubmit="return false;">
				<div class="row gap-per-row" style="display:flex; align-items:flex-end;">
					<div class="col-md-2">
						<label for="fltype_coverage">Type Coverage</label>
						<select id="fltype_coverage" name="type" class="form-control">
							<option value="">-- Choose --</option>
							<option value="jalan">Jalan</option>
							<option value="kelurahan">Kelurahan</option>
							<option value="kecamatan">Kecamatan</option>
							<option value="kabkota">Kabupaten / Kota</option>
							<option value="provinsi">Provinsi</option>
						</select>
					</div>
					<div class="col-md-1">
						<button id="reset-filter" class="btn btn-block btn-danger">Clear</button>
					</div>
				</div>
				<div class="row gap-per-row">
					<div class="col-md-3" id="toggle_provinsi" style="display: none;">
						<label for="iprovinsi">Provinsi</label>
						<select id="iprovinsi" name="provinsi" class="form-control"></select>
						<!-- <input type="text" name="provinsi" id="iprovinsi" class="form-control" autocomplete="off" /> -->
					</div>
					<div class="col-md-3" id="toggle_kabkota" style="display: none;">
						<label for="ikabkota">Kabupaten / Kota</label>
						<select id="ikabkota" name="kabkota" class="form-control"></select>
						<!-- <input type="text" name="kabkota" id="ikabkota" class="form-control" autocomplete="off" /> -->
					</div>
					<div class="col-md-3" id="toggle_kecamatan" style="display: none;">
						<label for="ikecamatan">Kecamatan</label>
						<select id="ikecamatan" name="kecamatan" class="form-control"></select>
						<!-- <input type="text" name="kecamatan" id="ikecamatan" class="form-control" autocomplete="off" /> -->
					</div>
					<div class="col-md-3 "id="toggle_kelurahan" style="display: none;">
						<label for="ikelurahan">Kelurahan</label>
						<select id="ikelurahan" name="kelurahan" class="form-control"></select>
						<!-- <input type="text" name="kelurahan" id="ikelurahan" class="form-control" autocomplete="off" /> -->
					</div>
				</div>
				<div class="row gap-per-row">
					<div class="col-md-4" id="toggle_jalan" style="display: none;">
						<label class="control-label" for="ijalan">Jalan</label>
						<input type="text" name="jalan" id="ijalan" class="form-control" autocomplete="off" />
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<label class="control-label" for="ilatitude">Latitude</label>
						<input type="text" name="latitude" id="ilatitude" class="form-control" autocomplete="off" />
					</div>
					<div class="col-md-4">
						<label class="control-label" for="ilongitude">Longitude</label>
						<input type="text" name="longitude" id="ilongitude" class="form-control" autocomplete="off" />
					</div>
					<div class="col-md-2">
						<label class="control-label" for="iradius">Radius</label>
						<input type="text" name="radius" id="iradius" class="form-control" autocomplete="off" />
					</div>
				</div>
				<div class="row">
					<div class="col-md-8"></div>
					<div class="col-md-4" style="margin-top: 15px;">
						<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-save"></i> Save Changes</button>
					</div>
				</div>
			</form>	
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th>No.</th>
						<th>#</th>
						<th>Type</th>
						<th>Provinsi</th>
						<th>Kabkota</th>
						<th>Kecamatan</th>
						<th>Kelurahan</th>
						<th>Jalan</th>
						<th>Latitude</th>
						<th>Longitude</th>
						<th>Radius</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div style="margin-top: 10px;">
			<button id="btn-coverage-inactive" type="button" class="btn btn-primary">Set Inactive</button>
			<button id="btn-coverage-active" type="button" class="btn btn-primary">Set Active</button>
			<button id="btn-coverage-delete" type="button" class="btn btn-danger">Delete</button>
			<strong> *you can select multiple row in table</strong>
		</div>
	</div>
</div>
