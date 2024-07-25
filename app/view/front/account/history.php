<!-- Main Content -->
    <div class="container m-t-3">
      <div class="row">

        <!-- Account Sidebar -->
				<?php $this->getThemeElement("account/sidebar",$__forward); ?>
        <!-- End Account Sidebar -->

        <!-- My Profile Content -->
        <div class="col-sm-8 col-md-9" style="padding-right: 1.5em;">
          <div class="wijet-profile-main">
            <div class="title m-b-2"><span>Order History</span></div>
              <div class="row">
                <div class="col-xs-12">
                  <div class="table-responsive">

                    <table id="order-history-table" class="table">
                      <thead>
                        <tr>
                          <th>ID Pembelian</th>
                          <th>Tgl Pembelian</th>
                          <th>Penerima</th>
                          <th>Tujuan</th>
                          <th>Kurir</th>
                          <th>Ongkir</th>
                          <th class="text-right">Amount</th>
                          <th class="text-center">Status</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>

                      </tbody>
                    </table>
                    
                  </div>
                </div>
              </div>
            </div>
          </div>
        <!-- End My Profile Content -->

      </div>
    </div>
    <!-- End Main Content -->
