//--------------------------------------------------------------------------
// 用于显示用户的账本信息
// document.write(" < script language = javascript src = './i_group_api.js' >< / script>");
Vue.mixin(group_item_sign_up_mixin)


Vue.component('generate_qrcode', {
  props: ['group_id','qrcode_flag'],
  data: function() {
    return {
    };
  },

  template: `
  <div class="generate_qrcode" v-show="qrcode_flag">
    <div class="qrcode_border">
      <div class="qrcode" id="qrcode"></div>
      <input id = 'creat_qrcode' class="center-container" @click="share_qrcode" type="button" value="分享二维码到群聊"/>
    </div>
  </div>
  `,

  mounted:function(){
    const that = this;
    i_get_rd_pwd_by_group_id(this.group_id)
    .then(function(data){
      that.created_qrcode_url_link(data.data)
    })
  },

  methods:{
    created_qrcode_url_link: function(rd_pwd) {
      var url_link = window.location.protocol + "//" + window.location.host + "/i_group/?rd_pwd=" + rd_pwd + "&group_id=" + this.group_id;
      new QRCode(document.getElementById("qrcode"), {
        text: url_link,
        width: 200,
        height: 200,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
      });
    },
    share_qrcode:function(){
      alert('功能开发中……')
      console.log('share_qrcode');
    },
  }
});


Vue.component('group_ui', {
  template: `
  <div class="group_ui">
  <slot name="title"></slot>
  <slot name="look_up_ui"></slot>
  <slot name="settlement_ui"></slot>
  </div>
  `,
});


Vue.component('history_go_back', {
  props: ['button_value'],
  template: `
  <div class="history_back" >
  <a class="a_button" onclick="history_go_back()"> 返回 </a>
  </div>
  `
});


Vue.component('user', {
  props: ['group_name', 'group_id' ,'user_id' , 'not_in_this_group_flag', 'is_admin_flag' ],
  template: `
  <div id="user">
  <h1>{{ group_name }}</h1> 
  当前的账本id：<a id = "group_id">{{ group_id }}</a>
  <input id = "submit_item" v-if="is_admin_flag" @click="delete_group(group_id)"  type="button" value="删除账本"/>
  
  <form id="form">
    <input v-if="not_in_this_group_flag" @click="join_group(group_id)" id = "submit_group"  type="button" value="加入该群"/>
  </form>
  </div>
  `,

  methods:{
    join_group:function(group_id){
      i_join_group(group_id)
      .then(function(data){
        console.log(data);
      })
    },
    delete_group:function(group_id){
      i_delete_group(group_id)
      .then(function(data){
        console.log(data);
        if(data.code == '200'){
          history_go_back();
        }
      })
    },
  }
});


Vue.component('add_costs', {
  props: ['in_this_group_flag', 'user_id', 'user_name', 'identity', 'all_user_info'],
  template: `
  <div id="add_costs" class="add_costs" v-if="in_this_group_flag">
    <div class="div_border_add_costs">
      <form id="form">
      <p>
        <div class="ul_a">*开支名：</div>
        <div class="ul_b"><input id = "item_name" type="text"   value="日常餐饮"></div>
      </p>
      <p>
        <div class="ul_a">
          <select id="is_reimbursement" value="1">
            <option value="1">*待报销(元)</option>
            <option value="0">*待支付(元)</option>
            <option value="2">*权重(点)</option>
          </select>
        </div>
        <div class="ul_b"><input id = "price" type="text"   value="8"></div>
      </p>
      <p><div class="ul_a">详细描述：</div>
        <div class="ul_b"><input id = "description" type="text"   value="日常饮食平摊"></div>
      </p>
      <br><input id = "submit_item" @click="i_item_sign_up()"  type="button" value="添加开支"/>
        <a>_ ⚠ 注意 ⚠ _</a> 待支付和待报销核验方式不同，谨慎选择。
      </form>
    </div>
  </div>
  `,
  methods:{
  }
});


