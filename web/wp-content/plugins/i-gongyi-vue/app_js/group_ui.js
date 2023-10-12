//--------------------------------------------------------------------------
// 用于显示用户的账本信息


Vue.component('user', {
  props: ['user_id', 'user_name', 'identity'],
  template: `
  <div class='user'>
     | 用户id:{{user_id}} | 用户名:{{user_name}} | 身份:{{identity}} | 
  </div>
  `,
});


Vue.component('i_group_by_group_id', {
  props: [],
  data:function(){
    return{
      visible_flag: false,
      visible_text: "申请入群",
      group_id: 0,
    }
  },

  template: `
  <div class="div_border_2" >
    <span class="right_icon" @click="change_visible_flag">{{visible_text}}</span>
    <form id="form" v-if="visible_flag">
      账本id: <input type="text" id="group_id"  v-model="group_id"/>
      <input id = 'submit' @click="join_group" type="button" value="申请加入"/>
    </form>
  </div>
  `,

  methods:{
    change_visible_flag:function(){
      this.visible_flag = !this.visible_flag;
      if(this.visible_flag){
        this.visible_text = "☓";
      }else{
        this.visible_text = "展开";
      }
    },
    join_group:function(){
      i_join_group(this.group_id)
      .then(function(data){
        console.log(data);
      })
    },
  }
})


Vue.component('form_creat_group', {
  props: ['identity'],
  template: `
  <div class="div_border" >
    <form id="form">
      *账本名：<input id = "name" type="text"   value="平摊表">
      *账本类型：<input id = "class_type" type="text"   value="日常饮食">
      简介：<input id = "description" type="text"   value="日常饮食平摊">
      <input id = 'submit' @click="group" type="button" value="创建账本"/>
    </form>
  </div>
  `,

  methods:{
    group:function(){
      const that = this;
      const name = document.getElementById('name').value;
      const description = document.getElementById('description').value;
      const class_type = document.getElementById('class_type').value;
      i_group(name, description, class_type, this.identity)
        .then(function(data){
          item_info = {
            group_id: data.data,
            name: name,
            description: description,
            is_delete: 0,
          };
          that.$emit('i_group_data', item_info);
          alert(data.msg+'  账本号：'+data.data);
        });
    }
  }
});


Vue.component('group_ui_card', {
  props: ['groups', 'link_url', 'user_id', 'user_name', 'identity'],
  template:`
  <div class='group_ui_card'>
    <div class='group_ui_card_border' v-for="group in groups" :key="group.group_id">
      <span v-if="group.create_user_id!=user_id" class="delete_button">uid:{{ group.create_user_id }}</span>
      <span v-if="group.create_user_id==user_id" class="delete_button">我的</span>
      <div class="group_ui_card_h1">
      <a class="a_button" @click="go_to_group_url(link_url, group.group_id, user_id, user_name, identity, group.name)">
        账本编号： {{ group.group_id }}
      </a></div>
      账本名：{{ group.name }}
      <br>描述：{{ group.description }}
      <!-- <br>已删除：{{ group.is_delete }} -->
    </div>
  </div>
  `,

  methods:{
    go_to_group_url:function(link_url,group_id,user_id,user_name,identity,group_name){
      const url = `${link_url}?group_id=${group_id}&user_id=${user_id}&user_name=${user_name}&identity=${identity}&group_name=${group_name}`;
      window.location.href = url;
    },
  }
});


var main = new Vue({
  el:'.main',
  data:{
      user_id: '',
      user_name: '',
      identity: '',
      user_status: '',
      user_nicename: '',
      groups:[
      ],
      page:{
        page_number: 0,
        page_size: 10,
      },
      link_url: '/账本内容/',
  },

  created:function(){
    this.set_identity();
    const queryString = window.location.search;
    const params = new URLSearchParams(queryString);
    user_id = params.get('user_id');
    this.page.page_number = params.get('page_number');
    this.page.page_size = params.get('page_size');
    this.set_all_group_info_by_user_id(user_id);
  },

  methods:{
    i_group_data:function(data){
      console.log(data);
      data['user_id'] = this.user_id;
      console.log(data);
      this.groups.unshift(data);
    },
    set_identity:function(){
      const that = this;
      i_set_identity()
        .then(function(data){
          if(data.identity == ''){
            window.location.href = '/设置身份/';
          }
          that.identity = data.identity;
          that.user_id = data.id;
          that.user_name = data.user_name;
          that.user_status = data.user_status;
          that.user_nicename = data.user_nicename;
      });
    },
    set_all_group_info_by_user_id:function(user_id = null){
      const that = this;
      page_number = this.page.page_number;
      page_size = this.page.page_size;
      i_get_all_group_info_by_user_id(page_number,page_size,user_id)
        .then(function(data){
          that.groups = [];
          for(i = 0; i<data.data.length; i++){
            that.groups.push(data.data[i]);
          }
      });
    },
    add_page_number:function(){
      this.page.page_number += page_size;
    },
    min_page_number:function(){
      this.page.page_number -= page_size;
    },
  },
});