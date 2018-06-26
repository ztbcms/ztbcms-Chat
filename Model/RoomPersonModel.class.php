<?php
/**
 * Created by PhpStorm.
 * User: yezhilie
 * Date: 2018/6/25
 * Time: 16:32
 */

namespace Chat\Model;

use Common\Model\Model;

class RoomPersonModel extends Model{

    protected $tableName = 'chat_room_person';

    /**
     * 聊天成员类型
     */
    const PERSON_TYPE_ADMIN = 'admin';
    const PERSON_TYPE_MEMBER = 'member';
}