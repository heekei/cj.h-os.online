<?php
/**
 * 旅游小程序接口定义
 *
 * @author 开吧源码社区
 * @url http://www.kai8.top/
 */
defined('IN_IA') or exit('Access Denied');

class Choujiang_pageModuleWxapp extends WeModuleWxapp {
        
        //获取用户信息

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
    public function doPageGetUid() {
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $result = pdo_fetch('SELECT * FROM ' . tablename('choujiang_base') . " where `uniacid`='{$uniacid}'");
        $APPID = trim($result['appid']);
        $SECRET = trim($result['appsecret']);
        $code = trim($_GPC['code']);
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$APPID}&secret={$SECRET}&js_code={$code}&grant_type=authorization_code";
        $data['userinfo'] = json_decode($this->httpGet($url));
        $openid = $data['userinfo']->openid;
        $item['openid'] = $openid;
        $item['uniacid'] = $uniacid;
        if ($openid) {
                $res = pdo_fetch('SELECT `id` FROM ' . tablename('choujiang_user') . " where `openid`='{$openid}' and `uniacid`='{$uniacid}'");
            if (!$res['id']) {
                $base = pdo_fetch('SELECT * FROM ' . tablename('choujiang_base') . " where `uniacid`='{$uniacid}'");
                $item['mf_num'] = $base['join_num'];
                $item['smoke_num'] = $base['smoke_num'];
                $item['winning_num'] = $base['winning_num'];
                $res = pdo_insert('choujiang_user', $item);
            }
        }
        $data['openid'] = $openid;
        $message = 'success';
        $errno = 0;
        return $this->result($errno, $message, $openid);
    }

    public function doPageMember() {
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $openid = $_REQUEST['openid'];
        $item['nickname'] = $_GPC['nickName'];
        $item['avatar'] = $_GPC['avatarUrl'];
        $item['uniacid'] = $uniacid;
        $item['create_time'] = time();
        $item['send_time'] = time();
        $member = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where `uniacid`='{$uniacid}' and `openid`='{$openid}'");
        if (!empty($member)) {
            $res = pdo_update('choujiang_user', $item, array('id' => $member['id']));
        }
        $message = 'success';
        $errno = 0;
        return $this->result($errno, $message, $item);
    }

    public function doPageMemberInfo(){
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $openid = $_REQUEST['openid'];
        $member = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where `uniacid`='{$uniacid}' and `openid`='{$openid}'");
        $base = pdo_fetch('SELECT * FROM ' . tablename('choujiang_base') . " where uniacid=:uniacid", array(":uniacid" => $_W['uniacid']));
        if($base['share_num'] == 0){
            $member['share_num_status'] = 2;
        }else{
            $member['share_num_status'] = 1;
        }
        if($base['join_num'] == 0){
            $member['join_num_status'] = 2;
        }else{
            $member['join_num_status'] = 1;
        }
        $member['num'] = $member['yu_num'] + $member['mf_num'];
        return $this->result($errno, $message, $member);

    }

    //查询用户是否完善资料
    public function doPageMemberziliao(){
        global $_GPC,$_W;
        $uniacid = $_W['uniacid'];
        $openid = $_GPC['openid'];
        $member = pdo_fetch("SELECT * FROM".tablename('choujiang_user'). " where uniacid = '{$uniacid}' and openid = '{$openid}' and name != '' and tel != '' and shengri != '' ");
        if(empty($member)){
            $type = 1;//未完善
        }else{
            $type = 2;//已完善
        }
        return $this->result(0,'success',$type);
    }

    public function doPageBase(){
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $base = pdo_fetch('SELECT * FROM ' . tablename('choujiang_base') . " where uniacid=:uniacid", array(":uniacid" => $_W['uniacid']));
        return $this->result(0,'success',$base);

    }


    // 抽奖列表
    public function doPageIndexList(){
        global $_GPC,$_W;
        $uniacid = $_W['uniacid'];
        $openid = $_REQUEST['openid'];
        $pindex =max(1, intval($_REQUEST['page']));
        $psize =5;//每页显示个数
        $condition = $_REQUEST['condition'];
        if($condition == 1){  //手动开奖
            $conditions = ' and smoke_set = 2';
        }else if($condition == 2){ //现场开奖
            $conditions = ' and smoke_set = 3';
        }else{
            $conditions = ' and (smoke_set = 0 or smoke_set = 1)';
        }
        $ret = pdo_fetchall("SELECT * from".tablename('choujiang_goods')."WHERE uniacid = :uniacid and status = 0 and is_del != -1 and audit_status = 1 and to_index = 1 ".$conditions." ORDER BY id desc LIMIT " . ($pindex - 1)* $psize . ',' . $psize,array(':uniacid'=>$uniacid));
        
        foreach ($ret as $key=>$value) {
            // $ret[$key]['goods_icon'] = $_W['attachurl'].$value['goods_icon'];
            $ret[$key]['goods_icon'] = $this->HomeImageSb($value['goods_icon'],$value['form_address']);
            
            $join_num = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_record').' where uniacid=:uniacid and goods_id = :goods_id',array(':uniacid'=>$uniacid,':goods_id'=>$value['id']));
            $join = pdo_fetch("SELECT * from".tablename('choujiang_record')."WHERE uniacid = :uniacid and goods_id = :id and openid = :openid",array(':uniacid'=>$uniacid,':id'=>$value['id'],':openid' => $openid));
            if(empty($join)){
                $str = -1;
            }else{
                $str = 1;
            }
            if($value['goods_status'] == 2){
                $ret[$key]['goods_name'] = '红包'.$value['red_envelope'].'元/人';
            }
            $ret[$key]['join'] = $str;
            $ret[$key]['join_num'] = $join_num;
        }
        $total =pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_goods').' where uniacid=:uniacid  and is_del != -1 '.$conditions,array(':uniacid'=>$uniacid));
        $pager =pagination($total, $pindex, $psize);
        return $this->result(0,'success',$ret);
    }

    // 列表详情页
    public function doPageGoodsXq(){
        global $_GPC,$_W;
        $uniacid = $_W['uniacid'];
        $id = $_REQUEST['id'];
        $openid = $_REQUEST['openid'];
        // $openid = 'oQQf_0KyaKENcRwM1kgeF6W4hH_Y';
        $join = pdo_fetch("SELECT * from".tablename('choujiang_record')."WHERE uniacid = :uniacid and goods_id = :id and openid = :openid",array(':uniacid'=>$uniacid,':id'=>$id,':openid' => $openid));
        $ret = pdo_fetch("SELECT * from".tablename('choujiang_goods')."WHERE uniacid = :uniacid and id = :id and is_del != -1",array(':uniacid'=>$uniacid,':id'=>$id));
        // $ret['goods_icon'] = $_W['attachurl'].$ret['goods_icon'];
        if($ret['smoke_set'] == 0){
            $ret['open_time'] = strtotime($ret['smoke_time']);
            $time = $ret['smoke_time'];
            $year = substr($time,0,4);
            $month = substr($time,5,2);
            $day= substr($time,8,2);
            $hour=substr($time,11,2);
            $min =substr($time,14,2);
            if(substr($month, 0, 1 ) == 0){
                $month = substr($month, 1, 1 );
            }
            if(substr($day, 0, 1 ) == 0){
                $day = substr($day, 1, 1 );
            }
            if(substr($hour, 0, 1 ) == 0){
                $hour = substr($hour, 1, 1 );
            }
            $ret['The_time']['year'] = $year;
            $ret['The_time']['month'] = $month;
            $ret['The_time']['day'] = $day;
            $ret['The_time']['hour'] = $hour;
            $ret['The_time']['min'] = $min;
        }
        $user = pdo_fetch("SELECT * from".tablename('choujiang_user')."WHERE uniacid = :uniacid and openid = :openid",array(':uniacid'=>$uniacid,':openid'=>$ret['goods_openid']));
        $ret['avatar'] = $user['avatar'];
        $ret['nickname'] = $user['nickname'];
        if(!empty($join)){
            $ret['join_status'] = 1;
        }else{
            $ret['join_status'] = 0;
        }
        $images = unserialize($ret['goods_images']);
                
        foreach($images as $key => $value){
            if($value == ''){
                unset($images[$key]);
            }
        }
              
        foreach($images as $key => $value){
            if (strstr($value, 'http')) {
                $images[$key] = $value;
            }else{
                $images[$key] = $this->HomeImageSb($value,$ret['form_address']);
            }
        }
        $ret['goods_icon'] = $this->HomeImageSb($ret['goods_icon'],$ret['form_address']);

        $ret['goods_images'] = $images;
        if($ret['goods_status'] == 2){
            $ret['goods_name'] = '红包 '.$ret['red_envelope'].'元/人';
        }else if($ret['goods_status'] == 1){
            $ret['card_info'] = unserialize($ret['card_info']);
        }
        return $this->result(0,'success',$ret);
    }
// 手续费
    public function doPagePoundage(){
        global $_GPC,$_W;
        $uniacid = $_W['uniacid'];
        $base = pdo_fetch('SELECT * FROM ' . tablename('choujiang_base') . " where uniacid=:uniacid", array(":uniacid" => $_W['uniacid']));
        $ret['poundage'] = $base['poundage'];
        $ret['xcx_price'] = $base['xcx_price'];
        return $this->result(0,'success',$ret);
    }

    //完善用户资料
    public function doPageMemberziliaoadd(){
        global $_GPC,$_W;
        $uniacid = $_W['uniacid'];
        $data = array('name'=>$_GPC['name'],'tel'=>$_GPC['tel'],'shengri'=>$_GPC['shengri']);
        pdo_update("choujiang_user",$data,array('openid'=>$_GPC['openid']));
    }


