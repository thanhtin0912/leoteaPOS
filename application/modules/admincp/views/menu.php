<?php
if($menu){
	foreach($menu as $v){
		$pos = strpos($perm[0]->permission,','.$v->id.'|');
		if($pos!=0){
			$pos = $pos + strlen($v->id);
		}else{
			$pos = 0;
		}
		if(substr($perm[0]->permission,$pos+2,3)!='---'){
?>
<li class="<?php if($this->uri->segment(2)==$v->name_function){ print 'active'; }?>">
	<a href="<?=PATH_URL_ADMIN.''.$v->name_function.'/'?>">
		<?php switch ($v->name_function) {
			case 'banners':
				echo '<i class="fa fa-picture-o"></i>';
				break;
			case 'categories':
				echo '<i class="fa fa-bars"></i>';
				break;
			case 'admincp_accounts':
				echo '<i class="fa fa-user"></i>';
				break;
			case 'admincp_account_groups':
				echo '<i class="fa fa-group"></i>';
				break;
			case 'admincp_logs':
				echo '<i class="fa fa-history"></i>';
				break;
			case 'admincp_modules':
				echo '<i class="fa fa-th-large"></i>';
				break;
			case 'menus':
				echo '<i class="fa fa-windows"></i>';
				break;
			case 'news':
				echo '<i class="fa fa-newspaper-o"></i>';
				break;
			case 'companies':
				echo '<i class="fa fa-square"></i>';
				break;
			case 'job_recruiments':
				echo '<i class="fa fa-cubes"></i>';
				break;
			case 'members':
				echo '<i class="fa fa-user"></i>';
				break;
			case 'static_pages':
				echo '<i class="fa fa-files-o"></i>';
				break;
			default:
				echo '<i class="icon-docs"></i>';
				break;
		} ?>
		<span class="title"><?=$v->name?></span>
	</a>
</li>
<?php }}} ?>