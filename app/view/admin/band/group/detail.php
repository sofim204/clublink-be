<style>
	table#drTable tr:hover {
		background-color: #EFBF65;
	}
  table#drTableParticipant tr:hover {
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

  .hidden {
    display: none;
  }
</style>
<div id="page-content">
  <div class="content-header">
		<!-- <div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">
				<div class="btn-group">
          <a onclick="history.go(-1)" class="btn btn-back"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
				</div>
			</div>
		</div> -->
	</div>
  <ul class="breadcrumb breadcrumb-top">
    <li>Admin</li>
    <li>Club</li>
    <li>Detail Club Post</li>
  </ul>
  <!-- END Static Layout Header -->

  <!-- Content -->
  <div class="row">    
    <div class="col-md-7">
      <div class="block full">
        <div class="block-title">
          <h2 id="title-group"><strong><i class="fa fa-wechat"></i>&nbsp;Club Post</strong></h2>
        </div>
        <div class="table-responsive">
          <table id="drTable" class="table table-vcenter table-condensed table-bordered">
            <thead>
              <tr style="background-color: #FFFFFF;">
                <th class="text-center" width="50px">No.</th>
                <th class="text-center">ID</th>
                <th>User</th>
                <th>Description</th>
                <th>Thumbnail</th>
                <th>Date</th>
                <th>Active</th>
                <th>Takedown</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-5">
      <div class="block full">
        <div class="block-title">
          <h2><strong><i class="fa fa-wechat"></i>&nbsp;Participant</strong></h2>
        </div>
        <div class="table-responsive">
          <table id="drTableParticipant" class="table table-vcenter table-condensed table-bordered">
            <thead>
              <tr style="background-color: #FFFFFF;">
                <th class="text-center" width="50px">No.</th>
                <th class="text-center">User ID</th>
                <th>Nama</th>
                <th>Owner</th>
                <th>Admin</th>
                <th>Join Date</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-5" id="container_total_post">
      <table id="clubTable" border="1">
        <thead>
          <tr id="header_table" class="hidden" style="font-size: 18px;">
            <th style="padding: 10px; width: 190px; text-align: center;">Club Name</th>
            <th style="padding: 10px; text-align: center;">Total Posts</th>
          </tr>
        </thead>
        <tbody id="clubData">
        </tbody>
      </table>
    </div>
  </div>
  <!-- END Content -->
</div>