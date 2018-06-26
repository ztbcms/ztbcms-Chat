<?php
/**
 * Created by PhpStorm.
 * User: yezhilie
 * Date: 2018/6/25
 * Time: 16:32
 */

namespace Chat\Model;

use Common\Model\Model;

class MsgModel extends Model{

    protected $tableName = 'chat_msg';

    /**
     * 消息类型
     */
    const CONTENT_TYPE_TEXT = 'text';   //文本
    const CONTENT_TYPE_IMAGE = 'image'; //图片
    const CONTENT_TYPE_VOICE = 'voice'; //语音
    const CONTENT_TYPE_VIDEO = 'video'; //视频
}