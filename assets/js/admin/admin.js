$(document).ready(function() {
	$('.btn-group').load(root+module+'/count/'+$("#hdshowData").val());
	$('.btn-danger').click(function(){
		// alert();
		if($(".checker input[type=checkbox]:checked").length > 0){
		}
		else{
			alert("Please choose at least 1 record");
			return false;
			
		}
		
	});
	//Fix Enter input
	$('#frmManagement input').keypress(function(event){
		if(event.which == 13){
			save();
			return false;
		}
	})

	//Parse URL
	var url = $.url(document.location.href);
	if(url.segment(-1)!='update' && url.segment(-2)!='update'){
		if(url.fsegment(1)=='back' || url.fsegment(1)=='save'){
			if(url.fsegment(1)=='save'){
				show_perm_success();
			}
			if(url.segment(-1)!='update_profile' && url.segment(-1)!='setting'){
				if($('#start').val()==''){
					$('#start').val(0);
				}
				searchContent($('#start').val(),10);
			}
		}else{
			if(url.segment(-1)!='update_profile' && url.segment(-1)!='setting' && url.segment(-1)!='import'){
				//Load Content
				if(module =='report'){
					searchContent(0,20);
					return false
				}
				if(module!='admincp'){
					searchContent(0,10);
				}
			}
		}
	}
	
	//Fix box permission
	widthBoxPerm();
});

function show_perm_denied(){
	$('.notification').show();
	$('.alert-danger').stop().fadeIn(500);
	setTimeout("$('.notification').stop().fadeOut(300);$('.alert-danger').stop().fadeOut(300)",3000);
	$('html,body').animate({
		scrollTop: 0
	}, 1000);
}

function show_perm_success(){
	$('.notification').show();
	$('.alert-success').stop().fadeIn(500);
	setTimeout("$('.notification').stop().fadeOut(300);$('.alert-success').stop().fadeOut(300)",3000);
	$('html,body').animate({
		scrollTop: 0
	}, 1000);
}
function showData(e) {
	$("#hdshowData").val($(e).attr("value"));
	searchContent(0);
}
function searchContent(start,per_page){
	var el = $('a.reload').closest(".portlet").children(".portlet-body");
	Metronic.blockUI({
		target: el,
		animate: true,
		overlayColor: 'none'
	});
	if(per_page==undefined){
		if($('#per_page').val()){
			per_page = $('#per_page').val();
		}else{
			per_page = 10;
		}
	}
	var func_sort = $('#func_sort').val();
	var type_sort = $('#type_sort').val();
	$('#start').val(start);
	$.post(root+module+'/ajaxLoadContent',{
		func_order_by: func_sort,
		order_by: type_sort,
		start: start,
		per_page: per_page,
		dateFrom: $('#caledar_from').val(),
		dateTo: $('#caledar_to').val(),
		content: $('#search_content').val(),
		title: $('#search_title').val(),
		cate_name: $('#search_cate_name').val(),
		url: $('#search_url').val(),
		description: $('#search_description').val(),
		name: $('#search_name').val(),
		slug: $('#search_slug').val(),
		username: $('#search_username').val(),
		module_name: $('#search_module_name').val(),
		status: $("input[name=search_status]:checked").val(),
		showData: $("#hdshowData").val(),
		csrf_token: token_value
	},function(data){
		$('.portlet-body').html(data);
		Metronic.unblockUI(el);
		Metronic.initAjax();
		
		//Set Icon Order By
		if(type_sort=='DESC'){
			$('#'+func_sort).removeClass('sorting');
			$('#'+func_sort).addClass('sorting_desc');
		}else{
			$('#'+func_sort).removeClass('sorting');
			$('#'+func_sort).addClass('sorting_asc');
		}
	});
}

function enterSearch(e){
	if (e.keyCode == 13){ 
		searchContent(0);
	}
}

function sort(func){
	var func_sort = $('#func_sort').val();
	var type_sort = $('#type_sort').val();
	if(func==func_sort){
		if(type_sort=='DESC'){
			$('#type_sort').val('ASC');
		}else{
			$('#type_sort').val('DESC');
		}
	}else{
		$('#func_sort').val(func)
		$('#type_sort').val('DESC');
	}
	searchContent(0,$('#per_page').val());
}

function updateStatus(id,status,module){
	var url = root+module+'/ajaxUpdateStatus';
	$.post(url,{
			id: id,
			status: status,
			csrf_token: token_value
		},
		function(data){
			$('#loadStatusID_'+id).html(data);
			if(module=="admincp_modules"){
				top.location.reload();
			}
			searchContent(0);
		}
	);
}

function selectItem(id){
	var itemCheck = document.getElementById('item'+id);
	var count = 0;
	console.log($("tbody tr").length);
	if(itemCheck.checked==true){
		$('.item_row'+id).addClass('row_active');
		// check 
		for(var i=0;i<$("tbody tr").length;i++){
			if(document.getElementById('item'+i)!=null){
				itemCheck = document.getElementById('item'+i);
				if(itemCheck.checked) 
				{
					count ++;
				}
			}
		}
		if(count == $("tbody tr").length) {
			$('#selectAllItems').parent('span').addClass('checked');
			document.getElementById('selectAllItems').checked = true;
		}
	}else{
		$('.item_row'+id).removeClass('row_active');
		$('#selectAllItems').parent('span').removeClass('checked');
		document.getElementById('selectAllItems').checked = false;
	}
}

