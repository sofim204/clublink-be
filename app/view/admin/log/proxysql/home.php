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
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">&nbsp;</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="" href="<?=base_url_admin('game/listing/tambah/')?>" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Log</li>
		<li>List</li>
		<input type="hidden" id="user_role" value="<?=$user_role; ?>" />
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<!-- <div class="block full">

		<div class="block-title">
			<h2><strong>Log List</strong></h2>
		</div>

        <div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">&nbsp;</div>
			<div class="col-md-6">
				<div class="btn-group pull-left">
					<a id=""  href="<?php echo ('http://testlog.sellon.net/') ?>" target="_blank" class="btn btn-info"><i class="fa fa-database"></i> ProxySQL Log</a>
				</div>
			</div>
		</div>

	</div> -->

    <div class="block full overflow-auto">
		<div class="block-title">
			<h2><strong>ProxySQL Log</strong></h2>
		</div>
        <div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-12">
			<p id='data_log'>
            <?php
                foreach($array_tampung as $at){
					echo $at;
				}
            ?>
			</p>
			<h5 id="last_sync"></h5>
			</div>
		</div>  
	</div>
    <div class="block full">
        <div class="row" style="padding: 0.5em 2em;">
			<!-- <div class="col-md-6">&nbsp;</div> -->
			<div class="col-md-6"><h5><b>ProxySQL Log</b></h5></div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="" href="<?=base_url_admin()?>" class="btn btn-info"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
		</div>
    </div>
	<!-- END Content -->
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>
	$( document ).ready(function() {
		// redirect to bottom when open page
		$('html,body').animate({scrollTop: document.body.scrollHeight},"fast");

		// Start set interval reload page
		let time_interval_seconds = 10; // second
		let time_interval_miliseconds = 1000; // milisecond (penambahan)
		let log_off = new Date();
		log_off.setSeconds(log_off.getSeconds() + time_interval_seconds)
		log_off = new Date(log_off)

		waktu_interval();

		function waktu_interval() {
			let int_logoff = setInterval(function(){
				let now = new Date();
				if (now >= log_off){
					// window.location.href = window.location.href;
					fetch_log();
					// console.log('reload at : '+log_off)
					$('#last_sync').text("Last Sync: "+log_off)
					clearInterval(int_logoff)
				}
			}, time_interval_miliseconds);
		}

		// $('body').on('click', function(){
		// 	// console.log('click')
		// 	log_off = new Date()
		// 	log_off.setSeconds(log_off.getSeconds() + time_interval_seconds) 
		// 	log_off = new Date(log_off)
		// 	// console.log(log_off)
		// })

		// $('body').bind('mousemove click mouseup mousedown keydown keypress keyup submit change mouseenter scroll resize dblclick', function () {
		// 	console.log('mousemove')
		// 	log_off = new Date()
		// 	log_off.setSeconds(log_off.getSeconds() + time_interval_seconds) 
		// 	log_off = new Date(log_off)
		// 	console.log(log_off)
		// });

		// $( window ).on( "scroll", function() {
		// 	// console.log('scroll')
		// 	log_off = new Date()
		// 	log_off.setSeconds(log_off.getSeconds() + time_interval_seconds) 
		// 	log_off = new Date(log_off)
		// 	// console.log(log_off)
		// } );
		// End set interval reload page

		function fetch_log() {
			var url = '<?=base_url("api_admin/log/proxysql/readLogJq"); ?>';
			$.ajax({
				url: url,
				type: 'POST',
				data: {  },
				success: function(respon){
					if(respon.status=="200" || respon.status == 200){
						// console.log(respon.data);
						$('#data_log').text('');
						$('#data_log').html(respon.data);

						log_off = new Date();
						log_off.setSeconds(log_off.getSeconds() + time_interval_seconds)
						log_off = new Date(log_off)

		waktu_interval();

					}else{
						growlType = 'danger';
						growlPesan = '<h4>Failed</h4><p>'+respon.message+'</p>';
						// window.location.href = "<?=base_url_admin(); ?>";
					}
				},
				error:function(){
					growlPesan = '<h4>Error</h4><p>Cannot process data right now, please try again later</p>';
					// window.location.href = "<?=base_url_admin(); ?>";
				}
			})
		}
	});

	

</script>
