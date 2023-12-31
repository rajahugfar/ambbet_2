<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Bank extends CI_Controller
{
	public $menu_service;
	public function __construct()
	{
		date_default_timezone_set('Asia/Bangkok');
		parent::__construct();
	}
	private function checkCanAccessMenu(){
		//if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != roleSuperAdmin()) {
		if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role'])) {
			redirect('../auth');
		}
		$this->load->library(['Menu_service']);
		if(!$this->menu_service->validate_permission_menu($this->uri)){
			redirect('../auth');
		}
	}
	private function checkOtherPageCanAccess(){
		//if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'],[roleSuperAdmin(),roleAdmin()])) {
		if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role'])) {
			redirect('../auth');
		}
		$permission_menu_role_cnt = $this->Permission_menu_role_model->permission_menu_role_cnt([
			'role_id' => $_SESSION['user']['role'],
		]);
		if($permission_menu_role_cnt >= 1){

		}else{
			redirect('../auth');
		}
	}
	public function index()
	{
		$this->checkCanAccessMenu();
		$this->load->helper('url');
		$currentURL = current_url();
		$log_page_id = $this->Log_page_model->log_page_create([
			'ip' => isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $this->input->ip_address(),
			'admin' => $_SESSION['user']['id'],
			'username' => $_SESSION['user']['username'],
			'page_name' => "ตั้งค่าธนาคาร",
			'description' => 'หน้ารายการ',
			'page_url' => $currentURL,
		]);
		$data['page'] = 'bank/bank';
		$data['bank_code_list'] = $this->Bank_model->bank_data_list();
		$this->load->view('main', $data);
	}
	public function bank_form_create()
	{
		$this->checkCanAccessMenu();
		$this->load->helper('url');
		$currentURL = current_url();
		$log_page_id = $this->Log_page_model->log_page_create([
			'ip' => isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $this->input->ip_address(),
			'admin' => $_SESSION['user']['id'],
			'username' => $_SESSION['user']['username'],
			'page_name' => "ตั้งค่าธนาคาร",
			'description' => 'หน้าสร้าง',
			'page_url' => $currentURL,
		]);
		$data['bank_data_list'] = $this->Bank_model->bank_data_list();
		$data['page'] = 'bank/bank_create';
		$this->load->view('main', $data);
	}
	public function bank_create()
	{
		$this->checkCanAccessMenu();

		if($_POST['bank_code']=='11'){
			$_POST['username']='xxxx';
			$_POST['password']='xxxx';
		}

		check_parameter([
			'bank_code',
			'account_name',
			'bank_number',
			'username',
			'password',
			'status',
			'status_withdraw',
			'api_token_1',
			'api_token_2',
			'api_token_3',
			'api_type',
		], 'POST');
		$post = $this->input->post();
		switch ($post['bank_code']) {
			case '01':
				$bank_name = "ธนาคารกรุงเทพ";
				break;
			case '02':
				$bank_name = "ธนาคารกสิกรไทย";
				break;
			case '03':
				$bank_name = "ธนาคารกรุงไทย";
				break;
			case '04':
				$bank_name = "ธนาคารทหารไทย";
				break;
			case '05':
				$bank_name = "ธนาคารไทยพาณิชย์";
				break;
			case '06':
				$bank_name = "ธนาคารกรุงศรีอยุธยา";
				break;
			case '07':
				$bank_name = "ธนาคารออมสิน";
				break;
			case '08':
				$bank_name = "ธนาคารธนชาติ";
				break;
			case '09':
				$bank_name = "ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร";
				break;
			case '10':
				$bank_name = "ทรูมันนี่วอลเล็ท";
				break;
			case '11':
				$bank_name = "ธนาคารเกียรตินาคิน จำกัด (มหาชน)";
				break;
			default :
				break;
		}
		$end_time_can_not_deposit = null;
		$start_time_can_not_deposit = null;
		if(isset($post['start_time_can_not_deposit']) || isset($post['end_time_can_not_deposit'])){
			if(
				(!empty($post['start_time_can_not_deposit']) && empty($post['end_time_can_not_deposit'])) ||
				(!empty($post['end_time_can_not_deposit']) && empty($post['start_time_can_not_deposit']))
			){
				$this->session->set_flashdata('warning', 'กรุณาระบุเวลาจาก-ถึง ที่ปิดระบบฝากออโต้');
				redirect('bank/bank_form_create');
				exit();
			}else{
				try{
					$from_time = new DateTime($post['start_time_can_not_deposit']);
					$end_date = new DateTime($post['end_time_can_not_deposit']);
					if($from_time->getTimestamp() > $end_date->getTimestamp()){
						if(in_array($from_time->format("H") ,array("23","22","21","20","19","18","17","16","15","14","13","12"))){
							$from_time = $from_time->sub(new DateInterval('P1D'));
							if($from_time->getTimestamp() > $end_date->getTimestamp()){
								$this->session->set_flashdata('warning', 'รูปแบบเวลา (จาก) ควรน้อยกว่าหรือเท่ากับรูปแบบเวลา (ถึง) ** ไม่ควรห่างกันเกิน 12 ชั่วโมง');
								redirect('bank/bank_form_create');
								exit();
							}
						}else{
							$this->session->set_flashdata('warning', 'รูปแบบเวลา (จาก) ควรน้อยกว่าหรือเท่ากับรูปแบบเวลา (ถึง) ** ไม่ควรห่างกันเกิน 12 ชั่วโมง');
							redirect('bank/bank_form_create');
							exit();
						}
					}
				}catch (Exception $ex){
					$this->session->set_flashdata('warning', 'รูปแบบเวลาที่ปิดระบบฝากออโต้ไม่ถูกต้อง ตัวอย่าง 00:00');
					redirect('bank/bank_form_create');
					exit();
				}
			}
		}
		if(isset($post['start_time_can_not_deposit'])){
			$start_time_can_not_deposit = $post['start_time_can_not_deposit'];
		}
		if(isset($post['end_time_can_not_deposit'])){
			$end_time_can_not_deposit = $post['end_time_can_not_deposit'];
		}
		$post['api_token_1'] = trim($post['api_token_1']);
		$post['api_token_2'] = trim($post['api_token_2']);
		$post['api_token_3'] = trim($post['api_token_3']);
		if(
			empty($post['api_token_1']) && in_array($post['bank_code'],["03","3","02","2","05","5","10","06","6","11"])
		){
			$text_message_token_1 = "Device ID";
			if($post['bank_code'] == "03" || $post['bank_code'] == "3"){
				$text_message_token_1 = "Account Token No";
			}else if($post['bank_code'] == "10"){
				$text_message_token_1 = "Pin";
			}else if($post['bank_code'] == "11"){
				$text_message_token_1 = "รหัสบัตรประชาชน";
			}
			$this->session->set_flashdata('warning', 'กรุณาระบุ '.$text_message_token_1);
			redirect('bank/bank_form_create');
			exit();
		}else if(
			empty($post['api_token_2']) && in_array($post['bank_code'],["03","3","02","2","05","5","10","06","6","11"])
		){
			$text_message_token_2 = "API Refresh";
			if($post['bank_code'] == "03" || $post['bank_code'] == "3"){
				$text_message_token_2 = "User Token ID";
			}else if($post['bank_code'] == "10"){
				$text_message_token_2 = "Login Token (login_token จากระบบ TMNOne)";
			}elseif($post['bank_code'] == "11"){
				$text_message_token_2 = "PIN";
			}
			$this->session->set_flashdata('warning', 'กรุณาระบุ '.$text_message_token_2);
			redirect('bank/bank_form_create');
			exit();
		}else if(
			empty($post['api_token_3']) && in_array($post['bank_code'],["03","3"])
		){
			$text_message_token_3 = "API Refresh";
			if($post['bank_code'] == "03" || $post['bank_code'] == "3"){
				$text_message_token_3 = "User Identity";
			}
			$this->session->set_flashdata('warning', 'กรุณาระบุ '.$text_message_token_3);
			redirect('bank/bank_form_create');
			exit();
		}
		if($post['promptpay_number']==''){
			$post['promptpay_number']='';
		}
		if($post['promptpay_status']==''){
			$post['promptpay_status']='';
		}
		$create = [
			'bank_name' => $bank_name,
			'bank_code' => $post['bank_code'],
			'account_name' => $post['account_name'],
			'bank_number' => $post['bank_number'],
			'username' => $post['username'],
			'password' => $post['password'],
            'promptpay_number' => $post['promptpay_number'],
            'promptpay_status' => $post['promptpay_status'],
			'status' => $post['status'],
			'check_regis' => $post['check_regis'],
			'message_can_not_deposit' => isset($post['message_can_not_deposit']) ? $post['message_can_not_deposit'] : null,
			'status_withdraw' => isset($post['status_withdraw']) ? $post['status_withdraw'] : 0,
			'start_time_can_not_deposit' => $start_time_can_not_deposit,
			'end_time_can_not_deposit' => $end_time_can_not_deposit,
			'api_token_1' => isset($post['api_token_1']) && in_array($post['bank_code'],["03","3","02","2","05","5","06","6","11"]) ? base64_encode(encrypt($post['api_token_1'],$this->config->item('secret_key_salt'))) : $post['api_token_1'],
			'api_token_2' => isset($post['api_token_2']) && in_array($post['bank_code'],["03","3","02","2","05","5","06","6","11"]) ? base64_encode(encrypt($post['api_token_2'],$this->config->item('secret_key_salt'))) : $post['api_token_2'],
			'api_token_3' => isset($post['api_token_3']) && in_array($post['bank_code'],["03","3"]) ? base64_encode(encrypt($post['api_token_3'],$this->config->item('secret_key_salt'))) : $post['api_token_3'],
			'max_amount_withdraw_auto' => isset($post['max_amount_withdraw_auto']) ? $post['max_amount_withdraw_auto'] : null,
			'api_type' => isset($post['api_type']) ? $post['api_type'] : 1,
			'auto_transfer' => isset($post['auto_transfer']) && in_array($post['bank_code'],["03","3","02","2","05","5","06","6","11"])  ? $post['auto_transfer'] : 0,
			'auto_min_amount_transfer' => isset($post['auto_min_amount_transfer']) && in_array($post['bank_code'],["03","3","02","2","05","5","06","6","11"])  ? $post['auto_min_amount_transfer'] : null,
			'auto_transfer_bank_code' => isset($post['auto_transfer_bank_code']) && in_array($post['bank_code'],["03","3","02","2","05","5","06","6","11"])  ? $post['auto_transfer_bank_code'] : null,
			'auto_transfer_bank_acc_name' => isset($post['auto_transfer_bank_acc_name']) && in_array($post['bank_code'],["03","3","02","2","05","5","06","6","11"])  ? trim($post['auto_transfer_bank_acc_name']) : null,
			'auto_transfer_bank_number' => isset($post['auto_transfer_bank_number']) && in_array($post['bank_code'],["03","3","02","2","05","5","06","6","11"])  ? trim($post['auto_transfer_bank_number']) : null,
		];
		$this->Bank_model->bank_create($create);
		$this->session->set_flashdata('toast', 'บันทึกข้อมูลเรียบร้อยแล้ว');
		redirect('bank');
	}
	public function bank_kkp_get_otp(){

		require_once FCPATH .'../lib/kkp/KkpClass.php';
		$idCard= $_POST['idCard'];
		$pin= $_POST['pin'];

		$data = array(
			"idCard" => trim($idCard),
			"pin" => trim($pin),
		);
		//print_r($data);
		$kkp = new KkpClass($data);
		$res = $kkp->RequestOTP();

		$json = array(
			'referenceNo'=>$res['result']['value']['referenceNo']
		,'verifyTransactionId'=>$res['verifyTransactionId']
		);
		/*$json = array(
					'referenceNo'=>'MTGOU'
					,'verifyTransactionId'=>"4675b063-88b3-46e7-8dcd-435a931fcd1a-4v5"
		);*/

		echo json_encode([
			'message' => 'success',
			'result' => $json
		]);
	}
	public function bank_kkp_confirm_otp(){
		$idCard= $_POST['idCard'];
		$pin= $_POST['pin'];
		$otp= $_POST['otp'];
		$verifyTransactionId= $_POST['verifyTransactionId'];
		$referenceNo= $_POST['referenceNo'];

		$data = array(
			"idCard" => trim($idCard),
			"pin" => trim($pin),
		);

		require_once FCPATH .'../lib/kkp/KkpClass.php';
		//print_r($data);
		$kkp = new KkpClass($data);
		$res = $kkp->SummitOTP($otp,$referenceNo,$verifyTransactionId);

		//$res['result']['value']=1;
		if($res['result']['value']==1){
			$kkp_login = new KkpClass($data);
			$res = $kkp_login->LoginPin();

			echo json_encode([
				'message' => 'success',
				'result' => $res
			]);
		}else{
			echo json_encode([
				'message' => 'error',
				'result' => $res
			]);
		}
		//print_r($res);
	}

	public function bank_scb_get_otp(){
		$cardId = trim($_POST["cardId"]);
		$cardType = trim($_POST["cardType"]);
		$dateOfBirth = trim($_POST["dateOfBirth"]);
		$MobilePhoneNo = trim($_POST["MobilePhoneNo"]);

		require_once FCPATH .'../lib/scb_getdevice.php';

		$scb_getdevice = new scb_getdevice();
		$res = $scb_getdevice->register_newdevice($cardId,$dateOfBirth,$MobilePhoneNo,$cardType);

		echo json_encode($res);
	}

	public function bank_scb_confirm_otp(){
		$cardId = trim($_POST["cardId"]);
		$cardType = trim($_POST["cardType"]);
		$dateOfBirth = trim($_POST["dateOfBirth"]);
		$MobilePhoneNo = trim($_POST["MobilePhoneNo"]);

		require_once FCPATH .'../lib/scb_getdevice.php';

		$scb_getdevice = new scb_getdevice();
		$res = $scb_getdevice->register_newdevice($cardId,$dateOfBirth,$MobilePhoneNo,$cardType);

		echo json_encode($res);
	}
	public function bank_list()
	{
		require_once FCPATH .'../config.php';
		$this->checkOtherPageCanAccess();
		$data = $this->Bank_model->bank_list($_GET);
		if(isset($_GET['security']) && $_GET['security'] && !empty($data)){
			foreach($data as $index => $bank){
				unset($bank['username']);
				unset($bank['password']);
				unset($bank['api_token_1']);
				unset($bank['api_token_2']);
				unset($bank['api_token_3']);
				$data[$index] = $bank;
			}

		}else{
			foreach($data as $index => $bank){
				unset($bank['api_token_1']);
				unset($bank['api_token_2']);
				unset($bank['api_token_3']);
				$data[$index] = $bank;
			}
		}
		$key_bank_number = [];
		$data_new = [];
		if(isset($_GET['group_by']) && $_GET['group_by'] == "bank_number"){
			foreach($data as $index => $bank){
				if(!array_key_exists($bank['bank_number'],$key_bank_number)){
					$key_bank_number[$bank['bank_number']] = $bank['bank_number'];
					$data_new[] = $bank;
				}
			}
		}else{
			$data_new = $data;
		}
		echo json_encode([
			'message' => 'success',
			'result' => $data_new,
			'tk_key' => API_TOKEN_KEY
		]);
	}
	public function bank_list_for_withdraw()
	{
		$this->checkOtherPageCanAccess();
		$data = $this->Bank_model->bank_list(['status'=>1,'status_withdraw' => 1,'api_type' => 1]);
		foreach($data as $index => $bank){
			unset($bank['api_token_1']);
			unset($bank['api_token_2']);
			unset($bank['api_token_3']);
			$data[$index] = $bank;
		}
		$key_bank_number = [];
		$data_new = [];
		foreach($data as $index => $bank){
			if(!array_key_exists($bank['bank_number'],$key_bank_number)){
				$key_bank_number[$bank['bank_number']] = $bank['bank_number'];
				$data_new[] = [
					'id' => $data[$index]['id'],
					'bank_number' => $data[$index]['bank_number'],
					'bank_name' => $data[$index]['bank_name'],
					'account_name' => $data[$index]['account_name'],
				];
			}
		}
		echo json_encode([
			'success' => true,
			'result' => $data_new
		]);
	}
	public function bank_status_update()
	{
		$this->checkCanAccessMenu();
		check_parameter([
			'id',
			'status'
		], 'POST');
		$post = $this->input->post();
		$update = [
			'id' => $post['id'],
			'status' => $post['status']
		];
		$this->Bank_model->bank_update($update);
		echo json_encode([
			'message' => 'เปลี่ยนแปลงสถานะเรียบร้อยแล้ว',
			'result' => true
		]);
	}
	public function bank_status_withdraw_update()
	{
		$this->checkCanAccessMenu();
		check_parameter([
			'id',
			'status_withdraw'
		], 'POST');
		$post = $this->input->post();
		$update = [
			'id' => $post['id'],
			'status_withdraw' => $post['status_withdraw']
		];
		$this->Bank_model->bank_update($update);
		echo json_encode([
			'message' => 'เปลี่ยนแปลงสถานะเรียบร้อยแล้ว',
			'result' => true
		]);
	}
	public function bank_form_update($id="")
	{
		$this->checkCanAccessMenu();
		$this->load->helper('url');
		$currentURL = current_url();
		$log_page_id = $this->Log_page_model->log_page_create([
			'ip' => isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $this->input->ip_address(),
			'admin' => $_SESSION['user']['id'],
			'username' => $_SESSION['user']['username'],
			'page_name' => "ตั้งค่าธนาคาร",
			'description' => 'หน้าแก้ไข',
			'page_url' => $currentURL,
		]);
		$data['bank'] = $this->Bank_model->bank_find([
			'id' => $id
		]);
		if ($data['bank']=="") {
			redirect('bank');
			exit();
		}
		//$data['bank']['api_token_1'] = is_null($data['bank']['api_token_1']) || in_array($data['bank']['bank_code'],["10"]) ? $data['bank']['api_token_1'] : decrypt(base64_decode($data['bank']['api_token_1']),$this->config->item('secret_key_salt'));
		//$data['bank']['api_token_2'] = is_null($data['bank']['api_token_2'])  || in_array($data['bank']['bank_code'],["10"]) ? $data['bank']['api_token_2'] : decrypt(base64_decode($data['bank']['api_token_2']),$this->config->item('secret_key_salt'));
		//$data['bank']['api_token_3'] = in_array($data['bank']['bank_code'],["03","3"]) ? decrypt(base64_decode($data['bank']['api_token_3']),$this->config->item('secret_key_salt')) : $data['bank']['api_token_3'];
		$data['bank_data_list'] = $this->Bank_model->bank_data_list();
		$data['page'] = 'bank/bank_update';
		$this->load->view('main', $data);
	}
	public function bank_update($id="")
	{
		$this->checkCanAccessMenu();
		if($_POST['bank_code']=='11'){
			$_POST['username']='xxxx';
			$_POST['password']='xxxx';
		}
		check_parameter([
			'bank_code',
			'account_name',
			'bank_number',
			'username',
			'password',
			'status',
			'status_withdraw',
			'api_token_1',
			'api_token_2',
			'api_token_3',
			'api_type',
		], 'POST');
		$post = $this->input->post();
		switch ($post['bank_code']) {
			case '01':
				$bank_name = "ธนาคารกรุงเทพ";
				break;
			case '02':
				$bank_name = "ธนาคารกสิกรไทย";
				break;
			case '03':
				$bank_name = "ธนาคารกรุงไทย";
				break;
			case '04':
				$bank_name = "ธนาคารทหารไทย";
				break;
			case '05':
				$bank_name = "ธนาคารไทยพาณิชย์";
				break;
			case '06':
				$bank_name = "ธนาคารกรุงศรีอยุธยา";
				break;
			case '07':
				$bank_name = "ธนาคารออมสิน";
				break;
			case '08':
				$bank_name = "ธนาคารธนชาติ";
				break;
			case '09':
				$bank_name = "ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร";
				break;
			case '10':
				$bank_name = "ทรูมันนี่วอลเล็ท";
				break;
			case '11':
				$bank_name = "ธนาคารเกียรตินาคิน จำกัด (มหาชน)";
				break;
			default :
				break;
		}
		$end_time_can_not_deposit = null;
		$start_time_can_not_deposit = null;
		if(isset($post['start_time_can_not_deposit']) || isset($post['end_time_can_not_deposit'])){
			if(
				(!empty($post['start_time_can_not_deposit']) && empty($post['end_time_can_not_deposit'])) ||
				(!empty($post['end_time_can_not_deposit']) && empty($post['start_time_can_not_deposit']))
			){
				$this->session->set_flashdata('warning', 'กรุณาระบุเวลาจาก-ถึง ที่ปิดระบบฝากออโต้');
				redirect('bank/bank_form_update/'.$id);
				exit();
			}else{
				try{
					$from_time = new DateTime($post['start_time_can_not_deposit']);
					$end_date = new DateTime($post['end_time_can_not_deposit']);
					if($from_time->getTimestamp() > $end_date->getTimestamp()){
						if( in_array($from_time->format("H"),array("23","22","21","20","19","18","17","16","15","14","13","12"))){
							$from_time = $from_time->sub(new DateInterval('P1D'));
							if($from_time->getTimestamp() > $end_date->getTimestamp()){
								$this->session->set_flashdata('warning', 'รูปแบบเวลา (จาก) ควรน้อยกว่าหรือเท่ากับรูปแบบเวลา (ถึง) ** ไม่ควรห่างกันเกิน 12 ชั่วโมง');
								redirect('bank/bank_form_update/'.$id);
								exit();
							}
						}else{
							$this->session->set_flashdata('warning', 'รูปแบบเวลา (จาก) ควรน้อยกว่าหรือเท่ากับรูปแบบเวลา (ถึง) ** ไม่ควรห่างกันเกิน 12 ชั่วโมง');
							redirect('bank/bank_form_update/'.$id);
							exit();
						}
					}
				}catch (Exception $ex){
					$this->session->set_flashdata('warning', 'รูปแบบเวลาที่ปิดระบบฝากออโต้ไม่ถูกต้อง ตัวอย่าง 00:00');
					redirect('bank/bank_form_update/'.$id);
					exit();
				}
			}
		}
		if(isset($post['start_time_can_not_deposit'])){
			$start_time_can_not_deposit = $post['start_time_can_not_deposit'];
		}
		if(isset($post['end_time_can_not_deposit'])){
			$end_time_can_not_deposit = $post['end_time_can_not_deposit'];
		}
		if($post['promptpay_number']==''){
			$post['promptpay_number']='';
		}
		if($post['promptpay_status']==''){
			$post['promptpay_status']='';
		}
		$post['api_token_1'] = trim($post['api_token_1']);
		$post['api_token_2'] = trim($post['api_token_2']);
		$post['api_token_3'] = trim($post['api_token_3']);
		$update = [
			'bank_code' => $post['bank_code'],
			'bank_name' => $bank_name,
			'account_name' => $post['account_name'],
			'bank_number' => $post['bank_number'],
			'username' => $post['username'],
			'password' => $post['password'],
            'promptpay_number' => $post['promptpay_number'],
            'promptpay_status' => $post['promptpay_status'],
			'id' => $id,
			'status' => $post['status'],
			'check_regis' => $post['check_regis'],
			'status_withdraw' => isset($post['status_withdraw']) ? $post['status_withdraw'] : 0,
			'message_can_not_deposit' => isset($post['message_can_not_deposit']) ? $post['message_can_not_deposit'] : null,
			'start_time_can_not_deposit' => $start_time_can_not_deposit,
			'end_time_can_not_deposit' => $end_time_can_not_deposit,
			//'api_token_1' => isset($post['api_token_1']) && in_array($post['bank_code'],["03","3","02","2","05","5"]) ? base64_encode(encrypt($post['api_token_1'],$this->config->item('secret_key_salt'))) : $post['api_token_1'],
			//'api_token_2' => isset($post['api_token_2']) && in_array($post['bank_code'],["03","3","02","2","05","5"])  ? base64_encode(encrypt($post['api_token_2'],$this->config->item('secret_key_salt'))) : $post['api_token_2'],
			//'api_token_3' => isset($post['api_token_3']) && in_array($post['bank_code'],["03","3"])  ? base64_encode(encrypt($post['api_token_3'],$this->config->item('secret_key_salt'))) : $post['api_token_3'],
			'max_amount_withdraw_auto' => isset($post['max_amount_withdraw_auto']) ? $post['max_amount_withdraw_auto'] : null,
			'api_type' => isset($post['api_type']) ? $post['api_type'] : 1,
			'auto_transfer' => isset($post['auto_transfer']) && in_array($post['bank_code'],["03","3","02","2","05","5","06","6","11"])  ? $post['auto_transfer'] : 0,
			'auto_min_amount_transfer' => isset($post['auto_min_amount_transfer']) && in_array($post['bank_code'],["03","3","02","2","05","5","06","6","11"])  ? $post['auto_min_amount_transfer'] : null,
			'auto_transfer_bank_code' => isset($post['auto_transfer_bank_code']) && in_array($post['bank_code'],["03","3","02","2","05","5","06","6","11"])  ? $post['auto_transfer_bank_code'] : null,
			'auto_transfer_bank_acc_name' => isset($post['auto_transfer_bank_acc_name']) && in_array($post['bank_code'],["03","3","02","2","05","5","06","6","11"])  ? trim($post['auto_transfer_bank_acc_name']) : null,
			'auto_transfer_bank_number' => isset($post['auto_transfer_bank_number']) && in_array($post['bank_code'],["03","3","02","2","05","5","06","6","11"])  ? trim($post['auto_transfer_bank_number']) : null,
		];
		if(isset($post['api_token_1']) && !empty($post['api_token_1'])){
			$update['api_token_1'] = isset($post['api_token_1']) && in_array($post['bank_code'],["03","3","02","2","05","5","06","6","11"]) ? base64_encode(encrypt($post['api_token_1'],$this->config->item('secret_key_salt'))) : $post['api_token_1'];
		}
		if(isset($post['api_token_2']) && !empty($post['api_token_2'])){
			$update['api_token_2'] = isset($post['api_token_2']) && in_array($post['bank_code'],["03","3","02","2","05","5","06","6","11"]) ? base64_encode(encrypt($post['api_token_2'],$this->config->item('secret_key_salt'))) : $post['api_token_2'];
		}
		if(isset($post['api_token_3']) && !empty($post['api_token_3'])){
			$update['api_token_3'] = isset($post['api_token_3']) && in_array($post['bank_code'],["03","3"]) ? base64_encode(encrypt($post['api_token_3'],$this->config->item('secret_key_salt'))) : $post['api_token_3'];
		}
		$this->Bank_model->bank_update($update);
		$this->session->set_flashdata('toast', 'แก้ไขข้อมูลเรียบร้อยแล้ว');
		redirect('bank');
	}
	public function bank_delete($id = "")
	{
		$this->checkCanAccessMenu();
		check_parameter([], 'POST');
		$update = [
			'id' => $id,
			'deleted' => 1
		];
		$this->Bank_model->bank_update($update);
		echo json_encode([
			'message' => 'ลบข้อมูลเรียบร้อยแล้ว',
			'result' => true
		]);
	}
}
