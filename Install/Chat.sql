DROP TABLE IF EXISTS `cms_chat_room`;
CREATE TABLE `cms_chat_room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `pic` varchar(255) NOT NULL,
  `last_msg` varchar(255) NOT NULL,
  `last_time` int(11) NOT NULL,
  `last_person_name` varchar(255) NOT NULL,
  `add_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cms_chat_room_person`;
CREATE TABLE `cms_chat_room_person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `person_type` varchar(255) NOT NULL,
  `person_id` int(11) NOT NULL,
  `person_name` varchar(255) NOT NULL,
  `person_pic` varchar(255) NOT NULL,
  `role_id` tinyint(1) NOT NULL,
  `add_time` int(11) NOT NULL COMMENT '加入时间',
  `no_read_num` int(11) NOT NULL COMMENT '未读消息数量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cms_chat_msg`;
CREATE TABLE `cms_chat_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `room_person_id` int(11) NOT NULL,
  `content_type` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `add_time` int(11) NOT NULL,
  `person_type` varchar(255) NOT NULL,
  `person_id` int(11) NOT NULL,
  `person_name` varchar(255) NOT NULL,
  `person_pic` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

