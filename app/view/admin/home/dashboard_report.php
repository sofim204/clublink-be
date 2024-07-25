<style>
.mtop10 {
  margin-top: 10px;
}
.nmbtm {
  margin-bottom: 0;
}
.table thead > tr > th {
  font-size: 1.2em;
}
</style>
<!-- widget cart most often -->
<div class="widget">
  <div class="widget-extra themed-background-light">
    <p class="mtop10 nmbtm">In the last 14 days</p>
    <h4> These products were added to cart the most often</h4>
    <h5>This includes sales, abandoned checkouts, and adds-to-carts.</h5>
  </div>
  <div class="">
    <table class="table">
      <thead>
        <tr>
          <th colspan="2">Produk</th>
          <th class="col-md-2 text-right">Ditambahkan</th>
        </tr>
      </thead>
      <tbody>
        <?php if(isset($cart_most)){ foreach($cart_most as $cm){ ?>
        <tr>
          <td class="col-md-2">
            <img src="<?=base_url($cm->thumb)?>" class="img-responsive" onerror="this.onerror=null;this.src='<?=base_url()?>media/produk/default.jpg'" />
          </td>
          <td><a href="<?=base_url('produk/'.$cm->slug)?>" target="_blank"><h4><?=$cm->nama?></h4></a></td>
          <td class="text-center"><?=$cm->qty_total?></td>
        </tr>
        <?php }} ?>
      </tbody>
    </table>
  </div>
</div>
<!-- widget cart most often -->

<!-- widget cart most often -->
<div class="widget">
  <div class="widget-extra themed-background-light">
    <p class="mtop10 nmbtm">In the last 14 days</p>
    <h4>These products were viewed most often</h4>
    <h5>Products that get more views are more likely to be purchased.</h5>
  </div>
  <div class="">
    <table class="table">
      <thead>
        <tr>
          <th colspan="2">Produk</th>
          <th class="col-md-2 text-right">Dilihat</th>
        </tr>
      </thead>
      <tbody>
        <?php if(isset($produk_viewed_most)){ foreach($produk_viewed_most as $cm){ ?>
        <tr>
          <td class="col-md-2">
            <img src="<?=base_url($cm->thumb)?>" class="img-responsive" onerror="this.onerror=null;this.src='<?=base_url()?>media/produk/default.jpg'" />
          </td>
          <td><a href="<?=base_url('produk/'.$cm->slug)?>" target="_blank"><h4><?=$cm->nama?></h4></a></td>
          <td class="text-center"><?=$cm->dilihat?></td>
        </tr>
        <?php }} ?>
      </tbody>
    </table>
  </div>
</div>
<!-- widget cart most often -->

<!-- widget cart most often -->
<div class="widget">
  <div class="widget-extra themed-background-light">
    <p class="mtop10 nmbtm">In the last 14 days</p>
    <h4>Some of your visitors can’t find what they’re looking for</h4>
    <h5>People are searching for these terms on your online store but aren’t getting any results. You might want to add them to your product descriptions to help them out.</h5>
  </div>
  <div class="">
    <table id="table_search_term" class="table">
      <thead>
        <tr>
          <th>Kata Kunci</th>
          <th class="col-md-2 text-center">Dicari</th>
        </tr>
      </thead>
      <tbody>
        <?php if(isset($search_term)){ foreach($search_term as $st){ if(empty($st->page_name)) continue;?>
        <tr>
          <td><?=$st->page_name?></td>
          <td class="text-center"><?=$st->total?></td>
        </tr>
        <?php }} ?>
      </tbody>
    </table>
  </div>
</div>
<!-- widget cart most often -->

<!-- widget cart most often -->
<div class="widget">
  <div class="widget-extra themed-background-light">
    <p class="mtop10 nmbtm">In the last 30 days</p>
    <h4>These are your top discounts by sales</h4>
    <h5>Discounts can help boost conversion rates and build customer loyalty. Use this summary to track which discounts are getting sales.</h5>
  </div>
  <div class="">
    <table id="" class="table">
      <thead>
        <tr>
          <th>Discount</th>
          <th class="col-md-3 text-right">Penjualan</th>
        </tr>
      </thead>
      <tbody>
        <?php if(isset($vouchered_order)){ foreach($vouchered_order as $cm){ ?>
        <tr>
          <td>
            <a href="#" target="_blank"><?=$cm->kode_voucher?></a><br />
            Digunakan sebanyak <?=$cm->jumlah?> kali
          </td>
          <td class="text-center">Rp<?=number_format($cm->grand_total,0,',','.')?></td>
        </tr>
        <?php }} ?>
      </tbody>
    </table>
  </div>
</div>
<!-- widget cart most often -->
