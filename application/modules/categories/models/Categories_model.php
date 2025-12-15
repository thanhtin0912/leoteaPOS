<?php
class Categories_model extends CI_Model {
	private $module = 'categories';
	private $table = 'categories';

	function getsearchContent($limit,$page){
		$this->db->select('*');
		$this->db->limit($limit,$page);
		$this->db->order_by('delete','ASC');
		$this->db->order_by($this->input->post('func_order_by'),$this->input->post('order_by'));
		if($this->input->post('title')!=''){
			$this->db->where('(`name` LIKE "%'.$this->input->post('title').'%")');
		}
		if($this->input->post('slug')!=''){
			$this->db->where('(`slug` LIKE "%'.$this->input->post('url').'%")');
		}
		if($this->input->post('status')!= 2){
			$this->db->where('status', $this->input->post('status'));
		}
		if($this->input->post('showData') != 2) {
			$this->db->where('delete', $this->input->post('showData'));
		}
		$this->db->where('type', 'PRODUCT');
		$query = $this->db->get(PREFIX.$this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function getTotalsearchContent(){
		$this->db->select('*');
		if($this->input->post('title')!=''){
			$this->db->where('(`name` LIKE "%'.$this->input->post('title').'%")');
		}
		if($this->input->post('slug')!=''){
			$this->db->where('(`slug` LIKE "%'.$this->input->post('url').'%")');
		}
		if($this->input->post('status')!= 2){
			$this->db->where('status', $this->input->post('status'));
		}
		if($this->input->post('showData') != 2) {
			$this->db->where('delete', $this->input->post('showData'));
		}
		$this->db->where('type', 'PRODUCT');
		$query = $this->db->count_all_results(PREFIX.$this->table);

		if($query > 0){
			return $query;
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
			$checkName = $this->checkName($this->input->post('nameAdmincp'),0);
			if($checkName){
				print 'error-name-exists.'.$this->security->get_csrf_hash();
				exit;
			}
			//check slug exist
			$checkSlug = $this->checkSlug($this->input->post('slugAdmincp'),0);
			if($checkSlug){
				print 'error-slug-exists.'.$this->security->get_csrf_hash();
				exit;
			}
			$data = array(
				'name'=> trim($this->input->post('nameAdmincp', true)),
				'slug'=> trim($this->input->post('slugAdmincp', true)),
				'image'=> trim($fileName['image']),
				'content'=> trim($this->input->post('contentAdmincp', true)),
				'order'=> trim($this->input->post('orderAdmincp', true)),
				'status'=> $this->input->post('statusAdmincp'),
				'type' => 'PRODUCT',
				'created'=> date('Y-m-d H:i:s',time()),
			);
			if($this->db->insert(PREFIX.$this->table,$data)){
				modules::run('admincp/saveLog',$this->module,$this->db->insert_id(),'Add new','Add new');
				return true;
			}
		}else{
			$result = $this->getDetailManagement($this->input->post('hiddenIdAdmincp'));
			if($result[0]->name!=$this->input->post('nameAdmincp')){
				$checkName = $this->checkName($this->input->post('nameAdmincp'),$this->input->post('hiddenIdAdmincp'));
				if($checkName){
					print 'error-name-exists.'.$this->security->get_csrf_hash();
					exit;
				}
			}
			//Xử lý xóa hình khi update thay đổi hình
			if($fileName['image']==''){
				$fileName['image'] = $result[0]->image;
			}else{
				@unlink(BASEFOLDER.DIR_UPLOAD_CATE.$result[0]->image);
			}
			
			$data = array(
				'name'=> trim($this->input->post('nameAdmincp', true)),
				'slug'=> trim($this->input->post('slugAdmincp', true)),
				'image'=> trim($fileName['image']),
				'content'=> trim($this->input->post('contentAdmincp', true)),
				'order'=> trim($this->input->post('orderAdmincp', true)),
				'status'=> $this->input->post('statusAdmincp'),
				'type' => 'PRODUCT'
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
		$this->db->where('type', 'PRODUCT');
		$this->db->order_by('created','ASC');
		$query = $this->db->get(PREFIX.$this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}

	function getDataToSelect(){
		$this->db->select('*');
		$this->db->where('status',1);
		$this->db->where('type', 'PRODUCT');
		$query = $this->db->get(PREFIX.$this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}

	function getDataAll(){
		$this->db->select('1');
		$this->db->where('type', 'PRODUCT');
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
		$this->db->where('type', 'PRODUCT');
		$this->db->where('delete',0);
		$query = $this->db->count_all_results(PREFIX.$this->table);

		if($query>0){
			return $query;
		}
		else{
			return 0;
		}
	}

	function checkName($name,$id=0){
		$this->db->select('id');
		$this->db->where('name',$name);
		$this->db->where('type', 'PRODUCT');
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
		$this->db->where('type', 'PRODUCT');
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
	
	/*--------------------END FRONTEND--------------------*/
}