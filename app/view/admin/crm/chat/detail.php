<?php
    function getCurrentDate($payload=null){
        if ($payload) {
            // return date("F j, Y, g:i a");
            return date("F j, Y G:i", strtotime($payload));
        } else {
            return date("F j, Y G:i");
        }
    }
    
    // by Muhammad Sofi 7 January 2022 16:10 | change the output text
    if (isset($chat_type)) {
        if ($chat_type == 'buyandsell') {
            $chat_type = 'Buy and Sell';
        }
    }
?>
<style>
    .__container, .__container-type{
        /* border: 1px dashed black; */
        display:flex;
        height: 80vh;
        padding: 0 3em;
        margin : 0 auto;
        width: 100%;
    }
    .__container-type{
        background: #FEFEFE;
        display: flex;
        height: 115px;
        justify-content: space-between;
        left:0;
        padding: 1.5em;
        position: absolute;
        bottom:0;
        width: 100%;
    }
    .__container-type.hide{
        visibility: hidden;
    }
    .chat, .menu, .type{
        align-items: flex-start;
        background: #FFF;
        border-radius: .25em;
        display: flex;
        max-height: 100%;
        padding: 1.5em;
        overflow-y: auto;
    }
    .chat{
        background: #E3BE9B;
        padding: 0;
        padding-top:75px;
        padding-bottom:115px;
        position: relative;
        width: 70%;
    }
    .chat.full{
        padding-bottom:0px;
    }
    #chat_header{
        background: #FEFEFE;
        height:75px;
        left:0;
        padding: .25em 2.5em;
        position: absolute;
        top:0;
        width: 100%;
    }
    #chat_room{
        display: flex;
        flex-direction: column;
        flex-direction: column-reverse;
        max-height: 100%;
        overflow-y: auto;
        padding: 1.5em;
        width: 100%;
    }
    .chats{
        background: white;
        /* border: 1px solid #DDD; */
        border-radius: 5px;
        margin: 1em 0;
        min-width: 250px;
        max-width: 350px;
        padding: .5em 1em;
    }
    .chats > h1 {
        border-bottom: 1px solid #DDD;
        font-size: 10pt;
        font-weight: bolder;
        margin: 0;
        margin-bottom: .5em;
        padding-bottom: .5em;
    }
    .attachment{
        background: white;
        border: 1px solid #DDD;
        border-radius: 5px;
        display: block;
        height: 100%;
        margin-bottom: 2em;
        padding: .5em;
        position: relative;
        width : 50%;
    }

    .align-flex-end{align-self:flex-end}
    .align-center{align-self:center}
    .align-flex-start{align-self:flex-start}
    .menu{
        background: #F5F5F5;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 30%
    } 
    .shadow {
        box-shadow: 0 3px 3px rgba(0,0,0,0.2);
    }

    .bottom_line {
        border-bottom: 1px solid #DDD;
        padding-bottom: 10px;
    }

    .message{
        border: 1px solid #DDD;
        border-radius: .25em;
        height: 100%;
        padding: .75em;
        resize: none;
        width : 80%;
    }

    .btn {
        width: 85px;
        cursor: pointer;
        background: transparent;
        border: 1px solid #999;
        outline: none;
        transition: .5s ease;
    }

    .btn.full {
        width: 100%;
    }

    .btn:hover {
        transition: .3s ease;
        background: #DD8A0D;
        border: 1px solid transparent;
        color:#FFF;
    }

    .btn:hover svg {
        stroke-dashoffset: -480;
    }

    .btn span {
        color: white;
        font-size: 18px;
        font-weight: 100;
    }

    #participant_area {
        width: 100%;
    }
    .menu-item {
        cursor: pointer;
        background: transparent;
        border-radius: .5em;
        margin: 1em 0;
        outline: none;
        padding: 1em;
        transition: .5s ease;
        width: 100%;
    }

    .menu-item:hover {
        transition: .5s ease;
        background: #DD8A0D;
        color:#FFF;
    }

    .menu-item.active {
        background: #DD8A0D;
        color:#FFF;
    }

    .menu-item:hover svg {
        stroke-dashoffset: -480;
    }

    .menu-item span {
        color: white;
        font-size: 18px;
        font-weight: 100;
    }

    .not-chats-offer{
        display: none;
    }
    .chats-offer-offering {
        color: blue;
        font-weight: bold;
    }
    .chats-offer-accepted {
        color: green;
        font-weight: bold;
    }
    .chats-offer-rejected, 
    .chats-offer-cancelled {
        color: red;
        font-weight: bold;
    }
</style>
<div id="page-content">
    <!-- Header -->
    <div class="content-header">
        <div class="row" style="padding: 0.5em 2em;">
        <div class="col-md-4">
            <div class="btn-group">
            <!-- <a id="aback" href="<?=base_url_admin('crm/chat/')?>" class="btn btn-default"><i class="fa fa-chevron-left"></i> Back</a> -->
            <!-- by Muhammad Sofi 7 January 2022 16:10 | go back to previous page -->
            <a onclick="history.go(-1)" class="btn btn-default"><i class="fa fa-chevron-left"></i> Back</a>
            </div>
        </div>
        <div class="col-md-8">
            <div class="btn-group pull-right"></div>
        </div>
        </div>
    </div>
    <ul class="breadcrumb breadcrumb-top">
        <li>Admin</li>
        <li>CRM</li>
        <li><a href="<?=base_url_admin("crm/chat/")?>">Chat Room</a></li>
        <li>Detail</li>
        <li><?= '#'.$chat_room_id ;?></li>
        <input type="hidden" id="chat_type" value="<?=$chat_type?>" />
        <input type="hidden" id="user_id" value="" />
    </ul>
    <!-- Header -->

    <div class="__container">
        <div class="shadow menu">
            <h1>Participants</h1>
            <div id="participant_area"></div>
        </div>
        <div id="message_container" class="shadow chat">
            <div class="shadow" id="chat_header">
                <input type="hidden" id="chat_room_id" value="<?=$chat_room_id?>" />
                <h3 id="caption_room_id">
                    <?=ucfirst($chat_type)?> Room #<?=$chat_room_id?>
                </h3>
            </div>
            <div id="chat_room"></div>
            <div id="message_board" class="__container-type">
                <!-- <div class="attachment"></div> -->
                <textarea
                    id="message"
                    class="message" 
                    name="message" 
                    rows="4" 
                ></textarea>
                <div style="display:flex; justify-content: space-between; width:18%">
                    <button id="send_button" class="btn icon">
                        <img src="<?=base_url('media/chat-iconpack/send-button.png')?>" alt=""><br/>
                        <div>Send</div>
                    </button>
                    <button id="attach_button" class="btn icon">
                        <img src="<?=base_url('media/chat-iconpack/attachment.png')?>" alt=""><br/>
                        <div>Attach</div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?=base_url("js/fetch.js")?>"></script>
