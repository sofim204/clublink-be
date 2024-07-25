function alertMessage(messages, type='info'){
	$.bootstrapGrowl(messages, {
		type,
		delay: 2500,
		allow_dismiss: true
	});
} 

App.datatables();

const payloads = [
	{
		menu: "data",
		url: "<?=base_url('api_admin/crm/chat'); ?>",
		table: {},
	},
]

let ieid = '';
let myAlert = {
	type: 'danger', 
	message: '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>'
}
$(document).ready(function() {
	payloads.map(payload=>{
		if(jQuery(`#${payload.menu}Table`).length>0){
			payload.table = jQuery(`#${payload.menu}Table`)
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
					{ "width": "50%" },
					{ "orderable": false }
				],
				"scrollX"		: true,
				"order"			: [[ 1, "desc" ]],
				"responsive"	: true,
				"bProcessing"	: true,
				"bServerSide"	: true,
				"sAjaxSource"	: payload.url,
				"fnServerParams": function ( aoData ) {
					aoData.push(
						{ "name": "from_date", "value": $("#from_date").val() },
						{ "name": "to_date", "value": $("#to_date").val() },
						{ "name": "room_type", "value": $("#room_type").val() },
					);
				},
				"fnServerData"	: function (sSource, aoData, fnCallback, oSettings) {
					oSettings.jqXHR = $.ajax({
						dataType 	: 'json',
						method 		: 'POST',
						url 		: sSource,
						data 		: aoData
					}).success(function (response, status, headers, config) {
						// console.log(response);
						$("#modal-preloader").modal("hide");
						//$('body').addClass('loaded');

						<!-- by Donny Dennison - 26 january 2021 17:36 -->
						<!-- change chat to open chatting -->
						/* $('#drTable > tbody').off('click', 'tr');
						$('#drTable > tbody`).on('click', 'tr', function (e) { */
						$(`#${payload.menu}Table > tbody`).off('click', 'button');
						$(`#${payload.menu}Table > tbody`).on('click', 'button', function (e) {

							e.preventDefault();

							<!-- by Donny Dennison - 26 january 2021 17:36 -->
							<!-- change chat to open chatting -->
							//var id = $(this).find("td").html();
							var id = $(this).attr("data-id");

							ieid = id;

							<!-- by Donny Dennison - 26 january 2021 17:36 -->
							<!-- change chat to open chatting -->
							$(`#${payload.menu}Table > tbody > #adetail`).attr("href",payload.url+'/detail/'+ieid);

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
					
			$("#apply-filter").on("click",function(e){
				e.preventDefault();
				payload.table.ajax.reload();
			});
		}
	})

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
		},
		maximumSelectionLength: 15,
		minimumInputLength: 3,
	});
		
	$("#create_room_chat").on("click",function(e){
		e.preventDefault();
		var to_customer = $("#if_to_customer").val();

		if(to_customer){
			fetch('<?=base_url('api_admin/crm/chat/get_room_admin/')?>'+to_customer)
				.then( response => response.json() )
				.then( ({data}) => {
					const id = data[0][0];
					var url = '<?=base_url_admin()?>crm/chat/detail/admin/'+id;
					window.location = url;
				})
		}else{
			growlPesan = '<h4>Failed</h4><p></p>';
			growlType = 'danger';
			alertMessage('<h4>Failed</h4><p>Please select customer</p>','danger');
			return false;
		}
	});

	<!-- set from date -30 days from today -->
	$('#from_date, #to_date').datepicker();

	//var date = new Date();
	//date.setDate(date.getDate() - 30);
	//var dateString = date.toISOString().split('T')[0];

    //$('#from_date').datepicker('setDate', dateString).val("");
	//$('#to_date').datepicker('setDate', 'today').val("");
});