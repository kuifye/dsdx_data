<!-- 组件：账本开支项具体显示的内容 -->
<template>
  <div>
    <el-card 
    class="app_el-card" 
    shadow="always"
    style="
      border: 0.1rem solid #45505F;
      box-shadow: 1px 2px 3px 0px rgba(0, 0, 0, .6),2px 2px 2px 2px rgb(255, 253, 252)"
    :body-style="{ 
      padding: '10px',
      margin: '0,auto',
    }">

    <!-- 表头 -->
    <!-- 删除按钮 -->
    <span 
      v-if="user_lever>=4 || item.user_id==user_id" 
        class="delete_button" 
        @click="is_delete_item(item.id,group.group_id)"> 
        <span v-if="item.father_item_id!=null">
          {{ item.father_item_id }}-</span>{{ item.id }}.删除 
      </span>
      <span 
      v-else 
        class="delete_button">
        <span v-if="item.father_item_id!=null">
        {{ item.father_item_id }}-</span>{{ item.id }}
    </span>

    <!-- 表标题 -->
    <a class="i_h4">
      {{ item.display_name }} {{ item.item_name }}
    </a>

    <!-- 表内容 -->
    <div class="text item">
      <!-- 报销 -->
      <div 
        v-if="item.reimbursement!=0">
        <div>
          <a  class="i_h4">
            <div style="display:inline;white-space: nowrap">
              <span>待报销：</span>
              {{ item.reimbursement/100 }}元 <span class="annotation"> {{ item.new_item_text }} </span>
            </div>
          </a>
        </div>
        <div v-if="item.group_id != group.group_id">归属群：{{ item.group_id }}</div>
        
      </div>

      <!-- 支付 -->
      <div 
        v-if="item.price!=0">
        <div>
          <a  class="i_h4">
            <div style="display:inline;white-space: nowrap">
              <span>待支付：</span>
              {{ item.price/100 }}元 <span class="annotation"> {{ item.new_item_text }} </span>
            </div>
          </a>
        </div>
        <div v-if="item.group_id != group.group_id">归属群：{{ item.group_id }}</div>
      </div>
      

      <!-- 消耗、权重 -->
      <div 
        v-if="item.expenses_weight!=0">
        <a class="i_h4">
          <div style="display:inline;white-space: nowrap">
          {{ expenses_weight_text }}{{ item.expenses_weight/100 }}{{ expenses_weight_measure_text }} <span class="annotation"> {{ item.new_item_text }} </span> </div>
        </a>
      </div>

      <!-- 描述 -->
      <div>{{ item.description }} ({{ item.created_time }})</div>

      <!-- 递归 -->
      <div 
        v-if="'children' in item && item.children.length > 0" 
        v-for="item_children in item.children" :key="item_children.item_id">
          <I_GroupItem_ItemUiCard 
            :item="item_children"
            :group="group"
            :user_lever="user_lever"
            :user_id="user_id"
            :item_visible_flag="item_visible_flag"
            @on-item_id="is_delete_item"
          />
      </div>

    </div>


    </el-card>
  </div>
</template>

<script>
export default {
  name: 'I_GroupItem_ItemUiCard',

  props: {
    item: {
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
		item_visible_flag:{
			type: Boolean,
			required: true,
		},
    expenses_weight_text:{
      type: String,
      required: true,
    },
    expenses_weight_measure_text:{
      type: String,
      required: true,
    },
  },

  components: {
  },

  setup () {
  },

  emits: ['on-item_id'],

  data:function(){
    return{
      user_id: 0,
    }
  },

  computed: {
  },

  beforeRouteEnter(to, from, next) {
    next(vm => {});
  },

  async beforeRouteUpdate(to, from) {
  },

	created:function(){

    const that = this;
    this.$store.dispatch('get_user_info').then(function (data) {
      that.user_id = data.user_id;
    })

  },

  activated() {
  },
	
  methods: {

    is_delete_item:function(item_id,group_id){
      const that = this;
      i_is_delete_item(item_id,group_id)
      .then(function(data){
        if(data.code == '200'){
          that.$emit('on-item_id', item_id);
        }
      })
    },
  },

};
</script>