//-----------------------------------------------------common----------------------------------------------------------------------------

// 获取时间
function getData(n){
	n=new Date(n);
	return n.toLocaleDateString().replace(/\//g, "-") + ":" + n.toTimeString().substr(0, 8);
}

//返回一页
function history_go_back(){
  history.back();
}

//复制到剪切板
function copyText(text) {
	var textareaEl = document.createElement('textarea');
	textareaEl.setAttribute('readonly', 'readonly'); // 防止手机上弹出软键盘
	textareaEl.value = text;
	document.body.appendChild(textareaEl);
	textareaEl.select();
	var res = document.execCommand('copy');
	document.body.removeChild(textareaEl);
	console.log("已经复制到剪贴板:\n" + text);
	return res;
}

//将带有子节点的数据转换成树状图
function build_tree(data) {
  // 创建一个空的树
  var tree = [];
  var map = {};
  // 遍历数据，将每个节点添加到树中
  data.forEach(function (item) {
    // 创建节点对象
    var node = {
      id: item.id,
      user_id: item.user_id,
      user_lever: item.user_lever,
      expenses_weight: item.expenses_weight,
      created_time: item.created_time,
      item_name: item.item_name,
      price: item.price,
      description: item.description,
      group_id: item.group_id,
      reimbursement: item.reimbursement,
      expenses_weight: item.expenses_weight,
      user_login: item.user_login,
      user_nicename: item.user_nicename,
      display_name: item.display_name,
      children: []
    };
    // 将节点添加到映射表中
    map[node.id] = node;
    // 如果有父节点，将当前节点添加到父节点的 children 数组中
    if (item.father_item_id !== null && map[item.father_item_id]) {
      map[item.father_item_id].children.push(node);
    } else {
      // 没有父节点的话，说明是根节点
      tree.push(node);
    }
  });
  return tree;
}

//转换成时戳
function timeToTimestamp(time){
	let timestamp = Date.parse(new Date(time).toString());
	return timestamp;
}

//获取当前时间
function getData(n){
	n=new Date(n);
	return n.toLocaleDateString().replace(/\//g, "-") + " " + n.toTimeString().substr(0, 8);
}

//挂起等待flag，符合条件后则释放线程
function wait_for(condition, dispatch, resolve_fuc, callback, getters, flag = true) {
	condition(dispatch).then(function(data){
		if(data != flag) {
			window.setTimeout(wait_for.bind(null, condition, dispatch, resolve_fuc, callback, getters, flag), 100); /* this checks the flag every 100 milliseconds*/
		} else {
			callback(getters).then(function(data){
				resolve_fuc(data);
			});
		}
	});
}

//挂起等待flag，符合条件后则释放线程,带参数
function wait_for_by_name(condition, dispatch, resolve_fuc, callback, getters, flag, name) {
	condition(dispatch,name).then(function(data){
		if(data != flag) {
			window.setTimeout(wait_for.bind(null, condition, dispatch, resolve_fuc, callback, getters, flag, name), 100); /* this checks the flag every 100 milliseconds*/
		} else {
			callback(getters,name).then(function(data){
				resolve_fuc(data);
			});
		}
	});
}

//-----------------------------------------------------可调用的api接口----------------------------------------------------------------------------

//通过costs_id删除一组item
function i_login(username, password, rememberme = true){
	return new Promise(function(resolve){
		jQuery.ajax({
			url: "/?rest_route=/gongyi/i_login",
			type: "post",
			dataType: "json",
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
			},
			data:{
				"username" : username,
				"password" : password,
				"rememberme" : rememberme,
			},
			success: function (data) {
				resolve(data);
			}
		});
	});
};

//获取一个群组的随机密码
function i_get_rd_pwd_by_group_id(group_id){
	return new Promise(function(resolve){
		jQuery.ajax({
			url: "/?rest_route=/gongyi/i_get_rd_pwd_by_group_id&group_id="+group_id,
			type: "get",
			dataType: "json",
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
			},
			success: function (data) {
				resolve(data);
			}
		})
	});
};

//加入一个群组
function i_join_group(group_id,rd_pwd){
	return new Promise(function(resolve){
		jQuery.ajax({
			url: "/?rest_route=/gongyi/i_join_group&group_id=" + group_id + "&rd_pwd=" + rd_pwd,
			type: "get",
			dataType: "json",
			//wp进行的api的nonce验证，如果不验证会返回未登陆，同下
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
			},
			success: function (data) {
				if(data.code == '401'){
					window.location.href= window.location.origin + '/wp-login.php';
				}else if(data.code == '4011'){
					window.location.href='/%e8%b4%a6%e6%9c%ac%e7%ae%a1%e7%90%86/%e8%ae%be%e7%bd%ae%e8%ba%ab%e4%bb%bd/';
				}else if(data.code == '409'){
					alert(data.msg);
				}else{
					console.log(data);
					alert('正在申请中，请等待管理员审核。');
				}
				resolve(data);
			}
		})
	});
};

