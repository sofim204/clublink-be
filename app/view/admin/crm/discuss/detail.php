<style>
	table#drTable tr:hover {
		background-color: #EFBF65;
	}
  .btn-back {
    width: 85px;
    cursor: pointer;
    background: #F9F5F5;
    border: 1px solid #999;
    outline: none;
    color: #222121;
    transition: .3s ease;
  }
  .btn-back:hover {
    transition: .3s ease;
    background: #DD8A0D;
    border: 1px solid transparent;
    color:#FFF;
  }
</style>
<div id="page-content">
  <div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">
				<div class="btn-group">
          <a onclick="history.go(-1)" class="btn btn-back"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
				</div>
			</div>
		</div>
	</div>
  <ul class="breadcrumb breadcrumb-top">
    <li>Admin</li>
    <li>CRM</li>
    <li>Detail Q&A Product</li>
  </ul>
  <!-- END Static Layout Header -->

  <!-- Content -->
  <div class="block full">
    <div class="block-title">
      <h2><strong><i class="fa fa-wechat"></i>&nbsp;Q&A Product</strong></h2>
    </div>
    <div class="table-responsive">
      <table id="drTable" class="table table-vcenter table-condensed table-bordered">
        <thead>
          <tr style="background-color: #FFFFFF;">
            <th class="text-center">Q&A ID </th>
            <th>Product</th>
            <th>User</th>
            <th>From</th> 
            <th>Date</th>
            <th>Status</th>
            <th>Answer</th>
            <th>Action</th>
            
<!--             <th>Action</th> -->
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
  <!-- END Content -->
</div>
