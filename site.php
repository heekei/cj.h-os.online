<?php
/**
 * 行业宝模块微站定义
 *
 * @author 开吧源码社区
 * @url http://www.kai8.top/
 */
defined('IN_IA') or exit('Access Denied');

class Choujiang_pageModuleSite extends WeModuleSite {

	// 基本信息
	public function doWebChoujiang_base() {
		global $_GPC, $_W;
		$op = $_GPC['op'];
		$ops = array('display', 'post','hexiaoma');
		$op = in_array($op, $ops) ? $op : 'display';
		$uniacid = $_W['uniacid'];
		load()->func('file'); //调用上传函数
		$dir_url='../web/cert/choujiang_page/'; //上传路径
		mkdirs($dir_url); 
		//创建目录
	    if ($_FILES["upfile"]["name"]){
			$upfile=$_FILES["upfile"]; 
			//获取数组里面的值 
			$name=$upfile["name"];//上传文件的文件名 
			$size=$upfile["size"];//上传文件的大小 
		if($size>2*1024*1024) {  
	       
	        message("文件过大，不能上传大于2M的文件!",$this->createWebUrl("choujiang_base",array("op"=>"display")),"success"); 
	        exit();  
	    } 
		if(file_exists($dir_url.$settings["upfile"]))@unlink ($dir_url.$settings["upfile"]);
			$cfg['upfile']=TIMESTAMP.".pem";
			move_uploaded_file($_FILES["upfile"]["tmp_name"],$dir_url.$upfile["name"]); //移动到目录下
			$upfiles = $dir_url.$name;
		
		}
      	if ($_FILES["keypem"]["name"]){
		    $upfile=$_FILES["keypem"]; 
			//获取数组里面的值 
			$name=$upfile["name"];//上传文件的文件名 
			//$type=$upfile["type"];//上传文件的类型 
			$size=$upfile["size"];//上传文件的大小 
		if($size>2*1024*1024) {  
	        message("文件过大，不能上传大于2M的文件!",$this->createWebUrl("choujiang_base",array("op"=>"display")),"success");  
	        exit();  
	    }  	
		if(file_exists($dir_url.$settings["keypem"]))@unlink ($dir_url.$settings["keypem"]);
			move_uploaded_file($_FILES["keypem"]["tmp_name"],$dir_url.$upfile["name"]); //移动到目录下
			$keypems = $dir_url.$name;
		}
		if ($op == 'display') {
			$item = pdo_fetch("SELECT * FROM " . tablename('choujiang_base') . " WHERE uniacid = :uniacid", array(':uniacid' => $uniacid));
			if (checksubmit('submit')) {
				if($_GPC['upfile1'] == ''){
					$_GPC['set']['upfile']=$upfiles;
				}else{
					$_GPC['set']['upfile']=$_GPC['upfile1'];
				}
				if($_GPC['keypem1'] == ''){
					$_GPC['set']['keypem']=$keypems;
				}else{
					$_GPC['set']['keypem']=$_GPC['keypem1'];
				}
				$_GPC['set']['uniacid'] = $uniacid;
						
				if (empty($item)) {
					$str = pdo_insert('choujiang_base', $_GPC['set']);
				} else {
					$str = pdo_update('choujiang_base', $_GPC['set'] , array('uniacid' => $uniacid));
			
				} 
				message('基础信息更新成功!', $this -> createWebUrl('choujiang_base', array('op' => 'display')), 'success');
				
			} 
		} 
		if($op == 'hexiaoma'){
			$number = $this->make_coupon_card();
			$fmdata = array(
				"success" => 1,
				"data" =>$number,
			);	 
			echo json_encode($fmdata);
			exit;
		}
		include $this -> template('choujiang_base');
	} 
	// 生成随机核销码
	function make_coupon_card() {
	    $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $rand = $code[rand(0,25)]
	        .strtoupper(dechex(date('m')))
	        .date('d').substr(time(),-5)
	        .substr(microtime(),2,5)
	        .sprintf('%02d',rand(0,99));
	    for(
	        $a = md5( $rand, true ),
	        $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
	        $d = '',
	        $f = 0;
	        $f < 8;
	        $g = ord( $a[ $f ] ),
	        $d .= $s[ ( $g ^ ord( $a[ $f + 8 ] ) ) - $g & 0x1F ],
	        $f++
	    );
	    return $d;
	}
	// 参数设置
	public function doWebChoujiang_num() {
		global $_GPC, $_W;
		$op = $_GPC['op'];
		$ops = array('display', 'post');
		$op = in_array($op, $ops) ? $op : 'display';
		$uniacid = $_W['uniacid'];

		if ($op == 'display') {
			$item = pdo_fetch("SELECT * FROM " . tablename('choujiang_base') . " WHERE uniacid = :uniacid", array(':uniacid' => $uniacid));
			if (checksubmit('submit')) {
				$_GPC['set']['uniacid'] = $uniacid;
				if (empty($item)) {
					$str = pdo_insert('choujiang_base', $_GPC['set']);
				} else {
					$products = pdo_fetchall("select * from ".tablename("choujiang_user"). " where uniacid=:uniacid",array(":uniacid"=>$uniacid));
					foreach($products as $key => $value){
						$data['mf_num'] = $_GPC['set']['join_num'];
						$data['smoke_num'] = $_GPC['set']['smoke_num'];
						$data['winning_num'] = $_GPC['set']['winning_num'];
						pdo_update('choujiang_user', $data, array('id' => $value['id']));
					}
	
					$str = pdo_update('choujiang_base', $_GPC['set'] , array('uniacid' => $uniacid));
			
				} 
				message('参数更新成功!', $this -> createWebUrl('choujiang_num', array('op' => 'display')), 'success');
				
			} 
		} 
		include $this -> template('choujiang_num');
	} 

