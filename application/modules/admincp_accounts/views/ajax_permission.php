<?php if($default_perm==''){ ?>
<input type="hidden" value="<?=$result->permission?>" name="perm_group" id="perm_group" />
<?php }else{ ?>
<input type="hidden" value="<?=$default_perm->permission?>" name="perm_group" id="perm_group" />
<?php
}
	if($list_modules){
		foreach($list_modules as $k=>$v){
			if(isset($result->permission)){
				if($k==0){
					$pos = strpos($result->permission,$v->id.'|');
					$pos = $pos - 1;
				}else{
					$pos = strpos($result->permission,','.$v->id.'|');
				}
				if($pos!==false){
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
			<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,$pos+2,3)=='---'){ ?>checked="checked"<?php }}else{ ?>checked="checked"<?php } ?> type="checkbox" onClick="chk_perm(<?=$v->id?>,'no_access')" class="custom_chk" name="noaccess<?=$v->id?>Admincp" id="noaccess<?=$v->id?>" /></div>
			<div class="item_perm_label">No Access</div>
		</div>
		
		<div class="gr_item_perm">
			<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,$pos+2,1)=='r'){ print 'checked="checked"'; }} ?> type="checkbox" onClick="chk_perm(<?=$v->id?>,'read')" class="custom_chk perm_access<?=$v->id?>" name="read<?=$v->id?>Admincp" id="read<?=$v->id?>" /></div>
			<div class="item_perm_label">Read</div>
		</div>
		
		<div class="gr_item_perm">
			<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,$pos+3,1)=='w'){ print 'checked="checked"'; }} ?> type="checkbox" onClick="chk_perm(<?=$v->id?>,'write')" class="custom_chk perm_access<?=$v->id?>" name="write<?=$v->id?>Admincp" id="write<?=$v->id?>" /></div>
			<div class="item_perm_label">Edit</div>
		</div>
		
		<div class="gr_item_perm">
			<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,$pos+4,1)=='d'){ print 'checked="checked"'; }} ?> type="checkbox" onClick="chk_perm(<?=$v->id?>,'delete')" class="custom_chk perm_access<?=$v->id?>" name="delete<?=$v->id?>Admincp" id="delete<?=$v->id?>" /></div>
			<div class="item_perm_label">Delete</div>
		</div>
	</div>
</div>
<?php }elseif($group_id==1){ ?>
<div class="gr_perm">
	<div class="title_perm"><?=$v->name?></div>
	<div class="content_perm">
		<div class="gr_item_perm">
			<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,$pos+2,3)=='---'){ ?>checked="checked"<?php }}else{ ?>checked="checked"<?php } ?> type="checkbox" onClick="chk_perm(<?=$v->id?>,'no_access')" class="custom_chk" name="noaccess<?=$v->id?>Admincp" id="noaccess<?=$v->id?>" /></div>
			<div class="item_perm_label">No Access</div>
		</div>
		
		<div class="gr_item_perm">
			<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,$pos+2,1)=='r'){ print 'checked="checked"'; }} ?> type="checkbox" onClick="chk_perm(<?=$v->id?>,'read')" class="custom_chk perm_access<?=$v->id?>" name="read<?=$v->id?>Admincp" id="read<?=$v->id?>" /></div>
			<div class="item_perm_label">Read</div>
		</div>
		
		<div class="gr_item_perm">
			<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,$pos+3,1)=='w'){ print 'checked="checked"'; }} ?> type="checkbox" onClick="chk_perm(<?=$v->id?>,'write')" class="custom_chk perm_access<?=$v->id?>" name="write<?=$v->id?>Admincp" id="write<?=$v->id?>" /></div>
			<div class="item_perm_label">Edit</div>
		</div>
		
		<div class="gr_item_perm">
			<div class="item_perm_chk"><input <?php if(isset($result->permission)){ if(substr($result->permission,$pos+4,1)=='d'){ print 'checked="checked"'; }} ?> type="checkbox" onClick="chk_perm(<?=$v->id?>,'delete')" class="custom_chk perm_access<?=$v->id?>" name="delete<?=$v->id?>Admincp" id="delete<?=$v->id?>" /></div>
			<div class="item_perm_label">Delete</div>
		</div>
	</div>
</div>
<?php }}} ?>
<script type="text/javascript">
$(document).ready(function(){
	token_value = '<?=$this->security->get_csrf_hash()?>';
	$('#csrf_token').val(token_value);
	widthBoxPerm();
	Metronic.initAjax();
});
</script>