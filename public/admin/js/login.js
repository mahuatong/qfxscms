
function login(){

	this.url='';
	this.check_code=false;

    this.init = function() {
    	var that = this;
	    $('#username').on('keyup', function(e) {
            $('#username').parent().removeClass('error');
            e.preventDefault();
        });
        $('#password').on('keyup', function(e) {
            $('#password').parent().removeClass('error');
            e.preventDefault();
        });
        $('#code').on('keyup', function(e) {
            $('#code').parent().removeClass('error');
            e.preventDefault();
        });
	    $("#imgCode").click(function() {
	    	that.newCode();
	    });
	    $(".btnLogin").click(function(e){
	    	that.doLoing();
	    	e.preventDefault();
	    	return false;
	    });
    };

    this.newCode = function(){
    	var verifyimg = $("#imgCode").attr("src");
        if (verifyimg.indexOf('?') > 0) {
            $("#imgCode").attr("src", verifyimg + '&random=' + Math.random());
        } else {
            $("#imgCode").attr("src", verifyimg.replace(/\?.*$/, '') + '?' + Math.random());
        }
        $("#code").val('');
        this.check_code=false;
    }

    this.checkName = function() {
		var name = $('#username').val();
		if (name.length == 0) {
			$('#username').parent().addClass('error');
			return false;
		}
		return true;
	};

	this.checkPassword = function(){
		var psw = $('#password').val();
		if (psw == null || psw == 'undefined' || psw.length == 0) {
			$('#password').parent().addClass('error');
			return false;
		}
		return true;
	};

	this.checkCode = function() {
		var code = $('#code').val();
		if (code.length == 0) {
			$('#code').parent().addClass('error');
			return false;
		}
		var that = this;
		$.ajax(this.url+"/index.php?s=user/user/check_code", {
         	data: { value: code},
         	dataType: 'jsonp',
         	success: function(result) {
		    	if(result.code==0){
					$('#code').parent().addClass('error');
					return false;
				}
				that.check_code=true;
				$('[name="cookie"]').val(result.cookie.PHPSESSID);
				that.doLoing();
         	}
       	});
	};

	this.doLoing = function() {
		var that = this;
		if (that.checkName() != true) {
			return false;
		}
		if (that.checkPassword() != true) {
			return false;
		}
		if ( that.check_code != true) {
			that.checkCode();
			return false;
		}
		layui.use('layer', function(){
			layer=layui.layer;
			var loadin_layer=layer.msg('加载中', {icon: 16,shade: 0.3});
	        var query = $("form").find('input,select,textarea').serialize();
	        var target = $("form").attr('action');
			$.post(target, query).done(function(data) {
				layer.close(loadin_layer);
			    if (data.code == 1) {
			    	layer.msg(data.msg, {icon: 1,time: 2000,shade:0.3},function(){
			            location.href = data.url;
			        });
			    } else {
			    	that.newCode();
			    	layer.msg(data.msg,{icon:0})
			    }
			});
			return false;
		});
	};

	this.init();
}