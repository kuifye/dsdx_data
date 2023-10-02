<template>
	<div>
    <div class='group_ui_card_border'>
			<div v-if="init_visible_flag">
				{{ msg }}
				<form id="form">
				账户身份: <select id="identity" v-model="input_identity">
					<option value="工作">工作</option>
					<option value="工人">工人</option>
					<option value="失业">失业</option>
					<option value="学生">学生</option>
				</select>
				<br><input id = 'submit'  type="button" value="提交" @click="set_identity()"/>
				</form>
			</div>
		</div>	
	</div>
</template>

<script>
export default {
  name: 'I_SetIdentity',

  props: {
  },

  components: {
  },

  setup () {
  },

  data:function(){
    return{
			
			user_id: 0,
			identity: '',
			input_identity: '工人',
			already_set_flag: false,
			init_visible_flag: false,
			msg: "你还没有设置自己的身份，设置完成后请提交页面。",

    }
  },

  beforeRouteEnter(to, from, next) {
    next(vm => {});
  },

  async beforeRouteUpdate(to, from) {
  },

	created:function(){
		const that = this;
		this.$store.dispatch('get_user_info').then(function(data){
      that.user_id = data.user_id;
			if(data.identity != ''){
				that.identity = data.identity;
				that.already_set_flag= true;
				that.msg = "你当前设置的身份是 " + that.identity + " ，确定要修改身份吗？"
			}
    });
		this.init_visible_flag = true;
  },

  activated() {
  },
	
  methods: {

		set_identity:function(){
			const that = this;
			if(this.input_identity == identity){
				alert('你已经将身份设置成' + identity + '了。');
			}else{
				i_set_identity_for_user(that.input_identity)
					.then(function (data) {
						alert(data.msg);
						that.identity = that.input_identity;
						that.$store.commit('set_user_identity', that.input_identity)
						that.$router.back();
				})
			}
		},
            
  },
};
</script>