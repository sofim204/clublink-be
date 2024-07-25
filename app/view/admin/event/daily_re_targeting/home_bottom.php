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
									"targets": [5], <!-- hide column -->
									"visible": false,
									"searchable": false
								},
								{
									"targets": [13], <!-- hide column -->
									"visible": false,
									"searchable": false
								},
								{
									"targets": [14], <!-- hide column -->
									"visible": false,
									"searchable": false
								},
								{
									"targets": [15], <!-- hide column -->
									"visible": false,
									"searchable": false
								},
								{
									"targets": [16], <!-- hide column -->
									"visible": false,
									"searchable": false
								},
								{
									"targets": [17], <!-- hide column -->
									"visible": false,
									"searchable": false
								},
								{
									"targets": [18], <!-- hide column -->
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
			"sAjaxSource"		: "<?=base_url("api_admin/event/daily_re_targeting/")?>",
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
						var url = '<?=base_url()?>api_admin/event/daily_re_targeting/detail/'+id;
						NProgress.start();
						var status = $('#drTable').DataTable().row(currentRow).data()[19];
						var cdate5 = $('#drTable').DataTable().row(currentRow).data()[17];
						var telp = $('#drTable').DataTable().row(currentRow).data()[4];
						var verif_telp_manual = $('#drTable').DataTable().row(currentRow).data()[5];
						<!-- console.log(cdate5); -->

						$("#b_accepted").hide();
						$("#b_rejected").hide();
						$("#b_finished").hide();
						$(".check-telp").hide();
						$(".edit-telp").hide();
						$(".verif-telp").hide();
						$("#modal_detail_event").modal("show");
						$("#modal_reject_note").modal("hide");
						$('#ivurejectnote').text("");
						
						$("#title-modal").html("<i>Loading . . .</i>");
						$("#ivnama").html("<i>Loading . . .</i>");
						$("#ivemail").html("<i>Loading . . .</i>");
						$("#ivtelp").html("<i>Loading . . .</i>");
						$("#ivdate").html("<i>Loading . . .</i>");
						$("#ivday1").html("<i>Loading . . .</i>");
						$("#ivday2").html("<i>Loading . . .</i>");
						$("#ivday3").html("<i>Loading . . .</i>");
						$("#ivday4").html("<i>Loading . . .</i>");
						$("#ivday5").html("<i>Loading . . .</i>");
						$("#ivutelp").val("");
						$("#reject_note").html("");

						$.get(url).done(function(response){
							NProgress.done();
							if(response.status==200){
								var dta = response.data;
								<!-- $("#ivid").text(dta.id); -->
								$("#ivnama").text(dta.user);
								$("#ivemail").text(dta.email);
								$("#ivtelp").html(dta.telp);
								$("#ivdate").text(dta.cdate);
								$("#ivday1").html(dta.day1);
								$("#ivday2").html(dta.day2);
								$("#ivday3").html(dta.day3);
								$("#ivday4").html(dta.day4);
								$("#ivday5").html(dta.day5);
								if (dta.status_redeem_pulsa == 'rejected') {
									$("#reject_note").html(dta.note_rejected);
								}
																		
							}else{
								gritter('<h4>Failed</h4><p>Cant fetch data right now, please try again later</p>','danger');
							}
						}).fail(function(){
							NProgress.done();
							gritter('<h4>Error</h4><p>Cant fetch data right now, please try again later</p>','Warning');
						});
						if (status == 'pending') {
							$("#title-modal").text('Data Pending');
							if (cdate5 != null) {
								if (verif_telp_manual == 0) {
									$("#b_rejected").show();
									$(".check-telp").show();
									$(".edit-telp").show();
									if (telp != '' && telp != null) {
										$(".verif-telp").show();
									}
								}else{
									$("#b_accepted").show();
									$("#b_rejected").show();
								}								
							}
						} else if (status == 'accepted') {
							$("#title-modal").text('Data Accepted');
							$("#b_finished").show();
						} else {
							$("#title-modal").text('Data Detail Event');
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

$("#modal_detail_event").on("hidden.bs.modal",function(e){
    $("#ivnama").text("");
	$("#ivemail").text("");
    $("#ivtelp").html("");
    $("#ivcdate").text("");
    $("#ivday1").text("");
    $("#ivday2").text("");
    $("#ivday3").text("");
    $("#ivday4").text("");
    $("#ivday5").text("");
	$("#ivutelp").val("");
	$('textarea#ivurejectnote').val("");
	$("#reject_note").html("");
	
	$("#modal_detail_event").find("form").trigger("reset");
});

$("#modal_reject_note").on("hidden.bs.modal",function(e){
    $("#ivnama").text("");
	$("#ivemail").text("");
    $("#ivtelp").html("");
    $("#ivcdate").text("");
    $("#ivday1").text("");
    $("#ivday2").text("");
    $("#ivday3").text("");
    $("#ivday4").text("");
    $("#ivday5").text("");
	$("#ivutelp").val("");
	$('textarea#ivurejectnote').val("");
	$("#reject_note").html("");
	
	$("#modal_reject_note").find("form").trigger("reset");
});

<!-- EDIT -->
$("#b_edit_telp").on("click", (e) => {
    e.preventDefault();
	var telp = $("#ivutelp").val();
	var numbers = /^[0-9]+$/;
	
		let confirm_message = confirm("Are you sure to submit this data?"); // same like takedown
		if(confirm_message) {
			if(telp.match(numbers))
			{
				NProgress.start();
				var url = '<?=base_url() ?>api_admin/event/daily_re_targeting/edit_telp/?id=' +ieid+ '&telp=' +telp;
				$.get(url).done(function(response){
					NProgress.done();
					if(response.status=="200"){
						gritter('<h4>Success</h4><p>Successfully Accepted</p>','success');
						setTimeout(function(){
							drTable.ajax.reload(null, false);
							$("#modal_detail_event").modal("hide");
						}, 400);
					}else{
						gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
						setTimeout(function(){
							drTable.ajax.reload(null, false);
							<!-- $("#modal_detail_event").modal("hide"); -->
							$("#modal_detail_event").find("form").trigger("reset");
						}, 1000);
					}
				}).fail(function() {
					NProgress.done();
					gritter('<h4>Error</h4><p>Error, Please try again</p>','danger');
					setTimeout(function(){
						drTable.ajax.reload(null, false);
						$("#modal_detail_event").modal("hide");
						$("#modal_detail_event").find("form").trigger("reset");
					}, 1000);
				});
			}
			else
			{
				alert('Please input numeric characters only');
				return false;
			} 			
		} 	   
});

<!-- VERIF -->
$("#b_verif_telp").on("click", (e) => {
    e.preventDefault();
	let confirm_message = confirm("Are you sure to submit this data?"); // same like takedown
	if(confirm_message) {
		NProgress.start();
		var url = '<?=base_url() ?>api_admin/event/daily_re_targeting/verif_telp/?id=' +ieid;
		$.get(url).done(function(response){
			NProgress.done();
			if(response.status=="200"){
				gritter('<h4>Success</h4><p>Successfully Accepted</p>','success');
				setTimeout(function(){
					drTable.ajax.reload(null, false);
					$("#modal_detail_event").modal("hide");
				}, 400);
			}else{
				gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
				setTimeout(function(){
					drTable.ajax.reload(null, false);
					<!-- $("#modal_detail_event").modal("hide"); -->
					$("#modal_detail_event").find("form").trigger("reset");
				}, 1000);
			}
		}).fail(function() {
			NProgress.done();
			gritter('<h4>Error</h4><p>Error, Please try again</p>','danger');
			setTimeout(function(){
				drTable.ajax.reload(null, false);
				$("#modal_detail_event").modal("hide");
				$("#modal_detail_event").find("form").trigger("reset");
			}, 1000);
		});			
	} 	   
});

<!-- ACCEPTED -->
$("#b_accepted").on("click", (e) => {
    e.preventDefault();
    let confirm_message = confirm("Are you sure to accept this data?"); // same like takedown
    if(confirm_message) {
        NProgress.start();
        var url = '<?=base_url() ?>api_admin/event/daily_re_targeting/accepted/?id=' +ieid;
        $.get(url).done(function(response){
            NProgress.done();
            if(response.status=="200"){
                gritter('<h4>Success</h4><p>Successfully Accepted</p>','success');
                setTimeout(function(){
                    drTable.ajax.reload(null, false);
					$("#modal_detail_event").modal("hide");
                }, 400);
            }else{
                gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
				setTimeout(function(){
					drTable.ajax.reload(null, false);
					$("#modal_detail_event").modal("hide");
					$("#modal_detail_event").find("form").trigger("reset");
				}, 1000);
            }
        }).fail(function() {
            NProgress.done();
            gritter('<h4>Error</h4><p>Error, Please try again</p>','danger');
			setTimeout(function(){
				drTable.ajax.reload(null, false);
				$("#modal_detail_event").modal("hide");
				$("#modal_detail_event").find("form").trigger("reset");
			}, 1000);
        });
    } 
});

<!-- REJECTED -->
$("#b_rejected").on("click", (e) => {
    e.preventDefault();
	$("#modal_reject_note").modal("show");
	$("#modal_detail_event").modal("hide");
	$("#modal_detail_event").find("form").trigger("reset");
});

<!-- REJECTED -->
$("#b_rejected_note").on("click", (e) => {
    e.preventDefault();
	var note = $('textarea#ivurejectnote').val();
    let confirm_message = confirm("Are you sure to reject this data?"); // same like takedown
    if(confirm_message) {
		if (note != '') {
			NProgress.start();
			var url = '<?=base_url() ?>api_admin/event/daily_re_targeting/rejected/?id=' +ieid+ '&note=' +note;
			$.get(url).done(function(response){
				NProgress.done();
				if(response.status=="200"){
					gritter('<h4>Success</h4><p>Successfully Rejected</p>','success');
					setTimeout(function(){
						drTable.ajax.reload(null, false);
						$("#modal_detail_event").modal("hide");
						$("#modal_reject_note").modal("hide");
					}, 400);
				}else{
					gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
					setTimeout(function(){
						drTable.ajax.reload(null, false);
						$("#modal_detail_event").modal("hide");
						$("#modal_detail_event").find("form").trigger("reset");
						$("#modal_reject_note").modal("hide");
						$("#modal_reject_note").find("form").trigger("reset");
					}, 1000);
				}
			}).fail(function() {
				NProgress.done();
				gritter('<h4>Error</h4><p>Error, Please try again</p>','danger');
				setTimeout(function(){
					drTable.ajax.reload(null, false);
					$("#modal_detail_event").modal("hide");
					$("#modal_detail_event").find("form").trigger("reset");
					$("#modal_reject_note").modal("hide");
					$("#modal_reject_note").find("form").trigger("reset");
				}, 1000);
			});
		}else{
			alert('Please input the reject reason!');
			return false;
		}
        
    } 
});

<!-- FINISHED -->
$("#b_finished").on("click", (e) => {
    e.preventDefault();
    let confirm_message = confirm("Are you sure to finish this data?"); // same like takedown
    if(confirm_message) {
        NProgress.start();
        var url = '<?=base_url() ?>api_admin/event/daily_re_targeting/finished/?id=' +ieid;
        $.get(url).done(function(response){
            NProgress.done();
            if(response.status=="200"){
                gritter('<h4>Success</h4><p>Successfully Finished</p>','success');
                setTimeout(function(){
                    drTable.ajax.reload(null, false);
					$("#modal_detail_event").modal("hide");
                }, 400);
            }else{
                gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
				setTimeout(function(){
					drTable.ajax.reload(null, false);
					$("#modal_detail_event").modal("hide");
					$("#modal_detail_event").find("form").trigger("reset");
				}, 1000);
            }
        }).fail(function() {
            NProgress.done();
            gritter('<h4>Error</h4><p>Error, Please try again</p>','danger');
			setTimeout(function(){
				drTable.ajax.reload(null, false);
				$("#modal_detail_event").modal("hide");
				$("#modal_detail_event").find("form").trigger("reset");
			}, 1000);
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
		var url = '<?=base_url("api_admin/event/daily_re_targeting/download_xls/?")?>';
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
		var url = '<?=base_url("api_admin/event/daily_re_targeting/download_xls/?")?>';
		url += 'cdate_start='+encodeURIComponent(cdate_start);
		url += '&cdate_end='+encodeURIComponent(cdate_end);
		url += '&status='+encodeURIComponent(status);
		url += '&type=agent';
		window.location = url;
	}
});