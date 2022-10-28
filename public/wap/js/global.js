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
    var $settings = $("#pageReadOpt")
    var data = {
        isLogin: user.Info.id,
        isPublish: $settings.data("book-publish") == "0" ? 0 : 1,
        salesMode: $settings.data("sale-modes") == "2" ? 1 : 0,
        hasSub: $settings.data("has-sub") == "0" ? 0 : 1,
        settingsStr: $settings.data("settingsStr"),
        chapterUrl: book.chapterUrl,
        endUrl: book.url,
        book: book.Info,
        chapters: function() {
            var chapters = {};
            chapters[book.chapter.id] = book.chapter;
            return chapters;
        }(),
        chapter: book.chapter,
        scrollLastChapter: book.chapter,
        lastOfflineChapterId: null,
        isInBookShelf: 0,
        catalog: null
    };
    var isOffline = function() {
        return navigator && navigator.onLine === false;
    };
    var Book = {
	    init: function () {
            var that = this;
            //获取屏幕的高度
            that.winHeight = $(window).height();
            //禁止页面选中文字、copy、右键功能
            that.forbidCopy();
            that.initExpPoints();
            this.initOptionsLayer();
            this.initVoteInteracter();
            this.initCatalogAside();
            this.initChapterProgress();
            this.initFontSizeSetting();
            this.initSkinSetting();
            this.initModeSetting();
            this.initDayNightSetting();
            this.initModes();
            if ($("#pageRead").hasClass("H")) {
                this.swipeReader.enable();
            } else {
                this.scrollReader.enable();
            }
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
        initExpPoints: function() {
            if(user.Info.id && user.Info.id!==0){
                setInterval(function () {
                    $.get("/user/user/add_exp_points");
                },60000);
            }
        },
        initOptionsLayer: function() {
            var $layer = $("#pageReadOpt");
            var $settingsMenu = $("#readBtnMore");
            $layer.on({
                click: function(event) {
                    var target = event.target;
                    if (target !== this || target.avoidClick) {
                        return;
                    }
                    var $activeLayer = $layer.find(".jsLayerTrigger.active");
                    if ($activeLayer.length) {
                        $activeLayer.trigger("click");
                    }
                    $layer.removeClass("active");
                },
                touchstart: function(event) {
                    this.flagMoveHide = event.target === this;
                },
                touchmove: function(event) {
                    if (this.flagMoveHide) {
                        $layer.trigger("click");
                    }
                    event.preventDefault();
                }
            });
            new Toggle($settingsMenu);
            var $layerTrigger = $(".jsLayerTrigger");
            $layerTrigger.on("click", function(event) {
                var self = this;
                if (event && (event.isTrusted === false || event.hasOwnProperty("_args") || event.pageY === 0 && event.screenY === 0)) {
                    return;
                }
                $layerTrigger.each(function() {
                    var $this = $(this);
                    if (this !== self && $this.hasClass("active")) {
                        $this.trigger("click");
                    }
                });
            });
        },
        initVoteInteracter: function() {
            $("#btnPledgeTrigger").on("click", function() {
                var $aside = $('<aside class="aside"></aside>'),
                $overlay = $('<i class="aside-overlay"></i>').on("touchmove", function(event) {
                    event.preventDefault();
                });
                $overlay.attr({
                    role: "button"
                });
                var $popup = $('<div class="aside-popup"></div>');
                var $body_popup = $('<div class="popup-pay-body"></div>');
                if(!user.Info.id || typeof(user.Info.id)=="undefined" || user.Info.id==0){
                    $('<main class="popup-pay-main"><p class="popup-pay-error">你还未登录，<a href="/user/user/login.html" class="red">立即登录</a></p></main><footer class="popup-pay-footer"></footer>').appendTo( $body_popup );
                }else{
                    if(!user.Info.recommend || typeof(user.Info.recommend)=="undefined" || user.Info.recommend==0){
                        $('<main class="popup-pay-main"><p class="popup-pay-error">暂无推荐票</p></main><footer class="popup-pay-footer"></footer>').appendTo( $body_popup );
                    }else{
                        var rec_list='';
                        for (i = 1; i < 10; i++) {
                            if(i==1){
                                rec_list += '<li class="popup-ticket-li label-all"><label class="popup-ticket-a act" data-rec="'+i+'"><output>'+i+'</output>推荐票</label></li>';
                            }else if(i>user.Info.recommend){
                                rec_list += '<li class="popup-ticket-li label-all"><label class="popup-ticket-a disabled" data-rec="'+i+'"><output>'+i+'</output>推荐票</label></li>';
                            }else{
                                rec_list += '<li class="popup-ticket-li label-all"><label class="popup-ticket-a" data-rec="'+i+'"><output>'+i+'</output>推荐票</label></li>';
                            }
                        }
                        rec_list += '<li class="popup-ticket-li label-all"><label class="popup-ticket-a" data-rec="'+user.Info.recommend+'"><output>全部</output>推荐票</label></li>';
                        var vote_form='<main class="popup-pay-main"><ol class="popup-ticket-x">'+rec_list+'</ol></main><footer class="popup-pay-footer"><div class="popup-pay-data">推荐票<output class="red">'+user.Info.recommend+'</output>张</div><div class="popup-pay-submit"><label class="btn-submit">投推荐票<span class="radio-update">1张</span></label></div></footer>';
                        $(vote_form).appendTo( $body_popup );
                    }
                }
                $popup.append($body_popup);
                $aside.append($overlay).append($popup);
                $("body").append($aside);
                $aside.css({
                    top: "auto",
                    height: $overlay.height()
                });
                $aside.addClass('active');
                $($body_popup).on('click','.popup-pay-main ol li label:not(.disabled)',function(){
                    $('.popup-pay-main ol li label:not(.disabled)').removeClass('act');
                    $(this).addClass('act');
                    var rec_text = $(this).find('output').text();
                    var re = / ^\d+$/;
                　　if (re.test(rec_text)) {
                　　　　 rec_text+='张';
                　　}
                    $('.radio-update').text(rec_text);
                });
                $($aside).on('click','.btn-submit',function(){
                    var post_rec = parseInt($('.popup-pay-main ol').find('li label.act').data('rec'));
                    $aside.remove();
                    var layer_index=layer.open({type: 2,content: '正在提交中'});
                    $.ajax({ type : "post", url : "/user/user/vote_recom_ticket",
                        data: {
                            bookid: book.Info.Id,
                            cnt: post_rec
                        },
                        dataType: "json",
                        success : function(data){
                            layer.close(layer_index);
                            if (data.code == 1) {
                                user.Info.recommend=user.Info.recommend-post_rec;
                            }
                            layer.open({
                                content: data.msg
                                ,btn: ['再投一张', '关闭']
                                ,yes: function(index){
                                  layer.close(index);
                                  $("#btnPledgeTrigger").trigger("click");
                                }
                            });
                        }
                    });
                });

                $($aside).on('click','.aside-overlay',function(){
                    $aside.remove();
                });

            });
        },
        initCatalogAside: function() {
            var self = this;
            $("#readBtnChapter").aside({
                scrollable: "#catelogX",
                onInit: function() {
                    var aside = this;
                    var layer_index=layer.open({type: 2,content: '加载中'});
                    $.ajax({ type : "get", url : "/home/chapter/lists",
                        data : {id: data.book.Id}, 
                        async : false, 
                        success : function(result){ 
                            layer.close(layer_index);
                            $("#asideChapterContent").html(result);
                            $("#asideChapterContent").on("click", ".jsChapter a", function(event) {
                                event.preventDefault();
                                aside.hide();
                                $("#pageReadOpt").trigger("click");
                                self.jumpChapter($(this).data("chapter-id"), true);
                            });
                        } 
                    }); 
                },
                onShow: function() {
                    var nowChapterId = data.chapter.id,chapterDom = $('[data-chapter-id="'+nowChapterId+'"]');
                    $('#catelogX').find('.chapter-index.red').removeClass('red');
                    chapterDom.find('.chapter-index').addClass('red');
                    $('#catelogX').scrollTop(0).scrollTop(chapterDom.offset().top - $('#catelogX').offset().top);
                }
            });
        },
        initChapterProgress: function() {
            var self = this;
            var $toggle = $("#readBtnProg");
            var range;
            var chapterIds = [];
            var chapters = {};
            function refresh(chapterId) {
                var chapterIndex = chapterIds.indexOf(chapterId);
                if (chapterIndex < 0) {
                    chapterIndex = 0;
                    chapterId = chapterIds[chapterIndex];
                }
                var chapterName = chapters[chapterId];
                var $currentChapter = $("#readProgVal");
                $currentChapter.find("h4").html(chapterName);
                $currentChapter.find("output").html((chapterIndex * 100 / chapterIds.length).toFixed(1) + "%");
            }
            function init(result) {
                $(result).find('.chapter-li-a').each(function(){
                    var chapter_id = $(this).data('chapter-id');
                    chapterIds.push(chapter_id);
                    chapters[chapter_id] = $(this).text();
                    
                });
                refresh(data.chapter.id);
                var $chapterProgressRange = $("#readProgRange");
                $chapterProgressRange.attr("max", chapterIds.length - 1);
                $chapterProgressRange.val(chapterIds.indexOf(data.chapter.id));
                $chapterProgressRange.on("change", function() {
                    var chapterId = chapterIds[this.value];
                    refresh(chapterId);
                });
                range = new Range($chapterProgressRange, {
                    shadow: true,
                    buttons: [ "#readProgPrev", "#readProgNext" ],
                    onChangeEnd: function() {
                        var chapterId = chapterIds[$chapterProgressRange.val()];
                        self.jumpChapter(chapterId, true);
                    },
                });
                range.shadow();
            }
            new Toggle($toggle, {
                callback: function() {
                    if (range) {
                        range.value(chapterIds.indexOf(data.chapter.id));
                        range.shadow();
                    }
                }
            });
            $toggle.one("click", function() {
                if ($('catelogX').length != 0) {
                    init($('#asideChapterContent').html());
                } else {
                    var $el = $("#readOptProg").css("opacity", 0);
                    var layer_index=layer.open({type: 2,content: '加载中'});
                    $.getJSON("/home/chapter/lists", {
                        id: data.book.Id
                    }, function(result) {
                        layer.close(layer_index);
                        $el.css("opacity", 1);
                        init(result);
                    });
                }
            });
        },
        initFontSizeSetting: function() {
            var self = this;
            var $toggle = $("#readBtnSet");
            var $range = $("#readFontRange");
            var $chapterContent = $("#chapterContent");
            var lazyHandler = debounce(function(val, e) {
                self.scrollReader.refresh();
                self.swipeReader.refresh();
                self.updateSettings({
                    fs: val
                });
            }, 300);
            new Toggle($toggle);
            new Range($range, {
                buttons: [ "#readFontDown", "#readFontUp" ]
            });
            $range.on("change", function(e) {
                var val = this.value;
                var rem = (val * 2 + 12) / 16;
                $chapterContent.css("font-size", rem + "rem");
                lazyHandler(val, e);
            });
        },
        initSkinSetting: function() {
            var self = this;
            var $body = $("body");
            var $dn = $("#readBtnMode");
            var lazyHandler = debounce(function(val) {
                self.updateSettings({
                    skin: val
                });
            }, 300);
            $("#readSetSkin").on("click", '[type="radio"]', function(){
                var $this = $(this);
                $body.removeClass("read-night");
                $body.attr("class", $body.attr("class").replace(/skin-[a-z]+/g, "skin-" + $this.val()));
                if ($dn.data("mode") === "day") {
                    $dn.trigger("click");
                }
                lazyHandler($this.data("index"));
            });
        },
        initModeSetting: function() {
            var self = this;
            var lazyHandler = debounce(function(val){
                self.updateSettings({
                    vh: val
                });
            }, 300);
            $("#readSetLayout").on("click", '[type="radio"]', function() {
                if (this.value === "v") {
                    $("#pageRead").removeClass("H");
                    self.scrollReader.enable();
                    self.swipeReader.disable();
                    self.jumpChapter(data.chapter.id);
                } else {
                    $("#pageRead").addClass("H");
                    self.scrollReader.disable();
                    self.swipeReader.enable();
                    self.jumpChapter(data.chapter.id);
                }
                lazyHandler(this.value);
            });
        },
        initDayNightSetting: function() {
            var self = this;
            var $body = $("body");
            var $btn = $("#readBtnMode");
            var $title = $btn.find("h4");
            var DAY = "day";
            var NIGHT = "night";
            var lazyHandler = debounce(function(val) {
                self.updateSettings({
                    dn: val
                });
            }, 300);
            $btn.on("click", function() {
                var mode = $btn.data("mode");
                if (mode === DAY) {
                    $btn.data("mode", NIGHT);
                    $title.html($title.html().replace("日", "夜"));
                    $body.removeClass("read-night");
                    var skin = $body.attr("class").match(/skin-([a-z]+)/);
                    if (skin) {
                        skin = skin[1];
                        $('#readSetSkin [type="radio"][value="' + skin + '"]').prop("checked", true);
                    }
                    lazyHandler("d");
                } else if (mode === NIGHT) {
                    $btn.data("mode", DAY);
                    $title.html($title.html().replace("夜", "日"));
                    $body.addClass("read-night");
                    $('#readSetSkin [type="radio"]').each(function() {
                        var $this = $(this);
                        if ($this.prop("checked")) {
                            $this.prop("checked", false);
                        }
                    });
                    lazyHandler("n");
                }
            });
        },
        initModes: function() {
            var self = this;
            var $goEnd = $("#aGotoBookEnd");
            // 当前章节id
            function isChapterShown(chapterId) {
                return $(".jsChapterWrapper").map(function() {
                    return $(this).data("chapter-id");
                }).indexOf(chapterId) > -1;
            }
            var scrollReader = this.scrollReader = new ScrollReader({
                wrapperSelector: "#readContent",
                prevSelector: "#readLoadPrev",
                nextSelector: "#readLoadNext",
                nextTriggerSelector: "#btnLoadNextChapter",
                markSelector: "#readPageMark",
                onPrevClick: function(e) {
                    //点击向前滚动
                },
                onCenterClick: function() {
                    $settings[0].avoidClick = true;
                    setTimeout(function() {
                        $settings[0].avoidClick = false;
                    }, 500);
                    $settings.addClass("active");
                },
                onNextClick: function(e) {
                    //点击向后滚动
                },
                onScroll: function(y) {
                    var isViewportChapterId;
                    scrollReader.$wrapper.find(".jsChapterWrapper").each(function() {
                        if (isViewportChapterId) {
                            return;
                        }
                        var $this = $(this);
                        var offset = $this.offset();
                        if (offset.top < y && offset.top + offset.height > y) {
                            isViewportChapterId = $this.data("chapter-id");
                        }
                    });
                    if (isViewportChapterId) {
                        self.setReadChapter(isViewportChapterId);
                    }
                },
                onScrollPrev: function(resolve, reject) {
                    var chapterId = data.chapter.prevId;
                    if (chapterId) {
                        self.jumpChapter(chapterId, false, resolve, reject);
                    } else {
                        layer.open({content:'已经是第一章了',skin:'msg',time:2});
                        reject();
                    }
                },
                onScrollNext: function(resolve, reject) {
                    var chapterId = data.scrollLastChapter.nextId;
                    if (chapterId) {
                        self.getChapter(chapterId, false, function(result) {
                            data.scrollLastChapter = result.data.chapterInfo;
                            if (!isChapterShown(chapterId)) {
                                if(result.data.chapterInfo.intro){
                                    $("#chapterContent").append('<section class="read-section jsChapterWrapper" data-chapter-id="'+result.data.chapterInfo.id+'"><h3>'+result.data.chapterInfo.title+'</h3>'+result.data.chapterInfo.content+'<div class="read-author-say jsAuthorSay"><div class="read-author-p">'+result.data.chapterInfo.intro+'</div></div></section>');
                                }else{
                                    $("#chapterContent").append('<section class="read-section jsChapterWrapper" data-chapter-id="'+result.data.chapterInfo.id+'"><h3>'+result.data.chapterInfo.title+'</h3>'+result.data.chapterInfo.content+'</section>');
                                }
                                if (data.scrollLastChapter.nextId) {
                                    scrollReader.$nextTrigger.show();
                                    $goEnd.hide();
                                } else {
                                    scrollReader.$nextTrigger.hide();
                                    $goEnd.show();
                                }
                                resolve();
                            } else {
                                reject();
                            }
                        }, reject);
                    } else {
                        scrollReader.$nextTrigger.hide();
                        $goEnd.show();
                        resolve();
                    }
                },
                onWillScrollNext: function(resolve, reject) {
                    var chapterId = data.scrollLastChapter.nextId;
                    if (chapterId && !(data.scrollLastChapter.vipStatus && $("#swiChk1").prop("checked"))) {
                        self.getChapter(chapterId, false, function(result) {
                            data.scrollLastChapter = result.data.chapterInfo;
                            if (!isChapterShown(chapterId)) {
                                if(result.data.chapterInfo.intro){
                                    $("#chapterContent").append('<section class="read-section jsChapterWrapper" data-chapter-id="'+result.data.chapterInfo.id+'"><h3>'+result.data.chapterInfo.title+'</h3>'+result.data.chapterInfo.content+'<div class="read-author-say jsAuthorSay"><div class="read-author-p">'+result.data.chapterInfo.intro+'</div></div></section>');
                                }else{
                                    $("#chapterContent").append('<section class="read-section jsChapterWrapper" data-chapter-id="'+result.data.chapterInfo.id+'"><h3>'+result.data.chapterInfo.title+'</h3>'+result.data.chapterInfo.content+'</section>');
                                }
                                if (data.scrollLastChapter.nextId) {
                                    scrollReader.$nextTrigger.show();
                                    $goEnd.hide();
                                } else {
                                    scrollReader.$nextTrigger.hide();
                                    $goEnd.show();
                                }
                                resolve();
                            } else {
                                reject();
                            }
                        }, reject);
                    } else {
                        reject();
                    }
                }
            });
            this.swipeReader = new SwipeReader({
                wrapperSelector: "#readContent",
                scrollerSelector: ".jsChapterWrapper",
                pagerSelector: "#pageNum",
                onCenterClick: function() {
                    $settings[0].avoidClick = true;
                    setTimeout(function() {
                        $settings[0].avoidClick = false;
                    }, 500);
                    $settings.addClass("active");
                },
                onSwipePrev: function(resolve, reject) {
                    var chapterId = data.chapter.prevId;
                    if (chapterId) {
                        self.jumpChapter(chapterId, false, resolve, reject);
                    } else {
                        layer.open({content:'已经是第一章了',skin:'msg',time:2});
                        reject();
                    }
                },
                onSwipeNext: function(resolve, reject) {
                    var chapterId = data.chapter.nextId;
                    if (chapterId) {
                        self.jumpChapter(chapterId, false, resolve, reject);
                    }else{
                        location.href = data.endUrl;
                        // layer.open({content:'已经是最后一章了',skin:'msg',time:2});
                        // reject();
                    }
                },
                onWillSwipeNext: function(resolve, reject) {
                    var chapterId = data.chapter.nextId;
                    if (chapterId) {
                        self.getChapter(chapterId, false, resolve, reject);
                    } else {
                        reject();
                    }
                }
            });
        },
        jumpChapter: function(chapterId, isShowLoading, resolve, reject) {
            var self = this;
            if (!history.replaceState) {
                location.href = data.chapterUrl.replace("chapterId", chapterId);
            } else {
                self.getChapter(chapterId, isShowLoading, function(result) {
                    var chapter = result.data.chapterInfo;
                    data.chapter = data.scrollLastChapter = chapter;
                    if (chapter.nextId) {
                        $("#btnLoadNextChapter").show();
                        $("#aGotoBookEnd").hide();
                    } else {
                        $("#btnLoadNextChapter").hide();
                        $("#aGotoBookEnd").show();
                    }
                    $("#chapterTitle").html(chapter.title);
                    if(chapter.intro){
                        $("#chapterContent").html('<section class="read-section jsChapterWrapper" data-chapter-id="'+chapter.id+'"><h3>'+chapter.title+'</h3>'+chapter.content+'<div class="read-author-say jsAuthorSay"><div class="read-author-p">'+chapter.intro+'</div></div></section>');
                    }else{
                        $("#chapterContent").html('<section class="read-section jsChapterWrapper" data-chapter-id="'+chapter.id+'"><h3>'+chapter.title+'</h3>'+chapter.content+'</section>');
                    }
                    self.updateReadProgress();
                    history.replaceState(null, null, data.chapterUrl.replace("chapterId", chapterId));
                    if ($.isFunction(resolve)) {
                        resolve();
                    } else if (self.scrollReader.enabled) {
                        self.scrollReader.restart();
                    } else if (self.swipeReader.enabled) {
                        self.swipeReader.restart();
                    }
                }, reject || function() {});
            }
        },
        updateSettings: function(settings) {
            $.each(settings, function(key, value) {
                data.settingsStr = data.settingsStr.replace(new RegExp(key + ":[^|]+"), key + ":" + value);
            });
            var settingsStr = data.settingsStr.replace(/[^:|]+:/g, "");
            Cookie.set('reader_config_wap', settingsStr, 86400000 * 365, '/');
        },
        updateReadProgress: function() {
            if (isOffline()) {
                return;
            }
            // 更新阅读记录
            $.get("/home/chapter/updread", {
                novel_id: data.book.Id,
                id: book.chapter.source_id,
                key: data.chapter.id
            });
        },
        getChapter: function(chapterId, isShowLoading, resolve, reject) {
            var self = this;
            var layer_index;
            if (isOffline()) {
                $("#readLoadNext").css("opacity", 1);
            }
            if (data.chapters[chapterId]) {
                resolve({
                    data: {
                        bookInfo: data.book,
                        chapterInfo: data.chapters[chapterId]
                    }
                });
            } else {
                if (isOffline()) {
                    if (data.chapter.chapterId === data.lastOfflineChapterId) {
                        $("#readLoadNext").css("opacity", 0);
                        layer.open({content:'已显示全部离线章节',skin:'msg',time:2});
                    }
                    return reject();
                }
                if (isShowLoading) {
                    layer_index=layer.open({type: 2,content: '加载中'});
                }
                $.getJSON("/home/chapter/info", {id: book.chapter.source_id , key: chapterId}, function(result) {
                    if (isShowLoading) {
                        layer.close(layer_index);
                    }
                    if (result.code === 1) {
                        var chapter = {
                            id : result.data.chapter.id,
                            title:result.data.chapter.title,
                            vipStatus : 0,
                            content : result.data.chapter.content,
                            intro : result.data.chapter.intro,
                            prevId : result.data.chapter.prev.id,
                            nextId : result.data.chapter.next.id
                        };
                        data.chapters[chapter.id] = chapter;
                        data.book = {
                            "Id": result.data.id,
                            "Name": result.data.title,
                            "authorName": result.data.author
                        };
                        resolve({
                            data: {
                                bookInfo: data.book,
                                chapterInfo: chapter
                            }
                        });
                    } else {
                        if (isShowLoading) {
                            layer.open({content:'获取章节内容失败，请稍候再试',skin:'msg',time:2});
                        }
                        reject();
                    }
                });
            }
        },
        setReadChapter: function(chapterId) {
            var self = this;
            var chapter = data.chapters[chapterId];
            if (!chapter || chapter.id === data.chapter.id) {
                return;
            }
            data.chapter = chapter;
            $("#chapterTitle").html(chapter.title);
            self.updateReadProgress();
            history.replaceState(null, null, data.chapterUrl.replace("chapterId", chapterId));
        }
    }
    Book.init();
});