// 参与抽奖的用户列表
    public function doPageGoodsRecord(){
        global $_GPC,$_W;
        $uniacid = $_W['uniacid'];
        $id = $_REQUEST['id'];
        $ret = pdo_fetchall("SELECT avatar from".tablename('choujiang_record')."WHERE uniacid = :uniacid and goods_id = :id ORDER BY id DESC",array(':uniacid'=>$uniacid,':id'=>$id));
        return $this->result(0,'success',$ret);
    }

    // 中奖者用户地址个数
    public function doPageObtainRecordAddress(){
        global $_GPC,$_W;
        $id = $_REQUEST['id'];
        $uniacid = $_W['uniacid'];
        $ret = count(pdo_fetchall("SELECT * from".tablename('choujiang_record')."WHERE uniacid = :uniacid and goods_id = :id and status = 1 and get_status != 0",array(':uniacid'=>$uniacid,':id'=>$id)));

        return $this->result(0,'success',$ret);

    }
    // 中奖者用户地址信息
    public function doPageObtainRecordAddressIn(){
        global $_GPC,$_W;
        $id = $_REQUEST['id'];
        $uniacid = $_W['uniacid'];
        $ret = pdo_fetchall("SELECT * from".tablename('choujiang_record')."WHERE uniacid = :uniacid and goods_id = :id and status = 1",array(':uniacid'=>$uniacid,':id'=>$id));
        return $this->result(0,'success',$ret);

    }
    // 地址最迟填写时间
    public function doPageAddress_out_time(){
        global $_GPC,$_W;
        $record_id = $_REQUEST['id'];
        $uniacid = $_W['uniacid'];
        $base = pdo_fetch("SELECT * from".tablename('choujiang_base')."WHERE uniacid = :uniacid",array(':uniacid'=>$uniacid));
        $record = pdo_fetch("SELECT * from".tablename('choujiang_record')."WHERE uniacid = :uniacid and id = :id",array(':uniacid'=>$uniacid,':id'=>$record_id));

        $sql = pdo_fetch("SELECT * from".tablename('choujiang_goods')."WHERE uniacid = :uniacid and id = :id",array(':uniacid'=>$uniacid,':id'=>$record['goods_id']));
        $latest_time = $base['latest_time'];
        if($latest_time == 0){
            $ret = 0;
        }else{
            $time = $sql['send_time'];
            $out_time = strtotime("+".$latest_time."hours",$time);
            $ret = date('Y-m-d H:i',$out_time);
        }
        
        return $this->result(0,'success',$ret);
    }
    // 前台添加图片到服务器
    public function doPageImgUrl(){
        global $_W;
        $str['uniacid'] = $_W['uniacid'];
        $str['url'] = $_W['siteroot'];
        return $this->result(0,'success',$str);
    }
    public function doPageUpload()
    {
        global $_W, $_GPC;

        if($_W['setting']['remote']['type']==3)  //七牛云开启
        {
            $qiniu = $_W['setting']['remote']['qiniu'];
             require_once(IA_ROOT . '/framework/library/qiniu/autoload.php');
             $accessKey=$qiniu['accesskey'];
             $secretKey=$qiniu['secretkey'];
             $bucket=$qiniu['bucket'];
             //转码时使用的队列名称
             //$pipeline = $qiniu['qn_queuename'];
             //要进行转码的转码操作
             $fops = "avthumb/mp4/ab/64k/ar/44100/acodec/libfaac";
             $auth = new Qiniu\Auth($accessKey, $secretKey); 

            $filekey=$_FILES['upfile)']['name'];         //上传文件名
            $filePath=$_FILES['upfile']['tmp_name'];    //上传文件的路径

             //可以对转码后的文件进行使用saveas参数自定义命名，当然也可以不指定文件会默认命名并保存在当间
             $savekey =  Qiniu\base64_urlSafeEncode($bucket.':'.$filekey.'_1');
             $fops = $fops.'|saveas/'.$savekey;
             $policy = array(
                     'persistentOps' => $fops,
                    // 'persistentPipeline' => $pipeline
             );
             $uptoken = $auth->uploadToken($bucket, null, 3600, $policy);    //上传凭证
             //上传文件的本地路径
             $uploadMgr = new Qiniu\Storage\UploadManager();
             $ss = $uploadMgr->putFile($uptoken, $filekey, $filePath);
             load()->func("logging");
             $error=logging_run("qiniu:error".$err."成个");
             if ($err !== null) {
                 load()->func("logging");
                 logging_run("qiniu:error");
                 return false;
             }
             //$ffff 为七牛云路径
            $fname=$qiniu['url'].'/'.$ss[0]['key'];
            echo $fname;
        }
        else if($_W['setting']['remote']['type']==2 )   //阿里云oss 开启
        {       
            //将本地图片先上传到服务器
            load()->func('file');
            $file = $_FILES['upfile'];
            $filename = $file['tmp_name'];
            $destination_folder = '../attachment/images/'.$_W['uniacid'].'/'.date('Y/m/').'/';  //图片文件夹路径
            //创建存放图片的文件夹
            if (!is_dir($destination_folder)) {
               $res = mkdir($destination_folder, 0777, true);
            }
            if (!is_uploaded_file($_FILES['upfile']['tmp_name'])) {
                echo '图片不存在!';
                die;
            }
           
            $pinfo = pathinfo($file['name']);
            $ftype = $pinfo['extension'];
            $destination = $destination_folder . str_shuffle(time() . rand(111111, 999999)) . '.' . $ftype;
            if (file_exists($destination) && $overwrite != true) {
                echo '同名文件已经存在了';
                die;
            }
            if (!move_uploaded_file($filename, $destination)) {
                echo '移动文件出错';
                die;
            }
            $pinfo = pathinfo($destination);
            $filename = 'images/'.$_W['uniacid'].'/'.date('Y/m/').$pinfo['basename'];

            //将服务器上的图片转移到阿里云oss
           
            $remote = $_W['setting']['remote'];
            require_once(IA_ROOT . '/framework/library/alioss/autoload.php');
            load()->model('attachment');
            $endpoint = 'http://'.$remote['alioss']['ossurl'];
            try {
                $ossClient = new \OSS\OssClient($remote['alioss']['key'], $remote['alioss']['secret'], $endpoint);              
                $ossClient->uploadFile($remote['alioss']['bucket'],$filename, ATTACHMENT_ROOT.$filename);
            } catch (\OSS\Core\OssException $e) {
              //echo  'error--->'.$e->getMessage();
                return error(1, $e->getMessage());
              
            }
            if ($auto_delete_local) {
                unlink($filename);
            }
            //删除服务器上的上传文件
            unlink(ATTACHMENT_ROOT.$filename);
            $fname = $remote['alioss']['url'].'/'.$filename;
           echo $fname;
            
        }
        else if ($_W['setting']['remote']['type']==4) {  //腾讯云
            $cosurl = $_W['setting']['remote']['cos']['url'];
            $uptypes = array('image/jpg', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/gif', 'image/bmp', 'image/x-png');
            $max_file_size=2000000;     //上传文件大小限制, 单位BYTE  
            $destination_folder = '../attachment/images/'.$_W['uniacid'].'/'.date('Y/m/').'/';  //图片文件夹路径
            if (!is_uploaded_file($_FILES["upfile"]['tmp_name'])) //是否存在文件
            {
                echo "图片不存在!";  
                exit;  
            }  
            $file = $_FILES["upfile"];
            if($max_file_size < $file["size"])
            {  
                echo "文件太大!"; 
                exit;
            }  
            if(!in_array($file["type"], $uptypes))   //检查文件类型  
            {
                echo "文件类型不符!".$file["type"];
                exit;
            }
            if(!file_exists($destination_folder))
            {
                mkdir($destination_folder);
            }  

            $filename=$file["tmp_name"];  
            $image_size = getimagesize($filename);  
            $pinfo=pathinfo($file["name"]);  
            $ftype=$pinfo['extension'];  
            $destination = $destination_folder.time().".".$ftype;  
            if (file_exists($destination) && $overwrite != true)  
            {  
                echo "同名文件已经存在了";  
                exit;  
            }  
            if(!move_uploaded_file ($filename, $destination))  
            {  
                echo "移动文件出错";  
                exit;
            }
            $pinfo=pathinfo($destination);  
            $fname=$pinfo['basename'];  
            
            @require_once (IA_ROOT . '/framework/function/file.func.php');
            @$filename='images/'.$_W['uniacid'].'/'.date('Y/m/').'/'.$fname;
            @file_remote_upload($filename);
            echo $cosurl.'/images/'.$_W['uniacid'].'/'.date('Y/m/').$fname;
        }
        else if($_W['setting']['remote']['type']==0)    //远程存储关闭
        {
            $uptypes = array('image/jpg', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/gif', 'image/bmp', 'image/x-png');
            $max_file_size = 2000000;
            $destination_folder = '../attachment/choujiang_page/';  //图片文件夹路径
            //创建存放图片的文件夹
            if (!is_dir($destination_folder)) {
               $res = mkdir($destination_folder, 0777, true);
            }
            if (!is_uploaded_file($_FILES['upfile']['tmp_name'])) {
                echo '图片不存在!';
                die;
            }
            $file = $_FILES['upfile'];
            if ($max_file_size < $file['size']) {
                echo '文件太大!';
                die;
            }
            if (!in_array($file['type'], $uptypes)) {
                echo '文件类型不符!' . $file['type'];
                die;
            }
            $filename = $file['tmp_name'];
            $pinfo = pathinfo($file['name']);
            $ftype = $pinfo['extension'];
            $destination = $destination_folder . str_shuffle(time() . rand(111111, 999999)) . '.' . $ftype;
            if (file_exists($destination) && $overwrite != true) {
                echo '同名文件已经存在了';
                die;
            }
            if (!move_uploaded_file($filename, $destination)) {
                echo '移动文件出错';
                die;
            }
            $pinfo = pathinfo($destination);
            $fname = $_W['attachurl'].'choujiang_page/'.$pinfo['basename'];
            echo $fname;
        } 
    }
    public function doPageMemberCiShu(){
        global $_W, $_GPC;
        $base = pdo_fetch("SELECT * FROM " . tablename('choujiang_base') . " WHERE uniacid = :uniacid" , array(':uniacid' => $_W['uniacid']));
        if($base['join_num'] == 0){
            $str = 1;
        }else if($base['join_num'] > 0){
            $sql = pdo_fetch("SELECT * FROM " . tablename('choujiang_user') . " WHERE openid = :openid and uniacid = :uniacid" , array(':uniacid' => $_W['uniacid'],':openid' => $_REQUEST['openid']));
            if($sql['mf_num']> 0 || $sql['yu_num'] > 0){
                $str = 1;
            }else{
                $str = -1;
            }
        }else{
            $str = -1;
        }
       
        return $this->result(0,'success',$str);
    }

    // 前台添加奖品
    public function doPageGoodsInto(){
        global $_W, $_GPC;
        $current = $_REQUEST['current'];
        $pay_id = $_REQUEST['pay_id'];
        $sh_status = $_REQUEST['sh_status'];
        $audit_set = pdo_fetch("SELECT * FROM " . tablename('choujiang_base') . " WHERE uniacid = :uniacid" , array(':uniacid' => $_W['uniacid']));
        $audit_status = $audit_set['audit_set'];
        if($current == 0){  //实物
            $data['goods_status'] = 0;
        }else if($current == 2){  //红包
            $data['red_envelope'] = $_REQUEST['hbname'];  //红包金额
            $data['goods_status'] = 2;
        }else if($current == 1){    //电子卡
            $dianzika = $_REQUEST['dainzika'];
            $picPath = str_replace('"',"",str_replace("}]","",str_replace("[{","", $dianzika)));
            $newarr = array();
            $newarrs = array();
            $arr = explode("},{",$picPath);
            foreach($arr as $key => $value){
                $arr1 = explode(",",$value);
                foreach($arr1 as $k => $v){
                    $arr2 = explode(":",$v);
                    $ke = $arr2[0];
                    $va = $arr2[1];
                    $newarr1[$ke] = $va;
                }
                array_push($newarr,$newarr1);
            }
            foreach($newarr as $key => $value){
                $k = $value['keys'];
                $v = $value['vals'];
                $newarrs[$k] = $v;       
            }
            $data['card_info'] =serialize($newarrs);
            $_REQUEST['jpnum1'] = count($newarrs);
            $data['goods_status'] = 1;
        }
        $id = $_REQUEST['id'];
        $uniacid = $_W['uniacid'];
        $status = $_REQUEST['index'];
        $data['mouth_command'] = $_REQUEST['jpkouling'];   //口令
        
        $price = $_REQUEST['fufeije']; //付费参与金额
        if($price > 0){
            $data['price'] = $_REQUEST['fufeije']; //付费参与金额
        }else{
            $data['price'] = 0; //付费参与金额
        }
        $YouSong = $_REQUEST['YouSong'];  //包邮
        if($YouSong == true){
            $YouSong = 1;
        }else{
            $YouSong = 0;
        }
        $data['YouSong'] = $YouSong;  //包邮
        $data['join_conditions'] = $_REQUEST['join_conditions'];
        $data['content'] = $_REQUEST['jpjjval'];  //奖品简介
        $data['goods_sponsorship'] = $_REQUEST['zanzhusval'];  //赞助商
        $data['sponsorship_text'] = $_REQUEST['zanzhusjjval']; //赞助商标题
        $data['sponsorship_appid'] = $_REQUEST['tiaozhuan'];   //小程序跳转input
        $data['sponsorship_content'] = $_REQUEST['zanzhusjsval'];  //赞助商介绍
        $data['sponsorship_url'] = $_REQUEST['sponsorship_url'];  //赞助商小程序跳转链接
        $data['business_address'] = $_REQUEST['jpdizhi'];
        $data['freight'] = $_REQUEST['jpyunfei'];
        $data['mail_status'] = $_REQUEST['youji_chk']; //支持邮寄
        $data['ziqu_status'] = $_REQUEST['ziqu_chk'];  //支持自取
        $data['team_status'] = $_REQUEST['zudui_status'];  //是否支持组队
        $data['team_num'] = $_REQUEST['team_num'];  //组队人数
        
        $data['team_num'] = $_REQUEST['fur_id'];  //皮一下标题
        $data['fur_status'] = $_REQUEST['fur_status'];  //皮一下状态
        $picPath = $_REQUEST['picPath'];
        if($picPath != ''){
            $picPath = str_replace('"',"",str_replace("]","",str_replace("[","", $picPath)));
            $arr = explode(",",$picPath);
            $data['goods_images'] = serialize($arr);
        }else{
            $data['goods_images'] = '';
        }
        
        $data['goods_icon']=str_replace($_W['attachurl'],'',$_REQUEST['icon']); 
        $data['goods_name'] = $_REQUEST['jpname1'];
        $data['goods_num'] = $_REQUEST['jpnum1'];
        if($status == 0){
            $year = $_REQUEST['year'];
            $month = $_REQUEST['month'];
            $day = $_REQUEST['day'];
            $hour = $_REQUEST['hour'];
            $min = $_REQUEST['min'];
            if(strlen($month) == 1){
                $month = '0'.$month;
            }
            if(strlen($day) == 1){
                $day = '0'.$day;
            }
            if(strlen($hour) == 1){
                $hour = '0'.$hour;
            }
            if(strlen($min) == 1){
                $min = '0'.$min;
            }
            $data['smoke_time'] = $year.'-'.$month.'-'.$day.' '.$hour.':'.$min;
        }else if($status == 1){
            $data['smoke_num'] = $_REQUEST['kjPeonum'];
        }
        $data['goods_openid'] = $_REQUEST['openid'];
        $data['smoke_set'] = $status;
        foreach($data as $key => $value){
            if($value == undefined){
                $data[$key] = '';
            }
        }
        if($_REQUEST['fur_status'] == 1){
            $data['goods_name'] = $_REQUEST['fur_title'];  //组队人数
        }
        if (empty($id)) {
            $data['uniacid'] = $uniacid;
            $base = pdo_fetch("SELECT * FROM " . tablename('choujiang_base') . " WHERE uniacid = :uniacid" , array(':uniacid' => $_W['uniacid']));
            if($base['join_num'] == 0){
                $strs = 1;
            }else{
                $sql = pdo_fetch("SELECT * FROM " . tablename('choujiang_user') . " WHERE openid = :openid and uniacid = :uniacid" , array(':uniacid' => $uniacid,':openid' => $_REQUEST['openid']));
                if($sql['mf_num']<= 0 && $sql['yu_num'] <= 0){
                    $strs = -1;

                }else if($sql['mf_num'] >0){

                    $pata['mf_num'] = $sql['mf_num'] -1;
                    $strs = pdo_update('choujiang_user', $pata, array('id' => $sql['id'] , 'uniacid' => $uniacid));

                }else if($sql['yu_num'] >0){
                    $pata['yu_num'] = $sql['yu_num'] -1;
                    $strs = pdo_update('choujiang_user', $pata, array('id' => $sql['id'] , 'uniacid' => $uniacid));

                }
            }
            
            if($strs != -1){
                if($audit_status == 1){
                    $data['audit_status'] = 0;
                }else{
                    $data['audit_status'] = 1;
                }
                $str = pdo_insert('choujiang_goods', $data);
            }
            if (!empty($str)) {
                $uid = pdo_insertid();
                $status = 1;
                $this->doWebInvitation($uid,$status);
                // if($data['audit_status'] == 1){
                //     $this->doPageGoodsInform($uid);
                // }
            }else{
                $status = -1;
            }
        } else {
            if($audit_status == 1){
                $data['audit_status'] = 0;
                $data['to_index'] = 0;
            }else{
                $data['audit_status'] = 1;
            }
            $str = pdo_update('choujiang_goods', $data, array('id' => $id , 'uniacid' => $uniacid));
            $uid = $id;
            $status = 2;
            // if($str && $data['audit_status'] == 1){
            //     $this->doPageGoodsInform($uid);
            // }
            
        } 
        if($uid){
            $poster = $this->GeneratePoster($uid);
            pdo_update('choujiang_verification',array('new_poster'=>$poster),array('goods_id'=>$uid));
        }
        $ret['status'] = $status;
        $ret['uid'] = $uid; 
        $ret['data'] = $data; 
        if(!empty($pay_id)){
            pdo_update('choujiang_pay_record', array('goods_id' => $uid), array('id' => $pay_id, 'uniacid' => $uniacid));
        }
        return $this->result(0,'success',$ret);
    }


    // 个人中心
    // 待开奖
    public function doPageGoodsStart(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $openid = $_REQUEST['openid'];
        $status = $_REQUEST['status'];
        //     $openid = 'oQQf_0KyaKENcRwM1kgeF6W4hH_Y';
        // $status = 2;
        if($status == 1){
            $ret = pdo_fetchall("SELECT * from".tablename('choujiang_goods')."WHERE uniacid = :uniacid and goods_openid = :openid and status = 0  and is_del = 1 ORDER BY id desc ",array(':uniacid'=>$uniacid,':openid'=>$openid)); 
            foreach ($ret as $key=>$value) {
                // $ret[$key]['goods_icons'] = $_W['attachurl'].$value['goods_icon'];
                $ret[$key]['goods_icons'] = $this->HomeImageSb($value['goods_icon'],$value['form_address']);

                $ret[$key]['goods_id'] = $value['id'];
                if($value['goods_status'] == 2){
                    $ret[$key]['goods_name'] = $value['red_envelope'].'元/人';
                }
            }
        }else if($status == 2){
            $ret = pdo_fetchall("SELECT * from".tablename('choujiang_record')."WHERE uniacid = :uniacid and openid = :openid ORDER BY id desc ",array(':uniacid'=>$uniacid,':openid'=>$openid)); 
            foreach($ret as $key => $value){
                $goods_id = $value['goods_id'];
                $good = pdo_fetch("SELECT * from".tablename('choujiang_goods')."WHERE uniacid = :uniacid and id = :id and status = 0",array(':uniacid'=>$uniacid,':id'=>$goods_id)); 
                // $ret[$key]['goods_icons'] = $_W['attachurl'].$good['goods_icon'];
                $ret[$key]['goods_num'] = $good['goods_num'];
                $ret[$key]['smoke_set'] = $good['smoke_set'];
                $ret[$key]['smoke_time'] = $good['smoke_time'];
                $ret[$key]['smoke_num'] = $good['smoke_num'];
                if($good['goods_status'] == 2){
                    $ret[$key]['goods_name'] = $good['red_envelope'].'元/人';
                }
                if(empty($good)){
                  $ret[$key]['goods_id'] = 0;
                }else{
                    $ret[$key]['goods_id'] = $good['id'];
                }
                $ret[$key]['create_time'] = date('Y-m-d H:i',$value['create_time']);
                // $ret[$key]['goods_icon'] = $_W['attachurl'].$value['goods_icon'];
                $ret[$key]['goods_icons'] = $this->HomeImageSb($good['goods_icon'],$good['form_address']);

            }
        }else{
             $ret = pdo_fetchall("SELECT * from".tablename('choujiang_record')."WHERE uniacid = :uniacid and openid = :openid and status != 0 ORDER BY id desc ",array(':uniacid'=>$uniacid,':openid'=>$openid)); 
            foreach($ret as $key => $value){
                $goods_id = $value['goods_id'];
                $good = pdo_fetch("SELECT * from".tablename('choujiang_goods')."WHERE uniacid = :uniacid and id = :id and status = 1",array(':uniacid'=>$uniacid,':id'=>$goods_id)); 
                if(empty($good)){
                  $ret[$key]['goods_id'] = 0;
                }else{
                    $ret[$key]['goods_id'] = $good['id'];
                }
                if($good['goods_status'] == 2){
                    $ret[$key]['goods_name'] = $good['red_envelope'].'元/人';
                }
                $ret[$key]['goods_num'] = $good['goods_num'];
                if($time>=$value['finish_time'] && $value['get_status'] == 0 && $value['status'] == 1){
                    pdo_update('choujiang_record', array('status'=>-1),array('id'=>$value['id']));
                }
                
                $ret[$key]['time'] = date('m-d H:i',$value['finish_time']);
            }
        }
        foreach ($ret as $k => $v){
            $goods_id = $v['goods_id'];
            $join_num = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_record').' where uniacid=:uniacid and goods_id = :goods_id',array(':uniacid'=>$uniacid,':goods_id'=>$goods_id));
            $ret[$k]['join_num'] = $join_num;
            if($goods_id == 0){
                unset($ret[$k]);
            }else{
                $good_del = pdo_fetch("SELECT * from".tablename('choujiang_goods')."WHERE uniacid = :uniacid and id = :id",array(':uniacid'=>$uniacid,':id'=>$goods_id));

                if($good_del['is_del'] == -1){
                    $ret[$k]['del'] = 1;
                }
            }
        } 
        return $this->result(0,'success',$ret);
    }
    // 已开奖
    public function doPageGoodsStart1(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $status = $_REQUEST['status'];
        $openid = $_REQUEST['openid'];
        $base = pdo_fetch("SELECT * from".tablename('choujiang_base')."WHERE uniacid = :uniacid",array(':uniacid'=>$uniacid)); 
        $latest_time = $base['latest_time'];
        if($latest_time != 0){
            $time = strtotime("-".$latest_time." hours");
        }
        if($status == 1){
            $ret = pdo_fetchall("SELECT * from".tablename('choujiang_goods')."WHERE uniacid = :uniacid and goods_openid = :openid and status = 1 and is_del = 1 ORDER BY id desc ",array(':uniacid'=>$uniacid,':openid'=>$openid)); 
            foreach ($ret as $key=>$value) {
                $ret[$key]['goods_id'] = $value['id'];
                $ret[$key]['time'] = date('m-d H:i',$value['send_time']);
                $ret[$key]['smoke_set'] = $good['smoke_set'];
            }
            if($value['goods_status'] == 2){
                $ret[$key]['goods_name'] = $value['red_envelope'].'元/人';
            }
        }else if($status == 2){
            $ret = pdo_fetchall("SELECT * from".tablename('choujiang_record')."WHERE uniacid = :uniacid and openid = :openid ORDER BY id desc ",array(':uniacid'=>$uniacid,':openid'=>$openid)); 
            foreach($ret as $key => $value){
                $goods_id = $value['goods_id'];
                $good = pdo_fetch("SELECT * from".tablename('choujiang_goods')."WHERE uniacid = :uniacid and id = :id and status = 1",array(':uniacid'=>$uniacid,':id'=>$goods_id)); 
                $ret[$key]['goods_num'] = $good['goods_num'];
                if(empty($good)){
                    $ret[$key]['goods_id'] = 0;
                }else{
                    $ret[$key]['goods_id'] = $good['id'];
                }
                if($time>=$value['finish_time'] && $value['get_status'] == 0 && $value['status'] == 1 && $latest_time != 0){
                    pdo_update('choujiang_record', array('status'=>-1),array('id'=>$value['id']));
                }
                if($good['goods_status'] == 2){
                    $ret[$key]['goods_name'] = $good['red_envelope'].'元/人';
                }
                $ret[$key]['time'] = date('m-d H:i',$value['finish_time']);
                $ret[$key]['smoke_set'] = $good['smoke_set'];
                if($good['smoke_set'] == 3){
                    $list = pdo_fetch('SELECT * FROM ' . tablename('choujiang_exchange') . " where uniacid=:uniacid and goods_id = :goods_id and openid = :openid", array(":goods_id"=>$goods_id,":uniacid" => $uniacid,":openid" => $openid));
                    if($list['status'] == 1){
                        $ret[$key]['hex_status'] = -1;
                    }else{
                        $ret[$key]['hex_status'] = 1;
                    }
                }

            }
        }else{
            $rets = pdo_fetchall("SELECT * from".tablename('choujiang_record')."WHERE uniacid = :uniacid and openid = :openid and status != 0 ORDER BY id desc ",array(':uniacid'=>$uniacid,':openid'=>$openid)); 
            foreach($rets as $key => $value){
                $base = pdo_fetch("SELECT * from".tablename('choujiang_base')."WHERE uniacid = :uniacid",array(':uniacid'=>$uniacid));
                if($base['takes_time'] > 0){
                    if($value['get_create_time'] != ''){
                        $out_time = strtotime("+".$base['takes_time']."hours",$value['get_create_time']);
                        if(time()>=$out_time && $value['get_status'] == 2 && $value['status'] == 1){
                            pdo_update('choujiang_record', array('status'=>-1),array('id'=>$value['id']));
                        }
                    }
                    
                }else{
                    break;
                }
            }
            $ret = array();
            $pt_record = pdo_fetchall("SELECT * from".tablename('choujiang_record')."WHERE uniacid = :uniacid and openid = :openid and status != 0 ORDER BY id desc ",array(':uniacid'=>$uniacid,':openid'=>$openid)); 
            $scene = pdo_fetchall("SELECT * from".tablename('choujiang_scene')."WHERE uniacid = :uniacid and openid = :openid and status != 0 ORDER BY id desc ",array(':uniacid'=>$uniacid,':openid'=>$openid));  //现场开奖
            foreach($pt_record as $key => $val){
                array_push($ret,$val);
            }
            foreach($scene as $key => $val){
                $good = pdo_fetch("SELECT * from".tablename('choujiang_goods')."WHERE uniacid = :uniacid and id = :id and status = 1",array(':uniacid'=>$uniacid,':id'=>$val['goods_id'])); 
                $scene[$key]['goods_name'] = $good['goods_name'];
                array_push($ret,$scene[$key]);
            }
            $ress = array();
            foreach($ret as $v){  
                  $ress[] = $v['finish_time'];  
            } 
            array_multisort($ress, SORT_DESC, $ret);
            foreach($ret as $key => $value){
                $goods_id = $value['goods_id'];
                $good = pdo_fetch("SELECT * from".tablename('choujiang_goods')."WHERE uniacid = :uniacid and id = :id and status = 1",array(':uniacid'=>$uniacid,':id'=>$goods_id)); 
                if(empty($good)){
                  $ret[$key]['goods_id'] = 0;
                }else{
                    $ret[$key]['goods_id'] = $good['id'];
                }
                if($good['goods_status'] == 2){
                    $ret[$key]['goods_name'] = $good['red_envelope'].'元/人';
                }
                $ret[$key]['goods_num'] = $good['goods_num'];
                if($time>=$value['finish_time'] && $value['get_status'] == 0 && $value['status'] == 1 && $latest_time != 0){
                    pdo_update('choujiang_record', array('status'=>-1),array('id'=>$value['id']));
                }
                $ret[$key]['time'] = date('m-d H:i',$value['finish_time']);
                $ret[$key]['smoke_set'] = $good['smoke_set'];
                if($good['smoke_set'] == 3){
                    $list = pdo_fetch('SELECT * FROM ' . tablename('choujiang_exchange') . " where uniacid=:uniacid and goods_id = :goods_id and openid = :openid", array(":goods_id"=>$goods_id,":uniacid" => $uniacid,":openid" => $openid));
                    if($list['status'] == 1){
                        $ret[$key]['hex_status'] = -1;
                    }else{
                        $ret[$key]['hex_status'] = 1;
                    }
                }
                if($good['goods_status'] == 0 && $value['get_status'] == 2){
                    $list = pdo_fetch('SELECT * FROM ' . tablename('choujiang_exchange') . " where uniacid=:uniacid and goods_id = :goods_id and openid = :openid", array(":goods_id"=>$goods_id,":uniacid" => $uniacid,":openid" => $openid));
                    if($list['status'] == 1){
                        $ret[$key]['hex_status'] = -1;
                    }else{
                        $ret[$key]['hex_status'] = 1;
                    }
                }
            }
        }
        foreach ($ret as $k => $v){
            $goods_id = $v['goods_id'];
            $join_num = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_record').' where uniacid=:uniacid and goods_id = :goods_id',array(':uniacid'=>$uniacid,':goods_id'=>$goods_id));
            $ret[$k]['join_num'] = $join_num;
            if($goods_id == 0){
                unset($ret[$k]);
            }else{
                $good_del = pdo_fetch("SELECT * from".tablename('choujiang_goods')."WHERE uniacid = :uniacid and id = :id",array(':uniacid'=>$uniacid,':id'=>$goods_id));

                if($good_del['is_del'] == -1){
                    $ret[$k]['del'] = 1;
                }
            }
        }     
                 
        return $this->result(0,'success',$ret);
    }
    // 发起抽奖数量
    public function doPageGoodsStartNum(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $openid = $_REQUEST['openid'];
        $ret['start'] = count(pdo_fetchall("SELECT * from".tablename('choujiang_goods')."WHERE uniacid = :uniacid and goods_openid = :openid and is_del = 1",array(':uniacid'=>$uniacid,':openid'=>$openid)));
        $ret['join'] = count(pdo_fetchall("SELECT * from".tablename('choujiang_record')."WHERE uniacid = :uniacid and openid = :openid",array(':uniacid'=>$uniacid,':openid'=>$openid)));
        $pt_record = count(pdo_fetchall("SELECT * from".tablename('choujiang_record')."WHERE uniacid = :uniacid and openid = :openid and status != 0",array(':uniacid'=>$uniacid,':openid'=>$openid)));
        $scene_record = count(pdo_fetchall("SELECT * from".tablename('choujiang_scene')."WHERE uniacid = :uniacid and openid = :openid and status != 0",array(':uniacid'=>$uniacid,':openid'=>$openid)));
        $ret['obtain'] = intval($pt_record) + intval($scene_record);
        return $this->result(0,'success',$ret);
    }


    // 参与抽奖
    public function doPageParticipate(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $openid = $_REQUEST['openid'];
        $id = $_REQUEST['id'];
        $payfu = $_REQUEST['payfu'];
        $sy_num = 0;
        $base = pdo_fetch("SELECT * from".tablename('choujiang_base')."WHERE uniacid = :uniacid",array(':uniacid'=>$uniacid));
        $user = pdo_fetch("SELECT * from".tablename('choujiang_user')."WHERE uniacid = :uniacid and openid = :openid",array(':uniacid'=>$uniacid,':openid'=>$openid));
        if ($openid == '') {
           $str['status'] = -3;
        }else{
            if($user['nickname'] == '' || $user['avatar'] == ''){
                $str['status'] = -3;
            }else{

                if($base['smoke_num'] == 0 || $payfu == 1){   //不限制
                    $sy_num = 1;
                }else{
                    if($user['smoke_num'] > 0){
                        $pata['smoke_num'] = $user['smoke_num'] - 1; 
                        $sy_num = 1;
                    }else if($user['smoke_share_num'] > 0){
                        $pata['smoke_share_num'] = $user['smoke_share_num'] - 1; 
                        $sy_num = 1;
                    }
                }
                
                if($sy_num == 1){
                    $join = pdo_fetch("SELECT * from".tablename('choujiang_record')."WHERE uniacid = :uniacid and goods_id = :id and openid = :openid",array(':uniacid'=>$uniacid,':id'=>$id,':openid' => $openid));
                    if(empty($join)){
                        $ret = pdo_fetch("SELECT * from".tablename('choujiang_goods')."WHERE uniacid = :uniacid and id = :id",array(':uniacid'=>$uniacid,':id'=>$id));
                        $team_id = 0;
                        $team_join = $_REQUEST['team_join'];
                        if($ret['team_status'] == 1 && $team_join == 1){
                            $c_openid = $_REQUEST['c_openid'];
                            
                            if($team_join == 1){
                                $c_team = pdo_fetch("SELECT * from".tablename('choujiang_team')."WHERE uniacid = :uniacid and goods_id = :id and openid = :openid",array(':uniacid'=>$uniacid,':id'=>$id,':openid'=>$c_openid));
                                $team = pdo_fetch("SELECT * from".tablename('choujiang_team')."WHERE uniacid = :uniacid and goods_id = :id and openid = :openid",array(':uniacid'=>$uniacid,':id'=>$id,':openid'=>$openid));
                                $team_num = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_team').' where uniacid = :uniacid and goods_id = :id and c_openid = :c_openid',array(':uniacid'=>$uniacid,':id'=>$id,':c_openid'=>$c_team['c_openid']));
                                if(empty($team)){
                                    if($ret['team_num'] > $team_num){
                                        $team = pdo_fetch("SELECT * from".tablename('choujiang_team')."WHERE uniacid = :uniacid and goods_id = :id and openid = :openid",array(':uniacid'=>$uniacid,':id'=>$id,':openid'=>$openid));
                                        $patas['uniacid'] = $uniacid; 
                                        $patas['goods_id'] = $id; 
                                        $patas['c_openid'] = $c_team['c_openid']; 
                                        $patas['openid'] = $openid; 
                                        $patas['avatar'] = $user['avatar']; 
                                        $patas['nickname'] = $user['nickname']; 
                                        $patas['c_team_id'] = $c_team['c_team_id']; 
                                        $sts = pdo_insert('choujiang_team',$patas);
                                        if($sts){
                                            $uid = pdo_insertid();
                                        }
                                       
                                    }else{
                                        // $str['status'] = -5; //队伍已满
                                        $te_status = -5; //队伍已满
                                        // return $this->result(0,'success',$str);
                                    }
                                    // 
                                }else{
                                   $str['status'] = -4; //已有队伍
                                    return $this->result(0,'success',$str);
                                }
                            }
                            
                        }else if($ret['team_status'] == 0 && $team_join == 1){
                           $str['status'] = -3; //暂不支持组队 
                           return $this->result(0,'success',$str);
                        }
                        $data['goods_id'] = $id;
                        $data['uniacid'] = $uniacid;
                        if($ret['goods_status'] == 2){
                            $data['goods_name'] = '红包：'.$ret['red_envelope'].'元';
                        }else{
                            $data['goods_name'] = $ret['goods_name'];
                        }   
                        $data['openid'] = $openid;
                        $data['nickname'] = $user['nickname'];
                        $data['status'] = '0';
                        $data['formid'] = $_REQUEST['formid'];
                        $data['create_time'] = time();
                        $data['avatar'] = $user['avatar'];
                        $data['team_id'] = $c_team['c_team_id'];
                        $str['status'] = pdo_insert('choujiang_record', $data);
                        if($str['status'] == 1 && $base['smoke_num'] > 0 && $payfu != 1){
                            pdo_update('choujiang_user', $pata,array('id'=>$user['id']));
                        }
                        if($team_join == 1 && $str['status'] == 1 && $sts == 1 && $ret['team_num'] == intval($team_num)+1){
                            $team_record = pdo_fetchall("SELECT * from".tablename('choujiang_record')."WHERE uniacid = :uniacid and goods_id = :id and team_id = :team_id",array(':uniacid'=>$uniacid,':id'=>$id,':team_id'=>$c_team['c_team_id']));
                            foreach($team_record as $k => $v){
                                pdo_update('choujiang_record',array('team_status'=>1),array('id'=>$v['id']));
                            }
                        }
                        $str['avatar'] = $user['avatar'];
                    }else{
                        $str['status'] = -1;
                    }
                }else{
                    $str['status'] = -2;
                }
            }
        }
        
        if($te_status == -5 && $str['status'] == 1){
            $str['status'] = -5;
        }
        
        return $this->result(0,'success',$str);
    }

    // 创建组队
    public function doPageCreateTeam(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_REQUEST['id'];
        $openid = $_REQUEST['openid'];
        $goods = pdo_fetch("SELECT * from".tablename('choujiang_goods')."WHERE uniacid = :uniacid and id = :id",array(':uniacid'=>$uniacid,':id'=>$id));
        if($goods['team_status'] == 1 and $goods['team_num'] >= 2){
            $team = pdo_fetch("SELECT * from".tablename('choujiang_team')."WHERE uniacid = :uniacid and goods_id = :id and openid = :openid",array(':uniacid'=>$uniacid,':id'=>$id,':openid'=>$openid));
            if(empty($team)){
                $member = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where uniacid=:uniacid and openid = :openid", array(":uniacid" => $_W['uniacid'],':openid' => $_REQUEST['openid']));
                $data['uniacid'] = $uniacid;
                $data['openid'] = $openid;
                $data['goods_id'] = $id;
                $data['c_openid'] = $openid;
                $data['nickname'] = $member['nickname'];
                $data['avatar'] = $member['avatar'];
                $str = pdo_insert('choujiang_team',$data);
                $uid = pdo_insertid();
                pdo_update('choujiang_team',array('c_team_id'=>$uid),array('openid'=>$openid,'goods_id'=>$id,'id'=>$uid));
                $strs = pdo_update('choujiang_record',array('team_id'=>$uid),array('openid'=>$openid,'goods_id'=>$id));
                if($strs && $str){
                    $status = 1; //创建队伍成功
                }else{
                    $status = -2; //创建队伍失败
                }
             }else{
                $status = -1; //已有队伍
            }
        }else{
            $status = -3; //暂不支持组队
        }
        
        return $this->result(0,'success',$status);

    }
    // 我的队伍
    public function doPageTeamInfo(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_REQUEST['id'];
        $openid = $_REQUEST['openid'];
        $team = pdo_fetch("SELECT * from".tablename('choujiang_team')."WHERE uniacid = :uniacid and goods_id = :id and openid = :openid",array(':uniacid'=>$uniacid,':id'=>$id,':openid'=>$openid));
        $teams = pdo_fetchall("SELECT * from".tablename('choujiang_team')."WHERE uniacid = :uniacid and goods_id = :id and c_openid = :openid",array(':uniacid'=>$uniacid,':id'=>$id,':openid'=>$team['c_openid']));
        return $this->result(0,'success',$teams);

    }

    // 开奖
    public function doPageGoodsOpen(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_REQUEST['id'];
        // $id = 962;
        $ret = pdo_fetch("SELECT * from".tablename('choujiang_goods')."WHERE uniacid = :uniacid and id = :id",array(':uniacid'=>$uniacid,':id'=>$id));
                
        $base = pdo_fetch("SELECT * from".tablename('choujiang_base')."WHERE uniacid = :uniacid",array(':uniacid'=>$uniacid));
        if($ret['status'] == 0){
            if($ret['The_winning'] == 1){    //指定中奖人
                $openid_arr = $ret['openid_arr'];
                $winning_openid = unserialize($openid_arr); 
                $str = pdo_update('choujiang_goods', array('status'=>1,'send_time'=>time()),array('id'=>$id));
                if($ret['goods_status'] == 2){   //红包
                    $money = $ret['red_envelope'];
                    foreach($winning_openid as $key => $value){
                        $user = pdo_fetch("SELECT * from".tablename('choujiang_user')."WHERE uniacid = :uniacid and openid = :openid",array(':uniacid'=>$uniacid,':openid'=>$value));
                        $earnings = $user['earnings'];
                        $remaining_sum = $user['remaining_sum'];
                        $pata['earnings'] = $earnings + $money;
                        $pata['remaining_sum'] = $remaining_sum + $money;
                        $data['uniacid'] = $uniacid;
                        $data['openid'] = $value['openid'];
                        $data['create_time'] = time();
                        $data['money'] = $money;
                        pdo_insert('choujiang_earnings',$data);
                        pdo_update('choujiang_user',$pata,array('id'=>$user['id']));
                    }
                }
                if($ret['goods_status'] == 1){   //电子卡
                    $cards = unserialize($ret['card_info']);
                    $card_arr = array();
                    $i = 0;
                    foreach ($cards as $k => $v) {
                        $card_arr[$i]['card_num'] = $k;
                        $card_arr[$i]['card_password'] = $v;
                        $i ++ ;
                    }
                }

                        
                $i = 0;
                foreach($winning_openid as $key => $value){
                    // if($ret['smoke_set'] == 3){  //现场开奖 生成核销码
                    //     $this->doWebExchange($id,$value);
                    // }
                    
                    $record = pdo_fetch("SELECT id,openid from".tablename('choujiang_record')."WHERE uniacid = :uniacid and goods_id = :id and openid = :openid",array(':uniacid'=>$uniacid,':id'=>$id,':openid'=>$value));
                   
                    if($ret['goods_status'] == 1){   //电子卡
                        $stat['status'] = 1;
                        $stat['card_num'] = $card_arr[$i]['card_num'];
                        $stat['card_password'] = $card_arr[$i]['card_password'];
                        pdo_update('choujiang_record', $stat,array('id'=>$record['id']));
                    }else{
                        pdo_update('choujiang_record', array('status'=>1),array('id'=>$record['id']));
                    }
                    $i ++;
                }
                if(!empty($str)){
                    $res['status'] = 1;
                    $res['goods_status'] = $ret['goods_status'];
                   
                    $this->doPageInform($id);

                }else{
                    $res['status'] = -1;
                }
            }else{   //未指定中奖人
                $join = pdo_fetchall("SELECT id,openid,team_id,team_status from".tablename('choujiang_record')."WHERE uniacid = :uniacid and goods_id = :id",array(':uniacid'=>$uniacid,':id'=>$id));
                        
                foreach($join as $key => $value){
                    pdo_update('choujiang_record', array('finish_time'=>time()),array('id'=>$value['id']));
                }

                $winning_num = $base['winning_num']; 
                if($winning_num > 0){   //限制中奖次数
                    foreach ($join as $key => $value) {
                        $user = pdo_fetch("SELECT * from".tablename('choujiang_user')."WHERE uniacid = :uniacid and openid = :openid",array(':uniacid'=>$uniacid,':openid'=>$value['openid']));
                        $send_time = $user['send_time'];
                        $finish_time = strtotime("+1 months",$send_time);
                        if($finish_time <= time()){
                            pdo_update('choujiang_user', array('send_time'=>time(),'winning_num'=>$winning_num),array('id'=>$user['id']));
                        }else if($send_time == '' || $send_time == null){
                            pdo_update('choujiang_user', array('send_time'=>time(),'winning_num'=>$winning_num),array('id'=>$user['id']));
                        }else{
                            if($user['winning_num'] <= 0){
                                unset($join[$key]);
                            }
                        }
                       
                    }
                }
                
                $str = pdo_update('choujiang_goods', array('status'=>1,'send_time'=>time()),array('id'=>$id));
                $max_num = $ret['goods_num'];
                $join_count = count($join);
                if($join_count < $max_num){
                    $max_num = $join_count;
                }
                $arr = array();
                if($max_num == 1){
                    $arr[] = array_rand($join,$max_num); 
                }else{
                    $arr = array_rand($join,$max_num); 
                }
                foreach($arr as $k => $val){
                    $obtain[]=$join[$val];
                }
                        
                if($ret['team_status'] == 1){  //如果支持组队
                    $team_num = $ret['team_num']; //每队人数
                    $team_id_arr = array(); //队伍类别数组
                    $new_obtain = array(); //队伍类别数组
                    $new_team_id_arr = array();
                    foreach ($obtain as $key => $value) {
                        if($value['team_id']>0 && $value['team_status'] == 1){    //是组队且组队成功
                            array_push($team_id_arr,$value['team_id']);
                            $team = pdo_fetchall("SELECT id,openid,team_id,team_status from".tablename('choujiang_record')."WHERE uniacid = :uniacid and goods_id = :id and team_id = :team_id and team_status = 1",array(':uniacid'=>$uniacid,':id'=>$id,':team_id'=>$value['team_id']));
                                    
                            foreach ($team as $k => $v){
                                array_push($obtain,$team[$k]);
                            }

                        }
                        
                    }
                    $obtain = array_values($this->array_unset_tt($obtain,'openid'));
                    $team_id_arr = array_unique($team_id_arr);
                            
                    $te_obtain_num = count($obtain);
                    if($te_obtain_num > $max_num){
                        $team_long = count($team_id_arr);  //队伍个数
                        $team_zong = intval($team_long) * intval($team_num); //组队的人数
                        if($team_zong > $max_num){  //如果组队人数大于总中奖人数
                            $duo_num = floor(intval($max_num)/intval($team_num)); //对大允许队伍数
                            for($i=1;$i<=$duo_num;$i++){
                                array_push($new_team_id_arr,$team_id_arr[$i]);
                            }
                        }else{
                            $new_team_id_arr = $team_id_arr;
                        }
                    }else{
                        $new_team_id_arr = $team_id_arr;
                    }
                    foreach($obtain as $key => $value){
                        foreach ($new_team_id_arr as $k => $v) {
                            if($value['team_id'] == $v){
                                array_push($new_obtain,$value);
                            }
                        }
                    }

                    $new_obtains = array_values($this->array_unset_tt($new_obtain,'openid'));
                    $new_obtains_num = count($new_obtains);
                    $getwxacodren_num = intval($max_num) - intval($new_obtains_num);
                    $is = 1;
                    foreach($obtain as $kk => $vv){
                        if(($vv['team_id'] == 0 || $vv['team_status'] == 0)&& $is <= $getwxacodren_num){
                            array_push($new_obtains,$vv);
                            $is++;
                        }
                    }
                    $obtain = $new_obtains;
                }

                if($ret['goods_status'] == 2){   //红包
                    $money = $ret['red_envelope'];
                    foreach($obtain as $key => $value){
                        $user = pdo_fetch("SELECT * from".tablename('choujiang_user')."WHERE uniacid = :uniacid and openid = :openid",array(':uniacid'=>$uniacid,':openid'=>$value['openid']));
                        $earnings = $user['earnings'];
                        $remaining_sum = $user['remaining_sum'];
                        $pata['earnings'] = $earnings + $money;
                        $pata['remaining_sum'] = $remaining_sum + $money;
                        $data['uniacid'] = $uniacid;
                        $data['openid'] = $value['openid'];
                        $data['create_time'] = time();
                        $data['money'] = $money;
                        pdo_insert('choujiang_earnings',$data);
                        pdo_update('choujiang_user',$pata,array('id'=>$user['id']));
                    }
                }
                if($ret['goods_status'] == 1){ 
                    $cards = unserialize($ret['card_info']);
                    $card_arr = array();
                    $i = 0;
                    foreach ($cards as $k => $v) {
                        $card_arr[$i]['card_num'] = $k;
                        $card_arr[$i]['card_password'] = $v;
                        $i ++ ;
                    }
                }

                $i = 0;
                foreach($obtain as $key => $value){
                    // if($ret['smoke_set'] == 3){  //现场开奖 生成核销码
                    //     $this->doWebExchange($id,$value['openid']);
                    // }
                    $users = pdo_fetch("SELECT * from".tablename('choujiang_user')."WHERE uniacid = :uniacid and openid = :openid",array(':uniacid'=>$uniacid,':openid'=>$value['openid']));
                    if($base['winning_num'] == 0){
                        $user_winning_num = $users['winning_num'];
                    }else{
                        $user_winning_num = $users['winning_num'] - 1;
                    }
                    pdo_update('choujiang_user', array('winning_num'=>$user_winning_num),array('id'=>$users['id']));
                    if($ret['goods_status'] == 1){   //电子卡
                        $stat['status'] = 1;
                        $stat['card_num'] = $card_arr[$i]['card_num'];
                        $stat['card_password'] = $card_arr[$i]['card_password'];
                        pdo_update('choujiang_record', $stat,array('id'=>$value['id']));
                    }else{
                        pdo_update('choujiang_record', array('status'=>1),array('id'=>$value['id']));
                    }
                    $i ++;
                }
                
                if(!empty($str)){
                    $res['status'] = 1;
                    $res['goods_status'] = $ret['goods_status'];
                   
                    $this->doPageInform($id);

                }else{
                    $res['status'] = -1;
                }
            }
                    
        }else{
            $res['status'] = -1;
        }
            
        return $this->result(0,'success',$res);
        

    }

     function array_unset_tt($arr,$key){      //去重
        //建立一个目标数组  
        $res = array();        
        foreach ($arr as $value) {           
           //查看有没有重复项  
             
           if(isset($res[$value[$key]])){  
                 //有：销毁  
                  
                 unset($value[$key]);  
                   
           }  
           else{  
                  
                $res[$value[$key]] = $value;  
           }    
        }  
        return $res;  
    }  

    // 实物中奖  领取方式
    public function doPageBusiness_address(){
        global $_GPC, $_W;
        $id = $_REQUEST['id'];
        $uniacid = $_W['uniacid'];
        $ret = pdo_fetch('SELECT * FROM ' . tablename('choujiang_goods') ."where uniacid = :uniacid and id = :id",array(':uniacid'=>$uniacid,':id'=>$id));
        $base = pdo_fetch('SELECT * FROM ' . tablename('choujiang_base') ."where `uniacid`='{$uniacid}'");
        $ret['zt_out_time'] = $base['takes_time'];
        return $this->result(0,'success',$ret);
    }
    // 电子卡中奖 获得卡号密码查询
    public function doPageMyOneRecord(){
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $id = $_REQUEST['id'];
        $openid = $_REQUEST['openid'];
        $cards = pdo_fetch('SELECT * FROM ' . tablename('choujiang_record') ."where uniacid = :uniacid and openid = :openid and goods_id = :id",array(':uniacid'=>$uniacid,':openid'=>$openid,':id'=>$id));
        $ret['card_num'] = $cards['card_num'];
        $ret['card_password'] = $cards['card_password'];
        return $this->result(0,'success',$ret);

    }
    

    //开奖成功 模板通知
    public function doPageInform($id){
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $base = pdo_fetch('SELECT * FROM ' . tablename('choujiang_base') ."where `uniacid`='{$uniacid}' ");
        $appid = $base['appid'];
        $appsecret = $base['appsecret'];
        $template_id = $base['template_id'];
        $tokenUrl="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
        $getArr=array();
        $tokenArr=json_decode($this->send_post($tokenUrl,$getArr,"GET"));
        $access_token=$tokenArr->access_token;
        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token ;
        $dd = array();
        $sql = pdo_fetchall("SELECT * FROM " .tablename('choujiang_record')." where `uniacid`='{$uniacid}' and `goods_id`='{$id}'" );
        foreach($sql as $key => $value){
            $dd['form_id'] = $value['formid'];
            $dd['touser'] = $value['openid'];
            $content = array(
                "keyword1"=>array(
                "value"=> $value['goods_name'],
                "color"=>"#4a4a4a"
                ),
                "keyword2"=>array(
                    "value"=>$value['nickname'].',您参与的 "'.$value['goods_name'].'" 活动现在开奖啦,点击查看中奖名单',
                    "color"=>"#9b9b9b"
                ),
                  
            );
            $dd['template_id']=$template_id;
            $dd['page']='choujiang_page/fuli_xq/fuli_xq?id='.$id;  //点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,该字段不填则模板无跳转。
            // $dd['page']='/choujiang_page/fuli_xq/fuli_xq?id='.$id;  //点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,该字段不填则模板无跳转。
            $dd['data']=$content;                        //模板内容，不填则下发空模板
            $dd['color']='';                        //模板内容字体的颜色，不填默认黑色
            $dd['emphasis_keyword']='';    //模板需要放大的关键词，不填则默认无放大
            $result = $this->https_curl_json($url,$dd,'json');
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

    // 中奖状态
    public function doPageObtainRecord(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_REQUEST['id'];
        $openid = $_REQUEST['openid'];
        $res = pdo_fetch("SELECT * from".tablename('choujiang_record')."WHERE uniacid = :uniacid and goods_id = :id and openid = :openid",array(':uniacid'=>$uniacid,':id'=>$id,':openid'=>$openid));
        return $this->result(0,'success',$res);

    }

    // 中奖人员
    public function doPageObtainRecordUser(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_REQUEST['id'];
        $openid = $_REQUEST['openid'];
        $res = pdo_fetchall("SELECT * from".tablename('choujiang_record')."WHERE uniacid = :uniacid and goods_id = :id and status != 0 ORDER BY id DESC",array(':uniacid'=>$uniacid,':id'=>$id));
        return $this->result(0,'success',$res);

    }

    // 选择保存地址
    public function doPageUpdAdd(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_REQUEST['record_id'];
        $data['user_tel'] = $_REQUEST['user_tel'];
        $data['user_zip'] = $_REQUEST['user_zip'];
        $data['user_address'] = $_REQUEST['user_address'];
        $data['user_name'] = $_REQUEST['user_name'];
        $data['get_status'] = 1;
        $data['get_create_time'] = date('Y-m-d H:i:s',time());
        $str = pdo_update('choujiang_record', $data,array('id'=>$id,'uniacid'=>$uniacid));
        if(!empty($str))
        {
            $res = 1;
        }else{
            $res = -1;
        }
        return $this->result(0,'success',$res);

    }



    // 常见问题
    public function doPageProblems(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $res = pdo_fetchall("SELECT * from".tablename('choujiang_problems')."WHERE uniacid = :uniacid",array(':uniacid'=>$uniacid));
        return $this->result(0,'success',$res);
    }
    public function doPageAnswer(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_REQUEST['id'];
        $res = pdo_fetch("SELECT answer from".tablename('choujiang_problems')."WHERE uniacid = :uniacid and id = :id",array(':uniacid'=>$uniacid,':id'=>$id));
        return $this->result(0,'success',$res);
    }

    // 小程序推荐
    public function doPageUrlXcx(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $res = pdo_fetchall("SELECT * from".tablename('choujiang_xcx')."WHERE uniacid = :uniacid and status = 1",array(':uniacid'=>$uniacid));
        foreach($res as $key => $value){
            $res[$key]['icon'] = $_W['attachurl'].$value['icon'];
        }
        return $this->result(0,'success',$res);
    }


    // 删除
    public function doPageHomeDelete(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_REQUEST['id'];
        $openid = $_REQUEST['openid'];
        $status = $_REQUEST['status']; 
        if($status == 1){  //发起的
            $str = pdo_update('choujiang_goods',array('is_del' => -1) ,array('id' => $id ,'goods_openid' => $openid, 'uniacid' => $uniacid));
        }else{
            $goods = pdo_fetch("SELECT * from".tablename('choujiang_goods')."WHERE uniacid = :uniacid and id = :id",array(':uniacid'=>$uniacid,':id'=>$id));
            if($goods['smoke_set'] == 3){
                $str = pdo_delete('choujiang_scene', array('goods_id' => $id ,'openid' => $openid, 'uniacid' => $uniacid));
            }else{
                $str = pdo_delete('choujiang_record', array('goods_id' => $id ,'openid' => $openid, 'uniacid' => $uniacid));
            }
        }
        
        if(!empty($str)){
            $ret = 1;
        }else{
            $ret = -1;
        }
        return $this->result(0,'success',$ret);

    }
    public function doPageMemberHxM(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $goods_id = $_REQUEST['goods_id'];
        $openid = $_REQUEST['openid'];
        $sql = pdo_fetch('SELECT * FROM ' . tablename('choujiang_exchange') . " where uniacid=:uniacid and goods_id = :id", array(":id"=>$goods_id,":uniacid" => $uniacid));
        if($_W['setting']['remote']['type'] != 0){   //当开启远程存储
            $in = 'https';
            $url = $_W['setting']['site']["url"];
            $sub = substr($url,0,strpos($url, ':'));
            if($sub == $in){
                $new_url = $url;
            }else{
                $new_url = $sub.'s:'.substr($url,strpos($url,':')+1);
            }
            $ret['verification'] = $new_url.'/attachment/choujiang_page/'.$sql['verification'];
            $ret['status'] = $sql['status'];
        }else{
            $ret['verification'] = $_W['attachurl'].'choujiang_page/'.$sql['verification'];
            $ret['status'] = $sql['status'];
        }
        return $this->result(0,'success',$ret);
    }

    // 核销
    // 核销判断
    public function doPageHexiaoIf()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $text_ewm = $_REQUEST['text_ewm'];
        $openid = $_REQUEST['openid'];
        $list = pdo_fetch('SELECT * FROM ' . tablename('choujiang_exchange') . " where uniacid=:uniacid and orders = :text_ewm", array(":text_ewm"=>$text_ewm,":uniacid" => $uniacid));
        if(empty($list)){
            // $str = '该订单不存在';
            $state = -2;
        }else{
            $goods = pdo_fetch('SELECT * FROM ' . tablename('choujiang_goods') . " where uniacid=:uniacid and id = :id", array(":id"=>$list['goods_id'],":uniacid" => $uniacid));
            if($list['status'] == 1){
              // $str = '该订单已核销';
                $state = -1;
            }else if($goods['goods_openid'] == $openid){
                $state = 1;
            }else{
                $state = -3;
            }
        }
        $ret['status'] = $state;
      // $ret['con'] = $str;
      return $this->result(0, '成功'/*'成功'*/, $ret);

    }
// 核销
    public function doPageHexiaoIfIn(){
    
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $text_ewm = $_REQUEST['text_ewm'];
        $list = pdo_fetch('SELECT * FROM ' . tablename('choujiang_exchange') . " where uniacid=:uniacid and orders = :text_ewm and status = 0", array(":text_ewm"=>$text_ewm,":uniacid" => $uniacid));
        if(!empty($list)){
            $rets = pdo_update("choujiang_exchange", array('status' => 1),array('id' => $list['id'],'uniacid' => $uniacid));                  
            if($rets){
              $str = 1;
            }else{
              $str = -1;
            }
        }else{
            $str = -1;
        }
      
      return $this->result(0, '成功'/*'成功'*/, $str);


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
    // 自提生成二维码
    public function doPageMemberTakeCode(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $goods_id = $_REQUEST['goods_id'];
        $openid = $_REQUEST['openid'];
        $result = pdo_fetch('SELECT * FROM ' . tablename('choujiang_base') . " where uniacid=:uniacid", array(":uniacid" => $uniacid));
        $APPID = $result['appid'];
        $SECRET = $result['appsecret'];
        $tokenUrl="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$APPID}&secret={$SECRET}";
        $getArr=array();
        $tokenArr=json_decode($this->send_post($tokenUrl,$getArr,"GET"));
        $access_token=$tokenArr->access_token;
        $noncestr = date('YmdHis') . rand(10000000,99999999);
        $width=430;
        $post_data='{"path":"'.$noncestr.'","width":'.$width.'}';
        $url = 'https://api.weixin.qq.com/wxa/getwxacode?access_token='.$access_token;

        $result=$this->api_notice_increment($url,$post_data); 

        $image_name = md5(uniqid(rand())).".jpg";
        $filepath = "../attachment/choujiang_page/{$image_name}";   
        $file_put = file_put_contents($filepath, $result);
    
        if($file_put){
            $sql = pdo_fetch('SELECT * FROM ' . tablename('choujiang_exchange') . " where uniacid=:uniacid and goods_id = :id and openid = :openid", array(":uniacid" => $uniacid,':id'=>$goods_id,':openid'=>$openid));
            if(empty($sql)){
                $datas = array('verification' => $image_name,'uniacid' => $uniacid,'goods_id'=>$goods_id,'openid'=>$openid,'status'=>0,'orders'=>$noncestr,'create_time'=>time());
                $sts = pdo_insert("choujiang_exchange", $datas);                  
            }
        }
        if($sts){
            $data['get_status'] = 2;
            $data['get_create_time'] = date('Y-m-d H:i:s',time());
            $sts = pdo_update('choujiang_record',$data,array('goods_id'=>$goods_id,'openid'=>$openid));
            if($sts){
                $status = 1;
            }else{
                $status = -1;
            }

        }else{
            $status = -1;
        }
        return $this->result(0,'success',$status);
    }


    // 现场抽奖中奖生成二维码
    public function doWebExchange($id,$openid){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $goods_id = $id;
        $result = pdo_fetch('SELECT * FROM ' . tablename('choujiang_base') . " where uniacid=:uniacid", array(":uniacid" => $uniacid));
        $APPID = $result['appid'];
        $SECRET = $result['appsecret'];
        $tokenUrl="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$APPID}&secret={$SECRET}";
        $getArr=array();
        $tokenArr=json_decode($this->send_post($tokenUrl,$getArr,"GET"));
        $access_token=$tokenArr->access_token;
        $noncestr = date('YmdHis') . rand(10000000,99999999);
        $width=430;
        $post_data='{"path":"'.$noncestr.'","width":'.$width.'}';
        $url = 'https://api.weixin.qq.com/wxa/getwxacode?access_token='.$access_token;

        $result=$this->api_notice_increment($url,$post_data); 

        $image_name = md5(uniqid(rand())).".jpg";
        $filepath = "../attachment/choujiang_page/{$image_name}";   
        $file_put = file_put_contents($filepath, $result);
    
        if($file_put){
            $sql = pdo_fetch('SELECT * FROM ' . tablename('choujiang_exchange') . " where uniacid=:uniacid and goods_id = :id and openid = :openid", array(":uniacid" => $uniacid,':id'=>$goods_id,':openid'=>$openid));
            if(empty($sql)){
                $datas = array('verification' => $image_name,'uniacid' => $uniacid,'goods_id'=>$goods_id,'openid'=>$openid,'status'=>0,'create_time'=>time(),'orders'=>$noncestr);
                pdo_insert("choujiang_exchange", $datas);                  
            }
        }
        return $image_name;

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

    // 付费版
    public function doPageVio_Num(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $ret = pdo_fetchall('SELECT * FROM ' . tablename('choujiang_vip_num') . " where uniacid=:uniacid", array(":uniacid" => $uniacid));
        return $this->result(0,'success',$ret);
    }

// 订单支付
    public function doPagePay()
    {
        global $_GPC, $_W;
        include 'wxpay.php';
        $res = pdo_get('choujiang_base', array('uniacid' => $_W['uniacid']));
        $appid = $res['appid'];
        $openid = $_REQUEST['openid'];
        $mch_id = $res['mch_id'];
        $key = $res['appkey'];
        $out_trade_no = $mch_id . time();
        $total_fee = $_REQUEST['total'];
        if (empty($total_fee)) {
            $body = '订单付款';
            $total_fee = floatval(0.01 * 100);
        } else {
            $body = '订单付款';
            $total_fee = floatval($total_fee * 100);
        }
        $weixinpay = new WeixinPay($appid, $openid, $mch_id, $key, $out_trade_no, $body, $total_fee);
        $return = $weixinpay->pay();
        echo json_encode($return);
    }

    public function doPageXcxPayRecord(){
        global $_GPC, $_W;
        $data['uniacid'] = $_W['uniacid'];
        $data['openid'] = $_REQUEST['openid'];
        $member = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where uniacid=:uniacid and openid = :openid", array(":uniacid" => $_W['uniacid'],':openid' => $_REQUEST['openid']));
        $data['total'] = $_REQUEST['total'];
        $poundage = $_REQUEST['poundage'];
        if($poundage == 0){
            $data['y_total'] = 0;
        }else{
            $data['y_total'] = $poundage;
        }
        $data['y_total'] = $_REQUEST['xcx_price'];
        $data['nickname'] = $member['nickname'];
        $data['avatar'] = $member['avatar'];
        $data['goods_id'] = $_REQUEST['id'];
        $data['status'] = 6;
        $data['create_time'] = time();
        $res = pdo_insert('choujiang_pay_record', $data);
        $pay_id = pdo_insertid();
        if(empty($res)){
            $ret['status'] = -1;
        }else{
            $ret['status'] = 1;
            $ret['pay_id'] = $pay_id;
        }
        return $this->result(0,'success',$ret);
    }
    // 付费抽奖
    public function doPageJoinPayRecord(){
        global $_GPC, $_W;
        $data['uniacid'] = $_W['uniacid'];
        $data['openid'] = $_REQUEST['openid'];
        $member = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where uniacid=:uniacid and openid = :openid", array(":uniacid" => $_W['uniacid'],':openid' => $_REQUEST['openid']));
        $data['total'] = $_REQUEST['total'];
        $data['nickname'] = $member['nickname'];
        $data['avatar'] = $member['avatar'];
        $data['goods_id'] = $_REQUEST['id'];
        $data['status'] = 3;  
        $data['create_time'] = time();

        // 发起人获得
        $goods = pdo_fetch('SELECT openid FROM ' . tablename('choujiang_goods') . " where uniacid=:uniacid and id = :id", array(":uniacid" => $_W['uniacid'],':id' => $_REQUEST['id']));
        $goods_openid = $goods['openid'];
        $fq_member = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where uniacid=:uniacid and openid = :openid", array(":uniacid" => $_W['uniacid'],':openid' => $goods_openid));
        $remaining_sum = floatval($_REQUEST['total']) + floatval($fq_member['remaining_sum']);
        $earnings = floatval($_REQUEST['total']) + floatval($fq_member['earnings']);
        pdo_update('choujiang_user', array('earnings'=>$earnings,'remaining_sum'=>$remaining_sum),array('id'=>$fq_member['id']));

        $res = pdo_insert('choujiang_pay_record', $data);
        $pay_id = pdo_insertid();
        if(empty($res)){
            $ret['status'] = -1;
        }else{
            $ret['status'] = 1;
        }
        return $this->result(0,'success',$ret);
    }
// 付运费
    public function doPageBusinessPayRecord(){
        global $_GPC, $_W;
        $data['uniacid'] = $_W['uniacid'];
        $data['openid'] = $_REQUEST['openid'];
        $member = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where uniacid=:uniacid and openid = :openid", array(":uniacid" => $_W['uniacid'],':openid' => $_REQUEST['openid']));
        $data['total'] = $_REQUEST['total'];
        $data['nickname'] = $member['nickname'];
        $data['avatar'] = $member['avatar'];
        $data['goods_id'] = $_REQUEST['id'];
        $data['status'] = 7;  
        $data['create_time'] = time();
        // 发起人获得
        $goods = pdo_fetch('SELECT openid FROM ' . tablename('choujiang_goods') . " where uniacid=:uniacid and id = :id", array(":uniacid" => $_W['uniacid'],':id' => $_REQUEST['id']));
        $goods_openid = $goods['openid'];
        $fq_member = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where uniacid=:uniacid and openid = :openid", array(":uniacid" => $_W['uniacid'],':openid' => $goods_openid));
        $remaining_sum = floatval($_REQUEST['total']) + floatval($fq_member['remaining_sum']);
        $earnings = floatval($_REQUEST['total']) + floatval($fq_member['earnings']);
        pdo_update('choujiang_user', array('earnings'=>$earnings,'remaining_sum'=>$remaining_sum),array('id'=>$fq_member['id']));

        $res = pdo_insert('choujiang_pay_record', $data);
        $pay_id = pdo_insertid();
        if(empty($res)){
            $ret['status'] = -1;
        }else{
            $ret['status'] = 1;
        }
        return $this->result(0,'success',$ret);
    }
    public function doPagePayorder()
    {
        global $_GPC, $_W;
        $data['uniacid'] = $_W['uniacid'];
        $data['openid'] = $_REQUEST['openid'];
        $member = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where uniacid=:uniacid and openid = :openid", array(":uniacid" => $_W['uniacid'],':openid' => $_REQUEST['openid']));

        $data['vip_id'] = $_REQUEST['id'];
        $data['num'] = $_REQUEST['num'];
        $data['total'] = $_REQUEST['total'];
        $data['y_total'] = $_REQUEST['total'];
        $data['poundage'] = 0;
        $data['nickname'] = $member['nickname'];
        $data['avatar'] = $member['avatar'];
        $data['create_time'] = time();
        $data['status'] = 1;
        $res = pdo_insert('choujiang_pay_record', $data);
        if(empty($res)){
            $str = -1;
        }else{
            $now_num = $member['yu_num'] + $_REQUEST['num'];
            $ret = pdo_update('choujiang_user', array('yu_num' => $now_num),array('id'=>$member['id']));
            if(!empty($ret)){
                $str = 1;
            }
        }
        return $this->result(0,'success',$str);

    }
    // 红包发起抽奖

    public function doPagePay1()
    {
        global $_GPC, $_W;
        include 'wxpay.php';
        $res = pdo_get('choujiang_base', array('uniacid' => $_W['uniacid']));
        $appid = $res['appid'];
        $poundage = intval($res['poundage'])/100;
        $openid = $_REQUEST['openid'];
        $mch_id = $res['mch_id'];
        $key = $res['appkey'];
        $out_trade_no = $mch_id . time();
        $total_fee = $_REQUEST['total'];
        // $total_fee = 0.01;
        if (empty($total_fee)) {
            $body = '订单付款';
            $total_fee = floatval(0.01 * 100);
        } else {
            $body = '订单付款';
            $total_fee = floatval($total_fee * 100);
        }
        if($total_fee >= 100){
            $total_fee = round(floatval($total_fee + $total_fee * $poundage),2);
        }
        $weixinpay = new WeixinPay($appid, $openid, $mch_id, $key, $out_trade_no, $body, $total_fee);
        $return = $weixinpay->pay();
        echo json_encode($return);
    }
    public function doPagePayGoods()
    {
        global $_GPC, $_W;
        $data['uniacid'] = $_W['uniacid'];
        $data['openid'] = $_REQUEST['openid'];
        $total_fee = $_REQUEST['total'];

        $member = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where uniacid=:uniacid and openid = :openid", array(":uniacid" => $_W['uniacid'],':openid' => $_REQUEST['openid']));
        $base = pdo_fetch('SELECT * FROM ' . tablename('choujiang_base') . " where uniacid=:uniacid", array(":uniacid" => $_W['uniacid']));
        $poundage = $base['poundage']/100;  //手续费百分比
        $total_fee = round(floatval($total_fee + $total_fee * $poundage),2);  //支付总金额
        $data['poundage'] = round(floatval($total_fee * $poundage),2);  //手续费价格
        $data['y_total'] = $_REQUEST['total'];   //不算手续费的总价
        $data['total'] = $total_fee;
        $data['nickname'] = $member['nickname'];
        $data['avatar'] = $member['avatar'];
        $data['num'] = 0;
        $data['vip_id'] = 0;
        $data['create_time'] = time();
        $data['status'] = 2;
        $res = pdo_insert('choujiang_pay_record', $data);
        $pay_id = pdo_insertid();
        if(empty($res)){
            $ret['status'] = -1;
        }else{
            $ret['status'] = 1;
            $ret['pay_id'] = $pay_id;
        }
        return $this->result(0,'success',$ret);

    }

    // // 提现
  
    public function doPageWithdrawal(){
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $total = floatval($_REQUEST['total']);
        if($total > 1){
            $member = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where uniacid=:uniacid and openid = :openid", array(":uniacid" => $_W['uniacid'],':openid' => $_REQUEST['openid']));
            $remaining_sum = floatval($member['remaining_sum']);
            $new_remaining_sum = $remaining_sum - $total;
            $base = pdo_get('choujiang_base', array('uniacid' => $uniacid));
            $poundage = intval($base['poundage'])/100;
            $money = round(floatval($total - $total * $poundage),2);
            $data['uniacid'] = $uniacid;
            $data['openid'] = $_REQUEST['openid'];
            $data['total'] = $total;  //原价
            $data['money'] = $money;  //实际提现
            $data['poundage'] = round(floatval($total * $poundage),2);  //手续费
            $data['nickname'] = $member['nickname']; 
            $data['avatar'] = $member['avatar'];
            $data['create_time'] = time();

            $rets = pdo_insert('choujiang_withdrawal', $data);
            $ret = pdo_update('choujiang_user',array('remaining_sum'=>$new_remaining_sum),array('id'=>$member['id']));
            if($ret && $ret){
                $str['status'] = 1;
                $str['remaining_sum'] = $new_remaining_sum;
            }else{
                $str['status'] = -1;
            }
        }
        return $this->result(0,'success', $str);
    }   

    // 我的钱包
    public function doPageMyMoney(){
        global $_GPC, $_W;
        $data['uniacid'] = $_W['uniacid'];
        $member = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where uniacid=:uniacid and openid = :openid", array(":uniacid" => $_W['uniacid'],':openid' => $_REQUEST['openid']));
        if ($member['remaining_sum'] == '') {
           $member['remaining_sum'] = 0;
        }
        if ($member['earnings'] == '') {
           $member['earnings'] = 0;
        }
        $ret['remaining_sum'] = $member['remaining_sum'];
        $ret['earnings'] = $member['earnings'];
        return $this->result(0,'success',$ret);
    }
    // 我的交易记录
    public function doPageMyPayRecord(){
        global $_GPC, $_W;
        $_REQUEST['openid'] = $_REQUEST['openid'];
        $arr = array();
        $withdrawal = pdo_fetchall('SELECT * FROM ' . tablename('choujiang_withdrawal') . " where uniacid=:uniacid and openid = :openid order by create_time desc", array(":uniacid" => $_W['uniacid'],':openid' => $_REQUEST['openid']));
        $earnings = pdo_fetchall('SELECT * FROM ' . tablename('choujiang_earnings') . " where uniacid=:uniacid and openid = :openid order by create_time desc", array(":uniacid" => $_W['uniacid'],':openid' => $_REQUEST['openid']));
        foreach($withdrawal as $key => $value){
            $withdrawal[$key]['record_status'] = 1;
            if($value['status'] == 0){
                $withdrawal[$key]['status_name'] = '提现中';
                $withdrawal[$key]['now_money'] = '-'.$value['total'];
            }else if($value['status'] == 1){
                $withdrawal[$key]['status_name'] = '提现成功';
                $withdrawal[$key]['now_money'] = '-'.$value['total'];
            }else if($value['status'] == -1){
                $withdrawal[$key]['status_name'] = '提现失败(已退回)';
                $withdrawal[$key]['now_money'] = '+'.$value['total'];
            }
        }
        foreach($earnings as $key => $value){
            $earnings[$key]['record_status'] = 2;
            $earnings[$key]['now_money'] = '+'.$value['money'];
        }
        foreach($withdrawal as $key => $value){
            array_push($arr,$value);
        }
        foreach($earnings as $key => $value){
            array_push($arr,$value);
        }
        $res = array();
        foreach($arr as $v){  
          $res[] = $v['create_time'];  
        } 
        array_multisort($res, SORT_DESC, $arr);
        foreach($arr as $key => $value){
            if($value['record_status'] == 1){
                $arr[$key]['record_name'] = $value['status_name'];
            }else if($value['record_status'] == 2){
                $arr[$key]['record_name'] = '中奖收益';
            }
            $arr[$key]['create_time'] = date('Y-m-d H:i',$value['create_time']);
        }
        return $this->result(0,'success',$arr);

    }


    // 每日分享获得抽奖次数
    public function doPageShareNumMy(){
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $openid = $_REQUEST['openid'];
        $base = pdo_fetch('SELECT * FROM ' . tablename('choujiang_base') . " where uniacid=:uniacid", array(":uniacid" => $uniacid));
        $user = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where uniacid=:uniacid and openid = :openid", array(":uniacid" => $uniacid,':openid' => $_REQUEST['openid']));
        $share_num = $base['share_num'];
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $finish_time = strtotime(date($year.'-'.$month.'-'.$day.' '.'23:59:59'));  //每日更新最迟时间
        $start_time = strtotime(date($year.'-'.$month.'-'.$day.' '.'00:00:00'));  //每日更新最迟时间
        $now_time = time();
        if ($now_time <= $finish_time && ($user['share_num_time'] < $start_time || $user['share_num_time'] == '')) {
            $rets = pdo_update('choujiang_user', array('share_num'=>$share_num,'share_num_time'=>time()),array('id'=>$user['id']));
            if($rets){
                $ret['status'] = 1;
                $ret['num'] = $share_num;
            }else{
                $ret['status'] = -1;
            }
        }else{
            $ret['status'] = -1;
        }
        return $this->result(0,'success',$ret);

       
    }

    // 分享好友
    public function doPageShareAddMy(){
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $openid = $_REQUEST['openid'];
        $base = pdo_fetch('SELECT * FROM ' . tablename('choujiang_base') . " where uniacid=:uniacid", array(":uniacid" => $uniacid));
        $share_num = $base['share_num'];
        if($share_num > 0){
            $user = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where uniacid=:uniacid and openid = :openid", array(":uniacid" => $uniacid,':openid' => $_REQUEST['openid']));
            if($user['share_num'] >0){
                $data['share_num'] = $user['share_num'] -1;
                $data['smoke_share_num'] = $user['smoke_share_num'] +1;
                $rets = pdo_update('choujiang_user', $data,array('id'=>$user['id']));
                if($rets){
                     $str = 1;
                }else{
                    $str = -1;
                }
            }else{
                $str = -1;
            }
        }else if($share_num == 0){
            $user = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where uniacid=:uniacid and openid = :openid", array(":uniacid" => $uniacid,':openid' => $_REQUEST['openid']));
            $data['smoke_share_num'] = $user['smoke_share_num'] +1;
            $rets = pdo_update('choujiang_user', $data,array('id'=>$user['id']));
            $str = 2;
        }
        return $this->result(0,'success',$str);
    }
    // 广告轮播
    public function doPageAdvertising(){
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $ret = pdo_fetchall('SELECT * FROM ' . tablename('choujiang_advertising') . " where uniacid=:uniacid and to_index = 1", array(":uniacid" => $uniacid));
        foreach($ret as $key => $value){
            $ret[$key]['image'] = $_W['attachurl'].$value['image'];
        }
        return $this->result(0,'success',$ret);
    }




// 骗审导航
    public function doPagePiandaohang(){
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $cheat_nav = pdo_fetchall('SELECT * FROM ' . tablename('choujiang_cheat_nav') . " where uniacid=:uniacid", array(":uniacid" => $uniacid));
        foreach($cheat_nav as $key => $value){
            $cheat_nav[$key]['icon'] = $_W['attachurl'].$value['icon'];
        }   
        return $this->result(0,'success',$cheat_nav);
    }
    // 骗审列表
    public function doPageCheatList(){
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $cheat = pdo_fetchall('SELECT * FROM ' . tablename('choujiang_cheat') . " where uniacid=:uniacid", array(":uniacid" => $uniacid));
        foreach($cheat as $key => $value){
            $cheat[$key]['icon'] = $_W['attachurl'].$value['icon'];
        }   
        return $this->result(0,'success',$cheat);
    }

    // 骗审列表详情
    public function doPageCheatListIn(){
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $id = $_REQUEST['id'];
        $cheat = pdo_fetch('SELECT * FROM ' . tablename('choujiang_cheat') . " where uniacid=:uniacid and id = :id", array(":uniacid" => $uniacid,":id"=>$id));
        $cheat['icon'] = $_W['attachurl'].$cheat['icon'];
        return $this->result(0,'success',$cheat);
    }


    // 获取用户formid
    public function doPageUserFormId(){
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $data['form_id'] = $_REQUEST['form_id'];
        $data['form_time'] = time();
        $openid = $_REQUEST['openid'];
        $member = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where uniacid=:uniacid and openid = :openid", array(":openid"=>$openid,":uniacid" => $uniacid));
        if($member['form_id'] == ''){
            $arr = array();
        }else{
            $arr = unserialize($member['form_id']);
            foreach($arr as $k => $v){
                $form_time = $v['form_time'];
                $out_time = strtotime('-7 days',time());
                if($out_time >= $form_time){
                    unset($arr[$k]);
                }
            }
        }
        array_push($arr,$data);
        $new_arr = serialize($arr);
        $ret = pdo_update('choujiang_user',array('form_id' =>$new_arr),array('id'=>$member['id']));
        return $this->result(0,'success',$ret);
    }

    // 现场参与抽奖
    public function doPageSceneInto(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_REQUEST['id'];
        $openid = $_REQUEST['openid'];
        $record = pdo_fetch('SELECT * FROM ' . tablename('choujiang_scene') . " where uniacid=:uniacid and goods_id = :id and openid = :openid", array(":id"=>$id,":uniacid" => $uniacid,':openid'=>$openid));
        if($record){
            if($record['status'] == 1){
                $status = -2;//已开奖
            }else{
                
                if($record['join_status'] == -1){
                    $data['join_status']= 1;
                    $data['send_time']= time();
                    $data['broadcast_status']= '';
                }else if($record['join_status'] == 1){
                    $data['send_time']= time();
                    $data['broadcast_status']= '';
                }
                $str = pdo_update('choujiang_scene',$data,array('id'=>$record['id']));
                if($str){
                    $status = 1;
                }else{
                    $status = -1; //参与失败
                }
            }
            
        }else{
            $member = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where uniacid=:uniacid and openid = :openid", array(":openid"=>$openid,":uniacid" => $uniacid));
            $data['openid'] = $openid;
            $data['avatar'] = $member['avatar'];
            $data['nickname'] = $member['nickname'];
            $data['uniacid'] = $uniacid;
            $data['send_time'] = time();
            $data['goods_id'] = $id;
            $data['join_status'] = 1;
            $str = pdo_insert('choujiang_scene',$data);
            if($str){
                $status = 1;
            }else{
                $status = -3;
            }
        }
        return $this->result(0,'success',$status);
    }

     // 现场退出人员
    public function doPageSceneOut(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_REQUEST['id'];
        $openid = $_REQUEST['openid'];
        $record = pdo_fetch('SELECT * FROM ' . tablename('choujiang_scene') . " where uniacid=:uniacid and goods_id = :id and join_status = 1 and status = 0 and openid = :openid", array(':id'=>$id,':openid'=>$openid,':uniacid'=>$uniacid));
        $str = pdo_update('choujiang_scene',array('join_status'=>-1,'send_time'=>time(),'broadcast_status'=>''),array('id'=>$record['id']));
        if($str){
            $status = 1;
        }else{
            $status = -1;
        }
        return $this->result(0,'success',$status);

    }
    // 现场参与抽奖记录(现场在线人员)
    function GoodsSceneNow($id,$openid){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];

        $time = strtotime("-15 second");
        // $time = strtotime("-1 minutes");
        $record = pdo_fetchall('SELECT * FROM ' . tablename('choujiang_scene') . " where uniacid=:uniacid and goods_id = :id and status = 0 and send_time >= {$time}", array(":id"=>$id,":uniacid" => $uniacid));
            
        $ret = array();
        if($record){
            foreach($record as $key => $value){
                if($value['join_status'] == 1){
                    $info = $value['nickname'].' 进入现场';
                }else if($value['join_status'] == -1){
                    $info = $value['nickname'].' 离开现场';
                }
                $returns['avatar'] = $value['avatar'];
                $returns['content'] = $info;
                if($value['broadcast_status'] == ''){
                    $new_arr = array();
                    array_push($ret,$returns);
                    $data['openid'] = $openid;
                    $data['status'] = 1;
                    array_push($new_arr,$data);
                }else{
                    $new_arr = unserialize($value['broadcast_status']);
                    foreach($new_arr as $k => $v){
                        if($v['openid'] == $openid && $v['status'] == 0){
                            unset($new_arr[$k]);
                            array_push($ret,$returns);
                            $data['openid'] = $openid;
                            $data['status'] = 1;
                            array_push($new_arr,$data);
                            continue;
                        }else if($v['openid'] == $openid && $v['status'] == 1){
                            continue;
                        }else if($this->deep_in_array($new_arr,$openid) == false){

                            array_push($ret,$returns);
                            $data['openid'] = $openid;
                            $data['status'] = 1;
                            array_push($new_arr,$data);
                            continue;
                        }
                    }
                }
                   
                if(!empty($new_arr)){
                    pdo_update('choujiang_scene',array('broadcast_status'=>serialize($new_arr)),array('id'=>$value['id']));
                }
            }
        }
               
                
        return $ret;
    }
   
    // 现场新增讲话
    public function doPageDialogue_into(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $member = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where uniacid=:uniacid and openid = :openid", array(":openid"=>$_REQUEST['openid'],":uniacid" => $uniacid));
        $data['uniacid'] = $uniacid;
        $data['goods_id'] = $_REQUEST['goods_id'];
        $data['openid'] = $_REQUEST['openid'];
        $data['avatar'] = $member['avatar'];
        $data['nickname'] = $member['nickname'];
        $data['content'] = $_REQUEST['content'];
        $data['create_time'] = time();
        $str = pdo_insert('choujiang_scene_dialogue',$data);
        if($str){
            $status = 3;
        }else{
            $status = -2;
        }
        return $this->result(0,'success',$status);
    }
     // 现场讲话记录
    public function doPageDialogue_records(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_REQUEST['id'];
        $openid = $_REQUEST['openid'];
        $goods = pdo_fetch('SELECT * FROM ' . tablename('choujiang_goods') . " where uniacid=:uniacid and id = :id", array(":id"=>$id,":uniacid" => $uniacid));
        $goods_openid = $goods['goods_openid'];
        // $id = 797;
        // $openid = 'oQQf_0KyaKENcRwM1kgeF6W4hH_Y';
        $MemberRecord = $this->GoodsSceneNow($id,$openid);
        $record = pdo_fetchall('SELECT * FROM ' . tablename('choujiang_scene_dialogue') . " where uniacid=:uniacid and goods_id = :id", array(":id"=>$id,":uniacid" => $uniacid));
        $ret = array();
        $res = array();
        $time = strtotime("-2 minutes");
        foreach($record as $key => $value){
            if($value['broadcast_status'] == ''){
                $new_arr = array();
            }else{
                $new_arr = unserialize($value['broadcast_status']);
            }
            
            if($value['create_time'] >= $time){
                if($value['broadcast_status'] == ''){
                    array_push($ret,$value);
                    $data['openid'] = $openid;
                    $data['status'] = 1;
                    array_push($new_arr,$data);
                }else{
                    
                    foreach($new_arr as $k => $v){
                        if($v['openid'] == $openid && $v['status'] == 0){
                            unset($new_arr[$k]);
                            array_push($ret,$value);
                            $data['openid'] = $openid;
                            $data['status'] = 1;
                            array_push($new_arr,$data);
                            continue;
                        }else if($v['openid'] == $openid && $v['status'] == 1){
                            continue;
                        }else if($this->deep_in_array($new_arr,$openid) == false){
                            array_push($ret,$value);
                            $data['openid'] = $openid;
                            $data['status'] = 1;
                            array_push($new_arr,$data);
                            continue;
                        }
                    }
                }

            }else{
                continue;
            }
            if(!empty($new_arr)){
                pdo_update('choujiang_scene_dialogue',array('broadcast_status'=>serialize($new_arr)),array('id'=>$value['id']));
            }
        }
        if(!empty($ret)){
            foreach($ret as $key => $value){
                if($value['openid'] == $goods_openid){
                    $ret[$key]['myself_status'] = 1;
                }else{
                    $ret[$key]['myself_status'] = 2;
                }
            }
        }
        $now_member = pdo_fetchall('SELECT avatar FROM ' . tablename('choujiang_scene') . " where uniacid=:uniacid and goods_id = :id and status = 0 and join_status = 1", array(":id"=>$id,":uniacid" => $uniacid));
        $res['member'] = $MemberRecord;
        $res['content'] = $ret;
        $res['now_member'] = $now_member;
        return $this->result(0,'success',$res);
    }

    // 输入核销码核销
    public function doPageShouHexiao(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_REQUEST['id']; 
        $hexiao = $_REQUEST['hexiao']; 
        $openid = $_REQUEST['openid']; 
        $list = pdo_fetch('SELECT * FROM ' . tablename('choujiang_exchange') . " where uniacid=:uniacid and goods_id = :id and openid = :openid", array(":id"=>$id,":uniacid" => $uniacid,":openid"=>$openid));
        if(empty($list)){
            $status = -2;
        }else{
            if($list['status'] == 1){
                $state = -1;// $str = '该订单已核销';
            }else{
                $base = pdo_fetch("SELECT * from".tablename('choujiang_base')."WHERE uniacid = :uniacid",array(':uniacid'=>$uniacid));
                $hexiaoma = $base['hexiaoma'];
                if($hexiaoma == $hexiao){
                    $str = pdo_update('choujiang_exchange',array('status'=>1),array('id'=>$list['id']));  //进行核销
                    if($str){
                        $status = 1;
                    }else{
                        $status = -1;
                    }
                }else{
                    $status = -3; //核销码不正确，核销失败
                }
            }
        }
        return $this->result(0,'success',$status);
    }

    function deep_in_array($arr,$openid) {   
        $length = count($arr);      
        for ($i=0; $i < $length; $i++) { 
 
            if($arr[$i]['openid'] == $openid){
                return true;
            }
        }
        return false;
    }
    // 现场开奖  所有领取都在现场
    public function doPageSceneOpen(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_REQUEST['id'];
        $goods = pdo_fetch('SELECT * FROM ' . tablename('choujiang_goods') . " where uniacid=:uniacid and id = :id", array(":id"=>$id,":uniacid" => $uniacid));
        if($goods['status'] == 0){
            if($goods['The_winning'] == 1){    //指定中奖人
                $openid_arr = $goods['openid_arr'];
                $winning_openid = unserialize($openid_arr); 
                $str = pdo_update('choujiang_goods', array('status'=>1,'send_time'=>time()),array('id'=>$id));
                foreach($winning_openid as $key => $value){
                    $record = pdo_fetch('SELECT * FROM ' . tablename('choujiang_scene') . " where uniacid=:uniacid and goods_id = :id and status = 0 and join_status = 1 and openid = :openid", array(":id"=>$id,":uniacid" => $uniacid,':openid'=>$value));
                    pdo_update('choujiang_scene', array('status'=>1),array('id'=>$record['id']));
                }
            }else{   //未指定中奖人
                $join = pdo_fetchall('SELECT * FROM ' . tablename('choujiang_scene') . " where uniacid=:uniacid and goods_id = :id and status = 0 and join_status = 1", array(":id"=>$id,":uniacid" => $uniacid));
                $base = pdo_fetch("SELECT * from".tablename('choujiang_base')."WHERE uniacid = :uniacid",array(':uniacid'=>$uniacid));
                foreach($join as $key => $value){
                    pdo_update('choujiang_scene', array('finish_time'=>time()),array('id'=>$value['id']));
                }
                $winning_num = $base['winning_num']; 
                if($winning_num > 0){   //限制中奖次数
                    foreach ($join as $key => $value) {
                        $user = pdo_fetch("SELECT * from".tablename('choujiang_user')."WHERE uniacid = :uniacid and openid = :openid",array(':uniacid'=>$uniacid,':openid'=>$value['openid']));
                        $send_time = $user['send_time'];
                        $finish_time = strtotime("+1 months",$send_time);
                        if($finish_time <= time()){
                            pdo_update('choujiang_user', array('send_time'=>time(),'winning_num'=>$winning_num),array('id'=>$user['id']));
                        }else if($send_time == '' || $send_time == null){
                            pdo_update('choujiang_user', array('send_time'=>time(),'winning_num'=>$winning_num),array('id'=>$user['id']));
                        }else{
                            if($user['winning_num'] <= 0){
                                unset($join[$key]);
                            }
                        }
                       
                    }
                }
                $str = pdo_update('choujiang_goods', array('status'=>1,'send_time'=>time()),array('id'=>$id));
                $max_num = $goods['goods_num'];
                $join_count = count($join);
                if($join_count < $max_num){
                    $max_num = $join_count;
                }
                $arr = array();
                if($max_num == 1){
                    $arr[] = array_rand($join,$max_num); 
                }else{
                    $arr = array_rand($join,$max_num); 
                }
                foreach($arr as $k => $val){
                    $obtain[]=$join[$val];
                }
                        
                foreach($obtain as $key => $value){
                    $users = pdo_fetch("SELECT * from".tablename('choujiang_user')."WHERE uniacid = :uniacid and openid = :openid",array(':uniacid'=>$uniacid,':openid'=>$value['openid']));
                    if($base['winning_num'] == 0){
                        $user_winning_num = $users['winning_num'];
                    }else{
                        $user_winning_num = $users['winning_num'] - 1;
                    }
                    pdo_update('choujiang_user', array('winning_num'=>$user_winning_num),array('id'=>$users['id']));
                    pdo_update('choujiang_scene', array('status'=>1),array('id'=>$value['id']));
                }
            }
            if(!empty($str)){
                $res['status'] = 1;
                $res['goods_status'] = $goods['goods_status'];
               
                $this->doPageInform($id);
            }else{
                $res['status'] = -1;
            }
                    
        }else{
            $res['status'] = -1;
        }
        return $this->result(0,'success',$res);
       
    }
// 现场中奖人员
    public function doPageSceneWinners(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_REQUEST['id'];
        $ret = pdo_fetchall('SELECT avatar,nickname FROM ' . tablename('choujiang_scene') . " where uniacid=:uniacid and goods_id = :id and status = 1", array(":id"=>$id,":uniacid" => $uniacid));
        $join_num = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('choujiang_scene')." where uniacid=:uniacid  and goods_id = :id and join_status = 1",array(':uniacid'=>$uniacid,':id'=>$id));
        $str['ret'] = $ret;
        $str['join_num'] = $join_num;
        return $this->result(0,'success',$str);
    }

    // 皮一下标题列表
    public function doPageFurList(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $current = $_REQUEST['current'];
        $ret = pdo_fetchall('SELECT * FROM ' . tablename('choujiang_fur') . " where uniacid=:uniacid and category = :current", array(":uniacid" => $uniacid,":current" => $current));
        return $this->result(0,'success',$ret);
    }


// 查看海报
public function doPageHaiBao(){
    global $_W, $_GPC;
    $uniacid = $_W['uniacid'];
    $id = $_REQUEST['id'];
    // $id = 864;
    $ret = pdo_fetch('SELECT * FROM ' . tablename('choujiang_verification') . " where uniacid=:uniacid and goods_id = :id", array(":id"=>$id,":uniacid" => $uniacid));
    $poster = $ret['new_poster'];
    if(empty($poster)){
        $poster = $this->GeneratePoster($id);
        // var_dump($poster);
        pdo_update('choujiang_verification',array('new_poster'=>$poster),array('id'=>$ret['id']));
    }
    return $this->result(0,'success',$poster);
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

 // 前台识别图片代码
    function HomeImageSb($img,$form_address){
        global $_GPC,$_W;
        if($_W['setting']['remote']['type'] != 0 && $form_address == 1){   //当开启远程存储
            $in = 'https';
            $url = $_W['setting']['site']["url"];
            $sub = substr($url,0,strpos($url, ':'));
            if($sub == $in){
                $new_url = $url;
            }else{
                $new_url = $sub.'s:'.substr($url,strpos($url,':')+1);
            }
            $img = $new_url.'/attachment/'.$img;
        }else{
            $img = $_W['attachurl'].$img;
        }
        return $img;
    }


    //查询礼物说首页数据
    public function doPageLws_base(){
        global $_GPC,$_W;
        $uniacid = $_W['uniacid'];
        $base = pdo_fetch("SELECT * from".tablename('choujiang_lws_base')."where uniacid = '{$uniacid}' ");
        if(!empty($base['video'])){
            $base['video'] = $_W['attachurl'].$base['video'];
        }
        $base['slide'] = unserialize($base['slide']);
        $num = count($base['slide']);
        for($i = 0; $i < $num; $i++) {
            $base['slide'][$i] = $_W['attachurl'] . $base['slide'][$i];
        } 
        return $this->result(0,'success',$base);
    }

    //查询礼物说商品分类及商品
    public function doPageLws_goods_fl(){
        global $_GPC,$_W;
        $uniacid = $_W['uniacid'];
        $fenlei = pdo_fetchall("SELECT * from".tablename('choujiang_lws_goods_fl')."where uniacid ='{$uniacid}' ");
        foreach ($fenlei as $key => $value) {
            $goods = pdo_fetchall("SELECT * FROM".tablename('choujiang_lws_goods')."where uniacid ='{$uniacid}' and fl_id ='{$value['id']}' and status = 0 and kucun > 0 order by num desc ");
            foreach ($goods as &$values) {
                $values['thumb'] = $_W['attachurl'].$values['thumb'];
            }
            $fenlei[$key]['goods'] = $goods;
        }
        return $this->result(0,'success',$fenlei);
    }

    //查询礼物说商品
    public function doPageLws_Goods(){
        global $_W,$_GPC;
        $uniacid = $_W['uniacid'];
        $fl_id = $_GPC['fl_id'];
        $lws_money = $_GPC['lws_money'];
        //var_dump($fl_id,$lws_money);
        if($lws_money == 'null' || $lws_money == 'undefined'){
            $content = " and fl_id = '{$fl_id}' ";
        }else if($lws_money == '1000+'){
            $content = "and fl_id = '{$fl_id}' and price >=1000 ";
        }else{
            $start = intval(substr($lws_money, 0, strpos($lws_money, '-')));
            $end = intval(substr($lws_money, strpos($lws_money, '-')+1));
            //var_dump($start,$end);
            $content = "and fl_id = '{$fl_id}' and price > '{$start}' and price <= '{$end}' ";
        }
        //var_dump($content);
        $list = pdo_fetchall("SELECT * FROM".tablename('choujiang_lws_goods')."where uniacid = '{$uniacid}' $content ");
        foreach ($list as &$value) {
            $value['thumb'] = $_W['attachurl'].$value['thumb'];
        }
        return $this->result(0,'success',$list);
    }

    //查询礼物说首页楼层及商品
    public function doPageLws_sy_goods(){
        global $_W,$_GPC;
        $uniacid = $_W['uniacid'];
        $louceng = pdo_fetchall("SELECT * FROM".tablename('choujiang_lws_sylc')."where uniacid ='{$uniacid}' ");
        foreach ($louceng as $key => $value) {
            $goods = pdo_fetchall("SELECT * FROM".tablename('choujiang_lws_goods')."where uniacid ='{$uniacid}' and lc_id = '{$value['id']}' and status = 0 and kucun > 0 order by num desc ");
            foreach ($goods as &$values) {
                $values['thumb'] = $_W['attachurl'].$values['thumb'];
            }
            $louceng[$key]['goods'] = $goods;
            $louceng[$key]['thumb'] = $_W['attachurl'].$value['thumb'];
        }
        return $this->result(0,'success',$louceng);
    }

    public function doPageLws_goods_sy(){
        global $_W,$_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_GPC['id'];
        $list  = pdo_fetchall("SELECT * FROM".tablename('choujiang_lws_goods')."where uniacid ='{$uniacid}' and lc_id = '{$id}' and status = 0 and kucun >0 order by num desc ");
        foreach ($list as &$value) {
            $value['thumb'] = $_W['attachurl'] .$value['thumb'];
        }
        return $this->result(0,'success',$list);
    }

    //查询礼物说商品详情
    public function doPageLws_goodsxq(){
        global $_W,$_GPC;
        $uniacid = $_W['uniacid'];
        $id = $_GPC['id'];
        $goods = pdo_fetch("SELECT * from".tablename('choujiang_lws_goods')."where uniacid ='{$uniacid}' and id = '{$id}' ");
        $goods['thumb'] = $_W['attachurl'].$goods['thumb'];
        $goods['slide'] = unserialize($goods['slide']);
        $num = count($goods['slide']);
        for($i = 0; $i < $num; $i++) {
            $goods['slide'][$i] = $_W['attachurl'] . $goods['slide'][$i];
        } 
        return $this->result(0,'success',$goods);
    }

    //支付成功加入订单表
    public function doPageLws_Order(){
        global $_GPC,$_W;
        $uniacid = $_W['uniacid'];
        //$orderid =str_shuffle(time() . rand(111111, 999999));//随机数，订单编号
        $data = array(
            'uniacid' => $uniacid,
            'openid' => $_GPC['openid'],
            'goods_id' => $_GPC['id'],
            'price' => $_GPC['price'],
            'title' => $_GPC['title'],
            'thumb' => $_GPC['thumb'],
            'totalPrice' => $_GPC['totalPrice'],
            'sltj' => $_GPC['sltj'],
            'counts' => $_GPC['counts'],
            'kjPeonum' => $_GPC['kjPeonum'],
            'greetings' => $_GPC['greetings'],
            'time' => time(),
            'orderid'=>$_GPC['ordersn'],
            'formid'=>$_GPC['formId'],
        );
        $res = pdo_insert("choujiang_lws_order",$data);
        if($res){//减少商品库存
            //查询商品库存
            $goods = pdo_fetch("SELECT * FROM".tablename('choujiang_lws_goods')."where uniacid = :uniacid and id = :id",array(':uniacid'=>$uniacid,':id'=>$_GPC['id']));
            pdo_update('choujiang_lws_goods',array('kucun'=>$goods['kucun']-$_GPC['counts']),array('id'=>$_GPC['id']));
        }
    }

    //根据订单id查询订单详情
    public function doPageLws_orderxq(){
        global $_GPC,$_W;
        $uniacid = $_W['uniacid'];
        $orderid = $_GPC['orderid'];
        $order = pdo_fetch("SELECT a.*,b.avatar,b.nickname FROM".tablename('choujiang_lws_order')." as a left join ".tablename('choujiang_user'). " as b on a.openid = b.openid  where a.uniacid ='{$uniacid}' and a.orderid = '{$orderid}' ");
        $order['time'] = date('Y-m-d H:i:s',$order['time']);
        return $this->result(0,'success',$order);
    }

    //查询订单收礼详情
    public function doPageLws_ordershouli(){
        global $_W,$_GPC;
        $uniacid = $_W['uniacid'];
        $orderid = $_GPC['orderid'];
        $openid = $_GPC['openid'];
        $list = pdo_fetch("SELECT * FROM ".tablename('choujiang_lws_order_liwu')." where uniacid ='{$uniacid}' and orderid = '{$orderid}' and openid = '{$openid}' ");
        $list['time'] = date('Y-m-d H:i:s',$list['time']);
        $list['fh_time'] = date('Y-m-d H:i:s',$list['fh_time']);
        return $this->result(0,'success',$list);
    }

    //收礼物
    public function doPageLws_order_liwu(){
        global $_W,$_GPC;
        $uniacid = $_W['uniacid'];
        $orderid = $_GPC['ordersn'];
        $openid = $_GPC['openid'];
        //根据订单id查询订单详情
        $order = pdo_fetch("SELECT * FROM".tablename('choujiang_lws_order')."where uniacid ='{$uniacid}' and orderid = '{$orderid}' ");
        if($order['status'] < 2){//未领取
            $data = array(
                'uniacid' =>$uniacid,
                'openid'=>$openid,
                'nickName'=>$_GPC['nickName'],
                'avatarUrl'=>$_GPC['avatarUrl'],
                'orderid'=>$orderid,
                'c_openid' => $_GPC['c_openid'],
                'c_nickName' => $_GPC['c_nickName'],
                'c_avatarUrl' => $_GPC['c_avatarUrl'],
                'formid'=>$_GPC['formID'],
                'time' =>time(),
            );
            //查询该用户是否领取
            $order_liwu = pdo_fetch('SELECT * FROM'.tablename('choujiang_lws_order_liwu')."where uniacid = :uniacid and orderid = :orderid and openid = :openid",array(':uniacid'=>$uniacid,':orderid'=>$orderid,':openid'=>$openid));
            if(empty($order_liwu)){
                pdo_insert("choujiang_lws_order_liwu",$data);
                pdo_update("choujiang_lws_order",array('status'=>1),array('orderid'=>$orderid));
            }
        }
        if($order['sltj'] == '满人送礼'){
            //查询收礼人数
            $num = pdo_fetchcolumn("SELECT count(*) FROM".tablename('choujiang_lws_order_liwu')."where uniacid = '{$uniacid}' and orderid = '{$orderid}' ");
            var_dump($num);
            if($num == $order['kjPeonum']){//满足开奖条件
                //查询参与客户
                $nums = $order['counts'];
                //随机查询中奖数据
                $shouli = pdo_fetchall("SELECT * FROM".tablename('choujiang_lws_order_liwu')."where uniacid ='{$uniacid}' and orderid = '{$orderid}' order by rand() LIMIT {$nums} ");
                foreach ($shouli as $key => $value) {
                    //查询判断该礼物是否开奖
                    $arr = pdo_fetch("SELECT * FROM".tablename('choujiang_lws_order')."where uniacid = '{$uniacid}' and orderid = '{$orderid}' ");
                    if($arr['types'] == 0){//未开奖
                        pdo_update("choujiang_lws_order_liwu",array('type'=>1),array('id'=>$value['id']));
                    }
                }
                //开奖完毕修改状态
                pdo_update("choujiang_lws_order",array('types'=>1),array('orderid'=>$orderid));
                //调用开奖结果模板通知
                $this->doPageLws_kaijiang_ok($orderid);
            }
        }
        
    }

    //查询判断该用户是否中奖
    public function doPageLws_My_zjjl(){
        global $_GPC,$_W;
        $uniacid = $_W['uniacid'];
        $openid = $_GPC['openid'];
        $orderid = $_GPC['ordersn'];
        $list = pdo_fetch("SELECT * FROM".tablename('choujiang_lws_order_liwu')."where uniacid = '{$uniacid}' and openid = '{$openid}' and orderid = '{$orderid}' ");
        return $this->result(0,'success',$list);
    }

    //查询收礼物信息
    public function doPageLws_order_shouli(){
        global $_GPC,$_W;
        $uniacid = $_W['uniacid'];
        $orderid = $_GPC['orderid'];
        $order = pdo_fetchall("SELECT * FROM".tablename('choujiang_lws_order_liwu')."where uniacid = '{$uniacid}' and orderid = '{$orderid}' ");
        foreach ($order as &$value) {
            $value['time'] = date("Y-m-d H:i:s",$value['time']);
        }
        return $this->result(0,'success',$order);
    }

    //填写收货地址领取礼物
    public function doPageLws_order_address(){
        global $_GPC,$_W;
        $uniacid = $_W['uniacid'];
        $openid = $_GPC['openid'];
        $orderid = $_GPC['orderid'];
        $data = array(
            'user_name'=>$_GPC['user_name'],
            'user_tel'=>$_GPC['user_tel'],
            'user_address'=>$_GPC['user_address'],
            'status'=>1,
            'fh_formid'=>$_GPC['fh_formID'],
        );
        //var_dump($data);
        //查询订单详情
        $order = pdo_fetch("SELECT * FROM".tablename('choujiang_lws_order')."where uniacid ='{$uniacid}' and orderid = '{$orderid}' ");
        if($order['sltj'] == '直接送礼'){
            if($order['status']<2){//未领取
                pdo_update('choujiang_lws_order_liwu',$data,array('uniacid'=>$uniacid,'openid'=>$openid,'orderid'=>$orderid));
                $res = pdo_update('choujiang_lws_order',array('status'=>2),array('uniacid'=>$uniacid,'orderid'=>$orderid));
                if($res){
                    //调用礼品领取成功通知
                    $this->doPageLws_lingqu_ok($orderid);
                }
            }
        }else if($order['sltj'] == '满人送礼'){
            if($order['status']<2){//未领取
                pdo_update('choujiang_lws_order_liwu',$data,array('uniacid'=>$uniacid,'openid'=>$openid,'orderid'=>$orderid));
                //pdo_update('choujiang_lws_order',array('status'=>2),array('uniacid'=>$uniacid,'orderid'=>$orderid));
            }
            //查询个数
            $sl_num = pdo_fetchcolumn("SELECT count(*) FROM".tablename('choujiang_lws_order_liwu')."where uniacid = '{$uniacid}' and orderid = '{$orderid}' and status = 1 and type = 1 ");
            if($sl_num == $order['counts']){//领取完毕
                pdo_update("choujiang_lws_order",array('status'=>2),array('uniacid'=>$uniacid,'orderid'=>$orderid));
            }
            //var_dump($sl_num);
        }
    }


    //礼物说搜索功能
    public function doPageLws_search(){
        global $_GPC,$_W;
        $uniacid = $_W['uniacid'];
        $title = $_GPC['title'];
        $list = pdo_fetchall("SELECT * FROM".tablename('choujiang_lws_goods')."where uniacid = '{$uniacid}' and title like '%{$title}%' order by num desc ");
        foreach ($list as &$value) {
            $value['thumb'] = $_W['attachurl'].$value['thumb'];
        }
        return $this->result(0,'success',$list);
    }

    //查询我送出的礼物
    public function doPageLws_Myorder(){
        global $_GPC,$_W;
        $uniacid = $_W['uniacid'];
        $openid = $_GPC['openid'];
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('choujiang_lws_order') . " WHERE uniacid = :uniacid and openid = :openid ", array(':uniacid' => $uniacid,':openid'=>$openid));
        $pindex = max(1, intval($_GPC['page'])); 
        $pagesize = 10;
        $pager = pagination($total,$pindex,$pagesize);
        $p = ($pindex-1) * $pagesize;
        $order = pdo_fetchall("SELECT * FROM".tablename('choujiang_lws_order')."where uniacid =:uniacid and openid = :openid order by id desc limit ".$p.",".$pagesize,array(':uniacid'=>$uniacid,':openid'=>$openid));
        foreach ($order as &$value) {
            $value['time'] = date('Y-m-d H:i:s',$value['time']);
        }
        return $this->result(0,'success',$order);
    }

    //查询我收到的礼物
    public function doPageLws_Mysd_liwu(){
        global $_GPC,$_W;
        $uniacid = $_W['uniacid'];
        $openid = $_GPC['openid'];
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('choujiang_lws_order_liwu') . " WHERE uniacid = :uniacid and openid = :openid and status = 1 ", array(':uniacid' => $uniacid,':openid'=>$openid));
        $pindex = max(1, intval($_GPC['page'])); 
        $pagesize = 10;
        $pager = pagination($total,$pindex,$pagesize);
        $p = ($pindex-1) * $pagesize;
        $order = pdo_fetchall("SELECT * FROM".tablename('choujiang_lws_order_liwu')." as a left join ".tablename('choujiang_lws_order')."as b on a.orderid = b.orderid where a.uniacid =:uniacid and a.openid = :openid and a.status != 0 order by a.id desc limit ".$p.",".$pagesize,array(':uniacid'=>$uniacid,':openid'=>$openid));
        foreach ($order as &$value) {
            $value['time'] = date('Y-m-d H:i:s',$value['time']);
        }
        return $this->result(0,'success',$order);
    }

    //查询我参与的
    public function doPageLws_Mycanyu_liwu(){
        global $_GPC,$_W;
        $uniacid = $_W['uniacid'];
        $openid = $_GPC['openid'];
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('choujiang_lws_order_liwu') . " WHERE uniacid = :uniacid and openid = :openid and status = 1 ", array(':uniacid' => $uniacid,':openid'=>$openid));
        $pindex = max(1, intval($_GPC['page'])); 
        $pagesize = 10;
        $pager = pagination($total,$pindex,$pagesize);
        $p = ($pindex-1) * $pagesize;
        $order = pdo_fetchall("SELECT * FROM".tablename('choujiang_lws_order_liwu')." as a left join ".tablename('choujiang_lws_order')."as b on a.orderid = b.orderid where a.uniacid =:uniacid and a.openid = :openid order by a.id desc limit ".$p.",".$pagesize,array(':uniacid'=>$uniacid,':openid'=>$openid));
        foreach ($order as &$value) {
            $value['time'] = date('Y-m-d H:i:s',$value['time']);
        }
        return $this->result(0,'success',$order);
    }


    //查询抽奖人数及中奖人数
    public function doPageNums(){
        global $_GPC,$_W;
        $uniacid = $_W['uniacid'];
        $cj_num = pdo_fetchcolumn("SELECT count(*) FROM".tablename('choujiang_record')."where uniacid ='{$uniacid}' ");
        $zj_num = pdo_fetchcolumn("SELECT count(*) FROM".tablename('choujiang_record')."where uniacid = '{$uniacid}' and status = 1 ");
        $data = array('cj_num'=>$cj_num,'zj_num'=>$zj_num);
        return $this->result(0,'success',$data);
    }

    //查询礼物说客服信息
    public function doPageLws_kefu(){
        global $_W,$_GPC;
        $uniacid = $_W['uniacid'];
        $list = pdo_fetch("SELECT * FROM".tablename('choujiang_lws_kf')."where uniacid = '{$uniacid}' ");
        return $this->result(0,'success',$list);
    }




    //礼物领取成功通知
    public function doPageLws_kaijiang_ok($orderid){
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $base = pdo_fetch('SELECT * FROM ' . tablename('choujiang_base') ."where `uniacid`='{$uniacid}' ");
        $appid = $base['appid'];
        $appsecret = $base['appsecret'];
        $template_id = $base['template_id4'];
        $tokenUrl="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
        $getArr=array();
        $tokenArr=json_decode($this->send_post($tokenUrl,$getArr,"GET"));
        $access_token=$tokenArr->access_token;
        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token ;
        //查询订单详情
        $order = pdo_fetch("SELECT * FROM ".tablename("choujiang_lws_order")."where uniacid ='{$uniacid}' and orderid = '{$orderid}' ");
        $order_shouli = pdo_fetchall("SELECT * FROM".tablename('choujiang_lws_order_liwu')."where uniacid = '{$uniacid}' and orderid = '{$orderid}' and type = 1 ");
        foreach($order_shouli as $key => $value){
            $dd['form_id'] = $value['formid'];
            $dd['touser'] = $value['openid'];
            $content = array(
                "keyword1"=>array(
                "value"=> $value['nickName'].',您参与的抽奖结果已开奖了，点击看看幸运儿是不是你',
                "color"=>"#4a4a4a"
                ),
                "keyword2"=>array(
                    "value"=>'如果你幸运中奖，请及时填写奖品领取信息',
                    "color"=>"#9b9b9b"
                ),
                  
            );
            $dd['template_id']=$template_id;
            $dd['page']='choujiang_page/lws_win/lws_win?ordersn='.$orderid;  //点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,该字段不填则模板无跳转。
            // $dd['page']='/choujiang_page/fuli_xq/fuli_xq?id='.$id;  //点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,该字段不填则模板无跳转。
            $dd['data']=$content;                        //模板内容，不填则下发空模板
            $dd['color']='';                        //模板内容字体的颜色，不填默认黑色
            $dd['emphasis_keyword']='';    //模板需要放大的关键词，不填则默认无放大
            $result = $this->https_curl_json($url,$dd,'json');
        }
        
       return $result;
    }


    //礼物领取成功通知
    public function doPageLws_lingqu_ok($orderid){
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $base = pdo_fetch('SELECT * FROM ' . tablename('choujiang_base') ."where `uniacid`='{$uniacid}' ");
        $appid = $base['appid'];
        $appsecret = $base['appsecret'];
        $template_id = $base['template_id3'];
        $tokenUrl="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
        $getArr=array();
        $tokenArr=json_decode($this->send_post($tokenUrl,$getArr,"GET"));
        $access_token=$tokenArr->access_token;
        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token ;
        //查询订单详情
        $order = pdo_fetch("SELECT * FROM ".tablename("choujiang_lws_order")."where uniacid ='{$uniacid}' and orderid = '{$orderid}' ");
        $order_shouli = pdo_fetch("SELECT * FROM".tablename('choujiang_lws_order_liwu')."where uniacid = '{$uniacid}' and orderid = '{$orderid}' and status = 1 ");
        $value = array(
                    "keyword1"=>array(
                    "value"=> $order['title'],
                    "color"=>"#4a4a4a"
                    ),
                    "keyword2"=>array(
                        "value"=>$order_shouli['nickName'],//$yuyue['qd_jl'],
                        "color"=>"#9b9b9b"
                    ),
                    "keyword3"=>array(
                        "value"=>date('Y-m-d H:i',time()),
                        "color"=>"#9b9b9b"
                    ),
                    "keyword4"=>array(
                        "value"=>'您送出的礼物已成功被对方领取',
                        "color"=>"#9b9b9b"
                    ), 
                );
        $dd = array();
        $dd['touser']=$order['openid'];//接收人openid
        $dd['template_id']=$template_id;
        $dd['page']="choujiang_page/lws_win/lws_win?ordersn=".$orderid;  //点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,该字段不填则模板无跳转。
        $dd['form_id']=$order['formid'];
        $dd['data']=$value;                        //模板内容，不填则下发空模板
        $dd['color']='';                        //模板内容字体的颜色，不填默认黑色
        $dd['emphasis_keyword']='';    //模板需要放大的关键词，不填则默认无放大
        $result = $this->https_curl_json($url,$dd,'json');
        if($result){
            echo json_encode(array('state'=>5,'msg'=>$result));
        }else{
            echo json_encode(array('state'=>5,'msg'=>$result));
        }

       return $result;
    }


    // 查看礼物说海报
    public function doPageHaiBao_lws(){
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $orderid = $_REQUEST['orderid'];
        //var_dump($orderid);
        // $id = 864;
        $ret = pdo_fetch('SELECT * FROM ' . tablename('choujiang_lws_order') . " where uniacid=:uniacid and orderid = :orderid", array(":orderid"=>$orderid,":uniacid" => $uniacid));
        $poster = $ret['new_poster'];
        if(empty($poster)){
            $poster = $this->GeneratePoster_lws($orderid);
            // var_dump($poster);
            pdo_update('choujiang_lws_order',array('new_poster'=>$poster),array('orderid'=>$orderid));
        }
        return $this->result(0,'success',$poster);
    }

    //生成礼物说订单二维码
    public function Lws_orderewmadd($orderid){
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];

        $result = pdo_fetch('SELECT * FROM ' . tablename('choujiang_base') . " where uniacid=:uniacid", array(":uniacid" => $uniacid));
        $APPID = $result['appid'];
        $SECRET = $result['appsecret'];
        $tokenUrl="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$APPID}&secret={$SECRET}";
        $getArr=array();
        $tokenArr=json_decode($this->send_post($tokenUrl,$getArr,"GET"));
        $access_token=$tokenArr->access_token;
        $noncestr = "choujiang_page/lws_win/lws_win?ordersn={$orderid}";
        $width=430;
        $post_data='{"path":"'.$noncestr.'","width":'.$width.'}';
        $url="https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=".$access_token;
        $result=$this->api_notice_increment($url,$post_data); 
        $image_name = md5(uniqid(rand())).".jpg";
        $filepath = "../attachment/{$image_name}";   
        $file_put = file_put_contents($filepath, $result);
        $ewm = $_W['attachurl'].$filepath; 
        if($file_put){
           pdo_update("choujiang_lws_order", array('ticketurl'=>$ewm),array('orderid' => $orderid,'uniacid' => $uniacid));
        }
        
    }


    // 生成礼物说海报
