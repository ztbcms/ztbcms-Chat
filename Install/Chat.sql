-- 聊天室
DROP TABLE IF EXISTS `cms_chat_room`;
CREATE TABLE `cms_chat_room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '聊天室名称',
  `pic` varchar(255) NOT NULL COMMENT '聊天室头像',
  `last_msg` varchar(255) NOT NULL COMMENT '最终消息内容',
  `last_time` int(11) NOT NULL COMMENT '最终消息时间',
  `last_person_name` varchar(255) NOT NULL COMMENT '最终消息发送人',
  `person_num` int(11) NOT NULL COMMENT '聊天室人数',
  `add_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 聊天室成员
DROP TABLE IF EXISTS `cms_chat_room_person`;
CREATE TABLE `cms_chat_room_person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `person_type` varchar(255) NOT NULL COMMENT '用户类型',
  `person_id` int(11) NOT NULL COMMENT '用户ID',
  `person_name` varchar(255) NOT NULL,
  `person_pic` varchar(255) NOT NULL,
  `role_id` tinyint(1) NOT NULL,
  `add_time` int(11) NOT NULL COMMENT '加入时间',
  `no_read_num` int(11) NOT NULL COMMENT '未读消息数量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 聊天消息
DROP TABLE IF EXISTS `cms_chat_msg`;
CREATE TABLE `cms_chat_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `room_person_id` int(11) NOT NULL,
  `content_type` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `add_time` int(11) NOT NULL,
  `person_type` varchar(255) NOT NULL COMMENT '用户类型',
  `person_id` int(11) NOT NULL COMMENT '用户ID',
  `person_name` varchar(255) NOT NULL,
  `person_pic` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 分组
DROP TABLE IF EXISTS `cms_chat_group`;
CREATE TABLE `cms_chat_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `person_type` varchar(255) NOT NULL COMMENT '所属用户类型',
  `person_id` int(11) NOT NULL COMMENT '所属用户ID',
  `sort` int(11) NOT NULL COMMENT '排序',
  `add_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 关注
DROP TABLE IF EXISTS `cms_chat_fans`;
CREATE TABLE `cms_chat_fans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `person_type` varchar(255) NOT NULL COMMENT '关注人用户类型',
  `person_id` int(11) NOT NULL COMMENT '关注人用户ID',
  `person_name` varchar(255) NOT NULL,
  `person_pic` varchar(255) NOT NULL,
  `be_person_type` varchar(255) NOT NULL COMMENT '被关注人用户类型',
  `be_person_id` int(11) NOT NULL COMMENT '被关注人用户ID',
  `be_person_name` varchar(255) NOT NULL,
  `be_person_pic` varchar(255) NOT NULL,
  `add_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
