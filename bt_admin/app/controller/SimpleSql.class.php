<?php


/**
 * 管理员管理
 * @author zhong
 * @version 2015-07-01
 */
class SimpleSqlAction extends BaseAction{

	public function __construct(){
		parent::__construct();
	}

	/**
	 * 列表
	 */
	public function index(){
		if(IS_AJAX && !empty($_POST)){
			$table=isset($_POST['table']) ? $_POST['table'] : '';
			if(empty($table)){
				printJson("提交不合法");
			}
			$column='';
			foreach ($table as $key => $val) {
				if(empty($val['name'])){
					continue;
				}

				if(in_array($val['type'],array('char','varchar','text') )){
					//string类型，0便是空字符串
					$val['default']=empty($val['default']) ? '' : $val['default'];
				}
				$val['default']=" DEFAULT '".$val['default']."' ";
				$type=$val['type'].'('.$val['length'].')';
				if($val['type']=='decimal'){
					//数值类型的长度有小数位
					$type=$val['type'].'('.$val['length'].','.$val['place'].')';
				}elseif(in_array($val['type'], array('text','mediumtext','longtext','datetime','date') )){
					//相关类型不需要默认值和长度设定
					$type=$val['type'];
					$val['default']='';
				}
				$null_str=$val['null'] ? 'NOT NULL' : '';
				//进行语句的拼接
				$col="`%s` %s %s %s COMMENT '%s',";
				$col=sprintf($col,$val['name'],$type,$null_str,$val['default'],$val['comment']);
				$column.=$col."\n";
			}
			$column=substr($column, 0,-1);
			$str="CREATE TABLE `table_name` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'pk',
{$column}
`create_at` int(10) NOT NULL DEFAULT '0',
`update_at` int(10) NOT NULL DEFAULT '0',
`deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0正常1删除',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ";
			printJson($str);
		}
		$this->display();
	}

	/**
	 * 列表
	 */
	public function create(){
		$tab_name=I('tab_name','');
		if(!empty($tab_name)){
			$this->table=$tab_name;
			$db=Factory::getDb('DEFAULT_DB');
			//得到具体的业务逻辑行数,进行tpl处理
			$sql="show full columns from {$tab_name}";
			$list=$db->query($sql);
			if(empty($list)){
				exit('数据表无法读取');
			}
			$row_id=array_slice($list, 0,1);
			$row_date=array_slice($list, -2,2);
			$this->columns=array_slice($list, 1,-3);
			foreach ($this->columns as $v) {
				$this->field[]=$v['Field'];
			}


			//得到语句
			$sql="SHOW CREATE TABLE {$tab_name}";
			$row=$db->query($sql);
			$tab=$row[0]['Create Table'];

			//生成add，tpl，edit tpl，list tpl
			$action_tpl=$add_tpl=$edit_tpl=$list_tpl='';

			$add_tpl=$this->getTpl('add');
			$edit_tpl=$this->getTpl('edit');
			$list_tpl=$this->getTpl('list');
			$action_tpl=$this->getTpl('act');

			$this->assign('table_ddl',$tab);
			$this->assign('tab_name',$tab_name);
			$this->assign("action_tpl",$action_tpl);
			$this->assign("add_tpl",$add_tpl);
			$this->assign("edit_tpl",$edit_tpl);
			$this->assign("list_tpl",$list_tpl);
			$this->assign("showType",'tpl');
		}
		$this->display();
	}

	public function getTpl($type){
		$tpl='';
		switch ($type) {
			case 'add':

				$addField='';
				foreach ($this->columns as $v) {
					$addField.=$this->createAddField($v);
				}
				$search=array('#AddField#','#TableName#','#SelectField#');
				$replace=array($addField,'','');
				$tpl=$this->fetch('tpl/add.php');
				$tpl=str_replace($search, $replace, $tpl);
				break;
			case 'edit':
				$EditField='';
				foreach ($this->columns as $v) {
					$EditField.=$this->createAddField($v,true);
				}
				$search=array('#EditField#','#TableName#','#SelectField#');
				$replace=array($EditField,'','');
				$tpl=$this->fetch('tpl/edit.php');
				$tpl=str_replace($search, $replace, $tpl);
				break;
			case 'list':
				$ListTh=$ListTd='';
				foreach ($this->columns as $v) {
					$ListTh.=$this->createListTh($v);
					$ListTd.=$this->createListTd($v);
				}
				$search=array('#ListTh#','#ListTd#','#SelectField#');
				$replace=array($ListTh,$ListTd,'');
				$tpl=$this->fetch('tpl/index.php');
				$tpl=str_replace($search, $replace, $tpl);
				break;
			case 'act':
				$tpl=$this->fetch('tpl/action.php');
				$actName=end(explode('_',$this->table));
				$search=array('#ActionName#','#TableName#','#SelectField#');
				$replace=array(ucfirst($actName),$this->table,implode(',', $this->field));
				$tpl=str_replace($search, $replace, $tpl);
				break;
			default:
				# code...
				break;
		}
		return $tpl;
	}

	public function fetch($file){
		$dir=__APP__.'/data/'.$file;
		if(is_file($dir)){
			$f=file_get_contents($dir);
			return $f;
		}
		return false;
	}


	public function createAddField($row,$flg=false){
		$str='';
		$val='';
		if($flg){
			$val=<<<EOF
<?php echo \$row['{$row['Field']}'];?>
EOF;
		}
		if(stristr($row['Type'], 'text') || stristr($row['Type'], 'mediumtext') || stristr($row['Type'], 'longtext') ){
			$str=<<<EOF
<tr>
	<td>
		<span>
			<label for="data[{$row['Field']}]">{$row['Comment']}</label>
		</span>
	</td>
	<td>
		<textarea rows="10" cols="20" class="bt-req inputS" name="data[{$row['Field']}]">{$val}</textarea>
		<bt class="hui">(必填)</bt>
	</td>
</tr>
EOF;
		}else{
			$str=<<<EOF
<tr>
	<td>
		<span>
			<label for="data[{$row['Field']}]">{$row['Comment']}</label>
		</span>
	</td>
	<td>
		<input type="text" name="data[{$row['Field']}]" class="add_input inputS bt-req" value="{$val}" />
		<bt class="hui">(必填)</bt>
	</td>
</tr>
EOF;
		}
		return $str;
	}

	/**
	* list-th
	*/
	public function createListTh($row){
		$th=<<<EOF
<td>{$row['Comment']}</td>\n
EOF;
		return $th;
	}

	/**
	* list-td
	*/
	public function createListTd($row){
		$td=<<<EOF
<td><?php echo \$vo['{$row['Field']}'];?></td>\n
EOF;
		return $td;
	}
}
