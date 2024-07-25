<style>
[class^="icon-"] {
  background: transparent;
  text-shadow: none;
  box-shadow: none;
}
[class^="icon-"]:hover {
  background: transparent;
  text-shadow: none;
  box-shadow: none;
  transition: all 0.5s ease;
  -webkit-transition: all 0.5s ease;
}
.icon-nav {
  margin-top: 30px;
}
.icon-nav a {
  text-align: center;
  display: block;
  background-color: #a9dee4;
  box-shadow: 0 1px 2px 0 rgba(0,0,0,0.15);
  padding: 20px 0;
  font-size: 14px;
  font-weight: 300;
  color: #777777;
  transition: all 0.2s ease-in;
  margin-bottom: 30px;
  font-family: 'Verdana';
}
.icon-nav a:hover {
  color: #FFFFFF;
  background-color: #E87169;
}
.icon-nav a i {
  display: block;
  margin-bottom: 8px;
  font-size: 28px;
}
.icon-nopadding {
  padding:0;
}
.h-nomargin {
  margin: 0;
}
.btn-transparent {
  color: #3c3c3c;
  background-color: transparent;
  border-color: #3c3c3c;
}

.btn-transparent-2 {
  color: #3c3c3c;
  background-color: transparent;
  border: none;
  text-align: left;
  padding: 0.1em;
}
.btn-transparent-2 img {
  border-radius: 4px;
}
.dr-icon-group {
  margin-bottom: 1em;
}
.dr-icon-group small {
  color: #3c3c3c;
  font-weight: 100;
  text-transform: none;
  font-size: 10px;
}
.section-title {
  border-left: 3px #00ef3a solid;
  font-size: 16px;
  padding-left: 0.5em;
  min-height: 22px;
  padding-top: 3px;
  margin-top: 0.5em;
}
.terlaris-list {
  padding-top: 1em;
  margin-right: 0.5em;
  border-radius: 6px;
}
ol.ol-terlaris {
}
ol.ol-terlaris li {
  margin-bottom: 0.25em;
}
ol.ol-terlaris li a {
  font-size: 15px;
  margin-left: 0.5em;
  text-decoration: underline;
}
a.aselengkapnya {
  color: #67c77e;
  text-decoration: underline;
}
a.aselengkapnya:hover, a.aselengkapnya:focus{
  color: #3c3c3c;
  text-decoration: underline;
}
.row.terlaris-list {
  padding-top: 0.25em;
  margin-right: 0.5em;
}
.margintopall10 {
  margin-top: 1em;
}
.label {
	color: #fff;
	font-weight: normal;
	padding: 0.5em;
}

.btn.btn-secondary {
	color: #000;
  background-color: #bbbcbd;
  border-color: #9e9e9e;
}
.btn.btn-secondary:hover {
	color: #000;
  background-color: #ececec;
  border-color: #adadad;
}
.kurir-title {
  color: #cf0000;
  font-weight: 700;
  margin:0;
  line-height: 1;
  font-size: 3em;
}
.wijet-profile-main.biru {
  background-color: #6b737b;
}
.ringan {
  color: #bdbdbd;
  font-size: 1.2em;
  margin: 0;
  margin-bottom: 0.25em;
}
h6 {
  font-size: 1.4em;
  margin:0;
  line-height: 1;
  color: #ededed;
}
.well {
  margin-bottom: 1em;
}
.well.well-success {
 background-color: #f4e3bf;
}
.well-p {
  line-height: 1.2;
  margin: 0;
  font-size: 1.2em;
}
.well-abu {
  line-height: 1;
  color: #545454;
  font-style: italic;
  margin: 0;
}
.fg-abu {
  color: #a9a9a9;
}
.bg-putih {
  background-color: #fff;
}
.bg-orange {
  background-color: #f0ad4e;
}
.bg-blue {
  background-color: #00acc1;
}
.bg-green {
  background-color: #67c368;
}
.bg-red {
  background-color: #c13500;
}
.produk-nama {
  margin:0;
  margin-bottom: 0.5em;
  line-height: 1.3;
  font-weight: 700;
  font-size: 1.1em;
  color: #888888;
}
.produk-properti {
  margin:0;
  margin-bottom: 0.25em;
  line-height: 1.1;
  font-weight: 400;
  font-size: 0.9em;
  color: #888888;
}
.marginbottommobile10 {
  margin-bottom: 1em;
}

