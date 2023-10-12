<template>

<div class='group_ui_card' >
  <div class='group_ui_card_border'>
    <el-form label-position="left"   label-width="100px">
      <el-form-item label="*账本名：">
        <el-input v-model="form.group_name" />
      </el-form-item>
      <el-form-item label="*账本类型：">
        <el-input v-model="form.class_type" />
      </el-form-item>
      <el-form-item label="简介：">
        <el-input v-model="form.description" />
      </el-form-item>
      <el-button @click="group" type="primary">创建账本</el-button>
    </el-form>
  </div>
</div>

</template>

<script>
export default {
  name: 'I_GroupUi_Create',

  components: {
  },

  setup () {
  },

  data:function(){
    return{
      form:{
        user_id: 0,
        visible_flag: false,
        group_name: '平摊表' + getData(new Date()),
        class_type: '日常饮食',
        description: "日常饮食平摊",
      }
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

		//创建群组
    group:function(){
      const that = this;
      const name = this.form.group_name;
      const description = this.form.description;
      const class_type = this.form.class_type;
      i_group(name, description, class_type, this.identity)
        .then(function(data){
          var i_group_data = {
						user_id: that.user_id,
						create_user_id: that.user_id,
            group_id: data.data,
            name: name,
            created_time: getData(new Date()),
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