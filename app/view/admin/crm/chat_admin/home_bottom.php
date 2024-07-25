
var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var api_url = '<?=base_url('api_admin/crm/chat_admin'); ?>';
var drTable = {};
var ieid = '';
App.datatables();
function gritter(pesan,jenis='info'){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 2500,
		allow_dismiss: true
	});
} 

if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		$("#modal-preloader").modal("hide");
		//$("#modal-preloader").modal("show");
	}).DataTable({
			"columns"				: [
		    null,
		    null,
		    null,
		    null,
		    null,
		    { "width": "60%" },
				{ "orderable": false }
	  	],
			"scrollX"				: true,
			"order"					: [[ 1, "desc" ]],
			"responsive"	  : true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/crm/chat_admin"); ?>",
			"fnServerData"	: function (sSource, aoData, fnCallback, oSettings) {
				oSettings.jqXHR = $.ajax({
					dataType 	: 'json',
					method 		: 'POST',
					url 		: sSource,
					data 		: aoData
				}).success(function (response, status, headers, config) {
					console.log(response);
					$("#modal-preloader").modal("hide");
					//$('body').addClass('loaded');

					<!-- by Donny Dennison - 26 january 2021 17:36 -->
					<!-- change chat to open chatting -->
					/* $('#drTable > tbody').off('click', 'tr');
					$('#drTable > tbody').on('click', 'tr', function (e) { */
					$('#drTable > tbody').off('click', 'button');
					$('#drTable > tbody').on('click', 'button', function (e) {

						e.preventDefault();

						<!-- by Donny Dennison - 26 january 2021 17:36 -->
						<!-- change chat to open chatting -->
						//var id = $(this).find("td").html();
						var id = $(this).attr("data-id");

						ieid = id;

						<!-- by Donny Dennison - 26 january 2021 17:36 -->
						<!-- change chat to open chatting -->
						$("#adetail").attr("href",'<?=base_url_admin('crm/chat_admin/detail/')?>'+ieid);

						$("#modal_option").modal("show");
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					$("#modal-preloader").modal("hide");
					//console.log(response, response.responseText);
					//$('body').addClass('loaded');
					alert("Error");
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Search Customer Name');
}

/* //detail
$("#adetail").on("click",function(e){
	e.preventDefault();
	$("#modal_option").modal("hide");
	setTimeout(function(){
		window.location='<?=base_url_admin('crm/chat_admin/detail/')?>'+ieid;
	},333);
}); */

$("#create_room_chat").on("click",function(e){
	e.preventDefault();
	var to_customer = $("#if_to_customer").val();

	if(to_customer){
		var url = '<?=base_url_admin()?>crm/chat_admin/detail/';
		url += '0/ADMIN/'+to_customer;
		window.location = url;
	}else{
		growlPesan = '<h4>Failed</h4><p></p>';
	    growlType = 'danger';
	    gritter('<h4>Failed</h4><p>Please select customer</p>','danger');
	    return false;
	}
});

$(document).ready(function() {

  $("#if_to_customer").select2({
    ajax: { 
      url: "<?= base_url('api_admin/ecommerce/pelanggan/getcustomerajax') ?>",
      type: "post",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          search: params.term, // search term
        };
      },
      processResults: function (response) {
        return {
          results: response
        };
      }
    }
  });

});