	// 用户列表 以及地址
	public function doWebChoujiang_users(){
		global $_W,$_GPC;
		$op = $_GPC['op'];
		$ops = array('url','content','post');
		$op = in_array($op, $ops) ? $op : 'content';
		$uniacid = $_W['uniacid'];
		if ($op == 'content') {
			$pindex =max(1, intval($_GPC['page']));
			$psize =20;//每页显示个数
			$keyword = $_GPC['keyword'];
			if($keyword){
				$products = pdo_fetchall("select * from ".tablename("choujiang_user"). " where uniacid=:uniacid and nickname like '%{$keyword}%' ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_user')." where uniacid=:uniacid and nickname like '%{$keyword}%'",array(':uniacid'=>$uniacid));
			}else{
				$products = pdo_fetchall("SELECT * from ".tablename("choujiang_user"). " where uniacid=:uniacid ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_user').' where uniacid=:uniacid ',array(':uniacid'=>$uniacid));
			}
			foreach ($products as $key => $value) {
				$products[$key]['create_time'] = date('Y-m-d H:i',$value['create_time']);
				$products[$key]['join_num'] = $value['yu_num']+$value['mf_num'];
				$products[$key]['smoke_num'] = $value['smoke_num']+$value['smoke_share_num'];
			}	
			$pager =pagination($total, $pindex, $psize);	
		}
		if($op == 'post'){
					
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename('choujiang_user') . " WHERE id = :id" , array(':id' => $id));
				$item['join_num'] = $item['yu_num']+$item['mf_num'];
				$item['smoke_num'] = $item['smoke_num']+$item['smoke_share_num'];
				if (empty($item)) {
					message('抱歉，信息不存在或是已经删除！', '', 'error');
				} 
				    	
			} 
			if (checksubmit('submit')) {
				$data['yu_num'] = $_GPC['join_num'];
				$data['smoke_share_num'] = $_GPC['smoke_num'];
				$data['winning_num'] = $_GPC['winning_num'];
				$str = pdo_update('choujiang_user', $data, array('id' => $id , 'uniacid' => $uniacid));
				if(!empty($str)){
					message('信息 添加/修改 成功!', $this -> createWeburl('choujiang_users', array('op' => 'content')), 'success');
				}
			} 
		}

		include $this->template('choujiang_users');
	}
	// 奖品列表
	public function doWebChoujiang_goods(){
		global $_W,$_GPC;
		$op = $_GPC['op'];
		$ops = array('post','content','delete','user','index','winners');
		$op = in_array($op, $ops) ? $op : 'content';
		$uniacid = $_W['uniacid'];
		if ($op == 'content') {
			$pindex =max(1, intval($_GPC['page']));
			$psize =10;//每页显示个数
			$keyword = $_GPC['keyword'];
			$goods_state = $_GPC['goods_state'];
			if($goods_state > 0){
				if($goods_state == 1){   //已开奖
					$condition = "and status = 1";
				}else if($goods_state == 2){  //待开奖
					$condition = "and audit_status = 1 and status = 0";
				}else if($goods_state == 3){  //待审核
					$condition = "and audit_status = 0";
				}else if($goods_state == 4){  //审核未通过
					$condition = "and audit_status = -1";
				}
			}
			if(!empty($keyword) and $goods_state >0){
				$products = pdo_fetchall("select * from ".tablename("choujiang_goods"). " where uniacid=:uniacid and is_del != -1 and goods_name like '%{$keyword}%' ".$condition." ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_goods')." where goods_name like '%{$keyword}%' and uniacid=:uniacid and is_del != -1".$condition,array(':uniacid'=>$uniacid));
			}else if($keyword and $goods_state == 0){
				$products = pdo_fetchall("select * from ".tablename("choujiang_goods"). " where uniacid=:uniacid and is_del != -1 and goods_name like '%{$keyword}%' ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_goods')." where goods_name like '%{$keyword}%' and uniacid=:uniacid and is_del != -1",array(':uniacid'=>$uniacid));
			}else if(empty($keyword) and $goods_state >0){
				$products = pdo_fetchall("select * from ".tablename("choujiang_goods"). " where uniacid=:uniacid and is_del != -1 ".$condition." ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_goods')." where uniacid=:uniacid and is_del != -1 ".$condition,array(':uniacid'=>$uniacid));
				    	
			}else{
				$products = pdo_fetchall("select * from ".tablename("choujiang_goods"). " where uniacid=:uniacid and is_del != -1 ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_goods').' where uniacid=:uniacid  and is_del != -1',array(':uniacid'=>$uniacid));
			}
			foreach($products as $key => $value){
				$status = $value['status'];
				$audit_status = $value['audit_status'];
				if($audit_status == 0){
					$products[$key]['zh_status'] = '待审核';
				}else if($audit_status == -1){
					$products[$key]['zh_status'] = '未通过';
				}else if($status == 1){
					$products[$key]['zh_status'] = '已开奖';
				}else if($status == 0){
					$products[$key]['zh_status'] = '待开奖';
				}
				if($value['goods_status'] == 2){
					$products[$key]['goods_name'] = $value['red_envelope'].'元/人';
					$products[$key]['goods_status_name'] = '红包';
				}else if($value['goods_status'] == 0){
					$products[$key]['goods_status_name'] = '实物';
				}else if($value['goods_status'] == 1){
					$products[$key]['goods_status_name'] = '电子卡';
				}
				if($value['smoke_set'] == 0){
					$products[$key]['smoke_name'] = '到时开奖';
				}else if($value['smoke_set'] == 1){
					$products[$key]['smoke_name'] = '人数开奖';
				}else if($value['smoke_set'] == 2){
					$products[$key]['smoke_name'] = '手动开奖';
				}else if($value['smoke_set'] == 3){
					$products[$key]['smoke_name'] = '现场开奖';
				}
				$userinfo = pdo_fetch("select * from ".tablename("choujiang_user"). " where uniacid=:uniacid and openid = :openid",array(":uniacid"=>$uniacid,"openid" => $value['goods_openid']));

				$products[$key]['goods_username'] = $userinfo['nickname'];
			}
			$pager =pagination($total, $pindex, $psize);	
		}
		if ($op == 'delete') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT * FROM " . tablename('choujiang_goods') . " WHERE id = :id and uniacid = :uniacid  and is_del != -1", array(':id' => $id , ':uniacid' => $uniacid));
			if (empty($row)) {
				message('信息不存在或是已经被删除！');
			} 
			pdo_update('choujiang_goods',array('is_del'=>-1), array('id' => $id , 'uniacid' => $uniacid));
			message('删除成功!', $this -> createWeburl('choujiang_goods', array('op' => 'content')), 'success');
		} 
		if($op == 'index'){
			$goods_id = intval($_GPC['goods_id']);
			$row = pdo_fetch("SELECT * FROM " . tablename('choujiang_goods') . " WHERE id = :id and uniacid = :uniacid ", array(':id' => $goods_id , ':uniacid' => $uniacid));
			if($row['to_index'] == 0){
				$data['to_index'] = 1;
				$this->doPageGoodsInform($goods_id);
			}else if($row['to_index'] == 1){
				$data['to_index'] = 0;
			}
			$str = pdo_update('choujiang_goods', $data, array('id' => $goods_id , 'uniacid' => $uniacid));
			$fmdata = array(
				"success" => 1,
				"data" =>$data['to_index'],
			);	 
			echo json_encode($fmdata);
			exit;
		}
			//多删
		if(!empty($_GPC['deleteall']))
		{
			for($i=0;$i<count($_GPC['deleteall']);$i++)
			{
				pdo_update('choujiang_goods',array('is_del'=>-1), array('id' =>$_GPC['deleteall'][$i]));
			}
			message('删除成功!', $this -> createWeburl('choujiang_goods', array('op' => 'content')), 'success');
			
		}

		if($op == 'post'){
			$fur_list = pdo_fetchall("select * from ".tablename("choujiang_fur"). " where uniacid=:uniacid",array(":uniacid"=>$uniacid));
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename('choujiang_goods') . " WHERE id = :id  and is_del != -1" , array(':id' => $id));
				$smoke_time = $item['smoke_time'];
				$userinfo = pdo_fetch("select * from ".tablename("choujiang_user"). " where uniacid=:uniacid and openid = :openid",array(":uniacid"=>$uniacid,"openid" => $item['goods_openid']));
				$item['goods_username'] = $userinfo['nickname'];
				$item['date'] = substr($smoke_time, 0, 10);
				$item['time'] = substr($smoke_time, 11, 5);
				$card_info = unserialize($item['card_info']);
				$goods_images = unserialize($item['goods_images']);
					
				foreach($goods_images as $key => $value){
					if(strstr($value,$_W['attachurl'])){
						$goods_images[$key] = str_replace($_W['attachurl'],'',$value); 
					}
				}
					
				$item['goods_images'] = $goods_images;
				$newarr = array();
				$i = 0;
				foreach($card_info as $key => $value){
					$newarr[$i][0] = $key;
					$newarr[$i][1] = $value;
					$i++;
					
				}
				$item['card_info'] = $newarr;
				if (empty($item)) {
					message('抱歉，信息不存在或是已经删除！', '', 'error');
				} 
				    	
			} 
			if (checksubmit('submit')) {
		    	$goods_status = $_GPC['cj']['goods_status'];
		    	$fur_status = $_GPC['cj']['fur_status'];
		    	if($goods_status == 0){  //实物
		    		
	    			if (empty($_GPC['goods_name1'])) {
						message('奖品名称不能为空，请输入奖品名称！');
					} 
					if (empty($_GPC['goods_num1']) || $_GPC['goods_num1']==0) {
						message('奖品数量不能为空！');
					} 
					if ($_GPC['cj']['ziqu_status'] == 1 && empty($_GPC['cj']['business_address'])) {
						message('商家地址不能为空！');
					} 
					if ($_GPC['cj']['mail_status'] == 1 && ($_GPC['cj']['YouSong'] == 0 && $_GPC['cj']['freight'] < 1)) {
						message('运费不能小于 0元');
					} 
					$_GPC['cj']['goods_name'] = $_GPC['goods_name1'];
					if ($_GPC['goods_num1'] < $_GPC['cj']['team_num'] &&  $_GPC['cj']['team_status'] == 1) {
						message('奖品数量不能小于组队人数');
					} 
					$_GPC['cj']['goods_num'] = $_GPC['goods_num1'];
					if($fur_status == 1){
        				$fur = pdo_fetch('SELECT * FROM ' . tablename('choujiang_fur') . " where uniacid=:uniacid and id = :id", array(":uniacid" => $uniacid,":id"=>$_GPC['cj']['fur_title']));
						$_GPC['cj']['goods_name'] = $fur['title'];
		    		}

		    	}else if($goods_status == 2){  //红包
		    		if (empty($_GPC['cj']['red_envelope'])) {
						message('红包金额不能为空，请输入金额！');
					} 
					if (empty($_GPC['goods_num2']) || $_GPC['goods_num2']==0) {
						message('奖品数量不能为空！');
					} 
					if ($_GPC['goods_num2'] < $_GPC['cj']['team_num'] &&  $_GPC['cj']['team_status'] == 1) {
							message('奖品数量不能小于组队人数');
						} 	  
					$_GPC['cj']['goods_num'] = $_GPC['goods_num2'];
		    	}else if($goods_status == 1){   //电子卡
		    		if (empty($_GPC['goods_name3'])) {
						message('电子卡名称不能为空，请输入名称！');
					} 
					$_GPC['cj']['goods_name'] = $_GPC['goods_name3'];
		    		$card_number = $_GPC['card_number'];
		    		$card_password = $_GPC['card_password'];
					if (count($card_number) <= 0 || count($card_password) <= 0)  {
						message('请填写电子卡信息');
					}else{
						$newarr=array();
						foreach($card_number as $key=>$value){  
							$newarr[$value]=$card_password[$key];   
	                    }
        				$_GPC['cj']['card_info'] = serialize($newarr);	
        				if (count($newarr) < $_GPC['cj']['team_num'] &&  $_GPC['cj']['team_status'] == 1) {
							message('奖品数量不能小于组队人数');
						} 	    	    	
						$_GPC['cj']['goods_num'] = count($newarr);
					}
		    	}
		    	    	
				$_GPC['cj']['goods_images'] = serialize($_GPC['goods_images']);
				$_GPC['cj']['smoke_time'] = $_GPC['date'].' '.$_GPC['time'];
				if(strtotime($_GPC['cj']['smoke_time'])<=time() && $_GPC['cj']['smoke_set'] == 0){
					message('开奖时间不得小于当前时间');
				}

				if(empty($_GPC['cj']['goods_openid'])){
					message('发起人不能为空，请添加发起人！');
				}
				$openid_arr = array();
				if($_GPC['cj']['The_winning'] == 1){
					$user = $_GPC['cj']['goods_winning'];
					$new_user = explode("++",$user);
					if(count($new_user) > $_GPC['cj']['goods_num']){
						message('中奖人数不得大于奖品数量');
					}else{
						foreach($new_user as $key => $value){
							$openidd = pdo_fetch("SELECT * FROM " . tablename('choujiang_user') . " WHERE nickname = :nickname and uniacid = :uniacid" , array(':uniacid' => $uniacid,':nickname' => $value));
							if(!empty($openidd)){
								array_push($openid_arr,$openidd['openid']);
							}
						}
					}
					
				}
				$_GPC['cj']['openid_arr'] = serialize($openid_arr);
				if (empty($id)) {
					$_GPC['cj']['uniacid'] = $uniacid;
					$sql = pdo_fetch("SELECT * FROM " . tablename('choujiang_user') . " WHERE openid = :openid and uniacid = :uniacid" , array(':uniacid' => $uniacid,':openid' => $_GPC['cj']['goods_openid']));
					$base = pdo_fetch("SELECT * FROM " . tablename('choujiang_base') . " WHERE uniacid = :uniacid", array(':uniacid' => $uniacid));
					if($base['join_num'] > 0){
						if($sql['mf_num']<= 0 && $sql['yu_num'] <= 0){
							message('该发起人次数已达上限', '', 'error');

						}else if($sql['mf_num'] >0){

							$data = array('mf_num'=>$sql['mf_num'] -1);

						}else if($sql['yu_num'] >0){

							$data = array('yu_num'=>$sql['yu_num'] -1);

						}
						$strs = pdo_update('choujiang_user',$data, array('id' => $sql['id'] , 'uniacid' => $uniacid));
					}else{
						$strs = 1;
					}
					
					
					if(!empty($strs)){
						$str = pdo_insert('choujiang_goods', $_GPC['cj']);
						if($str){
                			$uid = pdo_insertid();
						}
					}

					$goods_id = pdo_insertid();
					$smoke_set = $_GPC['cj']['smoke_set'];
					$this->doWebInvitation($goods_id,$smoke_set);

				} else {
					$goods_id = $id;
					$uid = $id;
					$record = pdo_fetch("SELECT * FROM " . tablename('choujiang_record') . " WHERE goods_id = :id" , array(':id' => $id));

					if(empty($record)){

						$str = pdo_update('choujiang_goods', $_GPC['cj'], array('id' => $id , 'uniacid' => $uniacid));

					}else{
						message('已有人参与该活动、不可编辑', '', 'error');
					}
				} 
				if(!empty($str)){
					$poster = $this->GeneratePoster($uid);
            		pdo_update('choujiang_verification',array('new_poster'=>$poster),array('goods_id'=>$uid));
					message('信息 添加/修改 成功!', $this -> createWeburl('choujiang_goods', array('op' => 'content')), 'success');

				}
			} 
		}

		if($op == 'user'){
			if(!empty($_GPC['user_nickname'])){
				$keyword=$_GPC['user_nickname'];
				$condition="uniacid=".$uniacid." AND nickname='".$keyword."'";
				$records=pdo_fetch("SELECT openid,nickname,avatar FROM ".tablename("choujiang_user")." WHERE ".$condition." LIMIT 1");
				if(!empty($records)){
					$fmdata = array(
						"success" => 1,
						"data" =>$records['openid'],
					);	 
				}else{
					$fmdata = array(

						"success" => -1,

						'data'=>'此用户未找到',
					);	
				}	 
				echo json_encode($fmdata);
				exit;
			}
			$keyword=$_GPC['keyword_user'];
			$condition="uniacid=".$uniacid." AND (nickname LIKE '%".$keyword."%')";
			$records=pdo_fetchall("SELECT openid,nickname,avatar FROM ".tablename("choujiang_user")." WHERE ".$condition);
			$fmdata = array(
					"success" => 1,
					"data" =>$records,
			);	 
			echo json_encode($fmdata);
			exit;
		}
		if($op == 'winners'){
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$goods = pdo_fetch("SELECT * FROM " . tablename('choujiang_goods') . " WHERE id = :id" , array(':id' => $id));
				if($goods['smoke_set'] == 3){
					$record = pdo_fetchall("SELECT * FROM " . tablename('choujiang_scene') . " WHERE goods_id = :id and status = 1" , array(':id' => $id));
				}else{
					$record = pdo_fetchall("SELECT * FROM " . tablename('choujiang_record') . " WHERE goods_id = :id and status = 1" , array(':id' => $id));
				}

				foreach($record as $key => $value){
					// $record[$key]['finish_time'] = date('Y-m-d H:i:s',$value['finish_time']);
					$goods_id = $value['goods_id'];
					$status = $value['status']; 
					$goods = pdo_fetch("SELECT * FROM " . tablename('choujiang_goods') . " WHERE id = :id " , array(':id' => $goods_id));
					$record[$key]['goods_icon'] = $goods['goods_icon'];
					if($goods['is_del'] == -1){
						$record[$key]['goods_name'] = $goods['goods_icon'].' (已作废)';
					}
					if($goods['status'] == 0){
						$str = '未开奖';
					}else if(empty($goods)){
						$str = '该奖品已失效';
					}elseif($status == 1){
						$str = '已中奖';
					}elseif($status == -1){
						$str = '已作废(未填写地址)';
					}else{
						$str = '未中奖';
					}
					if($value['get_status'] == 1){
						$record[$key]['get_title']  = '商家配送';
					}else if($value['get_status'] == 2){
						$record[$key]['get_title']  = '上门自提';
					}else if($value['get_status'] == 0){
						$record[$key]['get_title']  = '未填写';
					}
					$record[$key]['state'] = $str;
					$record[$key]['create_time'] = date('Y-m-d H:i',$value['create_time']);
					$record[$key]['finish_time'] = date('Y-m-d H:i',$value['finish_time']);
				}
			}
		}
		include $this->template('choujiang_goods');
	}

