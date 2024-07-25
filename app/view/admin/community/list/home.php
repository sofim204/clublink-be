<style>
	/* refer to https://stackoverflow.com/questions/60149994/how-to-add-a-x-to-clear-input-field 
		by Muhammad Sofi 27 December 2021 18:00 | Add x button to clear search box 
	*/
	.dataTables_wrapper .dataTables_filter input::-webkit-search-cancel-button {
		-webkit-appearance: button !important;
		padding: 2px;
		margin-right: 5px;
	}

	/* change input datepicker readonly background color */
	#ifcdate_start, #ifcdate_end {
		background-color: #FFFFFF;
	}

	table#drTableCommunityList tr:hover {
		background-color: #EFBF65;
	}
</style>
<div id="page-content">
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">&nbsp;</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="" href="<?=base_url_admin('community/listing/reported_discussion/')?>" class="btn btn-danger btn-block">
						<i class="fa fa-wechat"></i> Reported Discussion : <?=$discussion_count->jumlah ?>
					</a>
				</div>
				<div class="btn-group pull-right" style="margin-right:1em">
					<!-- <a id="" href="<?=base_url_admin('community/listing/reported/')?>" class="btn btn-danger btn-block"> -->
					<a id="reported_post_list" href="#" class="btn btn-danger text-left"><i class="fa fa-wechat"></i> Reported Post : <?=$count->jumlah ?></a>
						
					</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Community</li>
		<li>List</li>
		<li><span style="display: none;" id="admin_name"><?=$admin_name?></span></li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block">
		<div class="block-title">
			<h2><strong>Filter</strong></h2>
		</div>
		<div class="block-section">
			<div class="row" style="display:flex; align-items:flex-end">
				<div class="col-md-2">
					<label for="ifcdate_min">From Date</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input id="ifcdate_start" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="From date" readonly />
					</div>
				</div>
				<div class="col-md-2">
					<label for="ifcdate_max">To Date</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input id="ifcdate_end" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="To date" readonly />
					</div>
				</div>
				<div class="col-md-3" style="margin-bottom:6px;">
					<label for="select_customer">User</label>
					<select id="select_customer" class="form-control">
						<option value="">-- Select User --</option>
					</select>
				</div>
				<div class="col-md-2">
					<label for="ifproduk_status">Status</label>
					<select id="ifproduk_status" class="form-control">
						<option value="">-- View All --</option>
						<option value="active">Active</option>
						<option value="inactive">Inactive</option>
						<option value="reported">Reported</option>
						<option value="takedown">Takedown</option>
					</select>
				</div>
				<div class="col-md-1">
					<button id="apply-filter" class="btn btn-success btn-block">Apply</button>
				</div>
				<div class="col-md-1">
					<!-- by Muhammad Sofi 27 December 2021 14:43 | Fix issue button clear cannot reset filter and reload table -->
					<button id="reset-filter" class="btn btn-block btn-danger">Clear</button>
				</div>
				<div class="col-md-1">
					<button id="refresh-table" class="btn btn-block btn-primary">Refresh</button>
				</div>
			</div>
		</div>
	</div>
	<div class="block full">
		<div class="block-title">
			<h2><strong>Community List</strong></h2>
		</div>
		<div class="table-responsive">
			<table id="drTableCommunityList" class="table table-vcenter table-condensed table-bordered" width="100%">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th class="text-center">#</th>
						<th>Submit Date</th>
						<th>Post Thumbnail</th>
						<th>Title</th>
						<th width="450px">Description</th>
						<th>Community Category</th> <!-- by Muhammad Sofi 29 December 2021 11:34 | show community category on list and detail -->
						<th width="200px">User</th>
						<th width="100px">Status</th>
						<th width="100px">User Id</th>
						<th width="100px">Email</th>
						<th width="100px">Creator Name</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
	<!-- END Content -->
</div>