Vue.component('all_user_info', {
  props: ['all_user_info', 'group_id', 'user_id', 'user_lever'],
  template: `
  <div class="all_user_info">
	<h3>该账本用户：</h3>
    <div class='user' v-for="user_info in all_user_info" :key="user_info.user_id">
    | 用户名：{{ user_info.display_name }} | 
    用户权重：{{ user_info.expenses_weight/100 }} | 
    用户权限：{{ user_info.user_lever }} | 
    用户身份：{{ user_info.meta_value }} |
    <span v-if="user_info.user_lever<4 && user_info.user_id != user_id && user_lever>=4 && user_info.user_lever>0">
      <a class="a_button" 
      @click="set_admin_by_user_id(user_info.user_id,group_id)"
      >设置管理</a> |
    </span> 
    <span v-if="user_info.user_lever>=4 && user_info.user_id != user_id && user_lever>=4">
      <a class="a_button" 
      @click="remove_admin_by_user_id(user_info.user_id,group_id)"
      >撤销管理</a> |
    </span> 
    <span v-if="user_info.user_lever<=0 && user_info.user_id != user_id && user_lever>=4">
      <a class="a_button" 
      @click="agree_join_by_user_id(user_info.user_id,group_id)"
      >同意申请</a> |
    </span> 
    <span v-if="user_info.user_lever<=0 && user_info.user_id != user_id && user_lever>=4">
      <a class="a_button" 
      @click="refuse_join_by_user_id(user_info.user_id,group_id)"
      >拒绝申请</a> |
    </span> 
    </div>
	</div>
  `,
  methods:{
    set_admin_by_user_id:function(user_id, group_id){
      const that = this;
      i_set_admin_by_user_id(user_id, group_id)
      .then(function(data){
        if(data.code == '200'){
          that.all_user_info.find((user) => user.user_id === user_id).user_lever = 4;
        }
      })
    },
    remove_admin_by_user_id(user_id, group_id){
      const that = this;
      i_remove_admin_by_user_id(user_id, group_id)
      .then(function(data){
        if(data.code == '200'){
          that.all_user_info.find((user) => user.user_id === user_id).user_lever = 2;
        }
      })
    },
    agree_join_by_user_id(user_id, group_id){
      const that = this;
      i_agree_join_by_user_id(user_id, group_id)
      .then(function(data){
        if(data.code == '200'){
          that.all_user_info.find((user) => user.user_id === user_id).user_lever = 2;
        }
      })
    },
    refuse_join_by_user_id(user_id, group_id){
      const that = this;
      i_refuse_join_by_user_id(user_id, group_id)
      .then(function(data){
        if(data.code == '200'){
					for (let i=0; i<that.all_user_info.length; i++) {
						if (that.all_user_info[i].user_id === user_id) {
							that.all_user_info.splice(i, 1);
							return true;
						}
					}
        }
      })
    },
  }
});


Vue.component('group_item_list', {
  props: ['items', 'group_id', 'user_id', 'user_lever','just_look_self_item_flag'],
  template: `
  <div class="group_item_list">

    <span class="just_look_self_item">只看自己：<input type="checkbox" v-model="just_look_self_item_flag" /></span>
    <h3>账本开支项：</h3> 

		<div class='group_ui_card'>
      <div v-for="item in items" :key="item.item_id" class='group_ui_card_border' v-if="visible_item(item.user_id,user_id)">

        <div v-if="item.is_expenses_weight==0">
          <div>
            <span v-if="user_lever>=4 || item.user_id==user_id" class="delete_button" @click="is_delete_item(item.id,group_id)"> {{ item.id }}.删除 </span>
            <div>
              开支名：{{ item.item_name }} <span class="annotation"> {{ item.new_item_text }} </span>
            </div>
          </div>
          <div>父节点id：{{ item.father_item_id }}</div>
          <div>归属群：{{ item.group_id }}</div>
          <div>
            <span v-if="item.is_reimbursement==0">待支付：</span>
            <span v-else>待报销：</span>
            {{ item.price/100 }}元
          </div>
          <div>描述：{{ item.description }}</div>
          <div>归属用户：{{ item.display_name }} (id:{{ item.user_id }}) </div>
          <div>创建时间：{{ item.created_time }}</div>
        </div>

        <div v-else>
          <span v-if="user_lever>=4 || item.user_id==user_id" class="delete_button" @click="is_delete_item(item.id,group_id)"> {{ item.id }}.删除 </span>
          <div>
            权重名：{{ item.item_name }} <span class="annotation"> {{ item.new_item_text }} </span>
          </div>
          <div>父节点id：{{ item.father_item_id }}</div>
          <div>归属群：{{ item.group_id }}</div>
          <div>归属用户：{{ item.display_name }} (id:{{ item.user_id }}) </div>
          <div>
            登记增删权重：{{ item.price/100 }}点
          </div>
          <div>创建时间：{{ item.created_time }}</div>
        </div>
        <div v-else>
        </div>

      </div>
		</div>

	</div>
  `,

  methods:{
    visible_item: function (item_user_id,user_id) {
      if(this.just_look_self_item_flag){
        return item_user_id == user_id;
      }else{
        return true;
      }
    },
    is_delete_item:function(item_id,group_id){
      const that = this;
      i_is_delete_item(item_id,group_id)
      .then(function(data){
        if(data.code == '200'){
          that.$emit('item_id', item_id);
        }
      })
    },
  }
});

