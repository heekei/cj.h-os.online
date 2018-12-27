<?php
pdo_query("CREATE TABLE IF NOT EXISTS `ims_choujiang_advertising` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`title` varchar(100) DEFAULT NULL,
`image` varchar(255) DEFAULT NULL,
`to_index` int(11) DEFAULT NULL DEFAULT '0',
`appid` varchar(100) DEFAULT NULL,
`url` varchar(255) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_base` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`appid` varchar(255) DEFAULT NULL COMMENT '跳转小程序appid',
`appsecret` varchar(255) DEFAULT NULL COMMENT '密钥',
`template_id` varchar(255) DEFAULT NULL COMMENT '模板通知',
`appkey` varchar(255) DEFAULT NULL COMMENT '支付密钥',
`mch_id` varchar(255) DEFAULT NULL COMMENT '商户id',
`join_num` int(11) DEFAULT NULL DEFAULT '0' COMMENT '免费发起次数',
`smoke_num` int(11) DEFAULT NULL DEFAULT '0' COMMENT '免费抽奖次数',
`winning_num` int(11) DEFAULT NULL DEFAULT '0' COMMENT '每人每月中奖次数',
`poundage` int(11) DEFAULT NULL DEFAULT '0' COMMENT '红包手续费',
`xcx_price` decimal(10,2) DEFAULT NULL DEFAULT '0.00' COMMENT '小程序跳转 appid 付费价格',
`share_num` int(11) DEFAULT NULL DEFAULT '0' COMMENT '每日分享最多获得抽奖次数',
`cheat_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '0-不开启  1-开启',
`upfile` varchar(255) DEFAULT NULL,
`keypem` varchar(255) DEFAULT NULL,
`envelope_draw` int(11) DEFAULT NULL DEFAULT '0' COMMENT '0未开启 1已开启',
`index_title` varchar(100) DEFAULT NULL COMMENT '首页标题',
`audit_set` int(11) DEFAULT NULL DEFAULT '0' COMMENT '后台审核按钮',
`sponsor_status` int(11) DEFAULT NULL DEFAULT '0',
`template_id2` varchar(255) DEFAULT NULL COMMENT '商品发布成功通知',
`latest_time` int(11) DEFAULT NULL DEFAULT '0',
`share_title` varchar(255) DEFAULT NULL,
`takes_time` int(11) DEFAULT NULL DEFAULT '0' COMMENT '用户自取限时 小时',
`hexiaoma` varchar(50) DEFAULT NULL COMMENT '全局通用核销码',
`template_id3` varchar(255) DEFAULT NULL,
`template_id4` varchar(255) DEFAULT NULL,
`template_id5` varchar(255) DEFAULT NULL,
`ad` varchar(255) DEFAULT NULL COMMENT '流量主id',
`zzs_text` text NOT NULL COMMENT '赞助商内容',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_cheat` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`title` varchar(255) DEFAULT NULL,
`icon` varchar(1000) DEFAULT NULL,
`content` varchar(6666) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_cheat_nav` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`title` varchar(255) DEFAULT NULL,
`icon` varchar(255) DEFAULT NULL,
`appid` varchar(100) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_earnings` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`openid` varchar(100) DEFAULT NULL,
`money` decimal(10,2) DEFAULT NULL,
`create_time` varchar(100) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_exchange` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`goods_id` int(11) DEFAULT NULL,
`openid` varchar(100) DEFAULT NULL,
`status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '0-未领取  1-已领取',
`create_time` varchar(100) DEFAULT NULL,
`verification` varchar(255) DEFAULT NULL,
`orders` varchar(100) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_fur` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`title` varchar(255) DEFAULT NULL,
`status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1-使用中',
`category` int(11) DEFAULT NULL DEFAULT '0' COMMENT '0-实物 1-电子卡  2-红包',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_goods` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`goods_name` varchar(100) DEFAULT NULL,
`goods_num` int(11) DEFAULT NULL,
`smoke_time` varchar(100) DEFAULT NULL COMMENT '自动开奖时间',
`smoke_num` int(11) DEFAULT NULL COMMENT '开奖人数',
`smoke_set` int(11) DEFAULT NULL COMMENT '开奖条件(0、按时间  1、按人数  2、手动 3、现场)',
`goods_icon` text DEFAULT NULL COMMENT '商品图片',
`goods_sponsorship` varchar(100) DEFAULT NULL COMMENT '奖品赞助商',
`status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '奖品状态(1、已结束   0、正在进行中)',
`goods_openid` varchar(100) DEFAULT NULL COMMENT '发起人',
`send_time` int(11) DEFAULT NULL COMMENT '开奖的时间',
`content` text DEFAULT NULL COMMENT '奖品介绍',
`sponsorship_text` varchar(255) DEFAULT NULL COMMENT '赞助商标语',
`is_del` int(11) DEFAULT NULL DEFAULT '1' COMMENT '1 未删除 -1 已删除',
`The_winning` int(11) DEFAULT NULL DEFAULT '0' COMMENT '指定中奖者状态 0否，1是',
`goods_winning` text DEFAULT NULL COMMENT 'z指定的中奖者openid',
`sponsorship_appid` varchar(255) DEFAULT NULL,
`openid_arr` text DEFAULT NULL,
`audit_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '审核状态 0-审核中 1通过 -1失败',
`mouth_command` varchar(255) DEFAULT NULL COMMENT '口令',
`join_conditions` int(11) DEFAULT NULL DEFAULT '0' COMMENT '是否需要付费参与 0 没有  1 付费 2口令',
`price` decimal(10,2) DEFAULT NULL DEFAULT '0.00' COMMENT '付费参与价格',
`red_envelope` decimal(10,2) DEFAULT NULL COMMENT '红包金额',
`card_info` text DEFAULT NULL COMMENT '卡号',
`goods_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '0 实物 1红包 2电子卡',
`goods_images` text DEFAULT NULL COMMENT '奖品详情图',
`sponsorship_content` varchar(6666) DEFAULT NULL COMMENT '赞助商介绍',
`sponsorship_url` varchar(150) DEFAULT NULL COMMENT '跳转小程序路径',
`to_index` int(11) DEFAULT NULL DEFAULT '0',
`business_address` varchar(255) DEFAULT NULL COMMENT '商家地址',
`freight` decimal(10,2) DEFAULT NULL DEFAULT '0.00' COMMENT '运费',
`get_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '领取方式 0-无 1-店家配送  2-到店领取',
`YouSong` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1-包邮  0-付费',
`team_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '0-不支持 1-支持组队',
`mail_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1-支持邮寄',
`ziqu_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1-支持自取',
`team_num` int(11) DEFAULT NULL DEFAULT '0',
`fur_title` int(11) DEFAULT NULL DEFAULT '0' COMMENT '皮一下标题id',
`fur_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '皮一下状态 0-普通奖品 1-皮一下状态',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_lws_base` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`name` varchar(50) NOT NULL,
`title` varchar(50) NOT NULL,
`jianjie` varchar(50) NOT NULL,
`video` varchar(255) NOT NULL,
`sysm` text NOT NULL COMMENT '使用说明',
`yhxy` text NOT NULL COMMENT '用户协议',
`slide` varchar(2555) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_lws_goods` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`num` int(11) NOT NULL,
`title` varchar(50) NOT NULL,
`jianjie` varchar(50) NOT NULL,
`thumb` varchar(255) NOT NULL,
`slide` varchar(2555) NOT NULL,
`price` decimal(10,2) NOT NULL,
`text` text NOT NULL,
`lc_id` int(11) NOT NULL,
`fl_id` int(11) NOT NULL,
`status` int(4) NOT NULL,
`kucun` int(10) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_lws_goods_fl` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`num` int(11) NOT NULL,
`title` varchar(4) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_lws_kf` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`fapiao` varchar(255) NOT NULL COMMENT '发票',
`kffw` varchar(255) NOT NULL COMMENT '客服服务',
`swhz` varchar(255) NOT NULL COMMENT '商务合作',
`qyfw` varchar(255) NOT NULL COMMENT '企业服务',
`lwlq` varchar(255) NOT NULL COMMENT '礼物领取',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_lws_order` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`openid` varchar(255) NOT NULL,
`title` varchar(255) NOT NULL,
`price` varchar(255) NOT NULL,
`thumb` varchar(255) NOT NULL,
`totalPrice` varchar(255) NOT NULL COMMENT '总价',
`sltj` varchar(50) NOT NULL COMMENT '送礼条件',
`counts` varchar(255) NOT NULL,
`kjPeonum` int(10) NOT NULL COMMENT '开奖人数',
`greetings` varchar(255) NOT NULL COMMENT '祝福语',
`orderid` varchar(255) NOT NULL,
`time` varchar(255) NOT NULL,
`goods_id` int(10) NOT NULL,
`status` int(4) NOT NULL COMMENT '状态值,0,未送出;1已送出,2已领取,3已收货',
`types` int(4) NOT NULL COMMENT '满人送礼是否开奖;0,未开奖;1,已开奖',
`new_poster` varchar(255) NOT NULL COMMENT '海报',
`ticketurl` varchar(255) NOT NULL COMMENT '海报二维码',
`formid` varchar(255) NOT NULL COMMENT '领取成功formid',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_lws_order_liwu` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`openid` varchar(255) NOT NULL COMMENT '用户openid',
`nickName` varchar(255) NOT NULL COMMENT '用户昵称',
`avatarUrl` varchar(255) NOT NULL,
`orderid` varchar(255) NOT NULL,
`c_openid` varchar(255) NOT NULL,
`c_nickName` varchar(255) NOT NULL,
`c_avatarUrl` varchar(255) NOT NULL,
`time` varchar(255) NOT NULL,
`user_name` varchar(255) NOT NULL COMMENT '收货人姓名',
`user_tel` varchar(255) NOT NULL COMMENT '收货人电话',
`user_address` varchar(255) NOT NULL,
`type` int(4) NOT NULL COMMENT '是否中奖;0未中奖;1,中奖',
`status` int(4) NOT NULL COMMENT '是否领取礼物;0,未领取;1,已领取',
`kd_gongsi` varchar(255) NOT NULL COMMENT '快递公司',
`kd_order` varchar(255) NOT NULL COMMENT '快递单号',
`fh_time` varchar(255) NOT NULL COMMENT '发货时间',
`formid` varchar(255) NOT NULL COMMENT '中奖通知formid',
`fh_formid` varchar(255) NOT NULL COMMENT '发货通知formid',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_lws_sylc` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`num` int(11) NOT NULL,
`title` varchar(4) NOT NULL,
`y_name` varchar(10) NOT NULL,
`thumb` varchar(255) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_pay_record` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`openid` varchar(255) DEFAULT NULL COMMENT '用户',
`vip_id` int(11) DEFAULT NULL COMMENT '购买类别id',
`create_time` varchar(100) DEFAULT NULL COMMENT '消费时间',
`total` varchar(50) DEFAULT NULL COMMENT '总额',
`num` int(11) DEFAULT NULL COMMENT '新增次数',
`nickname` varchar(100) DEFAULT NULL,
`status` int(11) DEFAULT NULL COMMENT '1-购买发起次数   2-发起红包抽奖  3-付费抽奖   4-提现 5-中奖收益  6-支付跳转小程序',
`poundage` varchar(50) DEFAULT NULL DEFAULT '0' COMMENT '手续费',
`y_total` decimal(10,2) DEFAULT NULL COMMENT '原总价',
`goods_id` int(11) DEFAULT NULL COMMENT '奖品id',
`avatar` varchar(666) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_problems` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`title` varchar(255) DEFAULT NULL,
`answer` text DEFAULT NULL,
`status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1推送 0未推送',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_record` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`goods_id` int(11) DEFAULT NULL COMMENT '奖品',
`user_name` varchar(50) DEFAULT NULL COMMENT '收货人',
`status` int(11) NOT NULL COMMENT '中奖状态(0、未中   1、已中)',
`create_time` varchar(100) DEFAULT NULL,
`goods_name` varchar(100) DEFAULT NULL,
`user_address` varchar(255) DEFAULT NULL COMMENT '收货地址',
`user_zip` varchar(100) DEFAULT NULL COMMENT '邮编',
`openid` varchar(100) DEFAULT NULL,
`nickname` varchar(100) DEFAULT NULL COMMENT '用户名',
`avatar` varchar(255) DEFAULT NULL COMMENT '用户头像',
`user_tel` varchar(100) DEFAULT NULL COMMENT '联系人电话',
`finish_time` varchar(100) DEFAULT NULL COMMENT '开奖时间',
`formid` varchar(100) DEFAULT NULL,
`card_num` varchar(100) DEFAULT NULL,
`card_password` varchar(100) DEFAULT NULL,
`get_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '0 未选择  1商家配送  2自提',
`team_id` int(11) DEFAULT NULL DEFAULT '0',
`team_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1-组队成功',
`get_create_time` datetime DEFAULT NULL COMMENT '选择领取方式 时间',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_scene` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`openid` varchar(100) DEFAULT NULL,
`goods_id` int(11) DEFAULT NULL,
`status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '0-未开奖 1-已开奖',
`join_status` int(11) DEFAULT NULL DEFAULT '-1' COMMENT '1正在参与 -1未参与',
`get_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1-中奖 2-已领取 0-未开奖或未中奖',
`nickname` varchar(100) DEFAULT NULL,
`avatar` varchar(255) DEFAULT NULL,
`broadcast_status` text DEFAULT NULL COMMENT '1已播报',
`send_time` varchar(50) DEFAULT NULL,
`finish_time` varchar(50) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_scene_dialogue` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`goods_id` int(11) DEFAULT NULL,
`openid` varchar(100) DEFAULT NULL,
`content` varchar(255) DEFAULT NULL,
`nickname` varchar(100) DEFAULT NULL,
`avatar` varchar(255) DEFAULT NULL,
`create_time` varchar(50) DEFAULT NULL,
`broadcast_status` text DEFAULT NULL COMMENT '滚动状态',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_speak` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`openid` varchar(100) DEFAULT NULL,
`goods_id` int(11) DEFAULT NULL,
`content` varchar(1000) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_team` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`goods_id` int(11) DEFAULT NULL,
`c_openid` varchar(100) DEFAULT NULL DEFAULT '0',
`openid` varchar(100) DEFAULT NULL,
`avatar` varchar(255) DEFAULT NULL,
`nickname` varchar(100) DEFAULT NULL,
`c_team_id` int(11) DEFAULT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_user` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`openid` varchar(150) DEFAULT NULL,
`avatar` varchar(500) DEFAULT NULL,
`nickname` varchar(100) DEFAULT NULL,
`create_time` varchar(100) DEFAULT NULL,
`yu_num` int(11) DEFAULT NULL DEFAULT '0',
`mf_num` int(11) DEFAULT NULL COMMENT '免费次数',
`smoke_num` int(11) DEFAULT NULL DEFAULT '0' COMMENT '免费抽奖次数',
`smoke_share_num` int(11) DEFAULT NULL DEFAULT '0' COMMENT '抽奖购买次数(获得次数-分享)',
`winning_num` int(11) DEFAULT NULL DEFAULT '0' COMMENT '每人每月最多中奖次数',
`send_time` varchar(50) DEFAULT NULL,
`remaining_sum` varchar(100) DEFAULT NULL DEFAULT '0' COMMENT '余额',
`earnings` varchar(100) DEFAULT NULL DEFAULT '0' COMMENT '收益',
`share_num` int(11) DEFAULT NULL DEFAULT '0' COMMENT '每日分享最多获得的免费抽奖次数',
`share_num_time` varchar(100) DEFAULT NULL COMMENT '更新时间',
`form_id` text DEFAULT NULL,
`name` varchar(255) DEFAULT NULL,
`tel` varchar(255) DEFAULT NULL,
`shengri` varchar(255) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_verification` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`verification` varchar(500) DEFAULT NULL COMMENT '二维码',
`goods_id` int(11) DEFAULT NULL,
`new_poster` varchar(500) DEFAULT NULL COMMENT '海报',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_vip_num` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`title` varchar(50) DEFAULT NULL,
`price` decimal(10,2) DEFAULT NULL,
`num` int(11) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_withdrawal` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`nickname` varchar(255) DEFAULT NULL,
`avatar` varchar(255) DEFAULT NULL,
`total` decimal(10,2) DEFAULT NULL DEFAULT '0.00' COMMENT '原金额',
`status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1-通过 -1拒绝',
`create_time` varchar(100) DEFAULT NULL,
`openid` varchar(100) DEFAULT NULL,
`poundage` decimal(10,2) DEFAULT NULL DEFAULT '0.00' COMMENT '手续费',
`money` decimal(10,2) DEFAULT NULL DEFAULT '0.00' COMMENT '实际提现金额',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_choujiang_xcx` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`icon` text DEFAULT NULL COMMENT '小程序图标',
`name` varchar(100) DEFAULT NULL COMMENT '小程序名字',
`title` varchar(100) DEFAULT NULL COMMENT '小程序标题',
`url` varchar(255) DEFAULT NULL COMMENT '小程序链接',
`appid` varchar(100) DEFAULT NULL,
`appsecret` varchar(150) DEFAULT NULL,
`status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1推荐 0不推荐',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");
if(pdo_tableexists('choujiang_advertising')) {
	if(!pdo_fieldexists('choujiang_advertising',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_advertising')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_advertising')) {
	if(!pdo_fieldexists('choujiang_advertising',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_advertising')." ADD `uniacid` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_advertising')) {
	if(!pdo_fieldexists('choujiang_advertising',  'title')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_advertising')." ADD `title` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_advertising')) {
	if(!pdo_fieldexists('choujiang_advertising',  'image')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_advertising')." ADD `image` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_advertising')) {
	if(!pdo_fieldexists('choujiang_advertising',  'to_index')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_advertising')." ADD `to_index` int(11) DEFAULT NULL DEFAULT '0';");
	}	
}
if(pdo_tableexists('choujiang_advertising')) {
	if(!pdo_fieldexists('choujiang_advertising',  'appid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_advertising')." ADD `appid` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_advertising')) {
	if(!pdo_fieldexists('choujiang_advertising',  'url')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_advertising')." ADD `url` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `uniacid` int(11) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'appid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `appid` varchar(255) DEFAULT NULL COMMENT '跳转小程序appid';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'appsecret')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `appsecret` varchar(255) DEFAULT NULL COMMENT '密钥';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'template_id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `template_id` varchar(255) DEFAULT NULL COMMENT '模板通知';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'appkey')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `appkey` varchar(255) DEFAULT NULL COMMENT '支付密钥';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'mch_id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `mch_id` varchar(255) DEFAULT NULL COMMENT '商户id';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'join_num')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `join_num` int(11) DEFAULT NULL DEFAULT '0' COMMENT '免费发起次数';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'smoke_num')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `smoke_num` int(11) DEFAULT NULL DEFAULT '0' COMMENT '免费抽奖次数';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'winning_num')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `winning_num` int(11) DEFAULT NULL DEFAULT '0' COMMENT '每人每月中奖次数';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'poundage')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `poundage` int(11) DEFAULT NULL DEFAULT '0' COMMENT '红包手续费';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'xcx_price')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `xcx_price` decimal(10,2) DEFAULT NULL DEFAULT '0.00' COMMENT '小程序跳转 appid 付费价格';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'share_num')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `share_num` int(11) DEFAULT NULL DEFAULT '0' COMMENT '每日分享最多获得抽奖次数';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'cheat_status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `cheat_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '0-不开启  1-开启';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'upfile')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `upfile` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'keypem')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `keypem` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'envelope_draw')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `envelope_draw` int(11) DEFAULT NULL DEFAULT '0' COMMENT '0未开启 1已开启';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'index_title')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `index_title` varchar(100) DEFAULT NULL COMMENT '首页标题';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'audit_set')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `audit_set` int(11) DEFAULT NULL DEFAULT '0' COMMENT '后台审核按钮';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'sponsor_status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `sponsor_status` int(11) DEFAULT NULL DEFAULT '0';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'template_id2')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `template_id2` varchar(255) DEFAULT NULL COMMENT '商品发布成功通知';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'latest_time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `latest_time` int(11) DEFAULT NULL DEFAULT '0';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'share_title')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `share_title` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'takes_time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `takes_time` int(11) DEFAULT NULL DEFAULT '0' COMMENT '用户自取限时 小时';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'hexiaoma')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `hexiaoma` varchar(50) DEFAULT NULL COMMENT '全局通用核销码';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'template_id3')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `template_id3` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'template_id4')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `template_id4` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'template_id5')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `template_id5` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'ad')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `ad` varchar(255) DEFAULT NULL COMMENT '流量主id';");
	}	
}
if(pdo_tableexists('choujiang_base')) {
	if(!pdo_fieldexists('choujiang_base',  'zzs_text')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_base')." ADD `zzs_text` text NOT NULL COMMENT '赞助商内容';");
	}	
}
if(pdo_tableexists('choujiang_cheat')) {
	if(!pdo_fieldexists('choujiang_cheat',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_cheat')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_cheat')) {
	if(!pdo_fieldexists('choujiang_cheat',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_cheat')." ADD `uniacid` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_cheat')) {
	if(!pdo_fieldexists('choujiang_cheat',  'title')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_cheat')." ADD `title` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_cheat')) {
	if(!pdo_fieldexists('choujiang_cheat',  'icon')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_cheat')." ADD `icon` varchar(1000) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_cheat')) {
	if(!pdo_fieldexists('choujiang_cheat',  'content')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_cheat')." ADD `content` varchar(6666) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_cheat_nav')) {
	if(!pdo_fieldexists('choujiang_cheat_nav',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_cheat_nav')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_cheat_nav')) {
	if(!pdo_fieldexists('choujiang_cheat_nav',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_cheat_nav')." ADD `uniacid` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_cheat_nav')) {
	if(!pdo_fieldexists('choujiang_cheat_nav',  'title')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_cheat_nav')." ADD `title` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_cheat_nav')) {
	if(!pdo_fieldexists('choujiang_cheat_nav',  'icon')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_cheat_nav')." ADD `icon` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_cheat_nav')) {
	if(!pdo_fieldexists('choujiang_cheat_nav',  'appid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_cheat_nav')." ADD `appid` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_earnings')) {
	if(!pdo_fieldexists('choujiang_earnings',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_earnings')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_earnings')) {
	if(!pdo_fieldexists('choujiang_earnings',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_earnings')." ADD `uniacid` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_earnings')) {
	if(!pdo_fieldexists('choujiang_earnings',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_earnings')." ADD `openid` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_earnings')) {
	if(!pdo_fieldexists('choujiang_earnings',  'money')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_earnings')." ADD `money` decimal(10,2) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_earnings')) {
	if(!pdo_fieldexists('choujiang_earnings',  'create_time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_earnings')." ADD `create_time` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_exchange')) {
	if(!pdo_fieldexists('choujiang_exchange',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_exchange')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_exchange')) {
	if(!pdo_fieldexists('choujiang_exchange',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_exchange')." ADD `uniacid` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_exchange')) {
	if(!pdo_fieldexists('choujiang_exchange',  'goods_id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_exchange')." ADD `goods_id` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_exchange')) {
	if(!pdo_fieldexists('choujiang_exchange',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_exchange')." ADD `openid` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_exchange')) {
	if(!pdo_fieldexists('choujiang_exchange',  'status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_exchange')." ADD `status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '0-未领取  1-已领取';");
	}	
}
if(pdo_tableexists('choujiang_exchange')) {
	if(!pdo_fieldexists('choujiang_exchange',  'create_time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_exchange')." ADD `create_time` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_exchange')) {
	if(!pdo_fieldexists('choujiang_exchange',  'verification')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_exchange')." ADD `verification` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_exchange')) {
	if(!pdo_fieldexists('choujiang_exchange',  'orders')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_exchange')." ADD `orders` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_fur')) {
	if(!pdo_fieldexists('choujiang_fur',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_fur')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_fur')) {
	if(!pdo_fieldexists('choujiang_fur',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_fur')." ADD `uniacid` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_fur')) {
	if(!pdo_fieldexists('choujiang_fur',  'title')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_fur')." ADD `title` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_fur')) {
	if(!pdo_fieldexists('choujiang_fur',  'status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_fur')." ADD `status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1-使用中';");
	}	
}
if(pdo_tableexists('choujiang_fur')) {
	if(!pdo_fieldexists('choujiang_fur',  'category')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_fur')." ADD `category` int(11) DEFAULT NULL DEFAULT '0' COMMENT '0-实物 1-电子卡  2-红包';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `uniacid` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'goods_name')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `goods_name` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'goods_num')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `goods_num` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'smoke_time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `smoke_time` varchar(100) DEFAULT NULL COMMENT '自动开奖时间';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'smoke_num')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `smoke_num` int(11) DEFAULT NULL COMMENT '开奖人数';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'smoke_set')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `smoke_set` int(11) DEFAULT NULL COMMENT '开奖条件(0、按时间  1、按人数  2、手动 3、现场)';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'goods_icon')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `goods_icon` text DEFAULT NULL COMMENT '商品图片';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'goods_sponsorship')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `goods_sponsorship` varchar(100) DEFAULT NULL COMMENT '奖品赞助商';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '奖品状态(1、已结束   0、正在进行中)';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'goods_openid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `goods_openid` varchar(100) DEFAULT NULL COMMENT '发起人';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'send_time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `send_time` int(11) DEFAULT NULL COMMENT '开奖的时间';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'content')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `content` text DEFAULT NULL COMMENT '奖品介绍';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'sponsorship_text')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `sponsorship_text` varchar(255) DEFAULT NULL COMMENT '赞助商标语';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'is_del')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `is_del` int(11) DEFAULT NULL DEFAULT '1' COMMENT '1 未删除 -1 已删除';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'The_winning')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `The_winning` int(11) DEFAULT NULL DEFAULT '0' COMMENT '指定中奖者状态 0否，1是';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'goods_winning')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `goods_winning` text DEFAULT NULL COMMENT 'z指定的中奖者openid';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'sponsorship_appid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `sponsorship_appid` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'openid_arr')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `openid_arr` text DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'audit_status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `audit_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '审核状态 0-审核中 1通过 -1失败';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'mouth_command')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `mouth_command` varchar(255) DEFAULT NULL COMMENT '口令';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'join_conditions')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `join_conditions` int(11) DEFAULT NULL DEFAULT '0' COMMENT '是否需要付费参与 0 没有  1 付费 2口令';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'price')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `price` decimal(10,2) DEFAULT NULL DEFAULT '0.00' COMMENT '付费参与价格';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'red_envelope')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `red_envelope` decimal(10,2) DEFAULT NULL COMMENT '红包金额';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'card_info')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `card_info` text DEFAULT NULL COMMENT '卡号';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'goods_status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `goods_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '0 实物 1红包 2电子卡';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'goods_images')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `goods_images` text DEFAULT NULL COMMENT '奖品详情图';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'sponsorship_content')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `sponsorship_content` varchar(6666) DEFAULT NULL COMMENT '赞助商介绍';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'sponsorship_url')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `sponsorship_url` varchar(150) DEFAULT NULL COMMENT '跳转小程序路径';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'to_index')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `to_index` int(11) DEFAULT NULL DEFAULT '0';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'business_address')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `business_address` varchar(255) DEFAULT NULL COMMENT '商家地址';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'freight')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `freight` decimal(10,2) DEFAULT NULL DEFAULT '0.00' COMMENT '运费';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'get_status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `get_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '领取方式 0-无 1-店家配送  2-到店领取';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'YouSong')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `YouSong` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1-包邮  0-付费';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'team_status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `team_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '0-不支持 1-支持组队';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'mail_status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `mail_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1-支持邮寄';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'ziqu_status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `ziqu_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1-支持自取';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'team_num')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `team_num` int(11) DEFAULT NULL DEFAULT '0';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'fur_title')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `fur_title` int(11) DEFAULT NULL DEFAULT '0' COMMENT '皮一下标题id';");
	}	
}
if(pdo_tableexists('choujiang_goods')) {
	if(!pdo_fieldexists('choujiang_goods',  'fur_status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_goods')." ADD `fur_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '皮一下状态 0-普通奖品 1-皮一下状态';");
	}	
}
if(pdo_tableexists('choujiang_lws_base')) {
	if(!pdo_fieldexists('choujiang_lws_base',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_base')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_lws_base')) {
	if(!pdo_fieldexists('choujiang_lws_base',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_base')." ADD `uniacid` int(11) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_base')) {
	if(!pdo_fieldexists('choujiang_lws_base',  'name')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_base')." ADD `name` varchar(50) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_base')) {
	if(!pdo_fieldexists('choujiang_lws_base',  'title')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_base')." ADD `title` varchar(50) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_base')) {
	if(!pdo_fieldexists('choujiang_lws_base',  'jianjie')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_base')." ADD `jianjie` varchar(50) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_base')) {
	if(!pdo_fieldexists('choujiang_lws_base',  'video')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_base')." ADD `video` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_base')) {
	if(!pdo_fieldexists('choujiang_lws_base',  'sysm')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_base')." ADD `sysm` text NOT NULL COMMENT '使用说明';");
	}	
}
if(pdo_tableexists('choujiang_lws_base')) {
	if(!pdo_fieldexists('choujiang_lws_base',  'yhxy')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_base')." ADD `yhxy` text NOT NULL COMMENT '用户协议';");
	}	
}
if(pdo_tableexists('choujiang_lws_base')) {
	if(!pdo_fieldexists('choujiang_lws_base',  'slide')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_base')." ADD `slide` varchar(2555) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_goods')) {
	if(!pdo_fieldexists('choujiang_lws_goods',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_goods')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_lws_goods')) {
	if(!pdo_fieldexists('choujiang_lws_goods',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_goods')." ADD `uniacid` int(11) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_goods')) {
	if(!pdo_fieldexists('choujiang_lws_goods',  'num')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_goods')." ADD `num` int(11) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_goods')) {
	if(!pdo_fieldexists('choujiang_lws_goods',  'title')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_goods')." ADD `title` varchar(50) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_goods')) {
	if(!pdo_fieldexists('choujiang_lws_goods',  'jianjie')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_goods')." ADD `jianjie` varchar(50) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_goods')) {
	if(!pdo_fieldexists('choujiang_lws_goods',  'thumb')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_goods')." ADD `thumb` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_goods')) {
	if(!pdo_fieldexists('choujiang_lws_goods',  'slide')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_goods')." ADD `slide` varchar(2555) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_goods')) {
	if(!pdo_fieldexists('choujiang_lws_goods',  'price')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_goods')." ADD `price` decimal(10,2) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_goods')) {
	if(!pdo_fieldexists('choujiang_lws_goods',  'text')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_goods')." ADD `text` text NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_goods')) {
	if(!pdo_fieldexists('choujiang_lws_goods',  'lc_id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_goods')." ADD `lc_id` int(11) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_goods')) {
	if(!pdo_fieldexists('choujiang_lws_goods',  'fl_id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_goods')." ADD `fl_id` int(11) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_goods')) {
	if(!pdo_fieldexists('choujiang_lws_goods',  'status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_goods')." ADD `status` int(4) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_goods')) {
	if(!pdo_fieldexists('choujiang_lws_goods',  'kucun')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_goods')." ADD `kucun` int(10) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_goods_fl')) {
	if(!pdo_fieldexists('choujiang_lws_goods_fl',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_goods_fl')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_lws_goods_fl')) {
	if(!pdo_fieldexists('choujiang_lws_goods_fl',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_goods_fl')." ADD `uniacid` int(11) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_goods_fl')) {
	if(!pdo_fieldexists('choujiang_lws_goods_fl',  'num')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_goods_fl')." ADD `num` int(11) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_goods_fl')) {
	if(!pdo_fieldexists('choujiang_lws_goods_fl',  'title')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_goods_fl')." ADD `title` varchar(4) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_kf')) {
	if(!pdo_fieldexists('choujiang_lws_kf',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_kf')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_lws_kf')) {
	if(!pdo_fieldexists('choujiang_lws_kf',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_kf')." ADD `uniacid` int(11) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_kf')) {
	if(!pdo_fieldexists('choujiang_lws_kf',  'fapiao')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_kf')." ADD `fapiao` varchar(255) NOT NULL COMMENT '发票';");
	}	
}
if(pdo_tableexists('choujiang_lws_kf')) {
	if(!pdo_fieldexists('choujiang_lws_kf',  'kffw')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_kf')." ADD `kffw` varchar(255) NOT NULL COMMENT '客服服务';");
	}	
}
if(pdo_tableexists('choujiang_lws_kf')) {
	if(!pdo_fieldexists('choujiang_lws_kf',  'swhz')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_kf')." ADD `swhz` varchar(255) NOT NULL COMMENT '商务合作';");
	}	
}
if(pdo_tableexists('choujiang_lws_kf')) {
	if(!pdo_fieldexists('choujiang_lws_kf',  'qyfw')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_kf')." ADD `qyfw` varchar(255) NOT NULL COMMENT '企业服务';");
	}	
}
if(pdo_tableexists('choujiang_lws_kf')) {
	if(!pdo_fieldexists('choujiang_lws_kf',  'lwlq')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_kf')." ADD `lwlq` varchar(255) NOT NULL COMMENT '礼物领取';");
	}	
}
if(pdo_tableexists('choujiang_lws_order')) {
	if(!pdo_fieldexists('choujiang_lws_order',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_lws_order')) {
	if(!pdo_fieldexists('choujiang_lws_order',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order')." ADD `uniacid` int(11) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_order')) {
	if(!pdo_fieldexists('choujiang_lws_order',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order')." ADD `openid` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_order')) {
	if(!pdo_fieldexists('choujiang_lws_order',  'title')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order')." ADD `title` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_order')) {
	if(!pdo_fieldexists('choujiang_lws_order',  'price')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order')." ADD `price` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_order')) {
	if(!pdo_fieldexists('choujiang_lws_order',  'thumb')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order')." ADD `thumb` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_order')) {
	if(!pdo_fieldexists('choujiang_lws_order',  'totalPrice')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order')." ADD `totalPrice` varchar(255) NOT NULL COMMENT '总价';");
	}	
}
if(pdo_tableexists('choujiang_lws_order')) {
	if(!pdo_fieldexists('choujiang_lws_order',  'sltj')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order')." ADD `sltj` varchar(50) NOT NULL COMMENT '送礼条件';");
	}	
}
if(pdo_tableexists('choujiang_lws_order')) {
	if(!pdo_fieldexists('choujiang_lws_order',  'counts')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order')." ADD `counts` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_order')) {
	if(!pdo_fieldexists('choujiang_lws_order',  'kjPeonum')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order')." ADD `kjPeonum` int(10) NOT NULL COMMENT '开奖人数';");
	}	
}
if(pdo_tableexists('choujiang_lws_order')) {
	if(!pdo_fieldexists('choujiang_lws_order',  'greetings')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order')." ADD `greetings` varchar(255) NOT NULL COMMENT '祝福语';");
	}	
}
if(pdo_tableexists('choujiang_lws_order')) {
	if(!pdo_fieldexists('choujiang_lws_order',  'orderid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order')." ADD `orderid` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_order')) {
	if(!pdo_fieldexists('choujiang_lws_order',  'time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order')." ADD `time` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_order')) {
	if(!pdo_fieldexists('choujiang_lws_order',  'goods_id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order')." ADD `goods_id` int(10) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_order')) {
	if(!pdo_fieldexists('choujiang_lws_order',  'status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order')." ADD `status` int(4) NOT NULL COMMENT '状态值,0,未送出;1已送出,2已领取,3已收货';");
	}	
}
if(pdo_tableexists('choujiang_lws_order')) {
	if(!pdo_fieldexists('choujiang_lws_order',  'types')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order')." ADD `types` int(4) NOT NULL COMMENT '满人送礼是否开奖;0,未开奖;1,已开奖';");
	}	
}
if(pdo_tableexists('choujiang_lws_order')) {
	if(!pdo_fieldexists('choujiang_lws_order',  'new_poster')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order')." ADD `new_poster` varchar(255) NOT NULL COMMENT '海报';");
	}	
}
if(pdo_tableexists('choujiang_lws_order')) {
	if(!pdo_fieldexists('choujiang_lws_order',  'ticketurl')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order')." ADD `ticketurl` varchar(255) NOT NULL COMMENT '海报二维码';");
	}	
}
if(pdo_tableexists('choujiang_lws_order')) {
	if(!pdo_fieldexists('choujiang_lws_order',  'formid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order')." ADD `formid` varchar(255) NOT NULL COMMENT '领取成功formid';");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `id` int(10) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `uniacid` int(10) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `openid` varchar(255) NOT NULL COMMENT '用户openid';");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'nickName')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `nickName` varchar(255) NOT NULL COMMENT '用户昵称';");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'avatarUrl')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `avatarUrl` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'orderid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `orderid` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'c_openid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `c_openid` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'c_nickName')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `c_nickName` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'c_avatarUrl')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `c_avatarUrl` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `time` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'user_name')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `user_name` varchar(255) NOT NULL COMMENT '收货人姓名';");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'user_tel')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `user_tel` varchar(255) NOT NULL COMMENT '收货人电话';");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'user_address')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `user_address` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'type')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `type` int(4) NOT NULL COMMENT '是否中奖;0未中奖;1,中奖';");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `status` int(4) NOT NULL COMMENT '是否领取礼物;0,未领取;1,已领取';");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'kd_gongsi')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `kd_gongsi` varchar(255) NOT NULL COMMENT '快递公司';");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'kd_order')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `kd_order` varchar(255) NOT NULL COMMENT '快递单号';");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'fh_time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `fh_time` varchar(255) NOT NULL COMMENT '发货时间';");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'formid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `formid` varchar(255) NOT NULL COMMENT '中奖通知formid';");
	}	
}
if(pdo_tableexists('choujiang_lws_order_liwu')) {
	if(!pdo_fieldexists('choujiang_lws_order_liwu',  'fh_formid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_order_liwu')." ADD `fh_formid` varchar(255) NOT NULL COMMENT '发货通知formid';");
	}	
}
if(pdo_tableexists('choujiang_lws_sylc')) {
	if(!pdo_fieldexists('choujiang_lws_sylc',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_sylc')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_lws_sylc')) {
	if(!pdo_fieldexists('choujiang_lws_sylc',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_sylc')." ADD `uniacid` int(11) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_sylc')) {
	if(!pdo_fieldexists('choujiang_lws_sylc',  'num')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_sylc')." ADD `num` int(11) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_sylc')) {
	if(!pdo_fieldexists('choujiang_lws_sylc',  'title')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_sylc')." ADD `title` varchar(4) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_sylc')) {
	if(!pdo_fieldexists('choujiang_lws_sylc',  'y_name')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_sylc')." ADD `y_name` varchar(10) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_lws_sylc')) {
	if(!pdo_fieldexists('choujiang_lws_sylc',  'thumb')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_lws_sylc')." ADD `thumb` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('choujiang_pay_record')) {
	if(!pdo_fieldexists('choujiang_pay_record',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_pay_record')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_pay_record')) {
	if(!pdo_fieldexists('choujiang_pay_record',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_pay_record')." ADD `uniacid` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_pay_record')) {
	if(!pdo_fieldexists('choujiang_pay_record',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_pay_record')." ADD `openid` varchar(255) DEFAULT NULL COMMENT '用户';");
	}	
}
if(pdo_tableexists('choujiang_pay_record')) {
	if(!pdo_fieldexists('choujiang_pay_record',  'vip_id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_pay_record')." ADD `vip_id` int(11) DEFAULT NULL COMMENT '购买类别id';");
	}	
}
if(pdo_tableexists('choujiang_pay_record')) {
	if(!pdo_fieldexists('choujiang_pay_record',  'create_time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_pay_record')." ADD `create_time` varchar(100) DEFAULT NULL COMMENT '消费时间';");
	}	
}
if(pdo_tableexists('choujiang_pay_record')) {
	if(!pdo_fieldexists('choujiang_pay_record',  'total')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_pay_record')." ADD `total` varchar(50) DEFAULT NULL COMMENT '总额';");
	}	
}
if(pdo_tableexists('choujiang_pay_record')) {
	if(!pdo_fieldexists('choujiang_pay_record',  'num')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_pay_record')." ADD `num` int(11) DEFAULT NULL COMMENT '新增次数';");
	}	
}
if(pdo_tableexists('choujiang_pay_record')) {
	if(!pdo_fieldexists('choujiang_pay_record',  'nickname')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_pay_record')." ADD `nickname` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_pay_record')) {
	if(!pdo_fieldexists('choujiang_pay_record',  'status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_pay_record')." ADD `status` int(11) DEFAULT NULL COMMENT '1-购买发起次数   2-发起红包抽奖  3-付费抽奖   4-提现 5-中奖收益  6-支付跳转小程序';");
	}	
}
if(pdo_tableexists('choujiang_pay_record')) {
	if(!pdo_fieldexists('choujiang_pay_record',  'poundage')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_pay_record')." ADD `poundage` varchar(50) DEFAULT NULL DEFAULT '0' COMMENT '手续费';");
	}	
}
if(pdo_tableexists('choujiang_pay_record')) {
	if(!pdo_fieldexists('choujiang_pay_record',  'y_total')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_pay_record')." ADD `y_total` decimal(10,2) DEFAULT NULL COMMENT '原总价';");
	}	
}
if(pdo_tableexists('choujiang_pay_record')) {
	if(!pdo_fieldexists('choujiang_pay_record',  'goods_id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_pay_record')." ADD `goods_id` int(11) DEFAULT NULL COMMENT '奖品id';");
	}	
}
if(pdo_tableexists('choujiang_pay_record')) {
	if(!pdo_fieldexists('choujiang_pay_record',  'avatar')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_pay_record')." ADD `avatar` varchar(666) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_problems')) {
	if(!pdo_fieldexists('choujiang_problems',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_problems')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_problems')) {
	if(!pdo_fieldexists('choujiang_problems',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_problems')." ADD `uniacid` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_problems')) {
	if(!pdo_fieldexists('choujiang_problems',  'title')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_problems')." ADD `title` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_problems')) {
	if(!pdo_fieldexists('choujiang_problems',  'answer')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_problems')." ADD `answer` text DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_problems')) {
	if(!pdo_fieldexists('choujiang_problems',  'status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_problems')." ADD `status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1推送 0未推送';");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `uniacid` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'goods_id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `goods_id` int(11) DEFAULT NULL COMMENT '奖品';");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'user_name')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `user_name` varchar(50) DEFAULT NULL COMMENT '收货人';");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `status` int(11) NOT NULL COMMENT '中奖状态(0、未中   1、已中)';");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'create_time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `create_time` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'goods_name')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `goods_name` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'user_address')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `user_address` varchar(255) DEFAULT NULL COMMENT '收货地址';");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'user_zip')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `user_zip` varchar(100) DEFAULT NULL COMMENT '邮编';");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `openid` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'nickname')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `nickname` varchar(100) DEFAULT NULL COMMENT '用户名';");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'avatar')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `avatar` varchar(255) DEFAULT NULL COMMENT '用户头像';");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'user_tel')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `user_tel` varchar(100) DEFAULT NULL COMMENT '联系人电话';");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'finish_time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `finish_time` varchar(100) DEFAULT NULL COMMENT '开奖时间';");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'formid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `formid` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'card_num')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `card_num` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'card_password')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `card_password` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'get_status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `get_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '0 未选择  1商家配送  2自提';");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'team_id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `team_id` int(11) DEFAULT NULL DEFAULT '0';");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'team_status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `team_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1-组队成功';");
	}	
}
if(pdo_tableexists('choujiang_record')) {
	if(!pdo_fieldexists('choujiang_record',  'get_create_time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_record')." ADD `get_create_time` datetime DEFAULT NULL COMMENT '选择领取方式 时间';");
	}	
}
if(pdo_tableexists('choujiang_scene')) {
	if(!pdo_fieldexists('choujiang_scene',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_scene')) {
	if(!pdo_fieldexists('choujiang_scene',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene')." ADD `uniacid` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_scene')) {
	if(!pdo_fieldexists('choujiang_scene',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene')." ADD `openid` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_scene')) {
	if(!pdo_fieldexists('choujiang_scene',  'goods_id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene')." ADD `goods_id` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_scene')) {
	if(!pdo_fieldexists('choujiang_scene',  'status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene')." ADD `status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '0-未开奖 1-已开奖';");
	}	
}
if(pdo_tableexists('choujiang_scene')) {
	if(!pdo_fieldexists('choujiang_scene',  'join_status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene')." ADD `join_status` int(11) DEFAULT NULL DEFAULT '-1' COMMENT '1正在参与 -1未参与';");
	}	
}
if(pdo_tableexists('choujiang_scene')) {
	if(!pdo_fieldexists('choujiang_scene',  'get_status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene')." ADD `get_status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1-中奖 2-已领取 0-未开奖或未中奖';");
	}	
}
if(pdo_tableexists('choujiang_scene')) {
	if(!pdo_fieldexists('choujiang_scene',  'nickname')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene')." ADD `nickname` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_scene')) {
	if(!pdo_fieldexists('choujiang_scene',  'avatar')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene')." ADD `avatar` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_scene')) {
	if(!pdo_fieldexists('choujiang_scene',  'broadcast_status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene')." ADD `broadcast_status` text DEFAULT NULL COMMENT '1已播报';");
	}	
}
if(pdo_tableexists('choujiang_scene')) {
	if(!pdo_fieldexists('choujiang_scene',  'send_time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene')." ADD `send_time` varchar(50) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_scene')) {
	if(!pdo_fieldexists('choujiang_scene',  'finish_time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene')." ADD `finish_time` varchar(50) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_scene_dialogue')) {
	if(!pdo_fieldexists('choujiang_scene_dialogue',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene_dialogue')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_scene_dialogue')) {
	if(!pdo_fieldexists('choujiang_scene_dialogue',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene_dialogue')." ADD `uniacid` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_scene_dialogue')) {
	if(!pdo_fieldexists('choujiang_scene_dialogue',  'goods_id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene_dialogue')." ADD `goods_id` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_scene_dialogue')) {
	if(!pdo_fieldexists('choujiang_scene_dialogue',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene_dialogue')." ADD `openid` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_scene_dialogue')) {
	if(!pdo_fieldexists('choujiang_scene_dialogue',  'content')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene_dialogue')." ADD `content` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_scene_dialogue')) {
	if(!pdo_fieldexists('choujiang_scene_dialogue',  'nickname')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene_dialogue')." ADD `nickname` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_scene_dialogue')) {
	if(!pdo_fieldexists('choujiang_scene_dialogue',  'avatar')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene_dialogue')." ADD `avatar` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_scene_dialogue')) {
	if(!pdo_fieldexists('choujiang_scene_dialogue',  'create_time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene_dialogue')." ADD `create_time` varchar(50) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_scene_dialogue')) {
	if(!pdo_fieldexists('choujiang_scene_dialogue',  'broadcast_status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_scene_dialogue')." ADD `broadcast_status` text DEFAULT NULL COMMENT '滚动状态';");
	}	
}
if(pdo_tableexists('choujiang_speak')) {
	if(!pdo_fieldexists('choujiang_speak',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_speak')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_speak')) {
	if(!pdo_fieldexists('choujiang_speak',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_speak')." ADD `uniacid` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_speak')) {
	if(!pdo_fieldexists('choujiang_speak',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_speak')." ADD `openid` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_speak')) {
	if(!pdo_fieldexists('choujiang_speak',  'goods_id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_speak')." ADD `goods_id` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_speak')) {
	if(!pdo_fieldexists('choujiang_speak',  'content')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_speak')." ADD `content` varchar(1000) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_team')) {
	if(!pdo_fieldexists('choujiang_team',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_team')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_team')) {
	if(!pdo_fieldexists('choujiang_team',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_team')." ADD `uniacid` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_team')) {
	if(!pdo_fieldexists('choujiang_team',  'goods_id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_team')." ADD `goods_id` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_team')) {
	if(!pdo_fieldexists('choujiang_team',  'c_openid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_team')." ADD `c_openid` varchar(100) DEFAULT NULL DEFAULT '0';");
	}	
}
if(pdo_tableexists('choujiang_team')) {
	if(!pdo_fieldexists('choujiang_team',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_team')." ADD `openid` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_team')) {
	if(!pdo_fieldexists('choujiang_team',  'avatar')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_team')." ADD `avatar` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_team')) {
	if(!pdo_fieldexists('choujiang_team',  'nickname')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_team')." ADD `nickname` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_team')) {
	if(!pdo_fieldexists('choujiang_team',  'c_team_id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_team')." ADD `c_team_id` int(11) DEFAULT NULL DEFAULT '0';");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `uniacid` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `openid` varchar(150) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'avatar')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `avatar` varchar(500) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'nickname')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `nickname` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'create_time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `create_time` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'yu_num')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `yu_num` int(11) DEFAULT NULL DEFAULT '0';");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'mf_num')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `mf_num` int(11) DEFAULT NULL COMMENT '免费次数';");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'smoke_num')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `smoke_num` int(11) DEFAULT NULL DEFAULT '0' COMMENT '免费抽奖次数';");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'smoke_share_num')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `smoke_share_num` int(11) DEFAULT NULL DEFAULT '0' COMMENT '抽奖购买次数(获得次数-分享)';");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'winning_num')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `winning_num` int(11) DEFAULT NULL DEFAULT '0' COMMENT '每人每月最多中奖次数';");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'send_time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `send_time` varchar(50) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'remaining_sum')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `remaining_sum` varchar(100) DEFAULT NULL DEFAULT '0' COMMENT '余额';");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'earnings')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `earnings` varchar(100) DEFAULT NULL DEFAULT '0' COMMENT '收益';");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'share_num')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `share_num` int(11) DEFAULT NULL DEFAULT '0' COMMENT '每日分享最多获得的免费抽奖次数';");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'share_num_time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `share_num_time` varchar(100) DEFAULT NULL COMMENT '更新时间';");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'form_id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `form_id` text DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'name')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `name` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'tel')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `tel` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_user')) {
	if(!pdo_fieldexists('choujiang_user',  'shengri')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_user')." ADD `shengri` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_verification')) {
	if(!pdo_fieldexists('choujiang_verification',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_verification')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_verification')) {
	if(!pdo_fieldexists('choujiang_verification',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_verification')." ADD `uniacid` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_verification')) {
	if(!pdo_fieldexists('choujiang_verification',  'verification')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_verification')." ADD `verification` varchar(500) DEFAULT NULL COMMENT '二维码';");
	}	
}
if(pdo_tableexists('choujiang_verification')) {
	if(!pdo_fieldexists('choujiang_verification',  'goods_id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_verification')." ADD `goods_id` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_verification')) {
	if(!pdo_fieldexists('choujiang_verification',  'new_poster')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_verification')." ADD `new_poster` varchar(500) DEFAULT NULL COMMENT '海报';");
	}	
}
if(pdo_tableexists('choujiang_vip_num')) {
	if(!pdo_fieldexists('choujiang_vip_num',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_vip_num')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_vip_num')) {
	if(!pdo_fieldexists('choujiang_vip_num',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_vip_num')." ADD `uniacid` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_vip_num')) {
	if(!pdo_fieldexists('choujiang_vip_num',  'title')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_vip_num')." ADD `title` varchar(50) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_vip_num')) {
	if(!pdo_fieldexists('choujiang_vip_num',  'price')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_vip_num')." ADD `price` decimal(10,2) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_vip_num')) {
	if(!pdo_fieldexists('choujiang_vip_num',  'num')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_vip_num')." ADD `num` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_withdrawal')) {
	if(!pdo_fieldexists('choujiang_withdrawal',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_withdrawal')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_withdrawal')) {
	if(!pdo_fieldexists('choujiang_withdrawal',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_withdrawal')." ADD `uniacid` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_withdrawal')) {
	if(!pdo_fieldexists('choujiang_withdrawal',  'nickname')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_withdrawal')." ADD `nickname` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_withdrawal')) {
	if(!pdo_fieldexists('choujiang_withdrawal',  'avatar')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_withdrawal')." ADD `avatar` varchar(255) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_withdrawal')) {
	if(!pdo_fieldexists('choujiang_withdrawal',  'total')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_withdrawal')." ADD `total` decimal(10,2) DEFAULT NULL DEFAULT '0.00' COMMENT '原金额';");
	}	
}
if(pdo_tableexists('choujiang_withdrawal')) {
	if(!pdo_fieldexists('choujiang_withdrawal',  'status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_withdrawal')." ADD `status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1-通过 -1拒绝';");
	}	
}
if(pdo_tableexists('choujiang_withdrawal')) {
	if(!pdo_fieldexists('choujiang_withdrawal',  'create_time')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_withdrawal')." ADD `create_time` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_withdrawal')) {
	if(!pdo_fieldexists('choujiang_withdrawal',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_withdrawal')." ADD `openid` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_withdrawal')) {
	if(!pdo_fieldexists('choujiang_withdrawal',  'poundage')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_withdrawal')." ADD `poundage` decimal(10,2) DEFAULT NULL DEFAULT '0.00' COMMENT '手续费';");
	}	
}
if(pdo_tableexists('choujiang_withdrawal')) {
	if(!pdo_fieldexists('choujiang_withdrawal',  'money')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_withdrawal')." ADD `money` decimal(10,2) DEFAULT NULL DEFAULT '0.00' COMMENT '实际提现金额';");
	}	
}
if(pdo_tableexists('choujiang_xcx')) {
	if(!pdo_fieldexists('choujiang_xcx',  'id')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_xcx')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('choujiang_xcx')) {
	if(!pdo_fieldexists('choujiang_xcx',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_xcx')." ADD `uniacid` int(11) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_xcx')) {
	if(!pdo_fieldexists('choujiang_xcx',  'icon')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_xcx')." ADD `icon` text DEFAULT NULL COMMENT '小程序图标';");
	}	
}
if(pdo_tableexists('choujiang_xcx')) {
	if(!pdo_fieldexists('choujiang_xcx',  'name')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_xcx')." ADD `name` varchar(100) DEFAULT NULL COMMENT '小程序名字';");
	}	
}
if(pdo_tableexists('choujiang_xcx')) {
	if(!pdo_fieldexists('choujiang_xcx',  'title')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_xcx')." ADD `title` varchar(100) DEFAULT NULL COMMENT '小程序标题';");
	}	
}
if(pdo_tableexists('choujiang_xcx')) {
	if(!pdo_fieldexists('choujiang_xcx',  'url')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_xcx')." ADD `url` varchar(255) DEFAULT NULL COMMENT '小程序链接';");
	}	
}
if(pdo_tableexists('choujiang_xcx')) {
	if(!pdo_fieldexists('choujiang_xcx',  'appid')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_xcx')." ADD `appid` varchar(100) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_xcx')) {
	if(!pdo_fieldexists('choujiang_xcx',  'appsecret')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_xcx')." ADD `appsecret` varchar(150) DEFAULT NULL;");
	}	
}
if(pdo_tableexists('choujiang_xcx')) {
	if(!pdo_fieldexists('choujiang_xcx',  'status')) {
		pdo_query("ALTER TABLE ".tablename('choujiang_xcx')." ADD `status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1推荐 0不推荐';");
	}	
}
