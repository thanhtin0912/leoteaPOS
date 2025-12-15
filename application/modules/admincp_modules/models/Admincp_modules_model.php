<?php
class Admincp_modules_model extends CI_Model {
	private $table = 'admin_nqt_modules';

	function getsearchContent($limit,$page){
		$this->db->select('*');
		if($this->session->userdata('userInfo')!='root'){
			$this->db->where_not_in('id',array(1,2,3,4));
		}
		$this->db->limit($limit,$page);
		$this->db->order_by($this->input->post('func_order_by'),$this->input->post('order_by'));
		$this->db->order_by('name','ASC');
		if($this->input->post('module_name') != ''){
			$this->db->where('(`name` LIKE "%'.$this->input->post('module_name').'%")');
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')==''){
			$this->db->where('created >= "'.date('Y-m-d 00:00:00',strtotime($this->input->post('dateFrom'))).'"');
		}
		if($this->input->post('dateFrom')=='' && $this->input->post('dateTo')!=''){
			$this->db->where('created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')!=''){
			$this->db->where('created >= "'.date('Y-m-d 00:00:00',strtotime($this->input->post('dateFrom'))).'"');
			$this->db->where('created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('status') != 2){
			$this->db->where('status', $this->input->post('status'));
		}
		$query = $this->db->get($this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function getTotalsearchContent(){
		$this->db->select('*');
		if($this->session->userdata('userInfo')!='root'){
			$this->db->where_not_in('id',array(1,2,3,4));
		}
		if($this->input->post('module_name') != ''){
			$this->db->where('(`name` LIKE "%'.$this->input->post('module_name').'%")');
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')==''){
			$this->db->where('created >= "'.date('Y-m-d 00:00:00',strtotime($this->input->post('dateFrom'))).'"');
		}
		if($this->input->post('dateFrom')=='' && $this->input->post('dateTo')!=''){
			$this->db->where('created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')!=''){
			$this->db->where('created >= "'.date('Y-m-d 00:00:00',strtotime($this->input->post('dateFrom'))).'"');
			$this->db->where('created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('status') != 2){
			$this->db->where('status', $this->input->post('status'));
		}
		$query = $this->db->count_all_results($this->table);

		if($query > 0){
			return $query;
		}else{
			return false;
		}
	}
	
	function getDetailManagement($id){
		$this->db->select('*');
		$this->db->where('id',$id);
		$query = $this->db->get($this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function saveManagement($result_perm=''){
		if($this->input->post('hiddenIdAdmincp')!=0){
			$data = array(
				'name'=> $this->input->post('moduleNameAdmincp'),
				'order'=> $this->input->post('orderAdmincp'),
				'status'=> $this->input->post('statusAdmincp')
			);
			$this->db->where('id',$this->input->post('hiddenIdAdmincp'));
			if($this->db->update($this->table,$data)){
				return true;
			}
		}
		return false;
	}
	
	function check_modules($module){
		$this->db->select('id,name');
		$this->db->where('name_function',$module);
		$query = $this->db->get($this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function list_module($chk_admin=0){
		$this->db->select('*');
		$this->db->where('status',1);
		if($chk_admin!=0 && $this->session->userdata('userInfo')!='root' && $this->session->userdata('userInfo')!='admin'){
			$this->db->where_not_in('id',array(1,2,3,4));
		}
		$this->db->order_by('order','DESC');
		$query = $this->db->get($this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
}