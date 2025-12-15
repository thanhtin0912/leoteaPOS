<script type="text/javascript" src="<?=PATH_URL.'assets/js/admin/'?>jquery.slugit.js"></script>
<script type="text/javascript" src="<?=PATH_URL.'assets/editor/scripts/innovaeditor.js'?>"></script>
<script type="text/javascript" src="<?=PATH_URL.'assets/editor/scripts/innovamanager.js'?>"></script>
<script type="text/javascript">

$( document ).ready(function() {
	// $('input:radio[name=is_multiAdmincp]').change(function () {
	// 	if ($("input[name='is_multiAdmincp']:checked").val() == true) {
	// 		$('#saleableQtyAdmincp').prop("disabled", false);
	// 	} else {
	// 		$('#saleableQtyAdmincp').val('');
	// 		$('#saleableQtyAdmincp').prop("disabled", true);
	// 	}
	// });
	$(function () {
		$('select[multiple].active.3col').multiselect({
			columns: 3,
			placeholder: 'Product name',
			search: true,
			searchOptions: {
				'default': 'Search product'
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
	var form = jqForm[0];

	if(form.cateAdmincp.value == '' || form.nameAdmincp.value == '' || form.priceAdmincp.value == '' ){
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
											if($result->type == $cate->id){
												$select = 'selected="selected"';
											}
										?>
										<option value="<?= $cate->id; ?>" <?= $select; ?> ><?= $cate->name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">Name: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-8">
								<input value="<?php if(isset($result->name)) { print $result->name; }else{ print '';} ?>" type="text" name="nameAdmincp" id="nameAdmincp" class="form-control"/>
							</div>
						</div>
						<div class="form-group last">
							<label class="control-label col-md-2">Price: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-8">
								<input value="<?php if(isset($result->price)) { print $result->price; }else{ print '';} ?>" type="text" name="priceAdmincp" id="priceAdmincp" class="form-control"/>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-2">Được phép thêm số lượng:</label>
							<div class="col-md-3">
								<label class="radio-inline"><input type="radio" name="is_multiAdmincp" value="0" <?= isset($result->isMulti) ? $result->isMulti == 0 ? 'checked' : '' : 'checked' ?> > Không</label>
								<label class="radio-inline"><input type="radio" name="is_multiAdmincp" value="1" <?= isset($result->isMulti) ? $result->isMulti == 1 ? 'checked' : '' : '' ?> > Có</label>
							</div>

							<label class="control-label col-md-2">Limit add quantity: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-3">
								<input value="<?php if(isset($result->saleableQty)) { print $result->saleableQty; }else{ print '';} ?>" type="text" name="saleableQtyAdmincp" id="saleableQtyAdmincp" class="form-control"/>
							</div>
						</div>
						<!-- <div class="form-group">
							<label class="control-label col-md-2">Products: <span class="required" aria-required="true">*</span></label>
							<div class="col-md-8">
								<select class="3col active" multiple="multiple" name="productAdmincp[]" id="productAdmincp">
									<?php $select = ''  ?>
									<?php foreach ($cate_products as $key => $cate_product): ?>
										<optgroup label="<?php echo $cate_product->name; ?>">
											<?php foreach ($products as $key => $product): ?>
												<?php if ($cate_product->id == $product ->type) { ?>
													<?php  
														$select = '';
														if(in_array($product->id, $productsSelect)){
															$select = 'selected="selected"';
														}
														echo $select;	
													?>
												<option value="<?= $product->id; ?>" <?= $select; ?> ><?= $product->name; ?></option>
												<?php } ?>
											<?php endforeach; ?>
										</optgroup>
									<?php endforeach; ?>
								</select>
							</div>
						</div> -->


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