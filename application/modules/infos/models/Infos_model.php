<?php
class infos_model extends CI_Model {
	private $module = 'infos';
	private $table = 'infos';

	function getsearchContent($limit,$page){
		$this->db->select('*');
		$this->db->limit($limit,$page);
		$this->db->order_by($this->input->post('func_order_by'),$this->input->post('order_by'));
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
		$query = $this->db->get(PREFIX.$this->table);

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function getTotalsearchContent(){
		$this->db->select('*');
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
			//Kiểm tra đã tồn tại chưa?
			$data = array(
				'name'=> trim($this->input->post('nameAdmincp')),
				'phone'=> trim($this->input->post('phoneAdmincp')),
				'phoneother'=> trim($this->input->post('phoneotherAdmincp')),
				'mail'=> trim($this->input->post('mailAdmincp')),
				'facebook'=> trim($this->input->post('facebookAdmincp')),
				'address'=> trim($this->input->post('addressAdmincp')),
				'description'=> trim($this->input->post('descriptionAdmincp')),
				'status'=> ($this->input->post('statusAdmincp')=='on')?1:0,
				'created'=> date('Y-m-d H:i:s',time()),
			);
			if($this->db->insert(PREFIX.$this->table,$data)){
				modules::run('admincp/saveLog',$this->module,$this->db->insert_id(),'Add new','Add new');
				return true;
			}
		}else{
			$result = $this->getDetailManagement($this->input->post('hiddenIdAdmincp'));
			//Kiểm tra đã tồn tại chưa?

			
			$data = array(
				'name'=> trim($this->input->post('nameAdmincp')),
				'phone'=> trim($this->input->post('phoneAdmincp')),
				'phoneother'=> trim($this->input->post('phoneotherAdmincp')),
				'mail'=> trim($this->input->post('mailAdmincp')),
				'facebook'=> trim($this->input->post('facebookAdmincp')),
				'address'=> trim($this->input->post('addressAdmincp')),
				'description'=> trim($this->input->post('descriptionAdmincp')),
				'status'=> 1,
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