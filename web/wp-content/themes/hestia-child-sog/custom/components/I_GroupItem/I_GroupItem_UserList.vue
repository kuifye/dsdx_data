<!-- 组件：用户列表 -->
<template>
    <div class="all_user_info">
      <a class="i_h3">该账本用户：</a>
      <div class='group_ui_card' >
        <div class='group_ui_card_border' v-for="user_info in all_user_info" :key="user_info.user_id">
        | 用户名：{{ user_info.display_name }} | 
        用户权重：{{ user_info.expenses_weight/100 }} | 
        用户权限：{{ user_info.user_lever }} | 
        用户身份：{{ user_info.meta_value }} |
        <span v-if="user_info.user_lever<4 && user_info.user_id != user_id && user_lever>=4 && user_info.user_lever>0">
          <a class="a_button" 
          @click="set_admin_by_user_id(user_info.user_id,group.group_id)"
          >设置管理</a> |
        </span> 
        <span v-if="user_info.user_lever>=4 && user_info.user_id != user_id && user_lever>=4">
          <a class="a_button" 
          @click="remove_admin_by_user_id(user_info.user_id,group.group_id)"
          >撤销管理</a> |
        </span> 
        <span v-if="user_info.user_lever<=0 && user_info.user_id != user_id && user_lever>=4">
          <a class="a_button" 
          @click="agree_join_by_user_id(user_info.user_id,group.group_id)"
          >同意申请</a> |
        </span> 
        <span v-if="user_info.user_lever<=0 && user_info.user_id != user_id && user_lever>=4">
          <a class="a_button" 
          @click="refuse_join_by_user_id(user_info.user_id,group.group_id)"
          >拒绝申请</a> |
        </span> 
        </div>
      </div>
	</div>
</template>

<script>
export default {
  name: 'I_GroupItem_UserList',

  props: {
    all_user_info:{
      type: Object,
      required: true,
    },
    group: {
      type: Object,
      required: true,
    },
    user_lever: {
      type: Number,
      required: true,
    },
  },

  components: {
  },

  setup () {
  },

  data:function(){
    return{
      user_id: null,
    }
  },

	created:function(){
    const that = this;
    this.$store.dispatch('get_user_info').then(function (data) {
      that.user_id = data.user_id
    })
  },
	
  methods: {

    set_admin_by_user_id:function(user_id, group_id){
      const that = this;
      i_set_admin_by_user_id(user_id, group_id)
      .then(function(data){
        if(data.code == '200'){
          that.all_user_info.find((user) => user.user_id === user_id).user_lever = 4;
        }
      })
    },

    remove_admin_by_user_id(user_id, group_id){
      const that = this;
      i_remove_admin_by_user_id(user_id, group_id)
      .then(function(data){
        if(data.code == '200'){
          that.all_user_info.find((user) => user.user_id === user_id).user_lever = 2;
        }
      })
    },

    agree_join_by_user_id(user_id, group_id){
      const that = this;
      i_agree_join_by_user_id(user_id, group.group_id)
      .then(function(data){
        if(data.code == '200'){
          that.all_user_info.find((user) => user.user_id === user_id).user_lever = 2;
        }
      })
    },
    
    refuse_join_by_user_id(user_id, group_id){
      const that = this;
      i_refuse_join_by_user_id(user_id, group_id)
      .then(function(data){
        if(data.code == '200'){
					for (let i=0; i<that.all_user_info.length; i++) {
						if (that.all_user_info[i].user_id === user_id) {
							that.all_user_info.splice(i, 1);
							return true;
						}
					}
        }
      })
    },

  },
};
</script>