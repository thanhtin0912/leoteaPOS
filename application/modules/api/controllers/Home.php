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
	
	public function getStores(){
    	$req = $this->home->getStores();
		foreach ($req as $key => $item) {
			$req[$key]->image = DIR_UPLOAD_STORES.$item->image;
		}
		echo json_encode($req);
	}

	public function getCatelogiesProduct(){
    	$req = $this->home->getCategories('PRODUCT');
		foreach ($req as $key => $item) {
			$req[$key]->image = DIR_UPLOAD_CATE.$item->image;
		}
		echo json_encode($req);
	}

	public function getAllProduct(){
    	$req= $this->home->getProducts();
		foreach ($req as $key => $item) {
			$req[$key]->price = unserialize($item->price);
			$formatTopping = unserialize($item->toppings);

			if ($formatTopping) {
				$toppings = $this->home->getListToppingProduct($formatTopping);
				$req[$key]->toppings = $toppings;
			} else {
				$req[$key]->toppings = [];
			}
			$req[$key]->image = DIR_UPLOAD_PRODUCT.$item->image;
		}
		echo json_encode($req);
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
		echo json_encode($req);
	}

	public function saveCustomer(){
    	//
		if($this->home->saveCustomer()){
			$res = array("Return"=>"1","Msg"=>"success") ;
			echo json_encode($res);
		}else{
			$res = array("Return"=>"0","Msg"=>"error") ;
			echo json_encode($res);
		}
	}
	public function saveOrder() {
		if($this->home->saveOrder()){
			$res = array("Return"=>"1","Msg"=>"success") ;
			echo json_encode($res);
		}else{
			$res = array("Return"=>"0","Msg"=>"error") ;
			echo json_encode($res);
		}
	}

	public function getOrderHistoryCustomer(){
		$phone = $_POST['phone'];
    	$req = $this->home->getOrderHistoryCustomer($phone);
		echo json_encode($req);
	}
	public function getInfoCustomer(){
		$phone = $_POST['phone'];
    	$req = $this->home->getInfoCustomer($phone);
		echo json_encode($req);
	}
	
	public function getOrderForStore(){
		$store = $_POST['store'];
    	$req = $this->home->getOrderForStore($store);
		echo json_encode($req);
	}

	/*------------------------------------ End API --------------------------------*/

}