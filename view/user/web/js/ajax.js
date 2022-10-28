(function() {

//ajax get请求
    $('[ajax-get]').click(function(){
        var target;
        var that = this;
        if ( (target = $(this).attr('href')) || (target = $(this).attr('url')) ) {
            $.get(target).success(function(data){
				layer.msg(data.msg, {icon: 1,time: 2000,shade:0.3},function(){
                    var callback = $(that).attr('callback');
                    if(callback){
                        eval(callback);
                    }else{
                        if(data.url){
                            location.href = data.url;
                        }else{
                            location.reload();
                        }
                    }
                });
            });
        }
        return false;
    });

    $(document).on('click','[ajax-post]',function(){
        var target, query, form;
        var target_form = $(this).attr('ajax-post');
        var that = this;
        if (($(this).attr('type') == 'submit') || (target = $(this).attr('href')) || (target = $(this).attr('url'))) {
            form = $('.' + target_form);
            if (form.get(0).nodeName == 'FORM') {
                target = form.get(0).action;
                query = form.serialize();
            } else {
                query = form.find('input,select,textarea').serialize();
            }
            $(that).attr('autocomplete', 'off').prop('disabled', true);
            $.post(target, query).success(function(data) {
                if (data.code == 1) {
                    $(that).prop('disabled', false);
                    layer.msg(data.msg, {icon: 1,time: 2000,shade:0.3},function(){
                        var callback = $(that).attr('callback');
                        if(callback){
                            eval(callback);
                        }else{
                           location.href = data.url;
                        }
                    });
                } else {
                    layer.msg(data.msg, {icon: 2,time: 2000,shade:0.3},function(){
                        if(data.url){
                            location.href = data.url;
                        }else{
                           $(that).prop('disabled', false); 
                        }
                    });
                }
            });
        }
        return false;
    });
})();