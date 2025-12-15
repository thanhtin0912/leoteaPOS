<script type="text/javascript">

function save(){
	var options = {
		beforeSubmit:  showRequest,  // pre-submit callback 
		success:       showResponse  // post-submit callback 
    };
	$('#frmManagement').ajaxSubmit(options);
}

function showRequest(formData, jqForm, options) {
	var form = jqForm[0];

	if(form.phoneAdmincp.value != ''){
		$("#phoneAdmincp").val($("#phoneAdmincp").val().replace(/\s\s/g, ''));
		$("#passAdmincp_acc").val($("#passAdmincp_acc").val().replace(/\s/g, ''));

		$("#phoneAdmincp").val($("#phoneAdmincp").val().replace(/[<\!\>\?\@\#\$\%\^\&\*\"\'\[\]\{\}\=\𠮷\𠀋\𡈽\𠮟\𣘺\𡌛\𡢽\𡸴\𣜿]/g, ''));
	}
	if(form.phoneAdmincp.value == ' '){
		$('#txt_error').html('Please enter phone number.');
		show_perm_denied();
		return false;
	}
	if(form.phoneAdmincp.value == ''){
		$('#txt_error').html('Please enter phone number!!!');
		$('#loader').fadeOut(300);
		show_perm_denied();
		return false;
	}
	<?php if($id==0){ ?>
	if(form.passAdmincp.value == ''){
		$('#txt_error').html('Please enter Password !!!');
		$('#loader').fadeOut(300);
		show_perm_denied();
		return false;
	}
	<?php } ?>
}

function showResponse(responseText, statusText, xhr, $form) {
	responseText = responseText.split(".");
	token_value  = responseText[1];
	$('#csrf_token').val(token_value);
	if(responseText[0]=='success'){
		show_perm_success();
	}

	if(responseText[0]=='permission-denied'){
		$('#txt_error').html('Permission denied.');
		show_perm_denied();
		return false;
	}
	
	if(responseText[0]=='error-phone-exists'){
		$('#txt_error').html('Phone already exists.');
		show_perm_denied();
		$('#phoneAdmincp').focus();
		return false;
	}
}

</script>
<!-- BEGIN PAGE HEADER-->
<h3 class="page-title"><?=$this->session->userdata('Name_Module')?></h3>
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li><i class="fa fa-home"></i><a href="<?=PATH_URL_ADMIN?>">Home</a><i class="fa fa-angle-right"></i></li>
		<li><a href="<?=PATH_URL_ADMIN.$module?>"><?=$this->session->userdata('Name_Module')?></a><i class="fa fa-angle-right"></i></li>
		<li><?php ($this->uri->segment(4)=='') ? print 'Add new' : print 'Edit' ?></li>
	</ul>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-md-12">
		<!-- BEGIN EXAMPLE TABLE PORTLET-->
		<div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption">
                    <i class="icon-paper-plane font-green-haze"></i>
                    <span class="caption-subject bold font-green-haze uppercase">Form Input</span>
                </div>
			</div>
			
			<div class="portlet-body form">
				<div class="form-body notification" style="display:none">
					<div class="alert alert-success" style="display:none">
						<strong>Success!</strong> The page has been saved.
					</div>
					
					<div class="alert alert-danger" style="display:none">
						<strong>Error!</strong> <span id="txt_error"></span>
					</div>
				</div>
				
				<!-- BEGIN FORM-->
				<form id="frmManagement" action="<?=PATH_URL_ADMIN.$module.'/save/'?>" method="post" enctype="multipart/form-data" class="form-horizontal form-row-seperated">
					<input type="hidden" value="<?=$this->security->get_csrf_hash()?>" id="csrf_token" name="csrf_token" />
					<input type="hidden" value="<?=$id?>" name="hiddenIdAdmincp" />
					<div class="form-body">
						<div class="form-group">
							<label class="control-label col-md-2">Status:</label>
							<div class="col-md-10">
								<label class="radio-inline"> <input type="radio" name="statusAdmincp" value="0" <?php if(isset($result->status)){ if($result->status== 0){ ?>checked="checked"<?php }}else{ ?>checked <?php } ?> > Block </label>
								<label class="radio-inline"> <input type="radio" name="statusAdmincp" value="1" <?php if(isset($result->status)){ if($result->status== 1){ ?>checked="checked"<?php }}else{ ?>checked <?php } ?> > Approved </label>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">Group: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-4">
								<select class="select form-control" data-live-search="true" data-size="8" name="storeAdmincp" id="storeAdmincp">
									<option value="">None</option>
									<?php foreach ($cates as $key => $cate): ?>
										<?php  
											$select = '';
											if (isset($result->storeId)) {
												if($result->storeId == $cate->id){
													$select = 'selected="selected"';
												}
											}
										?>
										<option value="<?= $cate->id; ?>" <?= $select; ?> ><?= $cate->name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<!-- <div class="form-group">
							<label class="control-label col-md-2">Chỉ đăng nhập Kiểm tra:</label>
							<div class="col-md-3">
								<label class="radio-inline"><input type="radio" name="isCheckAdmincp" value="0" <?= isset($result->isCheck) ? $result->isCheck == 0 ? 'checked' : '' : 'checked' ?> > Không</label>
								<label class="radio-inline"><input type="radio" name="isCheckAdmincp" value="1" <?= isset($result->isCheck) ? $result->isCheck == 1 ? 'checked' : '' : '' ?> > Có</label>
							</div>
							<?php if ($id) { ?>
							<label class="control-label col-md-2">Đăng xuất thiết bị:</label>
							<div class="col-md-3">
								<label class="radio-inline"><input type="radio" name="logoutAdmincp" value="0" checked> Không</label>
								<label class="radio-inline"><input type="radio" name="logoutAdmincp" value="1"> Có</label>
							</div>
							<?php } ?>
						</div> -->
						<div class="form-group">
							<label class="control-label col-md-2">Số điện thoại: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-10"><input value="<?php if(isset($result->phone)) { print $result->phone; }else{ print '';} ?>" type="text" name="phoneAdmincp" id="phoneAdmincp" class="form-control"/></div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">Password: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-10"><input value="" type="password" name="passAdmincp" id="passAdmincp_acc" class="form-control"/></div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">Họ tên: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-10"><input value="<?php if(isset($result->name)) { print $result->name; }else{ print '';} ?>" type="text" name="nameAdmincp" id="nameAdmincp" class="form-control"/></div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">Giờ vào ca: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-4">
								<div class="input-group">
									<input type="text" class="form-control timepicker timepicker-24" value="<?php if(isset($result->in)) { print $result->in; }else{ print '';} ?>" name="checkInAdmincp" id="checkInAdmincp">
									<span class="input-group-btn">
										<button class="btn default" type="button">
											<i class="fa fa-clock-o"></i>
										</button>
									</span>
								</div>
							</div>
							<label class="control-label col-md-2">Giờ ra ca: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-4">
								<div class="input-group">
									<input type="text" class="form-control timepicker timepicker-24" value="<?php if(isset($result->out)) { print $result->out; }else{ print '';} ?>" name="checkOutAdmincp" id="checkOutAdmincp">
									<span class="input-group-btn">
										<button class="btn default" type="button">
											<i class="fa fa-clock-o"></i>
										</button>
									</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">Email: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-10"><input value="<?php if(isset($result->email)) { print $result->email; }else{ print '';} ?>" type="text" name="emailAdmincp" id="emailAdmincp" class="form-control"/></div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">Địa chỉ: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-10"><input value="<?php if(isset($result->address)) { print $result->address; }else{ print '';} ?>" type="text" name="addressAdmincp" id="addressAdmincp" class="form-control"/></div>
						</div>

					</div>
					<div class="form-actions">
						<div class="row">
							<div class="col-md-offset-2 col-md-9">
								<button onclick="save()" type="button" class="btn green"><i class="fa fa-pencil"></i> Save</button>
								<a href="<?=PATH_URL_ADMIN.$module.'/#/back'?>"><button type="button" class="btn default">Cancel</button></a>
							</div>
						</div>
					</div>
				</form>
				<!-- END FORM-->
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<!-- END PAGE CONTENT-->