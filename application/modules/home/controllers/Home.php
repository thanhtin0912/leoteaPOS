<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MX_Controller {

	function __construct(){
		parent::__construct();
		$this->load->helper('language');
		$this->lang->load('general');
		$this->load->model('home/Home_model','home');
		$this->load->library('Discord');
		$this->load->library('session');
		$this->load->helper('cookie');
		$token = get_cookie('remember_token');
		$user = $this->home->checkCookie($token);
		if (!empty($user) && $token!=NULL) {
			if(!$this->session->userdata('userLogin')) {
			// Tái tạo session
				$this->session->set_userdata('userLogin', $user[0]);
			}
		} else {
			$this->session->unset_userdata('userLogin');
			$this->session->unset_userdata('cart_products');
			delete_cookie('remember_token');
		}
	}
	
	/*------------------------------------ API ------------------------------------*/
	public function index()
	{
		if ($this->check_online_connection()) {
			$this->home->sync_pending_orders();
		}
		$data['info'] = $this->home->getInfoSite();
		$data['sales'] = $this->home->getProductsSales();
		$data['cates'] = $this->home->getCategories('PRODUCT');
		$data['products'] = $this->getAllProduct();
		$data['banner'] = $this->home->getBanner();
		$data["countCart"]=$this->countSessionCart();
		$data["countHold"] = $this->session->userdata('hold_orders') ? count($this->session->userdata('hold_orders')) : 0;
		//
		$this->template->write_view('content', 'index', $data);
		$this->template->render();
	}

	public function checkout()
	{
		$cart_products = $this->session->userdata('cart_products');
		if($cart_products == NULL){
			header('Location: '.PATH_URL);
		} else {
			$info = $this->session->userdata('userLogin');
			$data['info'] = $this->home->getInfoSite();
			$data['store'] = $this->home->getInfoStore($info->storeId);
			$data['cart'] =$this->getListCart();
			$data['countCart'] = $this->countSessionCart();
			$data['coupons'] = $this->home->getCoupons();
			if($data['coupons']) {
				foreach ($data['coupons'] as $key => $v) {
					if (mb_substr($v->code, 0, 1, 'UTF-8') === 'T') {
						$suffix = mb_substr($v->code, 0, 2);
						$cac_thu_trong_tuan = [
							1 => 'T2',
							2 => 'T3',
							3 => 'T4',
							4 => 'T5',
							5 => 'T6',
							6 => 'T7',
							7 => 'CN'
						];
						$thu_hien_tai = $cac_thu_trong_tuan[date('N')];
						if ($suffix != $thu_hien_tai) {
							unset($data['coupons'][$key]);
						}
					}
				}
			}
			$this->template->write_view('content', 'checkout', $data);
			$this->template->render();
		}
		//
		
	}
	
	public function syncProduct()
	{
		$tablesToSync = ['users','toppings','stores','products','printer','categories', 'coupons'];
		$DB_online = @$this->load->database('online', TRUE);
		$localDB = $this->db; // Kết nối local mặc định

		foreach ($tablesToSync as $table) {
			// 1. Lấy dữ liệu từ Live
			$query = $DB_online->get($table);
			$data = $query->result_array();

			if (!empty($data)) {
				// 2. Xóa dữ liệu cũ ở Local (để tránh trùng primary key)
				$localDB->truncate($table);
				$localDB->insert_batch($table, $data);
			}
		}
		$token = get_cookie('remember_token');
		if($token) {
			$info = $this->session->userdata('userLogin');
			$this->db->where('phone',$info->phone);
			$this->db->update('users', array('session' => $token));
		}
		$data = array(
			'status'=>true,
			'key' => $this->security->get_csrf_hash(),
		);
		
		return_json($data);
		//
	}

	public function delivery()
	{
		if ($this->check_online_connection()) {
			// Đồng bộ đơn hàng đang chờ hủy từ server online về local
			$this->home->sync_orders_verify();

		}
		$data['orders'] = $this->home->getListOrdersToCancel();
		$data['banking'] = $this->home->getListOrdersToPaymentBanking();
		$data['info'] = $this->home->getInfoSite();
		$data['cart'] =$this->getListCart();
		$data['countCart'] = $this->countSessionCart();
		$this->template->write_view('content', 'delivery', $data);
		$this->template->render();
		//
	}

	public function shiftIn()
	{
		$data['info'] = $this->home->getInfoSite();
		$data['cart'] =$this->getListCart();
		$data['countCart'] = $this->countSessionCart();
		$data['checkShift'] = $this->home->checkExsitShiftofDay();
		$lastShift = $this->home->getLastShift();
		if ($lastShift) {
			$data['lastShiftSizeCupsAmount'] = $lastShift[0]->size_cups ? unserialize($lastShift[0]->size_cups) : [];
		} else {
			$data['lastShiftSizeCupsAmount'] = null;
		}
		$data['sizes'] = $this->home->getCategories('PRODUCTSIZE');
		$this->template->write_view('content', 'shift_in', $data);
		$this->template->render();
		//
	}

	public function cancelSingleTransaction()
	{
		if ($this->check_online_connection()) {
			$this->home->sync_all_cancel_single_orders();
		}
		$data['info'] = $this->home->getInfoSite();
		$data['cart'] =$this->getListCart();
		$data['countCart'] = $this->countSessionCart();
		$data['checkShift'] = $this->home->checkExsitShiftofDay();
		$data['sizes'] = $this->home->getCategories('PRODUCTSIZE');
		$data['transactionNotVerify'] = false;
		if ($data['checkShift']) {
			$data['transactionNotVerify'] = $this->home->getOrderToCancelSingleTransaction($data['checkShift'][0]->name, $data['checkShift'][0]->from);
		}
		$this->template->write_view('content', 'cancel_single_transaction', $data);
		$this->template->render();
		//
	}
	public function saveCancelSingleOrder() {
		if (!$this->check_online_connection()) {
			$data = array(
				'status'=> false,
				'mes' => 'Tính năng này chỉ áp dụng khi online.',
				'key' => $this->security->get_csrf_hash(),
			);
			return_json($data);
			exit();
		}
		$sizeCups = array();
		$allSizes = $this->input->post('dataSizes');
		foreach ($allSizes as $key => $value) {
			$sizeIn = new stdClass();
			$sizeIn->name = $key;
			$sizeIn->in = $value;
			$sizeCups[] = $sizeIn;
		}

		$amount = $this->input->post('amount');
		$note = $this->input->post('note');
		$size_cups = serialize($sizeCups);

		$req = $this->home->saveCancelSingleOrder($size_cups, $amount, $note);
		
		$data = array(
			'status'=>false,
			'key' => $this->security->get_csrf_hash(),
		);
		
		if($req) {
			$data = array(
				'status'=>true,
				'key' => $this->security->get_csrf_hash(),
			);
		}
		
		return_json($data);
	}
	
	function checkIn(){
		$sizeCups = array();
		foreach ($this->input->post('dataSizes') as $key => $value) {
			$sizeIn = new stdClass();
			$sizeIn->name = $key;
			$sizeIn->in = $value;
			$sizeCups[] = $sizeIn;
		}
		$thungan = $this->input->post('user');
		$size_cups = serialize($sizeCups);

    	$req = $this->home->checkinShiftDay($thungan, $size_cups);
		// 1. Lấy data từ session ra
		$this->session->set_userdata('staffName', $_POST["user"]);
		$data = array(
			'status'=>false,
			'key' => $this->security->get_csrf_hash(),
		);
		if($req) {
			$data = array(
				'status'=>true,
				'key' => $this->security->get_csrf_hash(),
			);
		}
		return_json($data);
	}


	public function shiftOut()
	{
		if ($this->check_online_connection()) {
			$this->home->sync_all_cancel_single_orders();
		}
		$data['info'] = $this->home->getInfoSite();
		$data['cart'] =$this->getListCart();
		$data['countCart'] = $this->countSessionCart();
		$data['checkShift'] = $this->home->checkExsitShiftofDay();
		$data['cups_sizes_in'] = $data['checkShift'] && $data['checkShift'][0]->size_cups ? unserialize($data['checkShift'][0]->size_cups) : [];
		$this->template->write_view('content', 'shift_out', $data);
		$this->template->render();
		//
	}
	function checkOutShift(){
		$shift = $this->home->getShiftofDay($_POST["id"], 0);
		$salesShiftCash = $this->home->getTotalRevenueShift($shift[0]->from, date('Y-m-d H:i:s',time()), $shift[0]->user, 1);
		$salesShiftBanking = $this->home->getTotalRevenueShift($shift[0]->from, date('Y-m-d H:i:s',time()), $shift[0]->user, 2);
		$lastOrder = $this->home->getLastOrderShift($shift[0]->from, date('Y-m-d H:i:s',time()), $shift[0]->user);
		
		$data_cups_in = $this->input->post('data_cups_in');
		$data_cups_out = $this->input->post('data_cups_out');
		$new_size_cups = array();
		$size_cups = unserialize($shift[0]->size_cups);
		$listSize = $this->home->getCategories('PRODUCTSIZE');
		$dataOrder = $this->home->getTotalOrderShift($shift[0]->from, $shift[0]->to, $shift[0]->user);
		$sizeCounter = $this->countCupBySize($dataOrder, $listSize);
		$totalCancelSingleOrders = $this->home->getTotalCancelSingleOrders($shift[0]->from, $shift[0]->to, $shift[0]->user);
		$size_cups_cancel = array();
		$total_cancel_amount_price = 0;
		if ($totalCancelSingleOrders) {
			foreach ($totalCancelSingleOrders as $cancel) {
				$cancel_sizes = array();
				if (isset($cancel->amount_sizes) && $cancel->amount_sizes !== '') {
					$cancel_sizes = @unserialize($cancel->amount_sizes);
				}

				if (is_object($cancel_sizes)) {
					$cancel_sizes = array($cancel_sizes);
				}

				if (!is_array($cancel_sizes)) {
					continue;
				}

				foreach ($cancel_sizes as $cancel_size) {
					$cancel_size_name = '';
					$cancel_size_qty = 0;

					if (is_object($cancel_size)) {
						$cancel_size_name = isset($cancel_size->name) ? trim((string)$cancel_size->name) : '';
						$cancel_size_qty = isset($cancel_size->in) && is_numeric($cancel_size->in) ? (int)$cancel_size->in : 0;
					} elseif (is_array($cancel_size)) {
						$cancel_size_name = isset($cancel_size['name']) ? trim((string)$cancel_size['name']) : '';
						$cancel_size_qty = isset($cancel_size['in']) && is_numeric($cancel_size['in']) ? (int)$cancel_size['in'] : 0;
					}

					if ($cancel_size_name === '' || $cancel_size_qty <= 0) {
						continue;
					}

					if (!isset($size_cups_cancel[$cancel_size_name])) {
						$size_cups_cancel[$cancel_size_name] = 0;
					}

					$size_cups_cancel[$cancel_size_name] += $cancel_size_qty;
					$total_cancel_amount_price += $cancel->amount_price;
				}
			}
		}

		foreach ($size_cups as $size) {
			$size_name = $size->name;
			$size_sale_count = 0;
			$size_cancel_count = 0;
			if (isset($size->name) && $size->name === 'L-Latte') {
				$size_sale_count_l = isset($sizeCounter['L']) ? $sizeCounter['L'] : 0;
				$size_sale_count_latte = isset($sizeCounter['Latte']) ? $sizeCounter['Latte'] : 0;
				$size_sale_count = $size_sale_count_l + $size_sale_count_latte;
			} else {
				$size_sale_count = isset($sizeCounter[$size_name]) ? $sizeCounter[$size_name] : 0;			
			}
			// tổng số ly xin hủy
			$size_cancel_count = isset($size_cups_cancel[$size_name]) ? $size_cups_cancel[$size_name] : 0;
			$size_in = isset($data_cups_in[$size_name]) ? $data_cups_in[$size_name] : 0;
			$size_out = isset($data_cups_out[$size_name]) ? $data_cups_out[$size_name] : 0;
			$size_diff = $size_in - $size_out;
			$check_sale_count = $size_diff - ($size_sale_count - $size_cancel_count);
			$new_size_cups[] = (object)[
				'name' => $size_name,
				'in' => $size_in,
				'out' => $size_out,
				'diff' => $size_diff,
				'sale' => $size_sale_count,
				'cancel' => $size_cancel_count,
				'check_sale' => $check_sale_count
			];				
		}

 		$data=array(
			"to"=> date('Y-m-d H:i:s',time()),
			"sales"=> $salesShiftCash + $salesShiftBanking,
			"cash"=> $salesShiftCash,
			"spent"=> $total_cancel_amount_price,
			"banking"=> $salesShiftBanking,
			"size_cups"=> serialize($new_size_cups),
			"updated"=> date('Y-m-d H:i:s',time())
		);
    	$req = $this->home->updateShiftDay($_POST["id"], $data);
		$encoded = short_encode($_POST["id"]);
		if ($req) {
			$printTem = $this->home->getPrinter($shift[0]->store,'TEM');
			if($printTem) {
				try {
					@$this->printTemCheckout($printTem[0]->ip,$shift[0], $lastOrder[0]->orderId);
				} catch (Exception $e) {
					// Ghi log lỗi in nhưng không làm dừng chương trình
					log_message('error', 'Lỗi in Tem: ' . $e->getMessage());
				}
			}
			// Gửi thông tin kết ca lên Discord
			$nv = $shift[0]->name;
			$storename = $shift[0]->storeName;
			$from = $shift[0]->from;
			$to = date('Y-m-d H:i:s');
			$url = PATH_URL . "bao-cao-ca-lam-viec?id=" . $encoded;
			$tr = "**Link báo cáo kết ca theo của hàng - " . $to . "!**\n"
			. "+++++++++++++++++++++++++++++++++\n"
			. "NV: " . $nv . " - CH: " . $storename . "\n"
			. "Giờ vào: " . $from . "\n"
			. "Giờ ra: " . $to . "\n"
			. "+++++++++++++++++++++++++++++++++\n"
			// Thêm link kiểu Markdown Discord
			. $url." \n"
			. "+++++++++++++++++++++++++++++++++";
			$this->discord->sendLinkReport($tr);
		}

		// Viết sao hiển thị vậy, rất dễ quản lý
		$tr_diff_size ='Không có sự khác biệt về size ly.';
		foreach ($new_size_cups as $size) {
			if($size->check_sale != 0) {
				$text = $size->check_sale > 0 ? " (Thiếu)" : " (Thừa)";
				$tr_diff_size .= "Size: " . $size->name . " - Vào: " . $size->in . " - Ra: " . $size->out . " - Xuất: " . $size->diff . " - Bán: " . $size->sale . " - Xin hủy: " . $size->cancel . " - Kiểm tra: " . abs($size->check_sale) . $text . "\n";
			}
		}
		
		$tr_size = "**Báo cáo kết ca THEO SIZE LY- " . date('Y-m-d H:i:s',time()) . "!**\n"
			. "+++++++++++++++++++++++++++++++++\n"
			. "NV: " . $nv . " - CH: " . $storename . "\n"
			. "Giờ vào: " . $from . "\n"
			. "Giờ ra: " . $to . "\n"
			. "-----------------------------\n"
			. $tr_diff_size
			. "+++++++++++++++++++++++++++++++++";
		
		$this->discord->sendDiffSizeinShift($tr_size);
		

		$data = array( 
			'status'=>false,
			'key' => $this->security->get_csrf_hash(),
		);
		$this->session->unset_userdata('staffName');
		if ($this->check_online_connection()) {
			$this->home->sync_pending_shift();
		} else {
			log_message('error', 'Server Online không phản hồi sau 2s, bỏ qua đồng bộ.');
		}

		if($req) {
			$data = array(
				'status'=>true,
				'id'=> $encoded,
				'key' => $this->security->get_csrf_hash(),
			);
		}
		return_json($data);
	}

	public function printTemCheckout($ip,$data, $code="") {
		
        $this->load->library('TemPrinter', ['ip' => $ip, 'port' => 9100]);
		$now = date('Y-m-d H:i:s',time());
		//code in máy in tem phải đúng cấu trúc
		$commands = '';
		$commands .= "SIZE 53 mm,33 mm\n";
		$commands .= "GAP 2 mm,0 mm\n";
		$commands .= "CLS\n";
		//
		//
		$commands .= 'TEXT 15,40,"3",0,1,1,"CH:'.$data->storeName."\" \n";
		$commands .= 'TEXT 15,80,"3",0,1,1,"NV: '.$data->name."\" \n";
		//
		$commands .= 'TEXT 15,120,"2",0,1,1,"Vao: '.$data->from."\" \n";
		$commands .= 'TEXT 15,150,"2",0,1,1,"Ra:  '.$now."\" \n";
		$commands .= 'TEXT 15,180,"2",0,1,1,"'.$code."\" \n";
		$commands .= "PRINT 1\n";
		try {
			$this->temprinter->print($commands);
		} catch (Exception $e) {
			echo "Lỗi khi in: ".$e->getMessage();
		}

		$this->temprinter->close();
    }

	public function reportShift()
	{
		$key = short_decode($_GET["id"]); // Giải mã bằng chính hàm đó
		$shift = $this->home->getShiftofDay($key, 0);
		$data['sizes'] = $this->home->getCategories('PRODUCTSIZE');
		$data['res'] = false;
		if($_GET["id"] && $shift){
			$data['salesShift']= $shift[0]->cash - $shift[0]->spent; // tổng tiền mặt thu đc
			$data['res'] = $shift;
		}
		$this->load->view('report-shift', $data);
		//
	}
	function updateCheckoutShift(){
		$money_data = $this->input->post('money_data');
		if (!is_array($money_data)) {
			$money_data = []; // Đảm bảo luôn là mảng trước khi serialize
		}
		$data=array(
			"report"=> serialize($money_data),
			"actual"=> $_POST['actual'],
			"tip"=> $_POST['tip'],
			"spentNote"=> $_POST['spentNote'],
			"updated"=> date('Y-m-d H:i:s',time())
		);
    	$req = $this->home->updateShiftDay($_POST["id"], $data);
		$data = array(
			'status'=>false,
			'key' => $this->security->get_csrf_hash(),
		);
		if($req) {
			$s = $this->home->getShiftofDay($_POST["id"], 0);
			if ($s) {
				$now = date('Y-m-d H:i:s');
				$diff = ($s[0]->actual + $s[0]->spent - $_POST['tip'] - $s[0]->cash);
				$sales = number_format($s[0]->sales);
				$actual = number_format($s[0]->actual);
				$cash = number_format($s[0]->cash);
				$banking = number_format($s[0]->banking);
				$tip = number_format($s[0]->tip);
				$gio_vao = date('H:i:s', strtotime($s[0]->from));
				$gio_ra  = date('H:i:s', strtotime($s[0]->to));
				$chi = number_format($s[0]->spent);
				$note = $_POST['spentNote'];

				// Viết sao hiển thị vậy, rất dễ quản lý
                $tr = "**Báo cáo kết ca - " . $now . "!**\n"
                    . "-----------------------------\n"
                    . "NV: " . $s[0]->name . " - CH: " . $s[0]->storeName . "\n"
                    . "CA: " . $gio_vao . " - " . $gio_ra . "\n"
                    . "-----------------------------\n"
					. "Chi: " . $chi . " - Nội dung: " . $note . "\n"
					. "TM: " . $cash . " - CK: " . $banking . "\n"
                    . "TN: " . $actual . " - DT: " . $sales . "\n"
					. "Tip: " . $tip . "\n"
                    . "LTM: " . number_format($diff) . "\n"
                    . "-----------------------------";
			
				$this->discord->sendsms($tr);
				$data=array(
					"diff" => $diff,
					"updated"=> date('Y-m-d H:i:s',time())
				);
				$this->home->updateShiftDay($_POST["id"], $data);
				if ($this->check_online_connection()) {
					$this->home->sync_pending_shift();
				}
			}
			$data = array(
				'status'=>true,
				'key' => $this->security->get_csrf_hash(),
			);
		}
		return_json($data);
	}

	private function countCupBySize($dataOrder, $listSize){
		$counter = $this->buildEmptySizeCounter($listSize);

		if(!$dataOrder){
			return $counter;
		}

		foreach($dataOrder as $order){
			if(!isset($order->detailcart) || $order->detailcart === ''){
				continue;
			}

			$cartDetails = @unserialize($order->detailcart);
			if($cartDetails === false && $order->detailcart !== 'b:0;'){
				continue;
			}

			if(is_object($cartDetails)){
				$cartDetails = array($cartDetails);
			}

			if(!is_array($cartDetails)){
				continue;
			}

			foreach($cartDetails as $item){
				$isCupCustomer = 0;
				if(is_object($item) && isset($item->isCupCustomer)){
					$isCupCustomer = (int)$item->isCupCustomer;
				}elseif(is_array($item) && isset($item['isCupCustomer'])){
					$isCupCustomer = (int)$item['isCupCustomer'];
				}

				if($isCupCustomer === 1){
					continue;
				}

				$sizeName = '';
				// if isCupCustomer là 1 thì không tính size ly vào báo cáo kết ca
				if(is_object($item) && isset($item->size)){
					$sizeName = trim((string)$item->size);
				}elseif(is_array($item) && isset($item['size'])){
					$sizeName = trim((string)$item['size']);
				}

				if($sizeName === ''){
					continue;
				}

				$amount = 1;
				if(is_object($item) && isset($item->amount) && is_numeric($item->amount)){
					$amount = (int)$item->amount;
				}elseif(is_array($item) && isset($item['amount']) && is_numeric($item['amount'])){
					$amount = (int)$item['amount'];
				}

				if($amount < 0){
					$amount = 0;
				}

				if(array_key_exists($sizeName, $counter)){
					$counter[$sizeName] += $amount;
				}
			}
		}

		return $counter;
	}

	private function buildEmptySizeCounter($listSize){
		$counter = array();
		if($listSize){
			foreach($listSize as $size){
				$sizeName = isset($size->name) ? trim((string)$size->name) : '';
				if($sizeName !== ''){
					$counter[$sizeName] = 0;
				}
			}
		}

		return $counter;
	}

	public function cancelOrder()
	{
		$data['info'] = $this->home->getInfoSite();
		$data['cart'] =$this->getListCart();
		$data['countCart'] = $this->countSessionCart();
		$this->template->write_view('content', 'cancel_order', $data);
		$this->template->render();
		//
	}

	public function viewCancelOrder($link)
	{
		$orderCode = urldecode($link);
		$data['res'] = false;
		$order = $this->home->getOrder($orderCode);
		if($order){
			$data['res'] = $order;
		}
		$this->load->view('report-verify-order', $data);
		//
	}

	public function verifyCancelOrder() {
		if ($_POST["id"]) {
			$this->db->where('orderId',$_POST["id"]);
			$this->db->where('phone',$_POST["account"]);
			$dataUpdate = array(
				'status' => 0,
				'isVerify' => 0,
				"updated"=> date('Y-m-d H:i:s',time())
			);
			if($this->db->update('orders', $dataUpdate)){
				$event = array(
					'name' => 'HỦY bill',
					'orderCode' => $_POST["id"],
					'user' => $_POST["account"],
					'isVerify' => 1,
					'verifyBy' => 'admin',
					'created' => date('Y-m-d H:i:s',time())
				);
				$this->db->insert('order_events', $event);
				$data = array(
					'status'=>true,
					'key' => $this->security->get_csrf_hash(),
				);
			} else {
				$data = array(
					'status'=>false,
					'key' => $this->security->get_csrf_hash(),
				);
			}
		} else {
			$data = array(
				'status'=>false,
				'key' => $this->security->get_csrf_hash(),
			);
		}

		return_json($data);
	}
	public function updateCancelOrder()
	{
		if (!$this->check_online_connection()) {
			$data = array(
				'status'=> false,
				'mes' => 'Tính năng này chỉ áp dụng khi online.',
				'key' => $this->security->get_csrf_hash(),
			);
			return_json($data);
			exit();
		}

		if ($this->input->post('orderCode')) {
			$lastNo = strtoupper(substr($this->input->post('orderCode'), 0, -4));
			$checkOrder = $this->home->getOrderCode($lastNo);
			$type = $this->input->post('type');
			if($checkOrder) {
				if ($type === 'in') {
					$res = $checkOrder[0];
					$info = $this->session->userdata('userLogin');
					try {
						$printBill = $this->home->getPrinter($info->storeId,'BILL');
						if($printBill) {
							@$this->printBill($printBill[0]->ip, $this->input->post('orderCode'), $res);
						}
					} catch (Exception $e) {
						// Ghi log lỗi in nhưng không làm dừng chương trình
						log_message('error', 'Lỗi in Bill: ' . $e->getMessage());
						$data = array(
							'status'=>false,
							'key' => $this->security->get_csrf_hash(),
						);
					}
					$data = array(
						'status'=>true,
						'key' => $this->security->get_csrf_hash(),
					);
				}
				$db_online = $this->load->database('online', TRUE);
				if ($type === 'huy') {
					$this->db->where('id',$checkOrder[0]->id);
					$note = $this->input->post('note');
					$dataUpdate = array(
						'note' => $note,
						'isVerify' => 1,
						"updated"=> date('Y-m-d H:i:s',time())
					);
					if($this->db->update('orders', $dataUpdate)){
						$total = $checkOrder[0]->grandtotal;
						$nv = $checkOrder[0]->fullname;
						$tk = $checkOrder[0]->phone;
						$created = $checkOrder[0]->created;
						$now = date('Y-m-d H:i:s');
						$url = "https://61579.net/xac-nhan-huy-hoa-don/" . $lastNo;
						// Viết sao hiển thị vậy, rất dễ quản lý
						$tr = "**Yêu cầu hủy bill - " . $lastNo . " - " . $now . "!**\n"
						. "+++++++++++++++++++++++++++++++++\n"
						. "NV: " . $nv . " - TK: " . $tk . "\n"
						. "Lý do: " . $note . "\n"
						. "Tổng: " . $total . "\n"
						. "Ngày in: " . $created . "\n"
						. "+++++++++++++++++++++++++++++++++\n"
						. " [Xác nhận hủy bill: " . $lastNo . "](" . $url . ") \n" // Thêm link kiểu Markdown Discord
						. "+++++++++++++++++++++++++++++++++";
						$this->discord->sendsmsCancel($tr);

						$data = array(
							'status'=>true,
							'key' => $this->security->get_csrf_hash(),
						);
						return_json($data);
						// Kiểm tra xem record này có tồn tại trên server online không
						$check_online = $db_online->get_where('orders', array('orderId' => $lastNo))->row();
						if ($check_online) {
							// Nếu có thì tiến hành update online
							$db_online->where('orderId', $lastNo);
							$db_online->update('orders', $dataUpdate);
						}
						exit();
					}


				}
				if ($type === 'ck') {
					$this->db->where('orderId',$lastNo);
					$note = $this->input->post('note');
					$dataUpdate = array(
						'payment' => 2,
						'note' => $note,
						"updated"=> date('Y-m-d H:i:s',time())
					);
					if($this->db->update('orders', $dataUpdate)){
						$nv = $checkOrder[0]->fullname;
						$tk = $checkOrder[0]->phone;
						$created = $checkOrder[0]->created;
						$now = date('Y-m-d H:i:s');
						// Viết sao hiển thị vậy, rất dễ quản lý
						$tr = "**Thay đổi Bill từ TM sang CK - " . $lastNo . " - " . $now . "!**\n"
						. "+++++++++++++++++++++++++++++++++\n"
						. "NV: " . $nv . " - TK: " . $tk . "\n"
						. "Lý do: " . $note . "\n"
						. "Ngày in: " . $created . "\n"
						. "+++++++++++++++++++++++++++++++++\n";
						$this->discord->sendsmsCancel($tr);

						$data = array(
							'status'=>true,
							'mes' => 'Đã đổi thông tin thanh toán từ TM sang CK.',
							'key' => $this->security->get_csrf_hash(),
						);
						$res = $checkOrder[0];
						$info = $this->session->userdata('userLogin');	
						// Kiểm tra xem record này có tồn tại trên server online không
						$check_online = $db_online->get_where('orders', array('orderId' => $lastNo))->row();
						if ($check_online) {
							// Nếu có thì tiến hành update online
							$db_online->where('orderId', $lastNo);
							$db_online->update('orders', $dataUpdate);
						}
						return_json($data);
						exit();
					}
				}
				if ($type === 'tm') {
					$this->db->where('orderId',$lastNo);
					$note = $this->input->post('note');
					$dataUpdate = array(
						'payment' => 1,
						'note' => $note,
						"updated"=> date('Y-m-d H:i:s',time())
					);
					if($this->db->update('orders', $dataUpdate)){
						$nv = $checkOrder[0]->fullname;
						$tk = $checkOrder[0]->phone;
						$created = $checkOrder[0]->created;
						$now = date('Y-m-d H:i:s');
						// Viết sao hiển thị vậy, rất dễ quản lý
						$tr = "**Thay đổi Bill từ CK sang TM - " . $lastNo . " - " . $now . "!**\n"
						. "+++++++++++++++++++++++++++++++++\n"
						. "NV: " . $nv . " - TK: " . $tk . "\n"
						. "Lý do: " . $note . "\n"
						. "Ngày in: " . $created . "\n"
						. "+++++++++++++++++++++++++++++++++\n";
						$this->discord->sendsmsCancel($tr);

						$data = array(
							'status'=>true,
							'mes' => 'Đã đổi thông tin thanh toán từ CK sang TM.',
							'key' => $this->security->get_csrf_hash(),
						);
						
						return_json($data);
						// Kiểm tra xem record này có tồn tại trên server online không
						$check_online = $db_online->get_where('orders', array('orderId' => $lastNo))->row();
						if ($check_online) {
							// Nếu có thì tiến hành update online
							$db_online->where('orderId', $lastNo);
							$db_online->update('orders', $dataUpdate);
						}
						exit();
					}
				}
			} else {
				$data = array(
					'status'=>false,
					'mes' => 'Order không tồn tại trong hệ thống. Vui lòng kiểm tra.',
 					'key' => $this->security->get_csrf_hash(),
				);
			}
		} else {
			$data = array(
				'status'=>false,
				'mes' => 'Hệ thống không thể xử lý thông tin. Vui lòng kiểm tra.',
				'key' => $this->security->get_csrf_hash(),
			);
		}
		return_json($data);
		//
	}
	
	public function login(){
		if(!empty($_POST)){
			$user = $this->home->checkLogin($this->input->post('user'));
			if($user && md5($this->input->post('pass')) == $user[0]->password){
				$this->session->set_userdata('userLogin', $user[0]);
				// Tạo một token ngẫu nhiên và bảo mật
                $token = md5(uniqid(mt_rand(), true));
				$dataToken = array(
					'session' => $token
				);
				$this->db->where('id',$user[0]->id);
				if($this->db->update('users', $dataToken)){
					// 2. Tạo mảng dữ liệu cookie
					$cookie = array(
						'name'   => 'remember_token',
						'value'  => $token,
						'expire' => 31536000,
						'path'   => '/',
						'domain' => '',      // Quan trọng: Để trống khi dùng localhost
						'secure' => FALSE,   // Vì bạn dùng localhost thường (http), phải để FALSE
						'httponly' => TRUE   // Ngăn JavaScript can thiệp, giúp bảo mật hơn
					);
					// 3. Gọi hàm set_cookie
					$this->input->set_cookie($cookie);				
				}
				$checkShift = $this->home->checkExsitShiftofDay();
				if($checkShift) {
					$this->session->set_userdata('staffName', $checkShift[0]->name);
				}

				$data = array(
					'status'=>true,
					'key' => $this->security->get_csrf_hash(),
				);
			}else{
				$data = array(
					'status'=>false,
					'key' => $this->security->get_csrf_hash(),
				);
			}
		}else{
			$data = array(
				'status'=>false,
				'key' => $this->security->get_csrf_hash(),
			);
		}
		$info = $this->session->userdata('userLogin');
		return_json($data);
	}

	public function logout(){
		$this->session->unset_userdata('userLogin');
		$this->session->unset_userdata('staffName');
		$this->session->unset_userdata('cart_products');
			delete_cookie('remember_token');
			header('Location: '.PATH_URL);
	}

	public function getCatelogiesProduct(){
    	$req = $this->home->getCategories('PRODUCT');
		foreach ($req as $key => $item) {
			$req[$key]->image = DIR_UPLOAD_CATE.$item->image;
		}
		return_json($req);
	}

	public function getAllProduct(){
    	$req= $this->home->getAllProduct();
		foreach ($req as $key => $item) {
			$formatTopping = unserialize($item->toppings);
			if ($formatTopping) {
				$toppings = $this->home->getListToppingProduct($formatTopping);
				$req[$key]->toppings = $toppings;
			} else {
				$req[$key]->toppings = [];
			}
			$formatSize = unserialize($item->price_size);
			$req[$key]->price_size = [];
			if ($formatSize && count($formatSize) > 0) { 
				$req[$key]->price_size = $formatSize;
			}
			$req[$key]->image = $item->image;
		}
		return $req;
	}

	public function getProduct($id){
    	$req = $this->home->getProduct($id);
		$req[0]->price = unserialize($req[0]->price);
		$formatTopping = unserialize($req[0]->toppings);

		if ($formatTopping) {
			$toppings = $this->home->getListToppingProduct($formatTopping);
			$req[0]->toppings = $toppings;
		} else {
			$req[0]->toppings = [];
		}
		foreach($req[0]->price as $key => $item) {
			if ($item === 0 || $item == 0) {
				unset($req[0]->price[$key]);
			}
		}
		return_json($req);
	}

	function addcart(){
		if (!empty($_POST)) {
			$id  = $_POST["id"];
			$cart_products = $this->session->userdata('cart_products');
			if($cart_products == NULL){
				$cart = array();
				$product = new StdClass;
				$product->id = $_POST["id"];
				$product->topping = $_POST["topping"];
				$product->amount = $_POST["amount"];
				$product->note = $_POST["note"];
				$product->isCupCustomer = $_POST["isCupCustomer"];
				$product->size = $_POST["size"];
				$cart[] = $product;
				$this->session->set_userdata('cart_products', $cart);
				$newCount = $this->countSessionCart();
				$data = array(
					'status'=>true,
					'key' => $this->security->get_csrf_hash(),
					'countCart' => $newCount
				);
			} else {
				$check = true;
				foreach ($cart_products as $item) {
					if ($item->id == $_POST["id"] && $_POST["topping"] === '' && $item->topping == '' && $item->size == $_POST["size"] && ( $item->note == $_POST["note"] || $item->isCupCustomer == $_POST["isCupCustomer"])) {
						$item->amount += $_POST["amount"];
						$check = false;
						break;
					}
				}
				if($check) {
					$product = new StdClass;
					$product->id = $_POST["id"];
					$product->topping = $_POST["topping"];
					$product->amount = $_POST["amount"];
					$product->size = $_POST["size"];
					$product->note = $_POST["note"];
					$product->isCupCustomer = $_POST["isCupCustomer"];
					$cart_products[] = $product;
				}
				$this->session->set_userdata('cart_products', $cart_products);
				$newCount = $this->countSessionCart();
				$data = array(
					'status'=>true,
					'key' => $this->security->get_csrf_hash(),
					'countCart' => $newCount
				);
			}		
		} else {
			$data = array(
				'status'=>false,
				'key' => $this->security->get_csrf_hash(),
			);
		}
		return_json($data);
	}

	public function viewQuickCart(){
		$cart_products = $this->session->userdata('cart_products');

		if ($cart_products == NULL) {
			$data['count'] = 0;
			$this->load->view("ajax_viewcart",$data);
		}else{
			$data['cart'] =$this->getListCart();
			$data['count'] = $this->countSessionCart();
			$this->load->view("ajax_viewcart",$data);
		}
	}

	public function countSessionCart(){
		$count = 0;
		$cart_products = $this->session->userdata('cart_products');
		if($cart_products){
			foreach ($cart_products as $key => $c) {
				$count = $count + $c->amount;
			}
			
		}
		return $count;
	}

	public function getListCart(){
		$cart_products=$this->session->userdata('cart_products'); 
		$cart = [];
		if($cart_products) {
			foreach ($cart_products as $key => $p) {
				$dataProduct = $this->home->getProduct($p->id);
				if($dataProduct) {
					$productCart = new StdClass;
					$productCart->id = $p->id;
					$productCart->name = $dataProduct[0]->name;
					$productCart->amount = $p->amount;
					$productCart->image = $dataProduct[0]->image;
					$productCart->size = $p->size;
					$productCart->note = $p->note;
					$productCart->isCupCustomer = $p->isCupCustomer;
					$productCart->totalPriceSize = 0;
					if($p->size!='') {
						$dataPriceSize = unserialize($dataProduct[0]->price_size);
						$productCart->totalPriceSize = $dataPriceSize[$p->size];
					}
					$totalPriceTopping = 0;
					$toppingDetail = '';
					$productCart->toppings= [];
					if ($p->topping != '' && is_array($p->topping) ) {
						foreach ($p->topping as $k => $t) {
							$res = $this->home->getToppingWithId($t['id']);
							$topping = new StdClass;
							$topping->name = $res[0]->name;
							$topping->qty = $t['qty'];
							$topping->price = $res[0]->price;

							$priceToppingSetect = (int)$t['qty'] * $res[0]->price;
							$totalPriceTopping = $totalPriceTopping + $priceToppingSetect;
							if ($toppingDetail != '') {
								$toppingDetail .= ', '.$res[0]->name.' x'.$t['qty'];
							} else {
								$toppingDetail = $res[0]->name.' x'.$t['qty'];
							}
							$productCart->toppings[] = $topping;
						}
					}
					$productCart->topping = $toppingDetail;
					$productCart->priceTopping = $totalPriceTopping*$p->amount;
					$productCart->totalPrice = ($dataProduct[0]->price + $totalPriceTopping + $productCart->totalPriceSize) * $p->amount;
					$cart[] = $productCart;
				}
			}
		}

		return $cart;
	}

	public function removeCart(){
		$id  = $_POST["id"];
		if ($id) {
			$cart_products = $this->session->userdata('cart_products');
			foreach ($cart_products as $key => $c) {
				if($c->id == $id && $key == $_POST["index"]) {
					array_splice($cart_products, $_POST["index"], 1);
					break;
				}
			}
				
			$newcart = $cart_products;
			$this->session->set_userdata('cart_products', $newcart);
			$newCount = $this->countSessionCart();
			$data = array(
				'status'=>true,
				'key' => $this->security->get_csrf_hash(),
				'countCart' => $newCount
			);
		
		} else {
			$data = array(
				'status'=>false,
				'key' => $this->security->get_csrf_hash(),
			);
		}

		return_json($data);
	}
	
	public function updateItemCart(){
		$id  = $_POST["id"];
		if ($id) {
			$cart_products = $this->session->userdata('cart_products');
			foreach ($cart_products as $key => $c) {
				if($c->id == $id && $key == $_POST["index"]) {
					$c->amount = $_POST["qty"];
					break;
				}
			}
			$newcart = $cart_products;
			$this->session->set_userdata('cart_products', $newcart);
			$newCount = $this->countSessionCart();
			$data = array(
				'status'=>true,
				'key' => $this->security->get_csrf_hash(),
				'countCart' => $newCount
			);
		
		} else {
			$data = array(
				'status'=>false,
				'key' => $this->security->get_csrf_hash(),
			);
		}

		return_json($data);
	}
	public function selectedCardItem() {
		$id  = $_POST["productId"];
		$cartKey = isset($_POST["cartKey"]) ? $_POST["cartKey"] : false;
		$selected = false;
		$product = false;
		if ($id) {
			$cart_products = $this->session->userdata('cart_products');
			foreach ($cart_products as $key => $c) {
				if($c->id == $id && $key == $cartKey) {
					$selected = $c;
					break;
				}
			}
			$products = $this->getAllProduct();
			foreach ($products as $p) {
				if ($p->id == $id) {
					$product = $p;
					break;
				}
			}
			$data = array(
				'status'=>true,
				'key' => $this->security->get_csrf_hash(),
				'selected' => $selected,
				'product' => $product
			);
		
		} else {
			$data = array(
				'status'=>false,
				'key' => $this->security->get_csrf_hash(),
			);
		}

		return_json($data);
	}

	public function checkoutCart() {
		if($_POST["delivery"]){
			$cart = $this->getListCart();
			if($cart) {
				$total = array_reduce($cart, function ($sum, $entry) {
					$sum += $entry->totalPrice;
					return $sum;
				}, 0);
				$shipping = $_POST["delivery"];
				$note = $_POST["note"];
				$shippingTotal = $_POST["shippingFee"];
				$discountName = $_POST["discountName"];
				$res = $this->home->addOrder($cart, $total, $shippingTotal);
				// sử lý in dóa đơn
				if ($res) {
					$pushTimeHash = $this->generateRandomCode(4);
					$code = $res->orderId.$pushTimeHash;
					$info = $this->session->userdata('userLogin');
					// In hóa đơn

					if((isset($info->bill) && $info->bill > 0 && $_POST["delivery"] === "Delivery") || $this->input->post('discount') > 0) {
						 // Nếu là thanh toán chuyển khoản hoặc có giảm giá thì in bill
						try {
							$printBill = $this->home->getPrinter($info->storeId,'BILL');
							if($printBill) {
								@$this->printBill($printBill[0]->ip, $code, $res, $discountName);
							}
						} catch (Exception $e) {
							// Ghi log lỗi in nhưng không làm dừng chương trình
							log_message('error', 'Lỗi in Bill: ' . $e->getMessage());
						}
												
					}
					if(isset($info->tem) && $info->tem > 0){
						// in tem
						try {
							$printTem = $this->home->getPrinter($info->storeId,'TEM');
							if($printTem) {
								@$this->printTem($printTem[0]->ip,$code, $res);
							}
						} catch (Exception $e) {
							// Ghi log lỗi in nhưng không làm dừng chương trình
							log_message('error', 'Lỗi in Tem: ' . $e->getMessage());
						}
					}
					$this->session->unset_userdata('cart_products');
					if ($this->check_online_connection()) {
						$this->home->sync_pending_orders();
					} else {
						log_message('error', 'Server Online không phản hồi sau 2s, bỏ qua đồng bộ.');
					}
					$data['status'] = true;
					$data['key'] = $this->security->get_csrf_hash();
	
					return_json($data);
				} else {
					$data['status'] = false;
					$data['key'] = $this->security->get_csrf_hash();
					return_json($data);
				}
				exit();
			}
		}
	}
	public function printBill($ip,$code,$res, $discountName = '') {
		$cart = unserialize($res->detailcart);
		$subtotal = (int)$res->subtotal;
		$shippingtotal = (int)$res->shippingtotal;
		$grandtotal = $res->grandtotal;
		$shipping = $res->shipping;
		$note = $res->message;
		$discount = $res->discountcoupon;
		$payment = $res->payment == 1 ? 'TM' : 'CK';
		$this->load->library('PosPrinter', ['ip' => $ip, 'port' => 9100]);
		$totalAmount = array_sum(array_map(function($item){
			return $item->amount;
		}, $cart));
		$tr = '';
		
		$info = $this->session->userdata('userLogin');
		$staffName = $this->session->userdata('staffName');
		$receipt = [];
		$receipt[] = ['type' => 'center', 'text' => 'PHIẾU THANH TOÁN' , 'size' => 22];
		$receipt[] = ['type' => '2col', 'a' => 'CH: '.$info->storeName, 'b' => $shipping];
		$receipt[] = ['type' => '2col', 'a' => $code.' - '.$payment, 'b' => date('m-d H:i')];
		$receipt[] = ['type' => '2col', 'a' => 'Thu ngân: '.$info->phone, 'b' => $info->address ];
		$receipt[] = ['type' => '2col', 'a' => 'NV: '.$staffName, 'b' => ''];
		$receipt[] = ['type' => '2col', 'a' => 'Ghi chú: '.$note, 'b' => ''];
		$receipt[] = ['type' => 'line'];
		foreach ($cart as $item) {
			$name = $item->name . ($item->size ? " ({$item->size})" : "");
			$receipt[] = [
				'type' => '3col', 
				'a' => $name , 
				'b' => $item->amount, 
				'c' => number_format($item->totalPrice - $item->priceTopping, 0)
			];
			if (($item->note != NULL)) {
				$receipt[] = ['type' => '2col', 'a' => '+ '.$item->note, 'b' => ''];
			}
			if (!empty($item->toppings)) {
				foreach ($item->toppings as $t) {
					$receipt[] = [
						'type' => '3col', 
						'a' => '+ ' . $t->name , 
						'b' => $t->qty.'x'.$item->amount, 
						'c' => number_format($t->price*$item->amount*$t->qty, 0),
						'indent' => 20,
						'bold' => true
					];
				}
			}

		}
		$receipt[] = ['type' => 'line'];
		$receipt[] = ['type' => '3col', 'a' => 'Tổng cộng: ', 'b' => $totalAmount, 'c' => number_format($subtotal,0) ];
		if (($shippingtotal != 0 && $shippingtotal > 0)) {
			$receipt[] = ['type' => '2col', 'a' => 'Phí giao hàng: ', 'b' => number_format($shippingtotal,0)];
		}
		if ($discount > 0) {
			$receipt[] = ['type' => '2col', 'a' => $discountName, 'b' => '- '.number_format($discount,0)];
		}
		$receipt[] = ['type' => '3col', 'a' => 'Thành tiền: ', 'b' => $totalAmount, 'c' => number_format($grandtotal,0) ];
		$receipt[] = ['type' => 'line'];
		$receipt[] = ['type' => 'center', 'text' => 'Ghi Chú: Giá Khuyến Mãi (nếu có) đã được làm tròn.'];
		$receipt[] = ['type' => 'center', 'text' => 'Cảm ơn quý khách!'];
        // Nối chuỗi
        try {
			$this->posprinter->print($receipt);
        } catch (Exception $e) {
            echo "Lỗi khi in: ".$e->getMessage();
        }
		$this->posprinter->close();
    }


	public function printTem($ip,$code,$res) {
		$cart = unserialize($res->detailcart);
		$note = $res->message;
        $this->load->library('TemPrinter', ['ip' => $ip, 'port' => 9100]);
		$totalAmount = array_sum(array_map(function($item){
			return $item->amount;
		}, $cart));
		$int= 1;
		$staffName = $this->session->userdata('staffName');
		foreach ($cart as $key => $item) {
			for ($i=0; $i < $item->amount; $i++) { 
				$name = $item->name . ($item->size ? " ({$item->size})" : "");
				$perItem = $int.'/'.$totalAmount;
				$price = number_format($item->totalPrice/$item->amount);
				$noteLine = 0;
				$heightLine =0;
				$info = $this->session->userdata('userLogin');
				$noteItem = "+ ({$item->note})";
				//code in máy in tem phải đúng cấu trúc
				$commands = '';
				$commands .= "SIZE 53 mm,33 mm\n";
				$commands .= "GAP 2 mm,0 mm\n";
				$commands .= "CLS\n";
				//
				$commands .= 'TEXT 15,30,"2",0,1,1,"'.$note."\" \n"; //cột 1
				$commands .= 'TEXT 330,30,"2",0,1,1,"'.$perItem."\" \n"; //cột 2
				//
				$commands .= 'TEXT 15,60,"1",0,1,1,"'.$code."\" \n";
				$commands .= 'TEXT 330,60,"1",0,1,1,"'.$price."\" \n";
				$commands .= 'TEXT 15,70,"1",0,1,1,"'.'------------------------------------------------'."\" \n";
				//
				$commands .= 'TEXT 15,90,"2",0,1,1,"'.vn_to_ascii($name)."\" \n";
				if (($item->note != NULL)) {
					$commands .= 'TEXT 35,115,"2",0,1,1,"'.$noteItem."\" \n";
					$noteLine = 25;
				}
				
				if (!empty($item->toppings)) {
					foreach ($item->toppings as $key => $v) {
						$heightLine = $noteLine+(25*($key+1));
						$commands .=  'TEXT 15,'.(90+$heightLine).',"2",0,1,1,"'.vn_to_ascii($v->name).' x'.$v->qty ."\" \n";
					}
					
				}
				$commands .=  'TEXT 15,'.(115+$heightLine+$noteLine).',"2",0,1,1,"NV: '.$staffName.' - '.date('H:i')."\" \n";
				$commands .= "PRINT 1\n";
				try {
					$this->temprinter->print($commands);
				} catch (Exception $e) {
					echo "Lỗi khi in: ".$e->getMessage();
				}
				$int++;
			}
		}
		$this->temprinter->close();
    }

	
	public function updateFulfillmentOrder(){
    	//
		if($this->home->updateFulfillmentOrder($_POST["id"])){
			$data = array(
				'status'=>true,
				'key' => $this->security->get_csrf_hash(),
			);
		}else{
			$data = array(
				'status'=>false,
				'key' => $this->security->get_csrf_hash(),
			);
		}
		return_json($data);
	}

	public function removeAllCart(){
    	//
		$this->session->unset_userdata('cart_products');
		$data = array(
			'status'=>true,
			'key' => $this->security->get_csrf_hash(),
		);
		return_json($data);
	}

	public function saveCustomer(){
    	//
		if($this->home->saveCustomer()){
			$res = array("Return"=>"1","Msg"=>"success") ;
			return_json($res);
		}else{
			$res = array("Return"=>"0","Msg"=>"error") ;
			return_json($res);
		}
	}
	public function saveOrder() {
		if($this->home->saveOrder()){
			$res = array("Return"=>"1","Msg"=>"success") ;
			return_json($res);
		}else{
			$res = array("Return"=>"0","Msg"=>"error") ;
			return_json($res);
		}
	}

	public function getOrderHistoryCustomer(){
		$phone = $_POST['phone'];
    	$req = $this->home->getOrderHistoryCustomer($phone);
		return_json($req);
	}
	public function getInfoCustomer(){
		$phone = $_POST['phone'];
    	$req = $this->home->getInfoCustomer($phone);
		return_json($req);
	}
	
	public function getOrderForStore(){
		$store = $_POST['store'];
    	$req = $this->home->getOrderForStore($store);
		return_json($req);
	}

	function generateRandomCode($length = 6) {
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$shuffled = str_shuffle($characters);
		return substr($shuffled, 0, $length);
	}
	function addForWaiting() {
		// 1. Lấy giỏ hàng chính hiện tại
		$currentCart = $this->session->userdata('cart_products');
		
		if (empty($currentCart)) {
			return_json(['status' => false, 'key' => $this->security->get_csrf_hash(), 'msg' => 'Giỏ hàng trống']);
				exit();
		}
		// 2. Lấy danh sách các đơn hàng đã chờ trước đó (nếu có)
		$holdOrders = $this->session->userdata('hold_orders');
		if (!$holdOrders) {
			$holdOrders = [];
		}
		$cart =$this->getListCart();
		// 3. Tạo một đơn chờ mới kèm theo định danh (ví dụ dùng timestamp để phân biệt)
		$totalAmount = 0;
		$totalprice = 0;
		foreach ($cart as $item) {
			$totalAmount += $item->amount;
			$totalprice += $item->totalPrice;
		}
		$holdId = 'Hold_' . time(); 
		$holdOrders[$holdId] = [
			'hold_id'    => $holdId,
			'hold_time'  => date('Y-m-m H:i:s'),
			'products'   => $currentCart, // Lưu nguyên mảng sản phẩm vào đây
			'totalAmount'       => $totalAmount, // Có thể lưu thêm ghi chú nếu FE gửi lên
			'totalPrice' => $totalprice
		];

		// 4. Cập nhật lại vào Session hàng chờ
		$this->session->set_userdata('hold_orders', $holdOrders);

		// 5. Xóa giỏ hàng chính để thu ngân tạo đơn mới
		$this->session->unset_userdata('cart_products');
		$countHold = count($holdOrders);
		return_json(['status' => true, 'count' => $countHold, 'key' => $this->security->get_csrf_hash(), 'msg' => 'Đã chuyển vào hàng chờ']);
		exit();
	}

	public function viewHoldCart(){
		$holdOrders = $this->session->userdata('hold_orders');
		if ($holdOrders == NULL) {
			$data['count'] = 0;
			$this->load->view("ajax_viewhold",$data);
		}else{
			$data['count'] = count($holdOrders);
			$data['orders'] = $holdOrders;
			$this->load->view("ajax_viewhold",$data);
		}
	}
	public function getForWaiting() {
		$holdId = $this->input->post('hold_id'); // Nhận ID của đơn chờ từ FE gửi lên
		
		// 1. Lấy danh sách hàng chờ từ Session
		$holdOrders = $this->session->userdata('hold_orders');
		
		if (empty($holdOrders) || !isset($holdOrders[$holdId])) {
			return_json([
				'status' => false, 
				'msg' => 'Không tìm thấy đơn hàng chờ này hoặc đã bị xóa trước đó!'
			]);
			exit();
		}
		// Lấy ra danh sách sản phẩm trong đơn chờ này
		$holdProducts = $holdOrders[$holdId]['products'];

		// 2. Lấy giỏ hàng chính hiện tại ra để chuẩn bị gộp
		$currentCart = $this->session->unset_userdata('cart_products');

		// 4. Lưu lại giỏ hàng chính mới sau khi đã gộp/thêm
		$this->session->set_userdata('cart_products', $holdProducts);

		unset($holdOrders[$holdId]);
		$this->session->set_userdata('hold_orders', $holdOrders);

		// 6. Trả kết quả về cho FE load lại giao diện giỏ hàng
		return_json([
			'status' => true, 
			'msg' => 'Đã khôi phục sản phẩm vào giỏ hàng thành công!',
			'key' => $this->security->get_csrf_hash()
		]);
		exit();
	}

	function removeAllHold() {
		$this->session->unset_userdata('hold_orders');
		return_json([
			'status' => true, 
			'msg' => 'Đã xóa toàn bộ đơn hàng chờ!',
			'key' => $this->security->get_csrf_hash()
		]);
		exit();
	}
	/*------------------------------------ End API --------------------------------*/
	public function check_online_connection() {
		$host = DBOL_HOST; // IP Server của bạn
		$port = 3306;      // Cổng MySQL
		$timeout = 2;      // Chỉ chờ trong 2 giây
	
		// Thử mở một kết nối socket tới port 3306
		$connection = @fsockopen($host, $port, $errno, $errstr, $timeout);
	
		if ($connection) {
			fclose($connection);
			return TRUE; // Có mạng, Server đang mở
		} else {
			return FALSE; // Không có mạng hoặc Server chặn port
		}
	}

}