$(function(){
	if(typeof html_load == 'function'){
		html_load();
	}
	if(typeof aside_nav == 'function'){
		aside_nav();
	}
})
function ajax_post(url,data,fn){
	return $.ajax({
		type:"POST",url:url,data:data,cache:false,dataType:'json',
		success:fn
	})
}
function ajax_get(url,data,fn){
	return $.ajax({
		type:"GET",url:url,data:data,cache:false,dataType:'json',
		success:fn
	})
}
function format_string(str,param){
	var reg = /{([^{}]+)}/gm;
	str=str.replace(reg, function(match, name) {
		return param[name];
	})
	return str;
}
function get_random(min,max){
	return Math.ceil(Math.rand()*min)+max;
}
function test_reg(val,reg){
	var pat=reg;
	if(reg=='tel'){
		pat=/^1(\d{10})$/;
	}else if(reg=='mail'){
		pat=/^([\w-_]+(?:\.[\w-_]+)*)@((?:[a-z0-9]+(?:-[a-zA-Z0-9]+)*)+\.[a-z]{2,6})$/i;
	}	
	return pat.test(val);
}
function show_tips(msg){
	var level=arguments[1] ?  arguments[1] : 'info';
	var html='<div class="alert alert-'+level+'" role="alert">'+msg+'</div>';
	return html;
}
function aside_nav(){
	$("#aside-nav div.list-group a.active").on("click",function(){
		if($(this).next("a").eq(0).css("display")=='none'){
			$(this).children(".glyphicon").removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
			$(this).nextAll("a").slideDown().css("display","block");
		}else{
			$(this).children(".glyphicon").removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
			$(this).nextAll("a").slideUp();
		}
	});
	$("#nav-"+def.action).trigger('click');
}
/** dialog **/
function bt_alert(){
	var str=arguments[0];
	var fn=arguments[1];
	var obj=$("#bt_alert_modal");
	if(typeof str!='undefined'){
		obj.find(".modal-body").html(str);
	}
	if(typeof fn=='function'){
		obj.one('hidden.bs.modal',fn);
	}
	obj.modal();
}

function bt_confirm(){
	var str=arguments[0];
	var fn_ok=arguments[1];
	var fn_close=arguments[2];
	var obj=$("#bt_confirm_modal");
	if(typeof str!='undefined'){
		obj.find(".modal-body").html(str);
	}
	if(typeof fn_ok=='function'){
		obj.find(".btn-primary").one("click",fn_ok);
	}
	if(typeof fn_close=='function'){
		obj.one('hidden.bs.modal',fn_close);
	}
	obj.modal();
}
