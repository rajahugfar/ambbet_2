<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Withdraw extends CI_Controller
{
	public $menu_service;
	public function __construct()
	{
		date_default_timezone_set('Asia/Bangkok');
		parent::__construct();

		//if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'],[roleAdmin(),roleSuperAdmin()])) {
		if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role'])) {
			redirect('../auth');
		}
		$this->load->library(['Menu_service']);
		if(!$this->menu_service->validate_permission_menu($this->uri)){
			redirect('../auth');
		}
	}
    public function index()
    {
		$this->load->helper('url');
		$currentURL = current_url();
		$log_page_id = $this->Log_page_model->log_page_create([
			'ip' => isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $this->input->ip_address(),
			'admin' => $_SESSION['user']['id'],
			'username' => $_SESSION['user']['username'],
			'page_name' => "ถอนเงิน",
			'description' => 'หน้ารายการ',
			'page_url' => $currentURL,
		]);
		$data['user'] = [];
        $data['page'] = 'withdraw/withdraw';
        $this->load->view('main', $data);
    }
    public function withdraw_list_page()
    {
        $get = $this->input->get();
        $search = $get['search']['value'];
        // $dir = $get['order'][0]['dir'];//order
        $per_page = $get['length'];//จำนวนที่แสดงต่อ 1 หน้า
        $page = $get['start'];
        $search_data = [
         'per_page' => $per_page,//left
         'page' => $page,
         'type' => 2
        ];
        if ($search!="") {
            $search_data['search'] = $search;
        }
		if(isset($get['date_start']) && isset($get['date_end'])){
			$search_data['date_start'] = $get['date_start'];
			$search_data['date_end'] = $get['date_end'];
		}
		if(isset($get['status']) && $get['status'] !== ""){
			$search_data['status'] = $get['status'];
		}
        $withdraw_count_all = $this->Finance_model->finance_count([
        	'type' => 2
		]);
        $withdraw_count_search = $this->Finance_model->finance_count($search_data);
        $data = $this->Finance_model->finance_list_page($search_data);
        echo json_encode([
         "draw" => intval($get['draw']),
         "recordsTotal" => intval($withdraw_count_all),
         "recordsFiltered" => intval($withdraw_count_search),
         "data" => $data,
       ]);
    }

	public function withdraw_list_excel()
	{
		$post = $this->input->post();
		$search = $post['search']['value'];
		$search_data = [
			'type' => 2
		];
		if ($search!="") {
			$search_data['search'] = $search;
		}
		if(isset($post['date_start']) && isset($post['date_end'])){
			$search_data['date_start'] = $post['date_start'];
			$search_data['date_end'] = $post['date_end'];
		}
		if(isset($get['status']) && $get['status'] !== ""){
			$search_data['status'] = $get['status'];
		}
		$data = $this->Finance_model->finance_list_excel($search_data);
		echo json_encode([
			"data" => $data,
		]);
	}

	public function withdraw_list_no_paginate_page()
	{
		$get = $this->input->get();
		$search = $get['search']['value'];
		// $dir = $get['order'][0]['dir'];//order
		$per_page = $get['length'];//จำนวนที่แสดงต่อ 1 หน้า
		$page = $get['start'];
		$search_data = [
			'per_page' => $per_page,//left
			'page' => $page,
			'type' => 2,
			'status' => 0,
		];
		if ($search!="") {
			$search_data['search'] = $search;
		}
		$withdraw_count_all = $this->Finance_model->finance_no_join_count($search_data);
		$data = $this->Finance_model->finance_list_no_join_page($search_data);
		echo json_encode([
			"draw" => intval($get['draw']),
			"recordsTotal" => intval($withdraw_count_all),
			"data" => $data,
		]);
	}
    public function withdraw_form_detail($id = "")
    {
		$this->load->helper('url');
		$currentURL = current_url();
		$log_page_id = $this->Log_page_model->log_page_create([
			'ip' => isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $this->input->ip_address(),
			'admin' => $_SESSION['user']['id'],
			'username' => $_SESSION['user']['username'],
			'page_name' => "ถอนเงิน",
			'description' => 'หน้ารายละเอียด',
			'page_url' => $currentURL,
		]);
        $data['withdraw'] = $this->Finance_model->finance_find([
          'id' => $id
        ]);
        $data['page'] = 'withdraw/withdraw_detail';
        $this->load->view('main', $data);
    }
	public function withdraw_status($id = "")
	{
		check_parameter([], 'POST');
		$post = $this->input->post();
		if(isset($post['bank_id_withdraw']) && !empty($post['bank_id_withdraw']) && $post['status'] == "1"){
			$chk_cache_process_withdraw_once_chk = $this->cache->file->get('process_withdraw_once_'.$post['bank_id_withdraw']);
			if($chk_cache_process_withdraw_once_chk  !== FALSE){
				$chk_seconds =  strtotime(date("Y-m-d H:i:s")) - strtotime($chk_cache_process_withdraw_once_chk);
				$message_error = "กรุณารออีก ".($chk_seconds >= 45 ? "45" : $chk_seconds).' วินาทีถึงทำรายการใหม่ได้อีกครั้ง';
				echo json_encode([
					'message' => 'ทำรายการไม่สำเร็จ '.$message_error,
					'error' => true
				]);
				exit();
			}
			$this->cache->file->save('process_withdraw_once_'.$post['bank_id_withdraw'],date("Y-m-d H:i:s"), 45);
		}
		$chk_cache_process_withdraw_chk = $this->cache->file->get('process_withdraw_'.$id);
		if($chk_cache_process_withdraw_chk  !== FALSE){
			$message_error = "รายการนี้กำลังถูกดำเนินการอยู่";
			echo json_encode([
				'message' => 'ทำรายการไม่สำเร็จ '.$message_error,
				'error' => true
			]);
			exit();
		}else{
			$this->cache->file->save('process_withdraw_'.$id,$_SESSION['user']['id'], 180);
		}

		$finance = $this->Finance_model->finance_find([
			'id' => $id
		]);


		if ($finance!="") {

			$search_data = [
				'id' => $finance['account'],
			];
			$member = $this->User_model->user_find($search_data);
			//เพิ่ม Logs
			$log_deposit_withdraw_id = $this->Log_deposit_withdraw_model->log_deposit_withdraw_create([
				'account' => null,
				'amount' => null,
				'username' => $member['username'],
				'amount_before' => null,
				'type' => '2', //ถอน
				'description' => 'ถอนเงิน',
				'admin' =>$_SESSION['user']['id'],
				'withdraw_status_request' => $post['status'],
			]);

			if($finance['is_auto_withdraw'] == "1" && ($finance['auto_withdraw_status'] != "3" || $finance['auto_withdraw_status'] != 3)){
				$status_bot_description = [
					"0" => "รอดำเนินการ",
					"1" => "กำลังดำเนินการ",
					"2" => "สำเร็จ (ถอนออโต้)",
					"3" => "ไม่สำเร็จ",
				];
				$log_deposit_withdraw = $this->Log_deposit_withdraw_model->log_deposit_withdraw_find([
					'id' => $log_deposit_withdraw_id
				]);
				if($log_deposit_withdraw!=""){
					$this->Log_deposit_withdraw_model->log_deposit_withdraw_update([
						'id' => $log_deposit_withdraw_id,
						'account' => $finance['account'],
						'amount' => $finance['amount'],
						'description' => $log_deposit_withdraw['description']." | ทำรายการไม่สำเร็จ รายการ Finance#".$finance['id']." ถูก BOT จัดการอยู่, สถานะ => ".$status_bot_description[$finance['auto_withdraw_status']],
						'withdraw_status_status' => 0,
					]);
					$this->cache->file->delete('process_withdraw_'.$id);}
				echo json_encode([
					'message' => 'ทำรายการไม่สำเร็จ รายการ Finance#'.$finance["id"].' ถูก BOT จัดการอยู่, สถานะ => '.$status_bot_description[$finance['auto_withdraw_status']],
					'error' => true
				]);
				exit();
			}

			if(
				($finance['status'] == "1" || $finance['status'] == "3") && ($post['status'] == "1" || $post['status'] == "3")
			){
				$log_deposit_withdraw = $this->Log_deposit_withdraw_model->log_deposit_withdraw_find([
					'id' => $log_deposit_withdraw_id
				]);
				$status = "สำเร็จ (ถอนออโต้)";
				if($finance['status'] == "3"){
					$status = "สำเร็จ (ถอนมือ)";
				}
				if($log_deposit_withdraw!=""){
					$this->Log_deposit_withdraw_model->log_deposit_withdraw_update([
						'id' => $log_deposit_withdraw_id,
						'account' => $finance['account'],
						'amount' => $finance['amount'],
						'description' => $log_deposit_withdraw['description']." | ทำรายการไม่สำเร็จ รายการ Finance#".$finance['id']." อยู่ในสถานะ".$status." อยู่แล้ว",
						'withdraw_status_status' => 0,
					]);
							$this->cache->file->delete('process_withdraw_'.$id);}
				echo json_encode([
					'message' => 'ทำรายการไม่สำเร็จ รายการ Finance#'.$finance["id"].' อยู่ในสถานะ'.$status.' อยู่แล้ว',
					'error' => true
				]);
				exit();
			}else{
				if($post['status'] == "1"){
					$finance = $this->Finance_model->finance_find([
						'id' => $id
					]);
					if($finance['status'] == "4"){
						$log_deposit_withdraw = $this->Log_deposit_withdraw_model->log_deposit_withdraw_find([
							'id' => $log_deposit_withdraw_id
						]);
						if($log_deposit_withdraw!=""){
							$this->Log_deposit_withdraw_model->log_deposit_withdraw_update([
								'id' => $log_deposit_withdraw_id,
								'account' => $finance['account'],
								'amount' => $finance['amount'],
								'description' => $log_deposit_withdraw['description']." | ทำรายการไม่สำเร็จ รายการ Finance#".$finance['id']." อยู่ในสถานะ ดำเนินการถอนออโต้ อยู่ ณ ขณะนี้",
								'withdraw_status_status' => 0,
							]);
									$this->cache->file->delete('process_withdraw_'.$id);}
						echo json_encode([
							'message' => 'ทำรายการไม่สำเร็จ รายการ Finance#'.$finance["id"].' อยู่ในสถานะ ดำเนินการถอนออโต้ อยู่ ณ ขณะนี้',
							'error' => true
						]);
						exit();
					}else{
						/*$this->Finance_model->finance_update([
							'id' => $id,
							'status' => "4" //ดำเนินการถอน
						]);*/
					}
				}
			}


			//ถอน auto
			//ตรวจสอบสมาชิก
			$amount_before = 0;
			if($member != ""){
				$amount_before = $this->remaining_credit($member);
			}else{
				$log_deposit_withdraw = $this->Log_deposit_withdraw_model->log_deposit_withdraw_find([
					'id' => $log_deposit_withdraw_id
				]);
				if($log_deposit_withdraw!=""){
					$this->Log_deposit_withdraw_model->log_deposit_withdraw_update([
						'id' => $log_deposit_withdraw_id,
						'account' => $finance['account'],
						'amount' => $finance['amount'],
						'description' => $log_deposit_withdraw['description']." | ทำรายการไม่สำเร็จ ไม่พบสมาชิกในระบบ",
						'withdraw_status_status' => 0,
					]);
							$this->cache->file->delete('process_withdraw_'.$id);}
				echo json_encode([
					'message' => 'ทำรายการไม่สำเร็จ ไม่พบสมาชิกในระบบ',
					'error' => true
				]);
				exit();
			}

			if ($post['status']=="2") {

				$form_data_deposit = [];
				$form_data_deposit["account_agent_username"] = $member['account_agent_username'];
				$form_data_deposit["amount"] = $finance['amount'];
				$form_data_deposit = member_credit_data($form_data_deposit);
				$response_deposit = $this->game_api_librarie->deposit($form_data_deposit);
				if (isset($response_deposit['ref'])) {

					$user_admin = $this->User_model->user_find([
						'id' => $_SESSION['user']['id']
					]);
					$log_add_credit_id = $this->Log_add_credit_model->log_add_credit_create([
						'account' => $member['id'],
						'username' => $member['username'],
						'full_name' => $member['full_name'],
						'from_amount' => $finance['amount'],
						'amount' => $form_data_deposit["amount"],
						'type' => 'bonus_from_not_approve_withdraw',
						'description' => 'เพิ่มเครดิตกลับคืน (ถอนเงินไม่อนุมัติ)',
						'manage_by' =>$user_admin['id'],
						'manage_by_username' =>$user_admin['username'],
						'manage_by_full_name' =>$user_admin['full_name'],
					]);

					$log_deposit_id = $this->Log_deposit_withdraw_model->log_deposit_withdraw_create([
						'account' => $member['id'],
						'username' => $member['username'],
						'amount' => $finance['amount'],
						'amount_before' => $amount_before,
						'type' => '1', //ฝาก
						'description' => 'เพิ่มเครดิต (รายการถอน => ไม่อนุมัติ) #'.$finance['id'],
						'admin' =>$_SESSION['user']['id'],
					]);

					$log_deposit_withdraw = $this->Log_deposit_withdraw_model->log_deposit_withdraw_find([
						'id' => $log_deposit_withdraw_id
					]);
					if($log_deposit_withdraw!=""){
						$this->Log_deposit_withdraw_model->log_deposit_withdraw_update([
							'id' => $log_deposit_withdraw_id,
							'account' => $finance['account'],
							'amount' => $finance['amount'],
							'amount_before' => $amount_before,
							'description' => $log_deposit_withdraw['description']." | เปลี่ยนสถานะเป็นไม่อนุมัติ",
							'withdraw_status_status' => 1,
						]);
					}
				}else{
					$error_message = isset($response_deposit['code']) && !empty($response_deposit['code']) ? '(ไม่สำเร็จ : #'.$response_deposit['code'].')' : '(ไม่สำเร็จ)';
					$log_deposit_withdraw = $this->Log_deposit_withdraw_model->log_deposit_withdraw_find([
						'id' => $log_deposit_withdraw_id
					]);
					if($log_deposit_withdraw!=""){
						$this->Log_deposit_withdraw_model->log_deposit_withdraw_update([
							'id' => $log_deposit_withdraw_id,
							'account' => $finance['account'],
							'amount' => $finance['amount'],
							'amount_before' => $amount_before,
							'description' => $log_deposit_withdraw['description']." | เปลี่ยนสถานะเป็นไม่อนุมัติ ".$error_message,
							'withdraw_status_status' => 0,
						]);
								$this->cache->file->delete('process_withdraw_'.$id);}
					echo json_encode([
						'message' => 'ทำรายการไม่สำเร็จ '.$error_message,
						'error' => true
					]);
					exit();
				}

			}else{
				$log_deposit_withdraw = $this->Log_deposit_withdraw_model->log_deposit_withdraw_find([
					'id' => $log_deposit_withdraw_id
				]);
				if($log_deposit_withdraw!=""){
					$status_text = " รอตรวจสอบ";
					if($post['status'] == "1"){
						$status_text = " อนุมัติ (ถอนออโต้)";
					}else if($post['status'] == "3"){
						$status_text = " อนุมัติ (ถอนมือ)";
					}
					$this->Log_deposit_withdraw_model->log_deposit_withdraw_update([
						'id' => $log_deposit_withdraw_id,
						'account' => $finance['account'],
						'amount' => $finance['amount'],
						'amount_before' => $amount_before,
						'description' => $log_deposit_withdraw['description']." | เปลี่ยนสถานะเป็น".$status_text,
					]);
				}
			}
			$user_admin_withdraw = $this->User_model->user_find([
				'id' => $_SESSION['user']['id']
			]);
			$this->Finance_model->finance_update([
				'id' => $id,
				'status' => $post['status'],
				'ip' => isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $this->input->ip_address(),
				'manage_by_fullname' => $_SESSION['user']['username'].' - '.$user_admin_withdraw['full_name'],
				'manage_by' => $_SESSION['user']['id']
			]);

			if($post['status']=="1"){

				if($member != ""){

					//ตรวจธนาคาร
					$bank_list = array(
						'01' => 'bbl',
						'02' => 'kbank',
						'03' => 'ktb',
						'04' => 'tmb',
						'05' => 'scb',
						'06' => 'bay',
						'07' => 'gsb',
						'08' => 'tbank',
						'09' => 'baac',
						'1' => 'bbl',
						'2' => 'kbank',
						'3' => 'ktb',
						'4' => 'tmb',
						'5' => 'scb',
						'6' => 'bay',
						'7' => 'gsb',
						'8' => 'tbank',
						'9' => 'baac',
						'10' => 'tmn',
					);
					if(array_key_exists($finance['bank'],$bank_list)){
						$bank_data_list = $this->Bank_model->bank_data_list();
						$chk_match_bank = false;
						$bank_code = "";
						for($i =0;$i<count($bank_data_list);$i++){
							if(strtoupper($bank_list[$finance['bank']]) == strtoupper($bank_data_list[$i]['code_en'])){
								$bank_code = $bank_data_list[$i]['bank_code'];
								$chk_match_bank = true;
								break;
							}
						}

						if($chk_match_bank){
							try{

								$bank_can_withdraws = $this->Bank_model->bank_list(['status'=>1,'status_withdraw' => 1,'api_type' => 1]);
								$bank_can_withdraw_once = null;
								$chk_bank_can_withdraw = false;
								foreach($bank_can_withdraws as $bank_can_withdraw){
									if(isset($post['bank_id_withdraw']) && !empty($post['bank_id_withdraw'])
										&& $post['bank_id_withdraw'] == $bank_can_withdraw['id']
										&& ($bank_can_withdraw['bank_code'] == "05" || $bank_can_withdraw['bank_code'] == "5")){
										$bank_can_withdraw_once = $bank_can_withdraw;
										$chk_bank_can_withdraw = true;
										break;
									}else if(isset($post['bank_id_withdraw']) && !empty($post['bank_id_withdraw'])
										&& $post['bank_id_withdraw'] == $bank_can_withdraw['id']
										&& ($bank_can_withdraw['bank_code'] == "02" || $bank_can_withdraw['bank_code'] == "2")){
										$bank_can_withdraw_once = $bank_can_withdraw;
										$chk_bank_can_withdraw = true;
										break;
									}else if(isset($post['bank_id_withdraw']) && !empty($post['bank_id_withdraw'])
										&& $post['bank_id_withdraw'] == $bank_can_withdraw['id']
										&& ($bank_can_withdraw['bank_code'] == "06" || $bank_can_withdraw['bank_code'] == "6")){
										$bank_can_withdraw_once = $bank_can_withdraw;
										$chk_bank_can_withdraw = true;
										break;
									}else if(isset($post['bank_id_withdraw']) && !empty($post['bank_id_withdraw'])
										&& $post['bank_id_withdraw'] == $bank_can_withdraw['id']
										&& ($bank_can_withdraw['bank_code'] == "11" )){
										$bank_can_withdraw_once = $bank_can_withdraw;
										$chk_bank_can_withdraw = true;
										break;
									}else if(isset($post['bank_id_withdraw']) && !empty($post['bank_id_withdraw'])
										&& $post['bank_id_withdraw'] == $bank_can_withdraw['id']
										&& ($bank_can_withdraw['bank_code'] == "10" )){
										$bank_can_withdraw_once = $bank_can_withdraw;
										$chk_bank_can_withdraw = true;
										break;
									}else if(empty($post['bank_id_withdraw'])
										&& (($bank_can_withdraw['bank_code'] == "02" || $bank_can_withdraw['bank_code'] == "2")
											||  ($bank_can_withdraw['bank_code'] == "05" || $bank_can_withdraw['bank_code'] == "5")
											||  ($bank_can_withdraw['bank_code'] == "06" || $bank_can_withdraw['bank_code'] == "6")
											||  ($bank_can_withdraw['bank_code'] == "11" )
										)){
										$bank_can_withdraw_once = $bank_can_withdraw;
										$chk_bank_can_withdraw = true;
										break;
									}
								}

								if($chk_bank_can_withdraw){
									if(
										!isset($bank_can_withdraw_once['max_amount_withdraw_auto']) ||
										(
											isset($bank_can_withdraw_once['max_amount_withdraw_auto']) &&
											(float)$finance['amount'] > (float)$bank_can_withdraw_once['max_amount_withdraw_auto']
										)
									){
										$this->Finance_model->finance_update([
											'id' => $id,
											'status' => $finance['status'],
											'bank_withdraw_id' => !is_null($bank_can_withdraw_once) && !empty($bank_can_withdraw_once) ?  $bank_can_withdraw_once['id'] : null,
											'bank_withdraw_name' => !is_null($bank_can_withdraw_once) && !empty($bank_can_withdraw_once) ? $bank_can_withdraw_once['bank_name']." | ".$bank_can_withdraw_once['account_name'].' | '.$bank_can_withdraw_once['bank_number'] : null
										]);
										$log_deposit_withdraw = $this->Log_deposit_withdraw_model->log_deposit_withdraw_find([
											'id' => $log_deposit_withdraw_id
										]);
										if($log_deposit_withdraw!=""){
											$this->Log_deposit_withdraw_model->log_deposit_withdraw_update([
												'id' => $log_deposit_withdraw_id,
												'description' => $log_deposit_withdraw['description']." | ทำรายการไม่สำเร็จ, จำนวนเงินถอนออโต้ได้ไม่เกิน ( ".number_format($bank_can_withdraw_once['max_amount_withdraw_auto'],2)." บาท/ครั้ง) | ยอดถอน ".number_format($finance['amount'],2),
												'withdraw_status_status' => 0,
											]);
													$this->cache->file->delete('process_withdraw_'.$id);}
										echo json_encode([
											'message' => 'ทำรายการไม่สำเร็จ จำนวนเงินถอนออโต้ได้ไม่เกิน ( '.number_format($bank_can_withdraw_once["max_amount_withdraw_auto"],2).' บาท/ครั้ง)',
											'error' => true
										]);
									}else{
										$chk_cache_process_withdraw = $this->cache->file->get('process_withdraw');
										//if($chk_cache_process_withdraw  !== FALSE){
										if(false){
											$this->Finance_model->finance_update([
												'id' => $id,
												'manage_by' => $_SESSION['user']['id'],
												'status' => $finance['status'],
												'bank_withdraw_id' => !is_null($bank_can_withdraw_once) && !empty($bank_can_withdraw_once) ?  $bank_can_withdraw_once['id'] : null,
												'bank_withdraw_name' => !is_null($bank_can_withdraw_once) && !empty($bank_can_withdraw_once) ? $bank_can_withdraw_once['bank_name']." | ".$bank_can_withdraw_once['account_name'].' | '.$bank_can_withdraw_once['bank_number'] : null
											]);
											$log_deposit_withdraw = $this->Log_deposit_withdraw_model->log_deposit_withdraw_find([
												'id' => $log_deposit_withdraw_id
											]);
											$message_error = "มีบางรายการกำลังดำเนินการถอนออโต้อยู่, กรุณาลองใหม่อีกครั้ง";
											if($log_deposit_withdraw!=""){
												$this->Log_deposit_withdraw_model->log_deposit_withdraw_update([
													'id' => $log_deposit_withdraw_id,
													'description' => $log_deposit_withdraw['description']." | ทำรายการไม่สำเร็จ ".$message_error,
													'withdraw_status_status' => 0,
												]);
														$this->cache->file->delete('process_withdraw_'.$id);}
											echo json_encode([
												'message' => 'ทำรายการไม่สำเร็จ '.$message_error,
												'error' => true
											]);
											exit();
										}
										$this->cache->file->save('process_withdraw',date('Y-m-d H:i:s'), 50);
										$annotation ="WD ID :{$log_deposit_withdraw_id} Amount :{$finance['amount']} by {$_SESSION['user']['id']}:{$_SESSION['user']['username']}";
										if($bank_can_withdraw_once['bank_code'] == "05" || $bank_can_withdraw_once['bank_code'] == "5"){
											$res_withdraw = $this->auto_withdraw_librarie->transfer($member['username'],$finance['bank_number'],$bank_code,$finance['amount'],decrypt(base64_decode($bank_can_withdraw_once['api_token_1']),$this->config->item('secret_key_salt')),decrypt(base64_decode($bank_can_withdraw_once['api_token_2']),$this->config->item('secret_key_salt')),$bank_can_withdraw_once['bank_number'],$annotation);
										}else if($bank_can_withdraw_once['bank_code'] == "02" || $bank_can_withdraw_once['bank_code'] == "2"){
											$bank_code = getBankCodeForKbank()[$bank_code];
											$res_withdraw = $this->auto_withdraw_librarie->transfer_kplus($member['username'],$finance['bank_number'],$bank_code,$finance['amount'],decrypt(base64_decode($bank_can_withdraw_once['api_token_1']),$this->config->item('secret_key_salt')),decrypt(base64_decode($bank_can_withdraw_once['api_token_2']),$this->config->item('secret_key_salt')),$bank_can_withdraw_once['bank_number']);
										}else if($bank_can_withdraw_once['bank_code'] == "06" || $bank_can_withdraw_once['bank_code'] == "6"){
											$bank_code = getBankCodeForKrungsri()[$bank_code];
											$res_withdraw = $this->auto_withdraw_librarie->transfer_kma($member['username'],$finance['bank_number'],$bank_code,$finance['amount'],decrypt(base64_decode($bank_can_withdraw_once['api_token_1']),$this->config->item('secret_key_salt')),decrypt(base64_decode($bank_can_withdraw_once['api_token_2']),$this->config->item('secret_key_salt')),$bank_can_withdraw_once['bank_number']);
										}else if($bank_can_withdraw_once['bank_code'] == "11"){
											$res_withdraw = $this->auto_withdraw_librarie->transfer_kkp($member['username'],$finance['bank_number'],$bank_code,$finance['amount'],decrypt(base64_decode($bank_can_withdraw_once['api_token_1']),$this->config->item('secret_key_salt')),decrypt(base64_decode($bank_can_withdraw_once['api_token_2']),$this->config->item('secret_key_salt')),$bank_can_withdraw_once['bank_number']);
										}else if($bank_can_withdraw_once['bank_code'] == "10"){
											$res_withdraw = $this->auto_withdraw_librarie->transfer_truewallet($member['username'],$finance['bank_number'],$bank_code,$finance['amount'],$bank_can_withdraw_once['api_token_1'],$bank_can_withdraw_once['api_token_2'],$bank_can_withdraw_once['bank_number'],$bank_can_withdraw_once['username'],$bank_can_withdraw_once['password'],$annotation);
											//print_r($res_withdraw);
										}
										if($res_withdraw['status']){
											$this->cache->file->delete('process_withdraw');
											$qrcode = null;
											if(($bank_can_withdraw_once['bank_code'] == "05" || $bank_can_withdraw_once['bank_code'] == "5") && isset($res_withdraw['msg']['data']) && isset($res_withdraw['msg']['data']['additionalMetaData']) && isset($res_withdraw['msg']['data']['additionalMetaData']['paymentInfo']) && is_array($res_withdraw['msg']['data']['additionalMetaData']['paymentInfo']) && count($res_withdraw['msg']['data']['additionalMetaData']['paymentInfo']) > 0){
												foreach ($res_withdraw['msg']['data']['additionalMetaData']['paymentInfo'] as $paymentInfo){
													if(is_null($qrcode) && isset($paymentInfo['QRstring']) && !empty($paymentInfo['QRstring'])){
														$qrcode = $paymentInfo['QRstring'];
													}
												}
											}else if(($bank_can_withdraw_once['bank_code'] == "02" || $bank_can_withdraw_once['bank_code'] == "2")  && isset($res_withdraw['msg']['rawQr']) && !empty($res_withdraw['msg']['rawQr'])){
												$qrcode = $res_withdraw['msg']['rawQr'];
											}else if(($bank_can_withdraw_once['bank_code'] == "06" || $bank_can_withdraw_once['bank_code'] == "6" )  && isset($res_withdraw['msg']['QRCimagevalue'])){
												$qrcode = $res_withdraw['msg']['QRCimagevalue'];
											}else if(($bank_can_withdraw_once['bank_code'] == "11" )  && isset($res_withdraw['msg']['qrData'])){
												$qrcode = $res_withdraw['msg']['qrData'];

												$report_id = $res_withdraw['msg']['transactionRef'];
												$datetime_kk_tr = explode(" ",$res_withdraw['msg']['txnDate']);
												$date_kkp_tr = explode("/",$datetime_kk_tr[0]);
												$date_time_kkp_trs = $date_kkp_tr[2].'-'.$date_kkp_tr[1].'-'.$date_kkp_tr[0].' '.$datetime_kk_tr[1];
												$data_kkp_trans =array(
													'date_bank'=>$date_time_kkp_trs
												,'amount'=>(float)str_replace(",",'',$res_withdraw['msg']['transferAmount'])
												,'account'=>$member['id']
												,'type'=>2
												,'bank_number'=>$finance['bank_number']
												);

												$this->Transaction_model->transaction_create($data_kkp_trans);

												/*
													$data_kkp_credit = array(
														'process' => $res_withdraw['msg']['transferAmount']
														  , 'credit_before'=>$member['amount_deposit_auto']
														,'credit_after' => $member['amount_deposit_auto']-$res_withdraw['msg']['transferAmount']
														,'type'=>'2'
														,'account' =>$member['id']
														,'transaction'=>0
														,'admin'=>$_SESSION['user']['id']
														,'date_bank'=>$date_time_kkp_trs
														,'bank_id'=>$bank_can_withdraw_once['id']
														,'bank_name'=>$member['full_name']
														,'bank_number'=>$finance['bank_number']
														,'bank_code'=>'11'
														,'username'=>$member['username']
													);
													$this->Credit_model->credit_create($data_kkp_credit);*/

												$bank_list_kkp = getBankListUniqueTextCode();
												$data_reportsms = array(
													'config_api_id'=>$bank_can_withdraw_once['id']
												, 'payment_gateway'=>'โอนไป  '.$bank_list_kkp[$bank_code].' '.$finance['bank_number'].' '.$res_withdraw['msg']['toAccountInformation']['accountName']
												,'amount'=> (float)str_replace(",",'',$res_withdraw['msg']['transferAmount'])
												,'created_at'=>$date_time_kkp_trs
												,'is_bot_running'=>0
												,'create_date'=>$date_kkp_tr[2].'-'.$date_kkp_tr[1].'-'.$date_kkp_tr[0]
												,'create_time'=>$datetime_kk_tr[1]
												,'type_deposit_withdraw'=>'W'
												,'type'=>'KKP APP'
												,'report_id'=>$report_id
												);

												$this->Report_sms_model->report_sms_create($data_reportsms);
												$this->Bank_model->bank_update(['id'=>$bank_can_withdraw_once['id'],'balance'=>(float)str_replace(",",'',$res_withdraw['balance'])]);

											}
											$this->Finance_model->finance_update([
												'id' => $id,
												'qrcode' => $qrcode,
												'bank_withdraw_id' => !is_null($bank_can_withdraw_once) && !empty($bank_can_withdraw_once) ?  $bank_can_withdraw_once['id'] : null,
												'bank_withdraw_name' => !is_null($bank_can_withdraw_once) && !empty($bank_can_withdraw_once) ? $bank_can_withdraw_once['bank_name']." | ".$bank_can_withdraw_once['account_name'].' | '.$bank_can_withdraw_once['bank_number'] : null
											]);
											$log_deposit_withdraw = $this->Log_deposit_withdraw_model->log_deposit_withdraw_find([
												'id' => $log_deposit_withdraw_id
											]);
											if($log_deposit_withdraw!=""){
												$this->Log_deposit_withdraw_model->log_deposit_withdraw_update([
													'id' => $log_deposit_withdraw_id,
													'description' => $log_deposit_withdraw['description']." | ทำรายการสำเร็จ Finance ID #".$finance['id'],
													'withdraw_status_status' => 1,
												]);
											}

											//บันทึก line notify job
											date_default_timezone_set('Asia/Bangkok');
											$account_admin = $this->Account_model->account_find([
												'id' => $_SESSION['user']['id']
											]);
											$log_line_notify_id = $this->Log_line_notify_model->log_line_notify_create([
												'type' => 2,
												'message' => "ยอดถอน ".number_format($finance['amount'],2)." บาท ยูส ".$member['username']." เวลา ".date('Y-m-d H:i:s')." ถอนโดย ".$account_admin['full_name'],
											]);
											$line_send_messages_status = $this->Setting_model->setting_find([
												'name' => 'line_send_messages_status'
											]);
											$web_name = $this->Setting_model->setting_find([
												'name' => 'web_name'
											]);
											$line_login_callback = $this->Setting_model->setting_find([
												'name' => 'line_login_callback'
											]);
											$line_messages_token = $this->Setting_model->setting_find([
												'name' => 'line_messages_token'
											]);

											if(trim($line_send_messages_status['value'])==1 ) {

												$line_msg = array();
												$bank_list = array(
													'01' => 'bbl',
													'02' => 'kbank',
													'03' => 'ktb',
													'04' => 'tmb',
													'05' => 'scb',
													'06' => 'bay',
													'07' => 'gsb',
													'08' => 'tbank',
													'09' => 'baac',
													'1' => 'bbl',
													'2' => 'kbank',
													'3' => 'ktb',
													'4' => 'tmb',
													'5' => 'scb',
													'6' => 'bay',
													'7' => 'gsb',
													'8' => 'tbank',
													'9' => 'baac',
													'10' => 'True Wallet',
												);
												//print_r($user);
												$current_time = date('Y-m-d H:i:s');

												$line_msg['web_name'] = $web_name['value'];
												$line_msg['bank_tf_name'] = $bank_list[$member['bank_code']];
												$line_msg['bank_tf_number'] = $finance['bank_number'];
												$line_msg['balance'] = $res_withdraw['msg']['transferAmount'];
												$line_msg['bank_time'] = $current_time;
												$line_msg['credit_after'] = $member['amount_deposit_auto'] - $res_withdraw['msg']['transferAmount'];
												$line_msg['url_login'] = $line_login_callback['value'];
												$line_msg['linebot_userid'] = $member['linebot_userid'];
												$line_msg['type_tran'] = 2;
												//print_r($line_msg);
												//include_once ('/lib/send_line_message.php');
												if ($member['linebot_userid'] != '') {
													$this->auto_withdraw_librarie->send_line_message($line_msg, $line_messages_token['value']);
												}
											}

			$this->cache->file->delete('process_withdraw_'.$id);
											echo json_encode([
												'message' => 'ทำรายการสำเร็จ',
												'result' => true,
											]);
										}else{
											$this->cache->file->delete('process_withdraw');
											$this->Finance_model->finance_update([
												'id' => $id,
												'status' => $finance['status'],
												'bank_withdraw_id' => !is_null($bank_can_withdraw_once) && !empty($bank_can_withdraw_once) ?  $bank_can_withdraw_once['id'] : null,
												'bank_withdraw_name' => !is_null($bank_can_withdraw_once) && !empty($bank_can_withdraw_once) ? $bank_can_withdraw_once['bank_name']." | ".$bank_can_withdraw_once['account_name'].' | '.$bank_can_withdraw_once['bank_number'] : null
											]);

											$sum_amount_list = $this->Finance_model->sum_amount_deposit_and_withdraw(['account_list' => [$member['id']]]);
											if(array_key_exists($member['id'],$sum_amount_list)){
												$this->User_model->user_update(['sum_amount'=>$sum_amount_list[$member['id']]['sum_amount'],'id'=> $member['id']]);
											}

											$log_deposit_withdraw = $this->Log_deposit_withdraw_model->log_deposit_withdraw_find([
												'id' => $log_deposit_withdraw_id
											]);
											$message_error = isset($res_withdraw['msg']) ? $res_withdraw['msg'] : "";
											if(is_null($message_error) || empty(trim($message_error))){
												$message_error = " กรุณาตรวจสอบยอดถอนจำนวน ".number_format($finance['amount'],2)." บาท บน Internet Banking/Mobile App ว่าถูกถอนไปจริงหรือไม่";
											}
											if($log_deposit_withdraw!=""){
												$this->Log_deposit_withdraw_model->log_deposit_withdraw_update([
													'id' => $log_deposit_withdraw_id,
													'description' => $log_deposit_withdraw['description']." | ทำรายการไม่สำเร็จ ".$message_error,
													'withdraw_status_status' => 0,
												]);
														$this->cache->file->delete('process_withdraw_'.$id);}
											echo json_encode([
												'message' => 'ทำรายการไม่สำเร็จ '.$message_error,
												'error' => true
											]);
										}
									}

								}else{
									$this->Finance_model->finance_update([
										'id' => $id,
										'status' => $finance['status']
									]);
									$log_deposit_withdraw = $this->Log_deposit_withdraw_model->log_deposit_withdraw_find([
										'id' => $log_deposit_withdraw_id
									]);
									if($log_deposit_withdraw!=""){
										$this->Log_deposit_withdraw_model->log_deposit_withdraw_update([
											'id' => $log_deposit_withdraw_id,
											'description' => $log_deposit_withdraw['description']." | ทำรายการไม่สำเร็จ ไม่มีธนาคารสำหรับถอนออโต้เปิดใช้งาน (SCB,KBANK,KKP) เท่านั้น",
											'withdraw_status_status' => 0,
										]);
												$this->cache->file->delete('process_withdraw_'.$id);}
									echo json_encode([
										'message' => 'ทำรายการไม่สำเร็จ ไม่มีธนาคารสำหรับถอนออโต้เปิดใช้งาน (SCB,KBANK,KKP) เท่านั้น',
										'error' => true
									]);
								}
							}catch (Exception $ex){
								$this->Finance_model->finance_update([
									'id' => $id,
									'status' => $finance['status'],
									'bank_withdraw_id' => !is_null($bank_can_withdraw_once) && !empty($bank_can_withdraw_once) ?  $bank_can_withdraw_once['id'] : null,
									'bank_withdraw_name' => !is_null($bank_can_withdraw_once) && !empty($bank_can_withdraw_once) ? $bank_can_withdraw_once['bank_name']." | ".$bank_can_withdraw_once['account_name'].' | '.$bank_can_withdraw_once['bank_number'] : null
								]);
								$log_deposit_withdraw = $this->Log_deposit_withdraw_model->log_deposit_withdraw_find([
									'id' => $log_deposit_withdraw_id
								]);
								if($log_deposit_withdraw!=""){
									$this->Log_deposit_withdraw_model->log_deposit_withdraw_update([
										'id' => $log_deposit_withdraw_id,
										'description' => $log_deposit_withdraw['description']." | ทำรายการไม่สำเร็จ Something...",
										'withdraw_status_status' => 0,
									]);
											$this->cache->file->delete('process_withdraw_'.$id);}
								echo json_encode([
									'message' => 'ทำรายการไม่สำเร็จ Something...',
									'error' => true
								]);
							}

						}else{
							$this->Finance_model->finance_update([
								'id' => $id,
								'status' => $finance['status']
							]);
							$log_deposit_withdraw = $this->Log_deposit_withdraw_model->log_deposit_withdraw_find([
								'id' => $log_deposit_withdraw_id
							]);
							if($log_deposit_withdraw!=""){
								$this->Log_deposit_withdraw_model->log_deposit_withdraw_update([
									'id' => $log_deposit_withdraw_id,
									'description' => $log_deposit_withdraw['description']." | ทำรายการไม่สำเร็จ ไม่พบธนาคารสมาชิกในระบบ#1",
									'withdraw_status_status' => 0,
								]);
										$this->cache->file->delete('process_withdraw_'.$id);}
							echo json_encode([
								'message' => 'ทำรายการไม่สำเร็จ ไม่พบธนาคารสมาชิกในระบบ#1',
								'error' => true
							]);
						}
					}else{
						$this->Finance_model->finance_update([
							'id' => $id,
							'status' => $finance['status']
						]);
						$log_deposit_withdraw = $this->Log_deposit_withdraw_model->log_deposit_withdraw_find([
							'id' => $log_deposit_withdraw_id
						]);
						if($log_deposit_withdraw!=""){
							$this->Log_deposit_withdraw_model->log_deposit_withdraw_update([
								'id' => $log_deposit_withdraw_id,
								'description' => $log_deposit_withdraw['description']." | ทำรายการไม่สำเร็จ ไม่พบธนาคารสมาชิกในระบบ#2",
								'withdraw_status_status' => 0,
							]);
									$this->cache->file->delete('process_withdraw_'.$id);}
						echo json_encode([
							'message' => 'ทำรายการไม่สำเร็จ ไม่พบธนาคารสมาชิกในระบบ#2',
							'error' => true
						]);
					}
				}else{
					$this->Finance_model->finance_update([
						'id' => $id,
						'status' => $finance['status']
					]);

					$log_deposit_withdraw = $this->Log_deposit_withdraw_model->log_deposit_withdraw_find([
						'id' => $log_deposit_withdraw_id
					]);
					if($log_deposit_withdraw!=""){
						$this->Log_deposit_withdraw_model->log_deposit_withdraw_update([
							'id' => $log_deposit_withdraw_id,
							'description' => $log_deposit_withdraw['description']." | ทำรายการไม่สำเร็จ ไม่พบสมาชิกในระบบ",
							'withdraw_status_status' => 0,
						]);
								$this->cache->file->delete('process_withdraw_'.$id);}
					echo json_encode([
						'message' => 'ทำรายการไม่สำเร็จ ไม่พบสมาชิกในระบบ',
						'error' => true
					]);
				}
			}else{
				$log_deposit_withdraw = $this->Log_deposit_withdraw_model->log_deposit_withdraw_find([
					'id' => $log_deposit_withdraw_id
				]);
				if($log_deposit_withdraw!=""){
					$this->Log_deposit_withdraw_model->log_deposit_withdraw_update([
						'id' => $log_deposit_withdraw_id,
						'description' => $log_deposit_withdraw['description']." | ทำรายการสำเร็จ",
						'withdraw_status_status' => 1,
					]);
				}

				if($post['status'] == "3"){
					//บันทึก line notify job
					date_default_timezone_set('Asia/Bangkok');
					$account_admin = $this->Account_model->account_find([
						'id' => $_SESSION['user']['id']
					]);
					$log_line_notify_id = $this->Log_line_notify_model->log_line_notify_create([
						'type' => 2,
						'message' => "ยอดถอน ".number_format($finance['amount'],2)." บาท ยูส ".$member['username']." เวลา ".date('Y-m-d H:i:s')." ถอนโดย ".$account_admin['full_name'],
					]);

					$sum_amount_list = $this->Finance_model->sum_amount_deposit_and_withdraw(['account_list' => [$member['id']]]);
					if(array_key_exists($member['id'],$sum_amount_list)){
						$this->User_model->user_update(['sum_amount'=>$sum_amount_list[$member['id']]['sum_amount'],'id'=> $member['id']]);
					}

				}
			$this->cache->file->delete('process_withdraw_'.$id);
				echo json_encode([
					'message' => 'ทำรายการสำเร็จ',
					'result' => true
				]);
			}

		} else {
			$this->cache->file->delete('process_withdraw_'.$id);
			echo json_encode([
				'message' => 'ทำรายการไม่สำเร็จ, ไม่พบ Finance ID #'.$id,
				'error' => true
			]);
		}
	}

	public function withdraw_credit()
	{
		check_parameter([
			'account_id',
			'amount',
		], 'POST');
		$post = $this->input->post();
		$user = $this->User_model->user_find([
			'id' => $post['account_id']
		]);
		if ($user=="") {
			echo json_encode([
				'message' => 'ไม่พบข้อมูล Username นี้',
				'error' => true
			]);
			exit();
		}

		$credit_before = $this->remaining_credit($user);
		if ($post['amount']>$credit_before) {
			echo json_encode([
				'message' => "ยอดเงินคงเหลือไม่เพียงพอ : ".number_format($credit_before,2)." บาท",
				'error' => true
			]);
			exit();
		}

		/*$turnover_current = (float)$this->check_turn_before($user);
		if($turnover_current > (float)$user['turn_before']){
			$check_turn = $turnover_current-(float)$user['turn_before'];
		}else{
			$check_turn = (float)$user['turn_before'] - $turnover_current;
		}
		if ($check_turn < $user['turn_over']) {
			$turn_over = $user['turn_over']-$check_turn;
			echo json_encode([
				'message' => "เทิร์นคงเหลือ {$check_turn}/{$user['turn_over']}",
				'error' => true
			]);
			exit();
		}*/

		$turn_type = $this->Setting_model->setting_find([
			'name' => 'turn_type'
		]);
		$turn_type = $turn_type != "" ? $turn_type['value'] : 1;
		if($turn_type == "2"){
			$turnover_user = is_numeric($user['turn_over']) ? (float)$user['turn_over'] : 0.00;
			if ($credit_before<$turnover_user) {
				echo json_encode([
					'message' => "ยอดเครดิตคงเหลือต้องมากกว่าหรือเท่ากับ ".number_format($turnover_user, 2).' ฿',
					'error' => true
				]);
				exit();
			}
		}else{
			$chk_turn_all_pass = false;
			$text_error_turn_all = "";
			$turnover_data = $this->check_turn_before($user);
			$chk_take_turn_finish = 0;
			$chk_take_turn_finish_html = "";
			$chk_take_turn_finish_html_new = "";
			$chk_turn_zero_all = false;
			foreach (game_code_list() as $game_code) {
				if (
					!$chk_turn_zero_all &&
					(is_numeric($user['turn_over_' . strtolower($game_code)]) && (float)$user['turn_over_' . strtolower($game_code)] >= 1)
				) {
					$chk_turn_zero_all = true;
				}
			}
			if ($chk_turn_zero_all) {
				foreach (game_code_list() as $game_code) {
					if (!$chk_turn_all_pass) {
						if (isset($turnover_data[$game_code]) && isset($user['turn_over_' . strtolower($game_code)]) && (float)$user['turn_over_' . strtolower($game_code)] >= 0) {
							if($turn_type == "2"){
								$turnover_current = $credit_before;
							}else{
								$turnover_current = $turnover_data[$game_code]['amount'];
							}
							if ($turnover_current > (float)$user['turn_before_' . strtolower($game_code)]) {
								$check_turn = $turnover_current - (float)$user['turn_before_' . strtolower($game_code)];
							} else {
								$check_turn = 0;
							}
							if ($check_turn < $user['turn_over_' . strtolower($game_code)]) {

								$text_error_turn_all .= "<p class='mb-0'>เทิร์น " . game_code_text_list()[$game_code] . " คงเหลือ {$check_turn}/{$user['turn_over_' . strtolower($game_code)]}</p>";
								if ($check_turn  > 0 && $check_turn > $chk_take_turn_finish) {
									$chk_take_turn_finish = $check_turn;
									$chk_take_turn_finish_html = "<p class='mb-0'>เทิร์น " . game_code_text_list()[$game_code] . " คงเหลือ {$check_turn}/{$user['turn_over_' . strtolower($game_code)]}</p>";
									$chk_take_turn_finish_html_new = "<p class='mb-0 font-weight-bold text-success'>เทิร์น " . game_code_text_list()[$game_code] . " คงเหลือ {$check_turn}/{$user['turn_over_' . strtolower($game_code)]}</p>";
								}
								/*$turn_over = $user['turn_over']-$check_turn;
								echo json_encode([
									'message' => "เทิร์นคงเหลือ {$check_turn}/{$user['turn_over']}",
									'error' => true
								]);
								exit();*/
							} else {
								$turn_game_code = 'turn_over_' . strtolower($game_code);
								$chk_turn_all_pass = true;
							}
						}
					}
				}
			}

			if (!$chk_turn_all_pass && $chk_turn_zero_all) {
				echo json_encode([
					//'message' => "เทิร์นคงเหลือ {$check_turn}/{$user['turn_over']}",
					'message' => "<h5 class='text-danger font-weight-bold'>** เพียงทำเทิร์นให้ครบบางอย่างเท่านั้น</h5>" . str_replace($chk_take_turn_finish_html, $chk_take_turn_finish_html_new, $text_error_turn_all),
					'error' => true
				]);
				exit();
			}
		}


		if ($post['amount']>$credit_before) {
			echo json_encode([
				'message' => "ยอดเงินคงเหลือไม่เพียงพอ ".number_format($credit_before, 2).' ฿',
				'error' => true
			]);
			exit();
		}
		$amount_deposit = $_POST['amount'];
		$form_data = [];
		$form_data["account_agent_username"] = $user['account_agent_username'];
		$form_data["amount"] = $amount_deposit;
		$form_data = member_credit_data($form_data);

		//เพิ่ม Logs
		$transaction_message = !isset($post['transaction']) || (isset($post['transaction']) && $post['transaction'] == "Y") ? " (บันทึกลง Transaction + ปรับลด Credit)" : " (ปรับลด Credit อย่างเดียว)";
		$log_deposit_withdraw_id = $this->Log_deposit_withdraw_model->log_deposit_withdraw_create([
			'account' => $user['id'],
			'username' => $user['username'],
			'amount' => $form_data["amount"],
			'amount_before' => $credit_before,
			'type' => '2', //ถอน
			'description' => 'ลดเครดิต'.$transaction_message,
			'admin' =>$_SESSION['user']['id'],
		]);

		$response = $this->game_api_librarie->withdraw($form_data);
		if (isset($response['ref'])) {

			if(
				!isset($post['transaction']) || (isset($post['transaction']) && $post['transaction'] == "Y")
			){
				if($credit_before >= (float)$user['turn_over']){
					$this->Account_model->account_update([
						'id' => $user['id'],
						'turn_over' => 0,
						'sha1_acount' => '',
					]);
				}

				$this->Finance_model->finance_create([
					'account' => $user['id'],
					'amount' => $post['amount'],
					'bank' => $user['bank'],
					'bank_number' => $user['bank_number'],
					'bank_name' => $user['bank_name'],
					'username' => $user['username'],
					'type' => 2
				]);
			}

			$log_deposit_withdraw = $this->Log_deposit_withdraw_model->log_deposit_withdraw_find([
				'id' => $log_deposit_withdraw_id
			]);
			if($log_deposit_withdraw!=""){
				$this->Log_deposit_withdraw_model->log_deposit_withdraw_update([
					'id' => $log_deposit_withdraw_id,
					'description' => $log_deposit_withdraw['description']." | ทำรายการสำเร็จ",
				]);
			}

			echo json_encode([
				'message' => 'success',
				'result' => true
			]);
		} else {

			$log_deposit_withdraw = $this->Log_deposit_withdraw_model->log_deposit_withdraw_find([
				'id' => $log_deposit_withdraw_id
			]);
			if($log_deposit_withdraw!=""){
				$this->Log_deposit_withdraw_model->log_deposit_withdraw_update([
					'id' => $log_deposit_withdraw_id,
					'description' => $log_deposit_withdraw['description']." | ทำรายการไม่สำเร็จ",
				]);
			}

			echo json_encode([
				'message' => 'ทำรายการไม่สำเร็จ',
				'error' => true,
			]);
		}
	}

	public function check_turn_before($user)
	{
		$form_data = [];
		$form_data['username'] = $user['account_agent_username'];
		if ($user['turn_date']!="") {
			$date = new DateTime($user['turn_date']);
			$form_data['date_begin'] = $date->format('Y-m-d');
		}
		$form_data = member_turn_data($form_data);
		$turnover_amount = $this->game_api_librarie->getTurn($form_data);
		return $turnover_amount;
	}

	private function remaining_credit($user)
	{
		header('Content-Type: application/json');
		try {
			$balance_credit = $this->game_api_librarie->balanceCredit($user);
			return $balance_credit;
		} catch (\Exception $e) {
			echo json_encode([
				'message' => 'ทำรายการไม่สำเร็จ',
				'error' => true
			]);
			exit();
		}
	}

	public function withdraw_list_page_manage_transaction($account_id)
    {
        $get = $this->input->get();
        $search = $get['search']['value'];
        // $dir = $get['order'][0]['dir'];//order
        $per_page = $get['length'];//จำนวนที่แสดงต่อ 1 หน้า
        $page = $get['start'];
        $search_data = [
         'per_page' => $per_page,//left
         'page' => $page,
         'type' => 2
        ];
        if ($search!="") {
            $search_data['search'] = $search;
        }
		if(isset($get['date_start']) && isset($get['date_end'])){
			$search_data['date_start'] = $get['date_start'];
			$search_data['date_end'] = $get['date_end'];
		}
		if(isset($get['status']) && $get['status'] !== ""){
			$search_data['status'] = $get['status'];
		}
		$search_data['account'] = $account_id;
        $withdraw_count_all = $this->Finance_model->finance_count([
        	'type' => 2,
			'account' => $account_id
		]);
        $withdraw_count_search = $this->Finance_model->finance_count($search_data);
        $data = $this->Finance_model->finance_list_page($search_data);
        echo json_encode([
         "draw" => intval($get['draw']),
         "recordsTotal" => intval($withdraw_count_all),
         "recordsFiltered" => intval($withdraw_count_search),
         "data" => $data,
       ]);
    }
}
