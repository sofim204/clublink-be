<!-- women's collection -->
<div class="container">
  <div class="row">
    <div id="callout-images-1" class="col-md-6">
      <div class="feature-row feature-row--small-none">
        <div class="feature-row__item feature-row__callout-image">
          <div class="callout-images " data-aos="collection-callout" data-aos-duration="4000">
            <?php if(is_array($block3_items) && count($block3_items)){ $i=0; foreach($block3_items as $bi){ ?>
            <img class="callout-image lazyloaded" data-srcset="<?php echo base_url($bi->image); ?>" alt="" srcset="<?php echo base_url($bi->image); ?>" />
            <?php $i++; if($i>=5) break; } } ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6 style-women-line">
      <?=$block3_teks?>
    </div>
  </div>
</div>
<!-- end women's collection -->