function selectAllItems(max){
	console.log(document.getElementById('selectAllItems').checked);
	if(document.getElementById('selectAllItems').checked==true){
		for(var i=0;i<max;i++){
			if(document.getElementById('item'+i)!=null){
				$('.item_row'+i).addClass('row_active');
				$('#item'+i).parent("span").addClass('checked');
				$('#item'+i).parent("span").parent("div .checker").addClass('focus');
				itemCheck = document.getElementById('item'+i);
				itemCheck.checked = true;
			}
		}
	}else{
		for(var i=0;i<max;i++){
			if(document.getElementById('item'+i)!=null){
				$('.item_row'+i).removeClass('row_active');
				$('#item'+i).parent("span").removeClass('checked');
				$('#item'+i).parent("span").parent("div .checker").removeClass('focus');
				itemCheck = document.getElementById('item'+i);
				itemCheck.checked = false;
			}
		}
	}
}

function showStatusAll(){
	if($(".checker input[type=checkbox]:checked").length > 0) {
		var max = $('#per_page').val();
		for(var i=0;i<max;i++){
			if(document.getElementById('item'+i)!=null){
				if(document.getElementById('item'+i).checked==true){

					if($("#item"+i).closest("tr").find("span").hasClass("status-blocked")) {
						updateStatus($('#item'+i).val(),0,module);
					}
				}
			}
		}
	}
	else {
		alert("Please choose at least one record!");
	}
}

function hideStatusAll(){
	if($(".checker input[type=checkbox]:checked").length > 0) {
		var max = $('#per_page').val();
		for(var i=0;i<max;i++){
			if(document.getElementById('item'+i)!=null){
				if(document.getElementById('item'+i).checked==true){
					if($("#item"+i).closest("tr").find("span").hasClass("status-approved")) {
						updateStatus($('#item'+i).val(),1,module);
					}
				}
			}
		}
	}
	else {
		alert("Please choose at least one record!");
	}
}

function deleteAll(){
	console.log($("#hdshowData").val());
	var max = $('#per_page').val();
	for(var i=0;i<max;i++){
		if(document.getElementById('item'+i)!=null){
			if(document.getElementById('item'+i).checked==true){
				$('.modal-header .close').trigger('click');
				id = $('#item'+i).val();
				var url = root+module+'/delete';
				$.post(url,{
					id: id,
					csrf_token: token_value
				},function(data){
					if(module=='admincp_accounts'){
						data = data.split(".");
						token_value  = data[1];
						if(data[0]=='permission-denied'){
							$('#txt_error').html('Permission denied.');
							show_perm_denied();
							return false;
						}else{
							searchContent($('#start').val(),$('#per_page').val());
							$('.btn-group').load(root+module+'/count/'+$("#hdshowData").val());

						}
					}else{
						if(data=='permission-denied'){
							$('#txt_error').html('Permission denied.');
							show_perm_denied();
							return false;
						}else{
							token_value = data;
							searchContent($('#start').val(),$('#per_page').val());
							$('.btn-group').load(root+module+'/count/'+$("#hdshowData").val());
						}
					}
					
				});
			}
		}
	}
}

function restoreAll(){
	var max = $('#per_page').val();
	for(var i=0;i<max;i++){
		if(document.getElementById('item'+i)!=null){
			if(document.getElementById('item'+i).checked==true){
				id = $('#item'+i).val();
				var url = root+module+'/restore';
				$.post(url,{
					id: id,
					csrf_token: token_value
				},function(data){
					data = data.split(".");
					if(module=='admincp_accounts'){
						token_value  = data[1];
						if(data[0]=='permission-denied'){
							$('#txt_error').html('Permission denied.');
							show_perm_denied();
							return false;
						}else{
							searchContent($('#start').val(),$('#per_page').val());
							$('.btn-group').load(root+module+'/count/'+$("#hdshowData").val());
						}
					}else{
						if(data[0]=='permission-denied'){
							if(module=='menus'){
								$('#txt_error').html('Parent was deleted. Can not restore !!!');
							}
							if(module=='news'){
								$('#txt_error').html('Category was deleted. Can not restore !!!');
							}
							show_perm_denied();
							return false;
						}else{
							$('.btn-group').load(root+module+'/count/'+$("#hdshowData").val());
							searchContent(0);
						}
					}
				});
			}
		}
	}
}

function chk_perm(id,perm){
	if(perm!='no_access'){
		if(perm=='read'){
			if($('#read'+id).attr('checked')!='checked'){
				$('#noaccess'+id).attr("checked",true);
				$('#write'+id).attr("checked",false);
				$('#delete'+id).attr("checked",false);
				$('#noaccess'+id).parent("span").addClass('checked');
				$('#write'+id).parent("span").removeClass('checked');
				$('#delete'+id).parent("span").removeClass('checked');
			}else{
				$('#noaccess'+id).attr("checked",false);
				$('#noaccess'+id).parent("span").removeClass('checked');
			}
		}else{
			$('#read'+id).attr("checked",true);
			$('#read'+id).parent("span").addClass('checked');
			$('#noaccess'+id).attr("checked",false);
			$('#noaccess'+id).parent("span").removeClass('checked');
		}
	}else{
		if($('#noaccess'+id).attr('checked')!='checked'){
			$('#read'+id).attr("checked",true);
			$('#read'+id).parent("span").addClass('checked');
		}else{
			$('.perm_access'+id).attr("checked",false);
			$('.perm_access'+id).parent("span").removeClass('checked');
		}
	}
}
function widthBoxPerm(){
	if($(".gr_perm").length){
		$(".gr_perm").parent(".col-md-10").css("padding-right",0);
		var e=$(".gr_perm").length;var t=Math.floor(($(".col-md-10").width()+27)/($(".gr_perm").width()+17));
		var n=Math.floor(e/t);$(".gr_perm").removeAttr("style");
		$(".gr_perm").each(function(e){
			for(var r=1;r<=n;r++){
				if(e+1==t*r){
					$(this).css({marginRight:0});
				}
				if(e>=t){
					$(this).css({marginTop:15});
				}
			}
		})
	}
}