<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home_model extends CI_Model {
	private $module = 'stores';
	private $tbl_stores				= 'stores';
	private $tbl_products			= 'products';
	private $tbl_cate				= 'categories';
	private $tbl_customer			= 'customers';
	private $tbl_order				= 'orders';
	private $tbl_toppings				= 'toppings';
	private $tbl_infor				= 'infos';
	private $tbl_users				= 'users';
	private $tbl_printer				= 'printer';
	

	function getInfoSite(){
		$this->db->select('*');
		$query = $this->db->get(PREFIX.$this->tbl_infor);
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	function getPrinter($store,$type){
		$this->db->select('*');
		$this->db->where('storeId', $store);
		$this->db->where('type', $type);
		$this->db->where('status',1);
		$this->db->where('delete',0);
		$query = $this->db->get(PREFIX.$this->tbl_printer);
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	function getBanner(){
		$this->db->select('*');
		$query = $this->db->get('banners');
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}

	function checkLogin($user){
		$this->db->select('u.*, s.name as storeName, s.code as storeCode, s.address');
		$this->db->where('u.phone', $user);
		$this->db->where('u.status', 1);
		$this->db->where('u.delete', 0);
		$this->db->from(PREFIX.$this->tbl_users." u");
		$this->db->join(PREFIX.$this->tbl_stores." s", 'u.storeId = s.id', "left");
		$query = $this->db->get();
		foreach ($query->result() as $row){
			$pass = $row->password;
		}
		
		if(!empty($pass)){
			return $query->result();
		}else{
			return false;
		}
	}
	

	function getCategories($type){ 
		$this->db->select('id, image, name, slug');
		$this->db->where('status',1);
		$this->db->where('delete',0);
		$this->db->where('type',$type);
		$this->db->order_by('order','Asc');
		$this->db->from(PREFIX.$this->tbl_cate);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	function getProductsSales(){
		$this->db->select('*');
		$this->db->where('status',1);
		$this->db->where('delete',0);
		$this->db->order_by('sales','desc');
		$this->db->limit(10);
		$this->db->from(PREFIX.$this->tbl_products);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	function getAllProduct(){
		$this->db->select('p.*, c.name as cateName');
		$this->db->where('p.status', 1);
		$this->db->where('p.delete', 0);
		$this->db->from(PREFIX.$this->tbl_products." p");
		$this->db->join(PREFIX.$this->tbl_cate." c", 'p.type = c.id', "left");
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}

	function getProduct($id){
		$this->db->select('p.*, c.name as cateName');
		$this->db->where('p.id', $id);
		$this->db->where('p.status', 1);
		$this->db->where('p.delete', 0);
		$this->db->from(PREFIX.$this->tbl_products." p");
		$this->db->join(PREFIX.$this->tbl_cate." c", 'p.type = c.id', "left");
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}

	function getToppingWithId($id){
		$this->db->select('*');
		$this->db->where('id', $id);
		$this->db->from(PREFIX.$this->tbl_toppings);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}

	function getListToppingProduct($array){
		$this->db->select('t.id, t.name, t.price, t.isMulti, t.saleableQty');
		$this->db->where('`t.id` in ('. implode(',', $array) . ')');
		$this->db->order_by('c.order','ASC');
		$this->db->order_by('t.price','ASC');
		$this->db->from(PREFIX.$this->tbl_toppings." t");
		$this->db->join(PREFIX.$this->tbl_cate." c", 't.type = c.id', "left");
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	function getListToppingProductSumCart($array){
		$this->db->select_sum('price');
		$this->db->where('`id` in ('. implode(',', $array) . ')');
		$this->db->from(PREFIX.$this->tbl_toppings);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}

	function generateInvoiceCode($currentCode, $limit = 10) {
		$letter = substr($currentCode, 0, 1);      // Ký tự A, B, C
		$number = intval(substr($currentCode, 1)); // Số phía sau
		$number++;
		if ($number > $limit) {
			$number = 1;   // Reset lại 01
			$letter = chr(ord($letter) + 1);   // Tăng ký tự A→B→C
		}
	
		return $letter . str_pad($number, 2, '0', STR_PAD_LEFT);
	}

	function addOrder($cart, $total){
		//Kiểm tra đã tồn tại chưa?
		$info = $this->session->userdata('userLogin');
		$num = 'A01';
		$str = (string)($info->storeCode).(string)(date('ymd',time()));
		$findStr = $this->getLastOrderStore($str);
		if($findStr) {
			$lastNo = substr($findStr[0]->orderId, -3);
			$num = $this->generateInvoiceCode($lastNo);
		}
		$orderId = $str.$num;
		$data = array(
			'orderId'		=> $orderId,
			'mail'			=> '',
			'fullname'		=> $info->phone,
			'address'		=> '',
			'region'		=> '',
			'postcode'		=> '',
			'phone'			=> $info->phone,
			'message'		=> $_POST["note"],
			'subtotal'		=> $total,
			'discountmember'=> '',
			'discountcoupon'=> '',
			'codecoupon'	=> '',
			'tax'			=> '',
			'detailcart'	=> serialize($cart),
			'grandtotal'	=> $total,
			'shipping'		=> $_POST["delivery"],
			'fulfillment'	=> 2,
			'status'		=> 1,
			'delete'		=> 0,
			'created'		=> date('Y-m-d H:i:s',time()),
		);
		if($this->db->insert(PREFIX.$this->tbl_order,$data)){
			return $orderId;
		}
		return false;
	
	}

	function getListfulfillmentOrderStore(){
		$info = $this->session->userdata('userLogin');
		$this->db->select('*');
		$this->db->where('phone',$info->phone);
		$this->db->where('delete',0);
		$this->db->where('status',1);
		$this->db->where('fulfillment',2);
		$this->db->order_by('orderId','ABS');
		$this->db->from(PREFIX.$this->tbl_order);
		$this->db->limit(20);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	function getLastOrderStore($key){
		$this->db->select('*');
		$this->db->where('status',1);
		$this->db->where('delete',0);
		$this->db->like('orderId',$key);
		$this->db->order_by('orderId','DESC');
		$this->db->limit(1);
		$this->db->from(PREFIX.$this->tbl_order);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}

	function updateFulfillmentOrder($id){ 
		$this->db->where('id',$id);
		$data=array(
			"fulfillment"=>1,
			"updated"=> date('Y-m-d H:i:s',time())
		);
		$this->db->update(PREFIX.$this->tbl_order,$data);  
		return true;
	}

	function getListOrderToday(){
		$date = date("Y-m-d H:i:s",time());
		$info = $this->session->userdata('userLogin');
		$this->db->select('*');
		$this->db->where('phone',$info->phone);
		$this->db->where('status',1);
		$this->db->where('delete',0);
		$this->db->where('created >=', date('Y-m-d 00:00:01', strtotime($date)));
		$this->db->where('created <=', date('Y-m-d 23:59:59', strtotime($date)));
		$this->db->order_by('orderId','ABS');
		$this->db->from(PREFIX.$this->tbl_order);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}

	

}
?>