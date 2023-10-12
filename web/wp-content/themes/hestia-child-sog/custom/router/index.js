//基础页面
import NotFound from "../components/NotFound.vue";
import Home from "../components/Home.vue";
//测试用，可删除
import Test from "../components/Test.vue";
//中兴和苑日常饮食
import I_SignUp from "../components/I_SignUp.vue";
//查看用户所有的账本
import I_GroupUi from "../components/I_GroupUi/I_GroupUi.vue";
//查看某个特定的账本
import I_GroupItem from "../components/I_GroupItem/I_GroupItem.vue";
//结算账本
import I_GroupSettlement from "../components/I_GroupSettlement/I_GroupSettlement.vue";
//设置用户的身份
import I_SetIdentity from "../components/I_SetIdentity.vue";
//用户可以通过二维码受邀请，进入该页面，核验正确随机密码后自动进群
import I_Group_JoinByRdpwd from "../components/I_Group_JoinByRdpwd.vue";

const router = VueRouter.createRouter({
  history: VueRouter.createWebHistory(),
  routes: [
		{
			path: '/app/Test', 
			name: 'Test', 
			component: Test,
		},{
			path: "/:pathMatch(.*)*",
			name: "NotFound",
			component: NotFound,
		},{
			path: '/app', 
			name: 'Home',
			component: Home,
		},{
			path: '/app/I_SignUp', 
			name: 'I_SignUp', 
			component: I_SignUp,
		},{
			path: '/app/I_GroupUi', 
			name: 'I_GroupUi', 
			component: I_GroupUi,
		},{
			path: '/app/I_GroupItem', 
			name: 'I_GroupItem', 
			component: I_GroupItem,
		},{
			path: '/app/I_GroupSettlement', 
			name: 'I_GroupSettlement', 
			component: I_GroupSettlement,
		},{
			path: '/app/I_SetIdentity', 
			name: 'I_SetIdentity', 
			component: I_SetIdentity,
		},{
			path: '/app/I_Group_JoinByRdpwd/:rd_pwd/:group_id', 
			name: 'I_Group_JoinByRdpwd', 
			component: I_Group_JoinByRdpwd,
		},
  ]
});

export default router;