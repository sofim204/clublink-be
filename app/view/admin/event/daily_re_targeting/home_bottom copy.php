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
								[
								{
									"targets": [1], <!-- hide column -->
									"visible": false,
									"searchable": false
								},
								{
									"targets": [9], <!-- hide column -->
									"visible": false,
									"searchable": false
								},
								],
			"order"				: [[ 2, "desc" ]],
			//"stateSave"			: stateStatus,
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			//"pageLength"		: getLastPagination,
			//"iPageStart"		:2,
			//"iDisplayStart"		: getLastPagination, // from start data, page 1 start from array 0, page 2 start from array 10
			"sAjaxSource"		: "<?=base_url("api_admin/event/daily_new_user/")?>",
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
					$('#drTable > tbody').off('mouseover', 'tr');
					$('#drTable > tbody').on('mouseover', 'tr', function (e) {
						e.preventDefault();
						//var id = $(this).find("td").html();
						var currentRow = $(this).closest("tr");
						var id = $('#drTable').DataTable().row(currentRow).data()[1]; <!-- to get data from specific column, change this "data()[id_column]" -->
						<!-- console.log($('#drTable').DataTable().row(currentRow).data()); -->
						ieid = id;

						var status = $('#drTable').DataTable().row(currentRow).data()[10];						

						if (status == 'pending') {							
							$(".bpending").on("click", (e) => {
								console.log($(".bpending").data("idc"));
								console.log(id);
								e.preventDefault();
								var url = '<?=base_url()?>api_admin/event/daily_new_user/detail/'+id;
								NProgress.start();

								$("#b_accepted").hide();
								$("#b_rejected").hide();
								$("#b_finished").hide();
								$("#modal_pending").modal("show");
								
								$("#ivnama").html("<i>Loading . . .</i>");
								$("#ivdate").html("<i>Loading . . .</i>");
								$("#ivday1").html("<i>Loading . . .</i>");
								$("#ivday2").html("<i>Loading . . .</i>");
								$("#ivday3").html("<i>Loading . . .</i>");

								$.get(url).done(function(response){
									NProgress.done();
									if(response.status==200){
										var dta = response.data;
										<!-- $("#ivid").text(dta.id); -->
										$("#ivnama").text(dta.user);
										$("#ivdate").text(dta.cdate);
										$("#ivday1").text(dta.day1);
										$("#ivday2").text(dta.day2);
										$("#ivday3").text(dta.day3);
										$("#b_accepted").show();
										$("#b_rejected").show();										
									}else{
										gritter('<h4>Failed</h4><p>Cant fetch data right now, please try again later</p>','danger');
									}
								}).fail(function(){
									NProgress.done();
									gritter('<h4>Error</h4><p>Cant fetch data right now, please try again later</p>','Warning');
								});
							});
						} else if (status == 'accepted') {
							$("#baccepted").on("click", (e) => {
								e.preventDefault();
								$("#modal_notification").modal("show");
							});
						} else {
							
						}






						
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

$("#modal_pending").on("hidden.bs.modal",function(e){
    $("#ivnama").text("");
    $("#ivcdate").text("");
    $("#ivday1").text("");
    $("#ivday2").text("");
    $("#ivday3").text("");
	
	$("#modal_pending").find("form").trigger("reset");
});

<!-- ACCEPTED -->
$("#b_accepted").on("click", (e) => {
    e.preventDefault();
	$("#modal_pending").modal("hide");
});

<!-- SUCCESS -->
$("#b_success_option_1").on("click", (e) => {
    e.preventDefault();
    let confirm_message = confirm("Are you sure to accept this data?"); // same like takedown
    if(confirm_message) {
        NProgress.start();
        var url = '<?=base_url() ?>api_admin/event/daily_new_user/accepted/?id=' +ieid;
        $.get(url).done(function(response){
            NProgress.done();
            if(response.status=="200"){
                gritter('<h4>Success</h4><p>Successfully Accepted</p>','success');
                setTimeout(function(){
                    drTable.ajax.reload(null, false);
					$("#modal_pending").modal("hide");
                }, 400);
            }else{
                gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
				setTimeout(function(){
					drTable.ajax.reload(null, false);
					$("#modal_pending").modal("hide");
					$("#modal_pending").find("form").trigger("reset");
				}, 1000);
            }
        }).fail(function() {
            NProgress.done();
            gritter('<h4>Error</h4><p>Error, Please try again</p>','danger');
			setTimeout(function(){
				drTable.ajax.reload(null, false);
				$("#modal_pending").modal("hide");
				$("#modal_pending").find("form").trigger("reset");
			}, 1000);
        });
    } 
});

<!-- PROBLEM -->
$("#b_problem").on("click", (e) => {
    e.preventDefault();
    let confirm_message = confirm("Are you sure this data problem?"); // same like takedown
    if(confirm_message) {
        NProgress.start();
        var url = '<?=base_url() ?>api_admin/event/daily_new_user/problem/?id=' +ieid;
        $.get(url).done(function(response){
            NProgress.done();
            if(response.status=="200"){
                gritter('<h4>Success</h4><p>Post Successfully Problem</p>','success');
                setTimeout(function(){
                    drTable.ajax.reload(null, false);
					$("#modal_notification").modal("hide");
                }, 400);
            }else{
                gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
            }
        }).fail(function() {
            NProgress.done();
            gritter('<h4>Error</h4><p>Error, Please try again</p>','danger');
        });
    } 
});

<!-- REFUND -->
$("#b_refund").on("click", (e) => {
    e.preventDefault();
    let confirm_message = confirm("Are you sure this data refund?"); // same like takedown
    if(confirm_message) {
        NProgress.start();
        var url = '<?=base_url() ?>api_admin/event/daily_new_user/refund/?id=' +ieid;
        $.get(url).done(function(response){
            NProgress.done();
            if(response.status=="200"){
                gritter('<h4>Success</h4><p>Successfully Refund</p>','success');
                setTimeout(function(){
                    drTable.ajax.reload(null, false);
					$("#modal_notification").modal("hide");
                }, 400);
            }else{
                gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
            }
        }).fail(function() {
            NProgress.done();
            gritter('<h4>Error</h4><p>Error, Please try again</p>','danger');
        });
    } 
});

$("#bdownload_xls").on("click",function(e){
	e.preventDefault();
	var cdate_start = $("#ifcdate_start").val();
	var cdate_end = $("#ifcdate_end").val();
	var status = $("#ifstatus").val();
	if (cdate_start == '' || cdate_start == null) {
		alert('From Date must be fill')
	} else if (cdate_end == '' || cdate_end == null) {
		alert('To Date must be fill')
	} else {
		var url = '<?=base_url("api_admin/event/daily_new_user/download_xls/?")?>';
		url += 'cdate_start='+encodeURIComponent(cdate_start);
		url += '&cdate_end='+encodeURIComponent(cdate_end);
		url += '&status='+encodeURIComponent(status);
		url += '&type=general';
		window.location = url;
	}
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
		var url = '<?=base_url("api_admin/event/daily_new_user/download_xls/?")?>';
		url += 'cdate_start='+encodeURIComponent(cdate_start);
		url += '&cdate_end='+encodeURIComponent(cdate_end);
		url += '&status='+encodeURIComponent(status);
		url += '&type=agent';
		window.location = url;
	}
});