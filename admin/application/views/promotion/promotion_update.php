<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/dropify/dist/css/dropify.min.css'); ?>">
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
          <div class="col-12">
            <h2 class="content-header-title float-left mb-0">ระบบจัดการโปรโมชั่น</h2>
            <div class="breadcrumb-wrapper col-12">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo site_url('promotion') ?>">รายการโปรโมชั่น</a>
                </li>
                <li class="breadcrumb-item active">แก้ไขโปรโมชั่น</li>
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
            <form class="form" id="form_update" method="POST" action="<?php echo site_url("promotion/promotion_update/{$promotion['id']}") ?>" enctype="multipart/form-data">
              <h3 class="card-title">ข้อมูลโปรโมชั่น</h3>
              <hr>
              <div class="form-body mt-3">
				  <div class="row">
					  <div class="col-md-3">
						  <div class="form-group">
							  <label class="control-label">รูปแบบ</label>
							  <select class="form-control" name="category" id="category">
								  <option value="1">ปกติ</option>
								  <option value="2" <?php if ($promotion['category']==2): ?>
									  selected
								  <?php endif; ?>>Fix ยอดฝาก/โบนัส</option>
							  </select>
						  </div>
					  </div>
					  <div class="col-md-3">
						  <div class="form-group">
							  <label class="control-label">ประเภท</label>
							  <select class="form-control" name="type" id="type">
								  <option value="1">ถาวร</option>
								  <option value="2" <?php if ($promotion['type']==2): ?>
									  selected
								  <?php endif; ?>>รายวัน</option>
								  <option value="3" <?php if ($promotion['type']==3): ?>
									  selected
								  <?php endif; ?>>รายอาทิตย์</option>
								  <option value="4" <?php if ($promotion['type']==4): ?>
									  selected
								  <?php endif; ?>>รายเดือน</option>
                  <option value="5" <?php if ($promotion['type']==5): ?>
									  selected
								  <?php endif; ?>>นาทีทอง</option>
                  <option value="6" <?php if ($promotion['type']==6): ?>
									  selected
								  <?php endif; ?>>ฝากประจำ</option>
							  </select>
						  </div>
					  </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">จำนวนวันที่ฝากต่อเนื่อง (ตัวอย่าง 5)</label>
                <input type="text" class="form-control" id="number_of_deposit_days" name="number_of_deposit_days" placeholder="5" value="<?php echo $promotion['number_of_deposit_days'] ?>">
              </div>
              <div class="form-group">
                <label class="control-label">เริ่มต้นเวลา (ตัวอย่าง 12:00)</label>
                <input type="text" class="form-control" id="pro_start_time" name="start_time" placeholder="12:00" value="<?php echo $promotion['start_time'] ?>">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">สิ้นสุดเวลา (ตัวอย่าง 13:00)</label>
                <input type="text" class="form-control" id="pro_end_time"  name="end_time" placeholder="13:00" value="<?php echo $promotion['end_time'] ?>">
              </div>
            </div>
				  </div>
                <div class="row ">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">ชื่อโปรโมชั่น</label>
                      <input type="text" id="name" name="name" class="form-control"  value="<?php echo $promotion['name']; ?>" placeholder="ข้อมูลชื่อโปรโมชั่น">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">จำนวนโบนัส (%)</label>
                      <input type="text" id="percent" name="percent" class="form-control" value="<?php echo $promotion['percent']; ?>" placeholder="ข้อมูลจำนวนโบนัส (%)">
                    </div>
					  <div class="form-group">
						  <label class="control-label">Fix ยอดฝาก (บาท)</label>
						  <input type="text" id="fix_amount_deposit" name="fix_amount_deposit" value="<?php echo $promotion['fix_amount_deposit']; ?>" class="form-control"  placeholder="Fix ยอดฝาก (บาท)">
					  </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">โบนัสสูงสุด (บาท)</label>
                      <input type="text" id="max_value" name="max_value" class="form-control" value="<?php echo $promotion['max_value']; ?>"  placeholder="ข้อมูลโบนัสสูงสุด (บาท)">
                    </div>
					  <div class="form-group">
						  <label class="control-label">Fix โบนัส (บาท)</label>
						  <input type="text" id="fix_amount_deposit_bonus" name="fix_amount_deposit_bonus" value="<?php echo $promotion['fix_amount_deposit_bonus']; ?>" class="form-control"  placeholder="Fix โบนัส (บาท)">
					  </div>
                  </div>
                </div>
                <div class="row ">

                   <!-- style=" display: none;"  -->
                  <div class="col-md-4" id="div_max_use">
                    <div class="form-group">
                      <label class="control-label">ใช้ได้ต่อ User</label>
                      <input min="1" type="number" id="max_use" name="max_use" value="<?php echo $promotion['max_use']; ?>"  class="form-control"  placeholder="ข้อมูลใช้ได้ต่อ User">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">สถานะ</label>
                      <select class="form-control" name="status" id="status">
                        <option value="0">ปิด</option>
                        <option value="1" <?php if ($promotion['status']==1): ?>
                          selected
                        <?php endif; ?>>เปิด</option>
                      </select>
                    </div>
                  </div>
					<div class="col-md-4">
						<fieldset class="form-group">
							<label >รูปภาพ (700x200)</label>
							<input type="file" id="image"  name="image" class="dropify" data-height="200" data-allowed-file-extensions="jpg png"
									<?php if ($promotion['image_url']): ?>
										data-title="<?php echo $promotion['image'] ?>"
										data-default-file="<?php echo $promotion['image_url'] ?>"
										data-show-remove="false"
									<?php endif; ?>
							/>
						</fieldset>
					</div>
                </div>
                <div class="row">
					<?php foreach (game_code_list() as $game_code): ?>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">คูณยอดเทิร์น (<?php echo game_code_text_list()[$game_code]; ?>)</label>
							<input type="text" id="turn_<?php echo strtolower($game_code); ?>" name="turn_<?php echo strtolower($game_code); ?>" class="form-control" value="<?php echo $promotion['turn_'.strtolower($game_code)]; ?>" placeholder="ข้อมูลคูณยอดเทิร์น (<?php echo $game_code; ?>)">
						</div>
					</div>
					<?php endforeach; ?>
                </div>
                <hr />
                <div class="row">
                  <div class="col-md-12">
                    <div class="text-right m-b-10">
                      <a type="button" href="<?php echo site_url('promotion') ?>" class=" btn bg-gradient-warning waves-effect waves-light mr-1"><span><i class="fa fa-arrow-left mr-1"></i></span>ย้อนกลับ</a>
                      <button  id="btn_update" type="submit" class=" btn bg-gradient-success waves-effect waves-light"><span><i class="fa fa-save mr-1"></i></span>แก้ไข</button>
                    </div>
                  </div>
                </div>
              </div>

            </form>
          </div>
        </div>
      </section>
    </div>
  </div>
  <script src="<?php echo base_url('assets/plugins/dropify/dist/js/dropify.min.js') ?>"></script>
  <script src="<?php echo base_url('assets/plugins/numeral/min/numeral.min.js') ?>"></script>
  <script src="<?php echo base_url('assets/scripts/promotion/promotion_update.js?'.time()) ?>"></script>