	// 小程序推荐
	public function doWebChoujiang_xcx(){
		global $_W,$_GPC;
		$op = $_GPC['op'];
		$ops = array('post','content','delete','index');
		$op = in_array($op, $ops) ? $op : 'content';
		$uniacid = $_W['uniacid'];
		if ($op == 'content') {
			$pindex =max(1, intval($_GPC['page']));
			$psize =10;//每页显示个数
			$keyword = $_GPC['keyword'];
			if($keyword){
				$products = pdo_fetchall("select * from ".tablename("choujiang_xcx"). " where uniacid=:uniacid and name like '%{$keyword}%' ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_xcx')." where uniacid=:uniacid  and name like '%{$keyword}%'",array(':uniacid'=>$uniacid));
			}else{
				$products = pdo_fetchall("select * from ".tablename("choujiang_xcx"). " where uniacid=:uniacid ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_xcx').' where uniacid=:uniacid ',array(':uniacid'=>$uniacid));
			}
			
			$pager =pagination($total, $pindex, $psize);	
		}
		if($op == 'index'){
			$xcx_id = intval($_GPC['xcx_id']);
			$row = pdo_fetch("SELECT * FROM " . tablename('choujiang_xcx') . " WHERE id = :id and uniacid = :uniacid ", array(':id' => $xcx_id , ':uniacid' => $uniacid));
			if($row['status'] == 0){
				$data['status'] = 1;
			}else if($row['status'] == 1){
				$data['status'] = 0;
			}
			$str = pdo_update('choujiang_xcx', $data, array('id' => $xcx_id , 'uniacid' => $uniacid));
			$fmdata = array(
				"success" => 1,
				"data" =>$data['status'],
			);	 
			echo json_encode($fmdata);
			exit;
		}
		if ($op == 'delete') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT * FROM " . tablename('choujiang_xcx') . " WHERE id = :id and uniacid = :uniacid ", array(':id' => $id , ':uniacid' => $uniacid));
			if (empty($row)) {
				message('信息不存在或是已经被删除！');
			} 
			pdo_delete('choujiang_xcx', array('id' => $id , 'uniacid' => $uniacid));
			message('删除成功!', $this -> createWeburl('choujiang_xcx', array('op' => 'content')), 'success');
		} 
			//多删
		if(!empty($_GPC['deleteall']))
		{
			for($i=0;$i<count($_GPC['deleteall']);$i++)
			{
				pdo_delete('choujiang_xcx', array('id' =>$_GPC['deleteall'][$i]));
			}
			message('删除成功!', $this -> createWeburl('choujiang_xcx', array('op' => 'content')), 'success');
			
		}

		if($op == 'post'){
					
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename('choujiang_xcx') . " WHERE id = :id" , array(':id' => $id));
				if (empty($item)) {
					message('抱歉，信息不存在或是已经删除！', '', 'error');
				} 
				    	
			} 
			if (checksubmit('submit')) {
				if (empty($_GPC['cj']['name'])) {
					message('奖品名称不能为空，请输入奖品名称！');
				} 
				if (empty($id)) {
					$_GPC['cj']['uniacid'] = $uniacid;
					$str = pdo_insert('choujiang_xcx', $_GPC['cj']);
					    	
				} else {
					$str = pdo_update('choujiang_xcx', $_GPC['cj'], array('id' => $id , 'uniacid' => $uniacid));
				} 
				if(!empty($str)){

					message('信息 添加/修改 成功!', $this -> createWeburl('choujiang_xcx', array('op' => 'content')), 'success');

				}
			} 
		}
		include $this->template('choujiang_xcx');
	}


	// 常见问题
	public function doWebChoujiang_problems(){
		global $_W,$_GPC;
		$op = $_GPC['op'];
		$ops = array('post','content','delete','index');
		$op = in_array($op, $ops) ? $op : 'content';
		$uniacid = $_W['uniacid'];
		if ($op == 'content') {
			$pindex =max(1, intval($_GPC['page']));
			$psize =10;//每页显示个数
			$keyword = $_GPC['keyword'];
			if($keyword){
				$products = pdo_fetchall("select * from ".tablename("choujiang_problems"). " where uniacid=:uniacid and title like '%{$keyword}%' ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_problems')." where uniacid=:uniacid  and title like '%{$keyword}%'",array(':uniacid'=>$uniacid));
			}else{
				$products = pdo_fetchall("select * from ".tablename("choujiang_problems"). " where uniacid=:uniacid ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_problems').' where uniacid=:uniacid ',array(':uniacid'=>$uniacid));
			}
			
			$pager =pagination($total, $pindex, $psize);	
		}
		if($op == 'index'){
			$xcx_id = intval($_GPC['xcx_id']);
			$row = pdo_fetch("SELECT * FROM " . tablename('choujiang_problems') . " WHERE id = :id and uniacid = :uniacid ", array(':id' => $xcx_id , ':uniacid' => $uniacid));
			if($row['status'] == 0){
				$data['status'] = 1;
			}else if($row['status'] == 1){
				$data['status'] = 0;
			}
			$str = pdo_update('choujiang_problems', $data, array('id' => $xcx_id , 'uniacid' => $uniacid));
			$fmdata = array(
				"success" => 1,
				"data" =>$data['status'],
			);	 
			echo json_encode($fmdata);
			exit;
		}
		if ($op == 'delete') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT * FROM " . tablename('choujiang_problems') . " WHERE id = :id and uniacid = :uniacid ", array(':id' => $id , ':uniacid' => $uniacid));
			if (empty($row)) {
				message('信息不存在或是已经被删除！');
			} 
			pdo_delete('choujiang_problems', array('id' => $id , 'uniacid' => $uniacid));
			message('删除成功!', $this -> createWeburl('choujiang_problems', array('op' => 'content')), 'success');
		} 
			//多删
		if(!empty($_GPC['deleteall']))
		{
			for($i=0;$i<count($_GPC['deleteall']);$i++)
			{
				pdo_delete('choujiang_problems', array('id' =>$_GPC['deleteall'][$i]));
			}
			message('删除成功!', $this -> createWeburl('choujiang_problems', array('op' => 'content')), 'success');
			
		}

		if($op == 'post'){
					
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename('choujiang_problems') . " WHERE id = :id" , array(':id' => $id));
				if (empty($item)) {
					message('抱歉，信息不存在或是已经删除！', '', 'error');
				} 
				    	
			} 
			if (checksubmit('submit')) {
				if (empty($_GPC['cj']['title'])) {
					message('问题不能为空，请输入问题名称！');
				} 
				if (empty($id)) {
					$_GPC['cj']['uniacid'] = $uniacid;
					$str = pdo_insert('choujiang_problems', $_GPC['cj']);
					    	
				} else {
					$str = pdo_update('choujiang_problems', $_GPC['cj'], array('id' => $id , 'uniacid' => $uniacid));
				} 
				if(!empty($str)){

					message('信息 添加/修改 成功!', $this -> createWeburl('choujiang_problems', array('op' => 'content')), 'success');

				}
			} 
		}
		include $this->template('choujiang_problems');
	}

	// 皮一下
	public function doWebChoujiang_fur(){
		global $_W,$_GPC;
		$op = $_GPC['op'];
		$ops = array('post','content','delete','index');
		$op = in_array($op, $ops) ? $op : 'content';
		$uniacid = $_W['uniacid'];
		if ($op == 'content') {
			$pindex =max(1, intval($_GPC['page']));
			$psize =10;//每页显示个数
			$keyword = $_GPC['keyword'];
			if($keyword){
				$products = pdo_fetchall("select * from ".tablename("choujiang_fur"). " where uniacid=:uniacid and title like '%{$keyword}%' ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_fur')." where uniacid=:uniacid  and title like '%{$keyword}%'",array(':uniacid'=>$uniacid));
			}else{
				$products = pdo_fetchall("select * from ".tablename("choujiang_fur"). " where uniacid=:uniacid ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_fur').' where uniacid=:uniacid ',array(':uniacid'=>$uniacid));
			}
			foreach($products as $key => $value){
				if($value['category'] == 0){
					$products[$key]['category_title'] = '实物';
				}else if($value['category'] == 1){
					$products[$key]['category_title'] = '电子卡';
				}else if($value['category'] == 2){
					$products[$key]['category_title'] = '红包';
				}
			}
			$pager =pagination($total, $pindex, $psize);	
		}
		if($op == 'index'){
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT * FROM " . tablename('choujiang_fur') . " WHERE id = :id and uniacid = :uniacid ", array(':id' => $id , ':uniacid' => $uniacid));
			if($row['status'] == 0){
				$data['status'] = 1;
			}else if($row['status'] == 1){
				$data['status'] = 0;
			}
			pdo_update('choujiang_fur', $data, array('id' => $id , 'uniacid' => $uniacid));
			$fmdata = array(
				"success" => 1,
				"data" =>$data['status'],
			);
			echo json_encode($fmdata);
			exit;
		}
		if ($op == 'delete') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT * FROM " . tablename('choujiang_fur') . " WHERE id = :id and uniacid = :uniacid ", array(':id' => $id , ':uniacid' => $uniacid));
			if (empty($row)) {
				message('信息不存在或是已经被删除！');
			} 
			pdo_delete('choujiang_fur', array('id' => $id , 'uniacid' => $uniacid));
			message('删除成功!', $this -> createWeburl('choujiang_fur', array('op' => 'content')), 'success');
		} 
			//多删
		if(!empty($_GPC['deleteall']))
		{
			for($i=0;$i<count($_GPC['deleteall']);$i++)
			{
				pdo_delete('choujiang_fur', array('id' =>$_GPC['deleteall'][$i]));
			}
			message('删除成功!', $this -> createWeburl('choujiang_fur', array('op' => 'content')), 'success');
			
		}

		if($op == 'post'){
					
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename('choujiang_fur') . " WHERE id = :id" , array(':id' => $id));
				if (empty($item)) {
					message('抱歉，信息不存在或是已经删除！', '', 'error');
				} 
				    	
			} 
			if (checksubmit('submit')) {
				if (empty($_GPC['cj']['title'])) {
					message('问题不能为空，请输入问题名称！');
				} 
				if (empty($id)) {
					$_GPC['cj']['uniacid'] = $uniacid;
					$str = pdo_insert('choujiang_fur', $_GPC['cj']);
					    	
				} else {
					$str = pdo_update('choujiang_fur', $_GPC['cj'], array('id' => $id , 'uniacid' => $uniacid));
				} 
				if(!empty($str)){

					message('信息 添加/修改 成功!', $this -> createWeburl('choujiang_fur', array('op' => 'content')), 'success');

				}
			} 
		}
		include $this->template('choujiang_fur');
	}

	// 中奖记录
	public function doWebChoujiang_record(){
		global $_W,$_GPC;
		$op = $_GPC['op'];
		$ops = array('post','content');
		$op = in_array($op, $ops) ? $op : 'content';
		$uniacid = $_W['uniacid'];
		if ($op == 'content') {
			$pindex =max(1, intval($_GPC['page']));
			$psize =10;//每页显示个数
			$keyword = $_GPC['keyword'];
			if($keyword){
				$products = pdo_fetchall("select * from ".tablename("choujiang_record"). " where uniacid=:uniacid and goods_name like '%{$keyword}%' ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_record')." where uniacid=:uniacid and goods_name like '%{$keyword}%'",array(':uniacid'=>$uniacid));

			}else{
				$products = pdo_fetchall("select * from ".tablename("choujiang_record"). " where uniacid=:uniacid ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_record')." where uniacid=:uniacid ",array(':uniacid'=>$uniacid));

			}
			foreach($products as $key => $value){
				$goods_id = $value['goods_id'];
				$status = $value['status']; 
				$goods = pdo_fetch("SELECT * FROM " . tablename('choujiang_goods') . " WHERE id = :id " , array(':id' => $goods_id));
				$products[$key]['goods_icon'] = $goods['goods_icon'];
				if($goods['is_del'] == -1){
					$products[$key]['goods_name'] = $goods['goods_icon'].' (已作废)';
				}
				if($goods['status'] == 0){
					$str = '未开奖';
				}else if(empty($goods)){
					$str = '该奖品已失效';
				}elseif($status == 1){
					$str = '已中奖';
				}elseif($status == -1){
					$str = '已作废(未填写地址)';
				}else{
					$str = '未中奖';
				}
				if($value['get_status'] == 1){
					$products[$key]['get_title']  = '商家配送';
				}else if($value['get_status'] == 2){
					$products[$key]['get_title']  = '上门自提';
				}else if($value['get_status'] == 0){
					$products[$key]['get_title']  = '未填写';
				}
				$products[$key]['state'] = $str;
				$products[$key]['create_time'] = date('Y-m-d H:i',$value['create_time']);
				$products[$key]['finish_time'] = date('Y-m-d H:i',$value['finish_time']);
			}
			$pager =pagination($total, $pindex, $psize);	

		}
		if($op == 'post'){
			$id = $_GPC['id'];
			$item = pdo_fetch("select * from ".tablename("choujiang_record"). " where uniacid=:uniacid and id = :id",array(":id"=>$id,"uniacid"=>$uniacid));

		}

		include $this->template('choujiang_record');
	}





 // 新增奖品生成二维码图片
    public function doWebInvitation($id,$smoke_set){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $goods_id = $id;
        // $goods_id = 353;
        $result = pdo_fetch('SELECT * FROM ' . tablename('choujiang_base') . " where uniacid=:uniacid", array(":uniacid" => $uniacid));
        $APPID = $result['appid'];
        $SECRET = $result['appsecret'];
        $tokenUrl="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$APPID}&secret={$SECRET}";
        $getArr=array();
        $tokenArr=json_decode($this->send_post($tokenUrl,$getArr,"GET"));
        $access_token=$tokenArr->access_token;
        if($smoke_set == 3){
	        $noncestr = '/choujiang_page/xianchang_kj/xianchang_kj?id='.$goods_id;
        }else{
	        $noncestr = '/choujiang_page/fuli_xq/fuli_xq?id='.$goods_id;
	    }
        $width=430;
        $post_data='{"path":"'.$noncestr.'","width":'.$width.'}';
        // $url="https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=".$access_token;
        $url = 'https://api.weixin.qq.com/wxa/getwxacode?access_token='.$access_token;

        $result=$this->api_notice_increment($url,$post_data); 

        $image_name = md5(uniqid(rand())).".jpg";
        $filepath = "../attachment/choujiang_page/{$image_name}";   
        $file_put = file_put_contents($filepath, $result);
        //  var_dump($file_put);
        // exit;
        if($file_put){
            $sql = pdo_fetch('SELECT * FROM ' . tablename('choujiang_verification') . " where uniacid=:uniacid and goods_id = :id", array(":uniacid" => $uniacid,':id'=>$goods_id));
            if(empty($sql)){
                $datas = array('verification' => $image_name,'uniacid' => $uniacid,'goods_id'=>$goods_id);
                pdo_insert("choujiang_verification", $datas);                  
            }else{
                $datas = array('verification' => $image_name);
                pdo_update("choujiang_verification", $datas,array('goods_id' => $goods_id,'uniacid' => $uniacid));                  
            }
        }
        else{
             $filepath = "attachment/choujiang_page/{$image_name}"; 
        }

        return $filepath;

    }

    private function send_post($url, $post_data,$method='POST') {
        $postdata = http_build_query($post_data);
        $options = array(
          'http' => array(
            'method' => $method, //or GET
            'header' => 'Content-type:application/x-www-form-urlencoded',
            'content' => $postdata,
            'timeout' => 15 * 60 // 超时时间（单位:s）
          )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
    private function api_notice_increment($url, $data){
        $ch = curl_init();
       // $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
          return false;
        }else{
          return $tmpInfo;
        }
    }

    // 支付管理
    // 常见问题
	public function doWebChoujiang_pay(){
		global $_W,$_GPC;
		$op = $_GPC['op'];
		$ops = array('post','content','delete');
		$op = in_array($op, $ops) ? $op : 'content';
		$uniacid = $_W['uniacid'];
		if ($op == 'content') {
			$pindex =max(1, intval($_GPC['page']));
			$psize =10;//每页显示个数
			$keyword = $_GPC['keyword'];
			if($keyword){
				$products = pdo_fetchall("select * from ".tablename("choujiang_vip_num"). " where uniacid=:uniacid and title like '%{$keyword}%' ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_vip_num')." where uniacid=:uniacid  and title like '%{$keyword}%'",array(':uniacid'=>$uniacid));
			}else{
				$products = pdo_fetchall("select * from ".tablename("choujiang_vip_num"). " where uniacid=:uniacid ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_vip_num').' where uniacid=:uniacid ',array(':uniacid'=>$uniacid));
			}
			
			$pager =pagination($total, $pindex, $psize);	
		}
		if ($op == 'delete') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT * FROM " . tablename('choujiang_vip_num') . " WHERE id = :id and uniacid = :uniacid ", array(':id' => $id , ':uniacid' => $uniacid));
			if (empty($row)) {
				message('信息不存在或是已经被删除！');
			} 
			pdo_delete('choujiang_vip_num', array('id' => $id , 'uniacid' => $uniacid));
			message('删除成功!', $this -> createWeburl('choujiang_pay', array('op' => 'content')), 'success');
		} 
			//多删
		if(!empty($_GPC['deleteall']))
		{
			for($i=0;$i<count($_GPC['deleteall']);$i++)
			{
				pdo_delete('choujiang_vip_num', array('id' =>$_GPC['deleteall'][$i]));
			}
			message('删除成功!', $this -> createWeburl('choujiang_pay', array('op' => 'content')), 'success');
			
		}

		if($op == 'post'){
					
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename('choujiang_vip_num') . " WHERE id = :id" , array(':id' => $id));
				if (empty($item)) {
					message('抱歉，信息不存在或是已经删除！', '', 'error');
				} 
				    	
			} 
			if (checksubmit('submit')) {
				if (empty($_GPC['cj']['title'])) {
					message('问题不能为空，请输入问题名称！');
				} 
				if (empty($id)) {
					$_GPC['cj']['uniacid'] = $uniacid;
					$str = pdo_insert('choujiang_vip_num', $_GPC['cj']);
					    	
				} else {
					$str = pdo_update('choujiang_vip_num', $_GPC['cj'], array('id' => $id , 'uniacid' => $uniacid));
				} 
				if(!empty($str)){

					message('信息 添加/修改 成功!', $this -> createWeburl('choujiang_pay', array('op' => 'content')), 'success');

				}
			} 
		}
		include $this->template('choujiang_pay');
	}

	public function doWebChoujiang_pay_record(){
		global $_W,$_GPC;
		$op = $_GPC['op'];
		$uniacid = $_W['uniacid'];
		$pindex =max(1, intval($_GPC['page']));
		$psize =20;//每页显示个数
		$keyword = $_GPC['keyword'];
		if($keyword){
			$products = pdo_fetchall("select * from ".tablename("choujiang_pay_record"). " where uniacid=:uniacid and nickname like '%{$keyword}%' ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
			$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_pay_record')." where uniacid=:uniacid and nickname like '%{$keyword}%'",array(':uniacid'=>$uniacid));

		}else{
			$products = pdo_fetchall("select * from ".tablename("choujiang_pay_record"). " where uniacid=:uniacid ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
			$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_pay_record').' where uniacid=:uniacid ',array(':uniacid'=>$uniacid));

		}
		foreach($products as $key => $value){
			$openid = $value['openid'];
			if($value['status'] == 1){
				$pay_type = pdo_fetch("SELECT title FROM " . tablename('choujiang_vip_num') . " WHERE id = :id and uniacid = :uniacid" , array(':id' => $value['vip_id'],':uniacid'=>$uniacid));
				$products[$key]['pay_type'] = $pay_type['title'];
			}else if($value['status'] == 2){
				$products[$key]['pay_type'] = '发起红包抽奖';
			}else if($value['status'] == 6){
				$products[$key]['pay_type'] = '增加小程序跳转';
			}else if($value['status'] == 3){
				$products[$key]['pay_type'] = '付费抽奖';
				$products[$key]['y_total'] = $value['total'];
			}else if($value['status'] == 4){
				$products[$key]['pay_type'] = '用户提现';
				$products[$key]['total'] = '-'.$value['total'];
			}
			else if($value['status'] == 7){
				$products[$key]['pay_type'] = '运费';
				$products[$key]['y_total'] = $value['total'];
			}
			
			$products[$key]['create_time'] = date('Y-m-d H:i',$value['create_time']);
		}
		$pager =pagination($total, $pindex, $psize);	
		include $this->template('choujiang_pay_record');

	}

	// 用户提现
	public function doWebChoujiang_withdrawal(){
		global $_W,$_GPC;
		$op = $_GPC['op'];
		$ops = array('content','yes','no');
		$op = in_array($op, $ops) ? $op : 'content';
		$uniacid = $_W['uniacid'];
		if ($op == 'content') {
			$pindex =max(1, intval($_GPC['page']));
			$psize =20;//每页显示个数
			$keyword = $_GPC['keyword'];
			if($keyword){
				$products = pdo_fetchall("select * from ".tablename("choujiang_withdrawal"). " where uniacid=:uniacid and nickname like '%{$keyword}%' ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_withdrawal')." where uniacid=:uniacid  and nickname like '%{$keyword}%'",array(':uniacid'=>$uniacid));
			}else{
				$products = pdo_fetchall("select * from ".tablename("choujiang_withdrawal"). " where uniacid=:uniacid ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_withdrawal').' where uniacid=:uniacid ',array(':uniacid'=>$uniacid));
			}
			foreach($products as $key => $value){
				$products[$key]['create_time'] = date('Y-m-d H:i',$value['create_time']);
				if($value['status'] == 0){
					$products[$key]['status_name'] = '待受理';
				}else if($value['status'] == 1){
					$products[$key]['status_name'] = '已提现';
				}else if($value['status'] == -1){
					$products[$key]['status_name'] = '提现失败';
				}
			}
			
			$pager =pagination($total, $pindex, $psize);	
		}
		if($op == 'yes'){
			$id = intval($_GPC['id']);
			$products = pdo_fetch("select * from ".tablename("choujiang_withdrawal"). " where uniacid=:uniacid and id = :id and status = 0",array(":uniacid"=>$uniacid,":id"=>$id));

			if(!empty($products)){
				$tx = $this->doWebConfirm($products['money'],$products['openid'],$products['nickname']);
				if($tx == 1){
					$str = pdo_update('choujiang_withdrawal', array('status'=> 1), array('id' => $id , 'uniacid' => $uniacid));
				}
			}
			if($str){
				message('信息 添加/修改 成功!', $this -> createWeburl('choujiang_withdrawal', array('op' => 'display')), 'success');
			}else{
				message('信息 添加/修改 失败!', $this -> createWeburl('choujiang_withdrawal', array('op' => 'display')), 'error');
			}
			// if($str){
			// 	$fmdata = array(
			// 		"success" => 1,
			// 		"data" => 1,
			// 	);	 
			// }else{
			// 	$fmdata = array(
			// 		"success" => 1,
			// 		"data" => -1,
			// 	);	 
			// }
			
			// echo json_encode($fmdata);
			// exit;
		}
		if($op == 'no'){
			$id = intval($_GPC['id']);
			$products = pdo_fetch("select * from ".tablename("choujiang_withdrawal"). " where uniacid=:uniacid and id = :id and status = 0",array(":uniacid"=>$uniacid,":id"=>$id));
			if(!empty($products)){
				$member = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where uniacid=:uniacid and openid = :openid", array(":uniacid" => $_W['uniacid'],':openid' => $products['openid']));
				$remaining_sum = floatval($member['remaining_sum']);
            	$new_remaining_sum = $remaining_sum + $products['total'];
				$strs = pdo_update('choujiang_user', array('remaining_sum'=> $new_remaining_sum), array('id' => $member['id']));
				$str = pdo_update('choujiang_withdrawal', array('status'=> -1), array('id' => $id , 'uniacid' => $uniacid));
			}
			if($str && $strs){
				message('信息 添加/修改 成功!', $this -> createWeburl('choujiang_withdrawal', array('op' => 'display')), 'success');
			}else{
				message('信息 添加/修改 失败!', $this -> createWeburl('choujiang_withdrawal', array('op' => 'display')), 'error');
			}
			// 	$fmdata = array(
			// 		"success" => 1,
			// 		"data" => 1,
			// 	);	 
			// }else{
			// 	$fmdata = array(
			// 		"success" => 1,
			// 		"data" => -1,
			// 	);	 
			// }
			// echo json_encode($fmdata);
			// exit;
		}
		include $this->template('choujiang_withdrawal');

	}
	// 提现方法
	public function doWebConfirm($total,$openid,$nickname)
	{
		global $_W, $_GPC;
		include 'wxtx.php';
		load()->func('tpl');
		$user_openid = $openid;
		$tx_cost = intval($total * 100);
		$uniacid = $_W['uniacid'];
		$u_name = $nickname;
		$key = pdo_fetch("SELECT * FROM ".tablename("choujiang_base")." where uniacid=:uniacid",array(":uniacid"=>$uniacid));
		   // $appsecret =$key['appsecret'];
		$appid = $key['appid'];   //微信公众平台的appid
		$mch_id = $key['mch_id'];  //商户号id
		$openid = $user_openid;    //用户openid
		$amount = $tx_cost;  //提现金额$money_sj
		$desc = "提现";     //企业付款描述信息
		$appkey = $key['appkey'];   //商户号支付密钥
		$re_user_name = $u_name;   //收款用户姓名
		$Weixintx = new WeixinTx($appid,$mch_id,$openid,$amount,$desc,$appkey,$re_user_name);
    	$notify_url = $Weixintx->Wxtx();
           //var_dump($notify_url);
        if($notify_url['return_code']=="SUCCESS" && $notify_url['result_code']=="SUCCESS"){
        	$str = 1;
		} else {
			$str = -1;
		}
		return $str;

	}


	// 骗审管理
	public function doWebChoujiang_cheat(){
		global $_W,$_GPC;
		$op = $_GPC['op'];
		$ops = array('post','content','delete','index');
		$op = in_array($op, $ops) ? $op : 'content';
		$uniacid = $_W['uniacid'];
		if ($op == 'content') {
			$pindex =max(1, intval($_GPC['page']));
			$psize =10;//每页显示个数

			$products = pdo_fetchall("select * from ".tablename("choujiang_cheat"). " where uniacid=:uniacid ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
			$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_cheat').' where uniacid=:uniacid ',array(':uniacid'=>$uniacid));
			
			$pager =pagination($total, $pindex, $psize);
			$base = pdo_fetch("SELECT * FROM ".tablename("choujiang_base")." where uniacid=:uniacid",array(":uniacid"=>$uniacid));
			    	
		}
		if ($op == 'delete') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT * FROM " . tablename('choujiang_cheat') . " WHERE id = :id and uniacid = :uniacid ", array(':id' => $id , ':uniacid' => $uniacid));
			if (empty($row)) {
				message('信息不存在或是已经被删除！');
			} 
			pdo_delete('choujiang_cheat', array('id' => $id , 'uniacid' => $uniacid));
			message('删除成功!', $this -> createWeburl('choujiang_cheat', array('op' => 'content')), 'success');
		} 
			//多删
		if(!empty($_GPC['deleteall']))
		{
			for($i=0;$i<count($_GPC['deleteall']);$i++)
			{
				pdo_delete('choujiang_cheat', array('id' =>$_GPC['deleteall'][$i]));
			}
			message('删除成功!', $this -> createWeburl('choujiang_cheat', array('op' => 'content')), 'success');
		}

			//骗审按钮
		if($_GPC['teshu'] == 1)
		{
			pdo_update('choujiang_base', array('cheat_status' =>$_GPC['cheat_status']),array('uniacid' => $uniacid));
			message('操作成功', $this -> createWeburl('choujiang_cheat', array('op' => 'content')), 'success');
		}

		if($op == 'post'){
					
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename('choujiang_cheat') . " WHERE id = :id" , array(':id' => $id));
				if (empty($item)) {
					message('抱歉，信息不存在或是已经删除！', '', 'error');
				} 
				    	
			} 
			if (checksubmit('submit')) {
				if (empty($_GPC['cj']['title'])) {
					message('标题不能为空，请输入标题！');
				} 
				if (empty($id)) {
					$_GPC['cj']['uniacid'] = $uniacid;
					$str = pdo_insert('choujiang_cheat', $_GPC['cj']);
					    	
				} else {
					$str = pdo_update('choujiang_cheat', $_GPC['cj'], array('id' => $id , 'uniacid' => $uniacid));
				} 
				if(!empty($str)){

					message('信息 添加/修改 成功!', $this -> createWeburl('choujiang_cheat', array('op' => 'content')), 'success');

				}
			} 
		}
		include $this->template('choujiang_cheat');
	}

	// 骗审导航
	public function doWebChoujiang_cheat_nav(){
		global $_W,$_GPC;
		$op = $_GPC['op'];
		$ops = array('post','content','delete','index');
		$op = in_array($op, $ops) ? $op : 'content';
		$uniacid = $_W['uniacid'];
		if ($op == 'content') {
			$pindex =max(1, intval($_GPC['page']));
			$psize =10;//每页显示个数

			$products = pdo_fetchall("select * from ".tablename("choujiang_cheat_nav"). " where uniacid=:uniacid ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
			$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_cheat_nav').' where uniacid=:uniacid ',array(':uniacid'=>$uniacid));
			
			$pager =pagination($total, $pindex, $psize);
			$base = pdo_fetch("SELECT * FROM ".tablename("choujiang_base")." where uniacid=:uniacid",array(":uniacid"=>$uniacid));
			    	
		}
		if ($op == 'delete') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT * FROM " . tablename('choujiang_cheat_nav') . " WHERE id = :id and uniacid = :uniacid ", array(':id' => $id , ':uniacid' => $uniacid));
			if (empty($row)) {
				message('信息不存在或是已经被删除！');
			} 
			pdo_delete('choujiang_cheat_nav', array('id' => $id , 'uniacid' => $uniacid));
			message('删除成功!', $this -> createWeburl('choujiang_cheat_nav', array('op' => 'content')), 'success');
		} 
			//多删
		if(!empty($_GPC['deleteall']))
		{
			for($i=0;$i<count($_GPC['deleteall']);$i++)
			{
				pdo_delete('choujiang_cheat_nav', array('id' =>$_GPC['deleteall'][$i]));
			}
			message('删除成功!', $this -> createWeburl('choujiang_cheat_nav', array('op' => 'content')), 'success');
		}

			//骗审按钮
		if($_GPC['teshu'] == 1)
		{
			pdo_update('choujiang_base', array('cheat_status' =>$_GPC['cheat_status']),array('uniacid' => $uniacid));
			message('操作成功', $this -> createWeburl('choujiang_cheat_nav', array('op' => 'content')), 'success');
		}

		if($op == 'post'){
					
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename('choujiang_cheat_nav') . " WHERE id = :id" , array(':id' => $id));
				if (empty($item)) {
					message('抱歉，信息不存在或是已经删除！', '', 'error');
				} 
				    	
			} 
			if (checksubmit('submit')) {
				if (empty($_GPC['cj']['title'])) {
					message('标题不能为空，请输入标题！');
				} 
				if (empty($id)) {
					$_GPC['cj']['uniacid'] = $uniacid;
					$str = pdo_insert('choujiang_cheat_nav', $_GPC['cj']);
					    	
				} else {
					$str = pdo_update('choujiang_cheat_nav', $_GPC['cj'], array('id' => $id , 'uniacid' => $uniacid));
				} 
				if(!empty($str)){

					message('信息 添加/修改 成功!', $this -> createWeburl('choujiang_cheat_nav', array('op' => 'content')), 'success');

				}
			} 
		}
		include $this->template('choujiang_cheat_nav');
	}

// 小程序推荐
	public function doWebChoujiang_Advertising(){
		global $_W,$_GPC;
		$op = $_GPC['op'];
		$ops = array('post','content','delete','index');
		$op = in_array($op, $ops) ? $op : 'content';
		$uniacid = $_W['uniacid'];
		if ($op == 'content') {
			$pindex =max(1, intval($_GPC['page']));
			$psize =10;//每页显示个数
			$keyword = $_GPC['keyword'];
			if($keyword){
				$products = pdo_fetchall("select * from ".tablename("choujiang_advertising"). " where uniacid=:uniacid and title like '%{$keyword}%' ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_advertising')." where uniacid=:uniacid  and title like '%{$keyword}%'",array(':uniacid'=>$uniacid));
			}else{
				$products = pdo_fetchall("select * from ".tablename("choujiang_advertising"). " where uniacid=:uniacid ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(":uniacid"=>$uniacid));
				$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_advertising').' where uniacid=:uniacid ',array(':uniacid'=>$uniacid));
			}
			
			$pager =pagination($total, $pindex, $psize);	
		}
		if($op == 'index'){
			$xcx_id = intval($_GPC['xcx_id']);
			$row = pdo_fetch("SELECT * FROM " . tablename('choujiang_advertising') . " WHERE id = :id and uniacid = :uniacid ", array(':id' => $xcx_id , ':uniacid' => $uniacid));
			if($row['to_index'] == 0){
				$data['to_index'] = 1;
			}else if($row['to_index'] == 1){
				$data['to_index'] = 0;
			}
			$str = pdo_update('choujiang_advertising', $data, array('id' => $xcx_id , 'uniacid' => $uniacid));
			$fmdata = array(
				"success" => 1,
				"data" =>$data['to_index'],
			);	 
			echo json_encode($fmdata);
			exit;
		} 	
		if ($op == 'delete') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT * FROM " . tablename('choujiang_advertising') . " WHERE id = :id and uniacid = :uniacid ", array(':id' => $id , ':uniacid' => $uniacid));
			if (empty($row)) {
				message('信息不存在或是已经被删除！');
			} 
			pdo_delete('choujiang_advertising', array('id' => $id , 'uniacid' => $uniacid));
			message('删除成功!', $this -> createWeburl('choujiang_advertising', array('op' => 'content')), 'success');
		} 
			//多删
		if(!empty($_GPC['deleteall']))
		{
			for($i=0;$i<count($_GPC['deleteall']);$i++)
			{
				pdo_delete('choujiang_advertising', array('id' =>$_GPC['deleteall'][$i]));
			}
			message('删除成功!', $this -> createWeburl('choujiang_advertising', array('op' => 'content')), 'success');
			
		}

		if($op == 'post'){
					
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename('choujiang_advertising') . " WHERE id = :id" , array(':id' => $id));
				if (empty($item)) {
					message('抱歉，信息不存在或是已经删除！', '', 'error');
				} 
				    	
			} 
			if (checksubmit('submit')) {
				if (empty($_GPC['cj']['title'])) {
					message('广告标题不能为空，请输入标题！');
				} 
				if (empty($id)) {
					$_GPC['cj']['uniacid'] = $uniacid;
					$str = pdo_insert('choujiang_advertising', $_GPC['cj']);
					    	
				} else {
					$str = pdo_update('choujiang_advertising', $_GPC['cj'], array('id' => $id , 'uniacid' => $uniacid));
				} 
				if(!empty($str)){

					message('信息 添加/修改 成功!', $this -> createWeburl('choujiang_advertising', array('op' => 'content')), 'success');

				}
			} 
		}
		include $this->template('choujiang_advertising');
	}

	//新增商品成功推送至首页后 模板通知
    public function doPageGoodsInform($id){
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $base = pdo_fetch('SELECT * FROM ' . tablename('choujiang_base') ."where `uniacid`='{$uniacid}' ");
        $appid = $base['appid'];
        $appsecret = $base['appsecret'];
        $template_id = $base['template_id2'];
        $tokenUrl="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
        $getArr=array();
        $tokenArr=json_decode($this->send_post($tokenUrl,$getArr,"GET"));
        $access_token=$tokenArr->access_token;
        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token ;
        $dd = array();
        $goods = pdo_fetch("SELECT * FROM " .tablename('choujiang_goods')." where `uniacid`='{$uniacid}' and `id`='{$id}'" );
        if($goods['goods_status'] == 2){
			$goods['goods_name'] = '红包'.$goods['red_envelope'].'元';
        }
        $create_time = date('Y-m-d H:i',time());
        $member = pdo_fetchall("SELECT * FROM " .tablename('choujiang_user')." where `uniacid`='{$uniacid}' and form_id != ''");
        foreach($member as $key => $value){
    		$out_time = strtotime('-7 days',time());
        	$formids = unserialize($value['form_id']);
        	foreach($formids as $k => $v){
        		if($out_time >= $v['form_time']){
	                unset($formids[$k]);
	            }
        	}
        	$formids = array_values($formids); 
            $form_id = $formids[0]['form_id'];
            $dd['form_id'] = $form_id;
            $dd['touser'] = $value['openid'];
            $content = array(
                "keyword1"=>array(
                "value"=> '新的奖品 "'.$goods['goods_name'].'" 已发布',
                "color"=>"#4a4a4a"
                ),
                "keyword2"=>array(
                    "value"=>$create_time,
                    "color"=>""
                ),
                  
            );
            $dd['template_id']=$template_id;
            $dd['page']='choujiang_page/fuli_xq/fuli_xq?id='.$id;  //点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,该字段不填则模板无跳转。
            $dd['data']=$content;                        //模板内容，不填则下发空模板
            $dd['color']='';                        //模板内容字体的颜色，不填默认黑色
            $dd['emphasis_keyword']='';    //模板需要放大的关键词，不填则默认无放大
            $result = $this->https_curl_json($url,$dd,'json');
            foreach($formids as $k => $v){
        		if($form_id == $v['form_id']){
	                unset($formids[$k]);
	            }
        	}
        	$new_formids = array_values($formids); 
        	$datas['form_id'] = serialize($new_formids);
        	pdo_update('choujiang_user',$datas,array('id'=>$value['id']));
        }
        
       return $result;
    }

    private function https_curl_json($url,$data,$type){
        if($type=='json'){
            $headers = array("Content-type: application/json;charset=UTF-8","Accept: application/json","Cache-Control: no-cache", "Pragma: no-cache");
            $data=json_encode($data);
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers );
        $output = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl);
        return $output;
    }



   // 生成海报
