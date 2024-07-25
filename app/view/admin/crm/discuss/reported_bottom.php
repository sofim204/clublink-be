
var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var api_url = '<?=base_url('api_admin/crm/discuss/reported'); ?>';
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
      "columns"       : [
        null,
        null,
        null,
        null,
        null,
        null,
        null,
        { "width": "60%" },
        { "orderable": false }
      ],
      "scrollX"       : true,
      "order"         : [[ 5, "desc" ]],
      "responsive"    : true,
      "bProcessing"   : true,
      "bServerSide"   : true,
      "sAjaxSource"   : "<?=base_url("api_admin/crm/discuss/reported"); ?>",
      "fnServerData"  : function (sSource, aoData, fnCallback, oSettings) {
        oSettings.jqXHR = $.ajax({
          dataType  : 'json',
          method    : 'POST',
          url     : sSource,
          data    : aoData
        }).success(function (response, status, headers, config) {
          console.log(response);
          $("#modal-preloader").modal("hide");
          //$('body').addClass('loaded');
          $('#drTable > tbody').off('click', 'tr');
          $('#drTable > tbody').on('click', 'tr', function (e) {
            e.preventDefault();
            var id = $(this).find("td").html();
            ieid = id;
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
  $('.dataTables_filter input').attr('placeholder', 'Search last chat message');
}

//edit
$("#modal_edit").on("shown.bs.modal",function(e){
  //

});
$("#modal_edit").on("hidden.bs.modal",function(e){
  $("#modal_edit").find("form").trigger("reset");
});

//takedown
$("#takedown").on("click",function(e){

  e.preventDefault();
  var id = ieid;
  if(id){
    var c = confirm('Are you sure to takedown chat?');
    if(c){
      var url = '<?=base_url('api_admin/crm/discuss/takedown/'); ?>'+id;
      $.get(url).done(function(response){
        if(response.status==200){
          gritter('<h4>Success</h4><p>Data has been takedown</p>','success');
        }else{
          gritter('<h4>Failed</h4><p>'+response.message+'</p>','Danger');
        }
        drTable.ajax.reload();
        $("#modal_option").modal("hide");
      }).fail(function() {
        gritter('<h4>Error</h4><p>Cannot takedow data right now, please try again later</p>','warning');
      });
    }
  }
});

//ignore
$("#ignore").on("click",function(e){

  e.preventDefault();
  var id = ieid;
  if(id){
    var c = confirm('Are you sure to ignore this chat?');
    if(c){
      var url = '<?=base_url('api_admin/crm/discuss/ignore/'); ?>'+id;
      $.get(url).done(function(response){
        if(response.status==200){
          gritter('<h4>Success</h4><p>Data has been ignore</p>','success');
        }else{
          gritter('<h4>Failed</h4><p>'+response.message+'</p>','Danger');
        }
        drTable.ajax.reload();
        $("#modal_option").modal("hide");
      }).fail(function() {
        gritter('<h4>Error</h4><p>Cannot ignoring data right now, please try again later</p>','warning');
      });
    }
  }
});

$("#bhapus").on("click",function(e){
  e.preventDefault();
  $("#ahapus").trigger("click");
});

//option
$("#aedit").on("click",function(e){
  e.preventDefault();
  $("#modal_option").modal("hide");
  setTimeout(function(){
    $("#modal_edit").modal("show");
  },333);
});

