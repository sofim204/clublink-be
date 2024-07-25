<style>
	table {
		width: 100%;
	}

	th, td {
		padding: 8px;
		text-align: left;
		border-bottom: 1px solid #ddd;
	}
	/* change input datepicker readonly background color */
	#ifstart_date {
		background-color: #FFFFFF;
	}
	
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

	table#drTable tr:hover {
		background-color: #EFBF65;
	}
</style>
<div id="page-content">
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-12">
				<div class="btn-group">
					<a id="aback" href="<?=base_url_admin('community/leaderboard/'); ?>" class="btn btn-back"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Community</li>
		<li>Leaderboard</li>
		<li>Leaderboard Point History</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">
		<div class="block-title">
			<h2><strong>Filter</strong></h2>
		</div>
		<div class="block-section">
			<div class="row" style="display:flex; align-items:flex-end">
				<div class="col-md-2">
					<label for="ifstart_date">Submit Date</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input id="ifstart_date" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="Submit date" autocomplete="off" readonly /> <!-- fix autocomplete after select a date -->
					</div>
				</div>
				<div class="col-md-3">
					<label for="select_general_location">General Location</label>
					<select id="select_general_location" class="form-control"></select>
				</div>
				<div class="col-md-1">
					<button id="reset-filter" class="btn btn-block btn-danger">Clear</button>
				</div> 
			</div>
		</div>
	</div>
	<div class="block full">
		<div class="block-title">
			<h2><strong>Leaderboard Point History</strong></h2>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" style="width:100%">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th width="30px">No.</th>
						<th width="0px">ID</th>
						<th>Date</th>
						<th width="200px">Name</th>
						<th>Points</th>
						<th>Description</th>
						<th>General Location</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
	<!-- END Content -->
</div>