function GeneratePoster($id){
    global $_W,$_GPC;
    $uniacid = $_W['uniacid'];

    $tickesql = pdo_fetch('SELECT * FROM ' . tablename('choujiang_verification') . " where uniacid=:uniacid and goods_id = :id", array(":id"=>$id,":uniacid" => $uniacid));
    $goods = pdo_fetch('SELECT * FROM ' . tablename('choujiang_goods') . " where uniacid=:uniacid and id = :id", array(":id"=>$id,":uniacid" => $uniacid));
    if($goods['smoke_set'] == 0){
        $str = $goods['smoke_time'].' 自动开奖';
    }else if($goods['smoke_set'] == 1){
        $str = '参与人数达到 '.$goods['smoke_num'].' 人 自动开奖';
    }else if($goods['smoke_set'] == 2){
        $str = '由发起人手动开奖';
    }else if($goods['smoke_set'] == 3){
        $str = '由发起人现场开奖';
    }
    if($goods['goods_status'] == 2){
        $info['goods_name'] = '红包'.$goods['red_envelope'];
    }else{
        if(strlen($goods['goods_name'])>16){
            $info['goods_name'] = mb_substr($goods['goods_name'], 0, 16,'utf-8').'...';
        }else{
            $info['goods_name'] = $goods['goods_name'];
        }
    }

    $info['goods_num'] = $goods['goods_num'];
    $info['goods_icon'] = $_W['attachurl'].$goods['goods_icon'];
    $info['goods_set'] = $str;   
    $member = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where uniacid=:uniacid and openid = :openid", array(":openid"=>$goods['goods_openid'],":uniacid" => $uniacid));
    $info['nickname'] = $member['nickname'];   

    $background = $_W['siteroot']."addons/choujiang_page/resource/background.jpg";
    $updir = '../attachment/images/';
    $updateUrl = md5(uniqid(rand()));

    $user = $this->downloadWeixinFile($member['avatar']);

    // $test = $this->test($user['body'],$updir.$updateUrl);
    $test = $this->test($user['header']['url'],$updir.$updateUrl);

    $avatarlocal= $updir.$updateUrl.".png";
    $avatarlocal2= $updir.md5(uniqid(rand())).".png";

    $fontfile = MODULE_ROOT.'/resource/font/msyh.ttf';

    $ticketurl = $_W['siteroot'].'attachment/choujiang_page/'.$tickesql['verification']; //二维码
    $img = $this->qrcode($ticketurl,$background,$avatarlocal,$avatarlocal2,$fontfile,$info,$test);
    $image_name = str_replace('../attachment/','',$img); 
    
  	$in = 'https';
    $urls = $_W['siteroot'];
    $sub = substr($urls,0,strpos($urls, ':'));
    if($sub == $in){
        $new_url = $urls;
    }else{
        $new_url = $sub.'s:'.substr($urls,strpos($urls,':')+1);
    }
    $image = $new_url.'attachment/'.$image_name;
    $image = $_W['siteroot'].'attachment/'.$image_name;

    return $image;
}

