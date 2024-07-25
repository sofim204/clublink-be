<style>
    .btn {
        margin-left: 0px;
        margin-right: 0px;
    }

    .btn-warning {
        font-family: Raleway-SemiBold;
        font-size: 10px;
        color: rgba(240, 173, 78, 0.75);
        letter-spacing: 1px;
        line-height: 5px;
        border: 2px solid rgba(240, 173, 78, 0.75);
        border-radius: 40px;
        background: transparent;
        transition: all 0.3s ease 0s;
    }

    .btn-warning:hover {
        color: #FFF;
        background: rgb(240, 173, 78, 0.75);
        border: 2px solid rgba(240, 173, 78, 0.75);
    }

    input[type="file"] {
        display: block;
    }
    .imageThumb {
        max-height: 75px;
        border: 2px solid;
        padding: 1px;
        cursor: pointer;
    }
    .pip {
        display: inline-block;
        margin: 10px 10px 0 0;
    }
    .remove {
        display: block;
        background: #444;
        border: 1px solid black;
        color: white;
        text-align: center;
        cursor: pointer;
    }
    .remove:hover {
        background: white;
        color: black;
    }

    .image-upload > input {
        display: none;
    }

    .image-upload img {
        width: 80px;
        cursor: pointer;
    }
    .scroll-chat{
        overflow-y: scroll;
        height: 500px;
    }
    table.tabel {

    }
    table.tabel tr {

    }
    table.tabel tr td{
    padding: 0.25em;
    }
    .kartu-box {
    background-color: #f9fafc;
    }
    tr.chat-wrap {
        border: none;
        margin-bottom: 0.5em;
    }
    .table tbody > tr > td.tanpa-border, .table tbody > tr > th.tanpa-border {
        border: none;
    }
    .chat-box {
        border: none;
        background-color: #ffe2c9;
        border-radius: 1em;
        border-top-left-radius:0;
        margin-bottom: 0.5em;
        margin-top: auto;
        margin-bottom: auto;
        margin-right: 10px;
        padding: 10px;
        position: relative;
    }
    .chat-box.chat-box-adm {
        border-radius: 1em;
        border-top-right-radius:0;
        background-color: #d5e2e8;
    }

    .chat-judul {
        margin: 0;
        font-weight: bold;
        margin-bottom: 0.5em;
    }
    .chat-judul a{
        font-weight: bold;
    }
    .chat-isi{
        line-height: 1;
        margin-bottom: 0.5em;
    }
    .chat-tgl {
        font-size: smaller;
        margin-bottom:0
    }
</style>

