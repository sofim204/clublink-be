<style>
	body {font-family: Arial;}

	/* Style the tab */
	.tab-container {
		position:relative;
		height:750px;
	}

	.tab {
		overflow: hidden;
		border: 1px solid #ccc;
		background-color: #f1f1f1;
	}

	/* Style the buttons inside the tab */
	.tab button {
		background-color: inherit;
		float: left;
		border: none;
		outline: none;
		cursor: pointer;
		padding: 14px 16px;
		transition: 0.3s;
		font-size: 17px;
	}

	/* Change background color of buttons on hover */
	.tab button:hover {
		background-color: #ddd;
	}

	/* Create an active/current tablink class */
	.tab button.active {
		background-color: #ccc;
	}

	/* Style the tab content */
	.tabContents {
		position: absolute;
		left:200%;
		width:100%;
		max-height:720px;
		overflow:hidden;
		overflow-y:auto;
		padding:1em;
		border: 1px solid #ccc;
		border-top: none; 
		transition: all .5s;
	}

	.createRoom{
		display: flex;
		align-items:flex-end;
		margin-bottom:1em;
	}

	.createRoom div{
		padding:0;
		margin:0;
		margin-right:1em;
	}
	table#dataTable tr:hover {
		background-color: #EFBF65;
	}
</style>

<div id="page-content">
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>CRM</li>
		<li>Chat Community</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
    <div class="row">
		<div class="col-md-8">
			<div class="block full">
				<div class="block-title">
					<h2><strong><i class="fa fa-filter"></i>&nbsp;Filter</strong></h2>
				</div>
				<div class="createRoom">
					<div class="col-md-3">
						<label for="from_date">From Date</label>
						<div class="input-group">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input id="from_date" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" value="<?= $from_date; ?>" placeholder="From date" />
						</div>
					</div>
					<div class="col-md-3">
						<label for="to_date">To Date</label>
						<div class="input-group">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input id="to_date" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" value="<?= $to_date; ?>" placeholder="To date" />
						</div>
					</div>
					<div class="col-md-4">
						<label class="room_type">Room Type</label>
						<select id="room_type" class="form-control">
							<!-- <option value="" selected="selected">-- View All --</option> -->
							<option value="admin" selected="selected"> Admin </option>
							<option value="buyandsell"> Buy & Sell </option>
							<option value="barter"> Barter </option>
							<option value="offer"> Offer </option>
							<option value="community"> Community </option>
							<option value="private"> Private </option>							
						</select>
					</div>
					<div class="col-md-2">
						<label class="if_action">&nbsp;</label>
						<button id="apply-filter" class="btn btn-success btn-block"><i class="fa fa-check"></i> Apply</button>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="block full">
				<div class="block-title">
					<h2><strong><i class="fa fa-plus"></i>&nbsp;Create Room</strong></h2>
				</div>
				<div class="createRoom">
					<div class="col-md-8">
						<label class="if_to_customer">To Customer</label>
						<select id="if_to_customer" class="form-control">
							<option value="" selected="selected">Select Customer</option>
						</select>
					</div>
					<div class="col-md-4">
						<label class="if_action">&nbsp;</label>
						<button id="create_room_chat" class="btn btn-success btn-block">
							<i class="fa fa-download"></i> Create
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="block full">
		<div class="block-title">
			<h2><strong><i class="fa fa-wechat"></i>&nbsp;Chat</strong></h2>
		</div>
		<div class="tab-container">
			<div class="table-responsive">
				<table id="dataTable" class="table table-vcenter table-condensed table-bordered">
					<thead>
						<tr style="background-color: #FFFFFF;">
							<?php foreach ($table_column as $colName => $colProperty): ?>
								<th width="<?=$colProperty->width?>%"><?=$colName?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<!-- END Content -->
</div>