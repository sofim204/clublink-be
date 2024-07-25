var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';

var drTable = {};
var ieid = '';
var ieprovinsi = '';
var iekabkota = '';
var iekecamatan = '';
var stateStatus = false;

function gritter(pesan,jenis='info'){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 3500,
		allow_dismiss: true
	});
}

App.datatables();

const geturl = window.location.href; //get current url
const lastPageUrl = document.referrer; //get last page url

if(lastPageUrl.includes('ecommerce/pelanggan') || lastPageUrl.includes('ecommerce/pelanggan/detail')) {
	//stateStatus = true;
	//localStorage.getItem("lastpagination");
} else {
	localStorage.setItem("lastpagination", 0);
	//stateStatus = false;
}

var getLastPagination = localStorage.getItem("lastpagination");

if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
			"columnDefs"		: 
								[{
									"targets": [0], <!-- hide column -->
									"visible": false,
									"searchable": false
								},
								{
									"targets": [1], <!-- hide column -->
									"visible": false,
									"searchable": false
								},
								{
									"targets": [11], <!-- hide column -->
									"visible": false,
									"searchable": false
								}
								],
			"order"				: [[ 2, "desc" ]],
			//"stateSave"			: stateStatus,
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			//"pageLength"		: getLastPagination,
			//"iPageStart"		:2,
			//"iDisplayStart"		: getLastPagination, // from start data, page 1 start from array 0, page 2 start from array 10
			"sAjaxSource"		: "<?=base_url("api_admin/redemptionexchange/success/")?>",
			"fnServerParams"	: function ( aoData ) {
				aoData.push(
					{ "name": "from_date", "value": $("#ifcdate_start").val() },
					{ "name": "to_date", "value": $("#ifcdate_end").val() },
					{ "name": "status", "value": $("#ifstatus").val() },
				);
			},
			"drawCallback": function( settings ) {
				var table = $('#drTable').DataTable();
				var info = table.page.info();
			//	//console.log(info);
			//	//alert("page " + info.start);
			//	//alert( 'Now on page'+ this.fnPagingInfo().iPage );
				localStorage.setItem("lastpagination", info.start);
			},
			"fnServerData"		: function (sSource, aoData, fnCallback, oSettings) {
				//console.log(aoData);
				oSettings.jqXHR = $.ajax({
					dataType 	: 'json',
					method 		: 'POST',
					url 		: sSource,
					data 		: aoData
				}).success(function (response, status, headers, config) {
					NProgress.done();
					$('#drTable > tbody').off('click', 'tr');
					$('#drTable > tbody').on('click', 'tr', function (e) {
						e.preventDefault();
						//var id = $(this).find("td").html();
						var currentRow = $(this).closest("tr");
						var id = $('#drTable').DataTable().row(currentRow).data()[1]; <!-- to get data from specific column, change this "data()[id_column]" -->
						ieid = id;
						var url = '<?=base_url()?>api_admin/redemptionexchange/success/detail/'+id;
						NProgress.start();
						$("#modal_notification").modal("show");
						
						$("#ivnama").html("<i>Loading . . .</i>");
						$("#ivtype").html("<i>Loading . . .</i>");
						$("#ivre_name").html("<i>Loading . . .</i>");
						$("#ivtelp").html("<i>Loading . . .</i>");
						$("#ivcost_spt").html("<i>Loading . . .</i>");
						$("#ivamount_get").html("<i>Loading . . .</i>");
						$("#ivcdate").html("<i>Loading . . .</i>");

						$("#ivunama").html("<i>Loading . . .</i>");
						$("#ivuemail").html("<i>Loading . . .</i>");
						$("#ivureg_date").html("<i>Loading . . .</i>");
						$("#ivutelp").html("<i>Loading . . .</i>");
						$("#ivutotal_recruited").html("<i>Loading . . .</i>");
						$("#ivuis_influencer").html("<i>Loading . . .</i>");
						$("#ivuwallet_balance").html("<i>Loading . . .</i>");
						$("#ivuip_address").html("<i>Loading . . .</i>");
						$("#ivupermanent_inactive").html("<i>Loading . . .</i>");
						$("#ivurecommender").html("<i>Loading . . .</i>");
						$("#ivudevice_id").html("<i>Loading . . .</i>");
						$("#ivuaddress").html("<i>Loading . . .</i>");
						$("#ivusignup_method").html("<i>Loading . . .</i>");
						$.get(url).done(function(response){
							NProgress.done();
							if(response.status==200){
								var dta = response.data;
								$("#ivid").text(dta.id);
								$("#ivnama").text(dta.user);
								$("#ivtype").text(dta.type);
								$("#ivre_name").text(dta.redemption_exchange_name);
								$("#ivtelp").text(dta.telp);
								$("#ivcost_spt").text(dta.cost_spt);
								$("#ivamount_get").text(dta.amount_get);
								$("#ivcdate").text(dta.cdate);

								$("#ivunama").text(dta.user_name);
								$("#ivuemail").text(dta.user_email);
								$("#ivureg_date").text(dta.user_reg_date);
								$("#ivutelp").text(dta.user_telp);
								$("#ivutotal_recruited").text(dta.user_total_recruited);
								$("#ivuis_influencer").text(dta.user_is_influencer);
								$("#ivuwallet_balance").html("<b>"+dta.user_wallet_balance+"</b>");
								$("#ivuip_address").text(dta.user_ip_address);
								$("#ivupermanent_inactive").text(dta.user_permanent_inactive);
								$("#ivurecommender").text(dta.user_recommender);
								$("#ivudevice_id").text(dta.user_device_id);
								$("#ivuaddress").text(dta.user_address);
								$("#ivusignup_method").text(dta.user_signup_method);
								
							}else{
								gritter('<h4>Failed</h4><p>Cant fetch data right now, please try again later</p>','danger');
							}
						}).fail(function(){
							NProgress.done();
							gritter('<h4>Error</h4><p>Cant fetch data right now, please try again later</p>','Warning');
						});
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter('<h4>Error</h4><p>Cant fetch customer detail right now, please try again later</p>','info');
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Search by name, email or telp').css({'width':'250px', 'display':'inline-block'}); <!-- by Muhammad Sofi 29 December 2021 15:00 | resize search box -->
	$("#fl_reset").on("click",function(e){
		e.preventDefault();
		$("#ifcdate_start").val("");
		$("#ifcdate_end").val("");
		$("#ifstatus").val("");
		drTable.search('').columns().search('').draw(); <!-- by Muhammad Sofi 29 December 2021 15:00 | clear search box on click reset button -->
		drTable.ajax.reload(null,true);
	});
	$("#fl_button").on("click",function(e){
		e.preventDefault();
		drTable.ajax.reload(null,true);
	});
}

$("#modal_notification").on("hidden.bs.modal",function(e){
    $("#ivnama").text("");
    $("#ivtype").text("");
    $("#ivre_name").text("");
    $("#ivtelp").text("");
    $("#ivcost_spt").text("");
    $("#ivamount_get").text("");
    $("#ivcdate").text("");

	$("#ivunama").text("");
	$("#ivuemail").text("");
	$("#ivureg_date").text("");
	$("#ivutelp").text("");
	$("#ivutotal_recruited").text("");
	$("#ivuis_influencer").text("");
	$("#ivuwallet_balance").text("");
	$("#ivuip_address").text("");
	$("#ivupermanent_inactive").text("");
	$("#ivurecommender").text("");
	$("#ivudevice_id").text("");
	$("#ivuaddress").text("");
	$("#ivusignup_method").text("");
	
	$("#modal_notification").find("form").trigger("reset");
});

$("#bdownload_xls_agent").on("click",function(e){
	e.preventDefault();
	var cdate_start = $("#ifcdate_start").val();
	var cdate_end = $("#ifcdate_end").val();
	var status = $("#ifstatus").val();
	if (cdate_start == '' || cdate_start == null) {
		alert('From Date must be fill')
	} else if (cdate_end == '' || cdate_end == null) {
		alert('To Date must be fill')
	} else {
		var url = '<?=base_url("api_admin/redemptionexchange/success/download_xls/?")?>';
		url += 'cdate_start='+encodeURIComponent(cdate_start);
		url += '&cdate_end='+encodeURIComponent(cdate_end);
		<!-- url += '&status='+encodeURIComponent(status); -->
		<!-- url += '&type=agent'; -->
		window.location = url;
	}
});