.marginbottommobile05 {
  margin-bottom: 0.5em;
}
.cursor-pointer {
  cursor: pointer;
}
@media only screen and (max-width: 768px){
  .margintopmobile10 {
    margin-top: 1em;
  }
  ol.ol-terlaris li a {
    font-size: 0.9em;
  }
}
@media only screen and (max-width: 425px){
  .marginbottommobile10 {
    margin-bottom: 1em;
  }

  .ringan {
    color: #bdbdbd;
    font-size: 0.9em;
    margin: 0;
    margin-bottom: 0.25em;
  }
  h6 {
    margin:0;
    line-height: 1;
    color: #ededed;
  }
}
@media only screen and (max-width: 360px){
  .icon-nav a {
    font-size: 11px;
  }
  .margintopmobile10 {
    margin-top: 1em;
  }
  .row.terlaris-list {
    padding-top: 0;
    margin-right: 0.5em;
  }
}
@media only screen and (max-width: 320px){
  .h-nomargin.icon-nopadding {
    font-size: 1em;
  }
  .dr-icon-group small {
    display:none;
  }
}
</style>
<div class="row">

  <div class="col-md-12 marginbottommobile10">
    <div class="wijet-profile-main biru">
      <div class="row">
        <div class="col-md-6 col-xs-6">
          <p class="ringan">Konsultasi</p>
          <?php if(strlen($info)){ ?>
            <p class="ringan text-info"><?=$info?></p>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-8 marginbottommobile10">
    <div class="wijet-profile-main">
      <?php foreach($order->detail as $od){
        $prop = '';
        if(!empty($od->ukuran)) $prop .= $od->ukuran.' ';
        if(!empty($od->warna)) $prop .= $od->warna.' ';
        if(!empty($od->rasa)) $prop .= $od->rasa.' ';
      ?>
      <div class="row marginbottommobile10">
        <div class="col-xs-3 col-md-2">
          <img src="<?php echo base_url($od->thumb); ?>" class="img-responsive" />
        </div>
        <div class="col-xs-9 col-md-10">
          <h4 class="produk-nama"><a href="<?php echo base_url('produk/'.$od->slug); ?>" title="Lihat detail produk <?php echo $od->nama; ?>"><?php echo $od->nama; ?></a></h4>
          <p class="produk-properti"><?php echo $prop; ?></p>
          <p class="produk-properti"><?php echo 'Rp'.number_format($od->harga_jadi); ?> x <?php echo $od->qty; ?> Pcs</p>
        </div>
      </div>
      <?php } ?>
    </div>
  </div>

  <div class="col-md-4 marginbottommobile10">
    <div class="wijet-profile-main">
      <h5>Toko</h5>
      <?php if(empty($order->penerima_catatan)){ echo '-'; }else{ echo $order->penerima_catatan; } ?>
    </div>
  </div>
</div>

<!-- Message Body -->
<?php $i=0; foreach($konsultasi as $kon){ ?>
  <?php if($kon->b_user_id == $order->b_user_id){ ?>
<tr>
  <td class="text-left" style="width: 80px;">
    <p><strong><?=$kon->b_user_nama?></strong>&nbsp;<small><span class="label label-info">Pembeli</span></small></p>
  </td>
</tr>
<tr>
  <?php if($i==0){ ?>
  <h3><?=$kon->judul?></h3>
  <?php } ?>
  <p><?=$kon->isi?></p>
</tr>
<?php }else{ ?>
<tr>
  <td class="text-left" style="width: 80px;">
    <p><strong><?=$kon->b_user_nama?></strong>&nbsp;<small><span class="label label-danger">Penjual</span></small></p>
  </td>
</tr>
<tr>
  <?php if($i==0){ ?>
  <h3><?=$kon->judul?></h3>
  <?php } ?>
  <p><?=$kon->isi?></p>
</tr>
<?php } ?>
<hr>
<?php $i++; } ?>

<!-- END Message Body -->

<!-- Attachments Row -->


<div class="row block-section">
  <?php foreach($pesan_file as $pf) { ?>
    <div class="col-xs-4 col-sm-2 text-center">
        <a href="<?php echo $pf->url; ?>" data-toggle="lightbox-image">
            <img src="<?php echo $pf->url; ?>" class="img-responsive push-bit">
        </a>
        <span class="text-muted"><?php  echo $pf->caption; ?></span>
    </div>
  <?php }?>
</div>
<!-- END Attachments Row -->

<div class="row">
  <div class="col-md-12">
    <form action="<?php echo base_url('account/orderan/konsultasi/'.$order->id)?>" method="post" enctype="multipart/form-data">
      <input type='hidden' name="submit" value="1" />
      <div class="form-group">
        <textarea id="message-quick-reply" name="isi" rows="5" class="form-control push-bit" placeholder="Tuliskan pertanyaan.." required></textarea>
      </div>
      <div class="form-group">
        <input id="ilampiran" type="file" name="lampiran" class="form-control" />
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-share"></i> Kirim</button>
      </div>
    </form>
  </div>
</div>
