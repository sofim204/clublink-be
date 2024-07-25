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
  <!-- by Muhammad Sofi 18 January 2022 10:18 | add back button -->
  <div class="content-header">
    <div class="row" style="padding: 0.5em 2em;">
      <div class="col-md-12">
        <div class="btn-group">
          <a id="aback" href="<?=base_url_admin('crm/discuss/'); ?>" class="btn btn-back"><i class="fa fa-chevron-left"></i> Back</a>
        </div>
      </div>
    </div>
  </div>
  <ul class="breadcrumb breadcrumb-top">
    <li>Admin</li>
    <li>CRM</li>
    <li>Reported Q&A Product</li>
  </ul>
  <!-- END Static Layout Header -->

  <!-- Content -->
  <div class="block full">
    <div class="block-title">
      <h2><strong><i class="fa fa-wechat"></i>&nbsp;Reported Q&A</strong></h2>
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

            <!--  by Donny Dennison - 21 January 2021 10:32
            show last report cdate -->
            <th>Last Reported Date</th>

            <th>Status</th>
            <th>Message</th>
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
