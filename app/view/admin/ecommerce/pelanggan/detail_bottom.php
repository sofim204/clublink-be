var ieid = '<?=$pelanggan->id?>';
var is_unlock = 0;
function gritter(pesan,jenis='info'){
	$.bootstrapGrowl(pesan, {
		type: jenis,
		delay: 3500,
		allow_dismiss: true
	});
}

$("#bactivated").on('click',function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/ecommerce/pelanggan/activated/")?>"+ieid).done(function(dt){
		NProgress.done();
		$("#modal_option").modal("hide");
		if(dt.status == "200"){
			gritter("<h4>Success</h4><p>User activated.</p>",'success');
      window.location.reload();
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
	}).fail(function(e){
		NProgress.done();
		gritter("<h4>Error</h4><p>Cant change user activation right now, please try again.</p>",'warning');
	})
});

$("#bdeactivated").on('click',function(e){
	e.preventDefault();
	NProgress.start();
	$.get("<?=base_url("api_admin/ecommerce/pelanggan/deactivated/")?>"+ieid).done(function(dt){
		NProgress.done();
		$("#modal_option").modal("hide");
		if(dt.status == "200"){
			gritter("<h4>Success</h4><p>User deactivated.</p>",'success');
      window.location.reload();
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
	}).fail(function(e){
		NProgress.done();
		gritter("<h4>Error</h4><p>Cant change user activation right now, please try again.</p>",'warning');
	})
});

$("#bemail_konfirmasi").on('click',function(e){
	e.preventDefault();
	var c = confirm('Are you sure?');
	if(c){
		NProgress.start();
		$.get("<?=base_url("api_admin/ecommerce/pelanggan/email_konfirmasi/")?>"+ieid).done(function(dt){
			NProgress.done();
			$("#modal_option").modal("hide");
			if(dt.status == "200"){
				gritter("<h4>Success</h4><p>Registration confirmation link email has been sent</p>",'success');
        //window.location.reload();
			}else{
				gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
			}
		}).fail(function(e){
			NProgress.done();
			gritter("<h4>Error</h4><p>Cant send email right now, please try again.</p>",'warning');
		});
	}
});


$("#bemail_lupa").on('click',function(e){
	e.preventDefault();
	var c = confirm('Are you sure?');
	if(c){
		NProgress.start();
		$.get("<?=base_url("api_admin/ecommerce/pelanggan/email_lupa/")?>"+ieid).done(function(dt){
			NProgress.done();
			$("#modal_option").modal("hide");
			if(dt.status == "200"){
				gritter("<h4>Success</h4><p>Reset password link has been sent</p>",'success');
        //window.location.reload();
			}else{
				gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
			}
		}).fail(function(e){
			NProgress.done();
			gritter("<h4>Error</h4><p>Cant send email right now, please try again later.</p>",'warning');
		});
	}
});

$(".btn-bank-acc-locker").on("click",function(e){
	e.preventDefault();
	//$(this).attr("class","btn btn-default btn-bank-acc-locker");
	if(is_unlock == 0){
		$(this).html('<i class="fa fa-unlock"></i> Unlock');
		$(".form-bank-acc").prop("disabled",false);
		is_unlock = 1;
	}else{
		$(this).html('<i class="fa fa-lock"></i> Locked');
		$(".form-bank-acc").prop("disabled",true);
		is_unlock = 0;
	}
});
$("#form_bank_account").on("submit",function(e){
	e.preventDefault();
	if(is_unlock == 0){
		gritter("<h4>Caution</h4><p>Please unlock the bank account form before save changes.</p>",'info');
		return false;
	}
	NProgress.start();
	var fd = $(this).serialize();
	$.post('<?=base_url("api_admin/ecommerce/pelanggan/bank_account/")?>',fd).done(function(dt){
		NProgress.done();
		if(dt.status == 200){
			gritter("<h4>Success</h4><p>Bank account has been updated</p>",'success');
			$(".btn-bank-acc-locker").trigger("click");
		}else{
			gritter("<h4>Failed</h4><p>"+dt.message+"</p>",'danger');
		}
	}).fail(function(){
		NProgress.done();
		gritter("<h4>Error</h4><p>Cant change bank account right now, please try again later.</p>",'warning');

	});
});

const modal = document.getElementById("modalPreview");

const img = document.getElementById("userImage");
const modalImg = document.getElementById("image-preview");
const captionText = document.getElementById("caption");

img.onclick = function() {
	modal.style.display = "block";
	modalImg.src = this.src;
	captionText.innerHtml = this.alt;
}

const span = document.getElementsByClassName("close")[0];
span.onclick = function() {
	modal.style.display = "none";
}

// Function to render club data
function renderClubData(data) {
    const tbody = document.getElementById('clubData');
    // Clear existing content
    tbody.innerHTML = '';
    // Render new data
    data.forEach(club => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
			<td style='font-size: 14px;'>${club.club_name}</td>
			<td style='font-size: 14px; text-align: center;'>${club.count_total_post}</td>
        `;
        tbody.appendChild(tr);
    });
}

const loadClubData = () => {
	var url = '<?=base_url('api_admin/band/group/check_user/'); ?>'+ieid;
	$.get(url).done(function(response){
		NProgress.done();
		if(response.status==200){
			//gritter('<h4>Success</h4><p>Get Data</p>','success');
			console.log(response.data.join_in_which_club)
			if(response.data.join_in_which_club.length === 0) {
				$("#header_table").addClass("hidden")
			} else {
				$("#header_table").removeClass("hidden")
				renderClubData(response.data.join_in_which_club);
			}
		}else{
			gritter('<h4>Failed</h4><p>'+response.message+'</p>','danger');
		}
	}).fail(function() {
		NProgress.done();
		gritter('<h4>Error</h4><p>, please try again later</p>','danger');
	});
}

$(document).ready(function(){
	loadClubData()
});