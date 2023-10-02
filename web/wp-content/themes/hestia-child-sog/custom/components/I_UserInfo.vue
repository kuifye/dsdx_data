<template>
  <div class='group_ui_card' >
    <div class='group_ui_card_border'>
     | 用户id:{{user_info.user_id}} | 用户名:{{user_info.user_name}} | 身份:{{user_info.identity}} | 
    </div>
  </div>
</template>

<script>

export default {
	components: {
  },

	props: {
  },

  setup () {
  },

  data() {
    return {
      user_info:{
        user_id: '',
        user_name: '',
        identity: '',
        user_status: '',
        user_nicename: '',
      },
    };
  },

	created:function(){
    this.set_identity();
  },
	
  methods: {

    set_identity:function(){
      const that = this;
      this.$store.dispatch('get_user_info').then(function(data){
        that.user_info = data;
      });
    }
    
  },
};
</script>