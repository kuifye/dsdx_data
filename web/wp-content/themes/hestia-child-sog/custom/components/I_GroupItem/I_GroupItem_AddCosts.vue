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
      <el-tab-pane class="app_el-tab-pane" label="创建开支" name="common_item">

        <!-- 表单 -->
        <el-form :model="form" label-position="left" label-width="100px">
          <el-switch v-model="form.flag.more_input_flag" inactive-text="精简" active-text="详细"/>
          <!-- 开支名 -->
          <el-form-item :label=form.string.item_name>
            <el-input 
            clearable 
            :placeholder="form.item_name"
            v-model="form.item_name" />
          </el-form-item>

          <div v-if="!form.flag.more_input_flag">
            <!-- 报销、支付、权重 -->
            <el-form-item :label=form.string.reimbursement>
              <el-input clearable v-model="form.reimbursement" />
            </el-form-item>
          </div>

          <div v-if="form.flag.more_input_flag">
            <!-- 待报销 -->
            <el-form-item :label=form.string.reimbursement>
              <el-input clearable v-model="form.reimbursement" />
            </el-form-item>

            <!-- 待支付 -->
            <el-form-item :label=form.string.price>
              <el-input clearable v-model="form.price" />
            </el-form-item>

            <!-- 权重/消耗 -->
            <el-form-item :label=form.string.expenses_weight>
              <el-input clearable v-model="form.expenses_weight" />
            </el-form-item>
          </div>

          <!-- 详细描述 -->
          <el-form-item :label=form.string.description>
            <el-input clearable v-model="form.description" />
          </el-form-item>

          <!-- 折叠面板 -->
          <div v-if="form.flag.more_input_flag">
            <!-- 随机密码 -->
            <el-form-item :label=form.string.rd_pwd>
              <el-input clearable v-model="form.rd_pwd" />
            </el-form-item>

            <!-- 父节点id -->
            <el-form-item :label=form.string.father_item_id>
              <el-input clearable v-model="form.father_item_id" />
            </el-form-item>
          </div>
        
          <!-- 按钮:创建item -->
          <el-button type="primary" @click="i_item_sign_up()">创建</el-button>
          <el-button  @click="form_clear()">清零</el-button>

          <div v-if="form.flag.more_input_flag">
            <!-- 提示信息 -->
            <el-alert type="info" show-icon :closable="false">
              <a>_ ⚠ 注意 ⚠ _</a>
              待支付和待报销核验方式不同，谨慎填写。
            </el-alert>
          </div>
        </el-form>

      </el-tab-pane>

      <el-tab-pane class="app_el-tab-pane" label="复制开支" name="reference_item">
        <el-form-item :label=form.string.item_id>
          <el-input clearable v-model="form.item_id" />
        </el-form-item>
        <el-button type="primary" @click="i_item_sign_up_by_reference_id()">创建</el-button>
      </el-tab-pane>

      <el-tab-pane class="app_el-tab-pane" label="建立关系" name="set_up_item">
        用于建立条目之间的关系
      </el-tab-pane>

      <el-tab-pane class="app_el-tab-pane" label="Task" name="fourth">
        Task
      </el-tab-pane>

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
export default {
  name: 'I_GroupItem_AddCosts',

  props: {
    url_name:{
      type: String,
      required: true,
    },
  },

  components: {
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
        string:{
          item_id: '货物id:',
          item_name: '*开支名:',
          reimbursement: '*待报销(元)',
          price: '*待支付(元)',
          expenses_weight: '*消耗/权重:',
          description: '详细描述:',
          rd_pwd: '随机密码:',
          father_item_id: '父节点id:',
        },
        flag:{
          visible:false,
          more_input_flag:false,
        },
        item_id: null,
        item_name: '日常餐饮',
        reimbursement: Number(0),
        price: Number(0),
        expenses_weight: Number(0),
        description: '日常饮食平摊',
        rd_pwd: null,
        father_item_id: null,
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

    form_clear:function(){
      this.form.reimbursement = Number(0);
      this.form.price = Number(0);
      this.form.expenses_weight = Number(0);
    },

    i_item_sign_up:function() {
      const that = this;
      const item_name = this.form.item_name;
      const description = this.form.description;
      const group_id = this.group.group_id;
      const reimbursement = 100*parseInt(this.form.reimbursement);
      const price = 100*parseInt(this.form.price);
      const expenses_weight = 100*parseInt(this.form.expenses_weight);
      const rd_pwd = this.form.rd_pwd;
      const father_item_id = this.form.father_item_id;

      i_sign_up(item_name, description, price, group_id, reimbursement, expenses_weight, rd_pwd, father_item_id)
        .then(function(data){
          if(data.code == '200'){
            let date_now = getData(new Date())
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
              new_item_text: '  ~New',
            };
            that.$store.commit('add_item_by_url_name', {data: item_data, name: that.url_name});
            that.$emit('on-item_data', item_data);
            alert("创建成功");
          }
      })
    },

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

  },
};
</script>