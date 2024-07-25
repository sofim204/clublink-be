<?php if($block1_mode == 3){ ?>
<div class="section-first">
  <div class="container">
    <div class="row style-first-line">
      <?php if(isset($block1_items[0]->url)){ ?>
      <div class="col-md-6" style="padding:0;">
        <div class="grid-image2">
          <a href="<?=$block1_items[0]->url?>" class="skrim__link skrim__item-content">
            <div class="skrim__overlay grid__image lazyloaded" style="height:290px;background:url('<?=base_url($block1_items[0]->image); ?>');background-position: center; background-size: cover;">
            </div>
            <div class="skrim__title">
              <div class="skrim__underline-me">
                <?=$block1_items[0]->caption?>
              </div>
            </div>
          </a>
        </div>
      </div>
      <?php } ?>

      <div class="col-md-6" style="padding:0;">
        <div class="row">
          <?php if(isset($block1_items[1]->url)){ ?>
          <div class="col-md-6 col-sm-6 col-xs-6" style="padding:0;">
            <div class="grid-image3">
              <a href="<?=$block1_items[1]->url?>" class="skrim__link skrim__item-content">
                <div class="skrim__overlay grid__image lazyloaded" style="height:290px;background:url('<?=base_url($block1_items[1]->image); ?>');background-position: center; background-size: cover;">
                </div>
                <div class="skrim__title">
                  <div class="skrim__underline-me">
                    <?=$block1_items[1]->caption?>
                  </div>
                </div>
              </a>
            </div>
          </div>
          <?php } ?>

          <?php if(isset($block1_items[2]->url)){ ?>
          <div class="col-md-6 col-sm-6 col-xs-6" style="padding:0;">
            <div class="grid-image4">
              <a href<?=$block1_items[2]->url?> class="skrim__link skrim__item-content">
                <div class="skrim__overlay grid__image lazyloaded" style="height:290px;background:url('<?=base_url($block1_items[2]->image); ?>');background-position: center; background-size: cover;">
                </div>
                <div class="skrim__title">
                  <div class="skrim__underline-me">
                    <?=$block1_items[2]->caption?>
                  </div>
                </div>
              </a>
            </div>
          </div>
          <?php } ?>

        </div>
      </div>
    </div>
  </div>
</div>
<?php }else { ?>
<div class="section-first">
  <div class="container">
    <div class="row style-first-line">
      <?php if(isset($block1_items[0]->url)){ ?>
      <div class="col-md-6" style="padding:0;">
        <div class="grid-image2">
          <a href="<?=$block1_items[0]->url?>" class="skrim__link skrim__item-content">
            <div class="skrim__overlay grid__image lazyloaded" style="height:290px;background:url('<?=base_url($block1_items[0]->image); ?>');background-position: center; background-size: cover;">
            </div>
            <div class="skrim__title">
              <div class="skrim__underline-me">
                <?=$block1_items[0]->caption?>
              </div>
            </div>
          </a>
        </div>
      </div>
      <?php } ?>

      <div class="col-md-6" style="padding:0;">
        <div class="row">
          <?php if(isset($block1_items[1]->url)){ ?>
          <div class="col-md-6 col-sm-6 col-xs-6" style="padding:0;">
            <div class="grid-image3">
              <a href="<?=$block1_items[1]->url?>" class="skrim__link skrim__item-content">
                <div class="skrim__overlay grid__image lazyloaded" style="height:290px;background:url('<?=base_url($block1_items[1]->image); ?>');background-position: center; background-size: cover;">
                </div>
                <div class="skrim__title">
                  <div class="skrim__underline-me">
                    <?=$block1_items[1]->caption?>
                  </div>
                </div>
              </a>
            </div>
          </div>
          <?php } ?>

          <?php if(isset($block1_items[2]->url)){ ?>
          <div class="col-md-6 col-sm-6 col-xs-6" style="padding:0;">
            <div class="grid-image4">
              <a href<?=$block1_items[2]->url?> class="skrim__link skrim__item-content">
                <div class="skrim__overlay grid__image lazyloaded" style="height:290px;background:url('<?=base_url($block1_items[2]->image); ?>');background-position: center; background-size: cover;">
                </div>
                <div class="skrim__title">
                  <div class="skrim__underline-me">
                    <?=$block1_items[2]->caption?>
                  </div>
                </div>
              </a>
            </div>
          </div>
          <?php } ?>

        </div>

        <div class="row">
          <?php if(isset($block1_items[3]->url)){ ?>
          <div class="col-md-6 col-sm-6 col-xs-6" style="padding:0;">
            <div class="grid-image4">
              <a href<?=$block1_items[3]->url?> class="skrim__link skrim__item-content">
                <div class="skrim__overlay grid__image lazyloaded" style="height:290px;background:url('<?=base_url($block1_items[3]->image); ?>');background-position: center; background-size: cover;">
                </div>
                <div class="skrim__title">
                  <div class="skrim__underline-me">
                    <?=$block1_items[3]->caption?>
                  </div>
                </div>
              </a>
            </div>
          </div>
          <?php } ?>

          <?php if(isset($block1_items[4]->url)){ ?>
          <div class="col-md-6 col-sm-6 col-xs-6" style="padding:0;">
            <div class="grid-image4">
              <a href<?=$block1_items[4]->url?> class="skrim__link skrim__item-content">
                <div class="skrim__overlay grid__image lazyloaded" style="height:290px;background:url('<?=base_url($block1_items[4]->image); ?>');background-position: center; background-size: cover;">
                </div>
                <div class="skrim__title">
                  <div class="skrim__underline-me">
                    <?=$block1_items[4]->caption?>
                  </div>
                </div>
              </a>
            </div>
          </div>
          <?php } ?>

        </div>

      </div>
    </div>
  </div>
</div>
<?php } ?>
