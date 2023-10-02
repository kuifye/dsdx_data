<template>

  <!-- <div class='group_ui_card' >
		<div class='group_ui_card_border' v-if="visible_init_flag">
      <div class="controller">
        ● 当前页码：{{ current_page_number }} 合计： {{ page_sizing_number }} 页
        <br>
        <span class="delete_button">
          <a v-if="is_add_flag" class="a_button" @click="add_page_offset">▶</a>
        </span>
        <span v-if="page_sizing_number>1">●</span> 
        <span>
          <a v-if="is_min_flag" class="a_button" @click="min_page_offset">◀</a>
        </span>
      </div>
    </div>
  </div> -->

  <div class='group_ui_card' >
    <div class='group_ui_card_border' v-if="visible_init_flag">
      ● 合计： {{ page_sizing_number }} 页 &emsp; 当前页码：{{ current_page_number }} 
      <el-pagination
        small
        hide-on-single-page
        @size-change="handle_size_change"
        @current-change="handle_current_change"
        :pager-count="5"
        :current-page="Number(handle_current_page)"
        :total="Number(page.page_total)"
        :page-size="Number(page.page_size)"
        :page-sizes="[5, 10, 15, 20, 40]"
        layout="prev, pager, next, jumper">
      </el-pagination>
    </div>
  </div>

</template>

<script>
export default {
  name: 'I_PageController',

  props: {
    page: {
      type: Object,
      required: true,
    },
  },

  computed: {

    //一共有多少页
    page_sizing_number(){
      return Math.ceil(this.page.page_total/this.page.page_size);
    },

    //当前是多少页
    current_page_number(){
      return Math.ceil(this.page.page_offset/this.page.page_size) + 1;
    },
    
    //是否还能继续往下翻页
    is_add_flag(){
      if(this.page.page_offset + this.page.page_size  < this.page.page_total){
        return true;
      }else{
        return false;
      }
    },
    
    //是否还能继续往上翻页
    is_min_flag(){
      if(this.page.page_offset > 0){
        return true;
      }else{
        return false;
      }
    },

  },

  components: {
  },

  setup () {
  },

  data:function(){
    return{
      handle_current_page: 1,
      visible_init_flag: false,
    }
  },

  beforeRouteEnter(to, from, next) {
    next(vm => {});
  },

  async beforeRouteUpdate(to, from) {
  },

	created:function(){
    this.visible_init_flag= true;
  },

  activated() {
  },

  emits: ['on-page'],
	
  methods: {

    //控制翻页，注意，这里面的page_offset不是页数，而是偏移量
    add_page_offset:function(){
      const that = this;
      if( this.page.page_offset + this.page.page_size < this.page.page_total){
        that.page.page_offset += that.page.page_size;
      }
      this.emit_page();
    },

    min_page_offset:function(){
      const that = this;
      if( this.page.page_offset - this.page.page_size > -this.page.page_offset){
        that.page.page_offset -= that.page.page_size;
        if(that.page.page_offset < 0){
          that.page.page_offset = 0;
        };
      }
      this.emit_page();
    },

    go_page_number:function(page_number){
      const that = this;
      if(page_number<=this.page_sizing_number){
        that.page.page_offset = (page_number-1)*this.page_size;
      }
      this.emit_page();
    },

    handle_size_change:function(page_size){
      this.page.page_size = page_size;
      this.emit_page();
    },

    handle_current_change:function(current_page){
      this.page.page_offset = (current_page-1) * this.page.page_size;
      this.handle_current_page = current_page;
      this.emit_page();
    },

    emit_page:function(){
      this.$emit('on-page', this.page);
    }

  },
};
</script>