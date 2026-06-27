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
	private $tbl_shift				= 'shift';

	function getInfoSite(){
		$this->db->select('*');
		$query = $this->db->get(PREFIX.$this->tbl_infor);
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	function getInfoStore($id){
		$this->db->select('*');
		$this->db->where('id', $id);
		$query = $this->db->get(PREFIX.$this->tbl_stores);
		if ($query->num_rows() > 0) {
			return $query->row(); 
		} else {
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
	

	function checkCookie($token){
		$this->db->select('u.*, s.name as storeName, s.code as storeCode, s.address');
		$this->db->where('u.session', $token);
		$this->db->where('u.status', 1);
		$this->db->where('u.delete', 0);
		$this->db->from(PREFIX.$this->tbl_users." u");
		$this->db->join(PREFIX.$this->tbl_stores." s", 'u.storeId = s.id', "left");
		$query = $this->db->get();
		if($query->result()){
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

	function generateInvoiceCode($currentCode, $limit = 25) {
		$letter = substr($currentCode, 0, 1);      // Ký tự A, B, C
		$number = intval(substr($currentCode, 1)); // Số phía sau
		$number++;
		if ($number > $limit) {
			$number = 1;   // Reset lại 01
			$letter = chr(ord($letter) + 1);   // Tăng ký tự A→B→C
		}
	
		return $letter . str_pad($number, 2, '0', STR_PAD_LEFT);
	}

	function addOrder($cart, $total, $shippingTotal = 0){
		//Kiểm tra đã tồn tại chưa?
		$info = $this->session->userdata('userLogin');
		$staffName = $this->session->userdata('staffName');
		if(!$info) return false;
		$num = 'A01';
		$str = (string)($info->storeCode).(string)(date('ymd',time()));
		$findStr = $this->getLastOrderStore($str);
		if($findStr) {
			$lastNo = substr($findStr[0]->orderId, -3);
			$num = $this->generateInvoiceCode($lastNo);
		}
		$orderId = $str.$num;
		$discount = $this->input->post('discount') ? $this->input->post('discount') : 0;
		$grandtotal = (int)$total - (int)$discount + (int)$shippingTotal;
		$data = array(
			'orderId'		=> $orderId,
			'mail'			=> '',
			'fullname'		=> isset($staffName) ? $staffName : '',
			'address'		=> '',
			'region'		=> '',
			'postcode'		=> '',
			'phone'			=> $info->phone,
			'message'		=> $_POST["note"],
			'subtotal'		=> $total,
			'discountmember'=> '',
			'discountcoupon'=> $this->input->post('discount'),
			'codecoupon'	=> $this->input->post('discountCode') ? $this->input->post('discountCode') : '',
			'tax'			=> '',
			'detailcart'	=> serialize($cart),
			'shippingtotal'	=> $shippingTotal,
			'grandtotal'	=> $grandtotal,
			'shipping'		=> $this->input->post('delivery'),
			'fulfillment'	=> ($this->input->post('delivery')) === "Delivery" ? 2 : 1,
			'payment'		=> $this->input->post('payment'),
			'status'		=> 1,
			'delete'		=> 0,
			'created'		=> date('Y-m-d H:i:s',time()),
		);
		if($this->db->insert(PREFIX.$this->tbl_order,$data)){
			// 1. Lấy ID vừa mới tạo
			$insert_id = $this->db->insert_id();
			// 2. Select lại dữ liệu từ ID này
			$query = $this->db->get_where(PREFIX . $this->tbl_order, array('id' => $insert_id));
			// Trả về dòng dữ liệu dưới dạng Object (hoặc xử lý tùy ý bạn)
			return $query->row();
		}
		return false;
	
	}

	function getListfulfillmentOrderStore(){
		$info = $this->session->userdata('userLogin');
		if(!$info) return false;
		$this->db->select('*');
		$this->db->where('phone',$info->phone);
		$this->db->where('delete',0);
		$this->db->where('status',1);
		$this->db->where('fulfillment',2);
		$this->db->order_by('orderId','ASC');
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

	function getOrderCode($key){
		$this->db->select('*');
		$this->db->where('status',1);
		$this->db->where('delete',0);
		$this->db->where('orderId',$key);
		$this->db->order_by('created','DESC');
		$this->db->limit(1);
		$this->db->from(PREFIX.$this->tbl_order);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}

	function getOrder($code) {
		$this->db->select('*');
		$this->db->where('status',1);
		$this->db->where('delete',0);
		$this->db->where('orderId',$code);
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
		if(!$info) return false;
		$this->db->select('*');
		$this->db->where('phone',$info->phone);
		$this->db->where('status',1);
		$this->db->where('delete',0);
		$date = date("Y-m-d H:i:s",time());
		$this->db->where('created >=', date('Y-m-d 00:00:01', strtotime($date)));
		$this->db->where('created <=', date('Y-m-d 23:59:59', strtotime($date)));
		$this->db->order_by('orderId','ASC');
		$this->db->from(PREFIX.$this->tbl_order);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	function checkinShiftDay($thungan, $size_cups) {
		$info = $this->session->userdata('userLogin');
		if(!$info) return false;
		$data=array(
			"from" => date('Y-m-d H:i:s',time()),
			"store"=> $info->storeId,
			"user"=> $info->phone,
			"name"=> $thungan,
			"size_cups"=> $size_cups,
			"created" => date('Y-m-d H:i:s',time())
		);
		if($this->db->insert(PREFIX.$this->tbl_shift,$data)){
			return true;
		}
		return false;
	}

	function updateShiftDay($id, $data){ 
		$this->db->where('id',$id);
		$this->db->update(PREFIX.$this->tbl_shift,$data);  
		return true;
	}

	function checkExsitShiftofDay() {
		$today = date('Y-m-d');
		$info = $this->session->userdata('userLogin');
		if(!$info) return false;
		$this->db->select('*');
		$this->db->where('store',$info->storeId);
		$this->db->where('user',$info->phone);
		$this->db->where('status',1);
		$this->db->where('delete',0);
		$this->db->where('`from` <=', date('Y-m-d H:i:s'));
		$this->db->where('created >=', $today . ' 00:00:00');
		$this->db->where('created <=', $today . ' 23:59:59');
		$this->db->where('to IS NULL');
		$this->db->order_by('id','ASC');
		$this->db->from(PREFIX.$this->tbl_shift);
		$this->db->limit(1);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	
	}

	function getShiftofDay($key, $completed){
		$this->db->select('t.*, s.name as storeName');
		$this->db->where('t.status',1);
		$this->db->where('t.delete',0);
		$this->db->where('t.completed', $completed);
		$this->db->like('t.id',$key);
		$this->db->from(PREFIX.$this->tbl_shift." t");
		$this->db->join(PREFIX.$this->tbl_stores." s", 't.store = s.id', "left");
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}

	function getTotalRevenueShift($from, $to, $user, $payment_type= null) {
		$this->db->select_sum('grandtotal');
		$this->db->where('phone',$user);
		$this->db->where('status',1);
		$this->db->where('delete',0);
		if ($payment_type !== null) {
			$this->db->where('payment', $payment_type);
		}
		// Đảm bảo so sánh chính xác thời gian
		$this->db->where('created >=', $from);
		$this->db->where('created <=', $to);
		$this->db->from(PREFIX . $this->tbl_order);
		$query = $this->db->get();
		$result = $query->row();
		return ($result && $result->grandtotal) ? $result->grandtotal : 0;
	}
	function getTotalOrderShift($from, $to, $user) {
		$this->db->select('*');
		$this->db->where('phone',$user);
		$this->db->where('status',1);
		$this->db->where('delete',0);
		// Đảm bảo so sánh chính xác thời gian
		$this->db->where('created >=', $from);
		$this->db->where('created <=', $to);
		$this->db->from(PREFIX . $this->tbl_order);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	
	}

	function getLastOrderShift($from, $to, $user){
		$this->db->select('orderId');
		$this->db->where('phone',$user);
		$this->db->where('delete',0);
		$this->db->where('created >=', $from);
		$this->db->where('created <=', $to);
		$this->db->order_by('id','DESC');
		$this->db->from(PREFIX.$this->tbl_order);
		$this->db->limit(1);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	
	}

	function getListOrderCancel(){
		$date = date("Y-m-d H:i:s",time());
		$info = $this->session->userdata('userLogin');
		if(!$info) return false;
		$this->db->select('orderId, grandtotal, created, updated');
		$this->db->where('phone',$info->phone);
		$this->db->where('isVerify',1);
		$this->db->where('status',1);
		$this->db->where('delete',0);
		$date = date("Y-m-d H:i:s",time());
		$this->db->where('created >=', date('Y-m-d 00:00:01', strtotime($date)));
		$this->db->where('created <=', date('Y-m-d 23:59:59', strtotime($date)));
		$this->db->order_by('orderId','ASC');
		$this->db->from(PREFIX.$this->tbl_order);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	function getCoupons(){
		$this->db->select('*');
		$this->db->where('status',1);
		$this->db->where('delete',0);
		$this->db->order_by('id','DESC');
		$this->db->from('coupons');
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	function getListOrdersToCancel(){
		$date = date("Y-m-d H:i:s",time());
		$info = $this->session->userdata('userLogin');
		$staffName = $this->session->userdata('staffName');
		if(!$staffName) return false;
		$this->db->select('*');
		$this->db->where('fullname',$staffName);
		$this->db->where('isVerify',1);
		$this->db->where('created >=', date('Y-m-d 00:00:01', strtotime($date)));
		$this->db->where('created <=', date('Y-m-d 23:59:59', strtotime($date)));
		$this->db->order_by('orderId','ASC');
		$this->db->from(PREFIX.$this->tbl_order);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	
	}

	function getListOrdersToPaymentBanking(){
		$date = date("Y-m-d H:i:s",time());
		$staffName = $this->session->userdata('staffName');
		if(!$staffName) return false;
		$this->db->select('*');
		$this->db->where('fullname',$staffName);
		$this->db->where('payment', 2);
		$this->db->where('status', 1);
		$this->db->where('created >=', date('Y-m-d 00:00:01', strtotime($date)));
		$this->db->where('created <=', date('Y-m-d 23:59:59', strtotime($date)));
		//lất theo thời gian từ sớm nhất đến muộn nhất để dễ dàng đối chiếu với lịch sử giao dịch ngân hàng
		$this->db->order_by('created','ASC');	
		$this->db->from(PREFIX.$this->tbl_order);
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	
	}

	function sync_pending_orders() {
		// 1. Lấy danh sách đơn hàng chưa đồng bộ từ Local (giới hạn 20 đơn mỗi lần)
		$this->db->where('is_synced', 0);
		$this->db->limit(20);
		$query = $this->db->get('orders');
		$pending_orders = $query->result_array();
		if (empty($pending_orders)) {
			return "No data to sync";
		}
	
		// 2. Thử kết nối Database Online
		// Dùng @ để ẩn các lỗi cảnh báo kết nối nếu mất mạng
		$DB_online = @$this->load->database('online', TRUE);
	
		if ($DB_online && $DB_online->conn_id) {
			$success_count = 0;
			foreach ($pending_orders as $order) {
				// Tách ID local ra để tránh xung đột với ID tự tăng trên Server Online
				$local_id = $order['id'];
				unset($order['id']); 
				unset($order['is_synced']); 
				$order['is_synced'] = 1;
				// 3. Insert lên Server Online
				if ($DB_online->insert('orders', $order)) {
					// 4. Nếu thành công, cập nhật trạng thái tại Local ngay lập tức
					$this->db->where('id', $local_id);
					$this->db->update('orders', array('is_synced' => 1));
					$success_count++;
				}
			}
	
			$DB_online->close();
			return "Synced $success_count orders successfully.";
		} else {
			return "Server Online is unreachable.";
		}
	}
	function sync_pending_shift() {
		$this->db->where('completed', 0);
		$this->db->limit(20);
		$query = $this->db->get('shift');
		$pending_shift = $query->result_array();
		if (empty($pending_shift)) {
			return "No data to sync";
		}
		// 2. Thử kết nối Database Online
		$DB_online = @$this->load->database('online', TRUE);
		if ($DB_online && $DB_online->conn_id) {
			$success_count = 0;
			foreach ($pending_shift as $shift) {
				$local_id = $shift['id'];
				if($shift['id_online']!=NULL && $shift['id_online'] > 0 && $shift['is_synced'] == 1){
					$id_online = $shift['id_online'];
					unset($shift['id']); 
					unset($shift['id_online']); 
					$shift['completed'] = 1;
					$DB_online->where('id', $id_online);
					$DB_online->update('shift ', $shift);

					$this->db->where('id', $local_id);
					$this->db->update('shift', array('completed' => 1));
					
				} else {
					unset($shift['id']); 
					// 3. Insert lên Server Online
					$shift['is_synced'] = 1;
					unset($shift['id_online']); 
					$insert = $DB_online->insert('shift', $shift);
					if ($insert) {
						$insert_id = $DB_online->insert_id();
						// 4. Nếu thành công, cập nhật trạng thái tại Local ngay lập tức
						$this->db->where('id', $local_id);
						$this->db->update('shift', array('is_synced' => 1, 'id_online' => $insert_id));
						$success_count++; 
					}
				}
				
			}
			$DB_online->close();
			return "Synced $success_count orders successfully.";
		} else {
			return "Server Online is unreachable.";
		}
	}
	function sync_orders_verify() {

		$DB_online = @$this->load->database('online', TRUE);
		// query bảng order_events trên db oline để lấy danh sách đơn hàng đã được xác nhận nhưng chưa đồng bộ về local
		$DB_online->select('*');
		$DB_online->from('order_events');
		$DB_online->where('isSync', 0);
		$DB_online->limit(20);
		$query = $DB_online->get();
		$order_events = $query->result_array();
		if (empty($order_events)) {
			return "No data to sync";
		}
		if ($DB_online && $DB_online->conn_id) {
			foreach ($order_events as $order) {
				if ($this->getOrder($order['orderCode'])) {
					// Nếu chưa tồn tại, cập nhật trạng thái xác nhận vào local
					$dataUpdate = array(
						'status' => 0,
						'isVerify' => 0,
						"updated"=> date('Y-m-d H:i:s',time())
					);
					$this->db->where('orderId', $order['orderCode']);
					if($this->db->update('orders', $dataUpdate)) {
						// Cập nhật trạng thái đã đồng bộ trên Server Online
						$DB_online->where('id', $order['id']);
						$DB_online->update('order_events', array('isSync' => 1));
					}
				}
			}
		} else {
			return "Server Online is unreachable.";
		}
	}

}
?>