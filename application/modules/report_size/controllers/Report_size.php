<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_size extends MX_Controller {

	private $module = 'report_size';

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
		$this->session->set_userdata('report_size_first_load', 1);
		$default_func = 'created';
		$default_sort = 'DESC';
		$this->load->model('stores/stores_model');
		$data = array(
			'stores' => $this->stores_model->getData(),
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
			if($this->db->delete(PREFIX.$this->table)){
				print $this->security->get_csrf_hash();
				exit;
			}
		}
	}
	
	public function admincp_ajaxLoadContent(){
		$this->load->library('AdminPagination');
		$isInitialLoad = $this->isInitialSizeViewLoad();
		$config['total_rows'] = $isInitialLoad ? 0 : $this->model->getTotalsearchContent();
		$config['per_page'] = $this->input->post('per_page');
		$config['num_links'] = 3;
		$config['func_ajax'] = 'searchContent';
		$config['start'] = $this->input->post('start');
		$this->adminpagination->initialize($config);

		// mình cần đếm tất cả ly có size theo danh sách bên dưới đc ko

		$dataOrder = $isInitialLoad ? [] : $this->model->getsearchContent();

		$listSize = $this->model->getListSize();
		$sizeCounter = $this->buildEmptySizeCounter($listSize);
		if(!$isInitialLoad){
			$sizeCounter = $this->countCupBySize($dataOrder, $listSize);
		}
		$resultSize = $this->buildSizeResult($listSize, $sizeCounter);
		$data = array(
			'result'=>$resultSize,
			'per_page'=>$this->input->post('per_page'),
			'start'=>$this->input->post('start'),
			'module'=>$this->module,
			'total'=>$config['total_rows']
		);
		$this->session->set_userdata('start',$this->input->post('start'));
		$this->load->view('ajax_loadContent',$data);
	}

	private function buildSizeResult($listSize, $sizeCounter){
		$result = array();

		if(!$listSize){
			return $result;
		}

		foreach($listSize as $size){
			$sizeName = isset($size->name) ? trim((string)$size->name) : '';
			if($sizeName === ''){
				continue;
			}

			$result[] = (object) array(
				'id' => isset($size->id) ? (int)$size->id : 0,
				'name' => $sizeName,
				'total_cup' => isset($sizeCounter[$sizeName]) ? (int)$sizeCounter[$sizeName] : 0,
			);
		}

		return $result;
	}

	private function buildEmptySizeCounter($listSize){
		$counter = array();
		if($listSize){
			foreach($listSize as $size){
				$sizeName = isset($size->name) ? trim((string)$size->name) : '';
				if($sizeName !== ''){
					$counter[$sizeName] = 0;
				}
			}
		}

		return $counter;
	}

	private function isInitialSizeViewLoad(){
		$isFirstLoad = (int)$this->session->userdata('report_size_first_load') === 1;
		if($isFirstLoad){
			$this->session->set_userdata('report_size_first_load', 0);
			return true;
		}

		$textFilters = array('title', 'cate_name', 'username', 'name', 'description', 'content', 'dateFrom', 'dateTo', 'url');
		foreach($textFilters as $filter){
			if(trim((string)$this->input->post($filter)) !== ''){
				return false;
			}
		}

		$ischeck = $this->input->post('ischeck');
		if($ischeck !== null && $ischeck !== '' && (string)$ischeck !== '2'){
			return false;
		}

		$status = $this->input->post('status');
		if($status !== null && $status !== '' && (string)$status !== '2'){
			return false;
		}

		$showData = $this->input->post('showData');
		if($showData !== null && $showData !== '' && (string)$showData !== '2'){
			return false;
		}

		return true;
	}

	private function countCupBySize($dataOrder, $listSize){
		$counter = $this->buildEmptySizeCounter($listSize);

		if(!$dataOrder){
			return $counter;
		}

		foreach($dataOrder as $order){
			if(!isset($order->detailcart) || $order->detailcart === ''){
				continue;
			}

			$cartDetails = @unserialize($order->detailcart);
			if($cartDetails === false && $order->detailcart !== 'b:0;'){
				continue;
			}

			if(is_object($cartDetails)){
				$cartDetails = array($cartDetails);
			}

			if(!is_array($cartDetails)){
				continue;
			}

			foreach($cartDetails as $item){
				$sizeName = '';
				if(is_object($item) && isset($item->size)){
					$sizeName = trim((string)$item->size);
				}elseif(is_array($item) && isset($item['size'])){
					$sizeName = trim((string)$item['size']);
				}

				if($sizeName === ''){
					continue;
				}

				$amount = 1;
				if(is_object($item) && isset($item->amount) && is_numeric($item->amount)){
					$amount = (int)$item->amount;
				}elseif(is_array($item) && isset($item['amount']) && is_numeric($item['amount'])){
					$amount = (int)$item['amount'];
				}

				if($amount < 0){
					$amount = 0;
				}

				if(array_key_exists($sizeName, $counter)){
					$counter[$sizeName] += $amount;
				}
			}
		}

		return $counter;
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
		$this->load->view('ajax_updateStatus',$update);
	}
	/*------------------------------------ End Admin Control Panel --------------------------------*/
    /*------------------------------------ FRONTEND ------------------------------------*/
    public function index(){
        $data['result'] = $this->model->getData();
        $this->template->write('title','Thủ tục thực hiện');
        $this->template->write_view('content','index',$data);
        $this->template->render();
    }

    public function ajaxUpdateContent(){
		$this->load->model('Static_pages/Static_pages_model');
		if($this->input->post('slug') && $this->input->post('content')){
			if($this->Static_pages_model->updateContent($this->input->post('slug'), $this->input->post('content'))){
				print 'success.'.$this->security->get_csrf_hash();
				exit;
			}
		}
		print 'error.'.$this->security->get_csrf_hash();
		exit;
	}

	public function ajaxConvertImage(){
		$image = base64_to_png($this->input->post('img'));
		print $image;
		exit;
	}

	public function test(){
		$check = preg_match("/data:image\/jpeg;base64,/", "data:image/jpeg;base64,/9j/4AAQSkZJRg");
		if($check) {
			echo "oki";
		}
		exit;
	}
    /*------------------------------------ End FRONTEND --------------------------------*/
}