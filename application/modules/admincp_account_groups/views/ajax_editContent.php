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
	if(form.nameAdmincp.value == ''){
		$('#txt_error').html('Please enter information.');
		show_perm_denied();
		return false;
	}
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
	
	if(responseText[0]=='error-group-exists'){
		$('#txt_error').html('Group already exists.');
		show_perm_denied();
		$('#usernameAdmincp').focus();
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
							<label class="control-label col-md-2">Status: </label>
							<div class="col-md-10">
								<label class="radio-inline"><input type="radio" name="statusAdmincp" value="0" <?= isset($result->status) ? $result->status == 0 ? 'checked' : '' : '' ?> > Blocked</label>
								<label class="radio-inline"><input type="radio" name="statusAdmincp" value="1" <?= isset($result->status) ? $result->status == 1 ? 'checked' : '' : 'checked' ?> > Approved</label>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">Name: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-10"><input value="<?php if(isset($result->name)) { print $result->name; }else{ print '';} ?>" type="text" name="nameAdmincp" class="form-control"/></div>
						</div>
						<div class="form-group last">
							<label class="control-label col-md-2">Permission: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-10">
								<div class="gr_perm">
									<div class="title_perm">Default</div>
									<div class="content_perm">
										<div class="gr_item_perm">
											<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,2,3)=='---'){ ?>checked="checked"<?php }}else{ ?>checked="checked"<?php } ?> type="checkbox" onClick="chk_perm(0,'no_access')" name="noaccess0Admincp" id="noaccess0" /></div>
											<div class="item_perm_label">No Access</div>
										</div>
										
										<div class="gr_item_perm">
											<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,2,1)=='r'){ print 'checked="checked"'; }} ?> class="perm_access0" type="checkbox" onClick="chk_perm(0,'read')" name="read0Admincp" id="read0" /></div>
											<div class="item_perm_label">Read</div>
										</div>
										
										<div class="gr_item_perm">
											<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,3,1)=='w'){ print 'checked="checked"'; }} ?> class="perm_access0" type="checkbox" onClick="chk_perm(0,'write')" name="write0Admincp" id="write0" /></div>
											<div class="item_perm_label">Edit</div>
										</div>
										
										<div class="gr_item_perm">
											<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,4,1)=='d'){ print 'checked="checked"'; }} ?> class="perm_access0" type="checkbox" onClick="chk_perm(0,'delete')" name="delete0Admincp" id="delete0" /></div>
											<div class="item_perm_label">Delete</div>
										</div>
									</div>
								</div>
								
								<?php
									if($list_modules){
										foreach($list_modules as $v){
											if(isset($result->permission)){
												$pos = strpos($result->permission,','.$v->id.'|');
												if($pos!=0){
													$pos = $pos + strlen($v->id);
												}else{
													$pos = 0;
												}
											}
											
											if($v->id != 1 && $v->id != 2 && $v->id != 3 && $v->id != 4){
								?>
								<div class="gr_perm">
									<div class="title_perm"><?=$v->name?></div>
									<div class="content_perm">
										<div class="gr_item_perm">
											<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,$pos+2,3)=='---'){ ?>checked="checked"<?php }}else{ ?>checked="checked"<?php } ?> type="checkbox" onClick="chk_perm(<?=$v->id?>,'no_access')" name="noaccess<?=$v->id?>Admincp" id="noaccess<?=$v->id?>" /></div>
											<div class="item_perm_label">No Access</div>
										</div>
										
										<div class="gr_item_perm">
											<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,$pos+2,1)=='r'){ print 'checked="checked"'; }} ?> type="checkbox" onClick="chk_perm(<?=$v->id?>,'read')" class="perm_access<?=$v->id?>" name="read<?=$v->id?>Admincp" id="read<?=$v->id?>" /></div>
											<div class="item_perm_label">Read</div>
										</div>
										
										<div class="gr_item_perm">
											<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,$pos+3,1)=='w'){ print 'checked="checked"'; }} ?> type="checkbox" onClick="chk_perm(<?=$v->id?>,'write')" class="perm_access<?=$v->id?>" name="write<?=$v->id?>Admincp" id="write<?=$v->id?>" /></div>
											<div class="item_perm_label">Edit</div>
										</div>
										
										<div class="gr_item_perm">
											<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,$pos+4,1)=='d'){ print 'checked="checked"'; }} ?> type="checkbox" onClick="chk_perm(<?=$v->id?>,'delete')" class="perm_access<?=$v->id?>" name="delete<?=$v->id?>Admincp" id="delete<?=$v->id?>" /></div>
											<div class="item_perm_label">Delete</div>
										</div>
									</div>
								</div>
								<?php }elseif($id==1){ ?>
								<div class="gr_perm">
									<div class="title_perm"><?=$v->name?></div>
									<div class="content_perm">
										<div class="gr_item_perm">
											<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,$pos+2,3)=='---'){ ?>checked="checked"<?php }}else{ ?>checked="checked"<?php } ?> type="checkbox" onClick="chk_perm(<?=$v->id?>,'no_access')" name="noaccess<?=$v->id?>Admincp" id="noaccess<?=$v->id?>" /></div>
											<div class="item_perm_label">No Access</div>
										</div>
										
										<div class="gr_item_perm">
											<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,$pos+2,1)=='r'){ print 'checked="checked"'; }} ?> type="checkbox" onClick="chk_perm(<?=$v->id?>,'read')" class="perm_access<?=$v->id?>" name="read<?=$v->id?>Admincp" id="read<?=$v->id?>" /></div>
											<div class="item_perm_label">Read</div>
										</div>
										
										<div class="gr_item_perm">
											<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,$pos+3,1)=='w'){ print 'checked="checked"'; }} ?> type="checkbox" onClick="chk_perm(<?=$v->id?>,'write')" class="perm_access<?=$v->id?>" name="write<?=$v->id?>Admincp" id="write<?=$v->id?>" /></div>
											<div class="item_perm_label">Edit</div>
										</div>
										
										<div class="gr_item_perm">
											<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,$pos+4,1)=='d'){ print 'checked="checked"'; }} ?> type="checkbox" onClick="chk_perm(<?=$v->id?>,'delete')" class="perm_access<?=$v->id?>" name="delete<?=$v->id?>Admincp" id="delete<?=$v->id?>" /></div>
											<div class="item_perm_label">Delete</div>
										</div>
									</div>
								</div>
								<?php }}} ?>
							</div>
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