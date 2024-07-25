var baseUrl = '<?=base_url()?>';
const url = baseUrl + "api_admin/crm/chat/";
var ieid = '<?=$chat_room_id?>';
var growlPesan = '<h4>Error</h4><p>Cannot be proceed, please try again later!</p>';
var growlType = 'danger';
var drTable = {};
var showMessageBoard = false;

//Elements
const chatRoom = document.getElementById("chat_room");
const participantArea = document.getElementById("participant_area");
const room_id = document.getElementById("chat_room_id").value;
const user_id = document.getElementById("user_id").value;
const roomType = document.getElementById("chat_type").value;

const gritter = ( pesan, jenis='info' )=>{
    $.bootstrapGrowl(pesan, {
        type: jenis,
        delay: 3500,
        allow_dismiss: true
    });
}

const getDate = (payload) => {
    const monthNames = [
        "January", "February", "March", "April", 
        "May", "June", "July", "August", 
        "September", "October", "November", "December"
    ];
    
    let dateObj;
    if (payload) {
        dateObj = new Date(payload);
    } else {
        dateObj = new Date();
    }
    
    const month = monthNames[dateObj.getMonth()];
    const day = String(dateObj.getDate()).padStart(2, '0');
    const year = dateObj.getFullYear();
    const hour = dateObj.getHours();
    const minute = dateObj.getMinutes();
    const output = month + ' ' + day + ', ' + year + ' ' + hour + ':' + minute;
    return output;
}

