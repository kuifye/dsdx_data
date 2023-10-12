function history_go_back(){
  history.back();
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
      is_reimbursement: item.is_reimbursement,
      is_expenses_weight: item.is_expenses_weight,
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

function i_get_all_info_item_by_group_id(page_number,page_size,group_id = null){
  return new Promise(function(resolve){
    jQuery.ajax({
      url: "/?rest_route=/gongyi/i_get_all_info_item_by_group_id&page_number="+page_number+'&page_size='+page_size+'&group_id='+group_id,
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

function i_get_all_info_item_by_group_id_without_page(group_id = null){
  return new Promise(function(resolve){
    jQuery.ajax({
      url: "/?rest_route=/gongyi/i_get_all_info_item_by_group_id_without_page&group_id="+group_id,
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

//获取所有的信息及子信息
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
        resolve(data);
      }
    })
  });
};

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
				// test
        resolve(data);
      }
    })
  });
};

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

//--------------------------------------------------------------------------
// 获取某个用户的id信息
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

function i_sign_up(item_name, description, price, group_id, is_reimbursement, is_expenses_weight){
	return new Promise(function(resolve){
		jQuery.ajax({
			url: "/?rest_route=/gongyi/catering/i_sign_up&item_name=" + item_name + "&description=" + description + "&price=" + price+"&group_id=" + group_id +"&is_reimbursement=" + is_reimbursement +"&is_expenses_weight=" + is_expenses_weight,
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
}

function i_count(){
	return new Promise(function(resolve){
		jQuery.ajax({
			url: "/?rest_route=/gongyi/catering/i_count&item_name=日常饮食",
			type: "get",
			dataType: "json",
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', WP_API_Settings.nonce);
			},
			success: function (data) {
				console.log(data);
				resolve(data);
			}
		})
	});
}