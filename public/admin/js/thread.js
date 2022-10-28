var thread = {
    url:"",
    number:0,
    total:0,
    init: function(url,num,total,current){
        thread.total=total;
        thread.url=url;
        thread.number=current;
        for(var i = 0;i < num; i++) {
            thread.run();
        }
    }
    ,run: function(){
        $.ajax({url:thread.url,type:'get',dataType:'json'}).then(thread.successFun,thread.failFun);
    }
    ,successFun: function(data, textStatus, jqXHR){
        ++thread.number;
        layui.use(['element'], function(){
            layui.element.progress('component-progress-demo', parseInt(thread.number/thread.total*100)+'%');
        });
        $('[lay-filter="component-progress-demo"] h3').text('已采集:'+thread.number+'/共计:'+thread.total);
        if(data.code==1){
            if(data.data.state==='finish'){
                layui.use('layer', function(){
                    layui.layer.msg(data.msg, {icon: 6,id: 'LAY_layuifinish',btn: ['确定']});
                });
                return false;
            }
            var html='<tr><td>'+data.data.title+'</td><td><i class="layui-icon layui-icon-ok layui-color-green"></i><span class="pl-5">'+data.data.msg+'</span></td><td>'+data.data.count+'</td></tr>';
                $("[progressName] tbody").prepend(html);
                thread.run();
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
        if(data.data.state==='pay'){
            layui.use('layer', function(){
                layui.layer.open({
                    type: 1
                    ,title: false //不显示标题栏
                    ,closeBtn: false
                    ,area: '300px;'
                    ,shade: 0.8
                    ,id: 'LAY_layuipay' //设定一个id，防止重复弹出
                    ,btn: ['马上购买', '我在看看']
                    ,btnAlign: 'c'
                    ,moveType: 1 //拖拽模式，0或者1
                    ,content: '<div style="padding: 40px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 500;">'+data.msg+'</div>'
                    ,success: function(layero){
                      var btn = layero.find('.layui-layer-btn');
                      btn.find('.layui-layer-btn0').attr({href: data.url,target: '_parent'});
                    },btn2: function(index, layero){
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                    }
                });
            });
        }else{
            layui.use('layer', function(){
                layui.layer.msg(data.msg);
            });
        }
    }
};