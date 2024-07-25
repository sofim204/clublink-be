$(document).ready(function(){
  <!-- amountAndTransaction(); -->
  dashboardInit();
});

var ieid = "";
var filter_from_date;
var filter_to_date;

<!-- initiate -->
let filter_from = $("#from_cdate_offer_summary").val();
let filter_to = $("#to_cdate_offer_summary").val();

filter_from_date = filter_from;
filter_to_date = filter_to;

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
var chartOverview   = $('#chart-overview');

// Random data for the charts
var dataSales       = [[1, 11600], [2, 13950], [3, 10900], [4, 10050], [5, 11000], [6, 14300], [7, 12500], [8, 15050], [9, 12650], [10, 14000], [11, 15000], [12, 17900]];
var dataEarnings    = [[1, 3000], [2, 3500], [3, 2900], [4, 3800], [5, 2800], [6, 2408], [7, 2682], [8, 4400], [9, 5400], [10, 4750], [11, 6100], [12, 7560]];

// Array with month labels used in Classic and Stacked chart
var chartMonths     = [[1, 'Jan'], [2, 'Feb'], [3, 'Mar'], [4, 'Apr'], [5, 'May'], [6, 'Jun'], [7, 'Jul'], [8, 'Aug'], [9, 'Sep'], [10, 'Oct'], [11, 'Nov'], [12, 'Dec']];
var response        = {'status':200,'message':'','data':{'earning_total':0,'order_total':0,'pending_count':0,'bestseller': [],'overview': [],'order_latest': []}};

function overviewChart(){
  // Overview Chart
  $.plot(chartOverview,[
  {
    label: 'Earnings',
    data: dataEarnings,
    lines: {show: true, fill: true, fillColor: {colors: [{opacity: 0.25}, {opacity: 0.25}]}},
    points: {show: false, radius: 6}
  },
  {
    label: 'Sales',
    data: dataSales,
    lines: {show: true, fill: true, fillColor: {colors: [{opacity: 0.15}, {opacity: 0.15}]}},
    points: {show: false, radius: 6}
  }],{
    colors: ['#f6b54d', '#fcd23d'],
    legend: {show: true, position: 'nw', margin: [15, 10]},
    grid: {borderWidth: 0, hoverable: true, clickable: true},
    yaxis: {ticks: 3, tickColor: '#f1f1f1'},
    xaxis: {ticks: chartMonths, tickColor: '#ffffff'}
  });

  // Creating and attaching a tooltip to the classic chart
  var previousPoint = null, ttlabel = null;
  chartOverview.bind('plothover', function(event, pos, item) {
    if (item) {
      if (previousPoint !== item.dataIndex) {
        previousPoint = item.dataIndex;

        $('#chart-tooltip').remove();
        var x = item.datapoint[0], y = item.datapoint[1];

        if (item.seriesIndex === 1) {
          ttlabel = 'Sales: $<strong>' + y.toLocaleString() + '</strong>';
        } else {
          ttlabel = 'Earnings: $<strong>' + y.toLocaleString() + '</strong>';
        }

        $('<div id="chart-tooltip" class="chart-tooltip">' + ttlabel + '</div>').css({top: item.pageY - 45, left: item.pageX + 5}).appendTo("body").show();
      }
    }
    else {
      $('#chart-tooltip').remove();
      previousPoint = null;
    }
  });
}

function orderLatest(){
  if(typeof response.data === undefined){
    response = {};
    response.data = {};
  }
  if(typeof response.data.order_latest === undefined){
    response.data.order_latest = [];
  }
  var res = response.data.order_latest;
  var tol = $("#table-order-latest");
  $(tol).find("tbody").empty();
  $.each(res,function(kdt,vdt){
    //console.log(vdt);
    vdt.grand_total = Number(vdt.grand_total).toLocaleString();
    var h = '<tr>';
    h += '<td><b><a href="<?=base_url_admin("ecommerce/transaction/buyer_detail/")?>'+vdt.id+'" target="_blank">'+vdt.invoice_code+'</a></b></td>';
    h += '<td><b><a href="<?=base_url_admin("ecommerce/pelanggan/detail/")?>'+vdt.b_user_id+'" target="_blank">'+vdt.penerima_nama+'</a></b></td>';
    h += '<td>'+vdt.payment_gateway+'</td>';
    h += '<td>$'+vdt.grand_total+'</td>';
    h += '<td>';
    if(vdt.order_status == 'forward_to_seller'){
      h += '<label class="label label-info">Process</label>';
    }else if(vdt.order_status == 'payment_verification'){
      h += '<label class="label label-warning">Payment</label>';
    }else if(vdt.order_status == 'completed'){
      h += '<label class="label label-success">Succeed</label>';
    }else if(vdt.order_status == 'waiting_for_payment'){
      h += '<label class="label label-default">Waiting For Payment</label>';
    }else if(vdt.order_status == 'pending'){
      h += '<label class="label label-default">Pending</label>';
    }else if(vdt.order_status == 'cancelled'){
      h += '<label class="label label-danger">Cancelled</label>';
    }else{
      h += '<label class="label label-default">-</label>';
    }
    h += '</td>';
    h += '</tr>';
    $(tol).find("tbody").append(h);
  });
}