function GeneratePoster_lws($orderid){
    global $_W,$_GPC;
    $uniacid = $_W['uniacid'];
    $ticketurl = $this->Lws_orderewmadd($orderid);//生成二维码
    //查询订单详情
    $orderxq = pdo_fetch("SELECT * FROM".tablename('choujiang_lws_order')."where uniacid ='{$uniacid}' and orderid = '{$orderid}' ");

    $info['goods_name'] = $orderxq['title'];
    $info['goods_num'] = $orderxq['counts'];
    $info['goods_icon'] = $orderxq['thumb'];
    $info['goods_set'] = $orderxq['sltj'];   
    $member = pdo_fetch('SELECT * FROM ' . tablename('choujiang_user') . " where uniacid=:uniacid and openid = :openid", array(":openid"=>$orderxq['openid'],":uniacid" => $uniacid));
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

    $ticketurl = $orderxq['ticketurl']; //二维码
    $img = $this->qrcode_lws($ticketurl,$background,$avatarlocal,$avatarlocal2,$fontfile,$info,$test);

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

function qrcode_lws($ticketurl,$background,$avatarlocal,$avatarlocal2,$fontfile,$info,$user){
    $nickanme = $this->autowrap(20,0,$fontfile,$info['nickname'],350);

    $title = $info['goods_name']." x".$info['goods_num'];

    $smoke = $info['goods_set'];
    $tishi = '(长按识别二维码)';
    $faqi = '送你一份礼物，赶紧去领取吧！';

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


}
