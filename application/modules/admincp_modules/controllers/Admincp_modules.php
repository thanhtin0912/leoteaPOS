<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admincp_modules extends MX_Controller {

	private $module = 'admincp_modules';
	private $table = 'admin_nqt_modules';
	function __construct(){
		parent::__construct();
		$this->load->model($this->module.'_model','model');
		if($this->uri->segment(1)=='admincp'){
			if($this->uri->segment(2)!='login'){
				if(!$this->session->userdata('userInfo')){
					header('Location: '.PATH_URL_ADMIN.'login');
					return false;
				}
				$get_module = $this->model->check_modules($this->uri->segment(2));
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
		$default_func = 'status';
		$default_sort = 'ASC';
		$data = array(
			'module'=>$this->module,
			'module_name'=>$this->session->userdata('Name_Module'),
			'default_func'=>$default_func,
			'default_sort'=>$default_sort
		);
		$this->template->write_view('content','index',$data);
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
			'result'=>$result[0],
			'module'=>$this->module,
			'id'=>$id
		);
		$this->template->write_view('content','ajax_editContent',$data);
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
				print 'success.'.$this->security->get_csrf_hash();
				exit;
			}
		}
	}
	
	public function admincp_ajaxLoadContent(){
		$this->load->library('AdminPagination');
		$config['per_page'] = $this->input->post('per_page');
		$config['num_links'] = 3;
		$config['func_ajax'] = 'searchContent';
		$config['start'] = $this->input->post('start');

		$read_dir = scandir(BASEFOLDER.'/application/modules');
		foreach($read_dir as $v){
			if($v!='.' && $v!='..' && $v!='home' && $v!='admincp'){
				if(!$this->model->check_modules($v)){
					$data_module = array(
						'name'=>ucfirst(str_replace('_',' ',$v)),
						'name_function'=>$v,
						'status'=>0,
						'created'=>date('Y-m-d H:i:s')
					);
					$this->db->insert($this->table,$data_module);
				}
			}
		}
		$result = $this->model->getsearchContent($config['per_page'],$this->input->post('start'));
		if($result){
			foreach($result as $v){
				if(!is_dir(BASEFOLDER.'/application/modules/'.$v->name_function)){
					// $this->db->where('id',$v->id);
					// $this->db->delete($this->table);
					$result = $this->model->getsearchContent($config['per_page'],$this->input->post('start'));
				}
			}
		}

		$config['total_rows'] = $this->model->getTotalsearchContent();
		$this->adminpagination->initialize($config);
		$data = array(
			'result'=>$result,
			'per_page'=>$this->input->post('per_page'),
			'start'=>$this->input->post('start'),
			'module'=>$this->module,
			'total'=>count($read_dir)-9
		);
		$this->session->set_userdata('start',$this->input->post('start'));
		$this->load->view('ajax_loadContent',$data);
	}
	
	public function admincp_ajaxUpdateStatus(){
		$perm = modules::run('admincp/chk_perm',$this->session->userdata('ID_Module'),'w',1);
		if($perm=='permission-denied'){
			print '<script type="text/javascript">show_perm_denied()</script>';
			$status = $this->input->post('status');
			$data = array(
				'status'=>$status
			);
			$update = array(
				'status'=>$status,
				'id'=>$this->input->post('id'),
				'module'=>$this->module
			);
			$this->load->view('ajax_updateStatus',$update);
		}else{
			if($this->input->post('status')==0){
				$status = 1;
			}else{
				$status = 0;
			}
			$data = array(
				'status'=>$status
			);
			$this->db->where('id', $this->input->post('id'));
			if($this->db->update($this->table, $data)){
				//Xét quyền cho Group
				$this->load->model('admincp_account_groups/admincp_account_groups_model');
				$list_group = $this->admincp_account_groups_model->list_groups();
				foreach($list_group as $v){
					if($status==0){
						$pos = strpos($v->permission,','.$this->input->post('id').'|');
						if($pos !== false){
							$sub_str = substr($v->permission,$pos,5+strlen($this->input->post('id')));
							$new_perm = str_replace($sub_str,'',$v->permission);
							$this->db->where('id',$v->id);
							$this->db->update('admin_nqt_groups',array('permission'=>$new_perm));
						}
					}else{
						$pos = strpos($v->permission,'0');
						if($pos !== false){
							$new_perm = substr($v->permission,$pos+2,3);
							$this->db->where('id',$v->id);
							$this->db->update('admin_nqt_groups',array('permission'=>$v->permission.','.$this->input->post('id').'|'.$new_perm));
						}
					}
				}
				
				//Xét quyền lại cho từng Users
				$this->load->model('admincp_accounts/admincp_accounts_model');
				$list_account = $this->admincp_accounts_model->list_accounts();
				foreach($list_account as $v){
					if($status==0){
						$pos = strpos($v->permission,','.$this->input->post('id').'|');
						if($pos !== false){
							$sub_str = substr($v->permission,$pos,5+strlen($this->input->post('id')));
							$new_perm = str_replace($sub_str,'',$v->permission);
							$this->db->where('id',$v->id);
							$this->db->update('admin_nqt_users',array('permission'=>$new_perm));
						}
					}else{
						$group_account = $this->admincp_account_groups_model->getDetailManagement($v->group_id);
						$pos = strpos($group_account[0]->permission,'0');
						if($pos !== false){
							$new_perm = substr($v->permission,$pos+2,3);
							$this->db->where('id',$v->id);
							$this->db->update('admin_nqt_users',array('permission'=>$v->permission.','.$this->input->post('id').'|'.$new_perm));
						}
					}
				}
			}
			
			$update = array(
				'status'=>$status,
				'id'=>$this->input->post('id'),
				'module'=>$this->module
			);
			$this->load->view('ajax_updateStatus',$update);
		}
	}
	/*------------------------------------ End Admin Control Panel --------------------------------*/
	
	
	/*------------------------------------ FRONTEND ------------------------------------*/
	
	/*------------------------------------ End FRONTEND --------------------------------*/
}