$(function () {
    //加载弹出层
    layui.use(['form','element'],function() {

        layer = layui.layer;
        element = layui.element;
        form = layui.form;

        form.verify({
            username: function(value){
              if(value.length < 5){
                return '昵称至少得5个字符啊';
              }
            }
            ,pass: [/(.+){6,30}$/, '密码必须6到30位']
            ,repass: function(value){
                if($('[lay-verify="pass"]').val()!=$('[lay-verify="repass"]').val()){
                    return '两次密码不一致';
                }
            }
        });

        element.on('nav(top_nav)', function(elem){
            var index=$("[lay-filter='top_nav'] li a").index($(elem));
            if($('[lay-filter="left_nav"] .layui-tab-item').eq(index).length > 0){
                $('[lay-filter="left_nav"] .layui-tab-item').removeClass('layui-show');
                $('[lay-filter="left_nav"] .layui-tab-item').eq(index).addClass('layui-show');
            }
        });

        //监听提交
        form.on('submit(ajax)', function(data){
            var that = this;
            var callback = $(data.elem).attr('callback');
            var addtata = $(data.elem).attr('lay-data');
            var current = $(data.elem).attr('lay-current');
            if(addtata){
                data.field=$.extend(data.field,JSON.parse(addtata));
            }
            var loadin_layer=layer.msg('提交中，请稍后。', {icon: 16,shade: 0.3});
            $.post($(data.form).attr('action'),data.field, function(data){
                layer.close(loadin_layer);
                if(data.code){
                    if(callback){
                        eval(callback);
                    }else{
                        layer.msg(data.msg, {icon: 1,shade: 0.3,time: 2000},function(layero, index){
                            if(current){
                                location.reload();
                            }else{
                                parent.location.reload();
                            }
    					});
                    }
                } else {
                    layer.msg(data.msg, {icon: 0});
                }
            }, "json");
            return false;
        });

        form.on('submit(del)', function(data){
        	layer.confirm('确认要删除吗？',function(index){
                var loadin_layer=layer.msg('删除中，请稍后。', {icon: 16,shade: 0.3});
	            $.post($(data.form).attr('action'),data.field, function(data){
                    layer.close(loadin_layer);
	                if(data.code){
                        layer.msg(data.msg, {icon: 1,shade: 0.3,time: 2000},function(layero, index){
                            location.reload();
                        });
	                } else {
	                    layer.msg(data.msg, {icon: 0});
	                }
	            }, "json");
       		});
            return false;
        });

        form.on('checkbox(layTableAllChoose)', function(data){
			if(data.elem.checked == true){
				$('input[type="checkbox"]:not([lay-filter="status"])').prop("checked", true);
			}else{
				$('input[type="checkbox"]:not([lay-filter="status"])').prop("checked", false);
			}
			form.render('checkbox');
        });

        form.on('switch(status)', function(data){
            if (target = $(data.elem).attr('lay-url')) {
                $.get(target,function(data){
                    if (data.code) {
                        layer.msg(data.msg);
                    }else{
                        layer.msg(data.msg);
                    }
                });
            }
        });

        form.on('checkbox(position)', function(data){
            if (target = $('.layui-form').attr('action')) {
                var query=$('.layui-form').serialize();
                $.post(target,query);
            }
        });
    });

    //触发事件
  	var tab = {
        tabAdd: function(title,url,id){
          //新增一个Tab项
          element.tabAdd('xbs_tab', {
            title: title,
            content: '<iframe tab-id="'+id+'" frameborder="0" src="'+url+'" class="x-iframe"></iframe>',
            id: id
          })
        }
        ,tabChange: function(id){
          //切换到指定Tab项
          element.tabChange('xbs_tab', id); //切换到：用户管理
        }
    };
      
    $('.left_open').click(function(event) {
        if($('.left-nav').css('left')=='0px'){
            $(this).children('i').removeClass('layui-icon-shrink-right').addClass('layui-icon-spread-left');
            $('.left-nav').animate({left: '-220px'}, 100);
            $('.page-content').animate({left: '0px'}, 100);
        }else{
            $(this).children('i').removeClass('layui-icon-spread-left').addClass('layui-icon-shrink-right');
            $('.left-nav').animate({left: '0px'}, 100);
            $('.page-content').animate({left: '220px'}, 100);
        }
    });

    //左侧菜单效果
    $('[lay-filter="left_nav"] [nav-href]').click(function (event) {
        var url = $(this).attr('nav-href');
        var title = $(this).html();
        var index  = $('.left-nav [lay-filter="left_nav"] [nav-href]').index($(this))+1;
        if($('.x-iframe[tab-id="'+index+'"]').length > 0){
            tab.tabChange(index);
            $('[tab-id="'+index+'"]').attr('src', $('[tab-id="'+index+'"]').attr('src'));
            event.stopPropagation();
            return;
        }
        tab.tabAdd(title,url,index);
        tab.tabChange(index);
        event.stopPropagation();
    });

    $('[lay-filter="xbs_tab"] ul.layui-tab-title').on('click','li',function(event) {
        var index = $(this).attr('lay-id');
        $('[tab-id="'+index+'"]').attr('src', $('[tab-id="'+index+'"]').attr('src'));
    });

    $('[bind="union_type"]').click(function(event) {
        var that = this;
        layer.open({
          type: 1,
          shade: false,
          title: false,
          resize: false,
          offset: [$(that).offset().top+'px',$(that).offset().left+'px'],
          content: $('#bind_type'),
          success: function(layero, index){
            $('.layui-layer-content').css('overflow','visible');
            $('#bind_type').removeClass('layui-hide');
            $('#bind_type').find('input[name="id"]').val($(that).attr('bind-id'));
            if($(that).attr('category-id')){
                form.val("bind_type",{"category":$(that).attr('category-id')});
            }
          },
          end: function(){
            $('#bind_type').addClass('layui-hide');
          }
        });
    });

    $(document).on('click','.ajax-get',function(){
    	if ( (target = $(this).attr('href')) || (target = $(this).attr('url')) ) {
            var callback = $(this).attr('callback');
            var loadin_layer=layer.msg('提交中，请稍后。', {icon: 16,shade: 0.3});
    		$.get(target,function(data){
                layer.close(loadin_layer);
    			if (data.code) {
                    if(callback){
                        eval(callback);
                    }else{
        				layer.msg(data.msg, {icon: 1,shade: 0.3,time: 2000},function(layero, index){
        					location.reload();
    					});
                    }
    			}else{
    				layer.msg(data.msg, {icon: 0},function(layero, index){
                        if (data.url) {
                            location.href=data.url;
                        }
                    });
    			}
    		});
    	}
    	return false;
    });

    $('.x-show').click(function () {
        if($(this).attr('status')=='true'){
            $(this).html('&#xe625;'); 
            $(this).attr('status','false');
            cateId = $(this).parents('tr').attr('cate-id');
            $("tbody tr[fid="+cateId+"]").show();
       }else{
            cateIds = [];
            $(this).html('&#xe623;');
            $(this).attr('status','true');
            cateId = $(this).parents('tr').attr('cate-id');
            getCateId(cateId);
            for (var i in cateIds) {
                $("tbody tr[cate-id="+cateIds[i]+"]").hide().find('.x-show').html('&#xe623;').attr('status','true');
            }
       }
    });

    $('#collect_search').click(function(event) {
        location.href=$(this).attr('url')+'?keyword='+$('input[name="keyword"]').val();
    });

    $(document).on('click', '[open-select]', function() {
        var that = $(this);
        var _url = that.attr('open-url'),
            _title = that.attr('open-title'),
            _width = that.attr('open-width') ? that.attr('open-width')+'' : 750,
            _height = that.attr('open-height') ? that.attr('open-height')+'' : 500;
        var query = $("input:checkbox[name='id[]']:checked").map(function(index,elem) {
            return $(elem).val();
        }).get().join(',');
        if(!query){
            return layer.msg('请选择要操作的数据!', {icon: 0});
        }
        $.get(_url, {id:query}, function(res) {
            var lay = layer.open({type:1, title:_title, content:res, area: [_width+'px', _height+'px'],success: function(layero, index){
                $(layero).find(".layui-layer-content").css("overflow","inherit");
                form.render();
            }});
        });
    });
})

