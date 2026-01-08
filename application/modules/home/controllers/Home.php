<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MX_Controller {

	function __construct(){
		parent::__construct();
		$this->load->helper('language');
		$this->lang->load('general');
		$this->load->model('home/Home_model','home');
		$this->load->library('session');
		$this->load->helper('cookie');
		if(!$this->session->userdata('userLogin')) {
			$token = get_cookie('remember_token');
			if ($token) {
				$user = $this->home->checkCookie($token);
				if (!empty($user)) {
					// Tái tạo session
					$this->session->set_userdata('userLogin', $user[0]);
				} else {
					delete_cookie('remember_token');
				}
			}
		}

	}
	
	/*------------------------------------ API ------------------------------------*/
	public function index()
	{

		$data['info'] = $this->home->getInfoSite();
		$data['sales'] = $this->home->getProductsSales();
		$data['cates'] = $this->home->getCategories('PRODUCT');
		$data['products'] = $this->getAllProduct();
		$data['banner'] = $this->home->getBanner();
		$data["countCart"]=$this->countSessionCart();
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
			$data['info'] = $this->home->getInfoSite();
			$data['cart'] =$this->getListCart();
			$data['countCart'] = $this->countSessionCart();
			$this->template->write_view('content', 'checkout', $data);
			$this->template->render();
		}
		//
		
	}
	
	public function history()
	{
		$data['info'] = $this->home->getInfoSite();
		$data['cart'] =$this->getListCart();
		$data['countCart'] = $this->countSessionCart();
		$data['orderToday'] = $this->home->getListOrderToday();
		$this->template->write_view('content', 'history', $data);
		$this->template->render();
		//
	}

	public function delivery()
	{
		$data['orders'] = $this->home->getListfulfillmentOrderStore();
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
		$this->template->write_view('content', 'shift_in', $data);
		$this->template->render();
		//
	}
	
	function checkIn(){
    	$req = $this->home->checkinShiftDay();
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
		$data['info'] = $this->home->getInfoSite();
		$data['cart'] =$this->getListCart();
		$data['countCart'] = $this->countSessionCart();
		$data['checkShift'] = $this->home->checkExsitShiftofDay();
		$this->template->write_view('content', 'shift_out', $data);
		$this->template->render();
		//
	}
	function checkOutShift(){
		$data=array(
			"to"=> date('Y-m-d H:i:s',time()),
			"updated"=> date('Y-m-d H:i:s',time())
		);
    	$req = $this->home->updateShiftDay($_POST["id"], $data);
		$data = array(
			'status'=>false,
			'key' => $this->security->get_csrf_hash(),
		);
		$encoded = short_encode($_POST["id"]);
		if($req) {
			$data = array(
				'status'=>true,
				'id'=> $encoded,
				'key' => $this->security->get_csrf_hash(),
			);
		}
		return_json($data);
	}



	public function reportShift()
	{
		$key = short_decode($_GET["id"]); // Giải mã bằng chính hàm đó
		$shift = $this->home->getShiftofDay($key, 0);
		$data['res'] = false;
		if($_GET["id"] && $shift){
			$data['salesShift']= $this->home->getTotalRevenueShift($shift[0]->from, $shift[0]->to, $shift[0]->user);
			$data['res'] = $shift;
		}
		$this->load->view('report-shift', $data);
		//
	}
	function updateCheckoutShift(){
		$data=array(
			"report"=> serialize($_POST['money_data']) || [],
			"actual"=> $_POST['actual'],
			"sales"=> $_POST['sales'],
			"completed" => 1,
			"updated"=> date('Y-m-d H:i:s',time())
		);
    	$req = $this->home->updateShiftDay($_POST["id"], $data);
		$data = array(
			'status'=>false,
			'key' => $this->security->get_csrf_hash(),
		);
		if($req) {
			$this->load->library('Discord');
			$s = $this->home->getShiftofDay($_POST["id"], 1);
			if ($s) {
				$now = date('Y-m-d H:i:s');
				$diff = number_format($s[0]->actual - $s[0]->sales);
				$sales = number_format($s[0]->sales);
				$actual = number_format($s[0]->actual);
				$gio_vao = date('H:i:s', strtotime($s[0]->from));
				$gio_ra  = date('H:i:s', strtotime($s[0]->to));

				// Viết sao hiển thị vậy, rất dễ quản lý
				$tr = <<<EOT
					**Báo cáo kết ca - $now!**
					-----------------------------
					Nhân viên: {$s[0]->name} - Cửa hàng: {$s[0]->storeName}
					Thời gian: {$gio_vao} - {$gio_ra}
					-----------------------------
					Doanh thu: $sales - Tiền nộp: $actual
					Chêch lệch: $diff
					-----------------------------
					EOT;
			
				$dis = $this->discord->sendsms($tr);
			}
			$data = array(
				'status'=>true,
				'key' => $this->security->get_csrf_hash(),
			);
		}
		return_json($data);
	}

	public function login(){
		if(!empty($_POST)){
			$user = $this->home->checkLogin($this->input->post('user'));
			if($user && md5($this->input->post('pass')) == $user[0]->password){
				$this->session->set_userdata('userLogin', $user[0]);
				// Tạo một token ngẫu nhiên và bảo mật
                $token = bin2hex(random_bytes(32));
				$dataToken = array(
					'session' => $token
				);
				$this->db->where('id',$user[0]->id);
				if($this->db->update('users', $dataToken)){
					// 2. Tạo mảng dữ liệu cookie
					$cookie = array(
						'name'   => 'remember_token',
						'value'  => $token,                   // Biến token bạn đã tạo
						'expire' => 31536000,                // 1 năm (tính bằng giây)
					);
					// 3. Gọi hàm set_cookie
					set_cookie($cookie);					
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
			$req[$key]->image = DIR_UPLOAD_PRODUCT.$item->image;
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
					if ($item->id == $_POST["id"] && $_POST["topping"] === '' && $item->topping == '' && $item->size == $_POST["size"]) {
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
				$invoiceCode = $this->home->addOrder($cart, $total);
				// sử lý in dóa đơn
				$pushTimeHash = $this->generateRandomCode(4);
				$code = $invoiceCode.$pushTimeHash;
				if ($invoiceCode) {
					$info = $this->session->userdata('userLogin');
					$printBill = $this->home->getPrinter($info->storeId,'BILL');
					if($printBill) {
						$this->printBill($printBill[0]->ip,$code,$cart,$total, $shipping, $note);
					}
					$printTem = $this->home->getPrinter($info->storeId,'TEM');
					if($printTem) {
						$this->printTem($printTem [0]->ip,$code,$cart, $note);
					}
					$this->session->unset_userdata('cart_products');
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
	public function printBill($ip,$code,$cart,$total,$shipping, $note) {
		$this->load->library('PosPrinter', ['ip' => $ip, 'port' => 9100]);
		$totalAmount = array_sum(array_map(function($item){
			return $item->amount;
		}, $cart));
		$tr = '';
		
		$info = $this->session->userdata('userLogin');
		$receipt = [];
		$receipt[] = ['type' => 'center', 'text' => 'PHIẾU THANH TOÁN' , 'size' => 22];
		$receipt[] = ['type' => '2col', 'a' => 'CH: '.$info->storeName, 'b' => $shipping];
		$receipt[] = ['type' => '2col', 'a' => $code, 'b' => date('m-d H:i')];
		$receipt[] = ['type' => '2col', 'a' => 'Thu ngân: '.$info->phone, 'b' => $info->address ];
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
		$receipt[] = ['type' => '3col', 'a' => 'Tổng: ', 'b' => $totalAmount, 'c' => number_format($total,0) ];
		
		$receipt[] = ['type' => 'center', 'text' => 'Cảm ơn quý khách!'];
	
        // Nối chuỗi
        try {
			$this->posprinter->print($receipt);
        } catch (Exception $e) {
            echo "Lỗi khi in: ".$e->getMessage();
        }
		$this->posprinter->close();
    }

	// dugf chung máy in bill
	public function printTem_Img($ip,$res,$cart) {
        $this->load->library('TemPrinter');
		$totalAmount = array_sum(array_map(function($item){
			return $item->amount;
		}, $cart));
		$int= 1;
		foreach ($cart as $key => $item) {
			for ($i=0; $i < $item->amount; $i++) { 
				$name = $item->name . ($item->size ? " ({$item->size})" : "");
				$perItem = $int.'/'.$totalAmount;
				// in tem cho từng ly
				$receipt = [];
				$receipt[] = ['type' => 'center', 'text' => $perItem, 'size' => 24];
				$receipt[] = ['type' => '2col', 'a' => $res, 'b' => date('Y-m-d H:i:s')];
				$receipt[] = ['type' => 'line'];
				$name = $item->name . ($item->size ? " ({$item->size})" : "");
				$receipt[] = [
					'type' => '2col', 
					'a' => $name , 
					'b' => number_format($item->totalPrice/$item->amount, 0)
				];
				if (!empty($item->toppings)) {
					foreach ($item->toppings as $t) {
						$receipt[] = [
							'type' => '2col', 
							'a' => '+ ' . $t->name.' x'.$t->qty , 
							'b' => '',
						];
					}
				}
				
				try {
					$this->temprinter->print($receipt);
				} catch (Exception $e) {
					echo "Lỗi khi in: ".$e->getMessage();
				}
				$int++;
			}
		}
		$this->temprinter->close();
    }	

	public function printTem($ip,$code,$cart,$note) {
        $this->load->library('TemPrinter', ['ip' => $ip, 'port' => 9100]);
		$totalAmount = array_sum(array_map(function($item){
			return $item->amount;
		}, $cart));
		$int= 1;
		foreach ($cart as $key => $item) {
			for ($i=0; $i < $item->amount; $i++) { 
				$name = $item->name . ($item->size ? " ({$item->size})" : "");
				$perItem = $int.'/'.$totalAmount;
				$price = number_format($item->totalPrice/$item->amount);
				$noteLine = 0;
				//code in máy in tem phải đúng cấu trúc
				$commands = '';
				$commands .= "SIZE 53 mm,33 mm\n";
				$commands .= "GAP 2 mm,0 mm\n";
				$commands .= "CLS\n";
				//
				$commands .= 'TEXT 15,30,"2",0,1,1,"'.$note."\" \n"; //cột 1
				$commands .= 'TEXT 350,30,"2",0,1,1,"'.$perItem."\" \n"; //cột 2
				//
				$commands .= 'TEXT 15,60,"1",0,1,1,"'.$code."\" \n";
				$commands .= 'TEXT 350,60,"1",0,1,1,"'.$price."\" \n";
				$commands .= 'TEXT 15,70,"1",0,1,1,"'.'------------------------------------------------'."\" \n";
				//
				$commands .= 'TEXT 15,90,"2",0,1,1,"'.vn_to_ascii($name)."\" \n";
				if (($item->note != NULL)) {
					$commands .= 'TEXT 12,115,"2",0,1,1,"'.$item->note."\" \n";
					$noteLine = 25;
				}
				if (!empty($item->toppings)) {
					foreach ($item->toppings as $key => $v) {
						$commands .=  'TEXT 15,'.(90+$noteLine+(25*($key+1))).',"2",0,1,1,"'.vn_to_ascii($v->name).' x'.$v->qty ."\" \n";
					}
					
				}
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

	/*------------------------------------ End API --------------------------------*/


}