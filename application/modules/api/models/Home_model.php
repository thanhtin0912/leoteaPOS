<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home_model extends CI_Model {
	private $module = 'stores';
	private $tbl_stores				= 'stores';
	private $tbl_products			= 'products';
	private $tbl_cate				= 'categories';
	private $tbl_customer			= 'customers';
	private $tbl_order				= 'orders';
	private $tbl_toppings				= 'toppings';

	function getStores(){
		$this->db->select('id, name, description, image, longitude, latitude');
		$this->db->where('status',1);
		$this->db->where('delete',0);
		$this->db->order_by('id','ASC');
		$this->db->from(PREFIX.$this->tbl_stores);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	function getStoresId($id){
		$this->db->select('id, name');
		$this->db->where('status',1);
		$this->db->where('delete',0);
		$this->db->where('id',$id);
		$this->db->order_by('id','ASC');
		$this->db->from(PREFIX.$this->tbl_stores);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	function getCategories($type){ 
		$this->db->select('id, image, name');
		$this->db->where('status',1);
		$this->db->where('delete',0);
		$this->db->where('type',$type);
		$this->db->order_by('id','ASC');
		$this->db->from(PREFIX.$this->tbl_cate);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	function getProducts(){
		$this->db->select('*');
		$this->db->where('status',1);
		$this->db->where('delete',0);
		$this->db->order_by('id','ASC');
		$this->db->from(PREFIX.$this->tbl_products);
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

	function getListToppingProduct($array){
		$this->db->select('id, name, price');
		$this->db->where('`id` in ('. implode(',', $array) . ')');
		$this->db->from(PREFIX.$this->tbl_toppings);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}


	function saveCustomer(){
		$checkPhoneNumber = $this->checkPhoneNumber($_POST['phone']);
		if($checkPhoneNumber){
		    $data = array(
				'phone'=> $_POST['phone'],
				'name'=> $_POST['name'],
				'email'=> $_POST['email'],
				'address'=> $_POST['addresses'],
				'lastlogin'=> date('Y-m-d H:i:s',time())
			);
			$this->db->where('phone', $_POST['phone']);
			if($this->db->update(PREFIX.$this->tbl_customer,$data)){
				return true;
			}
		} else {
			$data = array(
				'phone'=> $_POST['phone'],
				'name'=> $_POST['name'],
				'email'=> $_POST['email'],
				'address'=> $_POST['addresses'],
				'lastlogin'=> date('Y-m-d H:i:s',time()),
				'created'=> date('Y-m-d H:i:s',time())
			);
			if($this->db->insert(PREFIX.$this->tbl_customer,$data)){
				return true;
			}
		}
		return false;
	}

	function checkPhoneNumber($phone){
		$this->db->select('*');
		$this->db->where('phone',$phone);
		$this->db->where('status',1);
		$this->db->where('delete',0);
		$this->db->limit(1);
		$query = $this->db->get(PREFIX.$this->tbl_customer);
		if($query->result()){
			return true;
		}else{
			return false;
		}
	}

	function saveOrder(){
		$data = array(
			'customerId'=> $_POST['customer'],
			'data'=> $_POST['data'],
			'storeId'=> $_POST['store'],
			'status'=> 0,
			'created'=> date('Y-m-d H:i:s',time()),
		);
		if($this->db->insert(PREFIX.$this->tbl_order,$data)){
			return true;
		} else {
			return false;
		}
	}
	function getOrderHistoryCustomer($phone){
		$this->db->select('data, storeId, status, created');
		$this->db->where('customerId', $phone);
		$this->db->where('delete',0);
		$this->db->order_by('created','DESC');
		$this->db->from(PREFIX.$this->tbl_order);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}

	function getInfoCustomer($phone){
	    $checkPhoneNumber = $this->checkPhoneNumber($phone);
	    if ($checkPhoneNumber) {
	        $this->db->select('phone, name, email, address, lastlogin');
    		$this->db->where('phone', $phone);
    		$this->db->where('status',1);
    		$this->db->where('delete',0);
    		$this->db->from(PREFIX.$this->tbl_customer);
    		$query = $this->db->get();
    		if($query->result()){
    			return $query->result();
    		}else{
    			return false;
    		} 
	    } else { 
	        	$data = array(
				'phone'=> $phone,
				'name'=> $phone,
				'email'=> '',
				'address'=> '',
				'lastlogin'=> date('Y-m-d H:i:s',time()),
				'created'=> date('Y-m-d H:i:s',time())
			);
			if($this->db->insert(PREFIX.$this->tbl_customer,$data)){
			    $newUser = $this->getInfoCustomer($phone);
				return $newUser;
			}
	    }
		
	}

	function getOrderForStore($stores){
		$this->db->select('data, total, customerId, created');
		$this->db->where('storeId', $stores);
		$this->db->where('status',0);
		$this->db->where('delete',0);
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