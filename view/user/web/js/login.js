
function login(){

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
    	if($('#dvCode').length>0){
	    	var verifyimg = $("#imgCode").attr("src");
	        if (verifyimg.indexOf('?') > 0) {
	            $("#imgCode").attr("src", verifyimg + '&random=' + Math.random());
	        } else {
	            $("#imgCode").attr("src", verifyimg.replace(/\?.*$/, '') + '?' + Math.random());
	        }
	        $("#code").val('');
	    }
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
		if($('#dvCode').length>0){
			var code = $('#code').val();
			if (code.length == 0) {
				$('#code').parent().addClass('error');
				return false;
			}
		}
		return true;
	};

	this.doLoing = function() {
		var that = this;
		if (that.checkName() != true) {
			return false;
		}
		if (that.checkPassword() != true) {
			return false;
		}
		if (that.checkCode() != true) {
			return false;
		}

		var loadin_layer=layer.msg('加载中', {icon: 16,shade: 0.3});
        var query = $('.login-box').find('input,select,textarea').serialize();
        var target = $('.btnLogin').attr('href');
		$.post(target, query).success(function(data) {
			layer.close(loadin_layer);
		    if (data.code == 1) {
		    	layer.msg(data.msg, {icon: 1,time: 2000,shade:0.3},function(){
		            location.href = data.url;
		        });
		    } else {
		    	that.newCode();
		        that.showError(data.msg);
		    }
		});
		return false;
	};

	this.showError = function(msg) {
        $('.error-tip').html(msg).removeClass('hidden');
	};

	this.init();
}