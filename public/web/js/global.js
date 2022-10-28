$(function(){
	var Cookie = {
        /**
         * method get
         * @param name
         * @returns {null}
         */
        get: function(name){
            var carr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));

            if (carr != null){
                return decodeURIComponent(carr[2]);
            }

            return null;
        },
        /**
         * method set
         * @param name
         * @returns {null}
         */
        set:function(name, value, expires, path, domain){
            if(expires){
                expires = new Date(+new Date() + expires);
            }
            var tempcookie = name + '=' + escape(value) +
                ((expires) ? '; expires=' + expires.toGMTString() : '') +
                ((path) ? '; path=' + path : '') +
                ((domain) ? '; domain=' + domain : '');

            //Ensure the cookie's size is under the limitation
            if(tempcookie.length < 4096) {
                document.cookie = tempcookie;
            }
        },
        clear: function (name, path, domain) {
	        if (this.get(name)) {
	            document.cookie = name + "=" + ((path) ? "; path=" + path : "; path=/") + ((domain) ? "; domain=" + domain : "") + ";expires=Fri, 02-Jan-1970 00:00:00 GMT";
	        }
	    }
    };
    var Book = {
        el: 'body',
        /**
         * 页面逻辑入口
         */
        readSetting: {
	        "t" : 0,
	        "ft" : 1,
	        "fs" : 18,
	        "w" : 1,
	        "rt" : 0
	    },
	    init: function () {
            var that = this;
            //书id
            that.bookId = $(that.el).data('bid');
            //chapterInfo是否加载完成标识,false为允许向下加载
            that.chapterLoad = false;
            //当为瀑布流时,章节预加载标识
            that.chapterAdvanceLoad = {
                bool: false,
                id: '',
                pageTurn: '',
                content: '',
                url: ''
            };
            //标识因为页面短第一次触发滚动
            that.firstScroll = false;
            //瀑布流模式时,页面进入当前章节是否为普通章节最后一章  or  页面进入当前章节是否为vip章节最后一章
            if (( book.chapter.vipStatus == 0 && book.nextChapterVip == 1 ) || book.chapter.nextId == "-1") {
                that.loadLastBool = true;
            }
            //左侧导航box
            that.leftNav = $('#j_leftBarList');
            //获取内容区域box
            that.readMainWrap = $('#j_readMainWrap');
			//获取body
            that.bodyDom = $('body');
            //获取章节dom
            that.chapterBox = $('#j_chapterBox');
            //最近阅读是否存在标识
            that.navNearRead = false;
            //定义暂时存储setting参数
            that.zanshiSetting = {};
            //获取屏幕的高度
            that.winHeight = $(window).height();
            //禁止页面选中文字、copy、右键功能
            that.forbidCopy();
            //增加用户积分和经验
            that.expPoints();
            //阅读设置cookie set
            that.setReadCookie();
            //阅读左右导航虚浮
            that.readNav();
            //窗口resize触发
            that.windowResize();
            //当为左右切换,加载键盘事件
            that.chapterKey();
            //当为瀑布流时,加载懒加载事件
            that.chapterLazyLoad()

            //左侧导航目录弹窗
            $('#j_navCatalogBtn').click(function(event) {
            	that.navCata(event);
            });
            //左侧导航设置弹窗
            $('#j_navSettingBtn').click(function(event) {
            	that.navSetting(event);
            });
            //阅读主题选择、正文字体切换
            $('#j_themeList span, #j_fontFamily span, #j_readMode span').click(function(event) {
            	that.switchStyle(event);
            });
            //阅读字体设置
            $('#j_fontSize span').click(function(event) {
            	that.fontSizeSet(event)
            });
            //阅读正文宽度设置
            $('#j_pageWidth span').click(function(event) {
            	that.widthSet(event)
            });
            //阅读设置保存
            $('#j_setSave').click(function(event) {
            	that.readSetSave(event)
            });
            //阅读设置取消,不保存
            $('#j_setCancel , .setting-close').click(function(event) {
            	that.readSetCancel(event)
            });
            //关闭左侧面板浮层
            $('#j_leftBarList .close-panel').click(function(event) {
            	that.closeLeftPanel(event)
            });
            //手机阅读
            $('#j_phoneRead').click(function(event) {
                that.phoneRead(event)
            });
            //投票按钮
            $('#navTicket').click(function(event) {
                that.showVotePopup()
            });
            //返回顶部
            $('#j_goTop').click(function(event) {
            	that.goPageTop()
            });

        },
        /*
         * 禁止页面选中文字、copy、右键功能
         * @method forbidCopy
         */
        forbidCopy: function () {
            //禁止copy
            $('body').on('copy', function () {
                return false;
            });
            //禁止cut
            $('body').on('cut', function () {
                return false;
            });
            //禁止鼠标右键默认弹窗
            $('body').on('contextmenu', function () {
                return false;
            });

        },
        /*
         * 当为左右切换,加载键盘事件
         * @method chapterKey
         */
        chapterKey: function () {
        	var that = this;
            var toPage;
            //键盘事件
            $(document).on("keydown", function (e) {
                var target = e.target,
                    tagName = target.nodeName.toLowerCase();
                //当阅读模式为翻页模式,上下按键切换章节有效
                if (that.readSetting.rt == 0 && tagName != 'textarea' && tagName != 'input') {
                    if (e.keyCode == 37) {
                        //左方面键
                        toPage = $('#j_chapterPrev');
                        if (!toPage.hasClass('disabled')) {
                            //页面跳转链接
                            window.location.href = toPage.attr('href');
                        }
                    } else if (e.keyCode == 39) {
                        //右方面键
                        toPage = $('#j_chapterNext');
                        if (!toPage.hasClass('disabled')) {
                            //页面跳转链接
                            window.location.href = toPage.attr('href');
                        }
                    }
                }
            });

        },
        expPoints: function () {
            if(user.Info.id && user.Info.id!==0){
                setInterval(function () {
                    $.get("/user/user/add_exp_points");
                },60000);
            }
        },
        /*
         * 简单的节流函数
         * @method throttle
         * @param func
         * @param wait
         * @param mustRun
         * */
        throttle: function (func, wait, mustRun) {

            var timeout,
                startTime = new Date();

            return function () {
                var context = this,
                    args = arguments,
                    curTime = new Date();

                clearTimeout(timeout);
                // 如果达到了规定的触发时间间隔，触发 handler
                if (curTime - startTime >= mustRun) {
                    func.apply(context, args);
                    startTime = curTime;
                    // 没达到触发间隔，重新设定定时器
                } else {
                    timeout = setTimeout(func, wait);
                }
            };
        },
    	/**
         * 关闭左侧浮层面板
         * @method closeLeftPanel
         * @param e 事件对象
         */
        closeLeftPanel: function (e) {
            var target = $(e.currentTarget);
            //当前面板关闭
            $(target).parents('.panel-wrap').hide();
            //去除左侧点击后的当前样式
            $('#j_leftBarList dd').removeClass('act');
        },
    	/*
         * 阅读设置cookie set
         * @method setReadCookie
         */
        setReadCookie: function () {
            //判断reader_config是否存在,不存在种植cookie
            // Cookie.clear('reader_config');
            if (!Cookie.get('reader_config_web')) {
                var cookieSetData = this.readSetting.t + '|' + this.readSetting.fs + '|' + this.readSetting.ft + '|' + this.readSetting.rt + '|' + this.readSetting.w;
                //设置保存cookie,不包括是否订阅配置 ,时长 1年 365天
                Cookie.set('reader_config_web', cookieSetData, 86400000 * 365, '/');
            }else{
            	var readData=Cookie.get('reader_config_web').split("|");
            	this.readSetting.t=readData[0];
            	this.readSetting.fs=readData[1];
            	this.readSetting.ft=readData[2];
            	this.readSetting.rt=readData[3];
            	this.readSetting.w=readData[4];
            }
        },
	    readNav: function () {
	        var that = this,
	            win = $(window),
	            doc = $(document);

	        // 左侧导航定参
	        var leftBar = $('#j_leftBarList'),
	            nowLeftTop = leftBarTop = 119;

	        //右侧导航定参
	        var rightBar = $('#j_rightBarList'),
	            nowRightBottom = rightBarBottom = 120,
	            pageHeight,
	            bottomTo;

	        var goTop = $('#j_goTop');

	        win.on('scroll', function () {

	            //获取滚动条距顶部的位置
	            winScrollTop = win.scrollTop();
	            //获取页面高度、屏幕高度
	            pageHeight = doc.height();

	            //当滚动条位置大于leftBar距顶部的位置时,并且 nowLeftTop != 0
	            if (winScrollTop >= leftBarTop && nowLeftTop != 0) {
	                nowLeftTop = 0;
	                leftBar.css('top', nowLeftTop);
	            } else if (winScrollTop < leftBarTop) {
	                nowLeftTop = leftBarTop - winScrollTop;
	                leftBar.css('top', nowLeftTop);
	            }

	            //获取滚动条距底部的距离
	            bottomTo = pageHeight - that.winHeight - rightBarBottom;
	            //当滚动条位置大于rightBar距底部的位置时,并且 nowRightBottom != 0
	            if (winScrollTop <= bottomTo && nowRightBottom != 0) {
	                nowRightBottom = 0;
	                rightBar.css('bottom', nowRightBottom);
	            } else if (winScrollTop > bottomTo) {
	                nowRightBottom = rightBarBottom - pageHeight + that.winHeight + winScrollTop;
	                rightBar.css('bottom', nowRightBottom);
	            }

	            //回到顶部按钮是否出现
	            if (winScrollTop > 0) {
	                goTop.show();
	            } else {
	                goTop.hide();
	            }

	        }).trigger('scroll');
	    },
	    /**
         * 左侧工具栏按钮 各自执行的方法
         * @method leftBtnMethod
         * @param e 事件对象
         */
        leftBtnMethod: function (e) {

            var that = this,
                target = $(e.currentTarget),
                bool = 0;

            // 阅读设置的弹窗单独逻辑
            if($('#j_navSettingBtn').hasClass('act') &&  target.attr('id') != 'j_navSettingBtn'  ) {
                that.readSetCancel(e);
            }

            if (target.hasClass('act')) {
                target.removeClass('act').siblings().removeClass('act');
                bool = 1;
            } else {
                target.addClass('act').siblings().removeClass('act');
            }

            that.leftNav.find('.panel-wrap').hide();

            return bool;

        },
        /*
         * 左侧获取目录按钮
         * @method navCatalog
         *  @param e 事件对象
         * */
        navCata: function (e) {
            var that = this,
                catalogPop = $('#j_catalog'),
                catalogBox = $('#j_catalogListWrap');
            //调用选中func
            if (that.leftBtnMethod(e)) return false;
            catalogPop.show();
            if ($('.catalog-list-wrap').length == 0) {
            	$.ajax({
                    type: 'GET',
                    url: '/home/chapter/lists',
                    data: {
                        id: book.Info.Id
                    },
                    success: function (response){
                    	catalogBox.html(response);
                    	$('.left-bar-list .panel-list-wrap').css('max-height', (that.winHeight - 250 ) + 'px');
                    	showChapter();
                    }
                });
            }else{
            	showChapter();
            }
            //目录定位到章节
            function showChapter(){
	            //获取页面当前显示的章节id
	            var nowChapterId = ( that.readSetting.rt == 0 ) ? book.chapter.id : that.scrollChapter(),
	                chapterDom = $('#chapter-' + nowChapterId);
	            //移除li选中样式
	            catalogBox.find('li.on').removeClass('on');
	            //给新的目录章节添加选中样式
	            chapterDom.addClass('on');
	            //滚动到选中章节区域
	            catalogBox.scrollTop(0).scrollTop(chapterDom.offset().top - catalogBox.offset().top);
	        }
        },
        /*
         * 加载setiing弹窗
         * @method navSetting
         * @param e 事件对象
         * */
        navSetting: function (e) {
            //获取配置数组
            var that = this,
                settingPop = $('#j_setting');
            //调用选中func
            if (that.leftBtnMethod(e)) {
                that.readSetCancel(e);
                return false;
            }
            $.extend(that.zanshiSetting, that.readSetting);
            settingPop.show();
        },
    	/**
         *  切换主题、字体、阅读方式时的高亮效果
         *  @method switchStyle
         *  @param e 事件对象
         */
        switchStyle: function (e) {

            var that = this,
                target = $(e.currentTarget),
                targetNum = parseInt(target.data('st')),
                wList = ['640', '800', '900', '1280'],
                parentId = target.parents('li').attr('id');

            target.addClass('act').siblings().removeClass('act');

            //判断父亲节点的id
            switch (parentId) {
                case 'j_themeList':
                    //修改页面整体样式
                    that.bodyDom.attr('class', 'theme-' + targetNum + ' w' + wList[that.zanshiSetting.w]);
                    that.zanshiSetting.t = targetNum;
                    break;
                case 'j_fontFamily':
                    //修改正文字体
                    that.readMainWrap.attr('class', 'read-main-wrap font-family0' + targetNum);
                    that.zanshiSetting.ft = targetNum;
                    break;
                case 'j_readMode':
                    //设置阅读模式
                    that.zanshiSetting.rt = targetNum;
                    break;
            }

        },
        /*
         * 阅读字体设置
         * @method fontSizeSet
         * @param e 事件对象
         * */
        fontSizeSet: function (e) {

            var that = this,
                target = $(e.currentTarget),
                sizeBox = target.parents('#j_fontSize');
            sizeDom = target.parents('#j_fontSize').find('.lang'),
                sizeNum = parseInt(sizeDom.text());

            if (target.hasClass('prev') && sizeNum > 12) {
                sizeNum = sizeNum - 2;
            } else if (target.hasClass('next') && sizeNum < 48) {
                sizeNum = sizeNum + 2;
            } else {
                return false;
            }
            that.readMainWrap.css('font-size', sizeNum + 'px');
            sizeDom.text(sizeNum);
            that.zanshiSetting.fs = sizeNum;
        },
        /*
         * 阅读正文宽度设置
         * @method WidthSet
         * @param e 事件对象
         * */
        widthSet: function (e) {

            var that = this,
                target = $(e.currentTarget),
                widthDom = target.parents('#j_pageWidth').find('.lang'),
                widthNum = parseInt(widthDom.text()),
                wList = ['640', '800', '900', '1280'],
                screenWidth = $(window).width(),
                numId;

            //获取宽度排序
            switch (widthNum) {
                case 640 :
                    numId = 0;
                    break;
                case 800 :
                    numId = 1;
                    break;
                case 900 :
                    numId = 2;
                    break;
                case 1280 :
                    numId = 3;
                    break;
            }

            //宽度为减小时,且w>640执行
            if (target.hasClass('prev') && numId > 0) {
                that.zanshiSetting.w = numId - 1;
                //宽度为加大,不为最大宽度限时,且判断屏幕宽度+100 大于下次需要增加到的宽度时,
            } else if (target.hasClass('next') && numId < 3 && wList[numId + 1] <= screenWidth - 180) {
                that.zanshiSetting.w = numId + 1;
            } else {
                return false;
            }
            //主题
            var themeTypeList = [0, 2, 0, 3, 5, 5, 4, 6, 1],
                themeType = themeTypeList[that.zanshiSetting.t];
            //设置宽度
            that.bodyDom.attr('class', 'theme-' + themeType + ' w' + wList[that.zanshiSetting.w]);
            widthDom.text(wList[that.zanshiSetting.w]);
            $(window).trigger('resize');
        },
        /*
         * 阅读设置保存
         * @method readSetSave
         * @param e 事件对象
         * */
        readSetSave: function (e) {
            var that = this;
            //如果设置对比有修改,发送ajax请求,并显示保存设置
            if (that.readSetting != that.zanshiSetting) {
                var zsSet = that.zanshiSetting,
                    cookieSetData = zsSet.t + '|' + zsSet.fs + '|' + zsSet.ft + '|' + zsSet.rt + '|' + zsSet.w;
                 //判断阅读模式是否变化,是否为阅读页
                var readTypeBool = 0;
                if (that.readSetting.rt != zsSet.rt && book.chapter != undefined) {
                    readTypeBool = 1;
                }
                //设置保存cookie,不包括是否订阅配置 ,时长 1年 365天
                Cookie.set('reader_config_web', cookieSetData, 86400000 * 365, '/');
                //把暂存配置存入保存设置中
                $.extend(that.readSetting, that.zanshiSetting);
                if (readTypeBool) {
                    if (that.readTypeCallBack) that.readTypeCallBack(zsSet.rt);
                }
            }
            that.closeLeftPanel(e);
        },
        /*
         * 阅读设置取消,不保存
         * @method readSetSave
         * */
        readSetCancel: function (e) {
            var that = this,
                setWidth = ['640', '800', '900', '1280'];
            //暂存设置重置回保存设置
            $.extend(that.zanshiSetting, that.readSetting);

            //主题
            var themeTypeList = [0, 2, 0, 3, 5, 5, 4, 6, 1],
                themeType = themeTypeList[that.zanshiSetting.t];
            //页面重置
            that.bodyDom.attr('class', 'theme-' + themeType + ' w' + setWidth[that.zanshiSetting.w]);
            that.readMainWrap.attr('class', 'read-main-wrap font-family0' + that.zanshiSetting.ft);
            that.readMainWrap.css('font-size', that.zanshiSetting.fs + 'px');
            //设置弹窗重置回系统保存的设置配置
            $('#j_themeList span').eq(themeType).addClass('act').siblings().removeClass('act');
            $('#j_fontFamily span').eq(that.zanshiSetting.ft-1).addClass('act').siblings().removeClass('act');
            $('#j_fontSize .lang').text(that.zanshiSetting.fs);
            $('#j_pageWidth .lang').text(setWidth[that.zanshiSetting.w]);
            $('#j_readMode span').eq(that.zanshiSetting.rt).addClass('act').siblings().removeClass('act');
           	that.closeLeftPanel(e);
        },
        /**
         * 手机阅读
         * @method addToBookShelf
         * @param e 事件对象
         */
        phoneRead: function(e){
            var that = this,
                catalogPop = $('#j_cellphone');
            //调用选中func
            if (that.leftBtnMethod(e)) return false;
            catalogPop.show();
            $('#code').html("");
            $('#code').qrcode({ 
                render: "canvas", //table方式 
                width: 120, //宽度 
                height:120, //高度 
                text: book.chapterUrl.replace("chapterId", book.chapter.id)
            });
        },
        /**
         *  开启投票弹窗
         *  @method votePopup
         *  @param e 事件对象
         */
        showVotePopup: function (e) {
            var $panel = $('<div class="lbf-panel"><a href="javascript:;" class="lbf-panel-close iconfont"></a><div class="lbf-panel-body"><div class="vote-popup" id="votePopup"><div class="vote-popup-wrap" id="voteWrap"><div class="popup-content rec" id="recPopup"></div></div></div></div></div>'),
            $rec_popup = $panel.find('#recPopup');
            if(!user.Info.id || typeof(user.Info.id)=="undefined" || user.Info.id==0){
                $('<div class="limit-wrap"><div class="null no-ticket"></div><h3>请先登录后在投票</h3><div><a class="red-btn" href="/user/user/login.html">登录</a></div></div>').appendTo( $rec_popup );
            }else{
                if(!user.Info.recommend || typeof(user.Info.recommend)=="undefined" || user.Info.recommend==0){
                    $('<div class="limit-wrap"><div class="null no-ticket"></div><h3>暂无推荐票</h3></div>').appendTo( $rec_popup );
                }else{
                    var rec_list='';
                    for (i = 1; i < 10; i++) {
                        if(i==1){
                            rec_list += '<li data-rec="'+i+'" class="act"><div class="price"><p>'+i+'</p><span>推荐票</span></div><i></i><cite></cite></li>';
                        }else if(i>user.Info.recommend){
                            rec_list += '<li data-rec="'+i+'" class="disabled"><div class="price"><p>'+i+'</p><span>推荐票</span></div><i></i><cite></cite></li>';
                        }else{
                            rec_list += '<li data-rec="'+i+'"><div class="price"><p>'+i+'</p><span>推荐票</span></div><i></i><cite></cite></li>';
                        }
                    }
                    rec_list += '<li data-rec="'+user.Info.recommend+'"><div class="price"><p>全部</p><span>推荐票</span></div><i></i><cite></cite></li>';
                    var vote_form='<div class="no-limit-wrap"><div class="vote-form-wrap"><div class="rec-list cf" id="recList"><ul>'+rec_list+'</ul></div><h3>剩余推荐票<em>'+user.Info.recommend+'</em>张</h3></div><div class="popup-btn"><a class="red-btn" id="voteRec" href="javascript:">立即投票</a></div></div>';
                    $(vote_form).appendTo( $rec_popup );
                }
            }
            $("body").append($panel);
            $($rec_popup).on('click','#recList ul li:not(.disabled)',function(){
                $('#recList ul li:not(.disabled)').removeClass('act');
                $(this).addClass('act');
            });
            $($panel).on('click','#voteRec',function(){
                var post_rec = parseInt($('#recList ul').find('li.act').data('rec'));
                $.ajax({
                    type: 'POST',
                    url: '/user/user/vote_recom_ticket',
                    data: {
                        bookid: book.Info.Id,
                        cnt: post_rec
                    },
                    dataType: "json",
                    success: function (data){
                        if (data.code == 1) {
                            var vote_complete='<div class="vote-complete"><div class="complete-img rec-img"></div><h3 class="mb50">已投出<span class="post-num">'+post_rec+'</span>张推荐票</h3><div class="popup-btn"><a class="red-btn closeBtn" href="javascript:">关闭</a></div></div>';
                            user.Info.recommend=user.Info.recommend-post_rec;
                        }else{
                            var vote_complete='<div class="limit-wrap"><div class="null no-ticket"></div><h3>'+data.msg+'</h3><div class="popup-btn"><a class="red-btn closeBtn" href="javascript:">关闭</a></div></div>';
                        }
                        $rec_popup.html(vote_complete);
                    }
                });
            });
            $($panel).on('click','.lbf-panel-close,.closeBtn',function(){
                $panel.remove();
            });
        },
         /**
         * 返回页面顶部
         * @method goPageTop
         */
        goPageTop: function () {
            $('body,html').animate({scrollTop: 0}, 220);
        },
        /*
         * 判断滚动条滚到哪一章节
         * @method scrollChapter
         * @return (num) 章节id
         * */
        scrollChapter: function () {
            //获取所有章节list
            var chapterList = $('.text-wrap'),
                win = $(window),
                scHeight = win.height(),
                scrollTop = win.scrollTop() + scHeight / 2;
            //章节遍历
            var chapterIdList = chapterList.map(function () {
                var that = $(this),
                //获取当前章节距离页面顶部的距离
                    chapterItem = that.offset().top;
                //当章节scrollTop 小于 当前屏幕显示距顶部距离时,获取返回改章节id
                if (chapterItem < scrollTop) return that.data('cid');
            });
            //返回当前显示的章节id
            return chapterIdList[chapterIdList.length - 1];

        },
    	/*
         * 窗体改变时,改变高度
         * @method windowResize
         * */
        windowResize: function () {

            var that = this;

            $(window).on('resize', function () {

                var screenWidth = parseInt($(this).width()),
                    ChapterWidth = parseInt($('#j_readMainWrap').width());

                if (screenWidth < ChapterWidth + 136) {
                    $('#j_floatWrap').addClass('fix-float-wrap');
                } else {
                    $('#j_floatWrap').removeClass('fix-float-wrap');
                }

                if (screenWidth < ChapterWidth + 42) {
                    $('#j_floatWrap').addClass('left-bar-guide');
                } else {
                    $('#j_floatWrap').removeClass('left-bar-guide');
                }

                //当高度改变,去重置
                if (that.winHeight != $(this).height()) {
                    //重置
                    that.winHeight = $(this).height();
                    //主动触发窗体滚动
                    $(this).trigger('scroll');
                    //设置展开区域的最大高度
                    $('.left-bar-list .panel-list-wrap').css('max-height', (that.winHeight - 250 ) + 'px');
                }

            }).trigger('resize');
        },
        /*
         * 阅读模式切换触发
         * @method readTypeChange
         * */
        readTypeCallBack: function (type) {
    		var that = this;
            //移除其他状态的翻页
            $('.chapter-control').remove();
            //判断为哪种阅读方式
            switch (type) {
                //翻页模式
                case 0:
                    that.readPageType();
                    break;
                //瀑布流模式
                case 1:
                    that.readLoadType();
                    // 主动触发scroll
                    $(window).trigger('scroll');
                    break;
            }
        },
        /*
         * 阅读模式切换成翻页模式
         * @method readTypePage
         * */
        readPageType: function () {
            var that = this,
            //获取页面当前显示的章节id
                nowshowChapterId = that.scrollChapter(),
            //章节对象
                chapterBox = $('#ajaxchapter-' + nowshowChapterId),
            //除了该章节其他章节
                otherChapter = chapterBox.siblings('.text-wrap');
            //遍历其他章节,删除
            otherChapter.each(function (i, el) {
                if ($(el).data('cid') != nowshowChapterId) {
                    $(el).remove();
                }
            });

            $('.j_chapterLoad').addClass('hidden');

            //获取上一章下一章url
            var prevUrl = chapterBox.data('purl'),
                nextUrl = chapterBox.data('nurl');
            //获取章节相关信息
            var chapterAndInfo = chapterBox.data('info').split('|');
            //生成dom
            var pageStr = '<div class="chapter-control dib-wrap" data-l1="3"><a id="j_chapterPrev" href="' + prevUrl + '">上一章</a><span>|</span><a id="j_chapterNext" href="' + nextUrl + '">下一章</a></div>';
            //加入页面中
            $('#j_readMainWrap').append(pageStr);

            //重置 book.chapter
            book.chapter = {
                //页面进入加载的章节id
                id: nowshowChapterId,
                //章节vip标识
                vipStatus: chapterAndInfo[0],
                //上一章id
                prevId: chapterAndInfo[1],
                //下一章id
                nextId: chapterAndInfo[2]
            };
            book.nextChapterVip = chapterAndInfo[3];
            //重置初始化章节预加载标识
            that.chapterAdvanceLoad = {
                bool: false,
                id: '',
                pageTurn: '',
                content: '',
                url: ''
            };
            that.firstScroll = false;

            $('.page-ops').remove();
        },
        /*
         * 阅读模式切换成瀑布流模式
         * @method readLoadType
         * @param   advanceBool   是否为预加载 默认false 不是 ,true 为是
         * @param   nextUrl   当advanceBool 为 true 的时候才有这个值
         * */
        readLoadType: function (advanceBool, nextUrl) {

            var that = this,
                nextBtnText = '',
                repartData = '';

            $('.page-ops').remove();

            advanceBool = advanceBool ? advanceBool : false;
            //当前章节是否为普通章节最后一章
            if (book.chapter.vipStatus == 0 && book.nextChapterVip == 1) {
                nextBtnText = '前往VIP章节';
                //当前章节是否为vip章节最后一章
            } else if (book.chapter.nextId=="-1") {
                nextBtnText = '最后一章没有了，前往书页';
            }
            //瀑布流时,显示按钮的方法
            if (nextBtnText != '') {
                //获取nextUrl
                var nextUrl = ( advanceBool ) ? nextUrl : $('#ajaxchapter-' + book.chapter.id).data('nurl');
                that.loadLastBool = true;
                var loadToDom = '<div class="chapter-control dib-wrap"> <a class="w-all" href="' + nextUrl + '">' + nextBtnText + '</a></div>';
                //判断是否是预加载
                if (advanceBool) {
                    that.chapterAdvanceLoad.pageTurn = loadToDom;
                } else {
                    //加入页面中
                    $('#j_readMainWrap').append(loadToDom);
                }
            } else {
                that.loadLastBool = false;
                //判断是否是预加载
                if (advanceBool) that.chapterAdvanceLoad.pageTurn = '';
            }

        },
        /*
         * 重新获取章节内容
         * @method  getChapterId
         * @param   chapterId           章节id
         * @param   isRepeat            是否是重复加载 是:true  否:false
         * @param   advanceBool         是否为预加载 默认false 不是 ,true 为是
         * @param   successCallback     成功操作之后等回调
         * @param   failCallBack        失败的回调
         * */
        getChapterInfo: function (chapterId, isRepeat, advanceBool, successCallback, failCallBack) {
            var that = this;
            advanceBool = advanceBool ? advanceBool : false;
            $.ajax({
                type: 'GET',
                url: '/home/chapter/info',
                dataType: 'json',
                data: {id: book.chapter.source_id, key: chapterId},
                success: function (response) {
                    if (response.code == 1) {
                        var data = response.data;
                        if (isRepeat) {
                            $('#ajaxchapter-' + chapterId).remove();
                            if (that.readSetting.rt == 1) $('.chapter-control').remove();
                        }
                        //加载章节到页面中
                        var chapter_content=that.chapterContent(data);
                        if (advanceBool) {
                            that.chapterAdvanceLoad.content = chapter_content;
                            that.chapterAdvanceLoad.id = data.chapter.id;
                        } else {
                            that.chapterBox.append(chapter_content);
                        }
                        //重置 book.chapter
                        book.chapter = {
                            //页面进入加载的章节id
                            id: data.chapter.id,
                            //章节源id
                            source_id:data.chapter.source_id,
                            //章节vip标识
                            vipStatus: data.chapter.vip,
                            //上一章id
                            prevId: data.chapter.prev.id,
                            //下一章id
                            nextId: data.chapter.next.id,
                        };
                        book.nextChapterVip = data.chapter.nextVip;
                        //获取当前章节的url
                        var nowChapterUrl = $('#ajaxchapter-' + book.chapter.prevId).data('nurl');
                        //加载章节到页面中
                        if (advanceBool) {
                            that.chapterAdvanceLoad.url = nowChapterUrl;
                        }
                        //判断章节是否为最后一章
                        var nextUrl = ( !data.chapter.next.id ) ? that.bookId : data.chapter.next.url;
                        //当为瀑布流是才去判断是否是最后一章
                        if (that.readSetting.rt == 1) that.readLoadType(advanceBool, nextUrl);
                        //成功操作之后等回调
                        if (successCallback) successCallback(data);
                    } else {
                        //失败的回调
                        if (failCallBack) failCallBack();
                    }
                }
            });
        },
        chapterContent: function(data){
        	var that = this;
        	var chapter_box=that.chapterBox.children('div:first').prop("outerHTML");
            var chapter_box_jq=$(chapter_box);
            chapter_box_jq.attr('id','ajaxchapter-'+data.chapter.id);
            chapter_box_jq.attr('data-cid',data.chapter.id);
            chapter_box_jq.attr('data-purl',data.chapter.prev.url);
            chapter_box_jq.attr('data-nurl',data.chapter.next.url);
            chapter_box_jq.attr('data-info',data.chapter.vip+'|'+data.chapter.prev.id+'|'+data.chapter.next.id+'|'+data.chapter.nextVip);
            chapter_box_jq.find('.j_chapterName').text(data.chapter.title);
            chapter_box_jq.find('.j_chapterWordCut').text(data.chapter.word);
            chapter_box_jq.find('.j_updateTime').text(data.chapter.time);
            chapter_box_jq.find('.j_readContent').html(data.chapter.content);
            if(data.chapter.intro){
                if(chapter_box_jq.hasClass('author-say')){
                    chapter_box_jq.find('.author-say p').html(data.chapter.intro);
                }else{
                    chapter_box_jq.find('.j_readContent').after('<div class="author-say-wrap"><h3>作者感言</h3><div class="author-say cf"><p>'+data.chapter.intro+'</p></div></div>');
                }
                
            }
            
            return chapter_box_jq.prop("outerHTML");
        },
        /*
         * 当为瀑布流时,加载懒加载事件
         * @method chapterLazyLoad
         * */
        chapterLazyLoad: function () {

            var that = this,
            //页面高度
                pageHeight = $(document).height(),
            //滚动条距顶部高度
                winSTop,
                winHeight = $(window).height();

            $(window).on('scroll', that.throttle(function () {
                if ((book.chapter.nextId=="-1" || !book.chapter.nextId) && !that.loadLastBool) {
                    that.loadLastBool = true;
                    var loadToDom = '<div class="chapter-control dib-wrap"><a class="w-all" href="' + book.Info.url + '">最后一章没有了，前往书页</a></div>';
                    $('#j_readMainWrap').append(loadToDom);
                }
                //当为左右切换时,不执行
                if (that.readSetting.rt == 0) return false;
                //当当前章节为vip章节,且未订阅时,不再加载下面章节
                // if (book.chapter.vipStatus == 1 && g_data.chapter.isBuy == 0) return false;
                //当正在加载章节时,不再加载下面章节
                if (that.chapterLoad) return false;
                //当前章节是否为最后一章(普通章节&&vip章节),不再加载下面章节
                if (that.loadLastBool && !that.chapterAdvanceLoad.bool) return false;

                //当页面为瀑布流形式,且章节判断是可以继续加载时,加载
                pageHeight = $(document).height();
                winSTop = $(window).scrollTop();
                //初始化浏览器高度
                winHeight = $(window).height();

                //vip 不提前加载,页面滚到底部,加载
                var cHeight = ( book.chapter.vipStatus == 1 && book.nextChapterVip == 1 ) ? winHeight : ( 2.5 * winHeight );
                //当剩下小于1屏未显示的时候,加载新的章节
                if (pageHeight <= ( winSTop + cHeight )) {
                    //显示加载load
                    $('.j_chapterLoad').show();
                    //判断与加载里面的是否有章节信息
                    if (that.chapterAdvanceLoad.bool && that.chapterAdvanceLoad.id == book.chapter.id) {
                        //去显示预加载的内容
                        that.addAdvanceChapter();
                        $('.j_chapterLoad').hide();
                    } else {
                        //重置为true,禁止发送请求
                        that.chapterLoad = true;
                        //拉取章节信息
                        that.getChapterInfo(book.chapter.nextId, false, false,
                            //数据拉取成功的回调
                            function (data) {
                                //重置为false,允许下次符合条件时,发送请求
                                that.chapterLoad = false;
                                $('.j_chapterLoad').hide();
                                //更新阅读进度
                                that.repeatReadStatus(data.id, data.chapter.source_id, data.chapter.id);
                                if (that.firstScroll) that.firstScroll = false;
                            },
                            //数据拉取失败的回调
                            function () {
                                //拉取失败,重置为false ,再次拉取
                                that.chapterLoad = false;
                            }
                        );
                    }
                    //章节预加载
                } else if (!that.chapterAdvanceLoad.bool && book.nextChapterVip != 1 && !that.firstScroll && !that.chapterLoad) {
                    //重置为true,禁止发送请求
                    that.chapterLoad = true;
                    that.chapterAdvanceLoad.bool = true;
                    //拉取章节信息
                    that.getChapterInfo(book.chapter.nextId, false, true,
                        //数据拉取成功的回调
                        function (data) {
                            //加载完成,重置
                            that.chapterLoad = false;
                            that.repeatReadStatus(data.id, data.chapter.source_id, data.chapter.id);
                            $(window).trigger('scroll');
                        },
                        //数据拉取失败的回调
                        function () {
                            //拉取失败,重置为false ,再次拉取
                            that.chapterLoad = false;
                            //预加载完成,标识
                            that.chapterAdvanceLoad.bool = false;
                        }
                    );
                }
            }, 100, 160));

            //当页面高度小于视窗高度时,触发
            if (pageHeight <= winHeight) {
                //标识第一次加载,直接加载dom到页面
                that.firstScroll = true;
                $(window).trigger('scroll');
            }
        },
        /*
         * 更新阅读进度
         * @method repeatReadStatus
         * */
        repeatReadStatus: function(novel_id,id,key){
            $.get("/home/chapter/updread", {
                novel_id: novel_id,
                id: id,
                key: key
            });
        },
        /*
         * 显示预加载章节
         * @method addAdvanceChapter
         * */
        addAdvanceChapter: function () {

            var that = this,
                chapterCon = that.chapterAdvanceLoad;
            //加入章节
            that.chapterBox.append(chapterCon.content);
            //pageTurn加入页面中
            $('#j_readMainWrap').append(chapterCon.pageTurn);

            //重置章节预加载标识
            that.chapterAdvanceLoad = {
                bool: false,
                id: '',
                pageTurn: '',
                content: '',
                url: ''
            };
        },
    }
    Book.init();
});