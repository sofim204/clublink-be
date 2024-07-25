//input status
$("#imip_input_price").on("keyup",function(e){
  e.preventDefault();
  var x = $(this).val().replace(/^[$£€]\d+(?:\.\d\d)*$/g,'');
  $(this).val(x);
});
$("#binput_price").on("click",function(e){
  e.preventDefault();
  $("#modal_input_price").modal("show");
  $("#imcs_input_price").val("<?=$produk->action_status?>");
});
$("#form_input_price").on("submit",function(e){
  e.preventDefault();
  var url = '<?=base_url("api_admin/ecommerce/bulksale/input_price/")?>';
  var fd = new FormData($(this)[0]);
  $("#form_input_price_alert").hide("fast");
  NProgress.start();
  $.ajax({
    type: $(this).attr('method'),
    url: url,
    data: fd,
    processData: false,
    contentType: false,
    success: function(dt){
      NProgress.done();
      $("#form_input_price_alert").show("slow");
      if(dt.status == 200){
        $("#form_input_price_alert").removeClass("alert-warning");
        $("#form_input_price_alert").addClass("alert-success");
        $("#form_input_price_alert").html("Success, reloading page..");
        setTimeout(function(){
          window.location.reload();
        },2000);
      }else{
        $("#form_input_price_alert").removeClass("alert-success");
        $("#form_input_price_alert").addClass("alert-warning");
        $("#form_input_price_alert").html(dt.message);
      }
    }, error: function(){
      $("#form_input_price_alert").show("slow");
      $("#form_input_price_alert").removeClass("alert-success");
      $("#form_input_price_alert").addClass("alert-warning");
      $("#form_input_price_alert").html("Can't update visit date, please try again later");
      NProgress.done();
    }
  });
});
//end input price

//change status
$("#imcs_change_status").on("change",function(e){
  e.preventDefault();
  var cs = $("#imcs_change_status").val();
  if(cs=='leaved' || cs=='rejected'){
    $("#div_reason").show("slow");
  }else{
    $("#imcs_reason").html("");
    $("#div_reason").hide();
  }
});
$("#bchange_status").on("click",function(e){
  e.preventDefault();
  $("#div_reason").hide();
  $("#modal_change_status").modal("show");
  $("#imcs_change_status").val("<?=$produk->action_status?>");
});
$("#form_change_status").on("submit",function(e){
  e.preventDefault();
  var cs = $("#imcs_change_status").val();
  if(cs=='leaved' || cs=='rejected'){
    var reason = $("#imcs_reason").val();
    if(reason.length<=10){
      $("#form_change_status_alert").show("slow");
      $("#form_change_status_alert").removeClass("alert-success");
      $("#form_change_status_alert").addClass("alert-warning");
      $("#form_change_status_alert").html("Please give reason at least 10 characters...");
      return false;
    }
  }else{
    $("#imcs_reason").html("");
  }
  var url = '<?=base_url("api_admin/ecommerce/bulksale/change_status/")?>';
  var fd = new FormData($(this)[0]);
  $("#form_change_status_alert").hide("fast");
  NProgress.start();
  $.ajax({
    type: $(this).attr('method'),
    url: url,
    data: fd,
    processData: false,
    contentType: false,
    success: function(dt){
      NProgress.done();
      $("#form_change_status_alert").show("slow");
      if(dt.status == 200){
        $("#form_change_status_alert").removeClass("alert-warning");
        $("#form_change_status_alert").addClass("alert-success");
        $("#form_change_status_alert").html("Success, reloading page..");
        setTimeout(function(){
          window.location.reload();
        },2000);
      }else{
        $("#form_change_status_alert").removeClass("alert-success");
        $("#form_change_status_alert").addClass("alert-warning");
        $("#form_change_status_alert").html(dt.message);
      }
    }, error: function(){
      $("#form_change_status_alert").show("slow");
      $("#form_change_status_alert").removeClass("alert-success");
      $("#form_change_status_alert").addClass("alert-warning");
      $("#form_change_status_alert").html("Can't update visit date, please try again later");
      NProgress.done();
    }
  });
});
//end change status

//visit date
$("#bvisit_date").on("click",function(e){
  e.preventDefault();
  $("#modal_visit_date").modal("show");
});
$("#form_visit_date").on("submit",function(e){
  e.preventDefault();
  var url = '<?=base_url("api_admin/ecommerce/bulksale/visit_date/")?>';
  var fd = new FormData($(this)[0]);
  $("#form_visit_date_alert").hide("fast");
  NProgress.start();
  $.ajax({
    type: $(this).attr('method'),
    url: url,
    data: fd,
    processData: false,
    contentType: false,
    success: function(dt){
      NProgress.done();
      $("#form_visit_date_alert").show("slow");
      if(dt.status == 200){
        $("#form_visit_date_alert").removeClass("alert-warning");
        $("#form_visit_date_alert").addClass("alert-success");
        $("#form_visit_date_alert").html("Success, reloading page..");
        setTimeout(function(){
          window.location.reload();
        },2000);
      }else{
        $("#form_visit_date_alert").removeClass("alert-success");
        $("#form_visit_date_alert").addClass("alert-warning");
        $("#form_visit_date_alert").html(dt.message);
      }
    }, error: function(){
      $("#form_visit_date_alert").show("slow");
      $("#form_visit_date_alert").removeClass("alert-success");
      $("#form_visit_date_alert").addClass("alert-warning");
      $("#form_visit_date_alert").html("Can't update visit date, please try again later");
      NProgress.done();
    }
  });
});
//end visit date
