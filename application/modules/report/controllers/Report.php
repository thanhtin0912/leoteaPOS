<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends MX_Controller {

	private $module = 'report';
	private $table = 'orders';
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
		$data['module'] = 'admincp';
		$this->load->model('stores/stores_model');
		$data = array(
			'stores' => $this->stores_model->getData(),
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
			'result'=>$result[0],
			'banners'=>$this->model->getData(),
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
			if($this->model->saveManagement($fileName)){
				//End Upload Image
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
						print $this->security->get_csrf_hash();
						exit;
					}
				}
				else{
					modules::run('admincp/saveLog',$this->module,$id,'Delete','Delete');
					$this->db->where('id',$id);
					if($this->db->delete(PREFIX.$this->table)){
						@unlink(BASEFOLDER.DIR_UPLOAD_BANNER.$result[0]->image);
						print $this->security->get_csrf_hash();
						exit;
					}
				}
			}
		}
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
			foreach ($result as $key => $value) {
				if($value->delete == 1){
					modules::run('admincp/saveLog',$this->module,$id,'Restore','Restore');
					$this->db->where('id',$id);
					if($this->db->update(PREFIX.$this->table,$data)){
						print $this->security->get_csrf_hash();
						exit;
					}
				}else{
					print $this->security->get_csrf_hash();
					exit;
				}
			}
		}
	}

	
	public function admincp_ajaxLoadContent(){
		$downloadUrl = "";
		if($this->input->post('export') != 0) {
			$dataExport = $this->model->getDataExport();
			$downloadUrl = $this->export($dataExport);
		}
		$this->load->library('AdminPagination');
		$config['total_rows'] = $this->model->getTotalsearchContent();
		$config['total_price'] = 0;

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
			'total'=>$config['total_rows'],
			'downloadUrl' => $downloadUrl // Truyền URL vào view
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
	/*------------------------------------ End Admin Control Panel --------------------------------*/

	public function export($dataList) {
		$this->load->helper('url');
		// 2. Load thư viện PHPExcel
		require_once APPPATH . "/third_party/PHPExcel.php";
		$objPHPExcel = new PHPExcel();
	
		// 3. Cấu hình các thông tin cơ bản
		$objPHPExcel->getProperties()->setCreator("System")
									 ->setLastModifiedBy("System")
									 ->setTitle("Export Data")
									 ->setSubject("Export Data");
	
		$sheet = $objPHPExcel->setActiveSheetIndex(0);
	
		// 4. Tạo tiêu đề cột (Header) - Dòng 1
		$sheet->setCellValue('A1', 'STT');
		$sheet->setCellValue('B1', 'Mã Hóa Đơn');
		$sheet->setCellValue('C1', 'Ngày Bán');
		$sheet->setCellValue('D1', 'Loại');
		$sheet->setCellValue('E1', 'Khách Hàng');
		$sheet->setCellValue('F1', 'Nhân Viên');
		$sheet->setCellValue('G1', 'Cửa Hàng');
		$sheet->setCellValue('H1', 'Món');
		$sheet->setCellValue('I1', 'Size');
		$sheet->setCellValue('J1', 'Số Lượng');
		$sheet->setCellValue('K1', 'Đơn Giá');
		$sheet->setCellValue('L1', 'Thành Tiền');
		$sheet->setCellValue('M1', 'Phụ Thu');
		$sheet->setCellValue('N1', 'Tiền Mặt');
		$sheet->setCellValue('O1', 'Chuyển Khoản');
		$sheet->setCellValue('P1', 'Doanh Thu');
		$sheet->setCellValue('Q1', 'Doanh Thu Thực');
		// Format cho Header (In đậm)
		$sheet->getStyle('A1:Q1')->applyFromArray(array(
			'font' => array(
				'bold'  => true,
				'color' => array('rgb' => '121212'), // Chữ màu trắng cho nổi trên nền tối
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'startcolor' => array(
					'rgb' => '95B3D7'
				)
			)
		));
		// 5. Đổ dữ liệu vào các dòng tiếp theo
		$rowCount = 2; // Bắt đầu từ dòng 2
		$stt = 1;
		foreach ($dataList as $row) {
			$startDate = date('d/m/Y', strtotime($row->created));
			$sheet->setCellValue('A' . $rowCount, $stt);
			$sheet->setCellValue('B' . $rowCount, $row->orderId);
			$sheet->setCellValue('C' . $rowCount, date('d/m/Y', strtotime($row->created)));
			$sheet->setCellValue('D' . $rowCount, $row->shipping);
			$sheet->setCellValue('E' . $rowCount, "Khách lẻ");
			$sheet->setCellValue('F' . $rowCount, $row->fullname);
			$sheet->setCellValue('G' . $rowCount, $row->storeName);
			$sheet->setCellValue('L' . $rowCount, $row->subtotal);
			$sheet->setCellValue('M' . $rowCount, $row->shippingtotal);
			$sheet->setCellValue('N' . $rowCount, $row->payment==1 ? $row->grandtotal : '');
			$sheet->setCellValue('O' . $rowCount, $row->payment==2 ? $row->grandtotal : '');
			$sheet->setCellValue('P' . $rowCount, $row->grandtotal);
			$sheet->setCellValue('Q' . $rowCount, $row->grandtotal);
			$sheet->getStyle('A'. $rowCount.':Q'. $rowCount)->applyFromArray(array(
				'font' => array(
					'bold'  => true,
					'color' => array('rgb' => 'FFFFFF'), // Chữ màu trắng cho nổi trên nền tối
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'startcolor' => array(
						'rgb' => '4CAF50' // Màu xanh lá (mã HEX)
					)
				)
			));
			$rowCount++;
			$cartDetails = unserialize($row->detailcart);
			if (is_array($cartDetails)) {
				foreach ($cartDetails as $item) {
					// Các thông tin chung của hóa đơn
					$sheet->setCellValue('B' . $rowCount, $row->orderId);
					$sheet->setCellValue('C' . $rowCount, date('d/m/Y', strtotime($row->created)));
					$sheet->setCellValue('D' . $rowCount, $row->shipping);
					$sheet->setCellValue('E' . $rowCount, "Khách lẻ");
					$sheet->setCellValue('F' . $rowCount, $row->fullname);
					$sheet->setCellValue('G' . $rowCount, $row->storeName);
	
					// --- Thông tin CHI TIẾT từng món ---
					$sheet->setCellValue('H' . $rowCount, $item->name);
					$sheet->setCellValue('I' . $rowCount, $item->size);
					$sheet->setCellValue('J' . $rowCount, $item->amount);       
					$sheet->setCellValue('K' . $rowCount, ($item->totalPrice - $item->priceTopping)/$item->amount);  
					// --- Thông tin Tài chính ---
					$sheet->setCellValue('L' . $rowCount, ($item->totalPrice - $item->priceTopping)); // Thành tiền món này (đã gồm topping)
					if ($item->toppings != '' || $item->toppings != NULL) {
						// Tách chuỗi dựa trên dấu phẩy và khoảng trắng
						$items = $item->toppings;
						if (count($items) > 0) {
							$totalItems = count($items);
							$i = 1;
							foreach ($items as $top) {
								$rowCount++;
								$sheet->setCellValue('H' . $rowCount, ' - '.$top->name);
								$sheet->setCellValue('J' . $rowCount, $top->qty * $item->amount);
								$sheet->setCellValue('K' . $rowCount, $top->price);
								$sheet->setCellValue('L' . $rowCount, $top->price * ( $top->qty * $item->amount));
							}							
						} 
					}
					$rowCount++;
				}
			}
			$stt++;
		}

		
		// Auto size cột
		foreach(range('A','Q') as $columnID) {
			$sheet->getColumnDimension($columnID)->setAutoSize(true);
		}

		// Lưu file
		$fileName = 'Export_order_' . date('Ymd_His') . '.xlsx';
		$export_path = DIR_EXPORT_FILES . $fileName;

		try {
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save($export_path);
			
			if (file_exists($export_path)) {
				// Bây giờ bạn có thể dùng base_url() mà không bị lỗi
				return base_url('assets/uploads/export/' . $fileName);
			} 
		} catch (Exception $e) {
			return false;
		}
		return false;
	}

	public function showBanner(){
		$this->load->model('banners/banners_model');
		$data["list_banners"] = $this->banners_model->getData();
		$this->load->view('FRONTEND/showBanner', $data);
	}
}