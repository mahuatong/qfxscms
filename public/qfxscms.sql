/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50737
Source Host           : localhost:3306
Source Database       : xiaoshuo

Target Server Type    : MYSQL
Target Server Version : 50737
File Encoding         : 65001

Date: 2022-10-26 09:37:00
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ky_ad
-- ----------------------------
DROP TABLE IF EXISTS `ky_ad`;
CREATE TABLE `ky_ad` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) DEFAULT NULL,
  `value` text,
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '数据状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='广告';

-- ----------------------------
-- Records of ky_ad
-- ----------------------------
INSERT INTO `ky_ad` VALUES ('2', '2', '2', '1', '1666603359', '1666603364');

-- ----------------------------
-- Table structure for ky_addons
-- ----------------------------
DROP TABLE IF EXISTS `ky_addons`;
CREATE TABLE `ky_addons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL COMMENT '插件名或标识',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '中文名',
  `description` text COMMENT '插件描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `config` text COMMENT '配置',
  `author` varchar(40) DEFAULT '' COMMENT '作者',
  `version` varchar(20) DEFAULT '' COMMENT '版本号',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `has_hook` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否钩子插件',
  `group` varchar(30) DEFAULT NULL COMMENT '配置分组',
  `mold` varchar(50) NOT NULL DEFAULT '' COMMENT '类型',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `exclusive` tinyint(1) unsigned DEFAULT '0' COMMENT '排他',
  `nav_display` tinyint(1) unsigned DEFAULT '0' COMMENT '导航显示',
  PRIMARY KEY (`id`),
  KEY `group` (`group`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='插件表';

-- ----------------------------
-- Records of ky_addons
-- ----------------------------
INSERT INTO `ky_addons` VALUES ('1', 'SendEmail', '发送Email', '发送Email接口', '1', '{\"mail_host\":\"\",\"mail_port\":\"\",\"mail_ssl\":\"0\",\"mail_smtpauth\":\"1\",\"mail_username\":\"\",\"mail_password\":\"\",\"mail_fromname\":\"\",\"mail_from\":\"\",\"mail_ishtml\":\"1\",\"mail_charset\":\"utf-8\",\"tpl_reg\":\"<div class=\\\"wrapper\\\" style=\\\"margin: 20px auto 0; width: 500px; padding-top:16px; padding-bottom:10px;\\\"><br style=\\\"clear:both; height:0\\\"><div class=\\\"content\\\" style=\\\"background: none repeat scroll 0 0 #FFFFFF; border: 1px solid #E9E9E9; margin: 2px 0 0; padding: 30px;\\\"><p>\\u60a8\\u597d: <\\/p><p>\\u611f\\u8c22\\u60a8\\u6ce8\\u518c <a href=\\\"{$web_url}\\\">{$web_name}<\\/a><\\/p><p style=\\\"border-top: 1px solid #DDDDDD;margin: 15px 0 25px;padding: 15px;\\\">\\u9a8c\\u8bc1\\u7801: <span style=\\\"color:red\\\">{$code}<\\/span><\\/p><p style=\\\"border-top: 1px solid #DDDDDD; padding-top:6px; margin-top:25px; color:#838383;\\\"><p>\\u8bf7\\u52ff\\u56de\\u590d\\u672c\\u90ae\\u4ef6, \\u6b64\\u90ae\\u7bb1\\u672a\\u53d7\\u76d1\\u63a7, \\u60a8\\u4e0d\\u4f1a\\u5f97\\u5230\\u4efb\\u4f55\\u56de\\u590d\\u3002<\\/p><\\/p><\\/div><\\/div>\",\"tpl_passw\":\"<div class=\\\"wrapper\\\" style=\\\"margin: 20px auto 0; width: 500px; padding-top:16px; padding-bottom:10px;\\\"><br style=\\\"clear:both; height:0\\\"><div class=\\\"content\\\" style=\\\"background: none repeat scroll 0 0 #FFFFFF; border: 1px solid #E9E9E9; margin: 2px 0 0; padding: 30px;\\\"><p>\\u60a8\\u6b63\\u5728\\u8fdb\\u884c<a href=\\\"{$web_url}\\\">{$web_name}<\\/a>\\u5bc6\\u7801\\u627e\\u56de<\\/p><p style=\\\"border-top: 1px solid #DDDDDD;margin: 15px 0 25px;padding: 15px;\\\">\\u9a8c\\u8bc1\\u7801: <span style=\\\"color:red\\\">{$code}<\\/span><\\/p><p style=\\\"border-top: 1px solid #DDDDDD; padding-top:6px; margin-top:25px; color:#838383;\\\"><p>\\u8bf7\\u52ff\\u56de\\u590d\\u672c\\u90ae\\u4ef6, \\u6b64\\u90ae\\u7bb1\\u672a\\u53d7\\u76d1\\u63a7, \\u60a8\\u4e0d\\u4f1a\\u5f97\\u5230\\u4efb\\u4f55\\u56de\\u590d\\u3002<\\/p><\\/p><\\/div><\\/div>\",\"tpl_bind\":\"<div class=\\\"wrapper\\\" style=\\\"margin: 20px auto 0; width: 500px; padding-top:16px; padding-bottom:10px;\\\"><br style=\\\"clear:both; height:0\\\"><div class=\\\"content\\\" style=\\\"background: none repeat scroll 0 0 #FFFFFF; border: 1px solid #E9E9E9; margin: 2px 0 0; padding: 30px;\\\"><p>\\u60a8\\u6b63\\u5728\\u8fdb\\u884c<a href=\\\"{$web_url}\\\">{$web_name}<\\/a>\\u7ed1\\u5b9a\\u90ae\\u7bb1<\\/p><p style=\\\"border-top: 1px solid #DDDDDD;margin: 15px 0 25px;padding: 15px;\\\">\\u9a8c\\u8bc1\\u7801: <span style=\\\"color:red\\\">{$code}<\\/span><\\/p><p style=\\\"border-top: 1px solid #DDDDDD; padding-top:6px; margin-top:25px; color:#838383;\\\"><p>\\u8bf7\\u52ff\\u56de\\u590d\\u672c\\u90ae\\u4ef6, \\u6b64\\u90ae\\u7bb1\\u672a\\u53d7\\u76d1\\u63a7, \\u60a8\\u4e0d\\u4f1a\\u5f97\\u5230\\u4efb\\u4f55\\u56de\\u590d\\u3002<\\/p><\\/p><\\/div><\\/div>\"}', 'kyxscms', '1.0.2', '1548661037', '0', 'email', 'web,wap,wechat', '0', '0', '0');

-- ----------------------------
-- Table structure for ky_bookshelf
-- ----------------------------
DROP TABLE IF EXISTS `ky_bookshelf`;
CREATE TABLE `ky_bookshelf` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `novel_id` int(11) unsigned NOT NULL DEFAULT '0',
  `chapter_id` int(11) unsigned NOT NULL DEFAULT '0',
  `chapter_key` char(20) DEFAULT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '数据状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户书架';

-- ----------------------------
-- Records of ky_bookshelf
-- ----------------------------

-- ----------------------------
-- Table structure for ky_category
-- ----------------------------
DROP TABLE IF EXISTS `ky_category`;
CREATE TABLE `ky_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `meta_title` varchar(50) NOT NULL DEFAULT '' COMMENT 'SEO的网页标题',
  `meta_keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字',
  `meta_description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `template_index` varchar(100) DEFAULT NULL COMMENT '频道页模板',
  `template_detail` varchar(100) DEFAULT NULL COMMENT '详情页模板',
  `template_filter` varchar(100) DEFAULT NULL COMMENT '筛选页模板',
  `link` varchar(255) NOT NULL DEFAULT '' COMMENT '外链地址',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '数据状态',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '分类模型',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='分类表';

