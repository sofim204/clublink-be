<style>
	.bordered {
		border: 1px #ededed solid;
	}
	.mp1 {
		padding: 1em;
	}
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">
				<div class="btn-group">
					<a id="" href="<?=base_url_admin('community/discussion/'); ?>" class="btn btn-default"><i class="fa fa-chevron-left"></i> Kembali</a>
				</div>
			</div>
			<div class="col-md-6" style="display:none">
				<div class="btn-group pull-right">
					<a id="" href="<?=base_url_admin('community/discussion/edit/'.$list_post->id); ?>" class="btn btn-info"><i class="fa fa-edit"></i> Edit</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Community</li>
		<li>Discussion</li>
		<li><?=$list_post->title?></li>
	</ul>
	<!-- END Static Layout Header -->
	
	<!-- Content -->
	<!-- Main Row -->
	<div class="row">
		<div class="col-md-8">
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-users"></i> <strong>Discussion Information</strong></h2>
				</div>
				<div class="block-section ">
					<table class="table table-borderless table-striped">
						<tr>
							<th class="col-md-4">Discussion Title</th>
							<td class="col-md-1">:</td>
							<td><?=$list_post->title?></td>
						</tr>
						<tr>
							<th>Community Post</th>
							<td>:</td>
							<td><?=$list_post->community_title?></td>
						</tr>
						<tr>
							<th>Category</th>
							<td>:</td>
							<td><?=$list_post->category?></td>
						</tr>
						<tr>
							<th>Status</th>
							<td>:</td>
							<td><?=$list_post->is_active?'Active':'Inactive'?></td>
						</tr>
						<tr>
							<th>Report</th>
							<td>:</td>
							<td><?=$list_post->is_report?'Reported':'Open'?></td>
						</tr>
						<tr>
							<th>Takedown</th>
							<td>:</td>
							<td><?=$list_post->is_take_down?'Taken Down':'Open'?></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<!-- About Block -->
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-users"></i> <strong>User Information</strong></h2>
				</div>
				<div class="block-section ">
					<table class="table table-borderless table-striped">
						<tr>
							<th class="col-md-4">User Name</th>
							<td class="col-md-1">:</td>
							<td><?=$list_post->user?></td>
						</tr>
						<tr>
							<th>Type</th>
							<td>:</td>
							<td><?=$list_post->user_type?></td>
						</tr>
						<!-- By Muhammad Sofi - 4 November 2021 10:00 | remark code -->
						<tr>
							<th>Address 2</th>
							<td>:</td>
							<td><?=$list_post->address2?></td>
						</tr>
					</table>
				</div>
			</div>
			<!-- About Block -->
		</div>

		<div class="col-md-12">
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-users"></i> <strong>Discussion Attachment</strong></h2>
				</div>
				<div class="block-section text-center">
					<div class="row">
						<?php foreach ($post_image as $foto) { ?>
							<div class="col-md-2 mp1">
								<div class="bordered">
									<a href="<?=base_url($foto->url); ?>" target="_blank" title="View Detail">
										<img src="<?=base_url($foto->url); ?>" class="img-responsive" />
									</a>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- END Main Row -->
</div>	