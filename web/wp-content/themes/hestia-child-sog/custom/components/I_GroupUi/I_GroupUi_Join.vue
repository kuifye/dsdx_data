<template>
	<div class='group_ui_card' >
    <div class='group_ui_card_border'>
      <span class="right_icon" @click="change_visible_flag">{{visible_text}}</span>
      <form id="form" v-if="visible_flag">
        账本id: <input type="text" id="group_id"  v-model="group_id"/>
        <input id = 'submit' @click="join_group" type="button" value="申请加入"/>
      </form>
    </div>
  </div> 
</template>

<script>
export default {
  name: 'I_GroupUi_Join',

  props: {
  },

  components: {
  },

  setup () {
  },

  data:function(){
    return{
			visible_flag: false,
      visible_text: "申请入群",
      group_id: 0,
    }
  },

  beforeRouteEnter(to, from, next) {
    next(vm => {});
  },

  async beforeRouteUpdate(to, from) {
  },

	created:function(){
  },

  activated() {
  },
	
  methods: {

		change_visible_flag:function(){
      this.visible_flag = !this.visible_flag;
      if(this.visible_flag){
        this.visible_text = "☓";
      }else{
        this.visible_text = "展开";
      }
    },

    join_group:function(){
      i_join_group(this.group_id)
      .then(function(data){
        // console.log(data);
      })
    },

  },
};
</script>