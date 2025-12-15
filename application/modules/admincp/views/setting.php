<script type="text/javascript">
function save(){
	var options = {
		beforeSubmit:  showRequest,
		success:       showResponse  // post-submit callback 
    };
	$('#frmManagement').ajaxSubmit(options);
}

function showRequest(formData, jqForm, options) {
	var form = jqForm[0];
}

function showResponse(responseText, statusText, xhr, $form) {
	responseText = responseText.split(".");
	token_value  = responseText[1];
	$('#csrf_token').val(token_value);
	if(responseText[0]=='success-setting'){
		show_perm_success();
	}
}
</script>
<!-- BEGIN PAGE HEADER-->
<div class="gr_perm_error" style="display:none;">
	<p><strong>FAILURE: </strong>Permission Denied.</p>
</div>
<h3 class="page-title">Setting</h3>
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li><i class="fa fa-home"></i><a href="<?=PATH_URL_ADMIN?>">Home</a><i class="fa fa-angle-right"></i></li>
		<li>Setting</li>
	</ul>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-md-12">
		<!-- BEGIN EXAMPLE TABLE PORTLET-->
		<div class="portlet box grey-cascade">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-globe"></i>System Setting
				</div>
			</div>
			
			<div class="portlet-body form">
				<div class="form-body notification" style="display:none">
					<div class="alert alert-success" style="display:none">
						<strong>Success!</strong> The page has been saved.
					</div>
					
					<div class="alert alert-danger" style="display:none">
						<p><strong>FAILURE: </strong>Timeout only number.</p>
					</div>
				</div>
				
				<!-- BEGIN FORM-->
				<form id="frmManagement" action="<?=PATH_URL_ADMIN.'setting/'?>" method="post" enctype="multipart/form-data" class="form-horizontal form-row-seperated">
					<input type="hidden" value="<?=$this->security->get_csrf_hash()?>" id="csrf_token" name="csrf_token" />
					<div class="form-body">
						<div class="form-group">
							<input type="hidden" value="mail-admin" name="hd_slugAdmincp[]" />
							<label class="control-label col-md-3">Mail admin </label>
							<div class="col-md-9"><input class="form-control" value="<?php if(isset($setting[0]->content)){ print $setting[0]->content; }else{ print''; } ?>" type="text" name="contentAdmincp[]" /></div>
						</div>
						
						<div class="form-group">
							<input type="hidden" value="name-mail-admin" name="hd_slugAdmincp[]" />
							<label class="control-label col-md-3">Name mail admin </label>
							<div class="col-md-9"><input class="form-control" value="<?php if(isset($setting[1]->content)){ print $setting[1]->content; }else{ print''; } ?>" type="text" name="contentAdmincp[]" /></div>
						</div>
						
						<div class="form-group">
							<input type="hidden" value="subject" name="hd_slugAdmincp[]" />
							<label class="control-label col-md-3">Subject </label>
							<div class="col-md-9"><input class="form-control" value="<?php if(isset($setting[2]->content)){ print $setting[2]->content; }else{ print''; } ?>" type="text" name="contentAdmincp[]" /></div>
						</div>
						
						<div class="form-group">
							<input type="hidden" value="seo-title" name="hd_slugAdmincp[]" />
							<label class="control-label col-md-3">SEO Title </label>
							<div class="col-md-9"><input class="form-control" value="<?php if(isset($setting[3]->content)){ print $setting[3]->content; }else{ print''; } ?>" type="text" name="contentAdmincp[]" /></div>
						</div>
						
						<div class="form-group">
							<input type="hidden" value="seo-meta-keyword" name="hd_slugAdmincp[]" />
							<label class="control-label col-md-3">SEO Meta keyword </label>
							<div class="col-md-9"><input class="form-control" value="<?php if(isset($setting[4]->content)){ print $setting[4]->content; }else{ print''; } ?>" type="text" name="contentAdmincp[]" /></div>
						</div>
						
						<div class="form-group">
							<input type="hidden" value="seo-meta-description" name="hd_slugAdmincp[]" />
							<label class="control-label col-md-3">SEO Meta description </label>
							<div class="col-md-9"><input class="form-control" value="<?php if(isset($setting[5]->content)){ print $setting[5]->content; }else{ print''; } ?>" type="text" name="contentAdmincp[]" /></div>
						</div>
					</div>
					<div class="form-actions">
						<div class="row">
							<div class="col-md-offset-3 col-md-9">
								<button onclick="save()" type="button" class="btn green"><i class="fa fa-pencil"></i> Save</button>
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