var ieid = '<?=$order->id?>';
var growlPesan = '<h4>Error</h4><p>Cannot be proceed, please try again later!</p>';
var growlType = 'danger';
var drTable = {};

function gritter(pesan,jenis='info'){
  $.bootstrapGrowl(pesan, {
    type: jenis,
    delay: 3500,
    allow_dismiss: true
  });
}

$("#fsendchat").on("submit",function(e){
  e.preventDefault();
  NProgress.start();
  var fd = new FormData($(this)[0]);
  var url = '<?=base_url("api_admin/crm/chat/sendMessage/"); ?>'+ieid;
  $.ajax({
    type: $(this).attr('method'),
    url: url,
    data: fd,
    processData: false,
    contentType: false,
    success: function(respon){
      NProgress.done();
      if(respon.status==200){
        $('#fsendchat')[0].reset(); // Clear the form
        gritter('<h4>Success</h4><p>Message has been send!</p>','success');
        window.location.reload();
      }else{
        growlPesan = '<h4>Failed</h4><p></p>';
        growlType = 'danger';
        gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
      }
    },
    error:function(){
      NProgress.done();
      gritter('<h4>Error</h4><p>Cannot add chat right now, please try again later</p>','warning');
      return false;
    }
  });
});
$(document).ready(function() {
  if (window.File && window.FileList && window.FileReader) {
    $("#files").on("change", function(e) {
      var files = e.target.files,
      filesLength = files.length;
      var fv = $(this).clone();
      fv.removeAttr("id").removeAttr("name").attr("name","files[]").addClass("hidden");
      for (var i = 0; i < filesLength; i++) {
        var f = files[i];
        var fileReader = new FileReader();
        fileReader.onload = (function(e) {
          var file = e.target;
          $("#attachments").empty();
          $("#attachments").append(
            $("<span>").addClass("pip").append(
              $("<img>").attr("src",e.target.result).addClass("imageThumb")
            ).append(
              $("<br>")
            ).append(
              $("<span>").addClass("remove").text("Remove")
            ).append(fv)
          );
          $(".remove").click(function(){
            $(this).parent(".pip").remove();
          });

          // Old code here
          /*$("<img></img>", {
          class: "imageThumb",
          src: e.target.result,
          title: file.name + " | Click to remove"
        }).insertAfter("#files").click(function(){$(this).remove();});*/

      });
      fileReader.readAsDataURL(f);
    }
  });
} else {
  alert("Your browser doesn't support to File API");
}
});

//Js scroll always on bottom
var messageBody = document.querySelector('#scroll-chat-down');
messageBody.scrollTop = messageBody.scrollHeight - messageBody.clientHeight;
// end Js scroll always on bottom

$(".btn-complain-cancel").on("click",function(e){
  e.preventDefault();
  var c = confirm("Are you sure?");
  if(c){
    NProgress.start();
    var nation_code = $(this).attr("data-nation_code");
    var d_order_id = $(this).attr("data-d_order_id");
    var d_order_detail_id = $(this).attr("data-d_order_detail_id");
    var d_order_detail_item_id = $(this).attr("data-d_order_detail_item_id");
    $.get('<?=base_url("api_admin/crm/chat/cancel/")?>'+nation_code+'/'+d_order_id+'/'+d_order_detail_id+'/'+d_order_detail_item_id+'/').done(function(dt){
      NProgress.done();
      window.location.reload();
    }).fail(function(){
      NProgress.done();
      gritter('<h4>Error</h4><p>Cannot take action right now, please try again later</p>','warning');
      return false;
    })
  }
});

$(".btn-solve-buyer").on("click",function(e){
  e.preventDefault();
  var c = confirm("Are you sure?");
  if(c){
    NProgress.start();
    var nation_code = $(this).attr("data-nation_code");
    var d_order_id = $(this).attr("data-d_order_id");
    var d_order_detail_id = $(this).attr("data-d_order_detail_id");
    var d_order_detail_item_id = $(this).attr("data-d_order_detail_item_id");
    $.get('<?=base_url("api_admin/crm/chat/solved_to_buyer/")?>'+nation_code+'/'+d_order_id+'/'+d_order_detail_id+'/'+d_order_detail_item_id+'/').done(function(dt){
      NProgress.done();
      window.location.reload();
    }).fail(function(){
      NProgress.done();
      gritter('<h4>Error</h4><p>Cannot take action right now, please try again later</p>','warning');
      return false;
    })
  }
});

$(".btn-solve-seller").on("click",function(e){
  e.preventDefault();
  var c = confirm("Are you sure?");
  if(c){
    NProgress.start();
    var nation_code = $(this).attr("data-nation_code");
    var d_order_id = $(this).attr("data-d_order_id");
    var d_order_detail_id = $(this).attr("data-d_order_detail_id");
    var d_order_detail_item_id = $(this).attr("data-d_order_detail_item_id");
    $.get('<?=base_url("api_admin/crm/chat/solved_to_seller/")?>'+nation_code+'/'+d_order_id+'/'+d_order_detail_id+'/'+d_order_detail_item_id+'/').done(function(dt){
      NProgress.done();
      window.location.reload();
    }).fail(function(){
      NProgress.done();
      gritter('<h4>Error</h4><p>Cannot take action right now, please try again later</p>','warning');
      return false;
    })
  }
});
