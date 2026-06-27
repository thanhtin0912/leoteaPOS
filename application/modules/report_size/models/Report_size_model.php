<?php
class Report_size_model extends CI_Model {
	private $module = 'report_size';
	private $table = 'categories';
	private $table_order = 'orders';
	private $table_user = 'users';
	private $table_store = 'stores';

	function getsearchContent(){
		$name = $this->input->post('name');
		$this->db->select('n.detailcart');
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
			$this->db->like('n.fullname', $this->input->post('name'));
		}
		if($this->input->post('description')!=''){
			$this->db->where('n.payment', $this->input->post('description'));
		}
		if($this->input->post('content')!=''){
			$this->db->like('n.detailcart', $this->input->post('content'));
		}
		if($this->input->post('url')!=''){
			$this->db->where('n.codecoupon', $this->input->post('url'));
		}
		if($this->input->post('ischeck')!=2){
			if($this->input->post('ischeck')==1){
				$this->db->where('n.codecoupon !=', '');
				$this->db->where('n.codecoupon IS NOT NULL', null, false);
			}else{
				$this->db->group_start();
				$this->db->where('n.codecoupon', '');
				$this->db->or_where('n.codecoupon IS NULL', null, false);
				$this->db->group_end();
			}
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
		$this->db->from(PREFIX.$this->table_order." n");
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
			$this->db->like('n.fullname', $this->input->post('name'));
		}
		if($this->input->post('description')!=''){
			$this->db->where('n.payment', $this->input->post('description'));
		}
		if($this->input->post('content')!=''){
			$this->db->like('n.detailcart', $this->input->post('content'));
		}
		if($this->input->post('url')!=''){
			$this->db->where('n.codecoupon', $this->input->post('url'));
		}
		if($this->input->post('ischeck')!=2){
			if($this->input->post('ischeck')==1){
				$this->db->where('n.codecoupon !=', '');
				$this->db->where('n.codecoupon IS NOT NULL', null, false);
			}else{
				$this->db->group_start();
				$this->db->where('n.codecoupon', '');
				$this->db->or_where('n.codecoupon IS NULL', null, false);
				$this->db->group_end();
			}
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
		$this->db->from(PREFIX.$this->table_order." n");
		$this->db->join(PREFIX.$this->table_user." u", 'n.phone = u.phone', "left");
		$this->db->join(PREFIX.$this->table_store." s", 'u.storeId = s.id', "left");
		$query = $this->db->count_all_results();
		if($query > 0){
			return $query;
		}else{
			return false;
		}
	}
	function getListSize(){
		$this->db->select('*');
		$this->db->where('status',1);
		$this->db->where('delete',0);
		$this->db->where('type', 'PRODUCTSIZE');
		$this->db->order_by('created','DESC');
		$query = $this->db->get(PREFIX.$this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function getDetailManagement($id){
		$this->db->select('*');
		$this->db->where('id',$id);
		$query = $this->db->get(PREFIX.$this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function saveManagement($fileName=''){
		if($this->input->post('hiddenIdAdmincp')==0){
			//Kiểm tra đã tồn tại chưa?
			$checkData = $this->checkData($this->input->post('titleAdmincp'));
			if($checkData){
				print 'error-title-exists.'.$this->security->get_csrf_hash();
				exit;
			}
			
			$checkSlug = $this->checkSlug($this->input->post('slugAdmincp'));
			if($checkSlug){
				print 'error-slug-exists.'.$this->security->get_csrf_hash();
				exit;
			}
			$data = array(
				'title'=> trim($this->input->post('titleAdmincp')),
				'slug'=> trim($this->input->post('slugAdmincp')),
				'content'=> trim($this->input->post('contentAdmincp')),
				'status'=> ($this->input->post('statusAdmincp')),
				'created'=> date('Y-m-d H:i:s',time()),
			);
			if($this->db->insert(PREFIX.$this->table,$data)){
				modules::run('admincp/saveLog',$this->module,$this->db->insert_id(),'Add new','Add new');
				return true;
			}
		}else{
			$result = $this->getDetailManagement($this->input->post('hiddenIdAdmincp'));
			//Kiểm tra đã tồn tại chưa?
			if($result[0]->title!=$this->input->post('titleAdmincp')){
				$checkData = $this->checkData($this->input->post('titleAdmincp'),$this->input->post('hiddenIdAdmincp'));
				if($checkData){
					print 'error-title-exists.'.$this->security->get_csrf_hash();
					exit;
				}
			}
			
			if($result[0]->slug!=$this->input->post('slugAdmincp')){
				$checkSlug = $this->checkSlug($this->input->post('slugAdmincp'),$this->input->post('hiddenIdAdmincp'));
				if($checkSlug){
					print 'error-slug-exists.'.$this->security->get_csrf_hash();
					exit;
				}
			}
			
			$data = array(
				'title'=> trim($this->input->post('titleAdmincp')),
				'slug'=> trim($this->input->post('slugAdmincp')),
				'content'=> trim($this->input->post('contentAdmincp')),
				'status'=> ($this->input->post('statusAdmincp')),
				'created'=> date('Y-m-d H:i:s',time()),
			);
			modules::run('admincp/saveLog',$this->module,$this->input->post('hiddenIdAdmincp'),'','Update',$result,$data);
			$this->db->where('id',$this->input->post('hiddenIdAdmincp'));
			if($this->db->update(PREFIX.$this->table,$data)){
				return true;
			}
		}
		return false;
	}
	
	function checkData($title,$id=0){
		$this->db->select('id');
		$this->db->where('title',$title);
		if($id!=0){
			$this->db->where_not_in('id',array($id));
		}
		$this->db->limit(1);
		$query = $this->db->get(PREFIX.$this->table);

		if($query->result()){
			return true;
		}else{
			return false;
		}
	}
	
	function checkSlug($slug,$id=0){
		$this->db->select('id');
		$this->db->where('slug',$slug);
		if($id!=0){
			$this->db->where_not_in('id',array($id));
		}
		$this->db->limit(1);
		$query = $this->db->get(PREFIX.$this->table);

		if($query->result()){
			return true;
		}else{
			return false;
		}
	}
	
	/*----------------------FRONTEND----------------------*/
	function getData($slug=''){
		$this->db->select('*');
		$this->db->where('slug',$slug);
		$this->db->where('status',0);
		$this->db->limit(1);
		$query = $this->db->get(PREFIX.$this->table);
        //echo $this->db->last_query();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}

	function updateContent($slug = "", $content = "") {
		$data = array(
			'content'=> parserHtmlToContent(trim($content)),
		);
		$this->db->where('slug',$slug);
		if($this->db->update(PREFIX.$this->table,$data)){
			return true;
		}	
	}
	/*--------------------END FRONTEND--------------------*/
}