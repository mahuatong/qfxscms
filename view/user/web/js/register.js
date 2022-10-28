
function register(){

	this.checkAccount = {
		name:false,
		email:false,
		code:false
	};
	this.loginUrl = '/index.php?s=user/user/login';
	this.tips = {
		nameEmpty: '请输入用户名',
		nameExist: '该用户名已注册，请尝试直接 <a class="blue" href="' + this.loginUrl + '">登录</a>',
		emailEmpty: '请输入邮箱',
		emailInvalid: '请输入正确的邮箱',
		emailNotSupport: '邮箱后辍不支持',
		emailExist: '该邮箱账号已注册，请尝试直接 <a class="blue" href="' + this.loginUrl + '">登录</a>',
		codeEmpty: '验证码不能为空',
		codeError: '验证码不正确',
		confirmPwdEmpty: '请再次输入密码',
		pwdNotSame: '您两次输入的密码不一致'
	};

	//屏蔽国外的邮箱
	this.forbidEmails = ['chacuo.net', '027168.com', 'sharklasers.com', 'grr.la', 'guerrillamail.biz',
  	'guerrillamail.com', 'guerrillamail.de', 'guerrillamail.net', 'guerrillamail.org',
  	'guerrillamailblock.com','spam4.me', 'yopmail.fr', 'yopmail.net', 'yopmail.com',
  	'cool.fr.nf','jetable.fr.nf','nospam.ze.tc','nomail.xl.cx','mega.zik.dj','speed.1s.fr',
    'courriel.fr.nf','moncourrier.fr.nf','monemail.fr.nf','monmail.fr.nf','meltmail.com',
    'mailinator.com','anonymbox.com'];

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
	    $("#txtemail").focus(function() {
	    	that.hideError('email');
	    });
	    $("#txtemail").blur(function() {
	    	that.checkEmail();
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
	    $("#refresh_code").click(function() {
	    	var verifyimg = $("#imgrnd").attr("src");
	        if (verifyimg.indexOf('?') > 0) {
	            $("#imgrnd").attr("src", verifyimg + '&random=' + Math.random());
	        } else {
	            $("#imgrnd").attr("src", verifyimg.replace(/\?.*$/, '') + '?' + Math.random());
	        }
	    });
	    $('.deal').click(function(){
	    	if ($('#deal').is(':checked') != true) {
				$(".go-reg").addClass('disabled');
			} else {
	    		$(".go-reg").removeClass('disabled');
	    	}
	    });
	    $("#btnRegister").click(function(){
	    	that.doRegister();
	    });
    };

    this.checkName = function() {
		var name = $('#txtname').val();
		if (name.length == 0) {
			this.showError('name', this.tips.nameEmpty);
			return false;
		}
		var that = this;
		$.getJSON("/index.php?s=user/user/check_require", { field: 'username', value: name }, function(result){
			if(result.code==0){
				that.checkAccount.name = false;
				that.showError('name', that.tips.nameExist);
				return false;
			}
			that.checkAccount.name = true;
			that.showPassTip('name');
		});
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

    this.checkEmailForbid = function() {
	    var checkresulet=false;
		var name = $("#txtemail").val();
		var idx=name.indexOf('@');
		if(idx!=-1){
	        var idxname = $.trim(name.substr(idx+1));//获取邮箱后缀
            for(var i=0;i < this.forbidEmails.length;i++)
			{
			   if(idxname == this.forbidEmails[i])
			   {
					checkresulet=true;
					break;
			   }
			}
	    }
		return checkresulet;
	};		
	
	this.checkEmail = function() {
		var showid='email';
        var name = $("#txtemail").val();
        name = $.trim(name);
        if (name == null || name.length <= 0) {
			this.showError(showid, this.tips.emailEmpty);
            return false;
        }
		var re = new RegExp(/^(\w)+([-.]\w+)*@[\w-]+(\.[\w-]+)+$/);
		if (!re.test(name)) {
			this.showError(showid, this.tips.emailInvalid);
            return false;
        }
		
		re = new RegExp(/^(\w)+([-.]\w+)*@[((\w)+\.)]+(com|net|cn|org)$/);
        if (!re.test(name.toLowerCase())) {
			this.showError(showid, this.tips.emailNotSupport);
            return false;
        }
        
		if(this.checkEmailForbid())
		{
			this.showError(showid, this.tips.emailNotSupport);
            return false;
		}
		this.showPassTip(showid);
		var that = this;
		$.getJSON("/index.php?s=user/user/check_require", { field: 'email', value: name }, function(result){
			if(result.code==0){
				that.checkAccount.email = false;
				that.showError(showid, that.tips.emailExist);
				return false;
			}
			that.checkAccount.email = true;
			that.showPassTip(showid);
		});
	};

	this.checkCode = function() {
		var code = $('#txtcode').val();
		if (code.length == 0) {
			this.showError('code', this.tips.codeEmpty);
			return false;
		}
		var that = this;
		$.getJSON("/index.php?s=user/user/check_code", { value: code }, function(result){
			if(result.code==0){
				that.checkAccount.code = false;
				that.showError('code', that.tips.codeError);
				return false;
			}
			that.checkAccount.code = true;
			that.showPassTip('code');
		});
	};

	this.doRegister = function() {

		if (this.checkAccount.name != true) {
			this.checkName();
			return false;
		}
		if (this.checkAccount.email != true) {
			this.checkEmail();
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
		if ($('#deal').is(':checked') != true) {
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