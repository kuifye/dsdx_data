<!-- 组件：账本过滤器 -->
<template>
  <div v-show="visible_show_flag">
    <a class="i_h3">{{ item_filtering_look_up_flag_raw.tittle }}</a>
    <div class='group_ui_card' >
      <div class='group_ui_card_border'>

        <div style="display:inline;white-space: nowrap"><span>按价格排序：<input type="checkbox" v-model="price" @click="set_sort_by(['price',price])"/> &emsp;</span></div>
        <div style="display:inline;white-space: nowrap"><span>按时间排序：<input type="checkbox" v-model="created_time" @click="set_sort_by(['created_time',created_time])"/> &emsp;</span></div>
        <div style="display:inline;white-space: nowrap"><span>权重不为0：<input type="checkbox" v-model="item_filtering_look_up_flag.just_look_expenses_weight_flag"/> &emsp;</span></div>
        <div style="display:inline;white-space: nowrap"><span>只看自己：<input type="checkbox" v-model="item_filtering_look_up_flag.just_look_self_item_flag"/> &emsp;</span></div>
        <div style="display:inline;white-space: nowrap"><span>合并权重：<input type="checkbox" v-model="item_filtering_look_up_flag.combine_expenses_weight_flag"/> &emsp;</span></div>
        <div style="display:inline;white-space: nowrap"><span>树状图：<input type="checkbox" v-model="item_filtering_look_up_flag.build_tree_flag"/> &emsp;</span></div>

      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';

export default defineComponent({
  name: 'I_GroupItem_FilteringLookUp',

  props: {

    page: {
      type: Object,
      required: true,
    },

    url_name: {
      type: String,
      required: true,
    },

  },

  components: {
  },

  setup () {
  },

  emits: ['on-items'],

  data: function() {
    return{
      item_filtering_look_up_flag_raw:{
        tittle:'账本开支项：',
        just_look_self_item_flag:false,
        just_look_expenses_weight_flag:null,
        combine_expenses_weight_flag:null,
        build_tree_flag:null,
        sort_by:null,
      },
      visible_show_flag: true,
      items_raw: {},
      price:false,
      created_time:false,
      user_id:0,
    }
  },

  computed: {

    //----------------------------------------------------------------过滤器--------------------------------------------------------------------------------
    //通过计算属性来实现items的过滤
    items: function() {
      
      const that = this;
      var page = this.page;
      //深拷贝一份给临时变量
      var itmes_tempt = JSON.parse(JSON.stringify(this.items_raw));
      
      //建立一个临时的权重之和计算表
      var items_combine_expenses_weight = {}

      //遍历items
      for(let i=0;i<itmes_tempt.length;i++){

        //如果只看自己
        if(that.item_filtering_look_up_flag_raw.just_look_self_item_flag){
          if(itmes_tempt[i].user_id != that.user_id){
            itmes_tempt.splice(i, 1);i--;
            continue;
          };
        };

        //如果用户希望查看权重之和
        if(this.item_filtering_look_up_flag_raw.combine_expenses_weight_flag){
          if(itmes_tempt[i].expenses_weight != 0){
            let item_user_id = itmes_tempt[i].user_id;
            //查询是否建立过这个id的item
            if(Object.hasOwnProperty.call(items_combine_expenses_weight,item_user_id)){
              //如果存在，则直接把权重值相加
              items_combine_expenses_weight[item_user_id].price += parseInt(itmes_tempt[i].price);
              items_combine_expenses_weight[item_user_id].reimbursement += parseInt(itmes_tempt[i].reimbursement);
              items_combine_expenses_weight[item_user_id].expenses_weight += parseInt(itmes_tempt[i].expenses_weight);
            }else{
              //如果不存在，则直接通过user_id作为字典索引建立一个
              items_combine_expenses_weight[item_user_id] = itmes_tempt[i];
              items_combine_expenses_weight[item_user_id].price = parseInt(items_combine_expenses_weight[item_user_id].price);
              items_combine_expenses_weight[item_user_id].reimbursement = parseInt(items_combine_expenses_weight[item_user_id].reimbursement);
              items_combine_expenses_weight[item_user_id].expenses_weight = parseInt(items_combine_expenses_weight[item_user_id].expenses_weight);
            }
            itmes_tempt.splice(i, 1);i--;
            continue;
          }
        }

        //如果只看权重不为0的项
        if(that.item_filtering_look_up_flag_raw.just_look_expenses_weight_flag){
          if(itmes_tempt[i].expenses_weight == 0){
            itmes_tempt.splice(i, 1);i--;
            continue;
          };
        };

      };

      //把字典数组化
      items_combine_expenses_weight = Object.values(items_combine_expenses_weight);
      //合并两个数组
      itmes_tempt = items_combine_expenses_weight.concat(itmes_tempt);


      //对列表进行排序
      if(this.item_filtering_look_up_flag_raw.sort_by != null){

        //是否按时间排序
        if(this.item_filtering_look_up_flag_raw.sort_by[0] == 'created_time'){
          if(this.item_filtering_look_up_flag_raw.sort_by[1]){
            itmes_tempt.sort((a, b) => timeToTimestamp(a.created_time) - timeToTimestamp(b.created_time));
          }else{
            itmes_tempt.sort((a, b) => timeToTimestamp(b.created_time) - timeToTimestamp(a.created_time));
          }

        //是否按价格排序
        }else if(this.item_filtering_look_up_flag_raw.sort_by[0] == 'price'){
          if(this.item_filtering_look_up_flag_raw.sort_by[1]){
            itmes_tempt.sort((a, b) => b.price - a.price);
          }else{
            itmes_tempt.sort((a, b) => a.price - b.price);
          }
        }

      };

      //如果转换成树状图
      if(that.item_filtering_look_up_flag_raw.build_tree_flag){
        itmes_tempt = build_tree(itmes_tempt);
        // console.log('build_tree:'); // 输出树状结构数据
        // console.log(itmes_tempt); // 输出树状结构数据
      };

      // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
      // 需要开发，这里需要返回一个数组的长度给父组件
      // let page_total = length(itmes_tempt);
      
      // 输出最终结果
      itmes_tempt = itmes_tempt.slice(page.page_offset,page.page_offset+page.page_size);
      return itmes_tempt;
      
    },
    //------------------------------------------------------------------------------------------------------------------------------------------------------
    
    item_filtering_look_up_flag: function() {
      this.$emit('on-items', this.items);
      return this.item_filtering_look_up_flag_raw;
    },

  }, 

  beforeRouteEnter(to, from, next) {
    next(vm => {});
  },

  async beforeRouteUpdate(to, from) {
  },

	created:function(){

    const that = this;
    that.$store.dispatch('get_current_group_all_info',that.url_name).then(function (data) {
      that.items_raw = data.items_raw;
    })
    this.$store.dispatch('get_user_info').then(function (data) {
      that.user_id = data.user_id;
    })

  },

  activated() {
  },
	
  methods: {

    set_sort_by: function(data) {
      this.item_filtering_look_up_flag.sort_by = data;
    },

  },
});
</script>