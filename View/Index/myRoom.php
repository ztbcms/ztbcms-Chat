<?php if (!defined('CMS_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap" id="app">
    <Admintemplate file="Common/Nav"/>
    <div class="h_a">聊天列表</div>
    <form onsubmit="return false;">
        <div class="table_full">
            <table class="table_form" width="100%" cellspacing="0">
                <tbody>
                <tr>
                    <th>ID</th>
                    <th>头像</th>
                    <th>名称</th>
                    <th>创建时间</th>
                    <th>未读数量</th>
                    <th>操作</th>
                </tr>
                <tr v-for="item in lists">
                    <td>{{ item.id }}</td>
                    <td>
                        <img :src="item.pic" height="50">
                    </td>
                    <td>{{ item.name }}</td>
                    <td>{{ item.add_time | getFormatTime }}</td>
                    <td>{{ item.no_read_num }}</td>
                    <td>
                        <a class="btn btn-primary" @click="showMsg(item.id)">查看聊天内容</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="box-footer clearfix border-t_0">
            <v-page :page="page" @update="getList" :page_count="page_count"></v-page>
        </div>
    </form>
</div>
</body>
<!-- 分页组件  -->
<script type="text/x-template" id="__vPage">
    <div class="col-sm-12">
        <div class="dataTables_paginate paging_simple_numbers">
            <button class="btn btn-primary" @click="preBtn">&lt; 上一页</button>
            <div style="display: inline; font-size: 16px; margin-left: 10px; margin-right: 10px;">
                <span>{{page}}</span> / <span>{{page_count}}</span></div>
            <button class="btn btn-primary" @click="nextBtn">下一页 &gt;</button>
            <input type="text" v-model="goPage" placeholder="跳转页码" class="form-control input-sm"
                   style="width: 70px; display: inline;">
            <button @click="goPageBtn" class="btn btn-primary">GO</button>
        </div>
    </div>
</script>
<script>
    var pageComponent = {
        props: ['page', 'page_count'],
        template: '#__vPage',
        data: function () {
            return {goPage: 1}
        },
        methods: {
            updateList: function () {
                var that = this;
                that.$emit('update');
            },
            preBtn: function () {
                if (this.page > 1) {
                    this.$parent.page -= 1;
                    this.updateList();
                } else {
                    layer.msg('当前已经是第一页')
                }
            },
            nextBtn: function () {
                if (this.page < this.page_count) {
                    this.$parent.page = parseInt(this.page) + 1;
                    console.log(this.$parent.page);
                    this.updateList();
                } else {
                    layer.msg('当前已经是最后一页');
                }
            },
            goPageBtn: function () {
                if (this.goPage < 1 || this.goPage > this.page_count) {
                    layer.msg('超出页数范围');
                    this.goPage = 1;
                } else {
                    this.$parent.page = this.goPage;
                    this.updateList();
                }
            }
        }
    };
</script>
<script>

    new Vue({
        el: '#app',
        data: {
            lists: [],
            page: 1,
            page_count: 1
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
                return y + '-' + (m < 10 ? '0' + m : m) + '-' + (d < 10 ? '0' + d : d) + ' ' + (h < 10 ? '0' + h : h) + ':' + (i < 10 ? '0' + i : i);
            }
        },
        methods: {
            getList: function(){
                var that = this;
                var url = '{:U("Chat/Index/getMyRoomList")}';
                var data = {page: that.page, limit: 20};
                $.get(url, data, function(res){
                    if(res.status){
                        that.lists = res.data.items;
                        that.page = res.data.page;
                        that.page_count = res.data.total_pages;
                    }
                }, 'json');
            },
            showMsg: function(id){
                var that = this;
                layer.open({
                    title: '聊天记录',
                    type: 2,
                    area: ['590px', '590px'],
                    content: "{:U('Chat/Index/chatMsg')}&is_allow_send=1&room_id="+id,
                    end: function () {
                        that.getList()
                    }
                });
            }
        },
        mounted: function(){
            this.getList();
        },
        components: {
            'v-page': pageComponent
        }

    });
</script>
</html>
