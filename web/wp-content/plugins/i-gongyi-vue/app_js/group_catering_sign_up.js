
var main = new Vue({
  el:'.main',
  data:{
		show_data: "等待签到",
		times_i_count: '?',
		sign_up_flag: false,
		item_info:{
			item_name: '日常饮食',
			description: '小组内循环饮食平摊费用',
			price: 100,
			group_id: 0,
			is_reimbursement: 0,
			is_expenses_weight: 1,
		},
  },

  created:function(){
		this.count();
  },

  methods:{
		sign_up:function(){
			const that = this;
			this.show_data = '正在签到...'
			i_sign_up(this.item_info.item_name, this.item_info.description, this.item_info.price, this.item_info.group_id, this.item_info.is_reimbursement, this.item_info.is_expenses_weight)
				.then(function(data){
					if(data.code == '200'){
						that.show_data = '√';
						that.sign_up_flag = true;
						alert('签到成功')
						this.count();
					}
				})
		},
		count:function(){
			const that = this;
			i_count()
				.then(function(data){
					that.times_i_count = parseInt(data.data);
				})
		},
  },
});