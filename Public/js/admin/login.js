var login = {

	check : function() {
		var username = $('input[name="username"]').val();
		var password = $('input[name="password"]').val();
		if(!username || !password){
			return dialog.error('用户名不能为空');
		}
		var url = "/index.php?m=admin&c=login&a=check";
		var data = {'username' : username, 'password' : password};
		$.post(url, data, function(res){
			if(res.status == 0){
				dialog.error(res.message);
			}
			if(res.status == 1){
				dialog.success(res.message, "index.php?m=admin&c=index");
			}
		},'JSON');
	}

}