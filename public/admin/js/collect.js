$(document).ready(function() {
    $.fn.extend({
        insertAtCaret: function(myValue) {
            var $t = $(this)[0];
            if (document.selection) {
                this.focus();
                sel = document.selection.createRange();
                sel.text = myValue;
                this.focus()
            } else if ($t.selectionStart || $t.selectionStart == '0') {
                var startPos = $t.selectionStart;
                var endPos = $t.selectionEnd;
                var scrollTop = $t.scrollTop;
                $t.value = $t.value.substring(0, startPos) + myValue + $t.value.substring(endPos, $t.value.length);
                this.focus();
                $t.selectionStart = startPos + myValue.length;
                $t.selectionEnd = startPos + myValue.length;
                $t.scrollTop = scrollTop
            } else {
                this.value += myValue;
                this.focus()
            }
        }
    });
});

//加载弹出层
layui.use(['form','element','laytpl'],function() {

    layer = layui.layer;
    element = layui.element;
    form = layui.form;
    laytpl = layui.laytpl;

    url_tpl('source');
    url_tpl('relation');
    add_field($('select[lay-filter="type"]').val());

    form.on('radio(type)',function(data){
        source_type(data.value);
    });

    form.on('select(type)',function(data){
        add_field(data.value);
    });

    form.on('radio(category_way)',function(data){
        category_show(data.value);
    });

    form.on('submit(source)', function(data){
        if(parent.$('[name="source_url"]').val()){
            var url=JSON.parse(parent.$('[name="source_url"]').val());
        }else{
            var url=[];
        }
        var type=$('[name="source[type]"]:checked').val();
        var index=$('[name="index"]').val();
        switch(type){
        case "1":
            var source_url=$('[name="source[url]"]').val();
            var param_num_start=$('[name="source[param_num_start]"]').val();
            var param_num_end=$('[name="source[param_num_end]"]').val();
            var param_num_inc=$('[name="source[param_num_inc]"]').val();
            var param_num_desc=parseInt($('[name="source[param_num_desc]"]:checked').val());
            if(!param_num_desc){
                param_num_desc=0;
            }
            if(index){
                url[index]={"url":source_url,"type":type,"param":[param_num_start,param_num_end,param_num_inc,param_num_desc]};
            }else{
                url.push({"url":source_url,"type":type,"param":[param_num_start,param_num_end,param_num_inc,param_num_desc]});
            }
          break;
        case "2":
            if(index){
                url[index]={"url":$('[name="source[large_urls]"]').val(),"type":type};
            }else{
                url.push({"url":$('[name="source[large_urls]"]').val(),"type":type});
            }
          break;
        case "3":
            if(index){
                url[index]={"url":$('[name="source[urls]"]').val(),"type":type};
            }else{
                url.push({"url":$('[name="source[urls]"]').val(),"type":type});
            }
          break;
        }
        laytpl(parent.$('#source').html()).render(url, function(html){
            parent.$('#source_view').html(html);
        });
        parent.$('[name="source_url"]').val(JSON.stringify(url));
        var layer_index = parent.layer.getFrameIndex(window.name);
        parent.layer.close(layer_index);
        return false;
    });

    form.on('submit(relation)', function(data){
        if(parent.$('[name="relation_url"]').val()){
            var relation=JSON.parse(parent.$('[name="relation_url"]').val());
        }else{
            var relation=[];
        }
        var index=$('[name="index"]').val();
        var relation_data={"title":$('[name="relation[title]"]').val(),"page":$('[name="relation[page]"]').val(),"chapter":$('[name="relation[chapter]"]:checked').val(),"section":$('[name="relation[section]"]').val(),"url_rule":$('[name="relation[url_rule]"]').val(),"url_merge":$('[name="relation[url_merge]"]').val()}
        if(index){
            relation[index]=relation_data;
            parent.$("select[name^='rule'][name$='[source]'] optgroup option[value='"+index+"']").text($('[name="relation[title]"]').val());
        }else{
            relation.push(relation_data);
            parent.$("select[name^='rule'][name$='[source]'] optgroup").append("<option value='"+(relation.length-1)+"'>"+$('[name="relation[title]"]').val()+"</option>");
        }
        laytpl(parent.$('#relation').html()).render(relation, function(html){
            parent.$('#relation_view').html(html);
        });
        parent.$('[name="relation_url"]').val(JSON.stringify(relation));
        var layer_index = parent.layer.getFrameIndex(window.name);
        parent.layer.close(layer_index);
        parent.form.render('select');
        return false;
    });

    form.on('submit(replace)', function(data){
        var index=$('[name="index"]').val();
        var reindex=$('[name="reindex"]').val();
        var replace_data={"find":$('[name="replace[find]"]').val(),"replaces":$('[name="replace[replaces]"]').val()};
        if(parent.$('[name="rule['+index+'][replace]"]').val()){
            var field_replace=JSON.parse(parent.$('[name="rule['+index+'][replace]"]').val());
        }else{
            var field_replace=[];
        }
        if(reindex){
            field_replace[reindex]=replace_data;
        }else{
            field_replace.push(replace_data);
        }
        laytpl(parent.$('#replace').html()).render({"field_index":index,"list":field_replace}, function(html){
            parent.$('#replace_'+index+'_view').html(html);
        });
        parent.$('[name="rule['+index+'][replace]"]').val(JSON.stringify(field_replace));
        var layer_index = parent.layer.getFrameIndex(window.name);
        parent.layer.close(layer_index);
    });

    form.on('submit(category)', function(data){
        var index=$('[name="index"]').val();
        var category_data={"target":$('[name="category[target]"]').val(),"local":$('[name="category[local]"]').val()};
        var category_equivalents=parent.$('input[name="category_equivalents"]').val();
        if(category_equivalents){
            var field_category=JSON.parse(category_equivalents);
        }else{
            var field_category=[];
        }
        if(index){
            field_category[index]=category_data;
        }else{
            field_category.push(category_data);
        }
        laytpl(parent.$('#category_equivalents').html()).render(field_category, function(html){
            parent.$('#category_equivalents_view').html(html);
        });
        parent.$('[name="category_equivalents"]').val(JSON.stringify(field_category));
        var layer_index = parent.layer.getFrameIndex(window.name);
        parent.layer.close(layer_index);
    });

    $(document).on('click','[lay-operate="up"]',function(){
        var data_name =$(this).attr('lay-operate-data');
        if(data_name=="replace"){
            var data_index=$(this).attr('lay-operate-index');
            var data=$.parseJSON($('[name="rule['+data_index+']['+data_name+']"]').val());
        }else{
            var data=$.parseJSON($('[name="'+data_name+'_url"]').val());
        }
        var index=$(this).parents('tr').index();
        data=upRecord(data,index);
        if(data){
            if(data_name=="replace"){
                laytpl($('#'+data_name).html()).render({"field_index":data_index,"list":data}, function(html){
                    $('#'+data_name+'_'+data_index+'_view').html(html);
                });
                $('[name="rule['+data_index+']['+data_name+']"]').val(JSON.stringify(data));
            }else{
                laytpl($('#'+data_name).html()).render(data, function(html){
                    $('#'+data_name+'_view').html(html);
                });
                $('[name="'+data_name+'_url"]').val(JSON.stringify(data));
            }
        }
    });

    $(document).on('click','[lay-operate="down"]',function(){
        var data_name=$(this).attr('lay-operate-data');
        if(data_name=="replace"){
            var data_index=$(this).attr('lay-operate-index');
            var data=$.parseJSON($('[name="rule['+data_index+']['+data_name+']"]').val());
        }else{
            var data=$.parseJSON($('[name="'+data_name+'_url"]').val());
        }
        var index=$(this).parents('tr').index();
        data=downRecord(data,index);
        if(data){
            if(data_name=="replace"){
                laytpl($('#'+data_name).html()).render({"field_index":data_index,"list":data}, function(html){
                    $('#'+data_name+'_'+data_index+'_view').html(html);
                });
                $('[name="rule['+data_index+']['+data_name+']"]').val(JSON.stringify(data));
            }else{
                laytpl($('#'+data_name).html()).render(data, function(html){
                    $('#'+data_name+'_view').html(html);
                });
                $('[name="'+data_name+'_url"]').val(JSON.stringify(data));
            }
        }
    });

    $(document).on('click','[lay-operate="del"]',function(){
        var that = this;
        layer.confirm('确认要删除吗？',function(layerindex){
            var data_name=$(that).attr('lay-operate-data');
            if(data_name=="replace"){
                var data_index=$(that).attr('lay-operate-index');
                var data=$.parseJSON($('[name="rule['+data_index+']['+data_name+']"]').val());
            }else{
                var data=$.parseJSON($('[name="'+data_name+'_url"]').val());
            }
            var index=$(that).parents('tr').index();
            data.splice(index,1);
            if(data_name=="replace"){
                laytpl($('#'+data_name).html()).render({"field_index":data_index,"list":data}, function(html){
                    $('#'+data_name+'_'+data_index+'_view').html(html);
                });
                $('[name="rule['+data_index+']['+data_name+']"]').val(JSON.stringify(data));
            }else{
                laytpl($('#'+data_name).html()).render(data, function(html){
                    $('#'+data_name+'_view').html(html);
                });
                $('[name="'+data_name+'_url"]').val(JSON.stringify(data));
            }
            layer.close(layerindex);
        });
    });

    $(document).on('click','[lay-operate="category_del"]',function(){
        var that = this;
        layer.confirm('确认要删除吗？',function(layerindex){
            var data_name=$(that).attr('lay-operate-data');
            var data=$.parseJSON($('[name="'+data_name+'"]').val());
            var index=$(that).parents('tr').index();
            data.splice(index,1);
            laytpl($('#'+data_name).html()).render(data, function(html){
                $('#'+data_name+'_view').html(html);
            });
            $('[name="'+data_name+'"]').val(JSON.stringify(data));
            layer.close(layerindex);
        });
    });

    $(document).on('click', '.layui-word-aux a', function() {
        var tag = $(this).find('span').text();
        var tagsObj = $(this).parents('.layui-word-aux').prev('input');
        var tags = tagsObj.val() + ',' + tag;
        tags = tags.replace(/(^,+)|(,+$)/, '');
        tagsObj.val(tags);
    });

    $(document).on('click','[lay-open]',function(){
        var url=$(this).attr('lay-open'),size=($(this).attr('lay-size') || "").split(','),title=$(this).attr('lay-title'),index=$(this).attr('lay-index');
        var full=$(this).attr('lay-full');
        if(index){
            url=url.indexOf("?") != -1 ? url+"&index="+index : url+"?index="+index;
        }
        if (title == null || title == '') {
            title=false;
        };
        if (size[0] == null || size[0] == '') {
            size[0]=($(window).width()*0.9);
        };
        if (size[1] == null || size[1] == '') {
            size[1]=($(window).height());
        };
        var lay = layer.open({
            type: 2,
            area: [size[0]+'px', size[1]+'px'],
            fix: false, //不固定
            maxmin: true,
            shadeClose: true,
            shade:0.4,
            title: title,
            content: url
        });
        if (full == 1) {
            layer.full(lay);
        };
    });

    function url_tpl(type){
        if($('[name="'+type+'_url"]').length > 0 && $('[name="'+type+'_url"]').val()){
            var data=$.parseJSON($('[name="'+type+'_url"]').val());
            laytpl($('#'+type).html()).render(data, function(html){
                $('#'+type+'_view').html(html);
            });
        }
    }

    function add_field(type){
        if($('select[lay-filter="type"]').length > 0){
            var data=[];
            data['field']=field_data['field'][type];
            data['category']=field_data['category'][type];
            if($('[name="relation_url"]').val()){
                data['relation']=$.parseJSON($('[name="relation_url"]').val());
            }
            if(field_data['info']){
                data['info']=field_data['info'];
                data['category_equivalents']=field_data['category_equivalents'];
            }
            laytpl($('#field').html()).render(data, function(html){
                $('#field_view').html(html);
                category_show($('input[lay-filter="category_way"]:checked').val());
            });
            form.render('radio');
            form.render('select');
        }
    }

    function category_show(type){
        if(type==1){
            $('[lay-id="category"]').find('[name="category_fixed"]').parents('.layui-form-item').show();
            $('[lay-id="category"]').find('[name="rule[category][source]"]').parents('.layui-form-item').hide();
            $('[lay-id="category"]').find('[name="rule[category][rule]"]').parents('.layui-form-item').hide();
            $('[lay-id="category"]').find('[name="rule[category][merge]"]').parents('.layui-form-item').hide();
            $('[lay-id="category"]').find('[name="rule[category][strip]"]').parents('.layui-form-item').hide();
            $('[lay-id="category"]').find('[name="category_equivalents"]').parents('.layui-form-item').hide();
        }else{
            $('[lay-id="category"]').find('[name="category_fixed"]').parents('.layui-form-item').hide();
            $('[lay-id="category"]').find('[name="rule[category][source]"]').parents('.layui-form-item').show();
            $('[lay-id="category"]').find('[name="rule[category][rule]"]').parents('.layui-form-item').show();
            $('[lay-id="category"]').find('[name="rule[category][merge]"]').parents('.layui-form-item').show();
            $('[lay-id="category"]').find('[name="rule[category][strip]"]').parents('.layui-form-item').show();
            $('[lay-id="category"]').find('[name="category_equivalents"]').parents('.layui-form-item').show();
        }
    }
});