function produkBestSeller(){
  var tbs = $("#table-best-seller");
  $(tbs).find("tbody").empty();
  if(typeof response.data === undefined){
    response = {};
    response.data = {};
  }
  if(typeof response.data.bestseller === undefined){
    response.data.bestseller = [];
  }
  var res = response.data.bestseller;

  $.each(res,function(kdt,vdt){
    vdt.qty = Number(vdt.qty).toLocaleString();
    var h = '<tr>';
    h += '<td><b>'+vdt.produk_sku+'</b></td>';
    h += '<td><a href="<?=base_url_admin("ecommerce/produk/detail/")?>'+vdt.produk_id+'" target="_blank">'+vdt.produk_nama+'</a></td>';
    h += '<td><a href="<?=base_url_admin("ecommerce/pelanggan/detail/")?>'+vdt.b_user_id_seller+'" target="_blank">'+vdt.b_user_fnama_seller+'</a></td>';
    h += '<td>'+vdt.qty+' Pcs</td>';
    h += '</tr>';
    $(tbs).find("tbody").append(h);
  });
}

// Edited By Aditya Adi Prabowo 5/8/2020 16:25
// Improve Filter Date On Dashboard and Change Value On Summary Report
// Start Improve

$("#afilter_do").on("click",function(e){
    e.preventDefault();
    <!-- console.log('JJJ'); -->
    var cdate_start = $("#ifcdate_start").val();
    var cdate_end = $("#ifcdate_max").val();
    $.ajax({
    url: '<?=base_url("api_admin/home")?>',
    data:{
          start_date:cdate_start,
          end_date:cdate_end,
        },
    method: 'POST',
    success: function(data)
    {
      $("#earning-this-month").html('$s'+data.data.earning_total);
      $("#sales-this-month").html('$s'+data.data.sales_total);
      $("#unpaid-this-month").html('$s'+data.data.unpaid_total);
    }
  })
});

$("#areset_do").on("click",function(e){
    e.preventDefault();
    $("#ifcdate_start").val("");
    $("#ifcdate_max").val("");
    drTable.ajax.reload(null,true);
  });

// End Improve
function dashboardInit(){
  NProgress.start();
  $.get("<?=base_url("api_admin/")?>  ").done(function(res){
    <!-- console.log(res); -->
    if(res.status == 200){
      response = res;
      //validation
      if(typeof response.data === undefined){
        response = {};
        response.data = {};
      }
      if(typeof response.data.overview === undefined){
        response.data.overview = [];
      }
      if(typeof response.data.earning_total === undefined){
        response.data.earning_total = 0;
      }
      if(typeof response.data.order_total === undefined){
        response.data.order_total = 0;
      }
      if(typeof response.data.pending_count === undefined){
        response.data.pending_count = 0;
      }
      dataEarnings = [];
      dataSales = [];
      $.each(response.data.overview,function(kd,vd){
        var ds = [vd.month,vd.sales];
        dataSales.push(ds);
        var de = [vd.month,vd.earnings];
        dataEarnings.push(de);
      });

      setTimeout(function(){
        //produkBestSeller();
        //overviewChart();
        //orderLatest();
        $("#earning-this-month").html('$'+response.data.earning_total.toLocaleString());
        $("#sales-this-month").html('$'+response.data.sales_total.toLocaleString());
        $("#unpaid-this-month").html('$'+response.data.unpaid_total.toLocaleString());

        <!-- by Donny Dennison - 25 january 2021 15:51 -->
        <!-- add need action column in dashboard -->
        $("#reported_product_total").html(response.data.reported_product_total.toLocaleString());
        $("#reported_discussion_total").html(response.data.reported_discussion_total.toLocaleString());
        $("#rejected_by_seller_total").html(response.data.rejected_by_seller_total.toLocaleString());
        $("#rejected_item_by_buyer_total").html(response.data.rejected_item_by_buyer_total.toLocaleString());
        $("#total_product_video").html(response.data.total_product_video.toLocaleString());
        $("#total_community_video").html(response.data.total_community_video.toLocaleString());
        $("#total_reported_community_post").html(response.data.total_reported_community_post.toLocaleString());

        $("#total_active_user").html(response.data.total_active_user.toLocaleString());
        $("#total_active_community").html(response.data.total_active_community.toLocaleString());
        $("#total_active_product").html(response.data.total_active_product.toLocaleString());

        //let currency = response.data.total_sales_seller_month.toLocaleString();
        //var formatter = new Intl.NumberFormat('id-ID', {
        //  style: 'currency',
        //  currency: 'IDR',

          // These options are needed to round to whole numbers if that's what you want.
          //minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
          //maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
        //});

        //let result_total_sales_seller_month = formatter.format(currency);
        //$("#total_sales_seller_month").html(result_total_sales_seller_month);
        //$("#total_transaction_seller_month").html(response.data.total_transaction_seller_month.toLocaleString());

        let currency_sales_all = response.data.total_sales_all.toLocaleString();
        var formatter = new Intl.NumberFormat('id-ID', {
          style: 'currency',
          currency: 'IDR',

          // These options are needed to round to whole numbers if that's what you want.
          //minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
          //maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
        });

        let result_total_sales_all = formatter.format(currency_sales_all);
        $("#total_sales_all").html(result_total_sales_all);

        //setTimeout(function(){
        //  NProgress.done()
        //},1000);

        // trigger event click to load amountAndTransaction()
        $("#filter_data_offer_summary").click();

      },1000);
    }else{
      NProgress.done();
    }
  }).fail(function(){
    NProgress.done();
  });
}

