<script type="text/javascript" src="<?=PATH_URL.'assets/js/admin/'?>jquery.slugit.js"></script>
<script type="text/javascript" src="<?=PATH_URL.'assets/editor/scripts/innovaeditor.js'?>"></script>
<script type="text/javascript" src="<?=PATH_URL.'assets/editor/scripts/innovamanager.js'?>"></script>
<script type="text/javascript">

$( document ).ready(function() {
	$(function () {
		$('select[multiple].active.3col').multiselect({
			columns: 4,
			placeholder: 'Chọn cửa hàng',
			search: true,
			searchOptions: {
				'default': 'Tìm kiếm'
			},
			selectAll: true,
		});
	});
});
	
function save(){
	var options = {
		beforeSubmit:  showRequest,  // pre-submit callback 
		success:       showResponse  // post-submit callback 
	};
	$('#frmManagement').ajaxSubmit(options);
}

function showRequest(formData, jqForm, options) {
	var id = '<?= $id; ?>';
	var form = jqForm[0];

	<?php if($id==0){ ?>
        if($('#imageAdmincp').val() == ''){
            $('#txt_error').html('Please choose image.');
            show_perm_denied();
            return false;
        }
    <?php } ?>

	if(form.cateAdmincp.value == '' || form.nameAdmincp.value == ''){
		$('#txt_error').html('Please enter information.');
		show_perm_denied();
		return false;
	}
}