function rndNum(under, over){
    switch(arguments.length){
        case 1: return parseInt(Math.random()*under+1);
        case 2: return parseInt(Math.random()*(over-under+1) + under);
        default: return 0;
    }
}

var cateIds = [];
function getCateId(cateId) {
    
    $("tbody tr[fid="+cateId+"]").each(function(index, el) {
        id = $(el).attr('cate-id');
        cateIds.push(id);
        getCateId(id);
    });
}

/*弹出层*/
/*
    参数解释：
    title   标题
    url     请求的url
    w       弹出层宽度（缺省调默认值）
    h       弹出层高度（缺省调默认值）
    full    全屏
    reload  刷新
*/
function admin_show(title,url,w,h,full,reload){
    if (title == null || title == '') {
        title=false;
    };
    if (url == null || url == '') {
        url="404.html";
    };
    if (w == null || w == '') {
        w=($(window).width()*0.9);
    };
    if (h == null || h == '') {
        h=($(window).height());
    };
    var lay = layer.open({
        type: 2,
        area: [w+'px', h +'px'],
        fix: false, //不固定
        maxmin: true,
        shadeClose: true,
        shade:0.4,
        title: title,
        content: url,
        end: function(){
            if(reload == 1){
                location.reload();
            }
        }    
    });
    if (full == 1) {
        layer.full(lay);
    };
}

