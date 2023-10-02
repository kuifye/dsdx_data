<template>
  <div class="main">
    <div class="i_div_border_add_costs">
      <a class="i_h1">核验数据中、请稍候……</a>
    </div>
  </div>
</template>

<script>
export default {
  name: 'I_Group_JoinByRdpwd',

  props: {
  },

  components: {
  },

  setup () {
  },

  data:function(){
    return{
      promise_flag: false,
      user_id: 0,
      group: {
        group_id: 0,
      },
    }
  },

  beforeRouteEnter(to, from, next) {
    next(vm => {});
  },

  async beforeRouteUpdate(to, from) {
  },

	created:function(){
    
    this.set_info();
    this.join_group();
    this.get_group_info_by_id();

  },

  activated() {
  },
	
  methods: {

    set_info:function(){
      const that = this;
      this.$store.dispatch('get_user_info').then(function(data){
        that.user_id = data.user_id;
      });
      this.rd_pwd = this.$route.params.rd_pwd;
      this.group.group_id = this.$route.params.group_id;
    },

    join_group:function(){
      const that = this;
      i_join_group(this.group.group_id,this.rd_pwd)
      .then(function(data){
        if(data.code == "200"){
          that.href_to_group_item();
        }else if(data.code == '409'){
          that.href_to_group_item();
        };
      })
    },

    get_group_info_by_id:function(){
      const that = this;
      i_get_group_info_by_id(this.group.group_id)
        .then(function(data){
          if(data.code == '200'){
            that.group = data.data;
            that.href_to_group_item();
          }
        })
    },

    href_to_group_item:function(){
      const that = this;
      if(!this.promise_flag){
        that.promise_flag = true;
      }else{
        var group = that.group;
        that.go_to_this_group_router_url(group)
      };
    },

    go_to_this_group_router_url:function(group){
			const link_url = 'I_GroupItem';
      //提交该群的信息
      this.$store.commit('set_current_group_id', {data: group.group_id, name: 'I_GroupItem'} );
			this.$store.commit('set_group_raw',{data: group, name: 'I_GroupItem'})
			this.$store.commit('set_current_group_done_flag', {data: 2, name: 'I_GroupItem'});
			//转跳到查看账本链接
			this.$store.commit('set_group_ui_url',link_url);
			this.$router.push({name:link_url});
		},

  },
};
</script>