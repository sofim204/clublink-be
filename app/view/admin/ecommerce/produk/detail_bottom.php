$(document).ready(function(){
    let product_type = $("#param_video_toggle").val();
    if(product_type == "Santa") {
        $("#product_video").hide();
    } else {
        $("#product_video").show();
    }

    function gritter(pesan,jenis="info"){
        $.bootstrapGrowl(pesan, {
            type: jenis,
            delay: 2500,
            allow_dismiss: true
        });
    }

    let status_is_active = $("#status_is_active").val();
    let status_is_published = $("#status_is_published").val();
    let status_is_visible = $("#status_is_visible").val();
    let status_is_takedown = $("#status_is_takedown").val();

    if(status_is_active == "0" && status_is_published == "0" && status_is_visible == "0") {
        $("#b_report_product").hide();
        $("#message_status_report").show();
    } else {
        $("#b_report_product").show();
        $("#message_status_report").hide();
    }

    if(status_is_active == "0" && status_is_published == "0" && status_is_visible == "0" && status_is_takedown == "takedown") {
        $("#b_delete_product").hide();
        $("#message_status_delete").show();
    } else {
        $("#b_delete_product").show();
        $("#message_status_delete").hide();
    }

    $("#b_report_product").on("click", (e) => {
        e.preventDefault();
        let product_id = $("#product_id").val();
        let confirm_message = confirm("Are you sure to report this product?");
        if(confirm_message) {
            NProgress.start();
            var url = '<?=base_url('api_admin/ecommerce/produk/report_from_admin/'); ?>'+product_id;
            $.get(url).done(function(response){
                NProgress.done();
                console.log(response)
                if(response.status=="200"){
                    gritter('<h4>Success</h4><p>Product Successfully Reported</p>','success');
                    setTimeout(function(){
                        location.reload();
                    }, 700);
                }else{
                    gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
                }
            }).fail(function() {
                NProgress.done();
                gritter('<h4>Error</h4><p>Error, Please try again</p>','danger');
            });
        } 
    });

    $("#b_delete_product").on("click", (e) => {
        e.preventDefault();
        let product_id = $("#product_id").val();
        let confirm_message = confirm("Are you sure to delete this product?"); // same like takedown
        if(confirm_message) {
            NProgress.start();
            var url = '<?=base_url('api_admin/ecommerce/produk/delete_from_admin/'); ?>'+product_id;
            $.get(url).done(function(response){
                NProgress.done();
                if(response.status=="200"){
                    gritter('<h4>Success</h4><p>Product Successfully Deleted</p>','success');
                    setTimeout(function(){
                        location.reload();
                        window.close();
                    }, 700);
                }else{
                    gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
                }
            }).fail(function() {
                NProgress.done();
                gritter('<h4>Error</h4><p>Error, Please try again</p>','danger');
            });
        } 
    });
});