function qrcode($ticketurl,$background,$avatarlocal,$avatarlocal2,$fontfile,$info,$user){
    $nickanme = $this->autowrap(20,0,$fontfile,$info['nickname'],350);

    $title = $info['goods_name']." x".$info['goods_num'];

    $smoke = $info['goods_set'];
    $tishi = '(长按识别二维码)';
    $faqi = '发起了一个新抽奖';

    $src = imagecreatefromstring(file_get_contents($ticketurl));//从字符串中新建一个二维码图像
    $src2 = imagecreatefromstring(file_get_contents($info['goods_icon']));//从字符串中新建一个二维码图像

    $dst = imagecreatefromstring(file_get_contents($background));

    imagejpeg($src,$avatarlocal);//保存二维码
    imagejpeg($src2,$avatarlocal2);//保存二维码

    list($width, $height)=getimagesize($avatarlocal);//拿到图片宽高
    list($width2, $height2)=getimagesize($avatarlocal2);//拿到图片宽高

    $b = imagettfbbox(12,0,$fontfile,$nickanme); //用户名长度

    $w = abs($b[2] - $b[0]);
    $b1 = imagettfbbox(14,0,$fontfile,$faqi); 

    $w1 = abs($b1[2] - $b1[0]);
    $b2 = imagettfbbox(11,0,$fontfile,$tishi); 

    $w2 = abs($b2[2] - $b2[0]);
    //缩放粘贴二维码至背景图

    // $per=0.5;

    // $n_w=$width*$per;

    // $n_h=$height*$per;
    $n_w=150;

    $n_h=150;

    $n_w2=380;

    $n_h2=200;

    $new=imagecreatetruecolor($n_w, $n_h);//新建一个真彩色图像 
    $new2=imagecreatetruecolor($n_w2, $n_h2);//新建一个真彩色图像 

    $img=imagecreatefromjpeg($avatarlocal);
    $img2=imagecreatefromjpeg($avatarlocal2);

    imagecopyresized($new, $img,0, 0,0, 0,$n_w, $n_h, $width, $height);  //二维码
    imagecopyresized($new2, $img2,0, 0,0, 0,$n_w2, $n_h2, $width2, $height2); //奖品图片

    imagecopymerge($dst, $new, 135, 520, 0, 0, $n_w, $n_h, 100);//二维码
    imagecopymerge($dst, $new2, 20, 180, 0, 0, $n_w2, $n_h2, 100);//奖品图片

    /*头像粘贴至背景图*/

    $im = imagecreatefrompng($user);

    imagecopyresampled($dst,$im,178,30,0,0,60,60,60,60);

    //粘贴文字

    $yellow = imagecolorallocate($dst, 252, 226, 191); //黄色
    $color = imagecolorallocate($dst, 255, 255, 255); //白色
    $black = imagecolorallocate($dst, 0 , 0 ,0); //黑色
    $hui = imagecolorallocate($dst, 171 , 171 ,171); //深灰
    $hui1 = imagecolorallocate($dst, 157 , 157 ,157); //蛋灰

    // $color = imagecolorallocate($dst, 0, 0, 0);
    imagettftext($dst,12,0,(420-$w)/2,115,$yellow,$fontfile,$nickanme);

    imagettftext($dst,14,0,20,415,$black,$fontfile,$title);

    imagettftext($dst,12,0,20,440,$hui1,$fontfile,$smoke);
    imagettftext($dst,12,0,(420-$w2)/2,690,$hui,$fontfile,$tishi);
    imagettftext($dst,14,0,(420-$w1)/2,150,$yellow,$fontfile,$faqi);



    
    //保存    

    imagepng($dst,$avatarlocal);

    imagedestroy($im);

    imagedestroy($new);

    imagedestroy($img);

    imagedestroy($dst);

    return $avatarlocal;

}

