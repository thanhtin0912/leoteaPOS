<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admincp_account_groups extends MX_Controller {
	private $module = 'admincp_account_groups';
	private $table = 'admin_nqt_groups';
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
		
		$this->load->model('admincp_modules/admincp_modules_model');
		$list_modules = $this->admincp_modules_model->list_module();
		$data = array(
			'result'=>$result[0],
			'list_modules'=>$list_modules,
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
			//Permission default
			$result_perm = '';
			$default_perm = '0|';
			if($this->input->post('read0Admincp')=='on'){
				$default_perm .= 'r';
			}else{
				$default_perm .= '-';
			}
			if($this->input->post('write0Admincp')=='on'){
				$default_perm .= 'w';
			}else{
				$default_perm .= '-';
			}
			if($this->input->post('delete0Admincp')=='on'){
				$default_perm .= 'd';
			}else{
				$default_perm .= '-';
			}
			
			//Permission list module
			$this->load->model('admincp_modules/admincp_modules_model');
			$list_modules = $this->admincp_modules_model->list_module();
			$perm = '';
			foreach($list_modules as $v){
				$perm .= ','.$v->id.'|';
				
				if($this->input->post('hiddenIdAdmincp')==1){
					if($this->input->post('read'.$v->id.'Admincp')=='on'){
						$perm .= 'r';
					}else{
						$perm .= '-';
					}
					if($this->input->post('write'.$v->id.'Admincp')=='on'){
						$perm .= 'w';
					}else{
						$perm .= '-';
					}
					if($this->input->post('delete'.$v->id.'Admincp')=='on'){
						$perm .= 'd';
					}else{
						$perm .= '-';
					}
				}else{
					if($v->id!=1 && $v->id!=2 && $v->id!=3 && $v->id!=4){
						if($this->input->post('read'.$v->id.'Admincp')=='on'){
							$perm .= 'r';
						}else{
							$perm .= '-';
						}
						if($this->input->post('write'.$v->id.'Admincp')=='on'){
							$perm .= 'w';
						}else{
							$perm .= '-';
						}
						if($this->input->post('delete'.$v->id.'Admincp')=='on'){
							$perm .= 'd';
						}else{
							$perm .= '-';
						}
					}else{
						$perm .= '---';
					}
				}
			}
			$result_perm = $default_perm.$perm;
			if($this->model->saveManagement($result_perm)){
				if($this->input->post('hiddenIdAdmincp')!=0){
					$this->load->model('admincp_accounts/admincp_accounts_model');
					$list_account = $this->admincp_accounts_model->list_accounts(1,$this->input->post('hiddenIdAdmincp'));
					if($list_account){
						foreach($list_account as $val){
							$this->db->where('id',$val->id);
							$this->db->update('admin_nqt_users',array('permission'=>substr($perm,1)));
						}
					}
				}
				print 'success.'.$this->security->get_csrf_hash();
				exit;
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
			$id = $this->input->post('id');
			$result = $this->model->getDetailManagement($id);
			modules::run('admincp/saveLog',$this->module,$id,'Delete','Delete');
			$this->db->where('id',$id);
			if($this->db->delete($this->table)){
				$this->load->model('admincp_accounts/admincp_accounts_model');
				$list_account = $this->admincp_accounts_model->list_accounts(0,$id);
				if($list_account){
					foreach($list_account as $val){
						$this->db->where('id',$val->id);
						$this->db->delete('admin_nqt_users');
					}
				}
				print $this->security->get_csrf_hash();
				exit;
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
			modules::run('admincp/saveLog',$this->module,$this->input->post('id'),'status','update',$this->input->post('status'),$status);
			$this->db->where('id', $this->input->post('id'));
			if($this->db->update($this->table, $data)){
				$this->load->model('admincp_accounts/admincp_accounts_model');
				$list_account = $this->admincp_accounts_model->list_accounts(1,$this->input->post('id'));
				if($list_account){
					foreach($list_account as $val){
						$this->db->where('id',$val->id);
						$this->db->update('admin_nqt_users',array('status'=>$status));
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