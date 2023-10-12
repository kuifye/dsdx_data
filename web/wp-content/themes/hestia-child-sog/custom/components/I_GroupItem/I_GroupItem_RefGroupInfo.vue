<template>
  
  <el-button @click="drawer = true">
    引用
  </el-button>

  <el-drawer 
  v-model="drawer" 
  direction="btt"
  :with-header="false"
  z-index="1050"
  size="50%">
    <span>

      <div style="margin-bottom: 20px">
        <el-button size="small" @click="add_tab(title,content,0)">
          添加标签
        </el-button>
      </div>

      <el-tabs
        v-model="editable_tabs_value"
        type="card"
        class="app_el-tabs"
        closable
        @tab-remove="remove_tab"
      >
        <el-tab-pane 
          v-for="item in editable_tabs"
          :key="item.name"
          :label="item.title"
          :name="item.name"
          >
          {{ item.content }}
        </el-tab-pane>

      </el-tabs>


    </span>
  </el-drawer>

</template>

<script>
import { defineComponent, ref } from 'vue'

export default defineComponent({
  name: 'I_GroupItem_RefGroupInfo',

  props: {
    url_name: {
      type: String,
      required: true,
    },
  },

  components: {
  },

  setup () {

    const drawer = ref(false);

    return {
      drawer,
    };

  },

  emits: [],

  data:function(){
    return{
      title: 'test',
      content: 'test……',
      items_raws: {},
      tab_index: 1,
      editable_tabs_value: '1',
      editable_tabs:[
        {
          title: '……',
          name: '1',
          content: '加载中、请稍候',
        },
      ],
    }
  },

  computed: {

    drawer_url_name: function() {
      return this.url_name + '/drawer'
    }

  },

  beforeRouteEnter(to, from, next) {
    next(vm => {});
  },

  async beforeRouteUpdate(to, from) {
  },

	created:function(){

    const that = this;
    that.$store.dispatch('get_current_group_all_info',that.url_name).then(function (data) {
      console.log(data)
      let title = data.group_raw.name;
      let content = data;
      that.remove_tab('1');
      that.items_raws[data.group_raw.group_id] = data
      that.add_tab(title,content)
    })

  },

  activated() {
  },
	
  methods: {

    //增加一个标签
    add_tab: function (title,content,group_id) {
      const that = this;
      const new_tab_name = `${++that.tab_index}`
      this.editable_tabs.push({
        title: title,
        name: new_tab_name,
        content: content,
        group_id: group_id
      });
      this.editable_tabs_value = new_tab_name;
    },

    //去除一个标签
    remove_tab: function (target_name) {
      const that = this;
      const tabs = this.editable_tabs;
      let active_name = this.editable_tabs_value
      if (active_name === target_name) {
        tabs.forEach((tab, index) => {
          if (tab.name === target_name) {
            const next_tab = tabs[index + 1] || tabs[index - 1]
            console.log(next_tab);
            if (next_tab) {
              active_name = next_tab.name;
            }
          }
        })
      }
      this.editable_tabs_value = active_name
      this.editable_tabs = tabs.filter((tab) => tab.name !== target_name)
    },

  },
});
</script>