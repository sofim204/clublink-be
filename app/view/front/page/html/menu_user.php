<style>
.nav.flex-column.nav-big .nav-item{
	border-bottom: 1px #3c3c3c dotted;
}
.nav.flex-column.nav-big a.nav-link{
	font-size: 1.4em;
}
</style>
<div class="nav flex-column nav-big">
  <li class="nav-item">
		<a href="<?=base_url('account/dashboard/')?>" class="nav-link active" ><i class="fa fa-home"></i> Home</a>
	</li>
  <li class="nav-item">
	  <a href="<?=base_url('account/orderan/')?>" class="nav-link"><i class="fa fa-history"></i> History Order</a>
	</li>
  <li class="nav-item">
	  <a href="<?=base_url('logout/')?>" class="nav-link"><i class="fa fa-sign-out"></i> Logout</a>
	</li>
</div>
