CREATE TABLE `sfy_baobao` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'pk',
  `number` varchar(20) NOT NULL DEFAULT '' COMMENT '参赛编号',
  `name` varchar(16) NOT NULL DEFAULT '' COMMENT '名称',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未知1男2女',
  `month` int(11) NOT NULL DEFAULT '0' COMMENT '年龄(10个月)',
  `vote_times` int(11) NOT NULL DEFAULT '0' COMMENT '投票次数',
  `vote_nums` int(11) NOT NULL DEFAULT '0' COMMENT '投票人数',
  `is_top` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1正常2置顶',
  `is_check` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0审核中1通过2拒绝',
  `check_mark` varchar(255) NOT NULL DEFAULT '' COMMENT '审核备注',
  `check_at` int(10) NOT NULL DEFAULT '0',
  `parent_name` varchar(32) NOT NULL DEFAULT '' COMMENT '家长姓名',
  `parent_phone` varchar(15) NOT NULL DEFAULT '' COMMENT '家长电话',
  `openid` varchar(28) NOT NULL DEFAULT '' COMMENT 'openid',
  `prov` varchar(32) NOT NULL DEFAULT '' COMMENT '省',
  `city` varchar(32) NOT NULL DEFAULT '' COMMENT '市',
  `dist` varchar(32) NOT NULL DEFAULT '' COMMENT '区',
  `intro` varchar(255) NOT NULL DEFAULT '' COMMENT '自我介绍',
  `img1` varchar(255) NOT NULL DEFAULT '' COMMENT '图片1',
  `img2` varchar(255) NOT NULL DEFAULT '' COMMENT '图片2',
  `img3` varchar(255) NOT NULL DEFAULT '' COMMENT '图片3',
  `hobby` varchar(255) NOT NULL DEFAULT '' COMMENT '宝宝特点逗号分隔',
  `create_at` int(10) NOT NULL DEFAULT '0',
  `update_at` int(10) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0正常1删除',
  PRIMARY KEY (`id`),
  UNIQUE KEY `openid` (`openid`),
  KEY `number` (`number`,`name`),
  KEY `vote_times` (`vote_times`),
  KEY `is_top` (`is_top`)
) ENGINE=InnoDB AUTO_INCREMENT=144510 DEFAULT CHARSET=utf8 COMMENT='晒娃活动';



CREATE TABLE `sfy_baobao_vote` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'pk',
  `openid` varchar(28) NOT NULL DEFAULT '',
  `bb_id` int(11) NOT NULL DEFAULT '0' COMMENT '宝宝id',
  `create_at` int(10) NOT NULL DEFAULT '0',
  `update_at` int(10) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0正常1删除',
  PRIMARY KEY (`id`),
  KEY `openid` (`openid`,`bb_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='晒娃投票记录';