//结算组件
Vue.component('settlement_ui', {
  props: ['items', 'all_user_info'],
  data: function() {
    return {
      user_info:{
        0:{
          user_id: 0,
          user_name: '公帐',
          identity: '工作',
          user_lever: 0,
          expenses_weight: 0,
          paid_cost: 0,
        },
      }
    }
  },

  template: `
    <div class="settlement_ui">
      <h1>合计：</h1>
        <div class="settlement_ui_div_border">
          总权重：{{ sum_weight/100 }} 点
          <br>待报销： {{ sum_is_reimbursement/100 }} 元 
          <br>待支付：{{ sum_not_reimbursement/100 }} 元 
          <br>公帐结余： {{ sum_price/100 }} 元 
        </div>
    <h3>该账本用户：</h3>
    <div v-for="(info, key) in this.user_info">
        <div class="settlement_ui_div_border">
          用户id：{{ key }} <br>
          用户名：{{ info.display_name }} <br>
          用户身份：{{ info.identity }} <br>
          用户权限：{{ info.user_lever }} <br>
          用户权重：{{ Math.round(info.expenses_weight)/100 }} <br>
          是否为固定支付：{{ info.fixed_credit }} <br>
          平摊部分： {{ Math.round(info.float_cost)/100 }} 元 <br>
          固定部分： {{ Math.round(info.fixed_cost)/100 }} 元 <br>
          用户待支付费用： {{ (Math.round(info.fixed_cost)+Math.round(info.float_cost))/100 }} 元 
        </div>
      </div>
    <slot name="settlement_ui_text"></slot>
    </div>
  `,

  computed: {
    // 计算属性的 getter
    sum_all_value: function () {
      var sum_not_reimbursement = 0;
      var sum_is_reimbursement = 0;
      const that = this;
      for(let i = 0; i<this.items.length; i++){
        if(that.items[i].price == null){
          that.items[i].price = 0;
        }
        if(that.items[i].is_expenses_weight == 0){
          if(that.items[i].is_reimbursement == 0){
            sum_not_reimbursement += parseInt(that.items[i].price);
          }else{
            sum_is_reimbursement += parseInt(that.items[i].price);
          }
        }
      }
      return [sum_not_reimbursement,sum_is_reimbursement];
    },
    sum_not_reimbursement:function(){
      return this.sum_all_value[0];
    },
    sum_is_reimbursement:function(){
      return this.sum_all_value[1];
    },
    sum_price: function () {
      return this.sum_is_reimbursement - this.sum_not_reimbursement;
    },
    sum_weight: function () {
      var sum_expenses_weight = 0;
      const that = this;
      for(let i = 0; i<this.all_user_info.length; i++){
        if(that.all_user_info[i].fixed_credit == 0 && that.all_user_info[i].user_lever > 0){
          sum_expenses_weight += parseFloat(that.all_user_info[i].expenses_weight);
        }
      }
      for(i=0; i<this.items.length; i++){
        if(that.items[i].is_expenses_weight == 1){
          sum_expenses_weight += parseInt(that.items[i].price)
        }
      }
      return sum_expenses_weight;
    },
  },

  created:function(){
    this.set_user_info();
  },

  methods:{
    set_user_info: function () {
      const that = this;
      sum_price = parseInt(this.sum_price),
      sum_weight = parseFloat(this.sum_weight),
      res = {};
      for(let i = 0; i<this.all_user_info.length; i++){
        var user_id = that.all_user_info[i].user_id;
        if(that.all_user_info[i].user_lever != 0){
          res[user_id] = {
            item_amount: 0,
            display_name: that.all_user_info[i].display_name,
            user_lever: that.all_user_info[i].user_lever,
            identity: that.all_user_info[i].meta_value,
            fixed_credit: that.all_user_info[i].fixed_credit,
            expenses_weight: parseFloat(that.all_user_info[i].expenses_weight),
            item_list_id: [],
            fixed_cost: 0,
            float_cost: that.sum_price*(that.all_user_info[i].expenses_weight / sum_weight),
            paid_cost: 0,
            item_amount: 0,
          }
          if(that.all_user_info[i].fixed_credit == 1){
            res[user_id].float_cost = 0;
          }
        }
      };
      for(i=0; i<this.items.length; i++){
        var user_id = that.items[i].user_id;
        if(user_id in res){
          if(that.items[i].is_expenses_weight == 0){
            res[user_id].item_amount += 1;
            res[user_id].item_list_id.push(that.items[i].id);
            if(that.items[i].is_reimbursement == 0){
              res[user_id].fixed_cost += parseInt(that.items[i].price);
            }else{
              res[user_id].fixed_cost -= parseInt(that.items[i].price);
            }
          }else{
            res[user_id].expenses_weight += parseInt(that.items[i].price)
            res[user_id].float_cost += that.sum_price*(parseInt(that.items[i].price) / sum_weight);
          };
          
        }
      }
      this.user_info = res;
    },
  },
});