function showResponse(responseText, statusText, xhr, $form) {
	var module_url = '<?=PATH_URL_ADMIN.$module?>';
	responseText = responseText.split(".");
	token_value  = responseText[1];
	$('#csrf_token').val(token_value);
	if(responseText[0]=='success'){
		show_perm_success();
	}

	if(responseText[0]=='redirect'){
		window.location = module_url;
	}
	
	if(responseText[0]=='error-title-exists'){
		$('#txt_error').html('Title already exists.');
		show_perm_denied();
		return false;
	}

	if(responseText[0]=='error-slug-exists'){
		$('#txt_error').html('Slug already exists.');
		show_perm_denied();
		return false;
	}

	if(responseText[0]=='permission-denied'){
		$('#txt_error').html('Permission denied.');
		show_perm_denied();
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
								<label class="radio-inline"><input type="radio" name="statusAdmincp" value="0" <?= isset($result->status) ? $result->status == 0 ? 'checked' : '' : '' ?> > Blocked</label>
								<label class="radio-inline"><input type="radio" name="statusAdmincp" value="1" <?= isset($result->status) ? $result->status == 1 ? 'checked' : '' : 'checked' ?> > Approved</label>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">HighLight:</label>
							<div class="col-md-10">
								<label class="radio-inline"><input type="radio" name="highlightAdmincp" value="0" <?= isset($result->status) ? $result->status == 0 ? 'checked' : '' : '' ?> > Blocked</label>
								<label class="radio-inline"><input type="radio" name="highlightAdmincp" value="1" <?= isset($result->status) ? $result->status == 1 ? 'checked' : '' : 'checked' ?> > Approved</label>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">Categories: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-4">
								<select class="select form-control" data-live-search="true" data-size="8" name="cateAdmincp" id="cateAdmincp">
									<option value="">None</option>
									<?php foreach ($cates as $key => $cate): ?>
										<?php  
											$select = '';
											if (isset($result->type)) {
												if($result->type == $cate->id){
													$select = 'selected="selected"';
												}
											}
										?>
										<option value="<?= $cate->id; ?>" <?= $select; ?> ><?= $cate->name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">Tên món: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-8">
								<input value="<?php if(isset($result->name)) { print $result->name; }else{ print '';} ?>" type="text" name="nameAdmincp" id="nameAdmincp" class="form-control"/>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">Bán chạy:</label>
							<div class="col-md-3">
								<label class="radio-inline"><input type="radio" name="salesAdmincp" value="0" <?= isset($result->sales) ? $result->sales == 0 ? 'checked' : '' : '' ?> > Không</label>
								<label class="radio-inline"><input type="radio" name="salesAdmincp" value="1" <?= isset($result->sales) ? $result->sales == 1 ? 'checked' : '' : 'checked' ?> > Có</label>
							</div>
							<label class="control-label col-md-2">Yêu thích:</label>
							<div class="col-md-3">
								<label class="radio-inline"><input type="radio" name="favoriteAdmincp" value="0" <?= isset($result->favorite) ? $result->favorite == 0 ? 'checked' : '' : '' ?> > Không</label>
								<label class="radio-inline"><input type="radio" name="favoriteAdmincp" value="1" <?= isset($result->favorite) ? $result->favorite == 1 ? 'checked' : '' : 'checked' ?> > Có</label>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">Giá bán: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-8">
								<input value="<?php if(isset($result->price)) { print $result->price; }else{ print '';} ?>" type="text" name="priceAdmincp" id="priceAdmincp" class="form-control"/>
							</div>
						</div>
						<?php if(isset($result->price_size)) { 
							$priceSize = unserialize($result->price_size);
						?>
						<div class="form-group">
							<?php foreach ($productSize as $key => $p): ?>
							<label class="control-label col-md-2">Giá bán <?= $p->name?> (+ thêm): <span class="required" aria-required="true">*</span></label>
							<div class="col-md-1">
								<input value="<?= $priceSize[$p->name] ?? ''; ?>" type="text" name="priceSizeAdmincp[<?= $p->name?>]" class="form-control"/>
							</div>
							<?php endforeach; ?>
						</div>

						<?php } else { ?>
						<div class="form-group">
							<?php foreach ($productSize as $key => $p): ?>
							<label class="control-label col-md-2">Giá size <?= $p->name ?> (+ thêm): <span class="required" aria-required="true">*</span></label>
							<div class="col-md-1">
								<input value="" type="text" name="priceSizeAdmincp[<?= $p->name ?>]" class="form-control"/>
							</div>
							<?php endforeach; ?>
						</div>
						<?php } ?>
						
						<div class="form-group">
							<label class="control-label col-md-2">Số lượng tối đa: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-1">
								<input value="<?php if(isset($result->limit_order)) { print $result->limit_order; }else{ print '';} ?>" type="text" name="limitAdmincp" id="limitAdmincp" class="form-control"/>
							</div>
							<label class="control-label col-md-2">Tối đa số topping: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-1">
								<input value="<?php if(isset($result->limit_topping)) { print $result->limit_topping; }else{ print '';} ?>" type="text" name="limitToppingAdmincp" id="limitToppingAdmincp" class="form-control"/>
							</div>
							<label class="control-label col-md-2">Mặc định size hiển thị: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-1">
								<select class="select form-control" data-live-search="true" data-size="8" name="is_sizeAdmincp" id="is_sizeAdmincp">
									<option value="">None</option>
									<?php foreach ($productSize as $key => $z): ?>
										<?php  
											$select = '';
											if (isset($result->is_size)) {
												if($result->is_size == $z->name){
													$select = 'selected="selected"';
												}
											}
										?>
										<option value="<?= $z->name; ?>" <?= $select; ?> ><?= $z->name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">Topping cho sản phẩm: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-8">
								<select class="3col active" multiple="multiple" name="toppingsAdmincp[]" >
									<?php foreach ($cate_toppings as $key => $cate_topping): ?>
									<optgroup label="<?php echo $cate_topping->name; ?>">
									<?php foreach ($toppings as $key => $topping): ?>
										<?php if ($cate_topping->id == $topping ->type) { ?>
											<?php  
												$select = '';
												if (isset($result->toppings)) {
													$toppingSelected = unserialize($result->toppings);
													if(in_array($topping->id, $toppingSelected)){
														$select = 'selected="selected"';
													}
												}
												echo $select
											?>
											<option value="<?= $topping->id; ?>" <?= $select; ?> ><?= $topping->name; ?></option>
										<?php } ?>
									<?php endforeach; ?>
									</optgroup>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">Hình ảnh: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-3">
								<div class="fileinput fileinput-new" data-provides="fileinput">
									<?php if(isset($result->image)){ if($result->image!=''){ ?>
									<div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
										<img src="<?=resizeImage(PATH_URL.DIR_UPLOAD_PRODUCT.$result->image,150, 150)?>" />
									</div>
									<?php }} ?>
									<div class="input-group input-large">
										<div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
											<i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
											</span>
										</div>
										<span class="input-group-addon btn default btn-file">
										<span class="fileinput-new">
										Select file </span>
										<span class="fileinput-exists">
										Change </span>
										<input type="file" id="imageAdmincp" name="fileAdmincp[image]">
										</span>
										<a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
										Remove </a>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">Mô tả sản phẩm:</label>
							<div class="col-md-10">
								<textarea rows="4" name="contentAdmincp" class="form-control"><?php if(isset($result->content)) { print $result->content; }else{ print '';} ?></textarea>
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