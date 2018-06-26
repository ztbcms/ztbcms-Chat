<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>系统后台 - {$Config.sitename} - by ZTBCMS</title>
    <Admintemplate file="Admin/Common/Cssjs"/>

    <style>
        /* vue相关  */
        [v-cloak] {
            display: none;
        }
    </style>
    <style type="text/css">
        .talk_con{
            width:100%;
            height:500px;
        }
        .talk_show{
            width:100%;
            height:465px;
            margin:10px auto 0;
            overflow:auto;
        }
        .talk_input{
            width:100%;
            margin:10px auto 0;
        }
        .ltalk{
            margin:10px;
            text-align:left;
        }
        .rtalk{
            margin:10px;
            text-align:right;
        }
        .content{
            display:inline-block;
        }
        .msg{
            border-radius:10px;
            color:#fff;
            padding:10px 10px;
            max-width: 400px;
            text-align: left;
        }

        .userpic{
            height: 40px;
            width: 40px;
            position: absolute;
        }
        .seat{
            display: inline-block;
            width: 40px;
        }
    </style>
    <script>
        /**
         * js资源加载完后进行全局初始化
         */
        ;(function () {

            $(document).ready(function () {
                //注册 ajax加载时 显示加载框
                $(document).ajaxStart(function () {
                    if (layer) {
                        window.__layer_loading_index = layer.load(1);
                    }
                });
                $(document).ajaxComplete(function () {
                    if (layer) {
                        layer.close(window.__layer_loading_index);
                    }
                });
                $(document).ajaxError(function () {
                    if (layer) {
                        layer.msg('网络繁忙，请稍后再试..');
                    }
                })
            });

        })(jQuery);
    </script>
</head>
<body>
<div class="talk_con" id="app">
    <div class="talk_show" id="words">
        <template v-for="(item, index) in lists">

            <div v-if="isShowTime(index)" style="text-align: center;">
                <span style="background: #CCCCCC;color: white;padding: 2px;border-radius: 5px;">{{item.add_time | getFormatTime}}</span>
            </div>

            <div :class="item.is_me == 1 ? 'rtalk' : 'ltalk'" style="position: relative;">
                <template v-if="item.is_me == 0">
                    <img :src="item.person_pic" class="userpic">
                    <div class="seat"></div>
                </template>
                <div class="content">
                    <div v-if="item.is_me == 0" style="color: gray;">{{item.person_name}}</div>
                    <div class="msg" :style="item.is_me == 1 ? 'background:#ef8201;' : 'background:#0181cc;'">
                        <span>{{item.content}}</span>
                    </div>
                </div>

                <template v-if="item.is_me == 1">
                    <img :src="item.person_pic" class="userpic">
                    <div class="seat"></div>
                </template>
            </div>
        </template>
    </div>
    <div v-if="is_allow_send == 1" class="talk_input">
        <input type="text" class="form-control" style="margin-left: 20%;display: inline;width:50%;" @focus="toBottom" @keydown="toBottom" v-model="content">
        <input type="button" value="发送" class="btn btn-primary" @click="sendMsg">
        <input type="button" value="1111" class="btn btn-primary" @click="getBeforeList">
    </div>
</div>
</body>
<script>
    new Vue({
        el: '#app',
        data: {
            room_id: '{:I("get.room_id")}',
            is_allow_send: '{:I("get.is_allow_send")}',
            lists: [],
            page: 1,
            limit: 5,
            page_count: 1,
            content: '',
            last_time: ''
        },
        filters: {
            getFormatTime: function(value){
                if(!value || value == '0') return '';
                var time = new Date(parseInt(value * 1000));
                var y = time.getFullYear();
                var m = time.getMonth() + 1;
                var d = time.getDate();
                var h = time.getHours();
                var i = time.getMinutes();
                var now = new Date();
                if(time.getFullYear() == now.getFullYear() && time.getMonth() == now.getMonth() && time.getDate() == now.getDate()){
                    return (h < 10 ? '0' + h : h) + ':' + (i < 10 ? '0' + i : i);
                }else{
                    return y + '-' + (m < 10 ? '0' + m : m) + '-' + (d < 10 ? '0' + d : d) + ' ' + (h < 10 ? '0' + h : h) + ':' + (i < 10 ? '0' + i : i);
                }
            }
        },
        methods: {
            getList: function(){
                var that = this;
                var url = '{:U("Chat/Index/getMsgList")}';
                var data = {room_id: that.room_id, page: that.page, limit: that.limit};
                $.get(url, data, function(res){
                    if(res.status){
                        that.lists = res.data.items;
                        that.page = res.data.page;
                        that.page_count = res.data.total_pages;
                        that.setLastTime();
                        setTimeout(function(){
                            that.toBottom();
                        }, 0);
                    }
                }, 'json');
            },
            getBeforeList: function(){
                //向上加载更多
                var that = this;
                var url = '{:U("Chat/Index/getMsgList")}';
                that.page++;
                var data = {room_id: that.room_id, page: that.page, limit: that.limit};
                $.get(url, data, function(res){
                    if(res.status){
                        if(res.data.items.length > 0){
                            var list = res.data.items;
                            for(var k in that.lists){
                                list.push(that.lists[k]);
                            }
                            that.lists = list;
                        }
                    }
                }, 'json');
            },
            getNewMsg: function(isToBottom){
                var that = this;
                var url = '{:U("Chat/Index/getNewMsg")}';
                var data = {room_id: that.room_id, last_time: that.last_time};
                $.get(url, data, function(res){
                    if(res.status){
                        if(res.data.length > 0){
                            for(var k in res.data){
                                that.lists.push(res.data[k])
                            }
                        }
                        that.setLastTime();
                        setTimeout(function(){
                            if(isToBottom){
                                that.toBottom();
                            }
                        }, 0);
                    }
                }, 'json');
            },
            setLastTime: function(){
                var that = this;
                var length = that.lists.length;
                if(length > 0){
                    that.last_time = that.lists[length-1].add_time;
                }
            },
            isShowTime: function(index){
                var that = this;
                if(index == 0)return true;
                var pre_index = index-1;
                var add_time = that.lists[index].add_time;
                var pre_add_time = that.lists[pre_index].add_time;
                if(add_time - pre_add_time > 5*60){
                    //与上一条聊天超过5分钟则显示时间
                    return true;
                }
                return false;
            },
            sendMsg: function(){
                var that = this;
                var url = '{:U("Chat/Index/sendMsg")}';
                var data = {room_id: that.room_id, content: that.content};
                $.post(url, data, function(res){
                    if(res.status){
                        that.content = '';
                        that.getNewMsg(true);
                    }
                }, 'json');
            },
            toBottom: function(){
                $('#words').scrollTop($('#words').prop("scrollHeight"));
            }
        },
        mounted: function(){
            var that = this;
            that.getList();
            setInterval(function(){
                that.getNewMsg(false);
            }, 1000*10);
        }
    });
</script>
</html>