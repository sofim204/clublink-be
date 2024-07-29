var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var api_url = '<?=$api_url?>';
var drTable = {};
var ieid = '';

function gritter(pesan,jenis='info'){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 3500,
		allow_dismiss: true
	});
}

App.datatables();

<!-- initialize datepicker -->
$('#from_date, #to_date').datepicker();
$('#from_date, #to_date').datepicker('setDate', 'today').val("");

$("#from_date, #to_date").change(function(){
	$('.datepicker').hide(); <!-- hide datepicker after select a date -->
});

if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
			"columnDefs"		: [{
									'targets': 0,
									'checkboxes': {'selectRow': true} 
									}],
			"select"			: {'style': 'multi'},
			"order"				: [[ 0, "desc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"bFilter"			: false,
			"ordering"			: false,

			"sAjaxSource"		: "<?=base_url("api_admin/marketingdailyprogress/")?>",
			"fnServerParams"	: function ( aoData ) {
				aoData.push(
                    { "name": "from_date", "value": $("#from_date").val() },
                    { "name": "to_date", "value": $("#to_date").val() }
				);
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
					console.log(response);
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter('<h4>Error</h4><p>Cant fetch data right now, please try again later</p>','info');
				});
			},
	});
	<!-- $('.dataTables_filter input').attr('placeholder', 'Search by mobile type').css({'width':'250px', 'display':'inline-block'}); -->
	$("#fl_reset").on("click",function(e){
		e.preventDefault();
        $("#from_date" ).datepicker('setDate','');
        $("#to_date" ).datepicker('setDate','');
		drTable.search('').columns().search('').draw();
		drTable.ajax.reload(null,true);
	});
	$("#fl_button").on("click",function(e){
		e.preventDefault();
		drTable.ajax.reload(null,true);
	});
}

$('#from_date, #to_date').datepicker();