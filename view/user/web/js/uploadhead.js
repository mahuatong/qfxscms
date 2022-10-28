(function( $ ){
    // 当domReady的时候开始初始化
    $(function() {
        var $list=$('#fileList'),$img,thumbnailWidth=640,thumbnailHeight=320,crop,jcrop_api;
        var uploader = WebUploader.create({
            auto: true,
            swf: 'webuploader/Uploader.swf',
            server: '/user/user/upload_head',
            pick: {
                id:'#filePicker',
                innerHTML:'选择您要上传的头像'
            },
            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif,image/bmp'
            },
            compress: {
                width: 1600,
                height: 1600,
                quality: 90,
                allowMagnify: false,
                crop: false,
                preserveHeaders: true,
                noCompressIfLarger: false,
                compressSize: 0
            },
            disableGlobalDnd: true,
            fileSizeLimit: 50 * 1024 * 1024,    // 200 M
            fileSingleSizeLimit: 5 * 1024 * 1024    // 50 M
        });

        uploader.on('ready', function() {
            window.uploader = uploader;
        });

        // 当有文件添加进来的时候
        uploader.on( 'fileQueued', function( file ) {
            var $li = $(
                    '<div id="' + file.id + '">' +
                        '<img>' +
                    '</div>'
                    ),
            $img = $li.find('img');
            $list.html( $li );
            uploader.makeThumb( file, function( error, src ) {
                if ( error ) {
                    $img.replaceWith('<span>不能预览</span>');
                    return;
                }
            }, thumbnailWidth, thumbnailHeight );
            $img.attr('id',"cropbox");
        });
        // 文件上传成功，给item添加成功class, 用样式标记上传成功。
        uploader.on( 'uploadSuccess', function( file, response ) {
            if(response.code==1){
                $('.imgBtn').removeClass('hide');
                $('#cropbox').attr( 'src', response['path']+'?random='+Math.random());
                $('#cropbox').Jcrop({
                    aspectRatio: 0,
                    boxWidth : thumbnailWidth,     //画布宽度
                    boxHeight : thumbnailHeight,    //画布高度
                    onSelect: updateCoordinate,
                    minSize: [160,160],
                    setSelect: [0,0,160,160]
                },function(){
                    jcrop_api=this;
                    crop=this.tellScaled();
                });
            }else{
                var $li = $( '#'+file.id ),
                $error = $li.find('div.error');
                // 避免重复创建
                if ( !$error.length ) {
                    $error = $('<div class="error"></div>').appendTo( $li );
                }
                $error.text(response.msg);
            }
        });
        // 文件上传失败，显示上传出错。
        uploader.on( 'uploadError', function( file ) {
            var $li = $( '#'+file.id ),
                $error = $li.find('div.error');
            // 避免重复创建
            if ( !$error.length ) {
                $error = $('<div class="error"></div>').appendTo( $li );
            }
            $error.text('上传失败');
        });
        // 完成上传完了，成功或者失败，先删除进度条。
        uploader.on( 'uploadComplete', function( file ) {
            $( '#'+file.id ).find('.progress').remove();
            uploader.reset();
        });

        function updateCoordinate(c) {
            crop = c;
        }
        $(document).on('click','.imgBtn',function(){
            //检查是否已经裁剪过
            var btnthis=this;
            if (crop.w == undefined || crop.w == 0) {
                layer.alert('请先选出图片中需要的部分',{ icon: 0,});
                return;
            }
            $(this).text('正在保存');
            $(this).attr("disabled",true);
            var crop2 = crop.x + ',' + crop.y + ',' + crop.w  + ',' + crop.h;
            $.post('/user/user/crop_img', {crop: crop2}, function (data) {
                if (data.code==1) {
                    $(btnthis).text('保存');
                    $(btnthis).removeAttr('disabled').addClass('hide');
                    $('.yh img').attr('src',data.path);
                    $list.empty();
                    jcrop_api.destroy();
                    layer.msg(data.msg, {icon: 1});
                } else {
                    $(btnthis).text('保存');
                    $(btnthis).removeAttr('disabled').addClass('hide');
                    layer.msg(data.msg, {icon: 0});
                }
            });
        })
    });
})( jQuery );