var thread = {
    url:"",
    sleep:0,
    init: function(url,num,sleep){
        thread.url=url;
        thread.sleep=sleep;
        for(var i = 0;i < num; i++) {
            thread.run();
        }
    }
    ,run: function(){
        $.ajax({url:thread.url,type:'get',dataType:'json'}).then(thread.successFun,thread.failFun);
    }
    ,successFun: function(data, textStatus, jqXHR){
        layui.use(['element'], function(){
            layui.element.progress('component-progress-demo', parseInt(data.data.current/data.data.count*100)+'%');
        });
        $('[lay-filter="component-progress-demo"] h3').text('已采集:'+data.data.current+'页/共计:'+data.data.count+'页');
        if(data.code==1){
            if(data.data.state==='finish'){
                $('[lay-filter="component-progress-demo"] h3').text('采集已完成');
                layui.use(['element','layer'], function(){
                    layui.layer.msg(data.msg, {icon: 6,id: 'LAY_layuifinish',btn: ['确定']});
                    layui.element.progress('component-progress-demo', '100%');
                });
                return false;
            }
            if(data.data.status=='error'){
                var html='<tr><td>'+data.data.url+'</td><td>'+data.data.title+'</td><td><i class="layui-icon layui-icon-close layui-color-red"></i><span class="pl-5">'+data.msg+'</span></td></tr>';
            }else{
                var html='<tr><td>'+data.data.url+'</td><td>'+data.data.title+'</td><td><i class="layui-icon layui-icon-ok layui-color-green"></i><span class="pl-5">'+data.msg+'</span></td></tr>';
            }
            $("[progressName] tbody").prepend(html);
            if(thread.sleep>0){
                setTimeout(function(){
                    thread.run();
                },1000*thread.sleep);
            }else{
                thread.run();
            }
        }else{
            thread.errorFun(data);
        }
        var msg_num=$("[progressName] tbody").find("tr").length;
        if(msg_num>40){
            $("[progressName] tbody").empty();
        }
    }
    ,failFun: function(jqXHR, textStatus, errorThrown){
        thread.run();
    }
    ,errorFun:function(data){
        layui.use('layer', function(){
            layui.layer.msg(data.msg);
        });
    }
};