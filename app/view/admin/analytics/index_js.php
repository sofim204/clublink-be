$(".arrow").on("click", function() {
    if ($(this).find('span').attr('data-active') == 'active') {
        $(this).find('span').addClass("fa-arrow-right tes").removeClass('fa-arrow-down tes');
        $(this).find('span').attr('data-active', 'no');
    } else {
        $(this).find('span').addClass("fa-arrow-down tes").removeClass('fa-arrow-right tes');
        $(this).find('span').attr('data-active', 'active');
    }
})

$("#download").on("click", function() {
    var urlParams = new URLSearchParams(window.location.search);

    $.ajax({
        url: '<?=base_url("api_admin/analytics/exportExcel")?>',
        type: "POST",
        dataType: "json",
        data: {
            type: "detail",
            from_date: urlParams.get('from_date'),
            to_date: urlParams.get('to_date')
        },
        beforeSend: function() {
            Swal.fire({
                title: "Loading...",
                html: "Please wait a moment"
              })
              Swal.showLoading()              
        },
        success: function(data){
            var $a = $("<a>");
            $a.attr("href",data.file);
            $("body").append($a);
            $a.attr("download", data.filename);
            $a[0].click();
            $a.remove();
            Swal.fire({
                title: "Finished!",
                html: "Data Successfuly Downloaded",
                showConfirmButton: false,
                timer: 1000
              });
            Swal.hideLoading()              
        },	
        error: function(data){
            $(this).attr("disabled", false);
			console.log(data);
        }
    });
});

$("#download-summary").on("click", function() {
    var urlParams = new URLSearchParams(window.location.search);

    $.ajax({
        url: '<?=base_url("api_admin/analytics/exportExcel")?>',
        type: "POST",
        dataType: "json",
        data: {
            type: "summary",
            from_date: urlParams.get('from_date'),
            to_date: urlParams.get('to_date')
        },
        beforeSend: function() {
            Swal.fire({
                title: "Loading...",
                html: "Please wait a moment"
              })
              Swal.showLoading()              
        },
        success: function(data){
            var $a = $("<a>");
            $a.attr("href",data.file);
            $("body").append($a);
            $a.attr("download", data.filename);
            $a[0].click();
            $a.remove();
            Swal.fire({
                title: "Finished!",
                html: "Data Successfuly Downloaded",
                showConfirmButton: false,
                timer: 1000
              });
            Swal.hideLoading()              
        },	
        error: function(data){
            $(this).attr("disabled", false);
			console.log(data);
        }
    });
});

<!-- $('#from_date, #to_date').datepicker('setDate', 'today'); -->
