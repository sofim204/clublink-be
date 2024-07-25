<!-- Main Content -->
    <div class="container m-t-3">
      <div class="row">

        <!-- Account Sidebar -->
				<?php $this->getThemeElement("account/sidebar",$__forward); ?>
        <!-- End Account Sidebar -->

        <!-- My Profile Content -->
        <div class="col-sm-8 col-md-9">
          <div class="title m-b-2"><span>Order History</span></div>
          <div class="row">
            <div class="col-xs-12">
              <section class="content account">
              <article class="account-content">
                <!--
                <div class="alert alert-warning alert-dismissible" role="alert">
                	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><i class="fa fa-times"></i></button>
                	<strong>Warning!</strong> This order is unpaid.
                </div>
              -->
                <h3>Order #12345</h3>
                <div class="order-meta">
                    <div class="row">
                        <div class="col-xs-6">
                            <span class="label label-lg label-warning order-status">Pending Payment</span>
                        </div>
                        <div class="col-xs-6">
                            <ul class="list-inline order-action">
                                <li><a onClick="print_window();" class="btn btn-primary btn-sm">Print</a></li>
                                <li><a href="#" class="btn btn-primary btn-sm">Save to PDF</a></li>
                                <li><a href="#" class="btn btn-primary btn-sm">Cancel Order</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                	<div class="col-sm-6">
                    	<div class="box">
                            <h4>Billing Address</h4>
                            <ul class="list-unstyled">
                                <li><b>John Doe</b></li>
                                <li>17 Boulevard des Invalides</li>
                                <li>75007 Paris</li>
                                <li>France</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6">
                    	<div class="box">
                            <h4>Shipping Address</h4>
                            <ul class="list-unstyled">
                                <li><b>John Doe</b></li>
                                <li>17 Boulevard des Invalides</li>
                                <li>75007 Paris</li>
                                <li>France</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                	<div class="col-sm-6">
                    	<div class="box">
                            <h4>Payment Method</h4>
                            <ul class="list-unstyled">
                                <li>Credit Card - VISA</li>
                            </ul>
                        </div>
                        <div class="box">
                            <h4>Shipping Method</h4>
                            <ul class="list-unstyled">
                                <li>Post Air Mail</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6">
                    	<div class="box">
                            <h4>Order Details</h4>
                            <ul class="list-unstyled">
                                <li><b>Email: </b>johndoe@umarket.com</li>
                                <li><b>Phone: </b>+420 123 456 789</li>
                            </ul>
                            <h5>Addition information:</h5>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut feugiat mauris eget magna egestas porta. Curabitur sagittis sagittis neque rutrum congue.</p>
                        </div>
                    </div>
                </div>

                <div class="products-order">

                	<div class="table-responsive">
                        <table class="table table-products">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Product</th>
                                    <th>Unit Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="col-xs-1"><img src="<?php echo base_url('media/produk/'); ?>rolciz-original.jpg" alt="" class="img-responsive"></td>
                                    <td class="col-xs-4 col-md-5"><h4><a href="#">Fusce Aliquam</a><small>M, Black, Esprit</small></h4></td>
                                    <td class="col-xs-2 text-center"><span>$30</span></td>
                                    <td class="col-xs-2 col-md-1 text-center"><span><b>2 items</b></span></td>
                                    <td class="col-xs-2 text-center"><span><b>$60</b></span></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-1"><img src="<?php echo base_url('media/produk/'); ?>rolciz-original.jpg" alt="" class="img-responsive"></td>
                                    <td class="col-xs-4 col-md-5"><h4><a href="#">Fusce Aliquam</a><small>M, Black, Esprit</small></h4></td>
                                    <td class="col-xs-2 text-center"><span>$80</span></td>
                                    <td class="col-xs-2 col-md-1 text-center"><span><b>2 items</b></span></td>
                                    <td class="col-xs-2 text-center"><span><b>$160</b></span></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-1"><img src="<?php echo base_url('media/produk/'); ?>rolciz-original.jpg" alt="" class="img-responsive"></td>
                                    <td class="col-xs-4 col-md-5"><h4><a href="#">Fusce Aliquam</a><small>M, Black, Esprit</small></h4></td>
                                    <td class="col-xs-2 text-center"><span>$95</span></td>
                                    <td class="col-xs-2 col-md-1 text-center"><span><b>1 item</b></span></td>
                                    <td class="col-xs-2 text-center"><span><b>$95</b></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <ul class="list-unstyled order-total">
                        <li>Total products<span>$315.00</span></li>
                        <li>Shipping<span>$15.00</span></li>
                        <li>Subtotal<span class="total">$330.00</span></li>
                    </ul>
                </div>
            </article>
            </section>

            </div>
          </div>
        </div>
        <!-- End My Profile Content -->

      </div>
    </div>
    <!-- End Main Content -->