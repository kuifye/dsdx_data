<template>
    <div>
    <I_GroupItem_Tittle 
      :group="group" 
      :user_lever="user_lever"
      :in_this_group_flag="in_this_group_flag" 
      :visible_init_flag="visible_init_flag"
      @on-qrcode="show_qrcode"
    />
    <I_Qrcode
      :key="key"
      :group="group"
      :visible_qrcode_flag="visible_qrcode_flag"
    />
    <I_GroupItem_AddCosts
      :url_name="url_name"
      @on-item_data="new_item_data"
      v-if="in_this_group_flag"
    />
    <I_GroupItem_UserList
      :all_user_info="all_user_info"
      :group="group"
      :user_lever="user_lever"
    />
    <I_GroupItem_FilteringLookUp
      :url_name="url_name"
      :key="key"
      :page="page"
      @on-items="set_items"
    />
    <I_PageController
      :page="page"
      @on-page="set_page"
    />
    <I_GroupItem_ItemList
      :items="items"
      :group="group"
      :user_lever="user_lever"
      :user_id="user_id"
      :item_visible_flag="item_visible_flag"
      @on-item_id="delete_item_data_by_item_id"
    />
    <I_PageController
      v-if="items.length>=5&&page.page_total>page.page_size"
      :page="page"
      @on-page="set_page"
    />
    </div>
</template>

<script>
import I_GroupItem_Tittle from "./I_GroupItem_Tittle.vue";
import I_Qrcode from "../I_Qrcode.vue";
import I_GroupItem_AddCosts from "./I_GroupItem_AddCosts.vue";
import I_GroupItem_UserList from "./I_GroupItem_UserList.vue";
import I_GroupItem_FilteringLookUp from "./I_GroupItem_FilteringLookUp.vue";
import I_PageController from "../I_PageController.vue";
import I_GroupItem_ItemList from "./I_GroupItem_ItemList.vue";

export default {
  name:'I_GroupItem',

  props: {
  },

  components: {
    I_GroupItem_Tittle,
    I_Qrcode,
    I_GroupItem_AddCosts,
    I_GroupItem_UserList,
    I_GroupItem_FilteringLookUp,
    I_PageController,
    I_GroupItem_ItemList,
  },

  setup() {
  },

  data() {
    return {
      url_name: "I_GroupItem",
      key: 0,
      group_raw: {},
      all_user_info: {},
      items_raw: [],
      items: [],
      user_id: 0,
      user_status: 0,
      user_lever: 0,
      in_this_group_flag: true,
      item_visible_flag: false,
      visible_qrcode_flag: false,
      visible_init_flag: false,
      page:{
        page_offset: Number(0),
        page_size: Number(10),
        page_total: Number(1),
      },
    };
  },

  computed: {

    group: function() {
      return this.group_raw;
    },

    item_filtering_look_up_flag: function (){
      return this.item_filtering_look_up_flag_raw;
    },

  },

  beforeRouteEnter(to, from, next) {
    next(vm => {});
  },

  async beforeRouteUpdate(to, from) {
  },

  created: function () {
  }, 

  //每当页面刷新，就重新获取参数和其他用户信息
  activated() {
    const that = this;
    this.visible_init_flag = false;
    //滚动到最上面
    document.documentElement.scrollTop = 0;
    var current_group_id = this.$store.getters.current_group_id[this.url_name];

    if(this.group.group_id != current_group_id || this.group.group_id == undefined){
      this.set_data_default();
      this.$store.dispatch('get_current_group',that.url_name).then(function (data) {
        that.set_group_raw(data);
      })
      that.$store.dispatch('get_current_group_all_info',that.url_name).then(function (data) {
        that.all_user_info = data.all_user_info;
        that.set_items_raw(data.items_raw);
        that.update_page_total();
        that.user_lever = data.current_user_info.user_lever;
        that.in_this_group_flag = data.flag_raw.in_this_group_flag;
        that.item_visible_flag = true;
        that.visible_init_flag = true;
        that.key ++;
      })
    }else{
      this.visible_init_flag = true;
    }
  },

  methods: {

    //初始化
    set_data_default:function(){
      this.key ++;
      this.user_lever = 0;
      this.all_user_info= {};
      this.set_items_raw([]);
      this.visible_qrcode_flag = false;
    },

    //设置当前查看的群组信息
    set_group_raw: function (group_raw) {
      this.group_raw = group_raw;
    },
    
    //过滤器调用emit方法函数以更新过滤条件，获取过滤后的items
    set_items: function (items) {
      this.items = items;
      this.update_page_total();
    },

    //设置item源条目初始化
    set_items_raw: function (items_raw) {
      this.items_raw = items_raw;
    },

    //页码控制器通过emit方法调用该函数以更新page
    set_page:function(page){
      this.page = page;
    },

    //显示或取消二维码
    show_qrcode:function(data){
      this.visible_qrcode_flag = !this.visible_qrcode_flag;
    },

    //更新item总条目数
    update_page_total: function (){
      this.page.page_total = this.items_raw.length;
    },

    //插入一条数据，一般在增加开支或报销时使用
    new_item_data: function (data) {
      this.items_raw.unshift(data);
      this.update_page_total();
      this.key ++;
    },

    //删除一条记录
    delete_item_data_by_item_id: function(item_id){
      const that = this;
      for(let i = 0; i<this.items_raw.length; i++){
        if(that.items_raw[i].id == item_id){
          that.items_raw.splice(i, 1);
          that.$store.commit('set_items_raw', {data: that.items_raw, name: 'I_GroupItem'});
          this.key ++;
          that.update_page_total();
          return true;
        }
      }
      return false;
    },

  },
};
</script>