function amountAndTransaction() {
  var url = '<?=base_url(); ?>api_admin/home/getTotalSalesTransactionYearMonth/'+ filter_from_date + '/' + filter_to_date;
  $.get(url).done(function(res){
    <!-- console.log(res); -->
    if(res.status == 200){
      response = res;
      //validation
      if(typeof response.data === undefined){
        response = {};
        response.data = {};
      }
      if(typeof response.data.overview === undefined){
        response.data.overview = [];
      }
      if(typeof response.data.earning_total === undefined){
        response.data.earning_total = 0;
      }
      if(typeof response.data.order_total === undefined){
        response.data.order_total = 0;
      }
      if(typeof response.data.pending_count === undefined){
        response.data.pending_count = 0;
      }
      dataEarnings = [];
      dataSales = [];
      $.each(response.data.overview,function(kd,vd){
        var ds = [vd.month,vd.sales];
        dataSales.push(ds);
        var de = [vd.month,vd.earnings];
        dataEarnings.push(de);
      });

      setTimeout(function(){
        let currency = response.data.total_sales_seller_month_offer.toLocaleString();
        var formatter = new Intl.NumberFormat('id-ID', {
          style: 'currency',
          currency: 'IDR',

          // These options are needed to round to whole numbers if that's what you want.
          //minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
          //maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
        });

        let result_total_sales_seller_month_offer = formatter.format(currency);
        $("#total_sales_seller_month").html(result_total_sales_seller_month_offer);
        $("#total_transaction_seller_month").html(response.data.total_transaction_seller_month_offer.toLocaleString());

        setTimeout(function(){
          NProgress.done()
        },1000);
      },1000);
    }else{
      NProgress.done();
    }
  }).fail(function(){
    NProgress.done();
  });
}



//$('#from_cdate_offer_summary, #to_cdate_offer_summary').datepicker({
//    changeMonth: true,
//    changeYear: true,
//    showButtonPanel: true,
//    dateFormat: 'MM yy',
//    onClose: function(dateText, inst) { 
//        $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
//    }
//});

//$('#from_cdate_offer_summary, #to_cdate_offer_summary').datepicker();
//$('#from_cdate_offer_summary, #to_cdate_offer_summary').datepicker('setDate', 'today');

//$("#from_cdate_offer_summary, #to_cdate_offer_summary").change(function(){
//  $('.datepicker').hide(); <!-- hide datepicker after select a date -->
//});

<!-- initialize date for offer summary -->

//function defaultMonthYear() {
//  let date_by_year_month = $("#reset_year_month").val();
//  let year_today = date_by_year_month.substring(0, 4);
//  let month_today = date_by_year_month.substring(5, 7);
//  $("#month_from_date").val(month_today);
//  $("#year_from_date").val(year_today);
//  $("#month_to_date").val(month_today);
//  $("#year_to_date").val(year_today);
//}

//defaultMonthYear();

//start create custom input date by year and month

$("#from_cdate_offer_summary").on("click", function() {
  //$("#modal-from-date").modal("show");

  let pos = $(this).offset();
  let width = $(this).width();   
  $("#custom_from_date_container").show();
  $("#custom_from_date_container").css({ "left": (pos.left + 80) + "px", "top": (pos.top - 170) + "px" });
  $("#custom_from_date_container").fadeIn();
});

$("#btn_done_from_date").on("click", function() {
  let month_from_date = $("#month_from_date").val();
  let year_from_date = $("#year_from_date").val();
  let input_from_date = year_from_date + '-' + month_from_date;
  $("#from_cdate_offer_summary").val(input_from_date);
  //$("#modal-from-date").modal("hide");

  $("#custom_from_date_container").hide();
  $("#custom_from_date_container").fadeOut();
});

