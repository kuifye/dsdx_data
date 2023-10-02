<template>

  <div class="common-layout">
    <el-container>
      <el-aside width="200px">Aside</el-aside>
      <el-container>
        <el-header>Header</el-header>
        <el-main>Main</el-main>
        <el-footer>Footer</el-footer>
      </el-container>
    </el-container>
  </div>

  <table class="demo-border">
    <tbody>
      <tr>
        <td class="text">Name</td>
        <td class="text">Thickness</td>
        <td class="line">Demo</td>
      </tr>
      <tr>
        <td class="text">Solid</td>
        <td class="text">1px</td>
        <td class="line">
          <div></div>
        </td>
      </tr>
      <tr>
        <td class="text">Dashed</td>
        <td class="text">2px</td>
        <td class="line">
          <div class="dashed"></div>
        </td>
      </tr>
    </tbody>
  </table>

  <el-row class="mb-4">
    <el-button>Default</el-button>
    <el-button type="primary">Primary</el-button>
    <el-button type="success">Success</el-button>
    <el-button type="info">Info</el-button>
    <el-button type="warning">Warning</el-button>
    <el-button type="danger">Danger</el-button>
  </el-row>

  <el-row class="mb-4">
    <el-button plain>Plain</el-button>
    <el-button type="primary" plain>Primary</el-button>
    <el-button type="success" plain>Success</el-button>
    <el-button type="info" plain>Info</el-button>
    <el-button type="warning" plain>Warning</el-button>
    <el-button type="danger" plain>Danger</el-button>
  </el-row>

  <div class="flex">
    <el-button type="primary" :icon="Edit" />
    <el-button type="primary" :icon="Share" />
    <el-button type="primary" :icon="Delete" />
    <el-button type="primary" :icon="Search">Search</el-button>
    <el-button type="primary">
      Upload<el-icon class="el-icon--right"><Upload /></el-icon>
    </el-button>
  </div>
</template>

<script>
export default {
  name: 'Test',

  props: {
  },

  components: {
  },

  setup () {
  },

  data:function(){
    return{
    }
  },

  beforeRouteEnter(to, from, next) {
    next(vm => {});
  },

  async beforeRouteUpdate(to, from) {
  },

	created:function(){
  },

  activated() {
  },
	
  methods: {
  },
};
</script>