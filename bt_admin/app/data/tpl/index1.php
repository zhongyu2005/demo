<?php tpl('Common.header_1');?>
<?php
$editUrl=url(__ACTION_NAME__,'edit');
?>
<style>
<!--
.sort-req{
	width:40px;text-align:center;
}
#pagin-ul{
}
#pagin-ul li{
	display:inline-block;
	width:28px;
	height:28px;
	font-size:1.1em;
	text-align:center;
	border:solid 1px #0076a3;
	color:#F7F7F7;
	padding:.4em .5em .42em;
	margin:0 2px;
	background:-webkit-gradient(linear, left top, left bottom, from(#1198e4), to(#0a78c2));
	border-radius:.5em;
	font-weight: bold;
}
#pagin-ul li.active{
	background:#FFF;
}
#pagin-ul li a{
	width:100%;
	height:200%;
	float:left;
	color:#F7F7F7;
}
-->
</style>
<div class="con_c_t">
	<div class="con_bzk">
		<div style="padding: 10px;">
			<a class="button green medium addKf" href="<?php echo url(__ACTION_NAME__,'add');?>">添加</a>
			<button class="button red medium delKf">删除所选</button>
			<button class="button red medium btnSort">更新排序</button>
			<form class="soso" method="get" action="<?php echo url(__ACTION_NAME__, 'index');?>" id="search-form">
				<input name="keyword" class="inputS" type="text" placeholder="查询标题" value="<?php echo $keyword;?>" />
				<input name="a" type="hidden" value="<?php echo __ACTION_NAME__;?>" />
				<input name="m" type="hidden" value="<?php echo __ACTION_METHOD__;?>" />
				<input type="button" id="search-button" class="button blue medium" value="查询" />
			</form>
		</div>
	</div>
</div>
<div class="con_c_c">
	<table class="tab_con" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<td width="15">
					<input type="checkbox" class="qxCB" />
				</td>
				<td width="20%">姓名</td>
				<td width="30%">年龄</td>
				<td width="10%">备注</td>
				<td width="10%">排序</td>
				<td width="20%">创建日期</td>
				<td width="60">操作</td>
			</tr>
		</thead>
		<tbody>
		<?php
			if (!empty($list)): 
				foreach ($list as $vo) :
			?>
			<tr>
				<td >
	            	  <input name="items" type="checkbox" id="ids[]" value="<?php echo $vo['id'];?>" />
	            </td>
				<td><a href="javascript:;"><?php echo $vo['name'];?></a></td>
				<td><?php echo $vo['age'];?></td>
				<td><a href="javascript:;"><?php echo $vo['intro']?></a></td>
				<td><input type="text" data-id="<?php echo $vo['id'];?>" value="<?php echo $vo['sort']?>" class="inputS sort-req" /></td>
				<td><?php echo $vo['create_date'];?></td>
				<td>
					<div class="czx">
						<a href="<?php echo $editUrl,'&id=',$vo['id']?>" title="编辑" class="edit_2"></a>
					</div>
				</td>
			</tr>
			<?php
				endforeach;
			 else :
			?>
			<tr>
				<td colspan="7" align="center">无内容</td>
			</tr>
		<?php endif;?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="5">
					<div class="tab_foot">
                            <ul class="pagination pull-left" id="pagin-ul" data-total="<?php echo $pageObj['total'];?>" data-page="<?php echo $pageObj['page'];?>" data-size="<?php echo $pageObj['pageSize'];?>">
                                <li><a href="javascript:;" aria-label="Previous"><span aria-hidden="true">«</span></a></li>
                                <li><a href="javascript:;" aria-label="Next"><span aria-hidden="true">»</span></a></li>
                            </ul>
					</div>
				</td>
			</tr>
		</tfoot>
	</table>
</div>
<script type="text/javascript">
	$(function(){
		$().checkboxqx('.tab_con .qxCB');

		//搜索
		$('#search-button').click(function (){
			$('#search-form').submit();
		});

		//批量删除
		$('.delKf').click(function(){
			removeMany('<?php echo url(__ACTION_NAME__, 'delete');?>');
		});

		$(".btnSort").on("click",function(){
			var arr=[];
			$(".con_c_c :checked").each(function(){
				var inp=$(this).parent().parent().find(".inputS");
				arr.push({"id":inp.attr("data-id"),"val":inp.val()});
			})
			if(arr.length==0){
				loadMack({off:'on',Limg:0,text:"请选中后,再更新!",set:2000});
				return false;
			}
			var url=def.root+"?a="+def.action+"&m=sort";
			var data={vals:arr};
			$.ajax({
    			type:"POST",url:url,data:data,cache:false,dataType:'json',
    			success:function(j){
    				if(j.error=='1'){
    					loadMack({off:'on',Limg:0,text:j.msg,set:2000});
    					return false;
    				}
    				location=def.self;
    			}
    		})
		})

		
		pageShow();
		
	});

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
			location=location.href.toString()+"&page="+page;
			return true;
			pagin.attr("data-page",page);
			var url=def.self;
			var data={'page':page,'echoAjax':'true'};
			$.ajax({
				type:"POST",url:url,data:data,cache:false,dataType:'json',
				success:function(j){
					def.lock='1';
					if(j.error=='1'){
						loadMack({off:'on',Limg:0,text:j.msg,set:2000});
						return false;
					}
					if(j.data.list.length){
						var str='';
						for(var i in j.data.list){
							var obj=j.data.list[i];
							//str+='<span class="btn_select">'+obj+'<i></i></span>';						
						}
						$("#pagin-ul").parent().prevAll("span").remove().end().before(str);
						pageShow();
					}				
				}
			})
		})
	}

</script>
<?php tpl('Common.footer_1');?>