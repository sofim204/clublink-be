<?php
if(isset($detail->sdate)){
	if($detail->sdate == '0000-00-00 00:00:00' || $detail->sdate == '0000-00-00'){
		$detail->sdate = '(tdk ditentukan)';
	}else{
		$detail->sdate = $this->__dateIndonesia($detail->sdate,'hari_tanggal_jam');
	}
}
if(isset($detail->edate)){
	if($detail->edate == '0000-00-00 00:00:00' || $detail->edate == '0000-00-00'){
		$detail->edate = '(tdk ditentukan)';
	}else{
		$detail->edate = $this->__dateIndonesia($detail->edate,'hari_tanggal_jam');
	}
}
$detail->total_sudah = (float) $detail->total_sudah;
$detail->total = (float) $detail->total;
if(empty($detail->total)) $detail->total = 1;
$detail_pie = ($detail->total_sudah / $detail->total) * 100;
$detail_pie = ceil($detail_pie);
?>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">
				<div class="btn-group">
					<a id="akembali123" href="<?php echo base_url('finance/target/'); ?>" class="btn btn-default"><i class="fa fa-chevron-left"></i> Kembali</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Finance</li>
		<li>Target</li>
		<li>Detail - <?=$detail->nama?></li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="row">
		<div class="col-md-8">
			<!-- Info Block -->
			<div class="block">
				<!-- Info Title -->
				<div class="block-title">
					<h2><strong>Detail</strong></h2>
				</div>
				<!-- END Info Title -->

				<!-- Info Content -->
				<table class="table table-borderless table-striped">
					<tbody>
						<tr>
							<th>Nama</th>
							<td><?php echo $detail->nama; ?></td>
						</tr>
						<tr>
							<th>Rentang</th>
							<td><?=$detail->sdate.' - '.$detail->edate?></td>
						</tr>
						<tr>
							<th>Deskripsi</th>
							<td><?php echo $detail->deskripsi; ?></td>
						</tr>
						<tr>
							<th>Total Target</th>
							<td id="dtotal" class="uang-rupiah"><?='Rp'.number_format($detail->total,0,',','.')?></td>
						</tr>
						<tr>
							<th>Belum Tercapai</th>
							<td id="dtotal_belum" class="uang-rupiah"><?='Rp'.number_format($detail->total_belum,0,',','.')?></td>
						</tr>
						<tr>
							<th>Sudah Tercapai</th>
							<td id="dtotal_sudah" class="uang-rupiah"><?='Rp'.number_format($detail->total_sudah,0,',','.')?></td>
						</tr>
						<tr>
							<th>Status</th>
							<td><?php
								$ia = (int) $detail->is_active;
								if($ia==1){
									echo '<label class="label label-success">Aktif</label>';
								}else{
									echo '<label class="label label-default">Tidak Aktif</label>';
								}
								$ia = (int) $detail->is_closed;
								if($ia==1){
									echo '<label class="label label-success">Achieved</label>';
								}else{
									echo '<label class="label label-default">Belum Achieved</label>';
								}
							?></td>
						</tr>
					</tbody>
				</table>
				<!-- END Info Content -->
			</div>
			<!-- END Info Block -->
		</div>
		<div class="col-md-4">
			<div class="block">
				<div class="block-title">
					<h2><strong>Detail</strong></h2>
				</div>
				<div id="target_pie" class="pie-chart block-section" data-percent="<?=$detail_pie?>" data-size="130">
					<span><?=$detail_pie?>%</span>
				</div>
			</div>
		</div>

		<div class="col-md-12">
			<div class="block">
				<!-- Info Title -->
				<div class="block-title">
					<div class="block-options pull-right">
						<a id="atambah" href="#" class="btn btn-alt btn-sm btn-default" data-toggle="tooltip" title="" data-original-title="Tambah Target"><i class="fa fa-plus"></i></a>
					</div>
					<h2><strong>Detail</strong></h2>
				</div>
				<div class="table-responsive">
					<table id="drTable" class="table table-vcenter table-condensed table-bordered">
						<thead>
							<tr>
								<th class="text-center">ID</th>
								<th>Prioritas</th>
								<th>Nama</th>
								<th>Harga</th>
								<th>Qty</th>
								<th>Subtotal</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<!-- END Content -->
</div>