-- ----------------------------
-- Records of ky_category
-- ----------------------------
INSERT INTO `ky_category` VALUES ('4', '男生', '0', '1', '', '', '', '', 'type.html', 'novel.html', 'lists.html', '', '1440469030', '1539841781', '1', '0');
INSERT INTO `ky_category` VALUES ('8', '女生', '0', '2', '', '', '', '', 'type.html', 'novel.html', 'lists.html', '', '1450770206', '1536657804', '1', '0');
INSERT INTO `ky_category` VALUES ('18', '奇幻玄幻', '4', '0', '', '', '', '', 'lists.html', 'novel.html', 'lists.html', '', '1536657923', '1536657923', '1', '0');
INSERT INTO `ky_category` VALUES ('19', '武侠仙侠', '4', '0', '', '', '', '', 'lists.html', 'novel.html', 'lists.html', '', '1536657995', '1536657995', '1', '0');
INSERT INTO `ky_category` VALUES ('20', '历史军事', '4', '0', '', '', '', '', 'lists.html', 'novel.html', 'lists.html', '', '1536658006', '1536658006', '1', '0');
INSERT INTO `ky_category` VALUES ('21', '都市娱乐', '4', '0', '', '', '', '', 'lists.html', 'novel.html', 'lists.html', '', '1536658015', '1536658015', '1', '0');
INSERT INTO `ky_category` VALUES ('22', '科幻末日', '4', '0', '', '', '', '', 'lists.html', 'novel.html', 'lists.html', '', '1536658027', '1536658027', '1', '0');
INSERT INTO `ky_category` VALUES ('23', '悬疑灵异', '4', '0', '', '', '', '', 'lists.html', 'novel.html', 'lists.html', '', '1536658034', '1536995233', '1', '0');
INSERT INTO `ky_category` VALUES ('25', '古装言情', '8', '0', '', '', '', '', 'lists.html', 'novel.html', 'lists.html', '', '1536658137', '1536658137', '1', '0');
INSERT INTO `ky_category` VALUES ('26', '都市言情', '8', '0', '', '', '', '', 'lists.html', 'novel.html', 'lists.html', '', '1536658146', '1536658146', '1', '0');
INSERT INTO `ky_category` VALUES ('27', '浪漫青春', '8', '0', '', '', '', '', 'lists.html', 'novel.html', 'lists.html', '', '1536658154', '1536658154', '1', '0');
INSERT INTO `ky_category` VALUES ('28', '幻想言情', '8', '0', '', '', '', '', 'lists.html', 'novel.html', 'lists.html', '', '1536658169', '1536658169', '1', '0');
INSERT INTO `ky_category` VALUES ('31', '文章', '0', '5', '', '', '', '', 'newslists.html', 'news.html', 'lists.html', '', '1537596272', '1548416358', '1', '1');
INSERT INTO `ky_category` VALUES ('34', '游戏竞技', '4', '0', '', '', '', '', 'lists.html', 'novel.html', 'lists.html', '', '1539845119', '1539845119', '1', '0');
INSERT INTO `ky_category` VALUES ('35', '其他', '4', '0', '', '', '', '', 'lists.html', 'novel.html', 'lists.html', '', '1539845177', '1539845177', '1', '0');
INSERT INTO `ky_category` VALUES ('37', '科幻空间', '8', '0', '', '', '', '', 'lists.html', 'novel.html', 'lists.html', '', '1548402683', '1548402683', '1', '0');
INSERT INTO `ky_category` VALUES ('38', '灵异悬疑', '8', '0', '', '', '', '', 'lists.html', 'novel.html', 'lists.html', '', '1548402695', '1548402695', '1', '0');
INSERT INTO `ky_category` VALUES ('39', '同人衍生', '8', '0', '', '', '', '', 'lists.html', 'novel.html', 'lists.html', '', '1548402705', '1548402705', '1', '0');
INSERT INTO `ky_category` VALUES ('40', '耽美百合', '8', '0', '', '', '', '', 'lists.html', 'novel.html', 'lists.html', '', '1548402718', '1548402718', '1', '0');
INSERT INTO `ky_category` VALUES ('41', '排行', '0', '4', '', '', '', '', 'rank.html', 'rank.html', 'lists.html', '', '1548416346', '1548416366', '1', '2');

-- ----------------------------
-- Table structure for ky_collect
-- ----------------------------
DROP TABLE IF EXISTS `ky_collect`;
CREATE TABLE `ky_collect` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) DEFAULT NULL,
  `charset` char(20) DEFAULT NULL COMMENT '网站编码',
  `type` char(20) DEFAULT NULL COMMENT '类型',
  `url_complete` tinyint(1) DEFAULT '0' COMMENT '网址补全',
  `url_reverse` tinyint(1) DEFAULT '0' COMMENT '倒序采集',
  `pic_local` tinyint(1) DEFAULT '0' COMMENT '图片本地化',
  `source_url` text COMMENT '列表地址',
  `section` text COMMENT '列表区间',
  `url_rule` text COMMENT '网址规则',
  `url_merge` varchar(255) DEFAULT NULL COMMENT '拼接网址',
  `url_must` varchar(255) DEFAULT NULL COMMENT '必须包含',
  `url_ban` varchar(255) DEFAULT NULL COMMENT '不能包含',
  `relation_url` text COMMENT '关联url',
  `rule` text COMMENT '规则',
  `category_way` tinyint(2) DEFAULT '0' COMMENT '入库方式',
  `category_fixed` int(11) DEFAULT '0' COMMENT '固定分类',
  `category_equivalents` text COMMENT '栏目转换',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '数据状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `collect_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '采集时间',
  `update` tinyint(3) DEFAULT '0',
  `is_auto` tinyint(1) DEFAULT '0' COMMENT '1自动采集',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10011 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='自定义采集';