<!-- Page content -->
<div id="page-content">
    <!-- Static Layout Header -->
    <div class="content-header">
        <div class="row" style="padding: 0.5em 2em;">
        <div class="col-md-4">
            <div class="btn-group ">
            <a id="aback" href="<?=base_url_admin('crm/chat_admin/')?>" class="btn btn-default"><i class="fa fa-chevron-left"></i> Back</a>
            </div>
        </div>
        <div class="col-md-8">
            <div class="btn-group pull-right">
            <!-- <a href="<?='';//base_url_admin("ecommerce/transaction/seller_detail/".$order->id."/".$order->detail->id."/");?>" class="btn btn-default btn-alt"><i class="fa fa-info-circle"></i> View Transaction Detail</a> -->

            </div>
        </div>
        </div>
    </div>
    <ul class="breadcrumb breadcrumb-top">
        <li>Admin</li>
        <li>CRM</li>
        <li><a href="<?=base_url_admin("crm/chat/")?>">Chat Room</a></li>
        <li>Detail</li>
        <li><?= '#'.$chat_room_id ;?></li>
    </ul>
    <!-- END Static Layout Header -->

    <!-- User Profile Content -->
    <div class="row">
        <!-- First Column -->
        <div class="col-md-4">
            <div class="block scroll-chat">
            <!-- <div class="block"> -->
                <div class="block-title">
                    <h2><i class="fa fa-file-text-o"></i> <strong>Information</strong></h2>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <table class="tabel">
                            <tr>
                                <th class="">Chat Starter</th>
                                <td class="">:</td>
                                <td><?= $this->__st($chat_room->starter_fname) ?></td>
                            </tr>
                        </table>
                    </div><!-- end col-md-4 -->
                    <div class="col-md-6">
                        <table class="tabel">
                            <tr>
                                <th class="">Chat Type</th>
                                <td class="">:</td>
                                <td><?= $chat_type ?></td>
                            </tr>
                        </table>
                    </div><!-- end col-md-4 -->
                    <div class="col-md-12" style="margin-top:2em">
                        <div class="btn-group pull-right">
                            <a href="<?=base_url_admin("crm/chat_admin/detail/$chat_room_id/ADMIN/$chat_room->b_user_id_starter/")?>" class="btn btn-danger"><i class="fa fa-wechat"></i> Chat With <?= $chat_room->starter_fname ?></a>
                        </div>
                    </div>
                    <div class="col-md-12">&nbsp;</div>
                </div>
            </div>
            <!-- END Info Block -->
        </div>
        <div class="col-md-8">
            <div class="block scroll-chat" id="scroll-chat-down">
                <!-- Account Status Title -->
                <div class="block-title">
                    <h2><i class="fa fa-wechat"></i> Chat</h2>
                </div>
                <!-- END Account Status Title -->

                <!-- Account Stats Content -->
                <div class="portlet-title" id="divChat">
                    <div class="caption">
                        <table class="table">
                            <tbody>
                                <?php foreach($chats as $cds): ?>
                                    <?php if($cds->b_user_id!=0): ?>
                                        <tr class="chat-wrap">
                                            <td class="tanpa-border text-left" >
                                                <div class="chat-box">
                                                    <h6 class="chat-judul">
                                                    <!-- <a href="#"><?='';//$cds->starter_fname?></a> <span class="label label-info"><?='';//$this->__e($cds->jenis)?></span> -->
                                                    <!-- <a href="#"><?='';//$cds->starter_fname?></a> <span <?php// if ($cds->jenis=="buyer"){?> class="label label-danger"<?php //}else{?> class="label label-danger" <?php //}?>><?='';//$this->__e($cds->jenis)?></span> --><!-- By Aditya Adi Prabowo buyer seller color are red, added comment 27 october 2020 10:44 -->
                                                    <a href="#"><?=$cds->starter_fname?></a>
                                                    </h6>
                                                    <p class="chat-isi">
                                                    <?php if(count($cds->attachments)>0){ ?>
                                                        <div class="row">
                                                        <?php foreach($cds->attachments as $att){?>
                                                            <?php if(strstr($att->jenis,"image")){ ?>

                                                            <div class="col-md-2">
                                                                <a href="<?=base_url($att->url)?>" target="_blank" title="attachment"><img src="<?=base_url($att->url)?>" class="img-responsive" onerror="this.src='<?=base_url()?>media/produk/default.png';"/></a>
                                                            </div>

                                                            <?php } else if($att->jenis == 'product'){ ?>

                                                            <div class="col-md-2">
                                                                <a href="<?=base_url($att->produk_thumb)?>" target="_blank" title="<?=$att->produk_nama?>"><img src="<?=base_url($att->produk_thumb)?>" class="img-responsive" onerror="this.src='<?=base_url()?>media/produk/default.png';"/></a>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <table class="tabel">
                                                                <tr>
                                                                    <th class="">Product Name</th>
                                                                    <td class="">:</td>
                                                                    <td><a href="<?=base_url_admin('ecommerce/produk/detail/'.$att->url)?>" target="_blank" title="<?= $this->__st($att->produk_nama) ?>"><?= $this->__st($att->produk_nama) ?> </a></td>
                                                                </tr>
                                                                <tr>
                                                                    <th class="">Product Price</th>
                                                                    <td class="">:</td>
                                                                    <td><?= $this->__st($att->produk_harga_jual) ?></td>
                                                                </tr>
                                                                </table>
                                                            </div>
                                                                
                                                            <?php } else if($att->jenis == 'order'){ ?>

                                                            <div class="col-md-2">
                                                                <a href="<?=base_url($att->produk_thumb)?>" target="_blank" title="<?=$att->produk_nama?>"><img src="<?=base_url($att->produk_thumb)?>" class="img-responsive" onerror="this.src='<?=base_url()?>media/produk/default.png';"/></a>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <table class="tabel">
                                                                <tr>
                                                                    <th class="">Invoice</th>
                                                                    <td class="">:</td>
                                                                    <td><a href="<?=base_url_admin('ecommerce/transactionhistory/detail/'.$att->url.'/'.$att->order_detail_id)?>" target="_blank" title="<?= $this->__st($att->produk_nama) ?>"><?= $att->order_invoice_code ?> </a></td>
                                                                </tr>
                                                                <tr>
                                                                    <th class="">Status</th>
                                                                    <td class="">:</td>
                                                                    <td><?= $att->order_status->text ?></td>
                                                                </tr>
                                                                </table>
                                                            </div>

                                                            <?php }else{ ?>
                                                                
                                                            <div class="col-md-2">
                                                                <a href="<?=base_url($att->url)?>" target="_blank" title="attachment"><img src="<?=base_url("media/attachment-icon.png")?>" class="img-responsive" /></a>
                                                            </div>

                                                            <?php } ?>
                                                        <?php } ?>
                                                        </div>
                                                    <?php } ?>

                                                    <?php if(count($cds->complains)>0){ ?>

                                                        <?php foreach($cds->complains as $com){?>
                                                        <div class="row">

                                                            <div class="col-md-2">
                                                            <a href="<?=base_url($com->thumb)?>" target="_blank" title="<?=$com->nama?>"><img src="<?=base_url($com->thumb)?>" class="img-responsive" onerror="this.src='<?=base_url()?>media/produk/default.png';"/></a>
                                                            </div>

                                                            <div class="col-md-6">
                                                            <table class="tabel">
                                                                <tr>
                                                                <th class="">Product</th>
                                                                <td class="">:</td>
                                                                <td><?= $this->__st($com->nama) ?></td>
                                                                </tr>
                                                                <tr>
                                                                <th class="">Invoice</th>
                                                                <td class="">:</td>
                                                                <td><a href="<?=base_url_admin('ecommerce/transactionhistory/detail/'.$com->d_order_id.'/'.$com->d_order_detail_id)?>" target="_blank" title="<?= $this->__st($att->produk_nama) ?>"><?= $com->invoice_code ?> </a></td>
                                                                </tr><tr>
                                                                <th class="">Status</th>
                                                                <td class="">:</td>
                                                                <td style="color: red;">Rejected by Buyer</td>
                                                                </tr>
                                                            </table>
                                                            </div>

                                                        </div>
                                                        <strong> Reason: </strong>
                                                        <?php } ?>

                                                    <?php } ?>
                                                    
                                                    <br/>
                                                    <?php echo $this->__e($cds->message)?>
                                                    </p>
                                                    <p class="chat-tgl text-right"><?=date("j F",strtotime($cds->cdate))?></p>
                                                </div>
                                            </td>
                                            <td class="col-md-2 tanpa-border">&nbsp;</td>
                                        </tr>
                                    <?php else: ?>
                                        <tr class="chat-wrap">
                                            <td class="col-md-2 tanpa-border">&nbsp;</td>
                                            <td class="tanpa-border text-left">
                                            <div class="chat-box chat-box-adm">
                                                <h6 class="chat-judul">
                                                <a href="#"><?=$cds->a_pengguna_nama?></a> <span class="label label-info">Administrator</span>
                                                </h6>
                                                <p class="chat-isi">
                                                <?php echo $this->__e($cds->message)?>
                                                <?php if(count($cds->attachments)>0){ ?>
                                                    <div class="row">
                                                    <?php foreach($cds->attachments as $att){?>
                                                        <?php if(strstr($att->jenis,"image")){ ?>

                                                        <div class="col-md-2">
                                                            <a href="<?=base_url($att->url)?>" target="_blank" title="attachment"><img src="<?=base_url($att->url)?>" class="img-responsive" onerror="this.src='<?=base_url()?>media/produk/default.png';"/></a>
                                                        </div>

                                                        <?php } else if($att->jenis == 'product'){ ?>

                                                        <div class="col-md-2">
                                                            <a href="<?=base_url($att->produk_thumb)?>" target="_blank" title="<?=$att->produk_nama?>"><img src="<?=base_url($att->produk_thumb)?>" class="img-responsive" onerror="this.src='<?=base_url()?>media/produk/default.png';"/></a>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <table class="tabel">
                                                            <tr>
                                                                <th class="">Product Name</th>
                                                                <td class="">:</td>
                                                                <td><a href="<?=base_url_admin('ecommerce/produk/detail/'.$att->url)?>" target="_blank" title="<?= $this->__st($att->produk_nama) ?>"><?= $this->__st($att->produk_nama) ?> </a></td>
                                                            </tr>
                                                            <tr>
                                                                <th class="">Product Price</th>
                                                                <td class="">:</td>
                                                                <td><?= $this->__st($att->produk_harga_jual) ?></td>
                                                            </tr>
                                                            </table>
                                                        </div>
                                                            
                                                        <?php } else if($att->jenis == 'order'){ ?>

                                                        <div class="col-md-2">
                                                            <a href="<?=base_url($att->order_thumb)?>" target="_blank" title="<?=$att->order_name?>"><img src="<?=base_url($att->order_thumb)?>" class="img-responsive" onerror="this.src='<?=base_url()?>media/produk/default.png';"/></a>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <table class="tabel">
                                                            <tr>
                                                                <th class="">Invoice</th>
                                                                <td class="">:</td>
                                                                <td><a href="<?=base_url_admin('ecommerce/transactionhistory/detail/'.$att->url.'/'.$att->order_detail_id)?>" target="_blank" title="<?= $this->__st($att->produk_nama) ?>"><?= $att->order_invoice_code ?> </a></td>
                                                            </tr>
                                                            <tr>
                                                                <th class="">Status</th>
                                                                <td class="">:</td>
                                                                <td><?= $att->order_status->text ?></td>
                                                            </tr>
                                                            </table>
                                                        </div>

                                                        <?php }else{ ?>
                                                            
                                                        <div class="col-md-2">
                                                            <a href="<?=base_url($att->url)?>" target="_blank" title="attachment"><img src="<?=base_url("media/attachment-icon.png")?>" class="img-responsive" /></a>
                                                        </div>

                                                        <?php } ?>
                                                    <?php } ?>
                                                    </div>
                                                <?php } ?>
                                                </p>
                                                <p class="chat-tgl text-right"><?=date("j F",strtotime($cds->cdate))?></p>
                                            </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- END Account Status Content -->
            </div>
        </div>
        <div class="col-md-12">
            <div class="block">
                <div class="portlet-title">
                <div class="caption">

                    <?php if(count($complains)){?>
                    <div class="row">
                        <div class="col-md-12">
                        <h6 style="font-weight: bolder; line-height:1; margin-bottom: 0;">Complain(s):</h6>
                        </div>
                        <?php foreach($complains as $cm){ ?>
                        <div class="col-md-1">
                        <img src="<?=$cm->thumb?>" class="img-responsive" />
                        </div>
                        <div class="col-md-4">
                        <h5><?=$cm->nama?></h5>
                        </div>
                        <div class="col-md-2">
                        <p>$<?=$cm->harga_jual.' x '.$cm->qty?></p>
                        </div>
                        <div class="col-md-5">
                        <div class="btn-group pull-right">
                            <button class="btn btn-info btn-xs btn-solve-seller" data-nation_code="<?=$cm->nation_code?>" data-d_order_id="<?='';//$cm->d_order_id?>" data-d_order_detail_id="<?='';//$cm->d_order_detail_id?>" data-d_order_detail_item_id="<?='';//$cm->c_produk_id?>"><i class="fa fa-commenting"></i> Solved to Seller</button>
                            <button class="btn btn-danger btn-xs btn-solve-buyer" data-nation_code="<?='';//$cm->nation_code?>"data-d_order_id="<?='';//$cm->d_order_id?>" data-d_order_detail_id="<?='';//$cm->d_order_detail_id?>" data-d_order_detail_item_id="<?='';//$cm->c_produk_id?>"><i class="fa fa-commenting-o"></i> Solved to Buyer</button>
                            <button class="btn btn-default btn-xs btn-complain-cancel" data-nation_code="<?='';//$cm->nation_code?>"data-d_order_id="<?='';//$cm->d_order_id?>" data-d_order_detail_id="<?='';//$cm->d_order_detail_id?>" data-d_order_detail_item_id="<?='';//$cm->c_produk_id?>">Cancel complain</button>
                        </div>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>

                    <!-- <form id="fsendchat" action="<?php //echo base_url('api_admin/crm/chat/sendMessage/').$chat[0]->order_id;?>" method="POST" enctype="multipart/form-data"> -->
                    <form id="fsendchat" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
                    <input type="hidden" name="chat_room_id" value="<?=$chat_room_id?>" />
                    <input type="hidden" name="c_produk_id" value="<?='';//$order->detail->id?>" />

                    <input type="hidden" name="chat_type" value="<?=$chat_type?>" />

                    <div class="form-group">
                        
                        <div class="col-md-12">
                        <button class="clearSelect2">Reset Attach</button>
                        </div>
                        
                        <div class="col-md-4">
                        <label>
                            <span class="text-danger" style="font-size: 12px; text-decoration: underline;">Attach <?= $chat_room->starter_fname ?>'s Product</span>
                        </label>
                        <select id="productCustomer1" class="form-control" name="productCustomer1">
                            <option value="" selected="selected">-Product Name - Price -</option>
                        </select>
                        </div>
                    
                        <div class="col-md-4">
                        <label>
                            <span class="text-danger" style="font-size: 12px; text-decoration: underline;">Attach <?= $chat_room->starter_fname ?>'s Invoice (Buyer)</span>
                        </label>
                        <select id="orderBuyerCustomer1" class="form-control" name="orderBuyerCustomer1">
                            <option value="" selected="selected">-Invoice - Product name-</option>
                        </select>
                        </div>

                        <div class="col-md-4">
                        <label>
                            <span class="text-danger" style="font-size: 12px; text-decoration: underline;">Attach <?= $chat_room->starter_fname ?>'s Invoice (Seller)</span>
                        </label>
                        <select id="orderSellerCustomer1" class="form-control" name="orderSellerCustomer1">
                            <option value="" selected="selected">-Invoice - Product name-</option>
                        </select>
                        </div>

                    </div>

                    <div class="form-group">
                        <div class="col-sm-1 col-xs-2 reply-files">
                        <div class="image-upload" style="font-size: 2rem;">
                            <label for="files">
                            <span class="text-danger" style="font-size: 12px; text-decoration: underline;">Choose</span>
                            </label>
                            <!-- <input type="file" id="files" name="files[]" multiple /> -->
                            <input type="file" id="files" />
                        </div>
                        </div>
                        <div class="col-sm-9 col-xs-8 reply-message">
                        <input id="imessage" type="text" name="message" class="form-control" minlength="1" placeholder="" required />
                        </div>
                        <div class="col-sm-2 col-xs-2 reply-send">
                        <button id="bsend" type="submit" class="btn btn-primary btn-block">
                            <i class="fa fa-send"></i> Send
                        </button>
                        </div>
                    </div>
                    <div id="attachments" class="form-group"> </div>
                    </form>
                </div>
                </div>
            </div>

        </div>
        <!-- END First Column -->
    </div>
    <!-- END User Profile Content -->
</div>
<!-- END Page Content -->
