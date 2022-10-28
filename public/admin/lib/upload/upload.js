var chunkSize = 2 * 1024 * 1024; //分块大小
var uniqueFileName = null; //文件唯一标识符
var md5Mark = null;
var uploader;
$(function() {
    //#############################################
    WebUploader.Uploader.register({
        "before-send-file": "beforeSendFile",
        "before-send": "beforeSend",
        "after-send-file": "afterSendFile"
    },
    {
        beforeSendFile: function(file) {
            var task = new $.Deferred();
            (new WebUploader.Uploader()).md5File(file, 0, 2 * 1024 * 1024).progress(function(percentage) {              
            }).then(function(val) {
                md5Mark = val;
                uniqueFileName = md5(file.name + file.type + file.lastModifiedDate + file.size);
                task.resolve();
            });
            return $.when(task);
        },
        beforeSend: function(block) {
            //分片验证是否已传过，用于断点续传
            var task = new $.Deferred();
            $.ajax({
                type: "POST",
                url: server_url,
                data: {
                    status: "chunkCheck",
                    name: uniqueFileName,
                    chunkIndex: block.chunk,
                    size: block.end - block.start,
                    path: upload_path
                },
                cache: false,
                timeout: 1000,
                //todo 超时的话，只能认为该分片未上传过
                dataType: "json"
            }).then(function(data, textStatus, jqXHR) {
                if (data.ifExist) { //若存在，返回失败给WebUploader，表明该分块不需要上传
                    task.reject();
                } else {
                    task.resolve();
                }
            },
            function(jqXHR, textStatus, errorThrown) { //任何形式的验证失败，都触发重新上传
                task.resolve();
            });
            return $.when(task);
        },
        afterSendFile: function(file) {
            var chunksTotal = 0;
			chunksTotal = Math.ceil(file.size / chunkSize)
            if ( chunksTotal >= 1) {
                //合并请求
                var task = new $.Deferred();
                $.ajax({
                    type: "POST",
                    url: server_url,
                    data: {
                        status: "chunksMerge",
                        name: uniqueFileName,
                        chunks: chunksTotal,
                        ext: file.ext,
                        md5: md5Mark,
                        path: upload_path
                    },
                    cache: false,
                    dataType: "json"
                }).then(function(data, textStatus, jqXHR) {
                    task.resolve();
                    if(data.code){
    					upload_return(data);
                    }
                },
                function(jqXHR, textStatus, errorThrown) {
                    task.reject();
                });
                return $.when(task);
            }
        }
    });
    //#############################################
    var $list = $('#divFileProgressContainer'),
    state = 'pending';
    uploader = WebUploader.create({
        auto: true,
        resize: false,
        swf: './public/admin/lib/upload/Uploader.swf',
        server: server_url,
        pick: '#upload',
        accept: {
            title: 'file',
            extensions: 'txt,html,zip,josn,mp3,wma,wav,amr,mp4,apk,ipa',
            mimeTypes: 'text/plain,text/html,application/zip,application/json,audio/mpeg,audio/x-ms-wma,audio/x-wav,audio/amr,video/mp4,application/vnd.android.package-archive,application/octet-stream.ipa'
        },
        duplicate: false,
        chunked: true,
        chunkSize: chunkSize,
		threads : 1,
		multiple : false, //多文件选择
        formData:{md5: "",uniqueFileName: "",path:upload_path}
    });
    uploader.on( 'uploadBeforeSend', function( obj, data ) {
        data.md5 = md5Mark;
        data.uniqueFileName = uniqueFileName;
    });  
    // 当有文件添加进来的时候
    uploader.on('fileQueued',
    function(file) {
        $list.parent().prev().addClass("layui-hide");
        $list.parent().removeClass("layui-hide");
        var box_width=$list.width();
        var html = "";
        html += "<div class=\"file_box\" id=\"" + file.id + "\" data-size=\"" + file.size + "\" data-time=\"" + new Date().getTime() + "\">";
        html += "    <div class=\"file_progress\">";
        html += "        <div class=\"file_progress_txt\" style=\"width:"+box_width+"px\">";
        html += "            <div class=\"file_status J_uploadFileStatus\">已上传：<em></em></div>";
        html += "            <div class=\"file_info J_uploadFileTip\">";
        html += "                <span>已上传：0 MB/0 MB</span>";
        html += "                <span>当前上传速度：0 MB/s</span>";
        html += "            </div>";
        html += "        </div>";
        html += "        <a href=\"javascript:upload_cancle('" + file.id + "');\" class=\"layui-icon layui-icon-close file_cancel\" style=\"left:"+(box_width-25)+"px\"></a>";
        html += "       <div class=\"file_progress_record\" style=\"width:  0.01%;\">";
        html += "           <div class=\"file_progress_txt\" style=\"width:"+box_width+"px\">";
        html += "               <div class=\"file_status J_uploadFileStatus\">已上传：<em></em></div>";
        html += "               <div class=\"file_info J_uploadFileTip\">";
        html += "                   <span>已上传：0 MB/0 MB</span>";
        html += "                   <span>当前上传速度：0 MB/s</span>";
        html += "               </div>";
        html += "           </div>";
        html += "           <a href=\"javascript:upload_cancle('" + file.id + "');\" class=\"layui-icon layui-icon-close file_cancel\" style=\"left:"+(box_width-25)+"px\"></a>";
        html += "       </div>";
        html += "   </div>";
        html += "</div>";
        $list.append(html);
    });
    // 文件上传过程中创建进度条实时显示。
    uploader.on('uploadProgress',
    function(file, percentage) {
        var $li = $('#' + file.id);
        $li.find('.file_progress_record').css('width', percentage * 100 + '%');
        $li.find('.J_uploadFileStatus em').html((percentage * 100).toFixed(2) + '%');
        var total_size = parseFloat($li.attr("data-size"));
        var file_size_M = roundNumber(((total_size / 1024) / 1024), 1);
        var uploaded_size = total_size * percentage;
        var uploaded_size_show = roundNumber(((uploaded_size / 1024) / 1024), 1).toFixed(1);
        $li.find('.J_uploadFileTip span:even').html("已上传："+uploaded_size_show+" MB/"+file_size_M+" MB");
        var currentTime = new Date().getTime();
        var start_time = parseInt($li.attr("data-time"));
        var used_time = (Math.ceil(currentTime - start_time) / 1000);
        var uploaded_speed = Math.floor(roundNumber(((uploaded_size / used_time) / 1024), 2));
        $li.find('.J_uploadFileTip span:odd').html("上传速度："+uploaded_speed+"KB/秒");
    });
    //文件上传错误
    uploader.on('uploadError',
    function(file, reason) {
        console.log("Error！" + file.id);
        if (state != 'stoped' && state != 'finished') {
            alert(file.name + " 上传错误，可能是网络原因，请稍后重试" + reason);
        }
    });
    //文件上传成功
    uploader.on('uploadSuccess',
    function(file) {
        var $li = $('#' + file.id);
        $li.parent().parent().addClass("layui-hide");
        $li.parent().parent().prev().removeClass("layui-hide");
        $li.remove();
        
    });
});
function roundNumber(num, dec) {
    var result = Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
    return result;
}
function upload_cancle(id) {
    uploader.cancelFile(id);
    $("#" + id).parent().parent().prev().removeClass("layui-hide");
    $("#" + id).parent().parent().addClass("layui-hide");
    $("#" + id).remove();
}
function upload_return(data){
    $('#upload').prev().val(data.path);
    $('[lay-filter="import_chapter"]').removeClass("layui-btn-disabled");
}