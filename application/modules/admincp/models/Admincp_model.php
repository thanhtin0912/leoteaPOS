<?php
class Admincp_model extends CI_Model {

	function checkLogin($user){
		$this->db->select('*');
		$this->db->where('username', $user);
		$this->db->where('status', 1);
		$this->db->where('delete', 0);
		$query = $this->db->get('admin_nqt_users');
		
		foreach ($query->result() as $row){
			$pass = $row->password;
		}
		
		if(!empty($pass)){
			return $pass;
		}else{
			return false;
		}
	}
	
	function getInfo($user){
		$this->db->select('*');
		$this->db->where('username', $user);
		$this->db->where('status', 1);
		$this->db->where('delete', 0);
		$query = $this->db->get('admin_nqt_users');

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function getSetting($slug=''){
		$this->db->select('*');
		if($slug!=''){
			$this->db->where('slug', $slug);
			$this->db->limit(1);
		}
		$query = $this->db->get('admin_nqt_settings');

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function checkSlug($slug){
		$this->db->select('id');
		$this->db->where('slug', $slug);
		$this->db->limit(1);
		$query = $this->db->get('admin_nqt_settings');

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
}