/*关闭弹出框口*/
function admin_close(){
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.close(index);
}

 /*删除*/
function admin_del(obj,url,delobj){
  	layer.confirm('确认要删除吗？',function(index){
      //发异步删除数据
        var loadin_layer=layer.msg('删除中，请稍后。', {icon: 16,shade: 0.3});
	    $.get(url,function(data){
            layer.close(loadin_layer);
	      	if(data.code){
                if (delobj == null || delobj == '') {
                    $(obj).parents("tr").remove();
                }else{
                    $(obj).parents(delobj).remove();
                }
	      		layer.msg(data.msg,{icon:1,time:1000});
	      	}else{
	      		layer.msg(data.msg, {icon: 0});
	      	}
	    });
  	});
}

function select_open(obj,value){
    parent.$('[name="'+obj+'"]').val(value);
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.close(index);
}

function removebind(obj,cid){
    var sval=$(obj).parents('#bind_type').find('select[name="category"]').val();
    var id=$(obj).parents('#bind_type').find('input[name="id"]').val()
    if(sval){
        $('[bind-id="'+id+'"]').removeClass('layui-btn-danger').html('<i class="layui-icon layui-icon-ok"></i> 已绑定');
        layer.msg('绑定成功!');
    }else{
        $('[bind-id="'+id+'"]').addClass('layui-btn-danger').html('<i class="layui-icon layui-icon-close"></i> 未绑定');
        layer.msg('解除绑定!');
    }
    var index=$(obj).parents('.layui-layer').attr('times');
    layer.close(index);
}

function collect(url){
    $.get(url,function(data){
        if (data.code) {
            console.log(data)
            layer.msg('是否继续采集', {
                time: 50000,
                btn: ['继续采集', '重新采集'],
                btn1:function(index, layero){
                    layer.close(index);
                    admin_show('采集',data.url,0,0,1);
                },
                btn2:function(index, layero){
                    layer.close(index);
                    admin_show('采集',data.url,0,0,1);
                }
            });
        }else{
            admin_show('采集',data.url,0,0,1);
        }
    });
}