const loadMessage = async (payload, userId) => {
    let roomId = 0;
    if(!payload) {
        roomId = await fetch(url+'get_room_admin/'+userId)
            .then(response => response.json())
            .then(({data}) => {
                return data;
            });
    }
    else roomId = payload;

    document.getElementById("caption_room_id").innerHTML = `Room #${roomId}`;
    document.getElementById("chat_room_id").value = roomId;

    fetch(url+'get_chat/'+roomId)
        .then(response => response.json())
        .then(response => {
            //console.log(response)
            chatRoom.innerHTML= "";
            response.data.map((data)=>{
                const messageDate = data[2];
                const userName = data[6];
                const chatType = data[8];
                const messageContent = data[9];
                const roomType = data[10];
                const attachmentPayload = data[11];

                const attachment = document.createElement("div");
                const header = document.createElement("h1");
                const message = document.createElement("div");
                const timestamp = document.createElement("i");
                const offer_status = document.createElement("div");

                if(roomType=='admin') toggleMessageBoard(true);
                else toggleMessageBoard(false);
                
                if(chatType=='announcement'){
                    timestamp.className = "align-center";
                    timestamp.appendChild(document.createTextNode(getDate(messageDate)));
                    message.className = "chats align-center shadow";
                    message.style = "background:#80ADCB";
                    message.align = "center";
                    message.appendChild(document.createTextNode(messageContent));
                }
                else if(userName=="Admin"){
                    timestamp.className = "align-flex-end";
                    timestamp.appendChild(document.createTextNode(getDate(messageDate)));
                    message.className = "chats align-flex-end shadow";
                    header.appendChild(document.createTextNode(userName));
                    message.appendChild(header);
                    message.appendChild(document.createTextNode(messageContent));
                    attachment.className = "attachment align-flex-end shadow";
                }
                else if(roomType=='offer' || roomType=='offering') {
                    timestamp.className = "align-flex-start";
                    timestamp.appendChild(document.createTextNode(getDate(messageDate)));
                    message.className = "chats align-flex-start shadow";
                    header.appendChild(document.createTextNode(userName));
                    if(chatType==='offering') {
                        offer_status.innerHTML = `status = <i class="fa fa-shopping-basket" aria-hidden="true"></i> `;
                        offer_status.className = "chats-offer-offering";
                    } else if(chatType==='accepted') {
                        offer_status.innerHTML = `status = <i class="fa fa-check-square" aria-hidden="true"></i> `;
                        offer_status.className = "chats-offer-accepted";
                    } else if(chatType==='rejected') { 
                        offer_status.innerHTML = `status = <i class="fa fa-window-close" aria-hidden="true"></i> `;
                        offer_status.className = "chats-offer-rejected";
                    } else if(chatType==='cancelled') { 
                        offer_status.innerHTML = `status = <i class="fa fa-ban" aria-hidden="true"></i> `;
                        offer_status.className = "chats-offer-cancelled";
                    } else {
                        offer_status.className = "not-chats-offer";
                    }
                    offer_status.appendChild(document.createTextNode(chatType));
                    message.appendChild(header);
                    message.appendChild(document.createTextNode(messageContent));
                    message.appendChild(offer_status);
                    attachment.className = "attachment shadow";
                }
                else {
                    timestamp.className = "align-flex-start";
                    timestamp.appendChild(document.createTextNode(getDate(messageDate)));
                    message.className = "chats align-flex-start shadow";
                    header.appendChild(document.createTextNode(userName));
                    message.appendChild(header);
                    message.appendChild(document.createTextNode(messageContent));
                    attachment.className = "attachment shadow";
                }
                
                chatRoom.prepend(timestamp);
                chatRoom.prepend(message);

                if(Array.isArray(attachmentPayload) && attachmentPayload.length>0){
                    attachmentPayload.map((attachmentData)=>{
                        const titleBarter = document.createElement("h5");
                        const titleExchange = document.createElement("h5");
                        const linkUrl = document.createElement("a");
                        const linkUrlBarter = document.createElement("a");
                        const linkUrlExchange = document.createElement("a");
                        const linkUrlOffer = document.createElement("a");
                        const listContent = document.createElement("ul");
                        const imgLink = document.createElement("img");
                        const imgLinkBarter = document.createElement("img");
                        const imgLinkExchange = document.createElement("img");
                        const imgLinkOffer = document.createElement("img");
                        const productName = document.createElement("li");
                        const productNameBarter = document.createElement("li");
                        const productNameExchange = document.createElement("li");
                        const productNameOffer = document.createElement("li");
                        const productPrice = document.createElement("li");
                        const productPriceBarter = document.createElement("li");
                        const productPriceExchange = document.createElement("li");
                        const productPriceOffer = document.createElement("li");
                        const invoiceId = document.createElement("li");
                        const orderStatus = document.createElement("li");
                        const textDoneReviewed = document.createElement("p");
                        const textRejectedOffer = document.createElement("p");

                        titleBarter.style="font-size:12pt; font-weight:bold;";
                        titleExchange.style="font-size:9pt; font-weight:bold; text-align:center; margin-top:30px;";
                        linkUrl.style="font-size:12pt; font-weight:bold;";
                        linkUrlBarter.style="font-size:12pt;";
                        linkUrlExchange.style="font-size:12pt;";
                        linkUrlOffer.style="font-size:12pt;";
                        imgLink.style="max-width:300px; margin: .5em";
                        imgLinkBarter.style="max-width:300px; ";
                        imgLinkExchange.style="max-width:300px; margin: .5em";
                        imgLinkOffer.style="max-width:300px; margin: .5em";
                        listContent.style="list-style:none; padding:0 1.5em";
                        productName.style="margin:1em 0";
                        productNameBarter.style="margin-top: -12px;";
                        productNameExchange.style="margin:1em 0";
                        productNameOffer.style="margin-top: -60px; margin-left: 60px;";
                        productPrice.style="margin:1em 0";
                        productPriceBarter.style="margin-top:5px;";
                        productPriceExchange.style="margin-top:-10px;";
                        productPriceOffer.style="margin:5px 0 16px 60px";
                        invoiceId.style="margin:1em 0;";
                        orderStatus.style="margin:1em 0";
                        textDoneReviewed.style="margin-left: -12px; font-size:9pt; color:#17D824";
                        textRejectedOffer.style="margin-left: -12px; font-size:9pt; color:#FF0A0A";
                        <!-- attachment.style="float:left"; -->

                        if(attachmentData.type === 'product') {
                            const __url = baseUrl + attachmentData.thumb;
                            //const __url = baseUrl + 'media/chat-iconpack/send-button.png';
                            imgLink.style="max-height:120px; margin: .5em";
                            imgLink.src = __url;

                            linkUrl.href = baseUrl + 'a/ecommerce/produk/detail/'+attachmentData.url;
                            linkUrl.innerText = `Product Name : ${attachmentData.product_name}`;
                            productName.appendChild(linkUrl)
                            productPrice.appendChild(document.createTextNode(`Price : ${attachmentData.price}`))

                            listContent.appendChild(productName);
                            listContent.appendChild(productPrice);
                            attachment.appendChild(imgLink);
                            attachment.appendChild(listContent);
                        } else if(attachmentData.type === 'barter_request' || attachmentData.type === 'barter_exchange') {
                            const __url = baseUrl + attachmentData.thumb;

                            if(attachmentData.type_barter == "Barter Request") {
                                imgLinkBarter.style=" float: left; margin-right: 10px; width: 60px; height: 60px;";
                                imgLinkBarter.src = __url;
                                linkUrlBarter.href = baseUrl + 'a/ecommerce/produk/detail/'+attachmentData.url;
                                titleBarter.innerText = `Barter Request`;
                                //titleBarter.innerHTML = `<br />`;
                                linkUrlBarter.innerText = `${attachmentData.product_name}`;
                                attachment.appendChild(titleBarter)

                                productNameBarter.appendChild(linkUrlBarter)
                                productPriceBarter.appendChild(document.createTextNode(` ${attachmentData.price}`))
                                //productPriceBarter.className = "bottom_line";
                                titleExchange.innerText = `Exchange With`;
                                productPriceBarter.appendChild(titleExchange);
                            } else if(attachmentData.type_barter == "Barter Exchange")  {
                                linkUrlExchange.href = baseUrl + 'a/ecommerce/produk/detail/'+attachmentData.url;
                                imgLinkExchange.style="float:left; margin-right: 10px; margin-top:10px; width: 60px; height: 60px;";
                                imgLinkExchange.src = __url;
                                linkUrlExchange.innerText = ` ${attachmentData.product_name}`;
                                productPriceExchange.appendChild(document.createTextNode(` ${attachmentData.price}`))
                                //productNameExchange.appendChild(titleExchange);
                                productNameExchange.appendChild(linkUrlExchange);
                            }

                            listContent.appendChild(productNameBarter);
                            listContent.appendChild(productPriceBarter);
                            listContent.appendChild(productNameExchange);
                            listContent.appendChild(productPriceExchange);
                            attachment.appendChild(imgLinkBarter);
                            attachment.appendChild(imgLinkExchange);
                            attachment.appendChild(listContent);
                        }
                        else if(attachmentData.type === 'order') {
                            linkUrl.href = baseUrl + 'a/ecommerce/transactionhistory/detail/'+attachmentData.url;
                            linkUrl.innerText = `Invoice ID: ${attachmentData.invoice_id}`;
                            invoiceId.appendChild(linkUrl)
                            orderStatus.appendChild(document.createTextNode(`Status : ${attachmentData.order_status}`))
                            listContent.appendChild(invoiceId);
                            listContent.appendChild(orderStatus);
                            attachment.appendChild(listContent);
                        }
                        else if(chatType === 'offer' || chatType === 'offering' || roomType === 'offer' || roomType === 'offering') {
                            const __url = baseUrl + attachmentData.thumbnail;
                            imgLinkOffer.style="max-height:120px; margin: .5em; width: 60px; height: 60px;";
                            imgLinkOffer.src = baseUrl + '/' + attachmentData.url;
                            linkUrlOffer.href = baseUrl + '/' + attachmentData.url;
                            linkUrlOffer.innerText = ` ${attachmentData.product_name_offer} `;
                            productNameOffer.appendChild(linkUrlOffer)
                            productPriceOffer.appendChild(document.createTextNode(`${attachmentData.harga_jual}`))

                            listContent.appendChild(productNameOffer);
                            listContent.appendChild(productPriceOffer);
                            attachment.appendChild(imgLinkOffer);
                            attachment.appendChild(listContent);
                        } 
                        else {
                            const __url = baseUrl + attachmentData.url;
                            //const __url = baseUrl + 'media/chat-iconpack/send-button.png';
                            linkUrl.href = __url;
                            linkUrl.target = "_blank";
                            imgLink.src = __url;
                            linkUrl.appendChild(imgLink);
                            attachment.appendChild(linkUrl);
                        }
                         
                    })
                    chatRoom.prepend(attachment);
                }
            })
            chatRoom.scrollTo(0, 0);
            document.getElementById("message").value = "";
            document.getElementById("message").focus();
        });
}

