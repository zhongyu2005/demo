<?php tpl('Common.header_1');?>
<?php
$editUrl=url(__ACTION_NAME__,'edit');
?>
<style>
<!--
.sort-req{
	width:40px;text-align:center;
}
-->
</style>
<div class="con_c_t">
	<div class="con_bzk">
		<div style="padding: 10px;">
			<a class="button green medium addKf" href="<?php echo url(__ACTION_NAME__,'add');?>">添加</a>
			<button class="button red medium delKf">删除所选</button>
			<button class="button red medium btnSort">更新排序</button>
			<form class="soso" method="get" action="?" id="search-form">
				<input name="keyword" class="inputS" type="text" placeholder="查询关键字" value="<?php echo $keyword;?>" />
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
#ListTh#<td width="60">操作</td>
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
#ListTd#<td>
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
				<td colspan="7">
					<div class="tab_foot"><?php echo $pageHtml;?></div>
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
	});

</script>
<?php tpl('Common.footer_1');?>