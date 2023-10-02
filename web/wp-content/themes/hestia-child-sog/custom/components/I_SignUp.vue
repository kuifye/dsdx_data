<template>
	<div>

		<div class='group_ui_card' >
			<div class='group_ui_card_border'>
				<div class="i_width">
					<div><a class="i_h1">{{show_data}}</a></div>
					<div>你已累计登记<a id="count"> {{times_i_count}} </a>次</div>
					<input v-if="!sign_up_flag"  @click="sign_up" id="sign_up" type="button" value="提交" />
				</div>
			</div>
		</div>

		<I_GroupItem_FilteringLookUp
			ref="Ref_I_GroupItem_FilteringLookUp"
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
			ref="Ref_I_GroupItem_ItemList"
			:items="items"
      :group="group"
      :user_lever="2"
      :user_id="user_id"
      :item_visible_flag="true"
      @on-item_id="delete_item_data_by_item_id"
		/>

	</div>
</template>

<script>
import { defineComponent, ref } from 'vue'
import I_GroupItem_FilteringLookUp from "./I_GroupItem/I_GroupItem_FilteringLookUp.vue";
import I_PageController from "./I_PageController.vue";
import I_GroupItem_ItemList from "./I_GroupItem/I_GroupItem_ItemList.vue";

export default defineComponent({
	name: 'I_SignUp',

  components: {
		I_GroupItem_FilteringLookUp,
		I_PageController,
    I_GroupItem_ItemList,
  },

  setup () {

		const Ref_I_GroupItem_FilteringLookUp = ref(null);
		const Ref_I_GroupItem_ItemList = ref(null);
		const set_ref_value = () => {
      Ref_I_GroupItem_FilteringLookUp.value.item_filtering_look_up_flag_raw.tittle = '';
			Ref_I_GroupItem_FilteringLookUp.value.item_filtering_look_up_flag_raw.combine_expenses_weight_flag = false;
			Ref_I_GroupItem_FilteringLookUp.value.created_time = true;
			Ref_I_GroupItem_FilteringLookUp.value.item_filtering_look_up_flag_raw.sort_by = ['created_time',false];
			Ref_I_GroupItem_FilteringLookUp.value.item_filtering_look_up_flag_raw.just_look_expenses_weight_flag=true;
			Ref_I_GroupItem_FilteringLookUp.value.visible_show_flag = false;

			Ref_I_GroupItem_ItemList.value.expenses_weight_text = '吃了';
			Ref_I_GroupItem_ItemList.value.expenses_weight_measure_text = '顿';
    }
		
    return {
      Ref_I_GroupItem_FilteringLookUp,
			Ref_I_GroupItem_ItemList,
      set_ref_value,
    }

  },

  data() {
    return {
			url_name: "I_SignUp",
			show_data: "等待签到",
			times_i_count: '?',
			sign_up_flag: false,
			item_info:{
				item_name: '日常饮食',
				description: '小组内循环饮食平摊费用',
				price: 0,
				group_id: 0,
				reimbursement: 0,
				expenses_weight: 100,
				rd_pwd: '7M0YvsMFpsN',
			},
			key: 0,
			user_id: 0,
			group:{},
			items_raw: [],
			items:[],
			page:{
        page_offset: Number(0),
        page_size: Number(10),
        page_total: Number(1),
      },
    };
  },

	created:function(){
		const that = this;
		this.$store.commit('set_current_group_id', { data: 0, name: this.url_name} );
		this.count();
		this.$store.dispatch('get_current_group_all_info',this.url_name).then(function (data) {
			that.items_raw = data.items_raw;
		})
  },

	activated() {
		const that = this;
		this.set_ref_value();
		this.$store.dispatch('get_current_group',this.url_name).then(function (data) {
			that.group = data;
		})

		this.$store.dispatch('get_user_info').then(function(data){
			that.user_info = data;
		});

		this.$store.dispatch('get_current_group_all_info',this.url_name).then(function (data) {
			that.items_raw = data.items_raw;
		})

	},
	
  methods: {

		//页码控制器通过emit方法调用该函数以更新page
    set_page:function(page){
      this.page = page;
    },

		//过滤器调用emit方法函数以更新过滤条件，获取过滤后的items
		set_items: function (items) {
      this.items = items;
      this.update_page_total();
    },

		//删除一条记录
		delete_item_data_by_item_id: function(item_id){
      const that = this;
      for(let i = 0; i<this.items_raw.length; i++){
        if(that.items_raw[i].id == item_id){
          that.items_raw.splice(i, 1);
          that.$store.commit('set_items_raw', that.items_raw);
          this.key ++;
          that.update_page_total();
          return true;
        }
      }
      return false;
    },

		//更新item总条目数
    update_page_total: function (){
      this.page.page_total = this.items.length;
    },

		sign_up:function(){
			const that = this;
			this.show_data = '正在签到...'
			i_sign_up(this.item_info.item_name, this.item_info.description, this.item_info.price, this.item_info.group_id, this.item_info.reimbursement, this.item_info.expenses_weight, this.item_info.rd_pwd)
				.then(function(data){
					if(data.code == '200'){
						that.show_data = '√';
						that.sign_up_flag = true;
						alert('签到成功')
						if(that.times_i_count != '?'){
							that.times_i_count++;
						}else{
							that.count();
						}
					}
				})
		},

		count:function(){
			const that = this;
			i_count()
				.then(function(data){
					that.times_i_count = parseInt(data.data);
				})
		},

  },
});
</script>