function autowrap($fontsize, $angle=0, $fontface="", $string, $width) {

    // 这几个变量分别是 字体大小, 角度, 字体名称, 字符串, 预设宽度

     $content = "";

     $v = 0;

     // 将字符串拆分成一个个单字 保存到数组 letter 中

     for ($i=0;$i<mb_strlen($string,'utf-8');$i++) {

         $letter[] = mb_substr($string, $i, 1,'utf-8');

     }

    

     foreach ($letter as $l) {

         $v+=1;

         $teststr = $content." ".$l;

         $testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);

         // 判断拼接后的字符串是否超过预设的宽度

         if (($testbox[2] > $width) && ($content !== "")) {

             $content .= "\n";

         }

         if($v<30){

              $content .= $l;

         }else{

             $content .='...';

             break;

         }

     }

     return $content;

}



function downloadWeixinFile($url) {

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_HEADER, 0);

    curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $package = curl_exec($ch);

    $httpinfo = curl_getinfo($ch);

    curl_close($ch);

    $imageAll = array_merge(array('header' => $httpinfo), array('body' => $package));

    return $imageAll;

}
function suofang($url,$useravatar){
    $filename=$useravatar."sf33.jpg";
    list($width, $height) = getimagesize($url);  
    $new_width = 60;  
    $new_height = 60;  
    $image_p = imagecreatetruecolor($new_width, $new_height);  
    $image = imagecreatefromjpeg($url);  
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);  
    imagepng($image_p,$filename);
    imagedestroy($image_p);
    return $filename;
}

