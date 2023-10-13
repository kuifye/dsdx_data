<!-- 组件：控制器-添加开支、复制开支等 -->
<template>
  <div class='group_ui_card' v-if="init_visible_flag">
    <a class="i_h3">{{ form.tittle }}</a> 
    <el-switch
      v-model="form.flag.visible"
      inline-prompt
    />
    <el-tabs
      v-if="form.flag.visible"
      v-model="active_tag"
      @tab-click="handleClick"
      class="app_el-tabs"
    >
      <el-tab-pane class="app_el-tab-pane" label="新建开支" name="common_item">

        <!-- 表单 -->
        <el-form :model="form" label-position="left" label-width="100px">
          <el-switch v-model="form.flag.more_input_flag" inactive-text="精简" active-text="详细"/>
          <!-- 开支名 -->
          <el-form-item :label=form.string.item_name>
            <el-input 
            clearable 
            placeholder="键入开支名"
            v-model="form.item_name"/>
          </el-form-item>

          <div v-if="!form.flag.more_input_flag">
            <!-- 报销、支付、权重 -->
            <el-form-item :label=form.string.reimbursement>
              <el-input-number v-model="form.reimbursement" :precision="2" :step="1"/>
            </el-form-item>
          </div>

          <div v-if="form.flag.more_input_flag">
            <!-- 待报销 -->
            <el-form-item :label=form.string.reimbursement>
              <el-input-number v-model="form.reimbursement" :precision="2" :step="1"/>
            </el-form-item>

            <!-- 待支付 -->
            <el-form-item :label=form.string.price>
              <el-input-number v-model="form.price" :precision="2" :step="1"/>
            </el-form-item>

            <!-- 权重/消耗 -->
            <el-form-item :label=form.string.expenses_weight>
              <el-input-number v-model="form.expenses_weight" :precision="2" :step="1"/>
            </el-form-item>
          </div>

          <!-- 详细描述 -->
          <el-form-item :label=form.string.description>
            <el-input clearable v-model="form.description" placeholder="小组内循环饮食平摊费用"/>
          </el-form-item>

          <!-- 折叠面板 -->
          <div v-if="form.flag.more_input_flag">

            <!-- 该item实际发生的时间、例如：隔一天订餐，则需要将发生时间设定在明天 -->
            <el-form-item :label=form.string.occurrence_time>
              <el-date-picker
                v-model="form.occurrence_time"
                type="datetime"
                format="YYYY-MM-DD hh:mm:ss"
                placeholder="选择日期和时间"
                value-format="YYYY-MM-DD hh:mm:ss"
              />
            </el-form-item>

            <!-- 父节点id -->
            <el-form-item :label=form.string.father_item_id>
              <el-input clearable v-model="form.father_item_id" placeholder="建议省略"/>
            </el-form-item>

            <!-- 随机密码 -->
            <el-form-item :label=form.string.rd_pwd>
              <el-input clearable v-model="form.rd_pwd" placeholder="向未加入的群添加开支时使用该参数"/>
            </el-form-item>

          </div>
        
          <!-- 按钮:创建item -->
          <el-row :gutter="20">
            <el-col :span="6">
              <el-button type="primary" @click="i_item_sign_up()">创建</el-button>
            </el-col>
            <el-col :span="6">
              <I_GroupItem_RefGroupInfo 
                :url_name="url_name"
                @on-emit_items="ref_this_item"
              />
            </el-col>
            <el-col :span="6">
              <el-button type="warning"  @click="form_clear()">清零</el-button>
            </el-col>
          </el-row>

          <!-- 提示信息 -->
          <div v-if="form.flag.more_input_flag">
            <br>
            <!-- 提示信息 -->
            <el-alert type="info" show-icon :closable="false">
              <a>_ ⚠ 注意 ⚠ _</a><br>
              待支付和待报销核验方式不同，谨慎填写。
            </el-alert>
          </div>
        </el-form>

      </el-tab-pane>

      <!-- 复制、引用开支 -->
      <el-tab-pane class="app_el-tab-pane" label="复制开支" name="reference_item">

        <!-- 被引用的id输入框 -->
        <el-form-item :label=form.string.item_id>
          <el-input clearable v-model="form.item_id" />
        </el-form-item>

        <!-- 按钮 -->
        <el-row :gutter="20">
          <el-col :span="6">
            <el-button type="primary" @click="i_item_sign_up_by_reference_id()">创建</el-button>
          </el-col>
          <el-col :span="6">
            <I_GroupItem_RefGroupInfo
              :url_name="url_name"
              @on-emit_items="ref_this_item"
            />
          </el-col>
        </el-row>

        <br>
        <!-- 提示信息 -->
        <el-alert type="info" show-icon :closable="false">
          <a>_ ⚠ 注意 ⚠ _</a><br>
          该复制是地址引用，而不是值复制，被复制的值改变后，更新方法会同时更新新值。
        </el-alert>

      </el-tab-pane>

      <!-- 建立关系 -->
      <el-tab-pane class="app_el-tab-pane" label="建立关系" name="set_up_item">
        功能开发中……

        <!-- 输入栏 -->
        *开支名:<el-input 
          clearable 
          placeholder="键入开支名"
          v-model="form.item_name"/>

        <!-- 按钮栏 -->
        <el-row :gutter="20">
          <!-- 新建 -->
          <el-col :span="6">
            <el-button type="primary" @click="">新建</el-button>
          </el-col>
          <!-- 引入按钮 -->
          <el-col :span="6">
            <I_GroupItem_RefGroupInfo
              :url_name="url_name"
              @on-emit_items="add_binded_items"
            />
          </el-col>
        </el-row>
        <br>

        <!-- 间隔 -->
        <el-space wrap :fill="true">
        <!-- 展示原材料的信息 -->
        <div 
          v-for="binded_item in form.binded_items"
          >
          <el-row :gutter="20">
            <!-- 原材料的信息 -->
            <el-col :span="12">{{ binded_item.id }}. {{ binded_item.item_name }}</el-col>
            <el-col :span="4">支付:{{ Number(binded_item.price)*binded_item.number/100 }}￥</el-col>
            <el-col :span="4">报销:{{ Number(binded_item.reimbursement)*binded_item.number/100 }}￥</el-col>
            <el-col :span="4">权重:{{ Number(binded_item.expenses_weight)*binded_item.number/100 }}</el-col>

            <!-- 原材料的数量、以及删除按钮 -->
            <el-col :span="14">
              份数:<el-input-number v-model="binded_item.number" :step="0.25" />
            </el-col>
            <el-col :span="4" :offset="6">
              <el-button type="danger" @click="delete_binded_items([binded_item])">删除</el-button>
            </el-col>
          </el-row>
        </div>
      </el-space>

      </el-tab-pane>

      <!-- <el-tab-pane class="app_el-tab-pane" label="Task" name="fourth">
        Task
      </el-tab-pane> -->

    </el-tabs>

    <!-- <div>
      <el-container class="app_el-container">
        <el-header class="app_el-header">
        </el-header>
        <el-main class="app_el-main">
        </el-main>
      </el-container>
    </div> -->

  </div>