//设置管理员
function i_set_admin_by_user_id(user_id, group_id){
	return new Promise(function(resolve){
		jQuery.ajax({
			url: "/?rest_route=/gongyi/i_set_admin_by_user_id&user_id="+user_id+"&group_id="+group_id,
			type: "get",
			dataType: "json",
			//wp进行的api的nonce验证，如果不验证会返回未登陆，同下
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
			},
			success: function (data) {
				if(data.code == '4025'){
					alert(data.msg);
				}else if(data.code == '404'){
					alert(data.msg);
				}else{
					alert(data.msg);
					resolve(data)
				}
			}
		})
	});
};

//移除管理员
function i_remove_admin_by_user_id(user_id, group_id){
	return new Promise(function(resolve){
		jQuery.ajax({
			url: "/?rest_route=/gongyi/i_remove_admin_by_user_id&user_id="+user_id+"&group_id="+group_id,
			type: "get",
			dataType: "json",
			//wp进行的api的nonce验证，如果不验证会返回未登陆，同下
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
			},
			success: function (data) {
				if(data.code == '4025'){
					alert(data.msg);
				}else if(data.code == '404'){
					alert(data.msg);
				}else{
					alert(data.msg);
					resolve(data);
				};
			}
		})
	});
};

//同意加入
function i_agree_join_by_user_id(user_id, group_id){
	return new Promise(function(resolve){
		jQuery.ajax({
			url: "/?rest_route=/gongyi/i_agree_join_by_user_id&user_id="+user_id+"&group_id="+group_id,
			type: "get",
			dataType: "json",
			//wp进行的api的nonce验证，如果不验证会返回未登陆，同下
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
			},
			success: function (data) {
				if(data.code == '4025'){
					alert(data.msg);
				}else if(data.code == '404'){
					alert(data.msg);
				}else if(data.code == '409'){
					alert(data.msg);
				}else{
					alert(data.msg);
					resolve(data);
				};
			}
		})
	});
};

//拒绝加入
function i_refuse_join_by_user_id(user_id, group_id){
	return new Promise(function(resolve){
		jQuery.ajax({
			url: "/?rest_route=/gongyi/i_refuse_join_by_user_id&user_id="+user_id+"&group_id="+group_id,
			type: "get",
			dataType: "json",
			//wp进行的api的nonce验证，如果不验证会返回未登陆，同下
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
			},
			success: function (data) {
				if(data.code == '4025'){
					alert(data.msg);
				}else if(data.code == '404'){
					alert(data.msg);
				}else if(data.code == '409'){
					alert(data.msg);
				}else{
					alert(data.msg);
					resolve(data);
				};
			}
		})
	});
};

//删除一条记录
function i_is_delete_item(item_id,group_id){
	return new Promise(function(resolve){
		if (window.confirm("警告！操作不可逆，你确定要删除吗？")){
			jQuery.ajax({
				url: "/?rest_route=/gongyi/catering/i_delete_item_by_id&group_id=" + group_id + "&item_id=" + item_id,
				type: "get",
				dataType: "json",
				//wp进行的api的nonce验证，如果不验证会显示未登陆，实际就是接口中无法得到用户信息的返回值
				beforeSend: function (xhr) {
					xhr.setRequestHeader('X-WP-Nonce',  WP_API_Settings.nonce);
				},
				success: function (data) {
					if(data.code == '404'){
						alert(data.msg);
					}else if(data.code == '4024'){
						alert(data.msg);
					}else{
						alert("删除成功");
						resolve(data);
						// window.location.reload();
					}
				}
			});
		}
	});
};

//通过群组id获取群组的信息
function i_get_all_info_item_by_group_id(group_id = null){
  return new Promise(function(resolve){
    jQuery.ajax({
      url: "/?rest_route=/gongyi/i_get_all_info_item_by_group_id&group_id=" + group_id,
      type: "get",
      dataType: "json",
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
      },
      success: function (data) {
        // 查询用户加入过的群组信息
        resolve(data);
      }
    });
  });
};

//获取群组的所有的信息及子信息（推荐使用）
function i_get_all_info_item_by_group_id_with_child_list(group_id){
	return new Promise(function(resolve){
    jQuery.ajax({
      url: "/?rest_route=/gongyi/i_get_all_info_item_by_group_id_with_child_list&group_id="+group_id,
      type: "get",
      dataType: "json",
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
      },
      success: function (data) {
        // 查询用户加入过的群组信息
				if(data.data == false){
					data.data = [];
				}
        resolve(data);
      }
    })
  });
};

