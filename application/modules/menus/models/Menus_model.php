<?php
class Menus_model extends CI_Model {
	private $module = 'menus';
	private $table = 'menus';

	function getsearchContent($limit,$page){
		$this->db->select('m.*, p.name as parent_name');
		$this->db->limit($limit,$page);
		$this->db->order_by('m.delete','ASC');
		$this->db->order_by($this->input->post('func_order_by'),$this->input->post('order_by'));
		if($this->input->post('name')!=''){
			$this->db->where('(m.`name` LIKE "%'.$this->input->post('name').'%")');
		}
		if($this->input->post('url')!=''){
			$this->db->where('(m.`url` LIKE "%'.$this->input->post('url').'%")');
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')==''){
			$this->db->where('m.created >= "'.date('Y-m-d 00:00:00',strtotime($this->input->post('dateFrom'))).'"');
		}
		if($this->input->post('dateFrom')=='' && $this->input->post('dateTo')!=''){
			$this->db->where('m.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')!=''){
			$this->db->where('m.created >= "'.date('Y-m-d 00:00:00',strtotime($this->input->post('dateFrom'))).'"');
			$this->db->where('m.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('status') != 2){
			$this->db->where('m.status', $this->input->post('status'));
		}
		if($this->input->post('showData') != 2) {
			$this->db->where('m.delete', $this->input->post('showData'));
		}
		$this->db->from(PREFIX.$this->table." m");
		$this->db->join(PREFIX.$this->table." p", 'p.id = m.parent_id', "left");
		$query = $this->db->get();

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function getTotalsearchContent(){
		$this->db->select('m.*, p.name as parent_name');
		if($this->input->post('name')!=''){
			$this->db->where('(m.`name` LIKE "%'.$this->input->post('name').'%")');
		}
		if($this->input->post('url')!=''){
			$this->db->where('(m.`url` LIKE "%'.$this->input->post('url').'%")');
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')==''){
			$this->db->where('m.created >= "'.date('Y-m-d 00:00:00',strtotime($this->input->post('dateFrom'))).'"');
		}
		if($this->input->post('dateFrom')=='' && $this->input->post('dateTo')!=''){
			$this->db->where('m.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')!=''){
			$this->db->where('m.created >= "'.date('Y-m-d 00:00:00',strtotime($this->input->post('dateFrom'))).'"');
			$this->db->where('m.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('status') != 2){
			$this->db->where('m.status', $this->input->post('status'));
		}
		if($this->input->post('showData') != 2) {
			$this->db->where('m.delete', $this->input->post('showData'));
		}
		$this->db->from(PREFIX.$this->table." m");
		$this->db->join(PREFIX.$this->table." p", 'p.id = m.parent_id', "left");
		$query = $this->db->count_all_results();

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
	
	function saveManagement(){
		if($this->input->post('hiddenIdAdmincp')==0){
			//Kiểm tra đã tồn tại chưa?
			$checkData = $this->checkData($this->input->post('nameAdmincp'));
			if($checkData){
				print 'error-name-exists.'.$this->security->get_csrf_hash();
				exit;
			}
			$data = array(
				'name'=> trim($this->input->post('nameAdmincp', true)),
				'url'=> trim($this->input->post('urlAdmincp', true)),
				'parent_id'=> trim($this->input->post('parentAdmincp', true)),
				'order'=> trim($this->input->post('orderAdmincp', true)),
				'status'=> $this->input->post('statusAdmincp'),
				'created'=> date('Y-m-d H:i:s',time()),
			);
			if($this->db->insert(PREFIX.$this->table,$data)){
				modules::run('admincp/saveLog',$this->module,$this->db->insert_id(),'Add new','Add new');
				return true;
			}
		}else{
			$result = $this->getDetailManagement($this->input->post('hiddenIdAdmincp'));
			
			//Kiểm tra đã tồn tại chưa?
			if($result[0]->name!=$this->input->post('nameAdmincp')){
				$checkData = $this->checkData($this->input->post('nameAdmincp'),$this->input->post('hiddenIdAdmincp'));
				if($checkData){
					print 'error-name-exists.'.$this->security->get_csrf_hash();
					exit;
				}
			}
			$data = array(
				'name'=> trim($this->input->post('nameAdmincp', true)),
				'url'=> trim($this->input->post('urlAdmincp', true)),
				'parent_id'=> trim($this->input->post('parentAdmincp', true)),
				'order'=> trim($this->input->post('orderAdmincp', true)),
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

	function getParent(){
		$this->db->select('*');
		$this->db->where('status',1);
		$this->db->where('delete',0);
		$this->db->where('parent_id', 0);
		$this->db->order_by('created','DESC');
		$query = $this->db->get(PREFIX.$this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}

	function checkData($name,$id=0){
		$this->db->select('id');
		$this->db->where('name',$name);
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

	function getMenuParent(){
		$this->db->select('*');
		$this->db->where('status',1);
		$this->db->where('parent_id',0);
		$this->db->order_by('order','ASC');
		$query = $this->db->get(PREFIX.$this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}

	function getMenuChild($parent_id){
		$this->db->select('*');
		$this->db->where('status', 1);
		$this->db->where('parent_id', $parent_id);
		$this->db->order_by('order','ASC');
		$query = $this->db->get(PREFIX.$this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	function getParentDel(){
		$this->db->select('id');
		$this->db->where('delete', 1);
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