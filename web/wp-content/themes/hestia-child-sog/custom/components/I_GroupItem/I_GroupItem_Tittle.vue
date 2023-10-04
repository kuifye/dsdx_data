<template>
  <div class='group_ui_card' >
    <div class='group_ui_card_border' style="text-align: left;">
      <el-form inline="true">
        <el-form-item>
          <el-button @click="go_group_ui_url">返回</el-button>
        </el-form-item>
        <el-form-item>
          <el-button @click="share_qrcode" v-if="user_lever>=4" type="primary">分享账本</el-button>
        </el-form-item>
        <el-form-item>
          <el-button @click="go_group_settlement_url" v-if="visible_init_flag" type="primary">进行结算</el-button>
        </el-form-item>
        <el-form-item>
          <el-button id = "submit_group" v-if="!in_this_group_flag" @click="join_group(group.group_id)" type="primary">加入该群</el-button>
        </el-form-item>
        <el-form-item>
          <el-button id = "submit_item" v-if="user_lever>=5" @click="delete_group(group.group_id)" type="danger">删除账本</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    group: {
      type: Object,
      required: true,
    },
    user_lever: {
      type: Number,
      required: true,
    },
    in_this_group_flag: {
      type: Boolean,
      required: true,
    },
    visible_init_flag: {
      type: Boolean,
      required: true,
    }
  },

  components: {
  },

  setup () {
  },

  data:function(){
    return{}
  },

	created:function(){
  },
	
  methods: {

    //加入该群
    join_group:function(){
      i_join_group(this.group.group_id)
      .then(function(data){
        console.log(data);
      })
    },

    //删除该群
    delete_group:function(){
      i_delete_group(this.group.group_id)
      .then(function(data){
        console.log(data);
        if(data.code == '200'){
          history_go_back();
        }
      })
    },

    go_group_ui_url:function(){
      this.$store.commit('set_group_ui_url_default');
      this.$router.push(this.$store.getters.group_ui_url);
    },

    go_group_settlement_url:function(){
      this.$router.push('/app/I_GroupSettlement');
    },

    share_qrcode:function(){
      this.$emit('on-qrcode',true);
    },

  },
};
</script>