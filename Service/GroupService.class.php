<?php
/**
 * Created by PhpStorm.
 * User: yezhilie
 * Date: 2018/6/25
 * Time: 15:47
 */

namespace Chat\Service;

use System\Service\BaseService;

class GroupService extends BaseService{

    /**
     * 添加分组
     *
     * @param $name
     * @param $person
     * @return array
     */
    static function addGroup($name, $person){
        $max_sort = D('Chat/Group')->where([
            'person_type' => $person['person_type'],
            'person_id' => $person['person_id']
        ])->max('sort') ?: 0;
        $data = [
            'name' => $name,
            'person_type' => $person['person_type'],
            'person_id' => $person['person_id'],
            'add_time' => time(),
            'sort' => $max_sort+1
        ];
        $res = D('Chat/Group')->add($data);
        return self::createReturn(true, $res, '添加成功');
    }

    /**
     * 获取分组
     *
     * @param $person
     * @return array
     */
    static function getGroup($person){
        $data = D('Chat/Group')->where([
            'person_type' => $person['person_type'],
            'person_id' => $person['person_id']
        ])->order('`sort` ASC,`add_time` ASC')->select();
        return self::createReturn(true, $data ?: [], '获取成功');
    }

    /**
     * 删除分组
     *
     * @param $id
     * @param $person
     * @return array
     */
    static function delGroup($id, $person){
        $count = D('Chat/Fans')->where(['group_id' => $id])->count();
        if($count){
            return self::createReturn(false, null, '该分组下还有好友，不能删除');
        }
        $res = D('Chat/Group')->where([
            'id' => $id,
            'person_type' => $person['person_type'],
            'person_id' => $person['person_id']
        ])->delete();
        if($res){
            return self::createReturn(true, $res, '');
        }else{
            return self::createReturn(false, null, '删除失败');
        }
    }

    /**
     * 重命名分组
     *
     * @param $id
     * @param $person
     * @param $name
     * @return array
     */
    static function renameGroup($id, $person, $name){
        $count = D('Chat/Group')->where([
            'id' => $id,
            'person_type' => $person['person_type'],
            'person_id' => $person['person_id']
        ])->count();
        if($count){
            $res = D('Chat/Group')->where([
                'id' => $id,
                'person_type' => $person['person_type'],
                'person_id' => $person['person_id']
            ])->save(['name' => $name]);
            return self::createReturn(true, $res, '操作成功');
        }else{
            return self::createReturn(false, null, '操作失败');
        }
    }

    /**
     * 分组排序
     *
     * @param $ids
     * @param $sorts
     * @return array
     */
    static function sortGroup($ids, $sorts){
        if(count($ids) == 0 || count($ids) != count($sorts)){
            return self::createReturn(false, null, '参数错误');
        }
        for($i = 1; $i <= count($ids); $i++){
            if(in_array($i, $sorts) === false){
                return self::createReturn(false, null, '缺少：'.$i);
            }
        }
        for($i = 0; $i < count($ids); $i++){
            $res = D('Chat/Group')->where(['id' => $ids[$i]])->save(['sort' => $sorts[$i]]);
        }
        return self::createReturn(true, $res, '更新成功');
    }
}