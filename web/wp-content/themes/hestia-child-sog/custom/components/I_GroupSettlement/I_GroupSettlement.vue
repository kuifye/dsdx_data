<template>
  <div>
    <!-- <el-button @click="forceRerender">刷新</el-button> -->
    <!-- <I_GroupItem_Tittle 
      :group="group" 
      :user_lever="user_lever"
      :in_this_group_flag="in_this_group_flag" 
    /> -->
    <I_GroupSettlement_Comput
      :key="key"
      :items="items"
      :all_user_info="all_user_info"
    />
    Tips:如果没有结算信息，请稍等片刻。
  </div>
</template>

<script>
import I_GroupItem_Tittle from "../I_GroupItem/I_GroupItem_Tittle.vue";
import I_Qrcode from "../I_Qrcode.vue";
import I_GroupSettlement_Comput from "./I_GroupSettlement_Comput.vue";

export default {
  name:'I_GroupSettlement',

  props: {
  },

  components: {
    I_GroupItem_Tittle,
    I_Qrcode,
    I_GroupSettlement_Comput,
  },

  setup() {
  },

  data() {
    return {
      url_name: "I_GroupItem",
      key:0,
      visible_qrcode_flag:false,
      in_this_group_flag:false,
      user_lever:0,
      group_raw:{},
      all_user_info:{},
      items_raw:[],
    };
  },

  computed: {

    group: function() {
      return this.group_raw;
    },

    items: function (){
      return this.items_raw;
    },

  },

  beforeRouteEnter(to, from, next) {
    next(vm => {});
  },

  async beforeRouteUpdate(to, from) {
  },

  created: function () {

    this.this_update();

  }, 

  //每当页面刷新，就重新获取参数和其他用户信息
  activated() {

    this.this_update();

  },

  methods: {

    //更新数据
    this_update:function () {
      this.visible_qrcode_flag = false;
      var current_group = this.$store.getters.current_group_by_name(this.url_name);
      this.group_raw = current_group.group_raw;
      this.all_user_info = current_group.all_user_info;
      this.items_raw = current_group.items_raw;
      this.user_lever = current_group.current_user_info.user_lever;
      this.in_this_group_flag = current_group.flag_raw.in_this_group_flag;
      this.forceRerender();
    },

    forceRerender:function () {
      this.key += 1;  //想让组件重新渲染的时候给key值加1变化
    },

  },
};
</script>