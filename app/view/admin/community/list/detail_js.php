var growlPesan = '<h4>Error</h4><p>Tidak dapat diproses, silakan coba beberapa saat lagi!</p>';
var growlType = 'danger';
var drTable = {};
var ieid = '';
var urlDiscussion = "<?=base_url("api_admin/community/discussion/list/$list_post->id"); ?>";

App.datatables();

function gritter(pesan,jenis="info"){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 2500,
		allow_dismiss: true
	});
}

fetch(urlDiscussion).then(response => response.json()).then(({data}) => {
    console.log(data);
    // append
    data.filter(discussion=>discussion[7]==="0").map((discussion,key)=>{
        const statusDiscussion = discussion[6]?"Taken Down":discussion[5]?"Reported":"";
        const classDiscussion = discussion[6]?"danger":discussion[5]?"warning":"";
        $("#discussionContainer").append(`<div onclick="alert(${discussion[0]})" id="discuss-no-${discussion[0]}" class="discuss-container level-0">`+
            "<div>"+
                "<i>"+
                    discussion[1]+
                "</i><br/>"+
                `<div class="discuss-title ${classDiscussion}">`+
                    "<strong>"+
                        discussion[3]+
                    "</strong>"+
                    "<div>"+
                        discussion[2]+
                    "</div>"+
                "</div>"+
            "</div>"+
            `<div class='discuss-option'>${statusDiscussion}</div>`+
        "</div>");
    })
    data.filter(discussion=>discussion[7]!=="0").map((discussion,key)=>{
        const statusDiscussion = discussion[6]?"Taken Down":discussion[5]?"Reported":"";
        const classDiscussion = discussion[6]?"danger":discussion[5]?"warning":"";
        $(`#discuss-no-${discussion[7]}`).after(`<div onclick="alert(${discussion[0]})" id="discuss-no-${discussion[0]}" class="discuss-container level-1">`+
            "<div>"+
                "<i>"+
                    discussion[1]+
                "</i><br/>"+
                `<div class="discuss-title ${classDiscussion}">`+
                    "<strong>"+
                        discussion[3]+
                    "</strong>"+
                    "<div>"+
                        discussion[2]+
                    "</div>"+
                "</div>"+
            "</div>"+
            `<div class='discuss-option'>${statusDiscussion}</div>`+
        "</div>");
    })
});

if(jQuery('#tableDiscussion').length>0){
	tableDiscussion = jQuery('#tableDiscussion')
	.on('preXhr.dt', function ( e, settings, data ){
		NProgress.start();
	}).DataTable({
			"order"				: [[ 0, "desc" ]],
			"responsive"	  	: true,
			"bProcessing"		: true,
			"bServerSide"		: true,
			"sAjaxSource"		: "<?=base_url("api_admin/community/discussion/list/$list_post->id"); ?>",
			"fnServerData"	: function (sSource, aoData, fnCallback, oSettings) {
				oSettings.jqXHR = $.ajax({
					dataType 	: 'json',
					method 		: 'POST',
					url 		: sSource,
					data 		: aoData
				}).success(function (response, status, headers, config) {
					NProgress.done();
					console.log(response);
					$('#tableDiscussion > tbody').off('click', 'tr');
					$('#tableDiscussion > tbody').on('click', 'tr', function (e) {
						e.preventDefault();
						var id = $(this).find("td").html();
						ieid = id;
						$("#modal_option").modal("show");
					});
					fnCallback(response);
				}).error(function (response, status, headers, config) {
					NProgress.done();
					gritter("<h4>Error</h4><p>Cant fetch data right now, please try again later</p>",'warning');
				});
			},
	});
	$('.dataTables_filter input').attr('placeholder', 'Search category name');
}

let status_post = $("#status_report").text();
if(status_post == "1") {
    $("#b_report_post").hide();
    $("#message_status_report").show();
} else {
    $("#b_report_post").show();
    $("#message_status_report").hide();
}

let status_delete = $("#status_delete").text();
if(status_delete == "1") {
    $("#b_delete_post").hide();
    $("#message_status_delete").show();
} else {
    $("#b_delete_post").show();
    $("#message_status_delete").hide();
}

$("#b_report_post").on("click", (e) => {
    e.preventDefault();
    let community_id = $("#community_id").text();
    let confirm_message = confirm("Are you sure to report this post?");
    if(confirm_message) {
        NProgress.start();
        var url = '<?=base_url('api_admin/community/listing/report_from_admin/'); ?>'+community_id;
        $.get(url).done(function(response){
            NProgress.done();
            if(response.status=="200"){
                gritter('<h4>Success</h4><p>Post Successfully Reported</p>','success');
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

$("#b_delete_post").on("click", (e) => {
    e.preventDefault();
    let community_id = $("#community_id").text();
    let admin_name = $("#admin_name").text();
    let confirm_message = confirm("Are you sure to delete this post?"); // same like takedown
    if(confirm_message) {
        NProgress.start();
        var url = '<?=base_url() ?>api_admin/community/listing/delete_from_admin/?community_id=' +community_id+'&admin_name='+admin_name;
        $.get(url).done(function(response){
            NProgress.done();
            if(response.status=="200"){
                gritter('<h4>Success</h4><p>Post Successfully Deleted</p>','success');
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