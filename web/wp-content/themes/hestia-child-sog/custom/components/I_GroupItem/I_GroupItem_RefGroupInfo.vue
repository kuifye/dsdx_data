<template>
  <div>
  <!-- 打开抽屉的按钮 -->
  <el-button @click="drawer = true">
    引用
  </el-button>

  <!-- 抽屉的表头 -->
  <el-drawer 
    v-model="drawer" 
    direction="btt"
    :with-header="false"
    z-index="1050"
    size="50%">
    <span>

      <!-- 添加标签页 -->
      <div style="margin-bottom: 20px">
        <el-button size="small" @click="add_tab(title,content,0)">
          添加标签
        </el-button>
      </div>

      <!-- 标签页页头 -->
      <el-tabs
        v-model="editable_tabs_value"
        type="card"
        class="app_el-tabs"
        closable
        @tab-remove="remove_tab"
      >
        <!-- 标签页 -->
        <el-tab-pane 
            v-for="tab in editable_tabs"
            :key="tab.name"
            :label="tab.title"
            :name="tab.name"
            >

          <!-- 间隔 -->
          <el-space wrap :fill="true">
          <!-- for循环，遍历所有的item -->
          <!-- 群组的项目列表 -->
          <div 
            v-for="item in tab.content.items_raw"
            >
            <!-- 如果是精简模式 -->
            <div v-if="flag.tab_simple_flag">
              <el-row :gutter="20">
                <el-col :span="10">{{ item.id }}. {{ item.item_name }}</el-col>
                <el-col :span="4">P:{{item.price/100}}￥</el-col>
                <el-col :span="4">{{item.reimbursement/100}}￥</el-col>
                <el-col :span="2">{{item.expenses_weight/100}}</el-col>
                <el-col :span="4">
                  <el-button type="primary" @click="emit_items([item])">引入</el-button>
                </el-col>
              </el-row>
            </div>
            <!-- 如果不是 -->
            <div v-else>
              <el-descriptions
                :title='item.id + ". " + item.item_name'
                :column="3"
                :size="small"
                border
              >
                <template #extra>
                  <el-button type="primary" @click="emit_items([item])">引入</el-button>
                </template>

                <el-descriptions-item>
                  <template #label>
                    <div class="app_cell-item">
                      支付
                    </div>
                  </template>
                  {{item.price}}
                </el-descriptions-item>

                <el-descriptions-item>
                  <template #label>
                    <div class="app_cell-item">
                      报销
                    </div>
                  </template>
                  {{item.reimbursement}}
                </el-descriptions-item>

                <el-descriptions-item>
                  <template #label>
                    <div class="app_cell-item">
                      消耗、权重
                    </div>
                  </template>
                  {{item.expenses_weight}}
                </el-descriptions-item>

                <el-descriptions-item>
                  <template #label>
                    <div class="app_cell-item">
                      创建人
                    </div>
                  </template>
                  <el-tag size="small">
                    {{ item.display_name }}
                  </el-tag>
                </el-descriptions-item>

                <el-descriptions-item>
                  <template #label>
                    <div class="app_cell-item">
                      描述
                    </div>
                  </template>
                  {{item.description}}
                </el-descriptions-item>
              </el-descriptions>

            </div>
          </div></el-space>
          <!-- {{ tab.content.items_raw }}
          {{ tab.content.group_raw }} -->
        </el-tab-pane>
      </el-tabs>
    </span>
    
  </el-drawer>
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue';

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

  emits: ['on-emit_items'],

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
      flag:{
        tab_simple_flag:true,
      }
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

    // 向父组件返回数条item
    emit_items: function(items) {
			this.$emit('on-emit_items', items);
		}

  },
});
</script>