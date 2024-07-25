
        <div class="col-sm-4 col-md-3 m-b-3 hidden-xs">
<?php if(isset($sess->user->fnama)){ ?>
          <div class="account-picture">
						<img src="<?php echo base_url('assets/img/'); ?>user.png" alt="" class="img-circle img-responsive">
					</div>
          <h4 class="text-center m-b-3"><?php echo $sess->user->fnama; ?></h4>
          <ul class="nav nav-pills nav-stacked">
            <li role="presentation" class="<?php if($page_sub=="profile") echo 'active'; ?>"><a href="<?php echo base_url(); ?>account/profile">My Profile</a></li>
            <li role="presentation" class="<?php if($page_sub=="address") echo 'active'; ?>"><a href="<?php echo base_url(); ?>account/address">My Address</a></li>
            <li role="presentation" class="<?php if($page_sub=="informasi_toko") echo 'active'; ?>"><a href="<?php echo base_url(); ?>account/toko">Informasi Toko</a></li>
            <li role="presentation" class="<?php if($page_sub=="history") echo 'active'; ?>"><a href="<?php echo base_url(); ?>account/history">Order History</a></li>
            <li role="presentation" class="<?php if($page_sub=="password") echo 'active'; ?>"><a href="<?php echo base_url(); ?>account/password">Change Password</a></li>
          </ul>
<?php } ?>
        </div>
