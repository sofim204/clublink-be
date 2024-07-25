<style>
	.bordered {
		border: 1px #ededed solid;
	}
	.mp1 {
		padding: 1em;
	}
	.first-section{
		height: 230px;
		overflow:none;
		overflow-y:auto;
		padding:0 2em;
	}
	.attachment-section{
		/* height: 300px; */
		height: auto;
		overflow:none;
		overflow-y:auto;
		padding:0 2em;
	}
	.likes-section{
		height: 350px;
		overflow:none;
		overflow-y:auto;
		padding:0 2em;
	}
	.third-section{
		height:350px;
		/* max-height:350px; */
		overflow:none;
		overflow-y:auto;
		padding:0 2em;
	}
	.like-container {
		display: flex;
		justify-content: flex-start;
		align-items: center;
		/* border: 1px dashed black; */
	}
	.like-container img{
		margin-right : 1em;
	}
	.discuss-container{
		align-items: center;
		/* border: 1px dashed black; */
		display: flex;
		justify-content: space-between;
		margin: 1em 0;
		padding: 1em;
		cursor: pointer;
	}
	.discuss-container:hover{
		background-color:#EEE;
	}
	.discuss-container.level-0{
		padding:1em;
		padding-left:1em;
	}
	.discuss-container.level-1{
		padding:1em;
		padding-left:5em;
	}
	.discuss-title{
		font-size:8pt;
		border-radius:1em;
		padding: .5em 1em;
		border:2px solid #555;
	}
	.discuss-title.warning{
		background-color:orange;
	}
	.discuss-title.danger{
		background-color:red;
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
</style>
<?php 
	if(isset($post_image) && !empty($post_image)) {
		foreach ($post_image as $foto) {
			$filetype = $foto->jenis;
			if($filetype == "image") {
				$title_section = "Post Attachment Image";
			} else if ($filetype == "video") {
				$title_section = "Post Attachment Video [" .$total_video. " Video and " .$total_uploading_image. " Uploading Image]";
			} else {
				$title_section = "Post Attachment";
			}
		}	
	} else {
		$title_section = "Post Attachment";
	}
?>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">
				<div class="btn-group" style="display: none;">
					<!-- <a id="" href="<?=base_url_admin('community/listing/'); ?>" class="btn btn-back"><i class="fa fa-chevron-left"></i> Back</a> -->
					<button id="b_report_post" type="button" class="btn btn-warning">Report Post</button>
					<div class="" id="message_status_report" style="display:none; background-color: #ed9111; font-size: 14px; font-weight: 500; color: white; padding: 6px 12px; border-radius: 4px;">This Post Already Reported</div>
				</div>
				<div class="btn-group">
					<button id="b_delete_post" type="button" class="btn btn-danger">Delete Post</button>
					<div class="" id="message_status_delete" style="display:none; background-color: #e60e0e; font-size: 14px; font-weight: 500; color: white; padding: 6px 12px; border-radius: 4px;">This Post Already Deleted</div>
				</div>
			</div>
			<div class="col-md-6" style="display:none">
				<div class="btn-group pull-right">
					<a id="" href="<?=base_url_admin('community/listing/edit/'.$list_post->id); ?>" class="btn btn-info"><i class="fa fa-edit"></i> Edit</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Community</li>
		<li>List</li>
		<li><?=$list_post->title?></li>
		<li><span style="display: none;" id="status_report"><?=$list_post->is_report?></span></li>
		<li><span style="display: none;" id="status_delete"><?=$list_post->is_take_down?></span></li>
		<li><span style="display: none;" id="admin_name"><?=$admin_name?></span></li>
	</ul>
	<!-- END Static Layout Header -->
	
	<!-- Content -->
	<!-- Main Row -->
	
	<div class="col-md-12">
		<!-- <pre>
			<--?=print_r($list_post)?>
		</pre> -->
	</div>
	<div class="row">
		<div class="col-md-5">
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-users"></i> <strong>Community Information</strong></h2>
				</div>
				<div class="block-section first-section">
					<table class="table table-borderless table-striped">
						<tr>
							<th class="col-md-4">#</th>
							<td class="col-md-1">:</td>
							<td><span id="community_id"><?=$list_post->id?></span></td>
						</tr>
						<tr>
							<th class="col-md-4">Title</th>
							<td class="col-md-1">:</td>
							<td><?=$list_post->title?></td>
						</tr>
						<tr>
							<th>Submit Date</th>
							<td>:</td>
							<td><?=$list_post->cdate?></td>
						</tr>
						<tr>
							<th class="col-md-4">User</th>
							<td class="col-md-1">:</td>
							<td><?=$list_post->user?></td>
						</tr>
						<tr>
							<th class="col-md-4">Email</th>
							<td class="col-md-1">:</td>
							<td><?=$list_post->email?></td>
						</tr>
						<tr>
							<th>Address</th>
							<td>:</td>
							<!-- By Muhammad Sofi - 4 November 2021 10:00 | remark code -->
							<td><?=$list_post->address2?></td>
						</tr>
						<tr>
							<th>Status</th>
							<td>:</td>
							<td><?=$list_post->is_active?'Active':'Inactive'?></td>
						</tr>
						<!-- by Muhammad Sofi 29 December 2021 11:34 | show community category on list and detail -->
						<tr>
							<th>Community Category</th>
							<td>:</td>
							<td><?=$list_post->nama?></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class="col-md-7">
			<!-- About Block -->
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-users"></i> <strong>Description</strong></h2>
				</div>
				<div class="block-section first-section">
					<?=$list_post->description?>
				</div>
			</div>
			<!-- About Block -->
		</div>

		<div class="col-md-7">
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-users"></i> <strong>Discussion</strong></h2>
				</div>
				<div class="block-section third-section">
					<div class="row" id="discussionContainer"></div>
				</div>
			</div>
		</div>
		<div class="col-md-5">
			<!-- <div class="block">
				<div class="block-title">
					<h2><i class="fa fa-users"></i> <strong>Post Attachment Image</strong></h2>
				</div>
				<div class="block-section attachment-section text-center">
					<div class="row">
						<?php foreach ($post_image as $foto) {
							$filetype = $foto->jenis;
							if($filetype == "image") {
						?>
							<div class="col-md-4 mp1">
								<div class="bordered">
									<a href="<?=base_url($foto->url); ?>" target="_blank" title="View Detail">
										<img src="<?=base_url($foto->url); ?>" class="img-responsive" />
									</a>
								</div>
							</div>
						<?php } else {} } ?>
					</div>
				</div>
			</div> -->
			<div class="block">
				<div class="block-title">
					<!-- // by Muhammad Sofi - 17 November 2021 17:20 | get total likes and top image -->
					<h2><i class="fa fa-users"></i> <strong>Likes : <?=$list_post->total_likes?></strong></h2>
				</div>
				<div class="block-section likes-section text-left">
					<div class="row">
						<?php foreach ($likes as $like) { ?>
							<div class="col-md-3 mp1">
								<div class="like-container">
									<img src="<?=base_url($like->image_icon); ?>" class="img-responsive" />
									<span><?=$like->user?></span>
								</div>
							</div>	
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-users"></i> <strong>Post Attachment Image</strong></h2>
				</div>
				<div class="block-section text-center">
					<div class="row">
					<?php foreach ($post_image as $foto) {
							$filetype = $foto->jenis;
							if($filetype == "image") {
						?>
							<div class="col-md-4 mp1">
								<div class="bordered">
									<a href="<?=base_url($foto->url); ?>" target="_blank" title="View Detail">
										<img src="<?=base_url($foto->url); ?>" class="img-responsive" />
									</a>
								</div>
							</div>
						<?php } else {} } ?>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-users"></i> <strong>Post Attachment Video [<?= $total_video; ?> Video and <?= $total_uploading_image; ?> Uploading Image]</strong></h2>
				</div>
				<div class="block-section text-center">
					<div class="row">
						<?php foreach ($post_videos as $videos) {
							$filetype = $videos->jenis;
							if($filetype == "video") {
						?>
							<div class="col-md-4">
								<?php 
									$filedata = $videos->url;
									$fileext = strtolower(pathinfo($filedata, PATHINFO_EXTENSION));
									if($fileext == "mp4" || $fileext == "mov") {
								?>
									<video width="320px" height="240px" controls>
										<source src="<?=base_url($videos->url); ?>" type="video/mp4">
									</video>
								<?php } else {} ?>	
							</div>
						<?php } else {} } ?>
					</div>
				</div>
			</div>
		</div>
		<!-- <div class="col-md-12">
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-users"></i> <strong><?=$title_section?></strong></h2>
				</div>
				<div class="block-section attachment-section">
					<div class="row">
						<?php foreach ($post_image as $foto) {
							$filetype = $foto->jenis;
							if($filetype == "image") {
						?>
							<div class="col-md-4 mp1">
								<div class="bordered">
									<a href="<?=base_url($foto->url); ?>" target="_blank" title="View Detail">
										<img src="<?=base_url($foto->url); ?>" class="img-responsive" />
									</a>
								</div>
							</div>
						<?php } else if($filetype == "video") { ?>
							<div class="col-md-4">
								<?php 
									$filedata = $videos->url;
									$fileext = strtolower(pathinfo($filedata, PATHINFO_EXTENSION));
									if($fileext == "mp4" || $fileext == "mov") {
								?>
									<video width="320px" height="240px" controls>
										<source src="<?=base_url($videos->url); ?>" type="video/mp4">
									</video>
								<?php } else {} ?>	
							</div>
						<?php } else { ?>
						<?php } } ?>
					</div>
				</div>
			</div>
		</div> -->
	</div>
	<!-- <pre>
		< ?=print_r($likes)?>
	</pre> -->
	<!-- END Main Row
</div>	