<?php
class Admincp_accounts_model extends CI_Model {
	private $module = 'admincp_accounts';
	private $table = 'admin_nqt_users';

	function getsearchContent($limit,$page){
		$this->db->select('u.*, g.name as group_name');
		$this->db->join('admin_nqt_groups g','g.id = u.group_id','LEFT');
		if($this->session->userdata('userInfo')!='root'){
			$this->db->where('u.username != "root"');
		}
		$this->db->limit($limit,$page);
		$this->db->order_by('u.delete','ASC');
		$this->db->order_by($this->input->post('func_order_by'),$this->input->post('order_by'));
		if($this->input->post('username')!=''){
			$this->db->where('(u.`username` LIKE "%'.$this->input->post('username').'%")');
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')==''){
			$this->db->where('u.created >= "'.date('Y-m-d 00:00:00',strtotime($this->input->post('dateFrom'))).'"');
		}
		if($this->input->post('dateFrom')=='' && $this->input->post('dateTo')!=''){
			$this->db->where('u.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')!=''){
			$this->db->where('u.created >= "'.date('Y-m-d 00:00:00',strtotime($this->input->post('dateFrom'))).'"');
			$this->db->where('u.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('status') != 2){
			$this->db->where('u.status', $this->input->post('status'));
		}
		if($this->input->post('showData') != 2) {
			$this->db->where('u.delete', $this->input->post('showData'));
		}
		$query = $this->db->get($this->table.' u');

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function getTotalsearchContent(){
		$this->db->select('*');
		$this->db->join('admin_nqt_groups g','g.id = u.group_id','LEFT');
		if($this->session->userdata('userInfo')!='root'){
			$this->db->where('u.username != "root"');
		}
		if($this->input->post('username')!=''){
			$this->db->where('(u.`username` LIKE "%'.$this->input->post('username').'%")');
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')==''){
			$this->db->where('u.created >= "'.date('Y-m-d 00:00:00',strtotime($this->input->post('dateFrom'))).'"');
		}
		if($this->input->post('dateFrom')=='' && $this->input->post('dateTo')!=''){
			$this->db->where('u.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')!=''){
			$this->db->where('u.created >= "'.date('Y-m-d 00:00:00',strtotime($this->input->post('dateFrom'))).'"');
			$this->db->where('u.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('status') != 2){
			$this->db->where('u.status', $this->input->post('status'));
		}
		if($this->input->post('showData') != 2) {
			$this->db->where('u.delete', $this->input->post('showData'));
		}
		$query = $this->db->count_all_results($this->table.' u');

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
	
	function saveManagement($perm=''){
		$arr_perm_group = explode(',',substr($this->input->post('perm_group'),6));
		foreach($arr_perm_group as $v){
			$custom_perm = 0;
			if(strpos($perm,$v)===false){
				$custom_perm = 1;
				break;
			}
		}
		if($this->input->post('hiddenIdAdmincp')==0){
			//Kiểm tra đã tồn tại chưa?
			$checkData = $this->checkData($this->input->post('usernameAdmincp'));
			if($checkData){
				print 'error-username-exists';
				exit;
			}
			
			$data = array(
				'username'=> $this->input->post('usernameAdmincp'),
				'password'=> md5($this->input->post('passAdmincp')),
				'group_id'=> $this->input->post('groupAdmincp'),
				'permission'=> $perm,
				'custom_permission'=> $custom_perm,
				'status'=> $this->input->post('statusAdmincp'),
				'created'=> date('Y-m-d H:i:s',time()),
			);
			if($this->db->insert($this->table,$data)){
				modules::run('admincp/saveLog',$this->module,$this->db->insert_id(),'Add new','Add new');
				return true;
			}
		}else{
			$result = $this->getDetailManagement($this->input->post('hiddenIdAdmincp'));
			//Kiểm tra đã tồn tại chưa?
			if($result[0]->username!=$this->input->post('usernameAdmincp')){
				$checkData = $this->checkData($this->input->post('usernameAdmincp'));
				if($checkData){
					print 'error-username-exists';
					exit;
				}
			}
			
			if($this->input->post('passAdmincp')==''){
				$pass = $result[0]->password;
			}else{
				$pass = md5($this->input->post('passAdmincp'));
			}
			$data = array(
				'username'=> $this->input->post('usernameAdmincp'),
				'password'=> $pass,
				'group_id'=> $this->input->post('groupAdmincp'),
				'permission'=> $perm,
				'custom_permission'=> $custom_perm,
				'status'=> $this->input->post('statusAdmincp')
			);
			modules::run('admincp/saveLog',$this->module,$this->input->post('hiddenIdAdmincp'),'','Update',$result,$data);
			$this->db->where('id',$this->input->post('hiddenIdAdmincp'));
			if($this->db->update($this->table,$data)){
				return true;
			}
		}
		return false;
	}
	
	function checkData($username){
		$this->db->select('id');
		$this->db->where('username',$username);
		$this->db->limit(1);
		$query = $this->db->get($this->table);

		if($query->result()){
			return true;
		}else{
			return false;
		}
	}
	
	function getData($username){
		$this->db->select('id,permission');
		$this->db->where('username',$username);
		$this->db->limit(1);
		$query = $this->db->get($this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function list_accounts($cus=0,$group=0){
		$this->db->select('*');
		if($cus!=0){
			$this->db->where('custom_permission',0);
		}
		if($group!=0){
			$this->db->where('group_id',$group);
		}
		$this->db->order_by('username','ASC');
		$query = $this->db->get($this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	function getDataAll(){
		$this->db->select('*');
		if($this->session->userdata('userInfo')!='root'){
			$this->db->where('username != "root"');
		}
		$query = $this->db->count_all_results($this->table);

		if($query>0){
			return $query;
		}
		else{
			return 0;
		}
	}
	function getDataPublish(){
		$this->db->select('*');
		if($this->session->userdata('userInfo')!='root'){
			$this->db->where('username != "root"');
		}
		$this->db->where('delete',0);
		$query = $this->db->count_all_results($this->table);

		if($query>0){
			return $query;
		}
		else{
			return 0;
		}
	}
}