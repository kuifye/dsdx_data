<template>
  <div class='group_ui_card' >
    
    <!-- <a class='i_h2'>合计：</a> -->

    <!-- <div class='group_ui_card_border'>
      <a class='i_h4'>公帐结余： {{ -sum_price/100 }} 元 </a>
      <br>总权重：{{ sum_weight/100 }} 点
      <br>待支付：{{ sum_price/100 }} 元 
      <br>待报销： {{ sum_reimbursement/100 }} 元 
    </div> -->

    <a class='i_h2'>该账本用户：</a>
    <span @click="go_group_ui_url" class="delete_button">返回</span>

    <div v-for="(info, key) in this.user_info">
      <div class='group_ui_card_border'>
        <a class='i_h4'>

          <span 
            v-if="((Math.round(info.float_cost))/100-Math.round(info.fixed_cost)/100)>=0">
            {{ info.display_name }} 待支付: {{ Math.abs((Math.round(info.float_cost))/100-Math.round(info.fixed_cost)/100) }} 元 
          </span>

          <a 
            v-else>
            {{ info.display_name }} 待收款: {{ Math.abs((Math.round(info.float_cost))/100-Math.round(info.fixed_cost)/100) }} 元 
          </a>
          
        </a>

        <!-- <br>用户身份：{{ info.identity }} <br>
        用户权限：{{ info.user_lever }} <br>
        用户权重：{{ Math.round(info.expenses_weight)/100 }} <br>
        是否为固定支付：{{ info.fixed_credit }} <br>
        平摊部分： {{ -Math.round(info.float_cost)/100 }} 元 <br>
        固定部分： {{ Math.round(info.fixed_cost)/100 }} 元 <br> -->

      </div>
    </div>

  </div>
</template>

<script>
export default {
  name: 'I_GroupSettlement_Comput',

  props: {

    items: {
      type: Object,
      required: true,
    },

    all_user_info: {
      type: Object,
      required: true,
    },

  },

  components: {
  },

  setup () {
  },

  emits: [],

  data:function(){
    return{
      user_info:{},
    }
  },

  computed: {

    // 计算属性的 getter
    sum_all_value: function () {
      var sum_price = 0;
      var sum_reimbursement = 0;
      var sum_expenses_weight = 0;
      const that = this;
      for(let i = 0; i<this.items.length; i++){
        sum_price += parseInt(that.items[i].price);
        sum_reimbursement += parseInt(that.items[i].reimbursement);
        sum_expenses_weight += parseInt(that.items[i].expenses_weight);
      }
      return [sum_reimbursement,sum_price,sum_expenses_weight];
    },

    sum_reimbursement:function(){
      return this.sum_all_value[0];
    },

    sum_price:function(){
      return this.sum_all_value[1];
    },
    
    sum_expenses_weight:function(){
      return this.sum_all_value[2];
    },

    sum_res_value: function () {
      return this.sum_reimbursement - this.sum_price;
    },

    //包括用户面板的总计权重
    sum_weight: function () {
      var sum_expenses_weight = 0;
      const that = this;
      for(let i = 0; i<this.all_user_info.length; i++){
        if(that.all_user_info[i].fixed_credit == 0 && that.all_user_info[i].user_lever > 0){
          sum_expenses_weight += parseFloat(that.all_user_info[i].expenses_weight);
        }
      }
      sum_expenses_weight += that.sum_expenses_weight;

      return sum_expenses_weight;
    },

  },

  beforeRouteEnter(to, from, next) {
    next(vm => {});
  },

  async beforeRouteUpdate(to, from) {
  },

	created:function(){
    this.set_user_info();
  },

  activated() {
    this.set_user_info();
  },
	
  methods: {

    go_group_ui_url:function(){
      this.$router.push(this.$store.getters.group_ui_url);
    },

    set_user_info: function () {
      const that = this;
      var sum_price = parseInt(this.sum_price);
      var sum_weight = parseFloat(this.sum_weight);
      var res = {};
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
      for(let i = 0; i<this.items.length; i++){
        var user_id = that.items[i].user_id;
        if(user_id in res){
            res[user_id].item_amount += 1;
            res[user_id].item_list_id.push(that.items[i].id);
            res[user_id].fixed_cost += parseInt(that.items[i].reimbursement);
            res[user_id].fixed_cost -= parseInt(that.items[i].price);
            res[user_id].expenses_weight += parseInt(that.items[i].expenses_weight)
            res[user_id].float_cost += that.sum_res_value*(parseInt(that.items[i].expenses_weight) / sum_weight);
        }
      }
      this.user_info = res;
    },

  },
};
</script>