</template>

<script>
import I_GroupItem_RefGroupInfo from "./I_GroupItem_RefGroupInfo.vue";

export default {
  name: 'I_GroupItem_AddCosts',

  props: {
    url_name:{
      type: String,
      required: true,
    },
  },

  components: {
    I_GroupItem_RefGroupInfo,
  },

  setup () {
  },

  data:function(){
    return{
      active_tag: 'common_item',
      user_id: 0,
      user_name: '',
      user_lever: 0,
      identity: '',
      init_visible_flag: false,
      all_user_info: {},
      group: {},
      form:{
        tittle: '控制台：',
        binded_items:[],
        string:{
          item_id: '货物id:',
          item_name: '*开支名:',
          reimbursement: '*待报销(元)',
          price: '*待支付(元)',
          expenses_weight: '*消耗/权重:',
          description: '详细描述:',
          rd_pwd: '随机密码:',
          father_item_id: '父节点id:',
          occurrence_time: '发生时间:',
        },
        flag:{
          visible:false,
          more_input_flag:false,
        },
        item_id: null,
        item_name: null,
        reimbursement: Number(0),
        price: Number(0),
        expenses_weight: Number(0),
        description: null,
        rd_pwd: null,
        father_item_id: null,
        occurrence_time: null,
      }
    }
  },

  beforeRouteEnter(to, from, next) {
    next(vm => {});
  },

  async beforeRouteUpdate(to, from) {
  },

	created:function(){

    const that = this;
    this.$store.dispatch('get_user_info').then(function (data) {
      that.user_id = data.user_id;
      that.user_name = data.user_name;
      that.identity = data.identity;
    })
    that.$store.dispatch('get_current_group_all_info',that.url_name).then(function (data) {
      that.group = data.group_raw;
      that.all_user_info = data.all_user_info;
      that.user_lever = data.current_user_info.user_lever;
      that.init_visible_flag = true;
    })

  },

  activated() {
  },
	
  methods: {

    // 清空表格的数值
    form_clear:function(){
      this.form.reimbursement = Number(0);
      this.form.price = Number(0);
      this.form.expenses_weight = Number(0);
      this.form.occurrence_time = null;
    },

    // 创建一条item
    i_item_sign_up:function() {
      // 获取参数
      const that = this;
      let item_name = this.form.item_name;
      if(item_name==null){item_name='日常餐饮';}
      let description = this.form.description;
      if(description==null){description='日常饮食平摊';}
      const group_id = this.group.group_id;
      const price = 100*parseInt(this.form.price);
      const reimbursement = 100*parseInt(this.form.reimbursement);
      const expenses_weight = 100*parseInt(this.form.expenses_weight);
      const rd_pwd = this.form.rd_pwd;
      const father_item_id = this.form.father_item_id;
      const reference_id = null;
      const occurrence_time = this.form.occurrence_time;
      // 调用api接口
      i_sign_up(item_name, description, price, group_id, reimbursement, expenses_weight, rd_pwd, father_item_id, reference_id, occurrence_time)
        .then(function(data){
          if(data.code == '200'){
            let date_now = getData(new Date());
            var item_data={
              id : data.data,
              created_time: date_now,
              item_id: data.data,
              item_name: item_name,
              display_name: that.user_name,
              user_name: that.user_name,
              price: price,
              description: description,
              group_id: group_id,
              user_id: that.user_id,
              reimbursement: reimbursement,
              expenses_weight: expenses_weight,
              father_item_id: father_item_id,
              user_lever: that.user_lever,
              occurrence_time: that.form.occurrence_time,
              new_item_text: '  ~New',
            };
            that.$store.commit('add_item_by_url_name', {data: item_data, name: that.url_name});
            that.$emit('on-item_data', item_data);
            alert("创建成功");
          }
      })
    },

    // 复制一条item
    i_item_sign_up_by_reference_id:function() {
      const that = this;
      const reference_id = this.form.item_id;
      const group_id = this.group.group_id;
      i_sign_up_by_reference_id(group_id, reference_id)
        .then(function(data){
          console.log(data);
          if(data.code == '200'){
            that.$store.commit('add_item_by_url_name', {data: data.data, name: that.url_name});
            that.$emit('on-item_data', data.data);
            alert("引用成功");
          }
      })
    },

    // 引入一条item信息
    ref_this_item:function(items) {
      const that = this;
      let item = items[0];
      this.form.item_id = item.id;
      this.form.item_name = item.item_name;
      this.form.reimbursement = item.reimbursement;
      this.form.price = item.price;
      this.form.expenses_weight = item.expenses_weight;
      this.form.description = item.description;
      this.form.rd_pwd = item.rd_pwd;
      this.form.father_item_id = item.father_item_id;
      this.form.occurrence_time = item.occurrence_time;
      this.form.flag.more_input_flag = true;
    },

    // 增加原材料item
    add_binded_items:function(items) {
      const that = this;
      for(let i = 0;i < items.length;i++){
        items[i].number = 1;
        that.form.binded_items.push(items[i]);
      };
    },

    // 删除一组原材料
    delete_binded_items:function(items) {
      const that = this;
      for(let i = 0;i < items.length;i++){
        let id = items[i].id
        for(let j = 0;j < that.form.binded_items.length;j++){
          if(id == that.form.binded_items[j].id){
            that.form.binded_items.splice(j, 1);j--;
            break;
          }
        }
      }
    }

  },
};
</script>