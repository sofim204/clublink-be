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

			"sAjaxSource"		: "<?=base_url("api_admin/ecommerce/customeronly/")?>",
			"fnServerParams"	: function ( aoData ) {
				aoData.push(
					{ "name": "is_confirmed", "value": $("#fl_is_confirmed").val() },
					{ "name": "pelanggan_status", "value": $("#fl_pelanggan_status").val() }
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
					$('#drTable > tbody').off('click', 'tr');
					$('#drTable > tbody').on('click', 'tr', function (e) {
						e.preventDefault();
						$(this).toggleClass('selected');
						var id = $(this).find("td").html();
						ieid = id;	
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter('<h4>Error</h4><p>Cant fetch customer detail right now, please try again later</p>','info');
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Search by name, email or telp no').css({'width':'250px', 'display':'inline-block'}); <!-- by Muhammad Sofi 29 December 2021 15:00 | resize search box -->
	$("#fl_reset").on("click",function(e){
		e.preventDefault();
		$("#fl_is_confirmed").val("");
		$("#fl_pelanggan_status").val("");
		drTable.search('').columns().search('').draw(); <!-- by Muhammad Sofi 29 December 2021 15:00 | clear search box on click reset button -->
		drTable.ajax.reload(null,true);
	});
	$("#fl_button").on("click",function(e){
		e.preventDefault();
		drTable.ajax.reload(null,true);
	});
}

$('#b_permanent_acc_stop_mass').click( function () {
		var row_length = drTable.rows('.selected').data().length;
		<!-- check if post is not selected -->
		if(!row_length > 0) {
			Swal.fire(
				'Warning',
				'please, select the customer first',
				'warning'
			)
			Swal.fire({
				title: 'Warning',
				text: "Please, select the customer first",
				icon: 'warning'
			})
		} else {
			Swal.fire({
				title: 'Are you sure?',
				text: "Account will be permanently stopped!",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes'
			}).then((result) => {
				if (result.isConfirmed) {
					for (var i = 0; i < row_length; i++) {
						var idmass = $.map(drTable.rows('.selected').data(), function (item) {
							return item[0];
						});
						$.post("<?=base_url(); ?>api_admin/ecommerce/customeronly/edit_status_permanent_inactive/"+ idmass[i], { }, 
						function(data) {
							if(data.status == 200) {
								drTable.ajax.reload(null, false);
							} else {
								gritter("<h4>Error</h4><p>Cannot fetch data right now, please try again later</p>", "warning");
							}	
						}, "json");
					};

					setTimeout(function(){
						Swal.fire({
							title: 'Customer is being permanently stopped',
							icon: 'success',
							showConfirmButton: false,
							timer: 1200
						});
					}, 300);

				}
			})
		}
	});