<div class="off-canvas-overlay"></div>
<div id="cart_detail_wrapper" class="off-canvas">
  <div class="top-cart-content">
    <div class="block-close">
      <a id="offcanvas_cart_close" href="#"><i class="fa fa-times"></i></a>
    </div>
    <div class="block-subtitle hidden-xs cart-content-title" onclick="window.location='<?php echo base_url('cart'); ?>'">Cart</div>
    <div class="top-style-select row"></div>
    <ul id="cart_detail_list" class="mini-products-list" style="padding: 0 0.5em;">

    </ul>
    <div id="cart_not_empty" style="display:none;">
      <div class="top-style-select row">
        <div class="col-md-6 col-xs-5">
          <button type="button" class="btn btn-default btn-block cart-select active">
           <i class="fa fa-globe"></i>
           <br>
           <p style="padding: 10px 0 0 0;">Shipping</p>
          </button>
        </div>
        <div class="col-md-6 col-xs-5">
          <button type="button" class="btn btn-default btn-block cart-select">
           <i class="fa fa-home"></i>
           <br>
           <p style="padding: 10px 0 0 0;">Store Pickup</p>
          </button>
        </div>
      </div>
      <div class="shipping-note">
        <div class="">
          <p style="color:#d2ac67;">Please click the checkout button to continue.</p>
        </div>
      </div>

      <div class="order-note-style">
        <div class="">
          <label>ORDER NOTE</label>
          <textarea name="note" class="input-full" id="order_note"></textarea>
        </div>
      </div>


      <br>
      <div class="top-subtotal row">
        <div class="col-md-4 col-xs-5">
          <span class="cart-content-subtotal-teks">Subtotal:</span>
        </div>
        <div class="col-m-8 col-xs-7 text-right">
          <span id="cart_subtotal" class="price">Rp0</span>
        </div>
      </div>
      <p style="color: #d2ac67; font-size: smaller; margin: 1em 0;">
        Shipping, taxes, and discounts calculated at checkout.
      </p>
      <div class="actions">
        <button id="b_checkout_to" class="btn btn-shop-dress" style="width: 100%;" type="button" rel='nofollow'><span>Check out</span></button>
        <!-- <button id="b_cart_list_to" class="view-cart" type="button" rel='nofollow'><i class="fa fa-shopping-cart"></i> <span>Belanjaan</span></button> -->
      </div>
    </div>
    <div id="cart_is_empty">
      <p style="color: #d2ac67; font-size: smaller; margin: 1em 0;">
        Your cart is currently empty.
      </p>
    </div>
  </div><!-- .top-cart-content -->
</div>
