<?php tpl("Common.header_1")?>
	<div class="con_c_t">
		<div class="con_bzk">
			<div style="padding: 10px;">
				<div id="go_back" class="button green medium">返回</div>
			</div>
		</div>
	</div>
	<div class="con_c_t ">
		<div class="con_edit">
			<form class="addform" id="frm">
				<TABLE cellpadding="0" cellspacing="0" class="t">
					<THEAD>
						<tr>
							<td colspan="2">编辑</td>
						</tr>
					</THEAD>
					<?php if(empty($row)):?>
					<TBODY>
						<tr>
							<td colspan="2" style="text-align:center;">
								要编辑的内容没有找到
							</td>
						</tr>
					</TBODY>
					<?php else:?>
					<TBODY>
						#EditField#
						<tr>
							<td></td>
							<td><a onclick="qrOk();" href="javascript:void(0);" class="button blue medium">提交</a></td>
						</tr>
					</TBODY>
					<?php endif;?>
				</TABLE>
				<input type="hidden" id="id" name="id" value="<?php echo $row['id']?>" />
				<input value="submit" name="submit" type="hidden" />
			</form>
		</div>
	</div>
	<script type="text/javascript">
		var href = "<?php echo url(__ACTION_NAME__, 'index');?>";

		function qrOk(){
    		var url=def.self;
    		var params=$("#frm").serialize();
    		var flg=false;
    		$("#frm .bt-req").each(function(){
        		if($.trim($(this).val()).length<=0){
            		flg=true;
        			loadMack({off:'on',Limg:0,text:'填写不能为空',set:2000});
            		return false;
        		}
        	})
        	if(flg)return false;
        	if(def.lock=='1')return false;
        	def.lock='1';
        	ajaxSubmit(url, 'POST', params, function(status, result){
     		   if(result.data=='200'){
         		   location=href;
     		   }
    		}, '操作成功');
	    }
   </script>
<?php tpl('Common.footer_1');?>