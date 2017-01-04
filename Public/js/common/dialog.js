var dialog = {
	error : function(message){
		layer.open({
			content : message,
			icon : 2,
			title : '错误提示'
		});
	},

	success : function(message, url){
		layer.open({
			content : message,
			icon : 1,
			yes : function(){
				location.href = url;
			}
		});
	}
}