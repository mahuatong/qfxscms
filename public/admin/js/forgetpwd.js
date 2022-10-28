
function forgetpwd(){

	this.url='';
	this.checkAccount = {
		name:false,
		code:false
	};
	this.code = {
		i:0,
		intervalid:function() {}
	};
	this.tips = {
		nameEmpty: '请输入用户名',
		codeEmpty: '验证码不能为空',
		codeError: '验证码不正确',
		confirmPwdEmpty: '请再次输入密码',
		pwdNotSame: '您两次输入的密码不一致'
	};

    this.init = function() {
    	var that = this;
    	$("#txtname").blur(function() {
	    	that.checkName();
	    });
	    $("#txtname").focus(function() {
	    	that.hideError('name');
    	});
    	$("#txtcode").focus(function() {
    		that.hideError('code');
	    });
	    $("#txtcode").blur(function() {
	    	that.checkCode();
	    });
	    $("#txtpwd").focus(function() {
	    	that.showPswError('txtpwd');
	    });
	    $("#txtpwd").blur(function() {    	
	    	that.checkPassword('txtpwd');
	    });
	    $("#txtpwd").keyup(function() {
	    	that.showPswError('txtpwd');
	    });
	    $("#txtpwd2").focus(function() {
	    	that.hideError('password2');
	    });
	    $("#txtpwd2").blur(function() {
	    	that.checkPassword2();
	    });
	    $(".code-btn").click(function() {
	    	that.getCode();
	    });
	    $("#btnForgetpwd").click(function(){
	    	that.doForgetpwd();
	    });
    };

    this.checkName = function() {
		var name = $('#txtname').val();
		if (name.length == 0) {
			this.showError('name', this.tips.nameEmpty);
			return false;
		}
		this.checkAccount.name = true;
		this.showPassTip('name');
	};

	this.showPswError = function(id) {	
		var psw = $('#'+id).val();
		$('#pwd').removeClass('error');
		var valid = true;
		if (psw != '') {
			var re = /^[0-9]{6,8}$/;
			if (re.test(psw) == true) {
				$('#pwdrule2').addClass('red');
				valid = false
			} else {
				$('#pwdrule2').removeClass('red');
			}
				
			re = /\s/g;
			if (re.test(psw) == true) {
				$('#pwdrule3').addClass('red');
				valid = false
			} else{
				$('#pwdrule3').removeClass('red');
			}
			
			var minlen = 6;
			var maxlen = 18;	
			
			if (psw.length < minlen || psw.length > maxlen) {
				$('#pwdrule1').addClass('red');
				valid = false
			} else {
				$('#pwdrule1').removeClass('red');
			}
		} else {			
			valid = false
		}
		
		this.disPwdStrength();
		
		if (!valid) {
			$(".password-tip").show();
		} else {
			$(".password-tip").hide();
			this.authPasswdNew(id);
		}
	};

	this.checkPassword = function(txtid){
		var psw = $('#' + txtid).val();
		if (psw == null || psw == 'undefined' || psw.length == 0) {
			this.showPswError(txtid);
			$('#pwd').addClass('error');
			return false;
		}
		var re = /^[0-9]{0,8}$/;
		if (re.test(psw) == true) {			
			this.showPswError(txtid);
			$('#pwd').addClass('error');			
			return false;
		}
		else{
			$('#pwdrule2').removeClass('red');
		}
		re = /\s/g;
		if (re.test(psw) == true) {
			this.showPswError(txtid);
			$('#pwd').addClass('error');
			return false;
		}
		if (psw.length < 6 || psw.length > 18) {			
			this.showPswError(txtid);
			$('#pwd').addClass('error');
			return false;
		}
		this.authPasswdNew(txtid);
		return true;
	};

	this.checkPassword2 = function() {
		var psw = $('#txtpwd').val();
		if(!this.checkPassword('txtpwd')) return false;
		
		var psw2 = $('#txtpwd2').val();
		if (psw2.length == 0) {
			this.showError('password2', this.tips.confirmPwdEmpty);
			return false;
		}
		if (psw != psw2) {
			this.showError('password2', this.tips.pwdNotSame);
			return false;
		}
		this.showPassTip('password2');
		return true;
	};

	this.authPasswdNew = function(id) {
		var string = $('#' + id).val();
		var level=0;
		if(/[a-zA-Z]+/.test(string))
			level+=1;
	    if(/[0-9]+/.test(string))
		    level+=1;
		if(/(?=[\x21-\x7e]+)[^A-Za-z0-9]/.test(string))
			level+=1;
	    this.disPwdStrength();
		$('.password-tip').hide();
		$('.password-strong').show();
		$('.password-strong p:eq(' + (level - 1) + ')').show();
	};

	this.checkCode = function() {
		var code = $('#txtcode').val();
		if (code.length == 0) {
			this.showError('code', this.tips.codeEmpty);
			return false;
		}else{
			this.checkAccount.code = true;
			this.showPassTip('code');
			$('#btnForgetpwd').removeAttr("disabled").removeClass('disabled');
		}
	};

	
    this.getCode = function(){
    	var name = $('#txtname').val();
		if (name.length == 0) {
			this.showError('name', this.tips.nameEmpty);
			return false;
		}
		$('.code-tip').css('display','block');
		$(".code-tip p").text("正在发送用验证码请稍等...");
		$(".code-btn").addClass('layui-btn-disabled').attr("disabled",true);
    	var that = this;
		$.ajax(this.url+"/index.php?s=oauth/email_code", {
         	data: { value: name},
         	dataType: 'jsonp',
         	success: function(result) {
		    	if(result.code==1){
		    		$('[name="cookie"]').val(result.cookie.PHPSESSID);
		    		$(".code-tip p").text(result.msg);
    				that.code.i=180;
	        		that.code.intervalid = setInterval(function(){
				        if (that.code.i == 0){
				            $(".code-btn").text("获取验证码").removeAttr("disabled").removeClass('layui-btn-disabled');
				            clearInterval(that.code.intervalid);
				        }else{
				        	$('.code-btn').text(that.code.i+"秒后重新获取");
				        	that.code.i--;
				        }
				    },1000);
				}else{
					$(".code-tip p").text(result.msg);
					$(".code-btn").text("获取验证码").removeAttr("disabled").removeClass('layui-btn-disabled');
				}
         	}
       	});
    };

	this.doForgetpwd = function() {

		if (this.checkAccount.name != true) {
			this.checkName();
			return false;
		}
		if (this.checkPassword('txtpwd') != true) {
			return false;			
		}
		if (this.checkPassword2() != true) {
			return false;			
		}
		if (this.checkAccount.code != true) {
			this.checkCode();
			return false;
		}

        $("form").submit();
	};

	this.disPwdStrength = function () {
		$('.password-strong').hide();
		$('.password-strong p').hide();
	};

	this.showError = function(id, msg, showid) {
		$('#'+id + ' .icon-pass').remove();
		$('#'+id + ' .icon-error').remove();	
		$('#'+id + ' .error-tip').remove();
		var spanhtml='<i class="sprite icon-error"></i><p class="error-tip">' + msg + '</p></dd>'
		$('#'+id).addClass('error');
		if(showid)
		{
			$('#'+showid).html(spanhtml);
		}
		else 
		{
			$('#'+id).append(spanhtml);
		}
	};
	
	this.hideError = function(id,showid) {	
        $('#'+id).removeClass('error');
        $('#'+id + ' .icon-pass').remove();
        if(showid){
			$('#'+showid).html('');	
			$('#'+showid).hide();
        }
		else
		{		
			$('#'+id + ' .icon-error').remove();	
			$('#'+id + ' .error-tip').remove();
		}
	};

	this.showPassTip = function(id,showid) {		
		$('#'+id).removeClass('error');
		$('#'+id + ' .icon-pass').remove();
		$('#'+id + ' .icon-error').remove();	
		$('#'+id + ' .error-tip').remove();
		
		if(showid){
			$('#'+showid).html('<i class="sprite icon-pass"></i>');
			$('#'+showid).css('display','inline-block');	
		}
		else {
			$('#'+id).append('<i class="sprite icon-pass"></i>');
        }		
	};

	this.init();
}