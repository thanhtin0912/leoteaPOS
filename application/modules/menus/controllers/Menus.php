<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menus extends MX_Controller {

	private $module = 'menus';
	private $table = 'menus';
	function __construct(){
		parent::__construct();
		$this->load->model($this->module.'_model','model');
		$this->load->model('admincp_modules/admincp_modules_model');
		if($this->uri->segment(1)=='admincp'){
			if($this->uri->segment(2)!='login'){
				if(!$this->session->userdata('userInfo')){
					header('Location: '.PATH_URL_ADMIN.'login');
					return false;
				}
				$get_module = $this->admincp_modules_model->check_modules($this->uri->segment(2));
				$this->session->set_userdata('ID_Module',$get_module[0]->id);
				$this->session->set_userdata('Name_Module',$get_module[0]->name);
			}
			$this->template->set_template('admin');
			$this->template->write('title','Admin Control Panel');
		}
	}
	/*------------------------------------ Admin Control Panel ------------------------------------*/
	public function admincp_index(){
		modules::run('admincp/chk_perm',$this->session->userdata('ID_Module'),'r',0);
		$default_func = 'created';
		$default_sort = 'DESC';
		$data = array(
			'module'=>$this->module,
			'module_name'=>$this->session->userdata('Name_Module'),
			'default_func'=>$default_func,
			'default_sort'=>$default_sort
		);
		$this->template->write_view('content','BACKEND/index',$data);
		$this->template->render();
	}
	
	
	public function admincp_update($id=0){
		if($id==0){
			modules::run('admincp/chk_perm',$this->session->userdata('ID_Module'),'w',0);
		}else{
			modules::run('admincp/chk_perm',$this->session->userdata('ID_Module'),'r',0);
		}
		$result[0] = array();
		if($id!=0){
			$result = $this->model->getDetailManagement($id);
		}
		$data = array(
			'menus'=>$this->model->getParent(),
			'result'=>$result[0],
			'module'=>$this->module,
			'id'=>$id
		);
		$this->template->write_view('content','BACKEND/ajax_editContent',$data);
		$this->template->render();
	}

	public function admincp_save(){
		$perm = modules::run('admincp/chk_perm',$this->session->userdata('ID_Module'),'w',1);
		if($perm=='permission-denied'){
			print $perm.'.'.$this->security->get_csrf_hash();
			exit;
		}
		if($_POST){
			if($this->model->saveManagement()){
				if($this->input->post('hiddenIdAdmincp')==0){
					print 'redirect.'.$this->security->get_csrf_hash();
					exit;
				}
				else {
					print 'success.'.$this->security->get_csrf_hash();
					exit;
				}
			}
		}
	}
	
	public function admincp_count($target = 2){
		modules::run('admincp/chk_perm',$this->session->userdata('ID_Module'),'r',0);
		$sumAll = $this->model->getDataAll();
		$sumPublish = $this->model->getDataPublish();
		$data = array(
			'totalAll'=>$sumAll,
			'totalPublish'=>$sumPublish,
			'target'=>$target,
		);
		$this->load->view('BACKEND/ajax_loadCount',$data);
	}
	
	public function admincp_restore(){
		$perm = modules::run('admincp/chk_perm',$this->session->userdata('ID_Module'),'d',1);
		if($perm=='permission-denied'){
			print $perm;
			exit;
		}
		if($this->input->post('id')){
			$data = array(
				'delete' => 0
			);
			$id = $this->input->post('id');
			$result = $this->model->getDetailManagement($id);
			$parentDels = $this->model->getParentDel();
			$listParents = array();
			if($parentDels != null){
				foreach ($parentDels as $key => $parentDel) {
					// pr($parentDel->id);
					if(!in_array($parentDel->id, $listParents)){
						array_push($listParents, $parentDel->id);
					}
				}
			}
			foreach ($result as $key => $value) {
				if($value->delete == 1){
					if($listParents != null){
						if(in_array($value->parent_id, $listParents)){
							print 'permission-denied.'.$this->security->get_csrf_hash();
							exit;
						}
						else{
							modules::run('admincp/saveLog',$this->module,$id,'Restore','Restore');
							$this->db->where('id',$id);
							if($this->db->update(PREFIX.$this->table,$data)){
								// $this->db->where('parent_id',$value->id);
								// $this->db->update(PREFIX.$this->table,$data);
								print $this->security->get_csrf_hash();
								exit;
							}
						}
					}
				}
				else{
					print $this->security->get_csrf_hash();
					exit;
				}
			}
		}
	}
	
	public function admincp_delete(){
		$perm = modules::run('admincp/chk_perm',$this->session->userdata('ID_Module'),'d',1);
		if($perm=='permission-denied'){
			print $perm;
			exit;
		}
		if($this->input->post('id')){
			$data = array(
				'delete' => 1
			);
			$id = $this->input->post('id');
			$result = $this->model->getDetailManagement($id);
			foreach ($result as $key => $value) {
				if($value->delete == 0){
					modules::run('admincp/saveLog',$this->module,$id,'Trash','Trash');
					$this->db->where('id',$id);
					if($this->db->update(PREFIX.$this->table,$data)){
						$this->db->where('parent_id',$value->id);
						$this->db->update(PREFIX.$this->table,$data);
						print $this->security->get_csrf_hash();
						exit;
					}
				}
				else{
					modules::run('admincp/saveLog',$this->module,$id,'Delete','Delete');
					$this->db->where('id',$id);
					if($this->db->delete(PREFIX.$this->table)){
						$this->db->where('parent_id',$value->id);
						$this->db->delete(PREFIX.$this->table);
						print $this->security->get_csrf_hash();
						exit;
					}
				}
			}
		}
	}
	
	public function admincp_ajaxLoadContent(){
		$this->load->library('AdminPagination');
		$config['total_rows'] = $this->model->getTotalsearchContent();
		$config['per_page'] = $this->input->post('per_page');
		$config['num_links'] = 3;
		$config['func_ajax'] = 'searchContent';
		$config['start'] = $this->input->post('start');
		$this->adminpagination->initialize($config);

		$result = $this->model->getsearchContent($config['per_page'],$this->input->post('start'));
		$data = array(
			'result'=>$result,
			'per_page'=>$this->input->post('per_page'),
			'start'=>$this->input->post('start'),
			'module'=>$this->module,
			'total'=>$config['total_rows']
		);
		$this->session->set_userdata('start',$this->input->post('start'));
		$this->load->view('BACKEND/ajax_loadContent',$data);
	}
	
	public function admincp_ajaxUpdateStatus(){
		$perm = modules::run('admincp/chk_perm',$this->session->userdata('ID_Module'),'w',1);
		if($perm=='permission-denied'){
			print '<script type="text/javascript">show_perm_denied()</script>';
			$status = $this->input->post('status');
			$data = array(
				'status'=>$status
			);
		}else{
			if($this->input->post('status')==0){
				$status = 1;
			}else{
				$status = 0;
			}
			$data = array(
				'status'=>$status
			);
			modules::run('admincp/saveLog',$this->module,$this->input->post('id'),'status','update',$this->input->post('status'),$status);
			$this->db->where('id', $this->input->post('id'));
			$this->db->update(PREFIX.$this->table, $data);
		}
		
		$update = array(
			'status'=>$status,
			'id'=>$this->input->post('id'),
			'module'=>$this->module
		);
		$this->load->view('BACKEND/ajax_updateStatus',$update);
	}

	public function showMenu() {
		$this->load->model('menus/menus_model');
		$data["list_menu"] = $this->menus_model->getMenuParent();
		foreach ($data["list_menu"] as $key => $parent) {
			$parent->list_child = $this->menus_model->getMenuChild($parent->id);
		}
		$this->load->view('FRONTEND/index', $data);
	}
	/*------------------------------------ End Admin Control Panel --------------------------------*/
}