function source_type(type){
    if(type == null || type == ''){
        type=$('[name="source[type]"]').val();
    }
    $("[source-type]").addClass('layui-hide').removeClass('layui-show');
    $("[source-type='"+type+"']").addClass('layui-show').removeClass('layui-hide');
}

function insertText(obj,sign,only) {
    var toObj=$('[name="'+obj+'"]');
    if (only) {
        sign = sign.replace('{num}', '');
        if ($(toObj).val().indexOf(sign) < 0) {
            $(toObj).insertAtCaret(sign);
        }
    } else {
        var reSign = new RegExp(sign.replace('{num}', '(\\d*)').replace('[', '\\[').replace(']', '\\]'), 'g');
        var list = null;
        var max = 0;
        while ((list = reSign.exec($(toObj).val())) != null) {
            var num = parseInt(list[1]);
            if (num > max) {
                max = num;
            }
        }
        sign = sign.replace('{num}', max + 1);
        $(toObj).insertAtCaret(sign);
    }
}

function insertReg(obj,sign,only) {
    var toObj=$('[name="'+obj+'"]');
    if (only) {
        if (toObj.val().indexOf(sign) < 0) {
            toObj.insertAtCaret(sign)
        }
    } else {
        toObj.insertAtCaret(sign)
    }
}