//获取所有的用户信息
function i_get_all_user_info_by_group_id(group_id){
  return new Promise(function(resolve){
    jQuery.ajax({
      url: "/?rest_route=/gongyi/i_get_all_user_info_by_group_id&group_id="+group_id,
      type: "get",
      dataType: "json",
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
      },
      success: function (data) {
        // 查询用户加入过的群组信息
        resolve(data);
      }
    })
  });
};

//删除群组（非永久删除）
function i_delete_group(group_id){
	return new Promise(function(resolve){
		if (window.confirm("警告！操作不可逆，你确定要删除吗？")){
			jQuery.ajax({
					url: "/?rest_route=/gongyi/i_delete_group_by_id",
					type: "post",
					dataType: "json",
					//wp进行的api的nonce验证，如果不验证会返回未登陆，同下
					beforeSend: function (xhr) {
							xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
					},
					data:{
							"group_id" : group_id,
					},
					success: function (data) {
							if(data.code == '401'){
									window.location.href= window.location.origin + '/wp-login.php';
							}else if(data.code == '4025'){
									alert(data.msg);
							}else{
									alert(data.msg);
									resolve(data);
							}
					}
			})
		}
	})
};

//创建群组
function i_group(name, description, class_type,identity) {
	return new Promise(function(resolve){
		jQuery.ajax({
			url: "/?rest_route=/gongyi/i_group&name=" + name + "&description=" + description + "&class_type=" + class_type + "&identity=" + identity,
			type: "get",
			dataType: "json",
			//wp进行的api的nonce验证，如果不验证会显示未登陆，实际就是接口中无法得到用户信息的返回值
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
			},
			success: function (data) {
				if(data.code == '401'){
					window.location.href= window.location.origin + '/wp-login.php';
				}else if(data.code == '4011'){
					window.location.href='/设置身份/';
				}else{
					resolve(data);
				}
			},
			error: function (msg) {
				alert("ajax连接异常：" + msg);
			}
		})
	});
}

//获取某个用户的id信息
function i_set_identity(){
  return new Promise(function(resolve){
    jQuery.ajax({
      url: "/?rest_route=/gongyi/i_get_info_by_user_id",
      type: "get",
      dataType: "json",
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
      },
      success: function (data) {
        console.log(data)
        resolve(data);
      }
    });
  });
};

//获取获取某个id用户的群组账本
function i_get_group_by_user_id(user_id){
  return new Promise(function(resolve){
    jQuery.ajax({
      url: "/?rest_route=/gongyi/i_get_group_by_user_id&user_id="+user_id+"",
      type: "get",
      dataType: "json",
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
      },
      success: function (data) {
        // 查询用户加入过的群组信息
        resolve(data);
      }
    });
  });
};

//获取某个id群组账本的信息（详细）
function i_get_group_info_by_id(group_id){
  return new Promise(function(resolve){
      jQuery.ajax({
        url: "/?rest_route=/gongyi/i_get_group_info_by_id&group_id="+group_id+"",
        // async: false,
        type: "get",
        dataType: "json",
        beforeSend: function (xhr) {
          xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
        },
        success: function (data) {
					data.data.group_id = data.data.id;
          resolve(data);
        }
      });
  });
};

//获取所有的信息
function i_get_all_group_info_by_user_id(page_number,page_size,user_id = null){
  return new Promise(function(resolve){
    jQuery.ajax({
      url: "/?rest_route=/gongyi/i_get_all_info_by_user_id&page_number="+page_number+'&page_size='+page_size+'&user_id='+user_id,
      type: "get",
      dataType: "json",
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
      },
      success: function (data) {
        console.log("/?rest_route=/gongyi/i_get_all_info_by_user_id&page_number="+page_number+'&page_size='+page_size+'&user_id='+user_id)
        resolve(data);
      }
    });
  });
};

//向群组注册一条信息（添加记录）
function i_sign_up(item_name, description, price, group_id, reimbursement, expenses_weight, rd_pwd = null, father_item_id = null, reference_id = null, occurrence_time = null){
	return new Promise(function(resolve){
		jQuery.ajax({
			url: "/?rest_route=/gongyi/catering/i_sign_up&item_name=" +item_name +"&description=" +description +"&price=" +price +"&group_id=" +group_id +"&reimbursement=" +reimbursement +"&expenses_weight=" +expenses_weight +"&rd_pwd=" +rd_pwd +"&father_item_id=" +father_item_id +"&reference_id=" +reference_id + "&occurrence_time=" +occurrence_time,
			type: "get",
			dataType: "json",
			//wp进行的api的nonce验证，如果不验证会显示未登陆，实际就是接口中无法得到用户信息的返回值
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce',  WP_API_Settings.nonce);
			},
			success: function (data) {
				if(data.code == '401'){
					window.location.href=window.location.origin + '/wp-login.php';
				}else if(data.code == '4014'){
					alert(data.msg);
				}else{
					resolve(data);
				}
			},
			error: function (msg) {
				alert("ajax连接异常：" + msg);
			}
		});
	});
};