$("#to_cdate_offer_summary").on("click", function() {
  //$("#modal-to-date").modal("show");

  let pos = $(this).offset();
  let width = $(this).width();   
  $("#custom_to_date_container").show();
  $("#custom_to_date_container").css({ "left": (pos.left + 90) + "px", "top": (pos.top - 170) + "px" });
  $("#custom_to_date_container").fadeIn();
});

$("#btn_done_to_date").on("click", function() {
  let month_to_date = $("#month_to_date").val();
  let year_to_date = $("#year_to_date").val();
  let input_to_date = year_to_date + '-' + month_to_date;
  $("#to_cdate_offer_summary").val(input_to_date);
  //$("#modal-to-date").modal("hide");

  $("#custom_to_date_container").hide();
  $("#custom_to_date_container").fadeOut();
});

$(document).mouseup(function(e)  {
  let area_from_date = $("#custom_from_date_container");
  let area_to_date = $("#custom_to_date_container");

  if (!area_from_date.is(e.target) && area_from_date.has(e.target).length === 0) {
    area_from_date.hide();
  }

  if (!area_to_date.is(e.target) && area_to_date.has(e.target).length === 0) {
    area_to_date.hide();
  }
});

//end create custom input date by year and month

//$(function() {
  //   $('#datepicker').datepicker({
    //       changeYear: true,
    //      changeMonth: true,
    //      showButtonPanel: true,
    //     dateFormat: 'yy-mm',
    //     onClose: function(dateText, inst) { 
      //        var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
      //         $(this).datepicker('setDate', new Date(year, 1));
      //    }
      // });
      //$(".date-picker-year").focus(function () {
        //       $(".ui-datepicker-month").hide();
        //   });
        //});

var drTable = {};
var drTableDailyTrack = {};
var ieid = '';
App.datatables();

if(jQuery('#drTable').length>0){
	drTable = jQuery('#drTable')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
      "columnDefs"		: [{
                        "targets": [1], <!-- hide column -->
                        "visible": false,
                        "searchable": false
                      }],
			"order"				: [[ 0, "asc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/home/show_offer_summary/"); ?>",
				"fnServerParams": function ( aoData ) {
					aoData.push(
						{ "name": "from_date", "value": $("#from_cdate_offer_summary").val() },
						{ "name": "to_date", "value": $("#to_cdate_offer_summary").val() },
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
					<!-- console.log(response); -->
					$('#drTable > tbody').off('click', 'tr');
					$('#drTable > tbody').on('click', 'tr', function (e) {
						e.preventDefault();
            var currentRow = $(this).closest("tr");
            var id = $('#drTable').DataTable().row(currentRow).data()[1]; <!-- to get data from specific column, change this "data()[id_column]" -->
            ieid = id;
            //alert(ieid);
            $("#user_id_toggle").val(ieid);
						$("#modal_options").modal("show");
            
            $("#btn_toggle_seller").on("click", function(e) {
              e.preventDefault();
              let toggle = $(this).attr('value_toggle');
              if(toggle === "seller") {
                window.location = '<?=base_url_admin('offer_detail/seller/');?>' + toggle + '/' + ieid + '/' + filter_from_date + '/' + filter_to_date;
              }
            });

            $("#btn_toggle_buyer").on("click", function(e) {
              e.preventDefault();
              let toggle = $(this).attr('value_toggle');
              if(toggle === "buyer") {
                window.location = '<?=base_url_admin('offer_detail/buyer/');?>' + toggle + '/' + ieid + '/' + filter_from_date + '/' + filter_to_date;
              }
            });
					});

					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter("<h4>Error</h4><p>Cant fetch data right now, please try again later</p>",'warning');
				});
			},
	});

	$('.dataTables_filter input').attr('placeholder', 'Search Name').css({'width':'250px', 'display':'inline-block'}); <!-- show searchbox + add styling -->
	$("#filter_data_offer_summary").on("click", function(e) {
		e.preventDefault();
		drTable.ajax.reload();

    let filter_from = $("#from_cdate_offer_summary").val();
    let filter_to = $("#to_cdate_offer_summary").val();
    filter_from_date = filter_from;
    filter_to_date = filter_to;

    amountAndTransaction();
	});

	$("#reset_data_offer_summary").on("click", function(e) {
    //$('#from_cdate_offer_summary, #to_cdate_offer_summary').datepicker('setDate', 'today');
    $("#from_cdate_offer_summary, #to_cdate_offer_summary").val($("#reset_year_month").val());
    //defaultMonthYear();
    drTable.ajax.reload();

    // trigger event click to load amountAndTransaction()
    $("#filter_data_offer_summary").click();
	});

}

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