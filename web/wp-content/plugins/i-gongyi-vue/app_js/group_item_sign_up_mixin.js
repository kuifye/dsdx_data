const group_item_sign_up_mixin = {
  methods:{
    i_item_sign_up:function() {
      const that = this;
      const item_name = document.getElementById('item_name').value;
      const price = parseInt(100*parseFloat(document.getElementById('price').value));
      const description = document.getElementById('description').value;
      let is_reimbursement = document.getElementById('is_reimbursement').value;
      const group_id = document.getElementById('group_id').innerHTML;
			let is_expenses_weight = 0;
      if(is_reimbursement==2){
        is_reimbursement=0;
        is_expenses_weight	= 1;
				console.log(is_expenses_weight);
      }
      i_sign_up(item_name, description, price, group_id, is_reimbursement, is_expenses_weight)
        .then(function(data){
          if(data.code == '200'){
            function getData(n){
              n=new Date(n);
              return n.toLocaleDateString().replace(/\//g, "-") + " " + n.toTimeString().substr(0, 8);
            }
            date_now = getData(new Date())
            item_data={
              id : data.data,
              created_time: date_now,
              item_id: data.item_id,
              item_name: item_name,
              display_name: that.user_name,
              user_name: that.user_name,
              price: price,
              description: description,
              group_id: group_id,
              user_id: that.user_id,
              is_reimbursement: is_reimbursement,
              is_expenses_weight: is_expenses_weight,
              new_item_text: ' .New',
            };
            for(i = 0; i<that.all_user_info.length; i++){
              if(that.all_user_info[i].user_id == that.user_id){
                item_data.expenses_weight = that.all_user_info[i].expenses_weight;
                item_data.user_lever = that.all_user_info[i].user_lever;
              }
            }
            that.$emit('item_data', item_data);
            alert("添加成功");
          }
        })
    }
  }
}
