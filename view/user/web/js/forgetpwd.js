(function() {
	$("#refresh_code").click(function() {
		var verifyimg = $("#imgrnd").attr("src");
	    if (verifyimg.indexOf('?') > 0) {
	        $("#imgrnd").attr("src", verifyimg + '&random=' + Math.random());
	    } else {
	        $("#imgrnd").attr("src", verifyimg.replace(/\?.*$/, '') + '?' + Math.random());
	    }
	});

	var i,intervalid;
    $('.code_send').click(function(){
        var loadin_layer=layer.msg('正在发送', {icon: 16,shade: 0.3});
    	var send_data = JSON.parse($(this).attr('send-data'));
    	send_data['type'] = 'passw';
    	$.get($(this).attr('send-url'),send_data,function(data){
            layer.close(loadin_layer);
    		if(data.code==1){
                layer.msg(data.msg, {icon: 1,shade:0.8});
    			$('.code_send').attr({"disabled":"true"});
    			i=180;
	        	intervalid = setInterval("codefun()",1000);
    		}else{
    			layer.msg(data.msg, {icon: 2,shade:0.8});
    		}
    	});
    });

    codefun=function(){
        if (i == 0){
            $(".code_send").text("获取验证码").removeAttr("disabled");
            clearInterval(intervalid);
        }else{
        	$('.code_send').text(i+"秒后重新获取");
        	i--;
        }
    }
})();