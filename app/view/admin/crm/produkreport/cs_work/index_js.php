var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';
var buser_id = ''; // user_id_reporter
App.datatables();
function gritter(pesan,jenis='info'){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 500,
		allow_dismiss: true
	});
}

if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
			"columnDefs"		: [{
									"targets": [1], <!-- hide column -->
									"visible": false,
									"searchable": false
								},{
									"targets": [2], <!-- hide column -->
									"visible": false,
									"searchable": false
								}],	
			"order"				: [[ 0, "asc" ]],//changed table sort to created date by Rendi Fajrianto - 26 october 2020 17:22
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/crm/produkreport/")?>",
			"fnServerParams": function ( aoData ) {
				aoData.push(
                    { "name": "b_kategori_id", "value": $("#ifb_kategori_id").val() },
					{ "name": "price_min", "value": $("#ifprice_min").val() },
					{ "name": "price_max", "value": $("#ifprice_max").val() },
					{ "name": "b_kondisi_id", "value": $("#ifb_kondisi_id").val() },
					{ "name": "courier_service", "value": $("#if_courier_service").val() },
					{ "name": "free_ship", "value": $("#iffree_ship").val() },
					{ "name": "reported_status", "value": $("#if_reported_status").val() },
                    { "name": "p_admin_name", "value": $("#input_admin_name").val() },
					{ "name": "from_date", "value": $("#from_date").val() },
					{ "name": "end_date", "value": $("#to_date").val() },
				);
			},
			"fnServerData"	: function (sSource, aoData, fnCallback, oSettings) {
				oSettings.jqXHR = $.ajax({
					dataType 	: 'json',
					method 		: 'POST',
					url 		: sSource,
					data 		: aoData
				}).success(function (response, status, headers, config) {
					console.log(response);
					NProgress.done();
					$('#drTable > tbody').off('click', 'tr');
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter('<h4>Error</h4><p>Cant fetch product data right now, please try again later</p>','warning');
				});
			},
	});

	$("#fl_reset").on("click",function(e){
		e.preventDefault();
		$("#input_admin_name").val("");
		$("#from_date").val("");
		$("#to_date").val("");
		drTable.ajax.reload();
	})
}

<!-- start  by ali - 18 january 2023 14:42 add export excel-->
$("#fl_download").on("click", function(e) {
    e.preventDefault();
    if ($("#input_admin_name").val() == null || $("#input_admin_name").val() == "") {
        alert("Name admin cannot be null");
    } else if($("#from_date").val() == null || $("#from_date").val() == "") {
        alert("From date cannot be null");
	} else if($("#to_date").val() == null || $("#to_date").val() == "") {
        alert("To date cannot be null");
	} else {
        $.ajax({
            url: '<?=base_url("a/crm/produkreport/exportExcel")?>',
            type: "POST",
            dataType: "json",
            data: {
                export: true,
                admin_name: $("#input_admin_name").val()
            },
            success: function(data){
                var $a = $("<a>");

                $a.attr("href",data.file);
                $("body").append($a);
                $a.attr("download", data.filename);
                $a[0].click();
                $a.remove();
            },	
            error: function(data){
				console.log(data);
            }
        });
    }
});

$("#bfilter").on("click",function(e){
	e.preventDefault();
	drTable.ajax.reload();
});

$('#from_date, #to_date').datepicker('setDate', 'today').val("");

<!-- end  by ali - 18 january 2023 14:42 add export excel-->

