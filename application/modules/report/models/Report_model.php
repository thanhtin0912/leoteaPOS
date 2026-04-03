<?php
class Report_model extends CI_Model {
	private $module = 'report';
	private $table = 'orders';
	private $table_store = 'stores';
	private $table_user = 'users';
	private $table_shift = 'shift';

	function getsearchContent($limit,$page){
		$name = $this->input->post('name');
		$this->db->select('n.*, s.name as storeName');
		$this->db->limit($limit,$page);
		$this->db->order_by('n.delete','ASC');
		$this->db->order_by('n.'.$this->input->post('func_order_by'),$this->input->post('order_by'));
		if($this->input->post('title')!=''){
			$this->db->like('n.orderId', $this->input->post('title'));
		}
		if($this->input->post('cate_name')!=''){
			$this->db->like('s.id', $this->input->post('cate_name'));
		}
		if($this->input->post('username')!=''){
			$this->db->where('n.phone', $this->input->post('username'));
		}
		if($this->input->post('name')!=''){
			// Sử dụng group_start để cô lập điều kiện Search
			$this->db->like('n.fullname', $this->input->post('name'));
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')==''){
			$this->db->where('n.created >= "'.date('Y-m-d 00:00:01',strtotime($this->input->post('dateFrom'))).'"');
		}
		if($this->input->post('dateFrom')=='' && $this->input->post('dateTo')!=''){
			$this->db->where('n.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')!=''){
			$this->db->where('n.created >= "'.date('Y-m-d 00:00:01',strtotime($this->input->post('dateFrom'))).'"');
			$this->db->where('n.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('status')!= 2){
			$this->db->where('n.status', $this->input->post('status'));
		}
		if($this->input->post('showData') != 2) {
			$this->db->where('n.delete', $this->input->post('showData'));
		}
		$this->db->group_by('n.id');
		$this->db->from(PREFIX.$this->table." n");
		$this->db->join(PREFIX.$this->table_user." u", 'n.phone = u.phone', "left");
		$this->db->join(PREFIX.$this->table_store." s", 'u.storeId = s.id', "left");
		$query = $this->db->get();

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function getTotalsearchContent(){
		$name = $this->input->post('name');
		$this->db->select('n.*, s.name as storeName');
		if($this->input->post('title')!=''){
			$this->db->like('n.orderId', $this->input->post('title'));
		}
		if($this->input->post('cate_name')!=''){
			$this->db->like('s.id', $this->input->post('cate_name'));
		}
		if($this->input->post('username')!=''){
			$this->db->where('n.phone', $this->input->post('username'));
		}
		if($this->input->post('name')!=''){
			// Sử dụng group_start để cô lập điều kiện Search
			$this->db->like('n.fullname', $this->input->post('name'));
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')==''){
			$this->db->where('n.created >= "'.date('Y-m-d 00:00:01',strtotime($this->input->post('dateFrom'))).'"');
		}
		if($this->input->post('dateFrom')=='' && $this->input->post('dateTo')!=''){
			$this->db->where('n.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')!=''){
			$this->db->where('n.created >= "'.date('Y-m-d 00:00:01',strtotime($this->input->post('dateFrom'))).'"');
			$this->db->where('n.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('status')!= 2){
			$this->db->where('n.status', $this->input->post('status'));
		}
		if($this->input->post('showData') != 2) {
			$this->db->where('n.delete', $this->input->post('showData'));
		}
		$this->db->from(PREFIX.$this->table." n");
		$this->db->join(PREFIX.$this->table_user." u", 'n.phone = u.phone', "left");
		$this->db->join(PREFIX.$this->table_store." s", 'u.storeId = s.id', "left");
		$query = $this->db->count_all_results();
		if($query > 0){
			return $query;
		}else{
			return false;
		}
	}

	function getTotalsearchPrice(){
		$name = $this->input->post('name');
		$this->db->select_sum('n.grandtotal');
		if($this->input->post('title')!=''){
			$this->db->like('n.orderId', $this->input->post('title'));
		}
		if($this->input->post('cate_name')!=''){
			$this->db->like('s.id', $this->input->post('cate_name'));
		}
		if($this->input->post('username')!=''){
			$this->db->where('n.phone', $this->input->post('username'));
		}
		if($this->input->post('name')!=''){
			// Sử dụng group_start để cô lập điều kiện Search
			$this->db->like('n.fullname', $this->input->post('name'));
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')==''){
			$this->db->where('n.created >= "'.date('Y-m-d 00:00:01',strtotime($this->input->post('dateFrom'))).'"');
		}
		if($this->input->post('dateFrom')=='' && $this->input->post('dateTo')!=''){
			$this->db->where('n.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')!=''){
			$this->db->where('n.created >= "'.date('Y-m-d 00:00:01',strtotime($this->input->post('dateFrom'))).'"');
			$this->db->where('n.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}

		$this->db->where('n.status', 1);
		$this->db->from(PREFIX.$this->table." n");
		$this->db->join(PREFIX.$this->table_user." u", 'n.phone = u.phone', "left");
		$this->db->join(PREFIX.$this->table_store." s", 'u.storeId = s.id', "left");
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}

	function getDataExport(){
		$this->db->select('n.*, s.name as storeName');
		$this->db->order_by('n.created','ASC');
		if($this->input->post('title')!=''){
			$this->db->like('n.orderId', $this->input->post('title'));
		}
		if($this->input->post('cate_name')!=''){
			$this->db->like('s.id', $this->input->post('cate_name'));
		}
		if($this->input->post('username')!=''){
			$this->db->where('n.phone', $this->input->post('username'));
		}
		if($this->input->post('name')!=''){
			$this->db->like('n.fullname', $this->input->post('name'));
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')==''){
			$this->db->where('n.created >= "'.date('Y-m-d 00:00:01',strtotime($this->input->post('dateFrom'))).'"');
		}
		if($this->input->post('dateFrom')=='' && $this->input->post('dateTo')!=''){
			$this->db->where('n.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')!=''){
			$this->db->where('n.created >= "'.date('Y-m-d 00:00:01',strtotime($this->input->post('dateFrom'))).'"');
			$this->db->where('n.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('status')!= 2){
			$this->db->where('n.status', $this->input->post('status'));
		}
		if($this->input->post('showData') != 2) {
			$this->db->where('n.delete', $this->input->post('showData'));
		}
		$this->db->group_by('n.id');
		$this->db->from(PREFIX.$this->table." n");
		$this->db->join(PREFIX.$this->table_user." u", 'n.phone = u.phone', "left");
		$this->db->join(PREFIX.$this->table_store." s", 'u.storeId = s.id', "left");
		$query = $this->db->get();

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function getDetailManagement($id){
		$this->db->select('o.*, s.name as storeName');
		$this->db->where('o.id',$id);
		$this->db->from(PREFIX.$this->table." o");
		$this->db->join(PREFIX.$this->table_user." u", 'o.phone = u.phone', "left");
		$this->db->join(PREFIX.$this->table_store." s", 's.id = u.storeId', "left");
		$query = $this->db->get();

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function saveManagement($fileName=''){
		if($this->input->post('hiddenIdAdmincp')==0){
			//Kiểm tra đã tồn tại chưa?
			$data = array(
				'url'=> trim($this->input->post('urlAdmincp', true)),
				'image'=> trim($fileName['image']),
				'title'=> trim($this->input->post('titleAdmincp', true)),
				'description'=> trim($this->input->post('descriptionAdmincp', true)),
				'status'=> $this->input->post('statusAdmincp'),
				'created'=> date('Y-m-d H:i:s',time()),
			);
			if($this->db->insert(PREFIX.$this->table,$data)){
				modules::run('admincp/saveLog',$this->module,$this->db->insert_id(),'Add new','Add new');
				return true;
			}
		}else{
			$result = $this->getDetailManagement($this->input->post('hiddenIdAdmincp'));

			//Xử lý xóa hình khi update thay đổi hình
			if($fileName['image']==''){
				$fileName['image'] = $result[0]->image;
			}else{
				@unlink(BASEFOLDER.DIR_UPLOAD_BANNER.$result[0]->image);
			}
			
			$data = array(
				'url'=> trim($this->input->post('urlAdmincp', true)),
				'image'=> trim($fileName['image']),
				'title'=> trim($this->input->post('titleAdmincp', true)),
				'description'=> trim($this->input->post('descriptionAdmincp', true)),
				'status'=> $this->input->post('statusAdmincp')
			);
			modules::run('admincp/saveLog',$this->module,$this->input->post('hiddenIdAdmincp'),'','Update',$result,$data);
			$this->db->where('id',$this->input->post('hiddenIdAdmincp'));
			if($this->db->update(PREFIX.$this->table,$data)){
				return true;
			}
		}
		return false;
	}
	
	/*----------------------FRONTEND----------------------*/
	function getData(){
		$this->db->select('*');
		$this->db->where('status',1);
		$this->db->order_by('created','DESC');
		$query = $this->db->get(PREFIX.$this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	function getDataAll(){
		$this->db->select('1');
		$query = $this->db->count_all_results(PREFIX.$this->table);

		if($query>0){
			return $query;
		}
		else{
			return 0;
		}
	}
	function getDataPublish(){
		$this->db->select('1');
		$this->db->where('delete',0);
		$query = $this->db->count_all_results(PREFIX.$this->table);

		if($query>0){
			return $query;
		}
		else{
			return 0;
		}
	}
	/*--------------------END FRONTEND--------------------*/
}