<style>
	/* refer to https://stackoverflow.com/questions/60149994/how-to-add-a-x-to-clear-input-field 
		by Muhammad Sofi 27 December 2021 18:00 | Add x button to clear search box 
	*/
	.dataTables_wrapper .dataTables_filter input::-webkit-search-cancel-button {
		-webkit-appearance: button !important;
		padding: 2px;
		margin-right: 5px;
	}

	table#drTable tr:hover {
		background-color: #EFBF65;
	}

	.swal2-popup {
		font-size: 1.2rem !important;
	}

	.btn-back {
        width: 85px;
        cursor: pointer;
        background: transparent;
        border: 1px solid #999;
        outline: none;
        transition: .5s ease;
    }

    .btn-back.full {
        width: 100%;
    }

    .btn-back:hover {
        transition: .3s ease;
        background: #DD8A0D;
        border: 1px solid transparent;
        color:#FFF;
    }

    .btn-back:hover svg {
        stroke-dashoffset: -480;
    }

    .btn-back span {
        color: white;
        font-size: 18px;
        font-weight: 100;
    }
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<!-- <div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">
				<div class="btn-group">
					<a id="aback" href="<?=base_url_admin(''); ?>" class="btn btn-default btn-back"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
		</div> -->
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>E-Commerce</li>
		<li>Recommendation Statistics</li>
	</ul>

	<div class="block full">
		<div class="block-title">
			<h2><strong>Recommendation Statistics</strong></h2>
		</div>
		<div class="block-section">
			<div class="row" style="display:flex; align-items:flex-end">
				<div class="col-md-2">
					<label for="flcdate_start">From Date</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input id="flcdate_start" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="From date" autocomplete="off" readonly />
					</div>
				</div>
				<div class="col-md-2">
					<label for="flcdate_end">To Date</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input id="flcdate_end" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="To date" autocomplete="off" readonly />
					</div>
				</div>
				<div class="col-md-2">
					<label for="flis_active">User Status</label>
					<select id="flis_active" class="form-control">
						<option value="">-- View All --</option>
						<option value="1">Active</option>
						<option value="0">Inactive</option>
					</select>
				</div>
				<div class="col-md-1">
					<button id="reset-filter" class="btn btn-block btn-danger">Reset</button>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<!-- <th width="10px">No.</th> -->
						<th width="10px">ID.</th>
						<th>Name</th>
						<th>Email</th>
						<th>User Status</th>
						<th>Referral Code</th>
						<th>Total Recommendation</th>
						<!-- <th>Register Date</th> -->
						<th>Updated Date</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>
