//--------------------------------------------------------------------------
// 用于显示用户的账本信息
// http://localhost/i_group/?group_id=0&rd_pwd=7M0YvsMFpsN

var main = new Vue({
  el:'.main',
  data:{
    promise_flag: false,
    rd_pwd:'',
    group_info:{
      id:'',
    },
  },
  created:function(){
    this.set_info();
    this.join_group();
    this.get_group_info_by_id();
  },
  methods:{
    set_info:function(){
      const queryString = window.location.search;
      const params = new URLSearchParams(queryString);
      this.rd_pwd = params.get('rd_pwd');
      this.group_info.id = params.get('group_id');
    },
    join_group:function(){
      const that = this;
      i_join_group(this.group_info.id,this.rd_pwd)
      .then(function(data){
        if(data.code == "200"){
          that.href_to_group_item();
        }else if(data.code == '409'){
          that.href_to_group_item();
        };
      })
    },
    get_group_info_by_id:function(){
      const that = this;
      i_get_group_info_by_id(this.group_info.id)
        .then(function(data){
          if(data.code == '200'){
            that.group_info = data.data;
            that.href_to_group_item();
          }
        })
    },
    href_to_group_item:function(){
      const that = this;
      if(this.promise_flag){
        group_id = that.group_info.id;
        group_name = that.group_info.name;
        user_id = parseInt(document.getElementById('user_id').innerHTML);
        url = `/账本内容/?group_id=${group_id}&user_id=${user_id}&group_name=${group_name}`;
        window.location.href = url;
      }else{
        that.promise_flag = true;
      }
    },
  }
});