//复制被引用id的数据，向群组注册一条记录，这条记录的数据和被引用id的信息除主键外完全一致
function i_sign_up_by_reference_id(group_id, reference_id, rd_pwd = null){
	return new Promise(function(resolve){
		jQuery.ajax({
			url: "/?rest_route=/gongyi/i_sign_up_by_reference_id&group_id=" +group_id +"&reference_id=" +reference_id +"&rd_pwd=" +rd_pwd,
			type: "get",
			dataType: "json",
			//wp进行的api的nonce验证，如果不验证会显示未登陆，实际就是接口中无法得到用户信息的返回值
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce',  WP_API_Settings.nonce);
			},
			success: function (data) {
				if(data.code == '401'){
					window.location.href=window.location.origin + '/wp-login.php';
				}else if(data.code == '4014'){
					alert(data.msg);
				}else{
					resolve(data);
				}
			},
			error: function (msg) {
				alert("ajax连接异常：" + msg);
				showData("签到失败，请稍后重试。");
			}
		});
	});
};

//统计次数
function i_count(item_name='日常饮食'){
	return new Promise(function(resolve){
		jQuery.ajax({
			url: "/?rest_route=/gongyi/catering/i_count&item_name=" +item_name,
			type: "get",
			dataType: "json",
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
			},
			success: function (data) {
				console.log(data);
				resolve(data);
			}
		});
	});
};

//为用户设置身份
function i_set_identity_for_user(identity){
	return new Promise(function(resolve){
		jQuery.ajax({
			url: "/?rest_route=/gongyi/i_set_identity_is_labor_or_student&identity=" + identity,
			type: "get",
			dataType: "json",
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
			},
			success: function (data) {
				console.log(data);
				resolve(data);
			}
		});
	});
};

//增加一组item货物之间关系，一般用来设定原材料和成品之间的关系，并通过这些关系，重新计算成品价格
//例如：将原材料账单中的鸡蛋和番茄设定为item_id，而交易记录账单中的番茄炒蛋为costs_id，而交易记录中的番茄炒蛋条目引用了来自菜谱账单中的番茄炒蛋为reference_id
//则程序会为被引用的番茄炒蛋和原材料之间建立一组关系，并即时核算交易记录中番茄炒蛋的价格
//数据示例：&data=[{"item_id":36,"quantity":1,"costs_id":52,"description":""},{"item_id":41,"quantity":2,"costs_id":52,"description":"test"}]
function i_add_item_ids_to_costs_ids(data){
	return new Promise(function(resolve){
		jQuery.ajax({
			url: "/?rest_route=/gongyi/i_add_item_ids_to_costs_ids&data=" + data,
			type: "get",
			dataType: "json",
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
			},
			success: function (data) {
				console.log(data);
				resolve(data);
			}
		});
	});
};

//更新一组item货物的价格，当原材料价格发生变化时，可以调用此接口更新成品价格
//例如：交易记录账单中的番茄炒蛋为costs_id，该成品引用了来自菜谱账单中的番茄炒蛋，并将菜谱中的番茄炒蛋id更新为前者的reference_id
//则程序会查找被引用的对象，根据被引用的对象关系确定该成品的价格
//数据示例：&costs_ids=[52,41]
function i_update_items_by_costs_ids(costs_ids){
	return new Promise(function(resolve){
		jQuery.ajax({
			url: "/?rest_route=/gongyi/i_update_items_by_costs_ids&costs_ids=" + costs_ids,
			type: "get",
			dataType: "json",
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
			},
			success: function (data) {
				console.log(data);
				resolve(data);
			}
		});
	});
};

//通过costs_id删除一组item
function i_delete_items_by_costs_ids(costs_ids){
	return new Promise(function(resolve){
		jQuery.ajax({
			url: "/?rest_route=/gongyi/i_delete_items_by_costs_ids&costs_ids=" + costs_ids,
			type: "get",
			dataType: "json",
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
			},
			success: function (data) {
				console.log(data);
				resolve(data);
			}
		});
	});
};

//通过costs_id查找一组包含该id的数据
function i_look_up_items_by_costs_ids(costs_ids){
	return new Promise(function(resolve){
		jQuery.ajax({
			url: "/?rest_route=/gongyi/i_look_up_items_by_costs_ids&costs_ids=" + costs_ids,
			type: "get",
			dataType: "json",
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
			},
			success: function (data) {
				console.log(data);
				resolve(data);
			}
		});
	});
};