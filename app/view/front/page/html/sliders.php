<?php
$sliders = array();
if(isset($slider_list)){
  $sls = explode(",",$slider_list);
  if(is_array($sls) && count($sls)) $sliders = $sls;
}
?>
<header>
  <div id="carouselSlidesOnly" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner" role="listbox">

      <?php $i=0; foreach($sliders as $slider){ ?>
      <div class="item <?php if($i==0) echo 'active'; ?>">
        <img class="d-block w-100" src="<?=base_url($slider); ?>" />
      </div>
      <?php $i++; }  ?>
      
    </div>
  </div>
</header>
