<!-- 组件：账本的具体开支列表 -->
<template>
  <div 
  v-if="init_visible_flag && item_visible_flag" >
  <el-space wrap :fill-ratio="vertical">
    <div 
    v-for="item in items" 
    :key="item.item_id"
    >
      <I_GroupItem_ItemUiCard
        :item="item"
        :group="group"
        :user_lever="user_lever"
        :item_visible_flag="item_visible_flag"
        :expenses_weight_text="expenses_weight_text"
        :expenses_weight_measure_text="expenses_weight_measure_text"
        @on-item_id="delete_item_data_by_item_id"
      />
    </div>
  </el-space></div>
</template>

<script>
import { defineComponent, ref } from 'vue'
import I_GroupItem_ItemUiCard from "./I_GroupItem_ItemUiCard.vue";

export default defineComponent({
	name:'I_GroupItem_ItemList',

  props: {
		items: {
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
		user_id: {
      type: Number,
      required: true,
    },
		item_visible_flag:{
			type: Boolean,
			required: true,
		}
  },

  components: {
		I_GroupItem_ItemUiCard,
  },

  setup () {
  },

	emits: ['on-item_id'],

  data:function(){
    return{
			init_visible_flag:false,
      expenses_weight_text: "消耗了：",
      expenses_weight_measure_text: "点",
    }
  },

  beforeRouteEnter(to, from, next) {
    next(vm => {});
  },

  async beforeRouteUpdate(to, from) {
  },

	created: function() {
		this.init_visible_flag = true;
  },

  activated() {
  },
	
  methods: {

		delete_item_data_by_item_id: function(item_id) {
			this.$emit('on-item_id', item_id);
		}

  },
});
</script>