<?php 
/**

tpl
<div class="bottom_lay clearfix">
    <ul class="pagination pull-left" id="pagin-ul" data-total="<?php echo $phone_len?>" data-page="1" data-size="63">
        <li><a href="javascript:;" aria-label="Previous"><span aria-hidden="true">«</span></a></li>
        <li><a href="javascript:;" aria-label="Next"><span aria-hidden="true">»</span></a></li>
    </ul>
</div>

script

function pageShow(){
	var pagin=$("#pagin-ul");
	pagin.find("li:not(:first,:last)").remove();
	pagin.find("li:first,li:last").removeClass('disabled').off("click");
	var total=pagin.attr("data-total")*1;
	var page=pagin.attr("data-page")*1;
	var size=pagin.attr("data-size")*1;
	var ctotal=Math.ceil(total/size);
	var min=0,max=0,str='',active='class="active"';
	page=page<1 ? 1 : (page >ctotal ? ctotal : page);
	pagin.attr("data-page",page);
	if(ctotal<page){
		pagin.find("li:last").addClass("disabled");
	}
	if(ctotal==page){
		if(page==1){
			str+='<li '+active+'><a href="javascript:;">1</a></li>';
			pagin.find("li").addClass("disabled");
		}else{
			min=page-2 >0 ? page-2 : 1;
			max=page+2 >=ctotal ? ctotal : page+2;
			while(min<=max){
				active='';
				if(min==page){
					active='class="active"'
				}
				str+='<li '+active+'><a href="javascript:;">'+(min++)+'</a></li>';
			}
			pagin.find("li:last").addClass("disabled");
		}
	}
	if(ctotal>page){
		if(page<=1){
			  pagin.find("li:first").addClass("disabled");
		}
		min=page-2 >0 ? page-2 : 1;
		max=page+2 >=ctotal ? ctotal : page+2;
		while(min<=max){
			active='';
			if(min==page){
				active='class="active"'
			}
			str+='<li '+active+'><a href="javascript:;">'+(min++)+'</a></li>';
		}
	}
	pagin.find("li:first").after(str);
	pagin.find("li:not(.disabled,.active)").off("click").on("click",function(){
		if(def.lock=='true'){
			return false;
		}
		def.lock='true';
		var page=pagin.attr("data-page")*1;
		if($(this).is(pagin.find("li:first"))){
			page-=1;
		}else if($(this).is(pagin.find("li:last"))){
			page+=1;
		}else{
			page=$(this).text()*1;
		}
		pagin.attr("data-page",page);
		var url=def.root+"?a="+def.action+"&m=getPhonePool";
		var data={'page':page};
		$.ajax({
			type:"POST",url:url,data:data,cache:false,dataType:'json',
			success:function(j){
				def.lock='1';
				if(j.error=='1'){
					showTips(j.msg);
					return false;
				}
				if(j.data.list.length){
					var str='';
					for(var i in j.data.list){
						var obj=j.data.list[i];
						str+='<span class="btn_select">'+obj+'<i></i></span>';						
					}
					$("#pagin-ul").parent().prevAll("span").remove().end().before(str);
					pageShow();
				}				
			}
		})
	})
}
  
  
  
  
  
 */