-- ----------------------------
-- Records of ky_collect
-- ----------------------------
INSERT INTO `ky_collect` VALUES ('1', 'http://www.shuquge.com/', 'auto', 'novel', '1', '0', '0', '[{\"url\":\"http://www.shuquge.com/category/1_[内容].html\",\"type\":\"1\",\"param\":[\"1\",\"1227\",\"1\",0]},{\"url\":\"http://www.shuquge.com/category/2_[内容].html\",\"type\":\"1\",\"param\":[\"1\",\"317\",\"1\",0]},{\"url\":\"http://www.shuquge.com/category/3_[内容].html\",\"type\":\"1\",\"param\":[\"1\",\"853\",\"1\",0]},{\"url\":\"http://www.shuquge.com/category/4_[内容].html\",\"type\":\"1\",\"param\":[\"1\",\"230\",\"1\",0]},{\"url\":\"http://www.shuquge.com/category/7_[内容].html\",\"type\":\"1\",\"param\":[\"1\",\"308\",\"1\",0]}]', '<body>[内容]</body>', '<li><span class=\"s1\">(*)</span><span class=\"s2\"><a href=\"[内容1]\">(*)</a></span>', '', '', '', '[{\"title\":\"章节页\",\"page\":\"default\",\"chapter\":\"1\",\"section\":\"<dt>(*)正文</dt>[内容]</div>\",\"url_rule\":\"<dd><a href=\\\"[内容1]\\\">[章节标题]</a></dd>\",\"url_merge\":\"\"}]', '{\"category\":{\"field\":\"category\",\"source\":\"default\",\"rule\":\"<meta property=\\\"og:novel:category\\\" content=\\\"[\\u5185\\u5bb91]\\\" \\/> \",\"merge\":\"\",\"strip\":\"\"},\"title\":{\"field\":\"title\",\"source\":\"default\",\"rule\":\"<meta property=\\\"og:novel:book_name\\\" content=\\\"[\\u5185\\u5bb91]\\\" \\/> \",\"merge\":\"\",\"strip\":\"\",\"replace\":\"\"},\"author\":{\"field\":\"author\",\"source\":\"default\",\"rule\":\"<meta property=\\\"og:novel:author\\\" content=\\\"[\\u5185\\u5bb91]\\\" \\/> \",\"merge\":\"\",\"strip\":\"\",\"replace\":\"\"},\"serialize\":{\"field\":\"serialize\",\"source\":\"default\",\"rule\":\"<meta property=\\\"og:novel:status\\\" content=\\\"[\\u5185\\u5bb91]\\\" \\/>\",\"merge\":\"\",\"serial\":\"\\u8fde\\u8f7d\\u4e2d\",\"over\":\"\\u5b8c\\u7ed3\",\"strip\":\"\",\"replace\":\"\"},\"pic\":{\"field\":\"pic\",\"source\":\"default\",\"rule\":\" <meta property=\\\"og:image\\\" content=\\\"[\\u5185\\u5bb91]\\\" \\/> \",\"merge\":\"\",\"strip\":\"\",\"replace\":\"\"},\"content\":{\"field\":\"content\",\"source\":\"default\",\"rule\":\" <meta property=\\\"og:description\\\" content=\\\" [\\u5185\\u5bb91]\\\" \\/> \",\"merge\":\"\",\"strip\":\"\",\"replace\":\"\"},\"tag\":{\"field\":\"tag\",\"source\":\"default\",\"rule\":\"<meta property=\\\"og:title\\\" content=\\\"[\\u5185\\u5bb91]\\\" \\/> \",\"merge\":\"\",\"strip\":\"\",\"replace\":\"\"},\"chapter_title\":{\"field\":\"chapter_title\",\"source\":\"0\",\"rule\":\"<h1>[\\u5185\\u5bb91]<\\/h1>\",\"merge\":\"\",\"strip\":\"\",\"replace\":\"\"},\"chapter_content\":{\"field\":\"chapter_content\",\"source\":\"0\",\"rule\":\"<div id=\\\"content\\\" class=\\\"showtxt\\\">[\\u5185\\u5bb91]<br \\/>\",\"merge\":\"\",\"strip\":\"\",\"replace\":\"[{\\\"find\\\":\\\"<br\\/>\\\",\\\"replaces\\\":\\\"\\\"}]\"}}', '0', '0', '[{\"target\":\"玄幻魔法\",\"local\":\"18\"},{\"target\":\"武侠修真\",\"local\":\"19\"},{\"target\":\"都市言情\",\"local\":\"21\"},{\"target\":\"历史军事\",\"local\":\"20\"},{\"target\":\"科幻灵异\",\"local\":\"22\"}]', '1', '1586771330', '1666659213', '1666748220', '0', '1');
INSERT INTO `ky_collect` VALUES ('2', 'huanyue', 'auto', 'novel', '1', '0', '0', '[{\"url\":\"http://www.huanyue123.com/book/xuanhuan/default-0-0-0-0-0-0-[内容].html\",\"type\":\"1\",\"param\":[\"1\",\"250\",\"1\",0]},{\"url\":\"http://www.huanyue123.com/book/xianxia/default-0-0-0-0-0-0-[内容].html\",\"type\":\"1\",\"param\":[\"1\",\"200\",\"1\",0]},{\"url\":\"http://www.huanyue123.com/book/dushi/default-0-0-0-0-0-0-[内容].html\",\"type\":\"1\",\"param\":[\"1\",\"150\",\"1\",0]},{\"url\":\"http://www.huanyue123.com/book/lishi/default-0-0-0-0-0-0-[内容].html\",\"type\":\"1\",\"param\":[\"1\",\"150\",\"1\",0]},{\"url\":\"http://www.huanyue123.com/book/kehuan/default-0-0-0-0-0-0-[内容].html\",\"type\":\"1\",\"param\":[\"1\",\"100\",\"1\",0]},{\"url\":\"http://www.huanyue123.com/book/yanqing/default-0-0-0-0-0-0-[内容].html\",\"type\":\"1\",\"param\":[\"1\",\"50\",\"1\",0]},{\"url\":\"http://www.huanyue123.com/book/quanbu/default-0-0-0-0-2-0-[内容].html\",\"type\":\"1\",\"param\":[\"1\",\"50\",\"1\",0]}]', '<body>[内容]</body>', '<dt><a href=\"[内容1]\" target=\"_blank\">(*)</a></dt>', '', '', '', '[{\"title\":\"章节页\",\"page\":\"default\",\"chapter\":\"1\",\"section\":\" <div class=\\\"book_list\\\">[内容]</div>\\n\",\"url_rule\":\"<li><a href=\\\"[内容1]\\\">[章节标题]</a></li>\",\"url_merge\":\"\"}]', '{\"category\":{\"field\":\"category\",\"source\":\"default\",\"rule\":\"<meta property=\\\"og:novel:category\\\" content=\\\"[内容1]\\\"\\/>\",\"merge\":\"\",\"strip\":\"\"},\"title\":{\"field\":\"title\",\"source\":\"default\",\"rule\":\"<meta property=\\\"og:novel:book_name\\\" content=\\\"[内容1]\\\"\\/>\\n\",\"merge\":\"\",\"strip\":\"\",\"replace\":\"\"},\"author\":{\"field\":\"author\",\"source\":\"default\",\"rule\":\"<meta property=\\\"og:novel:author\\\" content=\\\"[内容1]\\\"\\/>\",\"merge\":\"\",\"strip\":\"\",\"replace\":\"\"},\"serialize\":{\"field\":\"serialize\",\"source\":\"default\",\"rule\":\"<meta property=\\\"og:novel:status\\\" content=\\\"[内容1]\\\"\\/>\\n\",\"merge\":\"\",\"serial\":\"连载中\",\"over\":\"已完成\",\"strip\":\"\",\"replace\":\"\"},\"pic\":{\"field\":\"pic\",\"source\":\"default\",\"rule\":\"<meta property=\\\"og:image\\\" content=[内容1]\\\"\\/>\\n\",\"merge\":\"\",\"strip\":\"\",\"replace\":\"\"},\"content\":{\"field\":\"content\",\"source\":\"default\",\"rule\":\"<meta property=\\\"og:description\\\" content=\\\"    [内容1]<br \\/>\",\"merge\":\"\",\"strip\":\"\",\"replace\":\"\"},\"tag\":{\"field\":\"tag\",\"source\":\"default\",\"rule\":\"<meta property=\\\"og:title\\\" content=\\\"[内容1]\\\"\\/>\\n\",\"merge\":\"\",\"strip\":\"\",\"replace\":\"\"},\"chapter_title\":{\"field\":\"chapter_title\",\"source\":\"default\",\"rule\":\"<h1>[内容1]<\\/h1>\\n\",\"merge\":\"\",\"strip\":\"\",\"replace\":\"\"},\"chapter_content\":{\"field\":\"chapter_content\",\"source\":\"0\",\"rule\":\"<div id=\\\"htmlContent\\\" class=\\\"contentbox clear\\\">(*)<\\/a><\\/div>[内容1]<\\/div>\\n\\n\",\"merge\":\"\",\"strip\":\"\",\"replace\":\"\"}}', '1', '18', '[{\"target\":\"玄幻小说\",\"local\":\"18\"},{\"target\":\"仙侠小说\",\"local\":\"19\"},{\"target\":\"都市小说\",\"local\":\"21\"},{\"target\":\"军史小说\",\"local\":\"20\"},{\"target\":\"科幻小说\",\"local\":\"22\"},{\"target\":\"言情小说\",\"local\":\"8\"}]', '1', '1615016982', '1615021500', '1666242717', '0', '0');

