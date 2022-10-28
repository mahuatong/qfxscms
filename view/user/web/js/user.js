(function() {
	$('.i-left ul a').each(function(){
        var self = $(this),link = self.attr('href');
        if(location.pathname == link){
            self.parent().addClass('on');
        }
    });
    $('.i-title li').click(function(event) {
        var index=$(this).index();
        $('.i-title li').removeClass('on');
        $(this).addClass('on');
        $('.basicAccounts').addClass('hide');
        $('.basicAccounts').eq(index).removeClass('hide');
    });

    $('[data-upid]').each(function(){
        var that=this;
        var book_id = $(that).data('upid');
        $.get("/api/source/index",{id:book_id},function(data){
            if(data.code==1){
                $(that).siblings('.t3').children('a').text(data.title);
                layer.msg($(that).siblings('.t2').children('a').text()+" 章节已经更新");
            }
        });
    });

    var dialogMod = function(e, t) {
        var a = null,
        o = function() {
            a && (clearTimeout(a), a = null)
        };
        $(e).hover(function() {
            o(),
            $(t).show()
        },
        function() {
            o(),
            a = setTimeout(function() {
                $(t).hide()
            },
            50)
        }),
        $(t).hover(function() {
            o(),
            $(t).show()
        },
        function() {
            o(),
            a = setTimeout(function() {
                $(t).hide()
            },
            50)
        })
    }
    var accounts = $('li.accounts '), accountsDialog = $('li.accounts p');
    dialogMod(accounts,accountsDialog);
})();