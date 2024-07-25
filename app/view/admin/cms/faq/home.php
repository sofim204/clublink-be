<style>
#div_faqs h4 {
	font-size: 2em;
	font-weight: bolder;
	letter-spacing: -0.03em;
}
#div_faqs .std h1 {
	font-size: 1.9em;
}
#div_faqs .std h2 {
	font-size: 1.6em;
}
#div_faqs .std h4 {
	font-size: 1.4em;
}
#div_faqs .std h5 {
	font-size: 1.1em;
}
#div_faqs .std h6 {
	font-size: 1em;
}
#div_faqs .std p {
	font-size: 0.9em;
}

</style>
<div id="page-content">
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">&nbsp;</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="afaq_new" href="#" class="btn btn-info"><i class="fa fa-plus"></i> New FAQ</a>
				</div>
				<div class="btn-group pull-right" style="margin-right:1em">
					<a href="<?php echo base_url_admin('cms/faq/faqMobile/2')?>" target="_blank" class="btn btn-primary"> RESULT Indo</a>
				</div>
				<div class="btn-group pull-right" style="margin-right:1em">
					<a href="<?php echo base_url_admin('cms/faq/faqMobile/1')?>" target="_blank" class="btn btn-primary"> RESULT</a>
				</div>
			</div>
		</div>
	</div>

	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>CMS</li>
		<li>FAQ</li>
	</ul>
	<!-- END Static Layout Header -->

	<div class="row">
		<div class="col-md-6">
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-users"></i> <strong>Frequently Asked Questions</strong></h2>
				</div>
				<div class="block-section first-section">
					<table id="tableFAQ" class="table table-borderless table-striped">
						<?php foreach ($list_faq as $faq) { ?>
							<tr>
								<td class="pull-left"><?=$faq->title?></td>
								<td>
									<a href="javascript:void(0)" class="btn btn-info" style="display: inline-block;" title="Edit" onclick="EditData('<?=$faq->id?>');"><i class="fa fa-edit"></i>&nbsp;Edit&nbsp;&nbsp;&nbsp;&nbsp;</a>
									<a href="javascript:void(0)" class="btn btn-danger" style="display: inline-block;" title="Delete" onclick="DeleteData('<?=$faq->id?>');"><i class="fa fa-trash-o"></i>&nbsp;Delete</a>
								</td>
							</tr>
							<tr>
								<!-- by Muhammad Sofi 21 January 2022 20:53 | add insert priority and sort by priority -->
								<td><?=$faq->content?></td>
								<td><strong>Priority : <?=$faq->priority?></strong></td> 
							</tr>
						<?php } ?>	
					</table>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-users"></i> <strong>Pertanyaan yang Sering Ditanyakan</strong></h2>
				</div>
				<div class="block-section first-section">
					<table id="tableFAQ" class="table table-borderless table-striped">
						<?php foreach ($list_faq_indo as $faq_indo) { ?>
							<tr>
								<td class="pull-left"><?=$faq_indo->title?></td>
								<td>
									<a href="javascript:void(0)" class="btn btn-info" style="display: inline-block;" title="Edit" onclick="EditData('<?=$faq_indo->id?>');"><i class="fa fa-edit"></i>&nbsp;Edit&nbsp;&nbsp;&nbsp;&nbsp;</a>
									<a href="javascript:void(0)" class="btn btn-danger" style="display: inline-block;" title="Delete" onclick="DeleteData('<?=$faq_indo->id?>');"><i class="fa fa-trash-o"></i>&nbsp;Delete</a>
								</td>
							</tr>
							<tr>
								<!-- by Muhammad Sofi 21 January 2022 20:53 | add insert priority and sort by priority -->
								<td><?=$faq_indo->content?></td>
								<td><strong>Priority : <?=$faq_indo->priority?></strong></td> 
							</tr>
						<?php } ?>	
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
