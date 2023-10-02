<?php 
//获取wordpress原生页眉
get_header();
// 如果直接引用i_vue组件的短代码，导致vue版本冲突将会报错。
// echo do_shortcode('[i_vue_i_group_api]');
// do_action( 'hestia_before_single_post_wrapper' );
?>

<script>
		var process = {
				platform: 'win32',
				env: {},
		}
</script>
<!-- 引入访问i_api接口的js文件 -->
<script src="/wp-content/themes/hestia-child-sog/js/i_group_api.js"></script>
<!-- 构建.vue文件类型 -->
<script src="/wp-content/themes/hestia-child-sog/js/hash-sum.js"></script>
<script src="/wp-content/themes/hestia-child-sog/js/vue3/compiler-sfc.global.js"></script>
<script src="/wp-content/themes/hestia-child-sog/js/systemjs-6.10.3/system.js"></script>
<script src="/wp-content/themes/hestia-child-sog/js/systemjs-vue-0.0.1.js"></script>
<script src="/wp-content/themes/hestia-child-sog/js/systemjs-babel-0.3.1/systemjs-babel.js"></script>
<!-- 构建vue3 -->
<script src="/wp-content/themes/hestia-child-sog/js/vue3/vue.global.js"></script>
<script src="/wp-content/themes/hestia-child-sog/js/vue3/vue-router.global.js"></script>
<script src="/wp-content/themes/hestia-child-sog/js/vue3/vuex.global.js"></script>
<!-- 引入二维码qrcode.js -->
<script src="/wp-content/themes/hestia-child-sog/js/qrcode.js"></script>
<!-- 引入element-ui.js -->
<link rel="stylesheet" type="text/css" href="/wp-content/themes/hestia-child-sog/js/element-ui-local/index.css"/>
<script src="/wp-content/themes/hestia-child-sog/js/element-ui-local/index.full.js" type="text/javascript" charset="utf-8"></script>
<script src="/wp-content/themes/hestia-child-sog/js/element-ui-local/icons-vue.js" type="text/javascript" charset="utf-8"></script>
<!-- 引入clipboard.min.js -->
<script src="/wp-content/themes/hestia-child-sog/js/clipboard.js-master/dist/clipboard.min.js"></script>
<!-- 引入systemjs-importmap -->
<script type="systemjs-importmap">
		{
			"imports": {
				"vue": "app:vue"
			}
		}
</script>

<!-- 页眉 -->

<div id="primary" class="boxed-layout-header page-header-app header-small" data-parallax="active" style="transform: translate3d(0px, 0.444444px, 0px);">
	<div class="container">
			<div class="row">
					<div class="col-md-10 col-md-offset-1 text-center">
						<h1 class="hestia-title ">报表系统</h1>
					</div>
			</div>
	</div>
	<div class="header-filter" style="background-image: url(/wp-content/themes/hestia-child-sog/custom/jpg/header-filter.jpg);"></div>
</div>

<!-- <div class="main  main-raised "> -->
<div class="main" style="z-index: 1000;">
	<!-- <div class="container"> -->
		<!-- <div class="section section-text"> -->
		<!-- <div class="div_margin_top" sytle="height: 75px;width: 100%;"></div> -->

			<!-- 主页面 -->
			<div id="main_app" class="main_app"></div>
			
		<!-- <div class="div_margin_top" sytle="height: 75px;width: 100%;"></div> -->
	<!-- </div> -->
</div>
<!-- </div> -->

<script>
	//引入vuex的store进行数据存储，和router路由，定位页面
	System.set('app:vue', Vue);
	//引入路由
	System.import('/wp-content/themes/hestia-child-sog/custom/router/index.js').then(function(m) {
		const router = m.default;
	//引入vuex储存数据
	System.import('/wp-content/themes/hestia-child-sog/custom/vuex/store.js').then(function(m) {
		const store = m.default;
	//引入vue主程序并注册路由和store
	System.import('/wp-content/themes/hestia-child-sog/custom/App.vue').then(function(m) {
		var app = Vue.createApp(m.default)
		.use(router)
		.use(store)
		.use(ElementPlus)
		.mount('#main_app');
	});
	});
	});
</script>

<?php
// 获取页脚
// if ( ! is_singular( 'elementor_library' ) ) {
// 	do_action( 'hestia_blog_related_posts' );
// }
?>
<?php
get_footer();
?>
