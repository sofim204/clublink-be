<div id="page-content">
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>CRM</li>
		<li>Chat Admin</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">
		<div class="block-title">
			<h2><strong><i class="fa fa-wechat"></i>&nbsp;Chat Admin</strong></h2>
		</div>

		<div class="row">

			<div class="col-md-7">&nbsp;</div>
		</div>
		<div class="row" style="margin-bottom:16px;">
			<div class="col-md-3">
				<label class="if_to_customer">To Customer</label>
				<select id="if_to_customer" class="form-control">
						<option value="" selected="selected">Select Customer</option>
				</select>
			</div>
			<div class="col-md-3">
				<label class="if_action">&nbsp;</label>
				<button id="create_room_chat" class="btn btn-success btn-block"><i class="fa fa-download"></i> Create Room Chat</button>
			</div>
		</div>

		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered">
				<thead>
					<tr>
						<th class="text-center">Chat Room ID</th>
						<th>Last Update</th>
						<th>Chat Starter</th>
						<th>Chat Follower</th>
						<th>Last Chat By</th> 
						<th>Last Chat Message</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
	<!-- END Content -->
</div>
