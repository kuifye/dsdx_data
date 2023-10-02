<template>
  <div class='group_ui_card' >
    <div>
      <h1>ISMISM：地上的星俱乐部</h1>
    </div>
  </div>
</template>

<script>
export default {
  name: "Home",

  data() {
    return {
      msg: "欢迎",
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