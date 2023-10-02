const store = Vuex.createStore({

  state: {

		user_info:{
			done_flag: null,
			user_id: '',
			user_name: '',
			identity: '',
			user_status: '',
			user_nicename: '',
		},

		//点击管理账本界面时，会转跳到这个链接
		group_ui_url: '/app/I_GroupUi',

		//账本群的缓存
		groups: {
			//设置当前正在计算的列表id
			current_group_id: {
				'demo':'-1',
			},
			//示例
			'-1':{
				//0代表没有查询，1代表查询中，2代表可以返回group_raw
				//3代表查询中，4代表可以返回全部信息
				done_flag: 0,
				//示例群的信息，如群名称
				current_user_info:{
					user_lever:0,
				},
				group_raw:{
					name: '示例',
					group_id: 'demo',
					description: "日常饮食平摊",
				},
				all_user_info:{
				},
				//item信息
				items_raw:{
				},
				//开关信息flag_raw
				flag_raw:{
					in_this_group_flag:1,
				},
			}

		},
  },

	getters: {

		user_id: state => state.user_info.user_id,
		user_identity: state => state.user_info.identity,
		user_info: state => state.user_info,
		user_info_done_flag: state => state.user_info.done_flag,

		group_ui_url: state => state.group_ui_url,

		groups: state => state.groups,
		current_group_id: state => state.groups.current_group_id,
		current_group_by_name(state) {
      return function(name) {
        const current_group_id = state.groups.current_group_id[name];
				return state.groups[current_group_id];
      }
    },

  },
	
  mutations: {

		//设置身份
		set_user_info(state,user_info){
			state.user_info = user_info;
		},
		set_user_id(state,user_id){
			state.user_info.user_id = user_id;
		},
		set_user_identity(state,identity){
			state.user_info.identity = identity;
		},
		set_user_info_done_flag(state,user_info_done_flag){
			state.user_info.done_flag = user_info_done_flag;
		},

		//管理账本转跳的url地址
		set_group_ui_url(state,group_ui_url){
			// console.log('this.getters.group_ui_url:  '+group_ui_url);
			state.group_ui_url = group_ui_url;
		},
		set_group_ui_url_default(state){
			// console.log('this.getters.group_ui_url:  /app/I_GroupUi');
			state.group_ui_url = '/app/I_GroupUi';
		},

		//设置账本groups相关信息
		set_current_group_id(state,dict){
			state.groups.current_group_id[dict.name] = dict.data;
			if(!state.groups.hasOwnProperty(state.groups.current_group_id[dict.name])){
				state.groups[state.groups.current_group_id[dict.name]] = {};
				state.groups[state.groups.current_group_id[dict.name]].done_flag = 0;
				state.groups[state.groups.current_group_id[dict.name]].current_user_info = {user_lever:0};
				state.groups[state.groups.current_group_id[dict.name]].flag_raw = {in_this_group_flag:true};
			}
		},
		set_current_group_done_flag(state,dict){
			if(dict.data > state.groups[state.groups.current_group_id[dict.name]].done_flag){
				state.groups[state.groups.current_group_id[dict.name]].done_flag = dict.data;
			};
			// console.log('请求数据中：',state.groups[state.groups.current_group_id[dict.name]].done_flag);
		},
		set_group_raw(state,dict){
			state.groups[state.groups.current_group_id[dict.name]].group_raw = dict.data;
		},
		set_all_user_info(state,dict){
			state.groups[state.groups.current_group_id[dict.name]].all_user_info = dict.data;
		},
		set_items_raw(state,dict){
			state.groups[state.groups.current_group_id[dict.name]].items_raw = dict.data;
		},
		set_group_user_info(state,dict){
			state.groups[state.groups.current_group_id[dict.name]].current_user_info = dict.data;
		},
		set_group_user_info_user_lever(state,dict){
			state.groups[state.groups.current_group_id[dict.name]].current_user_info.user_lever = dict.data;
		},
		set_flag_raw(state,dict){
			state.groups[state.groups.current_group_id[dict.name]].flag_raw = dict.data;
		},
		set_flag_raw_in_this_group_flag(state,dict){
			state.groups[state.groups.current_group_id[dict.name]].flag_raw.in_this_group_flag = dict.data;
		},

		//增加一条记录
		add_item_by_url_name(state,dict){
			state.groups[state.groups.current_group_id[dict.name]].items_raw.unshift(dict.data);
		},
		//删除一条记录
		delete_item_by_url_name_and_item_id(state,dict){
			const that = state;
			const group_id = state.groups.current_group_id[dict.name];
      for(let i = 0; i<state.groups[group_id].items_raw.length; i++){
        if(that.groups[group_id].items_raw[i].id == dict.data){
          that.groups[group_id].items_raw.splice(i, 1);
					break;
        }
      }
		}

  },

	actions: {

		//异步flag,null代表还没开始查询，false正在查询,true查询完毕了
		get_user_info_done_flag(context){
			const that = context;
			return new Promise(function(resolve){
				resolve(context.getters.user_info_done_flag)
			});
		},

		//获取用户信息
		get_user_info(context){
			const that = context;
			return new Promise(function(resolve){
				//判断是否在向后端查询信息
				if(that.getters.user_info_done_flag != null){
					//如果正在查询，等待查询好后返回信息
					if(!that.getters.user_info_done_flag){
						wait_for(that.dispatch, 'get_user_info_done_flag', resolve, that.dispatch, 'get_user_info', true)
					}else{
						//如果已经查询好了，返回信息
						resolve(that.getters.user_info)
					}
				}else{
					//如果还没开始查询，开始查询
					that.commit('set_user_info_done_flag', false)
					i_set_identity()
						.then(function(data){
							that.commit('set_user_info', data)
							that.commit('set_user_id', data.id)
							that.commit('set_user_info_done_flag', true)
							resolve(data)
					});
				}
			});
		},

		//获取群组信息
		get_current_group_done_flag(context,name){
			const that = context;
			return new Promise(function(resolve){
				resolve(that.getters.current_group_by_name(name).current_group_done_flag)
			});
		},

		get_current_group(context,name){
      const that = context;
			return new Promise(function(resolve){
				if(that.getters.current_group_by_name(name).current_group_done_flag>=2){
					resolve(that.getters.current_group_by_name(name).group_raw)
				}else{
					if(that.getters.current_group_by_name(name).current_group_done_flag == 1){
						wait_for_by_name(that.dispatch, 'get_current_group_done_flag', resolve, that.dispatch, 'get_current_group', 2,name)
					}else{
						that.commit('set_current_group_done_flag', {data:1,name:name})
						i_get_group_info_by_id(that.getters.current_group_id[name])
						.then(function(data){
							if(data.code == '200'){
								that.commit('set_group_raw', {data:data.data,name:name})
								that.commit('set_current_group_done_flag', {data:2,name:name})
							}
							resolve(data.data)
						});
					}
				}
			})
    },

		get_current_group_all_info(context,name){
			const that = context;
			var group_raw = {};
			var all_user_info = {};
			var items_raw = [];
			return new Promise(function(resolve){

				that.dispatch('get_current_group',name).then(function (data) {
					group_raw = data;
					var current_group = that.getters.current_group_by_name(name)
					//4-直接获取数据
					if(current_group.current_group_done_flag>=4){
						resolve(current_group);
					}else{
						//3-等待变成4
						if(current_group.current_group_done_flag == 3){
							wait_for_by_name(that.dispatch, 'get_current_group_done_flag', resolve, that.dispatch, 'get_current_group',4,name)
						}
						//3-开始获取数据
						that.commit('set_current_group_done_flag', {data:3,name:name})
						i_get_all_user_info_by_group_id(that.getters.current_group_id[name]).then(function (data) {
							all_user_info = data.data;
							that.commit('set_all_user_info', {data:all_user_info,name:name});
							i_get_all_info_item_by_group_id_with_child_list(that.getters.current_group_id[name]).then(function(data){
								items_raw = data.data;
								that.commit('set_items_raw', {data:items_raw,name:name});
								//4-通过api获取到数据
								that.dispatch('set_current_user_info_by_all_user_info',name).then(function(data){
									// console.log(data);
									that.commit('set_current_group_done_flag', {data:4,name:name})
									resolve(that.getters.current_group_by_name(name))
								})
      				});
						});
					}
				})
			})
		},

		set_current_user_info_by_all_user_info(context,name) {
      const that = context;
			return new Promise(function(resolve){
				that.dispatch('get_user_info').then(function (data) {
					var user_id = data.user_id;
					var current_group = that.getters.current_group_by_name(name);
					for (let i = 0; i < current_group.all_user_info.length; i++) {
						if (current_group.all_user_info[i].user_id == user_id) {
							that.commit('set_group_user_info_user_lever', {data:parseInt(current_group.all_user_info[i].user_lever),name:name});
							that.commit('set_flag_raw_in_this_group_flag', {data:true,name:name});
							resolve(true);
						};
					};
					resolve(false);
				});
			})
    },
		
  },

})

export default store;