const loadParticipant = () => {
    toggleMessageBoard(false);
    fetch(url+'get_participant/'+room_id)
        .then(response => response.json())
        .then(response => {
            participantArea.innerHTML= "";
            const totalParticipant = response.recordsTotal;
            if(totalParticipant>1){
                const chatRoomItem = document.createElement("div");
                chatRoomItem.className = "menu-item active";
                chatRoomItem.addEventListener('click', ()=>{
                    loadMessage(ieid);
                    document.getElementById("chat_room_id").value = ieid;
                    toggleMessageBoard(false);
                    $(".menu-item.active").attr('class', 'menu-item');
                    chatRoomItem.className = "menu-item active";
                });
                chatRoomItem.appendChild(document.createTextNode(`${roomType.charAt(0).toUpperCase()+roomType.slice(1)} Room #${room_id}`));
                participantArea.appendChild(chatRoomItem);
                response.data.map((data)=>{
                    const menuItem = document.createElement("div");
                    const userId = data[4];
                    const participantName = data[5];
                    const roomAdmin = data[6];
                    menuItem.className = "menu-item";
                    menuItem.appendChild( document.createTextNode(participantName) );
                    menuItem.addEventListener('click', ()=>{
                        document.getElementById("chat_room_id").value = roomAdmin;
                        loadMessage(roomAdmin, userId);
                        toggleMessageBoard(true);
                        document.getElementById("user_id").value = userId;
                        $(".menu-item.active").attr('class', 'menu-item');
                        menuItem.className = "menu-item active";
                    });
                    participantArea.appendChild(menuItem);
                })
                toggleMessageBoard(false);
            }
            else{
                response.data.map((data)=>{
                    const userId = data[4];
                    const menuItem = document.createElement("div");
                    menuItem.className = "menu-item active";
                    menuItem.appendChild(document.createTextNode(data[5]));
                    menuItem.addEventListener('click', ()=>loadMessage(roomId));
                    participantArea.appendChild(menuItem);
                    document.getElementById("user_id").value = userId;
                })
                if(roomType==='admin') toggleMessageBoard(true);
            }
            loadMessage(room_id);
        });
}

