<style>
    h4.tbl-content {
        margin: 0;
        line-height: 1;
    }

    .tbl-content-category {
        margin: 0.5em 0;
        color: #9c9c9c;
        font-weight: bold;
        line-height: 1;
    }

    .tbl-product-properties {
        margin-top: 0.5em;
    }

    .img-responsive.img-icon {
        max-width: 64px;
        border-radius: 10px;
        border: 1px #acacac solid;
        margin-left: 0.5em;
    }

    table#drTable tr:hover {
        background-color: #EFBF65;
    }
</style>
<div id="page-content">
    <!-- Static Layout Header -->
    <!-- <div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6"></div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="" href="<?= base_url_admin('crm/produkreport/tambah/'); ?>" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
				</div>
			</div>
		</div>
	</div> -->
    <ul class="breadcrumb breadcrumb-top">
        <li>Admin</li>
        <li>E-Commerce</li>
        <li>Products Reports</li>
        <li><span style="display: none;" id="admin_name"><?= $admin_name ?></span></li>
    </ul>
    <!-- END Static Layout Header -->

    <!-- Content -->
    <div class="block full">

        <div class="block-title">
            <h2><strong>Products Reports</strong></h2>
        </div>

        <div class="row" style="margin-bottom: 1em;">
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-3" style="margin-top: 1em;">
                        <label for="ifb_kategori_id">Category</label>
                        <select id="ifb_kategori_id" class="form-control">
                            <option value="">--view all--</option>
                            <?php if (isset($kategori_list)) {
                                foreach ($kategori_list as $kt) { ?>
                                    <option value="<?= $kt->id ?>"><?= $kt->nama ?></option>
                                <?php }
                            } ?>
                        </select>
                    </div>
                    <div class="col-md-3" style="margin-top: 1em;">
                        <label for="ifprice_min">Price Range (Min)</label>
                        <input id="ifprice_min" type="number" class="form-control" value=""/>
                    </div>
                    <div class="col-md-3" style="margin-top: 1em;">
                        <label for="ifprice_max">Price Range (Max)</label>
                        <input id="ifprice_max" type="number" class="form-control" value=""/>
                    </div>
                    <div class="col-md-3" style="margin-top: 1em;">
                        <label for="ifb_kondisi_id">Condition</label>
                        <select id="ifb_kondisi_id" class="form-control">
                            <option value="">--view all--</option>
                            <?php if (isset($kondisi_list)) {
                                foreach ($kondisi_list as $kl) { ?>
                                    <option value="<?= $kl->id ?>"><?= $kl->nama ?></option>
                                <?php }
                            } ?>
                        </select>
                    </div>
                    <div class="col-md-3" style="margin-top: 1em;">
                        <label for="if_courier_service">Courier Service</label>
                        <select id="if_courier_service" class="form-control">
                            <option value="">--view all--</option>
                            <option value="qxpress">QXpress</option>

                            <!-- by Donny Dennison - 15 september 2020 17:45 change name, image, etc from gogovan to gogox -->
                            <!-- <option value="gogovan">Gogovan</option> -->
                            <option value="gogox">Gogox</option>

                            <!-- by Donny Dennison - 23 september 2020 15:42  add direct delivery feature -->
                            <option value="direct_delivery">Direct Delivery</option>

                        </select>
                    </div>
                    <div class="col-md-3" style="margin-top: 1em;">
                        <label for="if_free_ship">Free Shipping</label>
                        <select id="if_free_ship" class="form-control">
                            <option value="">--view all--</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-md-3" style="margin-top: 1em;">
                        <label for="if_reported_status">Action</label>
                        <select id="if_reported_status" class="form-control">
                            <option value="">--view all--</option>
                            <option value="takedown">Takedown</option>
                            <option value="ignore">Ignore</option>
                        </select>
                    </div>
                     <!-- start by ali - 18 january 2023 14:42 add filter admin name & filter date -->
                    <div class="col-md-3" style="margin-top: 1em;">
                        <label for="input_admin_name">CS Name</label>
                        <select id="input_admin_name" class="form-control">
                            <option value="">--view all--</option>
                            <?php foreach($admin_list as $row): ?>
                                <option value="<?= empty($row->user_alias) ? '-' : $row->user_alias ?>" style="text-transform: uppercase;">
                                    <?= empty($row->user_alias) ? $row->nama : strtoupper(str_replace("_", " ", $row->user_alias)) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-md-3" style="margin-top: 1em;">
                        <div class="form-group">
                            <label for="">From Date</label>
                            <input 
                                id="from_date" 
                                type="text" 
                                class="form-control input-datepicker" 
                                data-date-format="yyyy-mm-dd" 
                                placeholder="From date" 
                                readonly
                            />
                        </div>
                    </div>
                    <div class="col-md-3" style="margin-top: 1em;">
                        <div class="form-group">
                        <label for="">To Date</label>
                        <input 
                            id="to_date" 
                            type="text" 
                            class="form-control input-datepicker" 
                            data-date-format="yyyy-mm-dd" 
                            placeholder="To Date" 
                            readonly
                        />
                        </div>
                    </div>
                     <!-- end by ali - 18 january 2023 14:42 add filter admin name & filter date -->
                </div>
            </div>
            <div class="col-md-3" style="margin-bottom: 1em;">
                <div class="col-md-12" style="margin-top: 1em;">
                    <label for="fl_reset">&nbsp;</label>
                    <button id="fl_reset" type="button" class="btn btn-warning btn-block"><i class="fa fa-reset"></i>
                        Reset
                    </button>
                </div>
                <div class="col-md-12">
                    <label for="bfilter">&nbsp;</label>
                    <button id="bfilter" type="button" class="btn btn-info btn-block"><i class="fa fa-filter"></i>
                        Filter
                    </button>
                </div>
                <!-- start  by ali - 18 january 2023 14:42 add export excel-->
                 <!-- <div class="col-md-12">
                    <label for="fl_download">&nbsp;</label>
                    <button id="fl_download" type="button" class="btn btn-success btn-block">
                        Export to excel <i class="fa fa-file-excel-o"></i>
                    </button>
                </div> -->
                <!-- end by ali - 18 january 2023 14:42 add export excel -->
            </div>
        </div>

        <div class="table-responsive">
            <table id="drTable" class="table table-vcenter table-condensed table-bordered">
                <thead>
                <tr style="background-color: #FFFFFF;">
                    <th class="text-center">No.</th>
                    <th>Product Id</th>
                    <th>User Id Reporter</th>
                    <th>Submit Date</th>
                    <th>Product Image</th>
                    <th>Product</th>
                    <th>Owner Product</th>
                    <th>Report Description</th>
                    <th>Reported By</th>
                    <th>Takedown By</th>
                    <th class="text-center">Report Count</th>
                    <th class="text-center">Status</th>
                    <!-- <th class="text-center">Action</th> -->
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

    </div>
    <!-- END Content -->
</div>
