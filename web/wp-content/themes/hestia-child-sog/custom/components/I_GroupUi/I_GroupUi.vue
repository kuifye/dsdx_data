<template>
	<div>
		<div class="i_width">
      <I_UserInfo/>
      <I_GroupUi_Join/>
      <I_GroupUi_Create
        @on-i_group_data="new_i_group_data"
      />
      <I_PageController
        :page="page"
        @on-page="set_page"
      />
      <I_GroupUi_List
        :groups="groups"
      />
      <I_PageController
        v-if="groups.length>=7"
        :page="page"
        @on-page="set_page"
      />
		</div>
	</div>
</template>

<script>
import I_UserInfo from "../I_UserInfo.vue";
import I_GroupUi_Join from "./I_GroupUi_Join.vue";
import I_GroupUi_Create from "./I_GroupUi_Create.vue";
import I_PageController from "../I_PageController.vue";
import I_GroupUi_List from "./I_GroupUi_List.vue";

export default {
  name: 'I_GroupUi',

  components: {
    I_UserInfo,
    I_GroupUi_Join,
    I_GroupUi_Create,
    I_PageController,
    I_GroupUi_List,
  },

  setup () {
  },

  data() {
    return {
      user_id: 0,
      identity: null,
      groups:[
      ],
      //起始条目的偏移量、每页的尺寸、总条目数
      page:{
        page_offset: Number(0),
        page_size: Number(10),
        page_total: Number(1),
      },
    };
  },

  computed: {

    get_I_GroupUi_url(){
      return this.$store.getters.group_ui_url;
    }

  },

  //通过store获取所当前用户的id和其他用户的信息，$store的文件在../vuex/store.js里面
	created:function(){
    // this.$store.commit('set_group_ui_url_default')
  },

  //如果之前访问了子页面，则转跳到子页面
  activated() {
    this.go_I_GroupUi_url();
    const that = this;
    this.$store.dispatch('get_user_info').then(function(data){
      that.user_id = data.user_id;
      that.set_all_group_info_by_user_id(that.user_id);
      that.identity = data.identity
      if(that.identity==''){
        that.$router.push({name:'I_SetIdentity'});
      }
    });
  },
	
  methods: {

    //通过当前用户的id查找所有的用户拥有的列表，并写入到该vue的data中的groups的字典里面
    set_all_group_info_by_user_id:function(user_id = null){
      const that = this;
      var page_offset = this.page.page_offset;
      var page_size = this.page.page_size;
      i_get_all_group_info_by_user_id(page_offset,page_size,user_id)
        .then(function(data){
          that.set_data(data);
      });
    },

    //插入一条新数据（创建群组之后，通过子组件的emit调用该函数）
    new_i_group_data:function(data){
      this.groups.unshift(data);
    },

    //设置页面的groups信息和page的尺寸
    set_data:function(data){
      this.groups = data.data;
      this.page.page_total = data.count;
    },

    //设置页码，用来获取翻页子组件emit出来的page，并重新查询相关的分页
    set_page:function(page){
      this.page = page;
      this.set_all_group_info_by_user_id(this.user_id);
    },

    //返回群组的总条数
    get_page_total:function(){
      return this.page.page_total;
    },

    //如果之前访问的是子页面，则该方法会让当前页面转跳到子页面
    go_I_GroupUi_url(){
      if(this.get_I_GroupUi_url != '/app/I_GroupUi'){
        this.$router.push(this.get_I_GroupUi_url);
      }
    },

  },
};
</script>