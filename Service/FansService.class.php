<?php
/**
 * Created by PhpStorm.
 * User: yezhilie
 * Date: 2018/6/25
 * Time: 15:47
 */

namespace Chat\Service;

use System\Service\BaseService;

class FansService extends BaseService{

    /**
     * 添加关注
     *
     * @param $person
     * @param $be_person
     * @return array
     */
    static function addFollow($person, $be_person){
        $count = D('Chat/Fans')->where([
            'person_type' => $person['person_type'],
            'person_id' => $person['person_id'],
            'be_person_type' => $be_person['person_type'],
            'be_person_id' => $be_person['person_id']
        ])->count();
        if($count){
            return self::createReturn(false, null, '已经关注了');
        }
        $data = [
            'group_id' => 0,
            'person_type' => $person['person_type'],
            'person_id' => $person['person_id'],
            'person_name' => $person['person_name'],
            'person_pic' => $person['person_pic'],
            'be_person_type' => $be_person['person_type'],
            'be_person_id' => $be_person['person_id'],
            'be_person_name' => $be_person['person_name'],
            'be_person_pic' => $be_person['person_pic'],
            'add_time' => time()
        ];
        $res = D('Chat/Fans')->add($data);
        return self::createReturn(true, $res, '关注成功');
    }

    /**
     * 取消关注
     *
     * @param $person
     * @param $be_person
     * @return array
     */
    static function removeFollow($person, $be_person){
        $count = D('Chat/Fans')->where([
            'person_type' => $person['person_type'],
            'person_id' => $person['person_id'],
            'be_person_type' => $be_person['person_type'],
            'be_person_id' => $be_person['person_id']
        ])->count();
        if($count){
            $res = D('Chat/Fans')->where([
                'person_type' => $person['person_type'],
                'person_id' => $person['person_id'],
                'be_person_type' => $be_person['person_type'],
                'be_person_id' => $be_person['person_id']
            ])->delete();
            return self::createReturn(true, $res, '取消关注成功');
        }else{
            return self::createReturn(false, null, '未关注');
        }
    }

    /**
     * 获取分组粉丝列表
     *
     * @param $group_id
     * @return array
     */
    static function getFansByGroupId($group_id){
        $data = D('Chat/Fans')->where([
            'group_id' => $group_id
        ])->order('`person_name` DESC')->select();
        return self::createReturn(true, $data ?: [], '获取成功');
    }

    /**
     * 移动粉丝到其他分组
     *
     * @param $person
     * @param $be_person
     * @param $group_id
     * @return array
     */
    static function moveFans($person, $be_person, $group_id){
        $count = D('Chat/Fans')->where([
            'person_type' => $person['person_type'],
            'person_id' => $person['person_id'],
            'be_person_type' => $be_person['person_type'],
            'be_person_id' => $be_person['person_id']
        ])->count();
        if($count){
            $res = D('Chat/Fans')->where([
                'person_type' => $person['person_type'],
                'person_id' => $person['person_id'],
                'be_person_type' => $be_person['person_type'],
                'be_person_id' => $be_person['person_id']
            ])->save(['group_id' => $group_id]);
            return self::createReturn(true, $res, '移动成功');
        }else{
            return self::createReturn(false, null, '未关注');
        }
    }
}