// 交换数组元素
function swapItems(arr, index1, index2) {
    arr[index1] = arr.splice(index2, 1, arr[index1])[0];
    return arr;
}
  
// 上移
function upRecord(arr, $index) {
    if($index == 0) {
        return;
    }
    return swapItems(arr, $index, $index - 1);
}
  
    // 下移
function downRecord(arr, $index) {
    if($index == arr.length -1) {
      return;
    }
    return swapItems(arr, $index, $index + 1);
}

function source_edit(){
    var data=JSON.parse(parent.$('[name="source_url"]').val());
    var index=$('[name="index"]').val();
    layui.use('form', function(){
        var form = layui.form;
        switch(data[index]['type']){
        case "1":
            form.val("source", {
              "source[type]": data[index]['type'],
              "source[url]": data[index]['url'],
              "source[param_num_start]": data[index]['param'][0],
              "source[param_num_end]": data[index]['param'][1],
              "source[param_num_inc]": data[index]['param'][2],
              "source[param_num_desc]": data[index]['param'][3]
            });
            break;
        case "2":
            form.val("source", {
              "source[type]": data[index]['type'],
              "source[large_urls]": data[index]['url'],
            });
            break;
        case "3":
            form.val("source", {
              "source[type]": data[index]['type'],
              "source[urls]": data[index]['url'],
            });
            break;
        }
    });
    source_type(data[index]['type']);
}

function field_replace_edit(){
    var index=$('[name="index"]').val();
    var reindex=$('[name="reindex"]').val();
    var data=JSON.parse(parent.$('[name="rule['+index+'][replace]"]').val());
    layui.use('form', function(){
        var form = layui.form;
        form.val("replace_form", {
          "replace[find]": data[reindex]['find'],
          "replace[replaces]": data[reindex]['replaces']
        });
    });
}

function field_category_edit(){
    var index=$('[name="index"]').val();
    var category=parent.field_data['category'][parent.$('select[lay-filter="type"]').val()];
    layui.use(['laytpl','form'], function(){
        var form = layui.form;
        var laytpl = layui.laytpl;
        laytpl($('#category_html').html()).render(category, function(html){
            $('#category_view').html(html);
        });
        form.render('select');
        if(index){
            var category_equivalents=parent.$('[name="category_equivalents"]').val();
            var data=JSON.parse(category_equivalents);
            form.val("category_form", {
              "category[target]": data[index]['target'],
              "category[local]": data[index]['local']
            }); 
        }
    });
}