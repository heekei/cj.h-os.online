<?php
pdo_query("DROP TABLE IF EXISTS `ims_choujiang_advertising`;
CREATE TABLE IF NOT EXISTS `ims_choujiang_advertising` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`title` varchar(100) DEFAULT NULL,
`image` varchar(255) DEFAULT NULL,
`to_index` int(11) DEFAULT NULL DEFAULT '0',
`appid` varchar(100) DEFAULT NULL,
`url` varchar(255) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_choujiang_base`;
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

DROP TABLE IF EXISTS `ims_choujiang_cheat`;
CREATE TABLE IF NOT EXISTS `ims_choujiang_cheat` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`title` varchar(255) DEFAULT NULL,
`icon` varchar(1000) DEFAULT NULL,
`content` varchar(6666) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_choujiang_cheat_nav`;
CREATE TABLE IF NOT EXISTS `ims_choujiang_cheat_nav` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`title` varchar(255) DEFAULT NULL,
`icon` varchar(255) DEFAULT NULL,
`appid` varchar(100) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_choujiang_earnings`;
CREATE TABLE IF NOT EXISTS `ims_choujiang_earnings` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`openid` varchar(100) DEFAULT NULL,
`money` decimal(10,2) DEFAULT NULL,
`create_time` varchar(100) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_choujiang_exchange`;
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

DROP TABLE IF EXISTS `ims_choujiang_fur`;
CREATE TABLE IF NOT EXISTS `ims_choujiang_fur` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`title` varchar(255) DEFAULT NULL,
`status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1-使用中',
`category` int(11) DEFAULT NULL DEFAULT '0' COMMENT '0-实物 1-电子卡  2-红包',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_choujiang_goods`;
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

DROP TABLE IF EXISTS `ims_choujiang_lws_base`;
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

DROP TABLE IF EXISTS `ims_choujiang_lws_goods`;
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

DROP TABLE IF EXISTS `ims_choujiang_lws_goods_fl`;
CREATE TABLE IF NOT EXISTS `ims_choujiang_lws_goods_fl` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`num` int(11) NOT NULL,
`title` varchar(4) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_choujiang_lws_kf`;
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

DROP TABLE IF EXISTS `ims_choujiang_lws_order`;
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

DROP TABLE IF EXISTS `ims_choujiang_lws_order_liwu`;
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

DROP TABLE IF EXISTS `ims_choujiang_lws_sylc`;
CREATE TABLE IF NOT EXISTS `ims_choujiang_lws_sylc` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) NOT NULL,
`num` int(11) NOT NULL,
`title` varchar(4) NOT NULL,
`y_name` varchar(10) NOT NULL,
`thumb` varchar(255) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_choujiang_pay_record`;
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

DROP TABLE IF EXISTS `ims_choujiang_problems`;
CREATE TABLE IF NOT EXISTS `ims_choujiang_problems` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`title` varchar(255) DEFAULT NULL,
`answer` text DEFAULT NULL,
`status` int(11) DEFAULT NULL DEFAULT '0' COMMENT '1推送 0未推送',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_choujiang_record`;
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

DROP TABLE IF EXISTS `ims_choujiang_scene`;
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

DROP TABLE IF EXISTS `ims_choujiang_scene_dialogue`;
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

DROP TABLE IF EXISTS `ims_choujiang_speak`;
CREATE TABLE IF NOT EXISTS `ims_choujiang_speak` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`openid` varchar(100) DEFAULT NULL,
`goods_id` int(11) DEFAULT NULL,
`content` varchar(1000) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_choujiang_team`;
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

DROP TABLE IF EXISTS `ims_choujiang_user`;
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

DROP TABLE IF EXISTS `ims_choujiang_verification`;
CREATE TABLE IF NOT EXISTS `ims_choujiang_verification` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`verification` varchar(500) DEFAULT NULL COMMENT '二维码',
`goods_id` int(11) DEFAULT NULL,
`new_poster` varchar(500) DEFAULT NULL COMMENT '海报',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_choujiang_vip_num`;
CREATE TABLE IF NOT EXISTS `ims_choujiang_vip_num` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`uniacid` int(11) DEFAULT NULL,
`title` varchar(50) DEFAULT NULL,
`price` decimal(10,2) DEFAULT NULL,
`num` int(11) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_choujiang_withdrawal`;
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

DROP TABLE IF EXISTS `ims_choujiang_xcx`;
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
