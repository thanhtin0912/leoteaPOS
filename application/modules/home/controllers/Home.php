<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MX_Controller {

	function __construct(){
		parent::__construct();
		$this->load->helper('language');
		$this->lang->load('general');
		$this->load->model('home/Home_model','home');
		$this->load->library('session');
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

	public function login(){
		if(!empty($_POST)){
			$user = $this->home->checkLogin($this->input->post('user'));
			if($user && md5($this->input->post('pass')) == $user[0]->password){
				$this->session->set_userdata('userLogin', $user[0]);
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
				$invoiceCode = $this->home->addOrder($cart, $total);
				// sử lý in dóa đơn

				if ($invoiceCode) {
					$info = $this->session->userdata('userLogin');
					$printBill = $this->home->getPrinter($info->storeId,'BILL');
					if($printBill) {
						$this->printBill($printBill[0]->ip,$invoiceCode,$cart,$total);
					}
					$printTem = $this->home->getPrinter($info->storeId,'TEM');
					if($printTem) {
						$this->printTem($printTem [0]->ip,$invoiceCode,$cart);
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
	public function printBill($ip,$res,$cart,$total) {
		$this->load->library('PosPrinter', ['ip' => $ip, 'port' => 9100]);
		$totalAmount = array_sum(array_map(function($item){
			return $item->amount;
		}, $cart));
		$tr = '';
		// foreach ($cart as $item) {
		// 	$name = $item->name . ($item->size ? " ({$item->size})" : "");
		// 	$tr .= $this->formatItem($name, $item->amount, $item->totalPrice - $item->priceTopping). "\n";
		// 	if (!empty($item->toppings)) {
		// 		foreach ($item->toppings as $t) {
		// 			$tr .= "   + " . $t->name .' x'.$t->qty.' ---- '. number_format($t->price, 0)."\n";
		// 		}
		// 	}
		// }
		$info = $this->session->userdata('userLogin');

		$receipt = [];
		$receipt[] = ['type' => 'center', 'text' => $info->storeName , 'size' => 22];
		$receipt[] = ['type' => 'center', 'text' => 'HÓA ĐƠN THANH TOÁN' , 'size' => 22];
		$receipt[] = ['type' => '2col', 'a' => $res, 'b' => date('Y-m-d H:i:s')];
		$receipt[] = ['type' => '2col', 'a' => 'Thu ngân: '.$info->phone, 'b' => 'Phục vụ: '.$info->phone ];
		$receipt[] = ['type' => 'line'];
		foreach ($cart as $item) {
			$name = $item->name . ($item->size ? " ({$item->size})" : "");
			$receipt[] = [
				'type' => '3col', 
				'a' => $name , 
				'b' => $item->amount, 
				'c' => number_format($item->totalPrice - $item->priceTopping, 0)
			];
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
            // $this->posprinter->print_text($bill);
			$this->posprinter->print($receipt);
        } catch (Exception $e) {
            echo "Lỗi khi in: ".$e->getMessage();
        }
		// $this->posprinter->close();
    }

	// dugf chung máy in bill
	public function printTem($ip,$res,$cart) {
        $this->load->library('PosPrinter', ['ip' => $ip, 'port' => 9100]);
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
					$this->posprinter->print($receipt);
				} catch (Exception $e) {
					echo "Lỗi khi in: ".$e->getMessage();
				}
				$int++;
			}
		}
		$this->posprinter->close();
    }	

	public function printTem_maytem($ip,$res,$cart) {
        $this->load->library('TemPrinter');
		$totalAmount = array_sum(array_map(function($item){
			return $item->amount;
		}, $cart));
		$int= 1;
		foreach ($cart as $key => $item) {
			for ($i=0; $i < $item->amount; $i++) { 
				$tr = '';
				$name = $item->name . ($item->size ? " ({$item->size})" : "");
				$tr .= $this->formatItem($name, '', ($item->totalPrice)/$item->amount, 36). "\n";
				if (!empty($item->topping)) {
					$tr .= "   + " . $item->topping . "\n";
				}
				$perItem = $int.'/'.$totalAmount;
				$formatStr = $this->formatItem($res, '', $perItem, 36);
				$commands = "";
				$commands .= "SIZE 53 mm,33 mm\n";
				$commands .= "GAP 2 mm,0 mm\n";
				$commands .= "CLS\n";
				$commands .= 'TEXT 210,20,"4",0,1,1,"'.$perItem."\" \n";
				$commands .= 'TEXT 210,45,"1",0,1,1,"'.date('Y-m-d H:i:s',time())."\" \n";
				$commands .= 'TEXT 210,70,"1",0,1,1,"'.$res."\" \n";
				$commands .= 'TEXT 210,95,"1",0,1,1,"'.vn_to_ascii($name).'---'.number_format(($item->totalPrice)/$item->amount)."\" \n";
				if (!empty($item->topping)) {
					$commands .=  'TEXT 210,95,"1",0,1,1,"'.vn_to_ascii($item->topping)."\" \n";
				}
				$commands .= "PRINT 1\n";
				// $text = [
				// 	date('Y-m-d H:i:s',time()),
				// 	$formatStr,
				// 	$tr,
				// ];
				// Nối chuỗi
				
				try {
					$this->temprinter->print_text($commands);
				} catch (Exception $e) {
					echo "Lỗi khi in: ".$e->getMessage();
				}
				$int++;
			}
		}
		$this->temprinter->close();
    }

	private function formatItem($textLeft, $qty = '', $textRight, $totalWidth = 48) {
		if($qty != '') {
			$qty = "x".$qty;
		}
		$price = $textRight;
		if (is_numeric($textRight)) {
			$price = number_format($textRight, 0, ',', '.');
		}
		
	
		// tạo text số lượng + giá
		$rightCol = $qty . "  " . $price;
		$rightLen = mb_strlen($rightCol, "UTF-8");
	
		// chiều dài tên tối đa
		$maxNameLen = $totalWidth - $rightLen - 1;
	
		if (mb_strlen($textLeft, "UTF-8") > $maxNameLen) {
			// nếu tên dài → xuống dòng
			$firstLine = mb_substr($textLeft, 0, $maxNameLen);
			$nextLine = mb_substr($textLeft, $maxNameLen);
			return $firstLine . " " . $rightCol . "\n" . $nextLine . "\n";
		}
	
		// nếu tên ngắn → căn khoảng cách
		$space = str_repeat(" ", $maxNameLen - mb_strlen($textLeft, "UTF-8"));
		return $textLeft . $space . $rightCol;
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


	/*------------------------------------ End API --------------------------------*/

    public function sendMessage() {
        $webhook_url = "https://discord.com/api/webhooks/1381455257866997870/JoXcEl9wgdIcmK5zqTBM39HDZqH5iBHuzGSKUc3ZsZjC9W8dXa6Nx3IhOxtpgpvy1v9p";

        $data = [
            "username" => "CI3 Bot",
            "content"  => "**Đây là một tin nhắn từ CodeIgniter 3!**\nĐây là một tin nhắn từ CodeIgniter 3! "
        ];

        $json_data = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // Khởi tạo cURL
        $ch = curl_init($webhook_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);

        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Kiểm tra kết quả
        if ($http_status == 204) {
            echo "✅ Gửi webhook thành công!";
        } else {
            echo "❌ Lỗi gửi webhook. Mã lỗi HTTP: $http_status <br>Chi tiết: $curl_error <br>Response: $response";
        }
    }

}