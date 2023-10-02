<template>
  <div class="generate_qrcode" v-show="visible_qrcode_flag">
    <div class="qrcode_border">
      <div class="qrcode" id="qrcode"></div>
      <input id = 'creat_qrcode' v-if="visible_req_finish_flag" class="center-container" @click="share_qrcode" type="button" value="分享该账本到群聊"/>
      <span v-if="visible_text_flag"> {{ url }} </span>
    </div>
  </div>
</template>

<script>
export default {

  name: "I_Qrcode",

  props: {
    group: {
      type: Object,
      required: true,
    },
    visible_qrcode_flag: {
      type: Boolean,
      required: true,
    },
  },

  data() {
    return {

      user_info: {},
      group_id: 0,
      rd_pwd: null,
      visible_text_flag: false,
      visible_req_finish_flag: false,

    };
  },

  computed: {
    url() {
      var url_link = window.location.protocol + "//" + window.location.host + "/app?name=" + "I_Group_JoinByRdpwd" + "&rd_pwd=" + this.rd_pwd + "&group_id=" + this.group_id;
      return url_link;
    },
  },

  beforeRouteEnter(to, from, next) {
    next(vm => {});
  },

  created:function(){
    const that = this;
    this.group_id = this.group.group_id;
    this.visible_req_finish_flag = false;
    this.$store.dispatch('get_user_info').then(function (data) {
      that.user_info = data;
    })
    i_get_rd_pwd_by_group_id(this.group_id)
    .then(function(data){
      that.rd_pwd = data.data;
      that.created_qrcode_url_link();
      that.visible_req_finish_flag = true;
    })

  },

  activated() {
  },

  mounted:function(){
  },

  methods:{

    created_qrcode_url_link: function() {
      var url_link = this.url;
      // document.getElementById("qrcode").innerHTML = '';
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
      var share_text = '你的好友“ ' + this.user_info.user_name + ' ”邀请你加入账本“ ' + this.group.name + ' ”:\n';
      share_text += this.url
      let flag = copyText(share_text);
      //复制完成
      if(flag){
        alert('你已将链接复制到剪贴板，请粘贴并发送给邀请人。');
      }else{
        alert('该浏览器不支持自动复制，请手动复制下方链接并发送给邀请人。');
      }
    }
    
  }
};
</script>