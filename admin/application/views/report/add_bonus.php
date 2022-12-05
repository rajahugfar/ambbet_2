<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
<div class="content-wrapper">
	<div class="content-header row">
		<div class="content-header-left col-md-9 col-12 mb-2">
			<div class="row breadcrumbs-top">
				<div class="col-12">
					<h2 class="content-header-title float-left mb-0">รายงาน</h2>
				</div>
			</div>
		</div>
	</div>
	<div class="content-body">
		<section class="card">
			<div class="card-header">
				<h4 class="card-title">การรับโบนัส</h4>
			</div>
			<div class="card-content">
				<div class="card-body">
					<div class="row mx-auto" style="width: 98%">
						<div class="col-12 col-md-8 pr-1 pl-1">
							<div class="input-group mb-1">
								<div class="input-daterange input-group " id="datepicker">
									<div class="input-group-prepend">
										<span class="input-group-text" id="basic-addon1">วันที่ (จาก)</span>
									</div>
									<input type="text" class="input-sm form-control"  id="date_start_report" name="start" />
									<input type="text" class="input-sm form-control"  id="time_start_report" name="time_start" />
									<div class="input-group-prepend">
										<span class="input-group-text" id="basic-addon1">วันที่ (ถึง)</span>
									</div>
									<input type="text" class="input-sm form-control"  id="date_end_report" name="end" />
									<input type="text" class="input-sm form-control"  id="time_end_report" name="time_nd" />
									<div class="input-group-prepend">
										<span class="input-group-text" id="basic-addon1">ประเภท</span>
									</div>
									<select name="type" id="type" class="form-control">
										<option value="">ทั้งหมด</option>
										<?php foreach (getTypeLogAddCreditList() as $key => $type): ?>
										<option value="<?php echo $key; ?>"><?php echo $type; ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-4 mx-auto">
							<div class="form-group">
								<button type="button" id="btn-search" name="button" class="btn bg-gradient-primary waves-effect waves-light"><span class="text-silver">ค้นหา</span></button>
								<button id="btn_export_excel" type="button" class="btn bg-gradient-warning waves-effect waves-light"> &nbsp;&nbsp; ออกรายงาน Excel</button>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<div class="table-responsive">
								<!-- table-bordered -->
								<table id="table" class="table dataTables_wrapper dt-bootstrap4  table-hover" style="width:100%">
									<thead>
									<tr>
										<th class="text-center">วัน-เวลา</th>
										<th class="text-center">ยูส</th>
										<th class="text-right">จากยอด</th>
										<th class="text-right">โบนัสที่ได้รับ</th>
										<th class="text-center">ประเภท</th>
										<th class="text-center">รายละเอียด</th>
										<th class="text-center">ดำเนินการโดย</th>
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
		</section>
	</div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js" ></script>
<link href="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker3.min.css" rel="stylesheet">
<script src="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
<script src="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/locales/bootstrap-datepicker.th.min.js" charset="UTF-8"></script>
<script src="<?php echo base_url('assets/plugins/moment/min/moment.min.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/numeral/min/numeral.min.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/xlsx.full.min.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script>
	const bank_list = JSON.parse('<?php echo json_encode(getBankList()); ?>');
	const type_log_add_credit_list = JSON.parse('<?php echo json_encode(getTypeLogAddCreditList()); ?>');
	const game_code_list = JSON.parse('<?php echo json_encode(game_code_list()) ?>');
</script>
<script src="<?php echo base_url('assets/scripts/report/add_bonus.js?').time() ?>"></script>
