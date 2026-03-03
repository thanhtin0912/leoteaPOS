<?php
class Shift_model extends CI_Model {
	private $module = 'shift';
	private $table = 'shift';
	private $table_store = 'stores';

	function getsearchContent($limit,$page){
		$this->db->select('s.*, c.name as storeName');
		$this->db->limit($limit,$page);
		$this->db->order_by('s.delete','ASC');
		$this->db->order_by($this->input->post('func_order_by'),$this->input->post('order_by'));
		if($this->input->post('title')!=''){
			$this->db->like('s.name', $this->input->post('title'));
		}
		if($this->input->post('cate_name')!=''){
			$this->db->like('s.store', $this->input->post('cate_name'));
		}
		if($this->input->post('status')!= 2){
			$this->db->where('s.status', $this->input->post('status'));
		}
		if($this->input->post('showData') != 2) {
			$this->db->where('s.delete', $this->input->post('showData'));
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')==''){
			$this->db->where('s.created >= "'.date('Y-m-d 00:00:01',strtotime($this->input->post('dateFrom'))).'"');
		}
		if($this->input->post('dateFrom')=='' && $this->input->post('dateTo')!=''){
			$this->db->where('s.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')!=''){
			$this->db->where('s.created >= "'.date('Y-m-d 00:00:01',strtotime($this->input->post('dateFrom'))).'"');
			$this->db->where('s.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		$this->db->from(PREFIX.$this->table." s");
		$this->db->join(PREFIX.$this->table_store." c", 'c.id = s.store', "left");
		$query = $this->db->get();

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function getTotalsearchContent(){
		$this->db->select('s.*');
		if($this->input->post('title')!=''){
			$this->db->like('s.name', $this->input->post('title'));
		}
		if($this->input->post('cate_name')!=''){
			$this->db->like('s.store', $this->input->post('cate_name'));
		}
		if($this->input->post('status')!= 2){
			$this->db->where('s.status', $this->input->post('status'));
		}
		if($this->input->post('showData') != 2) {
			$this->db->where('s.delete', $this->input->post('showData'));
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')==''){
			$this->db->where('s.created >= "'.date('Y-m-d 00:00:01',strtotime($this->input->post('dateFrom'))).'"');
		}
		if($this->input->post('dateFrom')=='' && $this->input->post('dateTo')!=''){
			$this->db->where('s.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')!=''){
			$this->db->where('s.created >= "'.date('Y-m-d 00:00:01',strtotime($this->input->post('dateFrom'))).'"');
			$this->db->where('s.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		$this->db->from(PREFIX.$this->table." s");
		$this->db->join(PREFIX.$this->table_store." c", 'c.id = s.store', "left");
		$query = $this->db->count_all_results();

		if($query > 0){
			return $query;
		}else{
			return false;
		}
	}

	function getTotalsearchSum(){
		$this->db->select_sum('s.sales', 'sales');
		$this->db->select_sum('s.spent', 'spent');
		$this->db->select_sum('s.tip', 'tip');
		$this->db->select_sum('s.diff', 'diff');
		$this->db->select("COUNT(IF(s.diff < 0 , 1, NULL)) as total_diff", FALSE);

		if($this->input->post('title')!=''){
			$this->db->like('s.name', $this->input->post('title'));
		}
		if($this->input->post('cate_name')!=''){
			$this->db->like('s.store', $this->input->post('cate_name'));
		}
		if($this->input->post('status')!= 2){
			$this->db->where('s.status', $this->input->post('status'));
		}
		if($this->input->post('showData') != 2) {
			$this->db->where('s.delete', $this->input->post('showData'));
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')==''){
			$this->db->where('s.created >= "'.date('Y-m-d 00:00:01',strtotime($this->input->post('dateFrom'))).'"');
		}
		if($this->input->post('dateFrom')=='' && $this->input->post('dateTo')!=''){
			$this->db->where('s.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		if($this->input->post('dateFrom')!='' && $this->input->post('dateTo')!=''){
			$this->db->where('s.created >= "'.date('Y-m-d 00:00:01',strtotime($this->input->post('dateFrom'))).'"');
			$this->db->where('s.created <= "'.date('Y-m-d 23:59:59',strtotime($this->input->post('dateTo'))).'"');
		}
		$this->db->from(PREFIX.$this->table." s");
		$this->db->join(PREFIX.$this->table_store." c", 'c.id = s.store', "left");
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}

	
	function getDetailManagement($id){
		$this->db->select('s.*, c.name as storeName');
		$this->db->where('s.id',$id);
		$this->db->from(PREFIX.$this->table." s");
		$this->db->join(PREFIX.$this->table_store." c", 'c.id = s.store', "left");
		$query = $this->db->get();
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	
	/*----------------------FRONTEND----------------------*/
	function getData(){
		$this->db->select('*');
		$this->db->where('status',1);
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