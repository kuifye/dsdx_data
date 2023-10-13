<template>
  <div class='group_ui_card' >
    <div>
      <h1>ISMIST：地上的星俱乐部</h1>
      足球活动：
      <iframe 
        src="//player.bilibili.com/player.html?aid=276878473&bvid=BV1Cw411w76q&cid=1294301450&p=1" 
        width="100%" 
        height="500"
        scrolling="no" 
        border="0" 
        frameborder="no" 
        framespacing="0" 
        allowfullscreen="true"> 
      </iframe>
      <iframe 
        src="//player.bilibili.com/player.html?aid=739514057&bvid=BV1Sk4y1e7rC&cid=1083122590&p=1" 
        scrolling="no" 
        border="0" 
        frameborder="no" 
        framespacing="0" 
        width="100%" 
        height="500"
        allowfullscreen="true"> 
      </iframe>
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