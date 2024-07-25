
var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var ieid = '';
var api_url = '<?=base_url('api_admin/band/group_report/detail_group_post'); ?>'+ieid;
var drTable = {};
var drTableParticipant = {};
App.datatables();
function gritter(pesan,jenis='info'){
  $.bootstrapGrowl(pesan, {
    type: jenis,
    delay: 2500,
    allow_dismiss: true
  });
} 
var title = "<?=$title?>";
$('#title-group').text(title);
if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
			"columnDefs"		: [{
									"targets": [0,1,6], <!-- hide column -->
									"visible": false,
									"searchable": false
								}],
			"order"				: [[ 4, "desc" ]], <!-- by Muhammad Sofi 11 January 2022 9:47 | add & edit input priority, show priority in datatable -->
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/band/group_report/detail_group_post/".$ieid); ?>",
			"fnServerData"	: function (sSource, aoData, fnCallback, oSettings) {
				oSettings.jqXHR = $.ajax({
					dataType 	: 'json',
					method 		: 'POST',
					url 		: sSource,
					data 		: aoData
				}).success(function (response, status, headers, config) {
					NProgress.done();
					console.log(response);
					$('#drTable > tbody').off('click', 'tr');
					$('#drTable > tbody').on('click', 'tr', function (e) {
						e.preventDefault();
						//var id = $(this).find("td").html();
						//ieid = id;
						var currentRow = $(this).closest("tr");
						var id = $('#drTable').DataTable().row(currentRow).data()[1]; <!-- to get data from specific column, change this "data()[id_column]" -->
						ieid = id;
						$("#btakedown_post").hide();
						var is_take_down = $('#drTable').DataTable().row(currentRow).data()[6];
						var is_active = $('#drTable').DataTable().row(currentRow).data()[7];
						if (is_take_down == 1 || is_active == 0) {
							$("#btakedown_post").hide();
						}else{
							$("#btakedown_post").show();
						}
						$("#modal_option").modal("show");
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter("<h4>Error</h4><p>Cant fetch data right now, please try again later</p>",'warning');
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Search by desc').css({'width':'250px', 'display':'inline-block'});
}

if(jQuery('#drTableParticipant').length>0){
	drTableParticipant = jQuery('#drTableParticipant')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
			"columnDefs"		: [{
									"targets": [1], <!-- hide column -->
									"visible": false,
									"searchable": false
								}],
			"order"				: [[ 2, "asc" ]], <!-- by Muhammad Sofi 11 January 2022 9:47 | add & edit input priority, show priority in datatable -->
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/band/group_report/detail_participant_group/".$ieid); ?>",
			"fnServerData"	: function (sSource, aoData, fnCallback, oSettings) {
				oSettings.jqXHR = $.ajax({
					dataType 	: 'json',
					method 		: 'POST',
					url 		: sSource,
					data 		: aoData
				}).success(function (response, status, headers, config) {
					NProgress.done();
					console.log(response);
					$('#drTableParticipant > tbody').off('click', 'tr');
					$('#drTableParticipant > tbody').on('click', 'tr', function (e) {
						e.preventDefault();
						//var id = $(this).find("td").html();
						//ieid = id;
						var currentRow = $(this).closest("tr");
						var id = $('#drTableParticipant').DataTable().row(currentRow).data()[1]; <!-- to get data from specific column, change this "data()[id_column]" -->
						ieid = id;
						$("#modal_option_participant").modal("show");
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter("<h4>Error</h4><p>Cant fetch data right now, please try again later</p>",'warning');
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Search by user').css({'width':'250px', 'display':'inline-block'});
}

//detail
$("#adetail").on("click",function(e){
	e.preventDefault();
	$("#modal_option_participant").modal("hide");
	setTimeout(function(){
		//$("#modal_edit").modal("show");
		//window.location = '<?=base_url_admin('ecommerce/pelanggan/detail/')?>'+ieid;

		window.open("<?=base_url_admin('ecommerce/pelanggan/detail/')?>"+ieid, "_blank");
	},333);
});

//takedown
$("#btakedown_post").on("click",function(e){
	e.preventDefault();
	if(ieid){
		var c = confirm('Are you sure takedown this data?');
		if(c){
			NProgress.start();
			var url = '<?=base_url('api_admin/band/group_report/takedown_post/'); ?>'+ieid;
			$.get(url).done(function(response){
				NProgress.done();
				if(response.status==200){
					gritter('<h4>Success</h4><p>Data successfully takedown</p>','success');
				}else{
					gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
				}
				drTable.ajax.reload();
				$("#modal_option").modal("hide");
				<!-- $("#modal_edit").modal("hide"); -->
			}).fail(function() {
				NProgress.done();
				gritter('<h4>Error</h4><p>Cant takedown data right now, please try again later</p>','danger');
			});
		}
	}
});

//detail
$("#bdetail_post").on("click",function(e){
	e.preventDefault();

	$("#modal_detail_post").modal("hide");
	
	$("#title-modal").html("<i>Loading . . .</i>");
	$("#ivdesc_post").html("<i>Loading . . .</i>");
	$("#ivattach_post").html("<i>Loading . . .</i>");

	if(ieid){
		NProgress.start();
		var url = '<?=base_url('api_admin/band/group_report/detail_post/'); ?>'+ieid;
		$.get(url).done(function(response){
			NProgress.done();
			if(response.status==200){
				var dta = response.data;
				<!-- console.log(dta); -->
				$("#title-modal").text("Detail Postingan");
				$("#ivdesc_post").text(dta.post_desc);
				$("#ivattach_post").html(dta.url);
				$("#modal_detail_post").modal("show");
			}else{
				gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
			}
			drTable.ajax.reload();
			<!-- $("#modal_option").modal("hide"); -->
		}).fail(function() {
			NProgress.done();
			gritter('<h4>Error</h4><p>Cant takedown data right now, please try again later</p>','danger');
		});
	}
});
