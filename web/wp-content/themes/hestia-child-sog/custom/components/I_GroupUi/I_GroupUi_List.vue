<template>
	<div class='group_ui_card' >
		<div class='group_ui_card_border' v-for="group in groups" :key="group.group_id">
			<span class="delete_button">群id:{{ group.group_id }}</span>
			<div class="group_ui_card_h1">
				<a class="a_button" @click="go_to_this_group_router_url(group)">
					{{ group.name }}  {{ group.new_item_text }}
				</a>
			</div>
			<span v-if="group.create_user_id!=user_id" class="delete_button">uid:{{ group.create_user_id }}</span>
			<span v-if="group.create_user_id==user_id" class="delete_button">我的</span>
			描述：{{ group.description }}
			<!-- <br>已删除：{{ group.is_delete }} -->
		</div>
	</div>
</template>


<script>
export default {
	name: 'I_GroupUi_List',

	props: {
    groups: {
      type: Object,
      required: true,
    },
  },

  components: {
  },

  setup () {
  },

  data() {
    return {
			user_id: null,
    };
  },

	created:function(){

		const that = this;
		this.$store.dispatch('get_user_info').then(function(data){
      that.user_id = data.user_id;
    });

  },
	
  methods: {

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

	}

};
</script>