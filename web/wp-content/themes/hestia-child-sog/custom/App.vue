<template>
  <div>

    <el-container class="main_el-container">
      <el-container class="main_el-container">
        <el-header class="main_el-header">
          
          <el-menu
          :default-active="activeIndex"
          text-color="#FEFFFF"
          background-color="rgb(50, 52, 55)"
          active-text-color="#e91e63"
          class="app_el-menu"
          mode="horizontal"
          @select="handleSelect"
          router
          >
            <el-menu-item index="/app">主页</el-menu-item>
            <el-menu-item index="/app/Test">测试</el-menu-item>
            <el-menu-item index="/app/I_SignUp">餐饮签到</el-menu-item>
            <el-menu-item index="/app/I_GroupUi">账本管理</el-menu-item>
            <!-- <el-menu-item index="/app/I_GroupSettlement">结算账本</el-menu-item>
            <el-menu-item index="/app/I_SetIdentity">设置身份</el-menu-item> -->

          </el-menu>

        </el-header>
        <el-main class="main_el-main">

            <router-view v-slot="{ Component }">
              <keep-alive>
                <component :is="Component"/>
              </keep-alive>
            </router-view>

        </el-main>
        <!-- <el-footer class="main_el-footer">
        </el-footer> -->
      </el-container>
    </el-container>

  </div>
</template>

<script>
export default { 

  name: "App",

  //获取get参数
  created:function(){

    const queryString = window.location.search;
    const params = new URLSearchParams(queryString);
    var name = params.get('name');
    this.go_url(name)

  },

  computed: {
  },

  beforeRouteUpdate(to, from) {
  },

  methods: {

    //转跳到name相关的组件
    go_url:function(name){
    const queryString = window.location.search;
    const params = new URLSearchParams(queryString);
    if(name == 'I_Group_JoinByRdpwd'){
      var rd_pwd = params.get('rd_pwd');
      var group_id = params.get('group_id');
      this.$router.replace({ 
        name: name,
        params: {
          rd_pwd: rd_pwd,
          group_id: group_id,
        }
      });
    }else if(name == 'I_SignUp'){
      this.$router.replace({name: "I_SignUp"})
    }else if(name == 'I_SetIdentity'){
      this.$router.replace({name: "I_SetIdentity"})
    }else{
      return;
    }
  },

  }

};

</script>

<style>
  .main_el-header, .main_el-footer {
    background-color: #323437;
    color: #333;
    line-height: 60px;
  }
  
  .main_el-aside {
    background-color: #F1F1F1;
    color: #333;
    line-height: 200px;
  }
  
  .main_el-main {
    min-height: 100vh;
    background-color: #FEFFFF;
    color: #333;
  }
  
  body > .main_el-container {
    margin-bottom: 40px;
  }
  
  .main_el-container:nth-child(5) .main_el-aside,
  .main_el-container:nth-child(6) .main_el-aside {
    line-height: 260px;
  }
  
  .main_el-container:nth-child(7) .main_el-aside {
    line-height: 320px;
  }
</style>