$(document).ready(function(){
  var user_role = $("#check_user_role").val()
  if(user_role != 'customer_service') {
    dashboardInit();
  }
});

<!-- initialize datepicker -->
$('#from_cdate_daily_track, #to_cdate_daily_track').datepicker();
  $('#from_cdate_daily_track, #to_cdate_daily_track').datepicker('setDate', 'today').val("");

$("#from_cdate_daily_track, #to_cdate_daily_track").change(function(){
  $('.datepicker').hide(); <!-- hide datepicker after select a date -->
});

function gritter(pesan,jenis="info"){
  $.bootstrapGrowl(pesan, {
    type: jenis,
    delay: 2500,
    allow_dismiss: true
  });
}

// End Improve
function dashboardInit() {
  NProgress.start();
  $.get("<?=base_url("api_admin/")?>  ").done(function(res){
    if(res.status == 200){
      response = res;
      //validation
      if(typeof response.data === undefined){
        response = {};
        response.data = {};
      }

      setTimeout(function(){
        <!-- by Donny Dennison - 25 january 2021 15:51 -->
        <!-- add need action column in dashboard -->
        $("#reported_discussion_total").html(response.data.reported_discussion_total.toLocaleString());
        $("#total_product_video").html(response.data.total_product_video.toLocaleString());
        $("#total_community_video").html(response.data.total_community_video.toLocaleString());
        $("#total_reported_community_post").html(response.data.total_reported_community_post.toLocaleString());

        $("#total_active_user").html(response.data.total_active_user.toLocaleString());
        $("#total_active_community").html(response.data.total_active_community.toLocaleString());

        var formatter = new Intl.NumberFormat('id-ID', {
          style: 'currency',
          currency: 'IDR',

          // These options are needed to round to whole numbers if that's what you want.
          //minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
          //maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
        });

      },1000);
    }else{
      NProgress.done();
    }
  }).fail(function(){
    NProgress.done();
  });
}

var drTableDailyTrack = {};
var ieid = '';
App.datatables();

if(jQuery('#drTableDailyTrack').length>0){
	drTableDailyTrack = jQuery('#drTableDailyTrack')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
			"order"				  : [[ 1, "desc" ]],
			"responsive"	  : true,
			"bProcessing"		: true,
			"bServerSide"		: true,
      "bFilter"       : false,
			"sAjaxSource"		: "<?=base_url("api_admin/home/daily_track_record/"); ?>",
				"fnServerParams": function ( aoData ) {
					aoData.push(
						{ "name": "from_date", "value": $("#from_cdate_daily_track").val() },
						{ "name": "to_date", "value": $("#to_cdate_daily_track").val() },
					);
				},
			"fnServerData"	: function (sSource, aoData, fnCallback, oSettings) {
				oSettings.jqXHR = $.ajax({
					dataType 	: 'json',
					method 		: 'POST',
					url 		: sSource,
					data 		: aoData
				}).success(function (response, status, headers, config) {
					NProgress.done();

					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter("<h4>Error</h4><p>Cant fetch data right now, please try again later</p>",'warning');
				});
			},
	});

	$('.dataTables_filter input').attr('placeholder', 'Search Name').css({'width':'250px', 'display':'inline-block'}); <!-- show searchbox + add styling -->
	$("#filter_data_daily_track").on("click", function(e) {
		e.preventDefault();
		drTableDailyTrack.ajax.reload();
	});

  $("#reset_data_daily_track").on("click", function(e) {
    $('#from_cdate_daily_track, #to_cdate_daily_track').datepicker('setDate', 'today').val("");
    drTableDailyTrack.ajax.reload();
  });  

  $("#refresh_table_daily_track").on("click",function(e){
    e.preventDefault();
    drTableDailyTrack.ajax.reload(null, false);
  });
}