// vue的主函数实例
var main = new Vue({
  el:'.main',
  data:{
    //切换ui的布局tag，后期如果有需要可以优化成tab的形式，而不是一个bool值
    tag:{
      current_ui_flag: true,
      text: '进行结算',
    },
    user_id: '',
    group_id: '',
    group_name :'',
    user_name: '',
    identity: '',
    user_status: '',
    user_nicename: '',
    user_lever: '',
    new_item_text: '',
    in_this_group_flag: false,
    not_in_this_group_flag: false,
    is_admin_flag: false,
    qrcode_flag: false,
    just_look_self_item_flag:false,
    all_user_info:{
    },
    group_id: null,
    items:[
    ],
    page:{
      page_number: 0,
      page_size: 10,
    }
  },

  created:function(){
    this.set_identity();
    // this.group_id = this.$route.query.group_id;
    const queryString = window.location.search;
    const params = new URLSearchParams(queryString);
    this.group_id = params.get('group_id');
    this.page_number = params.get('page_number');
    this.page_size = params.get('page_size');
    this.set_all_group_info_by_user_id();
  },

  methods:{
    unshift_item_data:function(data){
      this.items.unshift(data);
    },
    delete_item_data_by_id:function(item_id){
      for(let i = 0; i<this.items.length; i++){
        if(this.items[i].id == item_id){
          this.items.splice(i, 1);
          return true;
        }
      }
      return false;
    },
    change_ui: function() {
      this.tag.current_ui_flag = !this.tag.current_ui_flag;
      if(this.tag.current_ui_flag){
        this.tag.text = '进行结算';
      }else{
        this.tag.text = '查看详情';
      };
    },
    change_qrcode: function() {
      this.qrcode_flag = !this.qrcode_flag;
    },
    set_identity:function(){
      const queryString = window.location.search;
      const params = new URLSearchParams(queryString);
      this.user_id= params.get('user_id');
      this.group_id= params.get('group_id');
      this.group_name= params.get('group_name');
      this.user_status= params.get('user_status');
      // this.user_nicename= params.get('user_nicename');
      this.set_all_user_info();
    },
    set_all_user_info:function(){
      const that = this;
      i_get_all_user_info_by_group_id(this.group_id)
        .then(function(data){
          that.all_user_info = data.data;
          for(let i = 0; i<data.data.length; i++){
            if(data.data[i].user_id == that.user_id){
              that.in_this_group_flag = true;
              that.user_lever = data.data[i].user_lever;
              that.user_name =  data.data[i].display_name;
              that.identity =  data.data[i].identity;
              that.fixed_credit =  data.data[i].fixed_credit;
              if(data.data[i].user_lever >= 5){
                that.is_admin_flag = true;
              }
              return true;
            }
          }
          that.not_in_this_group_flag = true;
          return false;
      });
    },
    set_all_group_info_by_user_id:function(){
      const that = this;
      page_number = this.page.page_number;
      page_size = this.page.page_size;
      i_get_all_info_item_by_group_id(page_number,page_size,this.group_id)
        .then(function(data){
          var treeData = build_tree(data.data);
          console.log(treeData); // 输出树状结构数据
          that.items = treeData;
      });
    },
    add_page_number:function(){
      this.page.page_number += this.pager.page_size;
    },
    min_page_number:function(){
      this.page.page_number -= this.pager.page_size;
    },
  },
});