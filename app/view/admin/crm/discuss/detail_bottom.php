
var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var ieid = '';
var api_url = '<?=base_url('api_admin/crm/discuss/detail'); ?>'+ieid;
var drTable = {};
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
        { "width": "60%" },
        { "orderable": false }
      ],
      "scrollX"       : true,
      "order"         : [[ 1, "desc" ]],
      "responsive"    : true,
      "bProcessing"   : true,
      "bServerSide"   : true,
      "sAjaxSource"   : "<?=base_url("api_admin/crm/discuss/detail/".$ieid); ?>",
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
            var url = '<?=base_url(); ?>api_admin/crm/discuss/show_edit/'+id;
            $.get(url).done(function(response){
              if(response.status==200){
                var dta = response.data;
                ieid = dta.id;
                $("#ieid").val(dta.id);
                $("#ieproduct").val(dta.product);
                $("#ietext").val(dta.text);
              }
          });
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

$("#fedit").on("submit",function(e){
  e.preventDefault();
  var fd = new FormData($(this)[0]);
  var id = ieid;
  var url = '<?=base_url("api_admin/crm/discuss/edit/"); ?>'+id;
  $.ajax({
    type: $(this).attr('method'),
    url: url,
    data: fd,
    processData: false,
    contentType: false,
    success: function(respon){
      if(respon.status == 200){
        gritter('<h4>Success</h4><p>Data changed successfully</p>','success');
        drTable.ajax.reload();
      }else{
        gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
      }
      $("#modal_edit").modal("hide");
    },
    error:function(){
      gritter('<h4>Error</h4><p>Cannot edit data right now, please try again later</p>','warning');
      return false;
    }
  });
});

//active
$("#active").on("click",function(e){

  e.preventDefault();
  var id = ieid;
  if(id){
    var c = confirm('Are you sure to active this chat?');
    if(c){
      var url = '<?=base_url('api_admin/crm/discuss/active/'); ?>'+id;
      $.get(url).done(function(response){
        if(response.status==200){
          gritter('<h4>Success</h4><p>Data has been actived successfully</p>','success');
        }else{
          gritter('<h4>Failed</h4><p>'+response.message+'</p>','Danger');
        }
        drTable.ajax.reload();
        $("#modal_option").modal("hide");
      }).fail(function() {
        gritter('<h4>Error</h4><p>Cannot activated data right now, please try again later</p>','warning');
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

