<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
          <div class="col-12">
            <h2 class="content-header-title float-left mb-0">ระบบจัดการธนาคาร</h2>
            <div class="breadcrumb-wrapper col-12">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo site_url('bank') ?>">รายการธนาคาร</a>
                </li>
                <li class="breadcrumb-item active">แก้ไขธนาคาร</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="content-body">
      <section class="card">
        <div class="card-content">
          <div class="card-body">
            <form class="form" id="form_update" method="POST" action="<?php echo site_url("bank/bank_update/{$bank['id']}") ?>">
              <h3 class="card-title">ข้อมูลธนาคาร</h3>
              <hr>
              <div class="form-body mt-3">
                <div class="row ">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">ธนาคาร</label>
                      <select class="form-control" id="bank_code" name="bank_code">
                        <option value="">เลือกธนาคาร</option>
                        <option <?php if ($bank['bank_code']=='01'): ?>
                          selected
                        <?php endif; ?> value="01">ธนาคารกรุงเทพ จำกัด (มหาชน)</option>
                        <option <?php if ($bank['bank_code']=='02'): ?>
                          selected
                        <?php endif; ?> value="02">ธนาคารกสิกรไทย จำกัด (มหาชน)</option>
                        <option <?php if ($bank['bank_code']=='03'): ?>
                          selected
                        <?php endif; ?> value="03">ธนาคารกรุงไทย จำกัด (มหาชน)</option>
                        <option <?php if ($bank['bank_code']=='04' || $bank['bank_code']=='08'): ?>
                          selected
                        <?php endif; ?> value="04">ธนาคารทีเอ็มบีธนชาต จำกัด (มหาชน)</option>
                        <option <?php if ($bank['bank_code']=='05'): ?>
                          selected
                        <?php endif; ?> value="05">ธนาคารไทยพาณิชย์ จำกัด (มหาชน)</option>
                        <option <?php if ($bank['bank_code']=='06'): ?>
                          selected
                        <?php endif; ?> value="06">ธนาคารกรุงศรีอยุธยา จำกัด (มหาชน)</option>
                        <option <?php if ($bank['bank_code']=='07'): ?>
                          selected
                        <?php endif; ?> value="07">ธนาคารออมสิน จำกัด (มหาชน)</option>
						<option <?php if ($bank['bank_code']=='09'): ?>
                          selected
                        <?php endif; ?> value="09">ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร จำกัด (มหาชน)</option>
						  <option <?php if ($bank['bank_code']=='11'): ?>
							  selected
						  <?php endif; ?> value="11">ธนาคารเกียรตินาคิน จำกัด (มหาชน)</option>
						<option <?php if ($bank['bank_code']=='10'): ?>
                          selected
                        <?php endif; ?> value="10">ทรูมันนี่วอลเล็ท</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">ชื่อบัญชี</label>
                      <input type="text" id="account_name" name="account_name" class="form-control" value="<?php echo $bank['account_name']; ?>" placeholder="ข้อมูลชื่อบัญชี">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">เลขบัญชี</label>
                      <input type="text" id="bank_number" name="bank_number" class="form-control" value="<?php echo $bank['bank_number']; ?>" placeholder="ข้อมูลเลขบัญชี" maxlength="15">
                    </div>
                  </div>
                </div>
                <div class="row ">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">ประเภท API</label>
							<select class="form-control" name="api_type" id="api_type">
								<option value="1">ดึงข้อมูลจาก App ธนาคาร</option>
								<option value="2" <?php if ($bank['api_type']==2): ?>
									selected
								<?php endif; ?> >ดึงข้อมูลจาก Internet Banking</option>
								<option value="3" <?php if ($bank['api_type']==3): ?>
									selected
								<?php endif; ?> >ดึงข้อมูลจาก Line Lift</option>
								<option value="4" <?php if ($bank['api_type']==4): ?>
									selected
								<?php endif; ?> >ดึงข้อมูลจาก SMS</option>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label" id="username_label">Username</label>
							<input type="text" id="username" name="username" class="form-control"  value="<?php echo $bank['username']; ?>" placeholder="Username">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label" id="password_label">Password</label>
							<input type="text" id="password" name="password" class="form-control" value="<?php echo $bank['password']; ?>" placeholder="Password">
						</div>
					</div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">เบอร์ PromptPay <font style="color:red;">*ใช้ได้เฉพาะไทยพาณิชย์</font></label>
                      <input type="text" id="promptpay_number" name="promptpay_number" class="form-control" value="<?php echo $bank['promptpay_number']; ?>" placeholder="เบอร์ PromptPay" maxlength="10">
                    </div>
                  </div>
                  <div class="col-md-1">
                    <div class="form-group">
                      <label class="control-label">สถานะ</label>
                      <select class="form-control" name="promptpay_status" id="promptpay_status">
                        <option value="0">ปิด</option>
                        <option value="1" <?php if ($bank['promptpay_status']==1): ?>
                          selected
                        <?php endif; ?> >เปิด</option>
                      </select>
                    </div>
                  </div>
					<div class="col-md-4">
						<div class="form-group">
						<label class="control-label">เวลาปิดระบบฝากออโต้ <font style="color:red;">(จาก) รูปแบบตัวอย่าง "00:15"</font></label>
							<input type="text" <?php echo $bank['status_withdraw'] == "0" ? "" : "readonly" ?>  id="start_time_can_not_deposit" name="start_time_can_not_deposit" class="form-control" value="<?php echo $bank['start_time_can_not_deposit']; ?>" placeholder="รุปแบบตัวอย่าง 00:30">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
						<label class="control-label">เวลาปิดระบบฝากออโต้ <font style="color:red;">(ถึง) รูปแบบตัวอย่าง "02:15"</font></label>
							<input type="text" <?php echo $bank['status_withdraw'] == "0" ? "" : "readonly" ?>  id="end_time_can_not_deposit" name="end_time_can_not_deposit" class="form-control" value="<?php echo $bank['end_time_can_not_deposit']; ?>" placeholder="รุปแบบตัวอย่าง 00:30">
						</div>
					</div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">สถานะ</label>
                      <select class="form-control" name="status" id="status">
                        <option value="0">ปิด</option>
                        <option value="1" <?php if ($bank['status']==1): ?>
                          selected
                        <?php endif; ?> >เปิด</option>
                      </select>
                    </div>
                  </div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">บัญชีที่ใช้ถอน</label>
							<select class="form-control" name="status_withdraw" id="status_withdraw">
								<option value="0">ปิด</option>
								<option value="1" <?php if ($bank['status_withdraw']==1): ?>
									selected
								<?php endif; ?> >เปิด</option>
							</select>
						</div>
					</div>
					<!-- <div class="col-md-4"></div> -->
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">จำนวนเงินถอนออโต้ได้ไม่เกิน (บาท/ครั้ง)</label>
							<input type="number" <?php echo $bank['status_withdraw'] == "1" ? "" : "readonly" ?> id="max_amount_withdraw_auto" value="<?php echo $bank['max_amount_withdraw_auto']; ?>" name="max_amount_withdraw_auto" class="form-control" placeholder="จำนวนเงินถอนออโต้ได้ไม่เกิน (บาท/ครั้ง)">
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
						<label class="control-label">ข้อความแสดงหน้าฝากเงิน <font style="color:red;">(เวลาปิดระบบฝากออโต้)</font></label>
							<textarea type="text" rows="6" style="" <?php echo $bank['status_withdraw'] == "0" ? "" : "readonly" ?>  id="message_can_not_deposit" name="message_can_not_deposit" class="form-control" placeholder="ข้อความแสดงหน้าฝากเงิน (เวลาปิดระบบฝากออโต้)"  cols="3"><?php echo $bank['message_can_not_deposit']; ?></textarea>
						</div>
					</div>
                </div>
              </div>
				<div id="container_auto_transfer" class="col-12" style="display: none">
					<h3 class="card-title header-form-api mt-2">โยกเงินออกเพื่อเก็บเข้าบัญชี AUTO</h3>
					<div class="form-body form-auto-trasnfer">
						<hr>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label">สถานะโยกเงินออกเพื่อเก็บเข้าบัญชี AUTO</label>
									<select class="form-control" name="auto_transfer" id="auto_transfer">
										<option value="0" <?php if ($bank['auto_transfer']==0): ?>
											selected
										<?php endif; ?> >ปิด</option>
										<option value="1" <?php if ($bank['auto_transfer']==1): ?>
											selected
										<?php endif; ?> >เปิด</option>
									</select>
								</div>
							</div>
							<div class="col-md-4 col-sm-4">
								<div class="form-group">
									<label class="control-label">ยอดเงินขั้นต่ำที่จะโยกออก</label>
									<input type="text" oninput="validateInputNumber(this)" id="auto_min_amount_transfer" name="auto_min_amount_transfer" class="form-control" value="<?php echo $bank['auto_min_amount_transfer']; ?>" placeholder="ระบุเฉพาะตัวเลข">
								</div>
							</div>
							<div class="col-md-4 col-sm-4">
								<div class="form-group">
									<label class="control-label">ธนาคารปลายทาง</label>
									<select id="auto_transfer_bank_code" name="auto_transfer_bank_code" class="form-control" style=" width: 100%;">
										<option value="">กรุณาเลือกธนาคารปลายทาง</option>
										<?php foreach ($bank_data_list as $value): ?>
											<option <?php if ($bank['auto_transfer_bank_code']==$value['bank_code']): ?>
												selected
											<?php endif; ?> value='<?php echo $value['bank_code']; ?>'><?php echo $value['bank_name']." : ".$value['code_en'];  ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
							<div class="col-md-4 col-sm-4">
								<div class="form-group">
									<label class="control-label">เลขบัญชีปลายทาง</label>
									<input type="number" id="auto_transfer_bank_number" name="auto_transfer_bank_number" class="form-control"  value="<?php echo $bank['auto_transfer_bank_number']; ?>" placeholder="ระบุเฉพาะตัวเลข">
								</div>
							</div>
							<div class="col-md-4 col-sm-4">
								<div class="form-group">
									<label class="control-label">ชื่อบัญชีปลายทาง</label>
									<input type="text"  id="auto_transfer_bank_acc_name" name="auto_transfer_bank_acc_name" class="form-control"  maxlength="255" value="<?php echo $bank['auto_transfer_bank_acc_name']; ?>" placeholder="ข้อมูลชื่อบัญชีปลายทาง">
								</div>
							</div>
						</div>
					</div>

				</div>

				<div id="container_check_regis" class="col-12" style="display: none">
					<h3 class="card-title header-form-api mt-2">เปิดใช้ตรวจสอบการสมัคร</h3>
					<div class="form-body form-auto-trasnfer">
						<hr>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label">เปิดใช้งานบัญชีนี้ในการตรวจสอบการสมัคร</label>
									<select class="form-control" name="check_regis" id="check_regis">
										<option value="0" <?php if ($bank['check_regis']==0): ?>
											selected
										<?php endif; ?> >ปิด</option>
										<option value="1" <?php if ($bank['check_regis']==1): ?>
											selected
										<?php endif; ?>>เปิด</option>
									</select>
								</div>
							</div>
						</div>
					</div>

				</div>
				<h3 class="card-title mt-2 header-form-api">API</h3>

				<div class="form-body form-api">
					<hr>
					<div class="row ">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label" id="api_token_1_label"><?php echo $bank['bank_code'] == "03" || $bank['bank_code'] == "3" ? "Account Token No" : "Device ID" ?></label><small class="text-danger ml-1 text-bold-500">*ระบุค่าหากต้องการแก้ไขเท่านั้น</small>
								<textarea type="text" id="api_token_1" name="api_token_1"   rows="3" class="form-control"  placeholder="Device ID"></textarea>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label" id="api_token_2_label"><?php echo $bank['bank_code'] == "03" || $bank['bank_code'] == "3" ? "User Token ID" : "PIN" ?></label><small class="text-danger ml-1 text-bold-500">*ระบุค่าหากต้องการแก้ไขเท่านั้น</small>
								<textarea type="text" id="api_token_2" name="api_token_2" rows="3" class="form-control" placeholder="PIN"></textarea>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label" id="api_token_3_label"><?php echo $bank['bank_code'] == "03" || $bank['bank_code'] == "3" ? "User Identity" : "Other" ?></label><small class="text-danger ml-1 text-bold-500">*ระบุค่าหากต้องการแก้ไขเท่านั้น</small>
								<textarea type="text" id="api_token_3" name="api_token_3" rows="3" class="form-control" placeholder="Other"></textarea>
							</div>
						</div>
					</div>
				</div>


				<hr />
				<div class="row mb-4">
					<div class="col-md-12">
						<div class="text-right m-b-10">
							<a type="button" href="<?php echo site_url('bank') ?>" class=" btn bg-gradient-warning waves-effect waves-light mr-1"><span><i class="fa fa-arrow-left mr-1"></i></span>ย้อนกลับ</a>
							<button  id="btn_update" type="submit" class=" btn bg-gradient-success waves-effect waves-light"><span><i class="fa fa-save mr-1"></i></span>แก้ไข</button>
						</div>
					</div>
				</div>
            </form>
          </div>
        </div>
      </section>
    </div>
  </div>
<script src="<?php echo base_url('assets/plugins/moment/min/moment.min.js') ?>"></script>
  <script src="<?php echo base_url('assets/plugins/numeral/min/numeral.min.js') ?>"></script>
  <script src="<?php echo base_url('assets/scripts/bank/bank_update.js?'.time()) ?>"></script>