-- ----------------------------
-- Table structure for ky_comment
-- ----------------------------
DROP TABLE IF EXISTS `ky_comment`;
CREATE TABLE `ky_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `create_time` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `up` int(11) unsigned DEFAULT '0',
  `pid` int(11) unsigned DEFAULT '0',
  `mid` int(11) unsigned DEFAULT '0',
  `type` char(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='评论表';

-- ----------------------------
-- Records of ky_comment
-- ----------------------------

-- ----------------------------
-- Table structure for ky_config
-- ----------------------------
DROP TABLE IF EXISTS `ky_config`;
CREATE TABLE `ky_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '配置说明',
  `group` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置分组',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '配置值',
  `remark` varchar(100) NOT NULL COMMENT '配置说明',
  `value` text NOT NULL COMMENT '配置值',
  `sort` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `display` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '可见性',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `group` (`group`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=116 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='配置';

-- ----------------------------
-- Records of ky_config
-- ----------------------------
INSERT INTO `ky_config` VALUES ('2', 'meta_description', '2', 'SEO描述', '1', '', '网站搜索引擎描述', '清风小说，可以免费看的小说站点', '6', '1');
INSERT INTO `ky_config` VALUES ('3', 'meta_keywords', '2', 'SEO关键字', '1', '', '网站搜索引擎关键字', '清风小说 免费小说 小说在线 app小说 番茄小说 顶点小说 h5小说 ', '5', '1');
INSERT INTO `ky_config` VALUES ('4', 'close', '4', '站点状态', '1', '0:关闭\r\n1:开启', '站点关闭后不能访问', '1', '8', '1');
INSERT INTO `ky_config` VALUES ('5', 'icp', '1', '网站备案号', '1', '', '设置在网站底部显示的备案号，如“沪ICP备12007941号-2', '', '7', '1');
INSERT INTO `ky_config` VALUES ('6', 'document_position', '3', '文档推荐位', '2', '', '文档推荐位，推荐到多个位置KEY值相加即可', '1:列表推荐\r\n2:频道推荐\r\n4:首页推荐', '3', '0');
INSERT INTO `ky_config` VALUES ('7', 'close_tip', '2', '关闭提示', '1', '', '', '网站维护中，请稍后访问。', '9', '1');
INSERT INTO `ky_config` VALUES ('13', 'config_group_list', '3', '配置分组', '4', '', '配置分组', '1:基本\r\n2:内容\r\n3:用户\r\n4:备份\r\n5:附件\r\n6:api', '4', '0');
INSERT INTO `ky_config` VALUES ('14', 'list_rows', '0', '后台记录数', '2', '', '后台数据每页显示记录数', '30', '0', '1');
INSERT INTO `ky_config` VALUES ('15', 'user_allow_register', '4', '注册开关', '3', '0:关闭\r\n1:开启', '是否开放用户注册', '0', '1', '1');
INSERT INTO `ky_config` VALUES ('17', 'data_backup_path', '0', '根路径', '4', '', '路径必须以 / 结尾', './public/database/', '1', '1');
INSERT INTO `ky_config` VALUES ('18', 'data_backup_part_size', '0', '份卷大小', '4', '', '该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M', '20971520', '2', '1');
INSERT INTO `ky_config` VALUES ('19', 'data_backup_compress', '4', '启用压缩', '4', '0:不压缩\r\n1:压缩', '压缩备份文件需要PHP环境支持gzopen,gzwrite函数', '1', '3', '1');
INSERT INTO `ky_config` VALUES ('20', 'data_backup_compress_level', '6', '压缩级别', '4', '1:普通\r\n4:一般\r\n9:最高', '数据库备份文件的压缩级别，该配置在开启压缩时生效', '9', '4', '1');
INSERT INTO `ky_config` VALUES ('22', 'url', '1', '网站地址', '1', '', '网站域名地址', 'http://xs.hs.cn', '2', '1');
INSERT INTO `ky_config` VALUES ('23', 'meta_title', '1', '网站名称', '1', '', '网站名称', '清风小说1', '1', '1');
INSERT INTO `ky_config` VALUES ('24', 'logo', '7', '网站logo', '1', '', '', '/public/storage/20221025/eed01730d553bb602708f5fb5394ea26.png', '3', '1');
INSERT INTO `ky_config` VALUES ('29', 'default_tpl', '1', '模板目录', '1', '', '', 'home', '0', '0');
INSERT INTO `ky_config` VALUES ('32', 'search_on', '4', '搜索开关', '2', '0:关闭\r\n1:开启', '搜索开关', '1', '1', '1');
INSERT INTO `ky_config` VALUES ('33', 'search_timespan', '0', '搜索间隔', '2', '', '单位秒，建议设置为3秒以上', '3', '2', '1');
INSERT INTO `ky_config` VALUES ('76', 'user_model_status', '4', '会员模块', '3', '0:关闭\r\n1:开启', '是否开启会员模块', '0', '0', '1');
INSERT INTO `ky_config` VALUES ('77', 'user_reg_status', '4', '注册状态', '3', '0:未审\r\n1:已审', '', '1', '2', '0');
INSERT INTO `ky_config` VALUES ('78', 'user_reg_verify', '4', '注册验证码', '3', '0:关闭\r\n1:开启', '', '1', '3', '1');
INSERT INTO `ky_config` VALUES ('79', 'user_login_verify', '4', '登录验证码', '3', '0:关闭\r\n1:开启', '', '1', '4', '1');
INSERT INTO `ky_config` VALUES ('80', 'user_reg_integral', '0', '默认积分', '3', '', '', '0', '5', '1');
INSERT INTO `ky_config` VALUES ('82', 'data_cache', '4', '数据缓存', '2', '0:关闭\r\n1:开启', '', '1', '3', '1');
INSERT INTO `ky_config` VALUES ('83', 'html_cache', '4', '页面缓存', '2', '0:关闭\r\n1:开启', '', '0', '5', '0');
INSERT INTO `ky_config` VALUES ('88', 'upload_path', '0', '上传目录', '5', '', '', './uploads/', '0', '1');
INSERT INTO `ky_config` VALUES ('89', 'filter_size', '3', '字数筛选', '2', '', '每行一个使用:分割开前面为参数后面为名称', '< 300000:30万字以下\nbetween 300000,500000:30-50万字\nbetween 500000,1000000:50-100万字\nbetween 1000000,2000000:100-200万字\n> 2000000:200万字以上', '5', '1');
INSERT INTO `ky_config` VALUES ('90', 'filter_serialize', '3', '连载筛选', '2', '', '每行一个使用:分割开前面为参数后面为名称', '0:连载\n1:完本', '6', '1');
INSERT INTO `ky_config` VALUES ('91', 'filter_update', '3', '更新筛选', '2', '', '每行一个使用:分割开前面为参数后面为名称', '-3 day:三日内\nweek:七日内\n-15 day:半月内\nmonth:一月内', '7', '1');
INSERT INTO `ky_config` VALUES ('92', 'official_url', '1', '官网地址', '6', '', '', 'http://www.kyxscms.com', '1', '0');
INSERT INTO `ky_config` VALUES ('93', 'version', '0', '系统版本', '1', '', '', '1.5.2', '10', '1');
INSERT INTO `ky_config` VALUES ('94', 'client_id', '0', '客户ID', '0', '', '', 'c6f15c31c16ca1fd580069c5616324b8', '0', '0');
INSERT INTO `ky_config` VALUES ('95', 'client_secret', '0', '客户SECRET', '0', '', '', '65c40bf6477b4c63a9e0cbeef39f938c', '0', '0');
INSERT INTO `ky_config` VALUES ('96', 'uinon_collect_chapter_save', '4', '章节保存', '7', '0:访问保存\r\n1:采集保存', '建议使用访问保存加快采集速度', '0', '0', '0');
INSERT INTO `ky_config` VALUES ('97', 'union_collect_pic_save', '4', '图片保存', '7', '0:关闭\r\n1:开启', '是否将图片保存到本地', '1', '0', '1');
INSERT INTO `ky_config` VALUES ('98', 'union_collect_update_novel', '5', '小说更新', '7', 'title:名称\r\ncategory:分类\r\nauthor:作者\r\npic:图片\r\ntag:标签\r\nrating:总评分\r\nrating_count:总评次\r\ncontent:介绍\r\nword:字数\r\nserialize:连载', '', 'tag,rating,rating_count,word,serialize', '0', '1');
INSERT INTO `ky_config` VALUES ('99', 'union_collect_field', '2', '联盟数据字段', '7', '', '', '{\"novel\":[\"title\",\"category\",\"author\",\"pic\",\"content\",\"tag\",\"rating\",\"rating_count\",\"serialize\",\"create_time\",\"update_time\",\"word\"],\"news\":[\"title\",\"category\",\"pic\",\"content\"]}', '0', '0');
INSERT INTO `ky_config` VALUES ('100', 'union_collect_chapter_page', '0', '章节分页', '7', '', '章节分页采集数量', '1000', '0', '0');
INSERT INTO `ky_config` VALUES ('101', 'comment_key', '2', '评论敏感词', '2', '', '评论敏感词,每行一个', 'www\nhttp://\n.com', '8', '1');
INSERT INTO `ky_config` VALUES ('102', 'collect_sleep', '0', '采集间隔', '8', '', '采集每个地址等待时间，单位为秒', '0', '0', '1');
INSERT INTO `ky_config` VALUES ('103', 'collect_thread_num', '0', '线程数量', '8', '', '采集线程数量，根据自身服务器和网络情况设置', '5', '0', '1');
INSERT INTO `ky_config` VALUES ('105', 'data_save_compress', '4', '数据压缩', '2', '0:关闭\r\n1:开启', '是否启用数据压缩，在添加小说数据前修改，有了小说数据后不要修改否则会到导致出错', '1', '2', '1');
INSERT INTO `ky_config` VALUES ('106', 'data_save_compress_level', '6', '数据压缩等级', '2', '1:普通\r\n4:一般\r\n9:最高', '', '4', '2', '1');
INSERT INTO `ky_config` VALUES ('107', 'union_collect_thread_num', '0', '线程数量', '7', '', '采集线程数量，根据自身服务器和网络情况设置', '5', '0', '1');
INSERT INTO `ky_config` VALUES ('108', 'wap_url', '1', '手机域名', '1', '', '网站手机访问域名,无特定域名为空就可以了,域名设置例如 m.xsz.com', 'http://xs.qfxscms.cn', '2', '1');
INSERT INTO `ky_config` VALUES ('109', 'chapter_txt', '4', '章节保存', '8', '0:关闭\r\n1:开启', '保存章节内容，关闭将每次从采集源站获取', '1', '2', '1');
INSERT INTO `ky_config` VALUES ('110', 'chapter_time_interval', '0', '章节刷新间隔', '8', '', '单位分钟，章节列表自动更新间隔，间隔时间不要太短', '20', '3', '1');
INSERT INTO `ky_config` VALUES ('111', 'chapter_time_interval_over', '0', '章节刷新成功间隔', '8', '', '单位分钟，章节列表自动更新成功后间隔，间隔时间不要太短', '120', '4', '1');
INSERT INTO `ky_config` VALUES ('112', 'chapter_preloading_num', '0', '章节预加载', '8', '', '单位章，章节阅读器会预采集后面章节内容', '3', '5', '1');
INSERT INTO `ky_config` VALUES ('113', 'api_key', '0', 'api调用key', '6', '', 'api调用的钥密', '', '0', '1');
INSERT INTO `ky_config` VALUES ('114', 'login_reader', '4', '登录阅读', '3', '0:关闭\r\n1:开启', '登录后才可以阅读', '0', '6', '1');
INSERT INTO `ky_config` VALUES ('115', 'login_reader_num', '0', '登录阅读数量', '3', '', '阅读同一小说多少章后提示登录阅读，0为登录阅读', '9999', '7', '1');

-- ----------------------------
-- Table structure for ky_crontab
-- ----------------------------
DROP TABLE IF EXISTS `ky_crontab`;
CREATE TABLE `ky_crontab` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(255) DEFAULT NULL,
  `interval` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '时间间隔',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '数据状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `run_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '运行时间',
  `display` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '可见性',
  `type` tinyint(2) DEFAULT '0',
  `relation_id` int(11) unsigned DEFAULT NULL,
  `class_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='定时任务';

-- ----------------------------
-- Records of ky_crontab
-- ----------------------------
INSERT INTO `ky_crontab` VALUES ('1', '{\"url\":\"admin\\/Check\",\"layer\":\"index\",\"vars\":\"\"}', '86400', '1', '1666237602', '1666237602', '1666324834', '0', '2', null, 'admin/Check');
INSERT INTO `ky_crontab` VALUES ('2', '{\"url\":\"api\\/Oauthcall\",\"layer\":\"check_order\",\"vars\":\"\"}', '86400', '1', '1666237602', '1666237602', '1668830434', '0', '1', null, 'api/Oauthcall');

-- ----------------------------
-- Table structure for ky_link
-- ----------------------------
DROP TABLE IF EXISTS `ky_link`;
CREATE TABLE `ky_link` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `sort` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '数据状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of ky_link
-- ----------------------------

-- ----------------------------
-- Table structure for ky_member
-- ----------------------------
DROP TABLE IF EXISTS `ky_member`;
CREATE TABLE `ky_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` char(16) DEFAULT NULL COMMENT '用户名',
  `password` char(32) DEFAULT NULL COMMENT '密码',
  `login` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `last_login_ip` varchar(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '用户状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='管理表';

-- ----------------------------
-- Records of ky_member
-- ----------------------------
INSERT INTO `ky_member` VALUES ('1', 'admin', 'b7a1b6924b9ed22c4255747445dccf12', '2', '127.0.0.1', '1666260403', '1');
INSERT INTO `ky_member` VALUES ('3', '962464', '65c40bf6477b4c63a9e0cbeef39f938c', '0', '0', '0', '1');

-- ----------------------------
-- Table structure for ky_member_log
-- ----------------------------
DROP TABLE IF EXISTS `ky_member_log`;
CREATE TABLE `ky_member_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `time` int(10) NOT NULL DEFAULT '0',
  `ip` bigint(20) NOT NULL DEFAULT '0',
  `method` char(50) DEFAULT NULL,
  `controller` char(50) DEFAULT NULL,
  `action` char(50) DEFAULT NULL,
  `param` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9393 DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='日志表';

-- ----------------------------
-- Records of ky_member_log
-- ----------------------------

-- ----------------------------
-- Table structure for ky_menu
-- ----------------------------
DROP TABLE IF EXISTS `ky_menu`;
CREATE TABLE `ky_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文档ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `url` char(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `hide` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `tip` varchar(255) NOT NULL DEFAULT '' COMMENT '提示',
  `group` varchar(50) DEFAULT '' COMMENT '分组',
  `icon` varchar(50) DEFAULT NULL COMMENT '图标',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=561 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='菜单';

-- ----------------------------
-- Records of ky_menu
-- ----------------------------
INSERT INTO `ky_menu` VALUES ('1', '管理首页', '0', '0', 'admin/index/index', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('2', '数据管理', '0', '1', 'admin/data/index', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('3', '用户管理', '0', '2', 'admin/data/review', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('4', '数据采集', '0', '3', 'admin/collect/index', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('100', '设置', '1', '1', 'admin/config/index', '0', '', '设置', 'layui-icon layui-icon-set');
INSERT INTO `ky_menu` VALUES ('101', '管理员', '1', '2', 'admin/member/index', '0', '', '管理员', 'layui-icon layui-icon-username');
INSERT INTO `ky_menu` VALUES ('102', '列表', '101', '1', 'admin/member/index', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('103', '添加', '101', '2', 'admin/member/add', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('104', '修改', '101', '3', 'admin/member/password', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('105', '权限管理', '1', '3', 'admin/auth/index', '1', '', '权限', 'layui-icon layui-icon-auz');
INSERT INTO `ky_menu` VALUES ('106', '用户组列表', '105', '1', 'admin/auth/index', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('107', '新增用户组', '105', '2', 'admin/auth/add', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('108', '修改用户组', '105', '3', 'admin/auth/edit', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('109', '访问授权', '105', '4', 'admin/auth/access', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('110', '成员授权', '105', '5', 'admin/auth/user', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('111', '路由管理', '1', '4', 'admin/route/index', '0', '', '路由', 'layui-icon layui-icon-senior');
INSERT INTO `ky_menu` VALUES ('112', '列表', '111', '1', 'admin/route/index', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('113', '修改', '111', '2', 'admin/route/edit', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('114', '备份数据', '1', '5', 'admin/database/index?type=export', '0', '', '数据', 'layui-icon layui-icon-console');
INSERT INTO `ky_menu` VALUES ('115', '还原数据', '1', '6', 'admin/database/index?type=import', '0', '', '数据', '');
INSERT INTO `ky_menu` VALUES ('116', '备份', '114', '1', 'admin/database/export', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('117', '优化', '114', '2', 'admin/database/optimize', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('118', '修复', '114', '3', 'admin/database/repair', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('119', '还原', '115', '1', 'admin/database/import', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('120', '删除', '115', '2', 'admin/database/del', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('121', '用户组删除', '105', '6', 'admin/auth/del', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('122', '删除', '101', '4', 'admin/member/del', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('123', '权限', '101', '5', 'admin/member/group', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('124', '友情链接', '1', '7', 'admin/link/index', '0', '', '链接', 'layui-icon layui-icon-link');
INSERT INTO `ky_menu` VALUES ('125', '列表', '124', '1', 'admin/link/index', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('126', '添加', '124', '2', 'admin/link/add', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('127', '修改', '124', '3', 'admin/link/edit', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('128', '删除', '124', '4', 'admin/link/del', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('130', '列表', '129', '0', 'admin/upgrade/lists', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('132', '操作记录', '1', '3', 'admin/log/index', '0', '', '操作记录', 'layui-icon layui-icon-log');
INSERT INTO `ky_menu` VALUES ('201', '分类管理', '2', '2', 'admin/category/index', '0', '', '分类', 'layui-icon layui-icon-more');
INSERT INTO `ky_menu` VALUES ('202', '列表', '201', '1', 'admin/category/index', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('203', '添加', '201', '2', 'admin/category/add', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('204', '修改', '201', '3', 'admin/category/edit', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('205', '删除', '201', '4', 'admin/category/del', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('206', '移动', '201', '5', 'admin/category/operate?type=move', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('207', '合并', '201', '6', 'admin/category/operate?type=merge', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('208', '小说管理', '2', '3', 'admin/novel/index', '0', '', '小说', 'layui-icon layui-icon-read');
INSERT INTO `ky_menu` VALUES ('209', '列表', '208', '1', 'admin/novel/index', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('210', '添加', '208', '2', 'admin/novel/add', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('211', '修改', '208', '3', 'admin/novel/edit', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('212', '删除', '208', '4', 'admin/novel/del', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('218', '幻灯', '2', '1', 'admin/slider/index', '0', '', '幻灯', 'layui-icon layui-icon-carousel');
INSERT INTO `ky_menu` VALUES ('219', '列表', '218', '1', 'admin/slider/index', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('220', '添加', '218', '2', 'admin/slider/add', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('221', '修改', '218', '3', 'admin/slider/edit', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('222', '删除', '218', '4', 'admin/slider/del', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('233', '用户管理', '3', '0', 'admin/user/index', '0', '', '用户', 'layui-icon layui-icon-user');
INSERT INTO `ky_menu` VALUES ('234', '列表', '233', '0', 'admin/user/index', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('235', '删除', '233', '2', 'admin/user/del', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('236', '修改', '233', '1', 'admin/user/edit', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('238', '修改密码', '233', '4', 'admin/user/password', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('244', '小说评论', '2', '4', 'admin/comment/index?type=novel', '0', '', '小说', '');
INSERT INTO `ky_menu` VALUES ('245', '文章管理', '2', '5', 'admin/news/index', '0', '', '文章', 'layui-icon layui-icon-form');
INSERT INTO `ky_menu` VALUES ('246', '列表', '245', '1', 'admin/news/index', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('247', '添加', '245', '2', 'admin/news/add', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('248', '修改', '245', '3', 'admin/news/edit', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('249', '删除', '245', '4', 'admin/news/del', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('250', '文章评论', '2', '5', 'admin/comment/index?type=news', '0', '', '文章', null);
INSERT INTO `ky_menu` VALUES ('261', '广告管理', '2', '6', 'admin/ad/index', '0', '', '广告', 'layui-icon layui-icon-template');
INSERT INTO `ky_menu` VALUES ('262', '列表', '261', '1', 'admin/ad/index', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('263', '添加', '261', '2', 'admin/ad/add', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('264', '修改', '261', '3', 'admin/ad/edit', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('265', '删除', '261', '4', 'admin/ad/del', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('310', ' 用户组管理', '3', '1', 'admin/user_group/index', '0', '', '用户组', 'layui-icon layui-icon-user');
INSERT INTO `ky_menu` VALUES ('311', '列表', '310', '1', 'admin/user_group/index', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('312', '添加', '310', '2', 'admin/user_group/add', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('313', '修改', '310', '3', 'admin/user_group/edit', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('314', '删除', '310', '4', 'admin/user_group/del', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('401', '采集管理', '4', '0', 'admin/collect/index', '0', '', '采集', 'layui-icon layui-icon-senior');
INSERT INTO `ky_menu` VALUES ('402', '添加', '401', '1', 'admin/collect/add', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('403', '编辑', '401', '2', 'admin/collect/edit', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('404', '删除', '401', '0', 'admin/collect/del', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('405', '采集', '401', '0', 'admin/collect/collect', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('406', '规则测试', '401', '0', 'admin/collect/test', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('408', '添加', '401', '3', 'admin/union_collect/add', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('409', '列表', '401', '4', 'admin/union_collect/lists', '0', '', '', '');
INSERT INTO `ky_menu` VALUES ('410', '编辑', '401', '5', 'admin/union_collect/edit', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('412', '删除', '401', '0', 'admin/union_collect/del', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('413', '采集', '401', '0', 'admin/union_collect/collect', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('414', '绑定分类', '401', '0', 'admin/union_coolect/bind_type', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('416', '采集设置', '4', '2', 'admin/config/index?id=8', '0', '', '采集', null);
INSERT INTO `ky_menu` VALUES ('417', '采集发布', '401', '0', 'admin/collect_union/release', '0', '', '采集', null);
INSERT INTO `ky_menu` VALUES ('502', '安装', '501', '0', 'admin/addons/install', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('503', '设置', '501', '0', 'admin/addons/config', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('504', '列表', '501', '0', 'admin/addons/index', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('505', '启用', '501', '0', 'admin/addons/enable', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('506', '禁用', '501', '0', 'admin/addons/disable', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('510', '安装', '509', '0', 'admin/template/install', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('511', '设置', '509', '0', 'admin/template/config', '0', '', '', null);
INSERT INTO `ky_menu` VALUES ('512', '编辑', '509', '0', 'admin/template/edit', '0', '', '', null);

-- ----------------------------
-- Table structure for ky_news
-- ----------------------------
DROP TABLE IF EXISTS `ky_news`;
CREATE TABLE `ky_news` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属分类',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '名称',
  `pic` varchar(255) DEFAULT NULL COMMENT '封面',
  `content` text COMMENT '介绍',
  `up` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '顶',
  `down` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '踩',
  `hits` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '浏览数量',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `position` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '推荐位',
  `reurl` char(255) DEFAULT NULL COMMENT '来源',
  `template` varchar(100) DEFAULT NULL COMMENT '模板',
  `link` varchar(255) NOT NULL DEFAULT '' COMMENT '外链地址',
  `hits_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '日浏览',
  `hits_week` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '周浏览',
  `hits_month` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '月浏览',
  `hits_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '浏览时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='新闻表';

-- ----------------------------
-- Records of ky_news
-- ----------------------------

-- ----------------------------
-- Table structure for ky_novel
-- ----------------------------
DROP TABLE IF EXISTS `ky_novel`;
CREATE TABLE `ky_novel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属分类',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '名称',
  `author` char(120) DEFAULT NULL COMMENT '作者',
  `pic` varchar(255) DEFAULT NULL COMMENT '图片',
  `content` text COMMENT '说明',
  `tag` varchar(255) DEFAULT NULL COMMENT '标签',
  `up` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '顶',
  `down` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '踩',
  `hits` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '浏览数量',
  `rating` char(10) NOT NULL DEFAULT '0' COMMENT '评分',
  `rating_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '评分人数',
  `serialize` tinyint(2) DEFAULT '0' COMMENT '连载',
  `favorites` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '收藏',
  `position` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '推荐位',
  `template` varchar(100) DEFAULT NULL COMMENT '模板',
  `link` varchar(255) DEFAULT NULL COMMENT '外链地址',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `reurl` char(255) DEFAULT NULL COMMENT '来源',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `hits_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '日浏览',
  `hits_week` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '周浏览',
  `hits_month` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '月浏览',
  `hits_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '浏览时间',
  `word` int(11) DEFAULT '0' COMMENT '字数',
  `recommend` int(11) DEFAULT '0' COMMENT '推荐票',
  `sole` tinyint(2) NOT NULL DEFAULT '3' COMMENT '0：未审核，1：提交审核，2:已审核,3：非独家',
  `author_id` int(11) DEFAULT NULL COMMENT '作者ID',
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `reurl` (`reurl`),
  KEY `update_time` (`update_time`),
  KEY `author` (`author`),
  KEY `hist` (`hits`),
  KEY `serialize` (`serialize`),
  KEY `category` (`category`),
  KEY `up` (`up`),
  KEY `down` (`down`),
  KEY `rating` (`rating`),
  KEY `position` (`position`),
  KEY `status` (`status`),
  KEY `hits_day` (`hits_day`),
  KEY `hits_week` (`hits_week`),
  KEY `hits_month` (`hits_month`),
  KEY `word` (`word`)
) ENGINE=MyISAM AUTO_INCREMENT=44890 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='小说表';

-- ----------------------------
-- Records of ky_novel
-- ----------------------------

-- ----------------------------
-- Table structure for ky_novel_chapter
-- ----------------------------
DROP TABLE IF EXISTS `ky_novel_chapter`;
CREATE TABLE `ky_novel_chapter` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `novel_id` int(11) unsigned NOT NULL DEFAULT '0',
  `source` varchar(255) NOT NULL DEFAULT '' COMMENT '源名称',
  `chapter` longtext COMMENT '内容',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `reurl` char(255) DEFAULT NULL COMMENT '来源',
  `collect_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '采集id',
  `run_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '运行时间',
  `updated` varchar(255) DEFAULT NULL COMMENT '最新内容',
  PRIMARY KEY (`id`),
  KEY `novel_id` (`novel_id`),
  KEY `status` (`status`),
  KEY `collect_id` (`collect_id`)
) ENGINE=MyISAM AUTO_INCREMENT=44874 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='小说章节';

-- ----------------------------
-- Records of ky_novel_chapter
-- ----------------------------

-- ----------------------------
-- Table structure for ky_route
-- ----------------------------
DROP TABLE IF EXISTS `ky_route`;
CREATE TABLE `ky_route` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '配置名称',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '配置说明',
  `group` varchar(30) DEFAULT NULL COMMENT '配置分组',
  `value` text NOT NULL COMMENT '配置值',
  `display` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '可见性',
  `addons` varchar(40) DEFAULT NULL COMMENT '对应插件标识 会进入导航选择',
  PRIMARY KEY (`id`),
  KEY `group` (`group`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='路由';

-- ----------------------------
-- Records of ky_route
-- ----------------------------
INSERT INTO `ky_route` VALUES ('1', '/', '首页', '', '[\"home/index/index\"]', '1', null);
INSERT INTO `ky_route` VALUES ('2', 'lists/:id', '列表', '', '[\"home/lists/index\"]', '1', null);
INSERT INTO `ky_route` VALUES ('3', 'all', '书库', '', '[\"home/lists/lists\"]', '1', null);
INSERT INTO `ky_route` VALUES ('4', 'novel/:id', '小说介绍', '', '[\"home/novel/index\"]', '1', null);
INSERT INTO `ky_route` VALUES ('5', 'book/:id/:key', '阅读器', '', '[\"home/chapter/index\"]', '1', null);
INSERT INTO `ky_route` VALUES ('6', 'news/:id', '文章', '', '[\"home/news/index\"]', '1', null);
INSERT INTO `ky_route` VALUES ('7', 'other/:tpl', '其他', '', '[\"home/other/index\"]', '1', null);
INSERT INTO `ky_route` VALUES ('8', 'search/[:keyword]', '搜索', '', '[\"home/search/index\"]', '1', null);
INSERT INTO `ky_route` VALUES ('9', '/', '首页', '', '[\"home/index/index\"]', '1', null);

-- ----------------------------
-- Table structure for ky_slider
-- ----------------------------
DROP TABLE IF EXISTS `ky_slider`;
CREATE TABLE `ky_slider` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(80) NOT NULL DEFAULT '',
  `type` tinyint(2) DEFAULT '0',
  `picpath` varchar(255) NOT NULL DEFAULT '0',
  `link` varchar(255) DEFAULT '',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='幻灯';

-- ----------------------------
-- Records of ky_slider
-- ----------------------------
INSERT INTO `ky_slider` VALUES ('1', '太古剑尊', '0', 'public/storage/20221025/4acd155954afe2d56b1663ac7e34e5b3.jpg', '/home/novel/index/id/1.html', '0', '1', '1548404324', '1548671504');
INSERT INTO `ky_slider` VALUES ('2', '双面傲妻宠不停一吻情深，双面傲妻宠不停', '0', 'public/storage/20221025/aa2b5e21e331e015d3e0a9844bd955fd.jpg', '/home/novel/index/id/6.html', '0', '1', '1548404455', '1548671511');
INSERT INTO `ky_slider` VALUES ('3', '婚途陌路：冷少，来试爱', '0', 'public/storage/20221025/5009ca0094e937b4fe64a61fb807f5a3.jpg', '/home/novel/index/id/11.html', '0', '1', '1548404474', '1548671518');
INSERT INTO `ky_slider` VALUES ('4', '帝霸苍穹', '0', 'public/storage/20221025/5009ca0094e937b4fe64a61fb807f5a3.jpg', '/home/novel/index/id/17.html', '0', '1', '1548404494', '1548671527');

-- ----------------------------
-- Table structure for ky_template
-- ----------------------------
DROP TABLE IF EXISTS `ky_template`;
CREATE TABLE `ky_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL COMMENT '插件名或标识',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '中文名',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `author` varchar(40) DEFAULT '' COMMENT '作者',
  `version` varchar(20) DEFAULT '' COMMENT '版本号',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `mold` varchar(50) NOT NULL DEFAULT '' COMMENT '类型',
  `default` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='模版表';

-- ----------------------------
-- Records of ky_template
-- ----------------------------
INSERT INTO `ky_template` VALUES ('1', 'default_web', '默认模版WEB', '1', 'kyxscms', '1.0.7', '0', 'web,wap', '1');

-- ----------------------------
-- Table structure for ky_user
-- ----------------------------
DROP TABLE IF EXISTS `ky_user`;
CREATE TABLE `ky_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` char(64) DEFAULT '' COMMENT '用户帐号',
  `email` char(32) NOT NULL DEFAULT '' COMMENT '用户邮箱',
  `password` char(32) NOT NULL COMMENT '密码',
  `sex` int(10) unsigned DEFAULT '0' COMMENT '用户的性别，值为1时是男性，值为2时是女性，值为0时是未知',
  `province` char(20) DEFAULT NULL COMMENT '用户个人资料填写的省份',
  `city` char(20) DEFAULT NULL COMMENT '普通用户个人资料填写的城市',
  `country` char(20) DEFAULT NULL COMMENT '国家，如中国为CN',
  `headimgurl` varchar(255) DEFAULT NULL COMMENT '头像',
  `introduce` varchar(255) DEFAULT NULL COMMENT '介绍',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '用户状态',
  `login` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `login_time` int(11) unsigned DEFAULT '0' COMMENT '最后登录时间',
  `exp` int(11) DEFAULT '0' COMMENT '经验',
  `integral` int(11) NOT NULL DEFAULT '0' COMMENT '积分',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `recommend` tinyint(3) DEFAULT '0' COMMENT '推荐票',
  `recommend_time` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户表';

-- ----------------------------
-- Records of ky_user
-- ----------------------------

-- ----------------------------
-- Table structure for ky_user_group
-- ----------------------------
DROP TABLE IF EXISTS `ky_user_group`;
CREATE TABLE `ky_user_group` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(12) NOT NULL,
  `exp_min` int(11) DEFAULT '0' COMMENT '经验',
  `exp_max` int(11) DEFAULT '0' COMMENT '经验',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态',
  `json` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `exp_min` (`exp_min`),
  KEY `exp_max` (`exp_max`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户组';

-- ----------------------------
-- Records of ky_user_group
-- ----------------------------
INSERT INTO `ky_user_group` VALUES ('1', '新手上路', '0', '999', '1', '{\"comment\":\"1\",\"comment_exp\":\"10\",\"comment_integral\":\"1\",\"faces\":\"0\",\"bookshelf\":\"1\",\"bookshelf_num\":\"5\",\"reader_exp\":\"1\",\"reader_integral\":\"1\",\"recommend\":\"1\"}');
INSERT INTO `ky_user_group` VALUES ('2', '中级会员', '1000', '19999', '1', '{\"comment\":\"1\",\"comment_exp\":\"20\",\"comment_integral\":\"2\",\"faces\":\"1\",\"bookshelf\":\"1\",\"bookshelf_num\":\"5\",\"reader_exp\":\"2\",\"reader_integral\":\"2\",\"recommend\":\"2\"}');
INSERT INTO `ky_user_group` VALUES ('3', '高级会员', '20000', '99999', '1', '{\"comment\":\"1\",\"comment_exp\":\"50\",\"comment_integral\":\"3\",\"faces\":\"1\",\"bookshelf\":\"1\",\"bookshelf_num\":\"20\",\"reader_exp\":\"5\",\"reader_integral\":\"3\",\"recommend\":\"3\"}');
INSERT INTO `ky_user_group` VALUES ('4', '金牌会员', '100000', '999999', '1', '{\"comment\":\"1\",\"comment_exp\":\"100\",\"comment_integral\":\"5\",\"faces\":\"1\",\"bookshelf\":\"1\",\"bookshelf_num\":\"50\",\"reader_exp\":\"10\",\"reader_integral\":\"5\",\"recommend\":\"4\"}');

-- ----------------------------
-- Table structure for ky_user_menu
-- ----------------------------
DROP TABLE IF EXISTS `ky_user_menu`;
CREATE TABLE `ky_user_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文档ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `url` char(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `hide` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `icon` varchar(50) DEFAULT NULL COMMENT '图标',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户菜单';

-- ----------------------------
-- Records of ky_user_menu
-- ----------------------------
INSERT INTO `ky_user_menu` VALUES ('1', '关注中心', '0', '', '0', '&#xe64a;', '0');
INSERT INTO `ky_user_menu` VALUES ('2', '我的书架', '0', 'user/bookshelf/index', '0', '&#xe605;', '1');
INSERT INTO `ky_user_menu` VALUES ('3', '最近阅读', '0', 'user/recentread/index', '0', '&#xe67e;', '1');
INSERT INTO `ky_user_menu` VALUES ('4', '我的评论', '0', 'user/comment/index', '0', '&#xe610;', '1');
INSERT INTO `ky_user_menu` VALUES ('7', '设置', '0', '', '0', '&#xe636;', '0');
INSERT INTO `ky_user_menu` VALUES ('9', '个人信息', '0', 'user/user/info', '0', '&#xe64a;', '7');
INSERT INTO `ky_user_menu` VALUES ('10', '修改密码', '0', 'user/user/password', '0', '&#xe60c;', '7');