/*处理圆头像并保存*/
function test($url,$useravatar){
    $newurl = $this->suofang($url,$useravatar);
    $img_data = getimagesize($newurl);
    $w = $img_data[0];  $h=$img_data[0]; // original size  
    $original_path= $newurl;  
    $dest_path = $useravatar.'22.png';  
    $src = imagecreatefromstring(file_get_contents($original_path));  
    $newpic = imagecreatetruecolor($w,$h);  
    imagealphablending($newpic,false);  
    $transparent = imagecolorallocatealpha($newpic, 0, 0, 0, 127);  
    $r=$w/2;  
    for($x=0;$x<$w;$x++)  
        for($y=0;$y<$h;$y++){  
            $c = imagecolorat($src,$x,$y);  
            $_x = $x - $w/2;  
            $_y = $y - $h/2;  
            if((($_x*$_x) + ($_y*$_y)) < ($r*$r)){  
                imagesetpixel($newpic,$x,$y,$c);  
            }else{  
                imagesetpixel($newpic,$x,$y,$transparent);  
            }  
        }  
    imagesavealpha($newpic, true);  
    imagepng($newpic, $dest_path);  
    imagedestroy($newpic);  
    imagedestroy($src);  
    return $dest_path;  

}




	/*
		礼物说基本设置
	*/
	public function doWebCj_lws_base() {
		global $_GPC, $_W;
		$op = $_GPC['op'];
		$ops = array('display', 'post');
		$op = in_array($op, $ops) ? $op : 'display';
		$uniacid = $_W['uniacid'];
		if ($op == 'display') {
			$item = pdo_fetch("SELECT * FROM " . tablename('choujiang_lws_base') . " WHERE uniacid = :uniacid", array(':uniacid' => $uniacid));
			$item['slide'] = unserialize($item['slide']);
			if (checksubmit('submit')) {
				$data = array(
					'uniacid' => $_W['uniacid'],
					'name'=>$_GPC['name'],
					'title'=> $_GPC['title'],
					'jianjie'=> $_GPC['jianjie'],
					'video'=>$_GPC['video'],
					'sysm'=>$_GPC['sysm'],
					'yhxy'=>$_GPC['yhxy'],
					'slide'=>serialize($_GPC['slide']),
				);
				if (empty($item['uniacid'])) {
					pdo_insert('choujiang_lws_base', $data);
				} else {
					pdo_update('choujiang_lws_base', $data , array('uniacid' => $uniacid));
				} 
				message('礼物说基本设置更新成功!', $this -> createWebUrl('cj_lws_base', array('op' => 'display')), 'success');
				
			} 
		}
		if($op == 'post'){
			$item = pdo_fetch("SELECT * FROM " . tablename('choujiang_lws_kf') . " WHERE uniacid = :uniacid", array(':uniacid' => $uniacid));
			$item['slide'] = unserialize($item['slide']);
			if (checksubmit('submit')) {
				$data = array(
					'uniacid' => $_W['uniacid'],
					'fapiao'=>$_GPC['fapiao'],
					'lwlq'=> $_GPC['lwlq'],
					'kffw'=> $_GPC['kffw'],
					'swhz'=>$_GPC['swhz'],
					'qyfw'=>$_GPC['qyfw'],
				);
				if (empty($item['uniacid'])) {
					pdo_insert('choujiang_lws_kf', $data);
				} else {
					pdo_update('choujiang_lws_kf', $data , array('uniacid' => $uniacid));
				} 
				message('礼物说客服信息更新成功!', $this -> createWebUrl('cj_lws_base', array('op' => 'post')), 'success');
				
			} 
		} 
		include $this -> template('cj_lws_base');
	} 


	/*
		礼物说商品管理
	*/
	public function doWebCj_lws_goods(){
		global $_W,$_GPC;
		$op = $_GPC['op'];
		$ops = array('display', 'post','delete','louceng','lcpost','del','fenlei','del_fl','flpost');
		$op = in_array($op, $ops) ? $op : 'louceng';
		$uniacid = $_W['uniacid'];
		if ($op == 'louceng') {
			$pindex =max(1, intval($_GPC['page']));
			$psize =10;//每页显示个数
			$products = pdo_fetchall("SELECT * FROM " . tablename('choujiang_lws_sylc') . " WHERE uniacid = :uniacid ORDER BY num DESC LIMIT " . ($pindex - 1)* $psize . ',' . $psize, array(':uniacid' => $uniacid));
			$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_lws_sylc').' where uniacid=:uniacid',array(':uniacid'=>$uniacid));
			$pager =pagination($total, $pindex, $psize);
		} 
		if ($op == 'lcpost') {
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename('choujiang_lws_sylc') . " WHERE id = :id" , array(':id' => $id));
				if (empty($item)) {
					message('抱歉，项目不存在或是已经删除！', '', 'error');
				} 
			} 
			if (checksubmit('submit')) {
				if (empty($_GPC['title'])) {
					message('标题不能为空，请输入标题！');
				} 
				$data = array('uniacid' => $_W['uniacid'], 'title' => $_GPC['title'], 'num' => $_GPC['num'],'y_name'=>$_GPC['y_name'],'thumb'=>$_GPC['thumb']);
				if (empty($id)) {
					pdo_insert('choujiang_lws_sylc', $data);
				} else {
					pdo_update('choujiang_lws_sylc', $data, array('id' => $id , 'uniacid' => $uniacid));
				} 
				message('礼物说首页楼层 添加/修改 成功!', $this -> createWebUrl('cj_lws_goods', array('op' => 'louceng')), 'success');
			} 
		} 
		if ($op == 'del') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT * FROM " . tablename('choujiang_lws_sylc') . " WHERE id = :id and uniacid = :uniacid ", array(':id' => $id , ':uniacid' => $uniacid));
			if (empty($row)) {
				message('内容不存在或是已经被删除！');
			} 
			pdo_delete('choujiang_lws_sylc', array('id' => $id , 'uniacid' => $uniacid));
			message('删除成功!', $this -> createWebUrl('cj_lws_goods', array('op' => 'louceng')), 'success');
		} 
		//多删内容
		if(!empty($_GPC['delall']))
		{
			for($i=0;$i<count($_GPC['delall']);$i++)
			{
				pdo_delete('choujiang_lws_sylc', array('id' =>$_GPC['delall'][$i]));
			}
			message('删除成功!', $this -> createWeburl('cj_lws_goods', array('op' => 'louceng')), 'success');
			
		} 
		if ($op == 'fenlei') {
			$pindex =max(1, intval($_GPC['page']));
			$psize =10;//每页显示个数
			$products = pdo_fetchall("SELECT * FROM " . tablename('choujiang_lws_goods_fl') . " WHERE uniacid = :uniacid ORDER BY num DESC LIMIT " . ($pindex - 1)* $psize . ',' . $psize, array(':uniacid' => $uniacid));
			$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_lws_goods_fl').' where uniacid=:uniacid',array(':uniacid'=>$uniacid));
			$pager =pagination($total, $pindex, $psize);
		} 
		if ($op == 'flpost') {
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename('choujiang_lws_goods_fl') . " WHERE id = :id" , array(':id' => $id));
				if (empty($item)) {
					message('抱歉，项目不存在或是已经删除！', '', 'error');
				} 
			} 
			if (checksubmit('submit')) {
				if (empty($_GPC['title'])) {
					message('标题不能为空，请输入标题！');
				} 
				$data = array('uniacid' => $_W['uniacid'], 'title' => $_GPC['title'], 'num' => $_GPC['num']);
				if (empty($id)) {
					pdo_insert('choujiang_lws_goods_fl', $data);
				} else {
					pdo_update('choujiang_lws_goods_fl', $data, array('id' => $id , 'uniacid' => $uniacid));
				} 
				message('礼物说商品分类 添加/修改 成功!', $this -> createWebUrl('cj_lws_goods', array('op' => 'fenlei')), 'success');
			} 
		} 
		if ($op == 'del_fl') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT * FROM " . tablename('choujiang_lws_goods_fl') . " WHERE id = :id and uniacid = :uniacid ", array(':id' => $id , ':uniacid' => $uniacid));
			if (empty($row)) {
				message('内容不存在或是已经被删除！');
			} 
			pdo_delete('choujiang_lws_goods_fl', array('id' => $id , 'uniacid' => $uniacid));
			message('删除成功!', $this -> createWebUrl('cj_lws_goods', array('op' => 'fenlei')), 'success');
		} 
		//多删内容
		if(!empty($_GPC['delall_fl']))
		{
			for($i=0;$i<count($_GPC['delall_fl']);$i++)
			{
				pdo_delete('choujiang_lws_goods_fl', array('id' =>$_GPC['delall'][$i]));
			}
			message('删除成功!', $this -> createWeburl('cj_lws_goods', array('op' => 'fenlei')), 'success');
			
		}
		if ($op == 'display') {
			$pindex =max(1, intval($_GPC['page']));
			$psize =10;//每页显示个数
			$products = pdo_fetchall("SELECT a.*,b.title as titles FROM " . tablename('choujiang_lws_goods') ."as a left join".tablename('choujiang_lws_goods_fl'). " as b on a.fl_id = b.id WHERE a.uniacid = :uniacid ORDER BY a.num DESC LIMIT " . ($pindex - 1)* $psize . ',' . $psize, array(':uniacid' => $uniacid));
			$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_lws_goods').' where uniacid=:uniacid',array(':uniacid'=>$uniacid));
			$pager =pagination($total, $pindex, $psize);
		} 
		if ($op == 'post') {
			//查询首页楼层
			$louceng = pdo_fetchall("SELECT * from ".tablename('choujiang_lws_sylc')."where uniacid = :uniacid",array(':uniacid'=>$uniacid));
			//查询商品分类
			$fenlei = pdo_fetchall("SELECT * from".tablename('choujiang_lws_goods_fl')."where uniacid = '{$uniacid}' ");
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename('choujiang_lws_goods') . " WHERE id = :id" , array(':id' => $id));
				$item['slide'] = unserialize($item['slide']);
				if (empty($item)) {
					message('抱歉，项目不存在或是已经删除！', '', 'error');
				} 
			} 
			if (checksubmit('submit')) {
				if (empty($_GPC['title'])) {
					message('标题不能为空，请输入商品标题！');
				} 
				$data = array('uniacid'=>$uniacid,'title'=>$_GPC['title'],'num'=>$_GPC['num'],'thumb'=>$_GPC['thumb'],'slide'=>serialize($_GPC['slide']),'jianjie'=>$_GPC['jianjie'],'price'=>$_GPC['price'],'lc_id'=>$_GPC['lc_id'],'fl_id'=>$_GPC['fl_id'],'text'=>$_GPC['text'],'status'=>$_GPC['status'],'kucun'=>$_GPC['kucun']);
				if (empty($id)) {
					pdo_insert('choujiang_lws_goods', $data);
				} else {
					pdo_update('choujiang_lws_goods', $data, array('id' => $id , 'uniacid' => $uniacid));
				} 
				message('商品 添加/修改 成功!', $this -> createWebUrl('cj_lws_goods', array('op' => 'display')), 'success');
			} 
		} 
		if ($op == 'delete') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT * FROM " . tablename('choujiang_lws_goods') . " WHERE id = :id and uniacid = :uniacid ", array(':id' => $id , ':uniacid' => $uniacid));
			if (empty($row)) {
				message('商品不存在或是已经被删除！');
			} 
			pdo_delete('choujiang_lws_goods', array('id' => $id , 'uniacid' => $uniacid));
			message('删除成功!', $this -> createWebUrl('cj_lws_goods', array('op' => 'display')), 'success');
		} 
		//多删内容
		if(!empty($_GPC['deleteall']))
		{
			for($i=0;$i<count($_GPC['deleteall']);$i++)
			{
				pdo_delete('choujiang_lws_goods', array('id' =>$_GPC['deleteall'][$i]));
			}
			message('删除成功!', $this -> createWeburl('cj_lws_goods', array('op' => 'content')), 'success');
			
		} 
		include $this->template('cj_lws_goods');

	}


	//礼物说订单管理
	public function doWebCj_lws_order(){
		global $_W,$_GPC;
		$op = $_GPC['op'];
		$ops = array('display', 'post','delete','del');
		$op = in_array($op, $ops) ? $op : 'display';
		$uniacid = $_W['uniacid'];
		if ($op == 'display') {
			$pindex =max(1, intval($_GPC['page']));
			$psize =10;//每页显示个数
			$products = pdo_fetchall("SELECT a.*,b.avatar,b.nickname FROM " . tablename('choujiang_lws_order') ." as a left join ".tablename('choujiang_user'). "as b on a.openid = b.openid WHERE a.uniacid = :uniacid ORDER BY a.id DESC LIMIT " . ($pindex - 1)* $psize . ',' . $psize, array(':uniacid' => $uniacid));
			$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_lws_order').' where uniacid=:uniacid',array(':uniacid'=>$uniacid));
			$pager =pagination($total, $pindex, $psize);
		} 
		if ($op == 'delete') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT * FROM " . tablename('choujiang_lws_order') . " WHERE id = :id and uniacid = :uniacid ", array(':id' => $id , ':uniacid' => $uniacid));
			if (empty($row)) {
				message('订单不存在或是已经被删除！');
			} 
			pdo_delete('choujiang_lws_order', array('id' => $id , 'uniacid' => $uniacid));
			message('删除成功!', $this -> createWebUrl('cj_lws_order', array('op' => 'display')), 'success');
		} 
		//多删内容
		if(!empty($_GPC['deleteall']))
		{
			for($i=0;$i<count($_GPC['deleteall']);$i++)
			{
				pdo_delete('choujiang_lws_order', array('id' =>$_GPC['deleteall'][$i]));
			}
			message('删除成功!', $this -> createWeburl('cj_lws_order', array('op' => 'display')), 'success');
			
		} 
		//查询详情
		if($op == 'post'){
			$orderid = $_GPC['orderid'];
			$pindex =max(1, intval($_GPC['page']));
			$psize =10;//每页显示个数
			$order = pdo_fetchall("SELECT a.*,b.sltj,b.counts FROM " . tablename('choujiang_lws_order_liwu') ." as a left join".tablename('choujiang_lws_order')." as b on a.orderid = b.orderid WHERE a.uniacid = :uniacid and a.orderid = :orderid ORDER BY a.id DESC LIMIT " . ($pindex - 1)* $psize . ',' . $psize, array(':uniacid' => $uniacid,':orderid'=>$orderid));
			$total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_lws_order_liwu').' where uniacid=:uniacid and orderid = :orderid ',array(':uniacid'=>$uniacid,':orderid'=>$orderid));
			$pager =pagination($total, $pindex, $psize);
			//发货
			if (checksubmit('submit')) {
				$id = $_GPC['id'];
				//var_dump($id);
				//查询订单信息
				$liwu = pdo_fetch("SELECT * FROM".tablename('choujiang_lws_order_liwu')."where uniacid ='{$uniacid}' and id = '{$id}' ");
				if(empty($_GPC['kd_gongsi'])){
					message('请输入快递公司');
				}
				if(empty($_GPC['kd_order'])){
					message('请输入快递单号');
				}
				$data = array(
					'kd_gongsi' => $_GPC['kd_gongsi'],
					'kd_order'=> $_GPC['kd_order'],
					'fh_time'=>time(),
					'status'=>2,
				);
				pdo_update('choujiang_lws_order_liwu', $data , array('id' => $id,'uniacid'=>$uniacid));
				$this->doPageLws_fh_ok($liwu['orderid'],$liwu['openid']);
				message('发货成功!', $this -> createWebUrl('cj_lws_order', array('op' => 'display')), 'success');
			}
		}
		if ($op == 'del') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT * FROM " . tablename('choujiang_lws_order_liwu') . " WHERE id = :id and uniacid = :uniacid ", array(':id' => $id , ':uniacid' => $uniacid));
			if (empty($row)) {
				message('订单不存在或是已经被删除！');
			} 
			pdo_delete('choujiang_lws_order_liwu', array('id' => $id , 'uniacid' => $uniacid));
			message('删除成功!', $this -> createWebUrl('cj_lws_order', array('op' => 'display')), 'success');
		} 
		//多删内容
		if(!empty($_GPC['delall']))
		{
			for($i=0;$i<count($_GPC['delall']);$i++)
			{
				pdo_delete('choujiang_lws_order_liwu', array('id' =>$_GPC['delall'][$i]));
			}
			message('删除成功!', $this -> createWeburl('cj_lws_order', array('op' => 'display')), 'success');
			
		} 


		include $this->template('cj_lws_order');
	}


	//礼物发货成功通知
    public function doPageLws_fh_ok($orderid,$openid){
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        //var_dump($orderid,$openid);exit();
        $base = pdo_fetch('SELECT * FROM ' . tablename('choujiang_base') ."where `uniacid`='{$uniacid}' ");
        $appid = $base['appid'];
        $appsecret = $base['appsecret'];
        $template_id = $base['template_id5'];
        $tokenUrl="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
        $getArr=array();
        $tokenArr=json_decode($this->send_post($tokenUrl,$getArr,"GET"));
        $access_token=$tokenArr->access_token;
        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token ;
        //查询订单详情
        $order = pdo_fetch("SELECT * FROM ".tablename("choujiang_lws_order")."where uniacid ='{$uniacid}' and orderid = '{$orderid}' ");
        $order_shouli = pdo_fetch("SELECT * FROM".tablename('choujiang_lws_order_liwu')."where uniacid = '{$uniacid}' and openid = '{$openid}' and orderid = '{$orderid}' and status = 2 ");
        $value = array(
                    "keyword1"=>array(
                    "value"=> $order['title'],
                    "color"=>"#4a4a4a"
                    ),
                    "keyword2"=>array(
                        "value"=>$orderid,
                        "color"=>"#9b9b9b"
                    ),
                    "keyword3"=>array(
                        "value"=>$order_shouli['user_address'],
                        "color"=>"#9b9b9b"
                    ),
                    "keyword4"=>array(
                        "value"=>$order_shouli['kd_gongsi'],
                        "color"=>"#9b9b9b"
                    ),
                    "keyword5"=>array(
                        "value"=>$order_shouli['kd_order'],
                        "color"=>"#9b9b9b"
                    ),  
                );
        $dd = array();
        $dd['touser']=$order_shouli['openid'];//接收人openid
        $dd['template_id']=$template_id;
        $dd['page']="choujiang_page/lws_win/lws_win?ordersn=".$orderid;  //点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,该字段不填则模板无跳转。
        $dd['form_id']=$order_shouli['fh_formid'];
        $dd['data']=$value;                        //模板内容，不填则下发空模板
        $dd['color']='';                        //模板内容字体的颜色，不填默认黑色
        $dd['emphasis_keyword']='';    //模板需要放大的关键词，不填则默认无放大
        $result = $this->https_curl_json($url,$dd,'json');
       
        return $result;
    }



























}