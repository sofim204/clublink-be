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
					<a id="" href="<?=base_url_admin('community/likes/'); ?>" class="btn btn-default"><i class="fa fa-chevron-left"></i> Kembali</a>
				</div>
			</div>
			<div class="col-md-6" style="display:none">
				<div class="btn-group pull-right">
					<a id="" href="<?=base_url_admin('community/likes/edit/'.$like_post->id); ?>" class="btn btn-info"><i class="fa fa-edit"></i> Edit</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Community</li>
		<li>Like</li>
		<li><?=$like_post->title?></li>
	</ul>
	<!-- END Static Layout Header -->
	
	<!-- Content -->
	<!-- Main Row -->
	
	<div class="col-md-12">
		<!-- <pre>
			<--?=print_r($like_post)?>
		</pre> -->
	</div>
	<div class="row">
		<div class="col-md-8">
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-users"></i> <strong>Like Information</strong></h2>
				</div>
				<div class="block-section ">
					<table class="table table-borderless table-striped">
						<tr>
							<th class="col-md-4">Like Title</th>
							<td class="col-md-1">:</td>
							<td><?=$like_post->title?></td>
						</tr>
						<tr>
							<th>Post Date</th>
							<td>:</td>
							<td><?=$like_post->cdate?></td>
						</tr>
						<tr>
							<th>Deskripsi</th>
							<td>:</td>
							<td><?=$like_post->description?></td>
						</tr>
						<tr>
							<th>Status</th>
							<td>:</td>
							<td><?=$like_post->is_active?'Active':'Inactive'?></td>
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
							<td><?=$like_post->user?></td>
						</tr>
						<!-- By Muhammad Sofi - 4 November 2021 10:00 | remark code-->
						<tr>
							<th>Address 2</th>
							<td>:</td>
							<td><?=$like_post->address2?></td>
						</tr>
					</table>
				</div>
			</div>
			<!-- About Block -->
		</div>
	</div>
	<!-- END Main Row -->
</div>	