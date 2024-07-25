<!-- men's collection -->
<div class="container">
  <div class="row">
    <div id="callout-images-2" class="col-md-6">
      <div class="feature-row feature-row--small-none">
        <div class="feature-row__item feature-row__callout-image">
          <div class="callout-images" data-aos="collection-callout" data-aos-duration="4000">
            <?php if(is_array($block5_items) && count($block5_items)){ $i=0; foreach($block5_items as $bi){ ?>
            <img class="callout-image lazyloaded" data-srcset="<?php echo base_url($bi->image); ?>" alt="" srcset="<?php echo base_url($bi->image); ?>" />
            <?php $i++; if($i>=5) break; } } ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6 style-men-line">
      <?=$block5_teks?>
    </div>
  </div>
</div>
<!-- end men's collection -->
