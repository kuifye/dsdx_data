<template>
  <div class='group_ui_card' >
  <div class='group_ui_card_border'>
    <form id="form" v-if="visible_flag">
      *账本名：<input id = "name" type="text"   v-model="group_name">
      <br>*账本类型：<input id = "class_type" type="text"   v-model="class_type">
      <br>简介：<input id = "description" type="text"   v-model="description">
      <br><input id = 'submit' @click="group" type="button" value="创建账本"/>
    </form>
  </div>
  </div>
</template>

<script>
export default {
  components: {
  },

  setup () {
  },

  data:function(){
    return{
			user_id: 0,
			visible_flag: false,
      group_name: '平摊表' + this.getData(new Date()),
      class_type: '日常饮食',
      description: "日常饮食平摊",
    }
  },

  computed: {
  },

	created:function(){
		
		const that = this;
		this.$store.dispatch('get_user_info').then(function(data){
			that.user_id = data.user_id;
			that.visible_flag = true;
		});

  },
	
  methods: {

    getData:function(n){
      n=new Date(n);
      return n.toLocaleDateString().replace(/\//g, "-") + ":" + n.toTimeString().substr(0, 8);
    },

		//创建群组
    group:function(){
      const that = this;
      const name = document.getElementById('name').value;
      const description = document.getElementById('description').value;
      const class_type = document.getElementById('class_type').value;
      i_group(name, description, class_type, this.identity)
        .then(function(data){
          var i_group_data = {
						user_id: that.user_id,
						create_user_id: that.user_id,
            group_id: data.data,
            name: name,
            created_time: this.his.getData(new Date()),
            description: description,
            is_delete: 0,
						new_item_text: ' ~new',
          };
          that.$emit('on-i_group_data', i_group_data);
          alert(data.msg+'  账本号：'+data.data);
        });
    }

  },
};
</script>