const toggleMessageBoard = (status) =>{
    if(status) {
        document.getElementById("message_container").className = "shadow chat";
        document.getElementById("message_board").className = "__container-type";
    }
    else {
        document.getElementById("message_container").className = "shadow chat full";
        document.getElementById("message_board").className = "__container-type hide";
    }
}

const sendMessage = (roomId, textMessage)=>{
    postData(url+'send_chat_admin/'+roomId, { 
        roomId, textMessage,
    }).then(data => loadMessage(roomId) );
}

const resetForm = () => {
    $('#product_attachment').val(null).trigger('change');
    $('#buyer_invoice').val(null).trigger('change');
    $('#seller_invoice').val(null).trigger('change');
    $('#image_attachment').val(null);
    $('#message_attachment').val(null);
}

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

    $("#product_attachment").select2({
        ajax: { 
            url: baseUrl + "api_admin/ecommerce/produk/getproductajax/",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term, // search term
                    user_id: document.getElementById("user_id").value,
                };
            },
            processResults: function (response) {
                response.unshift({id: '', text: '===== Cancel your selection ====='})
                return {
                    results: response
                };
            }
        }
    });

    $("#buyer_invoice").select2({
        ajax: { 
            url: baseUrl + "api_admin/ecommerce/transaction/getinvoiceajax/",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term, // search term
                    user_id_buyer: document.getElementById("user_id").value,
                    user_id_seller: 0,
                };
            },
            processResults: function (response) {
                response.unshift({id: '', text: '===== Cancel your selection ====='})
                return {
                    results: response
                };
            }
        }
    });

    $("#seller_invoice").select2({
        ajax: { 
            url: baseUrl + "api_admin/ecommerce/transaction/getinvoiceajax/",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term, // search term
                    user_id_buyer: 0,
                    user_id_seller: document.getElementById("user_id").value,
                };
            },
            processResults: function (response) {
                response.unshift({id: '', text: '===== Cancel your selection ====='})
                return {
                    results: response
                };
            }
        }
    });

    $("#send_chat_admin").on("submit",function(e){
        e.preventDefault();
        NProgress.start();
        var formPayload = new FormData($(this)[0]);

        var productAttachment = $('#product_attachment').val();
        var buyerInvoice = $('#buyer_invoice').val();
        var sellerInvoice = $('#seller_invoice').val();
        var messageAttachment = $('#message_attachment').val();
        var roomId = document.getElementById("chat_room_id").value;
        var urlMessage = url +'send_chat_admin/'+roomId;
        var isSelected = 0;

        if(productAttachment){
            isSelected += 1;
        }
        if(buyerInvoice){
            isSelected += 1;
        }
        if(sellerInvoice){
            isSelected += 1;
        }
        if( document.getElementById("image_attachment").files.length != 0 ){
            isSelected += 1;
        }
        if(isSelected == 0 ){
            NProgress.done();
            growlPesan = '<h4>Failed</h4><p></p>';
            growlType = 'danger';
            gritter('<h4>Failed</h4><p>Please input at least one option</p>','danger');
            return false;
        }
        if(isSelected >= 2 ){
            NProgress.done();
            growlPesan = '<h4>Failed</h4><p></p>';
            growlType = 'danger';
            gritter('<h4>Failed</h4><p>Can only attach one kind at a time</p>','danger');
            return false;
        }

        $.ajax({
            type: $(this).attr('method'),
            url: urlMessage,
            data: formPayload,
            processData: false,
            contentType: false,
            success: function(respon){
                NProgress.done();
                if(respon.status==200){
                    $('#send_chat_admin')[0].reset(); // Clear the form
                    gritter('<h4>Success</h4><p>Message has been send!</p>','success');
                }else{
                    growlPesan = '<h4>Failed</h4><p></p>';
                    growlType = 'danger';
                    gritter('<h4>Failed</h4><p>'+respon.message+'</p>','danger');
                }
                resetForm();
                loadMessage(roomId);
                $("#__message_attachment").modal("hide")
            },
            error:function(){
                NProgress.done();
                gritter('<h4>Error</h4><p>Cannot add chat right now, please try again later</p>','warning');
                return false;
            }
        });
    });

    $("#reset_attach").on("click",function(e){
        e.preventDefault();
        resetForm();
    });
        
    const sendButton = document.getElementById("send_button");
    sendButton.addEventListener("click", ()=>{
        const textMessageContainer = document.getElementById("message");
        if(textMessageContainer.value.length>0){
            sendMessage(
                document.getElementById("chat_room_id").value,
                document.getElementById("message").value
            );
        }
        else{
            growlPesan = '<h4>Failed</h4><p></p>';
            growlType = 'danger';
            gritter('<h4>Failed</h4><p>Do not put blank message</p>','danger');
        }
    }); 
        
    const attachButton = document.getElementById("attach_button");
    attachButton.addEventListener("click", ()=>$("#__message_attachment").modal("show")); 

    $(".mandatory-form").on('change', function(event){
        event.stopPropagation();
        event.stopImmediatePropagation();
        if($(this).val()==="") $('.mandatory-form').prop("disabled", false); 
        else $('.mandatory-form').prop("disabled", true); 
        $(this).prop("disabled", false); 

        console.log($(this).val())
    });

    loadParticipant();
});
