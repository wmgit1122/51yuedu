$(function () {
    try{FastClick.attach(document.body);}
    catch(ex){};
	
	//Delete History Search
	$(".search-md .del-icon").on("click", function () {
		$("#historySearch").empty();
		$.fn.cookie("historySearch", null, -1);
	});

	//Search
	$("#SearchShow").on("click", function() {
		$(".wrapper").hide();
		$(".search-md").show();
	});
	$("#CancelSearch").on("click", function() {
		$(".search-md").hide();
		$(".wrapper").show();
	});
	$("#SearchText").on("input", function() {
		if($(this).val() != "") {
			$("#CloseSearch").show();
		}else{
			$("#CloseSearch").hide();
		}
	});
	$("#CloseSearch").on("click", function() {
		$("#SearchText").val("");
		$(this).hide();
		$(".autocompleter-list").empty();
	});

	//Filter Show
	$(".filtershow").on("click", function(e) {
		$("body").append("<div class=\"mask-bg\"></div>");
		$(".filter-md").css({left:0});
	});
	$(".filter-md").on("click", function() {
		$(".filter-md").css({left:"100%"});
		$(".mask-bg").remove();
	});
	$(".filter-con .labels a, .ask-md .labels a").on("click", function() {
		$(this).siblings("a").removeClass("active");
		$(this).addClass("active");
		$(".ask-md .asktitle-text").val($(this).text());
	});
	$(".filter-btn .confirm-btn").on("click", function () {
		var booklistUrl = "/book/";
		$(".filter-con .labels").each(function (index) {
			if (index == 0)
			{
				$(this).find("a").each(function () {
					if ($(this).is(".active"))
					{
						booklistUrl += "tc" + $(this).attr("data-id") + "_";
					}
				});
			}
			else if (index == 1)
			{
				$(this).find("a").each(function () {
					if ($(this).is(".active")) {
						booklistUrl += "d" + $(this).attr("data-id") + "_u0_";
					}
				});
			}
			else if (index == 2) {
				$(this).find("a").each(function (index) {
					if ($(this).is(".active")) {
						booklistUrl += "ln" + index + "_";
					}
				});
			}
			else if (index == 3) {
				$(this).find("a").each(function () {
					if ($(this).is(".active")) {
						booklistUrl += "s" + $(this).attr("data-id") + "_op0_p0/";
					}
				});
			}
		});
		window.location.href = booklistUrl;
	});
	$(".filter-btn .reset-btn").on("click", function() {
		$(".filter-con .labels a").removeClass("active");
		$(".filter-con .labels a:first-child").addClass("active");
	});
	$(".filter-con").on("click", function(e) {
		e.stopPropagation();
	});
	
	//Recharge Amount
	$(".rech-amount li a").on("click", function() {
		$(this).parents("ul").find("a").removeClass("active");
		$(this).addClass("active");
	});

	//Book Intro
	var intro = 0;
	if($(".bk-intro-bd").height() <= 75) {
		$(".bk-intro-con").css({paddingBottom:10});
	}
	if($(".bk-intro-bd").height() > 75) {
		$(".bk-intro-toggle").show();
		$(".bk-intro-text").css({maxHeight:"none",height:75});
		$(".bk-intro-toggle a").on("click", function() {
			if(intro == 0) {
				$(".bk-intro-text").css({height:$(".bk-intro-bd").height()});
				$(this).parent().removeClass("gradient");
				$(this).html("收起<i class=\"icon arrow-icon open\"></i>");
				intro = 1;
			}else{
				$(".bk-intro-text").css({height:75});
				$(this).parent().addClass("gradient");
				$(this).html("展开<i class=\"icon arrow-icon\"></i>");
				intro = 0;
			}
		});
	}
	
	//Book Catalog
	var catalog = 0;
	$(".catalog-hd").on("click", function() {
		if(catalog == 0) {
			$(".catalog-con").slideDown();
			$(this).children(".arrow-icon").addClass("open");
			catalog = 1;
		}else{
			$(".catalog-con").slideUp();
			$(this).children(".arrow-icon").removeClass("open");
			catalog = 0;
		}
	});
	
	//Help Toggle
	var help = 0;
	$(".help-list li .item").each(function() {
	   if($(this).next().hasClass("help-con")){
		   $(this).click(function() {
			  if(help == 0) {
				  $(this).next(".help-con").show();
				  $(this).parent("li").addClass("open");
				  help = 1;
			  }else{
				  $(this).next(".help-con").hide();
				  $(this).parent("li").removeClass("open");
				  help = 0;
			  }
			});   
		}
	});
	
	//Gift Select
	$(".gift-select a").on("click", function () {
	    var vipMoney = parseInt($(".over-money .num").text());
	    var propsCount = $(".rw-select").val();
		$(this).parents("ul").find("a").removeClass("active");
		$(this).addClass("active");
		var bid = $("input[name='bid']").val();
		var neededMoney = 0;
		switch ($(this).parent().index())
		{
		    case 1: neededMoney = 500 * propsCount; $("form").attr("action", "/book/prop/" + bid + "/" + 2 + "/"); break;
		    case 2: neededMoney = 1000 * propsCount; $("form").attr("action", "/book/prop/" + bid + "/" + 3 + "/"); break;
		    case 3: neededMoney = 10000 * propsCount; $("form").attr("action", "/book/prop/" + bid + "/" + 4 + "/"); break;
		    case 4: neededMoney = 100000 * propsCount; $("form").attr("action", "/book/prop/" + bid + "/" + 5 + "/"); break;
		    default: neededMoney = 100 * propsCount; $("form").attr("action", "/book/prop/" + bid + "/" + 1 + "/"); break;
		}
		$(".use-money .num").text(neededMoney);
		if (vipMoney > neededMoney)
		{
		    if ($(".use-money .low").length > 0) { $(".use-money .low").remove(); }
		    propBtn(1);
		}
		else
		{
		    propBtn(0);
		    if ($(".use-money .low").length <= 0) {
		        $(".use-money").append('<i class="low">余额不足</i>'); 
		    }
		}
	});

	$(".rw-select").change(function () {
	    var vipMoney = parseInt($(".over-money .num").text());
	    var propsCount = $(this).val();
	    var neededMoney = 0;
	    switch ($(".gift-select a.active").parent().index()) {
	        case 1: neededMoney = 500 * propsCount; break;
	        case 2: neededMoney = 1000 * propsCount; break;
	        case 3: neededMoney = 10000 * propsCount; break;
	        case 4: neededMoney = 100000 * propsCount; break;
	        default: neededMoney = 100 * propsCount; break;
	    }
	    $(".use-money .num").text(neededMoney);
	    if (vipMoney > neededMoney) {
	        propBtn(1);
	        if ($(".use-money .low").length > 0) { $(".use-money .low").remove();  }
	    }
	    else {
	        propBtn(0);
	        if ($(".use-money .low").length <= 0) {
	            $(".use-money").append('<i class="low">余额不足</i>');
	        }
	    }
	});
	
	//Side Spen
	$(".sidespen .close-icon").on("click", function() {
		$(this).parents(".sidespen").remove();
	});

	var lastReplyIndex = -1;
	
	//Reply Modal
	$(".comment-item .reply-num").on("click", function () {
	    if (!check_login()) { return false; }
	    $("body").append("<div class=\"mask-bg modal-mask-bg\"></div>");
	    var username = $(this).parent().parent(".comment-item").find(".user-name").text();
	    if ($(".reply-md").length == 0) {
	        var Reply = '<div class="modal reply-md"><div class="modal-hd">';
	        Reply += '<span class="cancel-btn">取消</span><input type="submit" value="回复" class="reply-btn submit"></div>';
	        Reply += '<div class="modal-con"><textarea placeholder="输入你对本书的看法…" rows="5" class="form-text reply-text"></textarea>';
	        Reply += '</div></div>';
	        $("body").append(Reply);
	    }
	    if (lastReplyIndex != $(this).parent().parent(".comment-item").index()) {
	        lastReplyIndex = $(this).parent().parent(".comment-item").index();
	        $(".reply-text").attr("placeholder", "@ " + username);
	        $(".reply-text").val('');
	    }
		setTimeout(function(){
			$(".reply-md").addClass("show-md").css({transform:"translateY(0)"});
		},50);
	});
	
    //Correct Modal
	$(".article-set .correct").on("click", function () {
	    $("body").append("<div class=\"mask-bg modal-mask-bg\"></div>");
	    if ($(".correct-md").length == 0) {
	        var msgSubmit = '<div class="modal correct-md">';
	        msgSubmit += '<div class="modal-hd"><h3>纠错</h3><a href="javascript:void(0);" class="icon close-icon"></a></div>';
	        msgSubmit += '<div class="modal-con"><div class="clearfix labels"><a href="javascript:void(0);" class="active">章节内容错误</a><a href="javascript:void(0);">错别字较多</a><a href="javascript:void(0);">低俗情色</a></div>';
	        msgSubmit += '<div class="text-box"><textarea placeholder="补充说明…" rows="3" class="form-text correct-text"></textarea></div></div>';
	        msgSubmit += '<div class="modal-ft"><a href="javascript:void(0)" class="btn cancel-btn">取消</a><a href="javascript:void(0)" class="btn confirm-btn error">提交</a></div>';
	        msgSubmit += '</div>';
	        $("body").append(msgSubmit);
	    }
		setTimeout(function(){
			$(".correct-md").addClass("show-md").css({transform:"translateY(0)"});
		},50);
	});
	
	$("body").on("click", ".correct-md .labels a", function() {
		$(this).siblings("a").removeClass("active");
		$(this).addClass("active");
	});
	
	//ask Modal
	$(".ask-link").on("click", function() {
		$("body").append("<div class=\"mask-bg modal-mask-bg\"></div>");
		$(".ask-md").addClass("show-md").css({transform:"translateY(0)"});
	});
	
	//remove modal
	$("body").on("click", ".modal-mask-bg", function() {
	    $(".show-md").css({transform:"translateY(100%)"}).removeClass("show-md");
	    $(".modal-mask-bg").remove();
	});	
	$("body").on("click", ".modal .cancel-btn, .modal .close-icon", function () {
	    $(this).parents(".modal").css({transform:"translateY(100%)"}).removeClass("show-md");
	    $(".modal-mask-bg").remove();
	});
	
    //Reply submit
	$("body").on("click", ".modal .submit", function () {
	    if ($(".reply-text").val().length == 0) {
	        alertMsg('fail-icon', "回复内容不能为空！");
	        return;
	    }
	    var tid = $(".comment-list article:eq(" + lastReplyIndex + ")").attr("id");
	    $.ajax({
	        url: "/api/add_Reply",
	        type: "post",
	        data: "bid=" + userObj.bid + "&tid=" + tid + "&content=" + encodeURI($(".reply-text").val()),
	        success: function (result) {
	            if (result.Success) {
	                var ReplyHtml = '<li><div class="reply-con"><span class="user">' + userObj.name + "：</span>" + $(".reply-text").val() + "</div>";
	                ReplyHtml += '<div class="reply-time">刚刚</div></li>';
	                var ReplyList = $(".comment-list article:eq(" + lastReplyIndex + ")").find("ul.reply-list");
	                var ReplyCount = 0;
	                if (ReplyList.length > 0) {
	                    ReplyCount = parseInt($(".comment-list article:eq(" + lastReplyIndex + ")").find(".reply-num").text());
	                    $(ReplyList).prepend(ReplyHtml);
	                }
	                else {
	                    $(".comment-list article:eq(" + lastReplyIndex + ")").find(".comment-con").after('<ul class="reply-list">' + ReplyHtml + '</ul>');
	                }
	                ReplyCount++;
	                $(".comment-list article:eq(" + lastReplyIndex + ")").find(".reply-num").html('<i class=\"icon reply-icon\"></i>' + ReplyCount);
	                alertMsg('success-icon', "回复成功！");
	            }
	            else { alertMsg("fail-icon", result.Message.Content); }
	        }
	    });
	    
	    $(this).parents(".modal").css({transform:"translateY(100%)"}).removeClass("show-md");
	    $(".modal-mask-bg").remove();
	});
	
    //error sumit
	$("body").on("click", ".modal .error", function () {
	    var title = document.title.replace("_小说520", "");
	    $.ajax({
	        url: "/api/sendSys",
	        type: "post",
	        data: "title=" + encodeURI(title + "_" + $(".labels .active").text()) + "&content=" + encodeURI($(".correct-text").val()),
	        success: function (result) {
	            if (result.Success) {
	                alertMsg('success-icon', "<p>提交成功！</p><p>感谢您的支持，我们会尽快检查校队！</p>");
	            }
	            else { alertMsg("fail-icon", result.Message.Content); }
	        }
	    });

	    $(this).parents(".modal").css({transform:"translateY(100%)"}).removeClass("show-md");
	    $(".modal-mask-bg").remove();
	});
	
    //login out
	$("#loginOut").on("click", function () {
	    alertMsg("loading-icon", "");
	    $.ajax({
	        url: "/api/loginout",
	        type: "post",
	        success: function (result) {
	            if (result.Success) {
	                if (result.Message.Content == '0')
	                    window.location.reload();
	                else
	                    window.location = '/';
	            }
	            else { alertMsg("fail-icon", result.Message.Content); }
	        }
	    });
	});
	
    //bookcase del
	$(".read-list .delete-btn").on("click", function () {
	    var order = $(".bkshelf-tab a.active").index();
	    var del_bid = $(this).attr('data-bid');
	    var bookcaseObj = $(this).parent();
	    if (order == 1) {
	        var userbookCount = parseInt($(".cl-infos .num:eq(0)").html()) - 1;
	        dialogMsg("删除提醒", "您确定要删除藏书记录吗？", function () {
	            $.ajax({
	                url: "/api/delmark",
	                type: "post",
	                data: "bid=" + del_bid,
	                success: function (result) {
	                    if (result.Success) {
	                        if ($(".read-list li").length == 1 && getUrlParam("page") > 1) {
	                            location.href = '/user/bookcase/?order=1&page=' + (parseInt(getUrlParam("page")) - 1).toString();
	                        }
	                        else {
	                            $(".cl-infos .num:eq(0)").html(userbookCount);
	                            $(bookcaseObj).remove();
	                        }
	                    }
	                    else { alertMsg("fail-icon", result.Message.Content); }
	                }
	            });
	        });
	    }
	    else {
	        $.ajax({
	            url: "/api/delTempMark",
	            type: "post",
	            data: "bid=" + del_bid,
	            success: function (result) {
	                if (result.Success) {
	                    alertMsg("success-icon", "移除成功！");
	                    $(bookcaseObj).remove();
	                }
	                else { alertMsg("fail-icon", result.Error); }
	            }
	        });
	    }
	});
	
    //Delete autobuy
	$(".autosubs-list .del-icon").on("click", function () {
	    var bid = $(this).parent().find(".chapter").attr("data-bid");
	    var li = $(this).parent();
	    dialogMsg("删除提醒", "您确定要删除自动订阅吗？", function () {
	        $.ajax({
	            url: "/api/delAutoBuy",
	            type: "post",
	            data: "bid=" + bid,
	            success: function (result) {
	                if (result.Success) {
	                    if ($(".autosubs-list li").length == 1 && getUrlParam("page") > 1) {
	                        location.href = '/user/autobuy/?fl=1&page=' + (parseInt(getUrlParam("page")) - 1).toString();
	                    }
	                    else {
	                        $(li).remove();
	                    }
	                }
	                else { alertMsg("fail-icon", result.Message.Content); }
	            }
	        });
	    });
	});
	
	//Add book mark
	$(".add-bkmark").on("click", function() {
	    $.ajax({
	        url: "/api/addmark",
	        type: "post",
	        data: "bid=" + userObj.bid + "&cid=" + userObj.cid,
	        success: function (result) {
	            if (result.Success) {
	                alertMsg("", "成功加入收藏");
	                $(".collect-tips").remove();
	            }
	            else { alertMsg("fail-icon", result.Error); }
	        }
	    });
	});
	
	//Set Cookie
	$.fn.cookie = function (name, value, options) {
		if(typeof value != 'undefined') { // name and value given, set cookie 
			options = options || {};
			if(value === null) {
				value = '';
				options.expires = -1;
			}
			var expires = '';
			if(options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
				var date;
				if(typeof options.expires == 'number') {
					date = new Date();
					date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
				}else{
					date = options.expires;
				}
				expires = '; expires=' + date.toGMTString(); // use expires attribute, max-age is not supported by IE 
			}
			var path = options.path ? '; path=' + options.path : '';
			var domain = options.domain ? '; domain=' + options.domain : '';
			var secure = options.secure ? '; secure' : '';
			document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure, ';'].join('');
		}else{ // only name given, get cookie 
			var cookieValue = null;
			if(document.cookie && document.cookie != '') {
				var cookies = document.cookie.split(';');
				for (var i = 0; i < cookies.length; i++) {
					var cookie = $.trim(cookies[i]);
					// Does this cookie string begin with the name we want? 
					if(cookie.substring(0, name.length + 1) == (name + '=')) {
						cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
						break;
					}
				}
			}
			return cookieValue;
		}
	};
	var isHide = !!$.fn.cookie("AppDownload")
	var isNight = !!$.fn.cookie("LightChange");
	var chapterView = $(".article-con");
	var pageContent = $(".article-bd"), saveFont = $.fn.cookie("CurrentFont"), currentFont = 0;
	var font = function () {
		//Font Size;
		var sizes = ["font-normal", "font-large", "font-xlarge", "font-xxlarge", "font-xxxlarge"],
			level = sizes.length;
		return {
			set: function (c) {
				//console.log(c);
				//console.log(sizes[c]);
				pageContent.removeClass().addClass("article-bd" + " " + sizes[c]);
				currentFont = c;
				$.fn.cookie("CurrentFont", c, { expires: 3600, path: "/" });
				$.fn.cookie("currentFontString", sizes[c], { expires: 3600, path: "/" });
			},
			increase: function () {
				if(currentFont < level - 1) {
					this.set(currentFont + 1);
				}
			},
			descrease: function () {
				if(currentFont > 0) {
					this.set(currentFont - 1);
				}
			},
			day: function () {
				isNight = false;
				chapterView.removeClass("turnoff");
				$(".sun-icon").removeClass().addClass("icon moon-icon");
				$.fn.cookie("LightChange", false, { expires: -1, path: "/" });
			},
			night: function () {
				isNight = true;
				chapterView.addClass("turnoff");
				$(".moon-icon").removeClass().addClass("icon sun-icon");
				$.fn.cookie("LightChange", true, { expires: 3600, path: "/" });
			},
			appshow: function() {
				isHide = false;
				$('.appdownload').show();
				$.fn.cookie("AppDownload", true, { expires: -1, path: "/" });
			},
			apphide: function() {
				isHide = true;
				$('.appdownload').hide();
				$.fn.cookie("AppDownload", true, { expires: 3600, path: "/" });
			}
		};
	}();
	if(typeof saveFont !== "undefined") {
		if(saveFont == null) {
			saveFont = 0;
		}
		font.set(saveFont * 1);
	}
	if(isNight) {
		font.night();
	}
	if(!isHide) {
		font.appshow();
	}
	$(".article-set .a-plus").on("click", function() {
		font.increase();
	});
	$(".article-set .a-minus").on("click", function() {
		font.descrease();
	});
	$(".article-set .pattern").on("click", function() {
		if(isNight) {
			font.day();
		}else{
			font.night();
		}
	});
	$(".appdownload .close").on("click", function() {
		if(isHide) {
			font.appshow();
		}else{
			font.apphide();
		}
	});
});

//dialog modal
function dialogMsg(msgtitle, msgtext,fn) {
	var dialogMsgHtml = "";
		dialogMsgHtml += "<div class=\"modal dialog-msg\">\n";
		dialogMsgHtml += "	<div class=\"modal-hd\">\n";
		dialogMsgHtml += "		<h3><\/h3>\n";
		dialogMsgHtml += "		<a href=\"javascript:void(0)\" class=\"icon close-icon\"><\/a>\n";
		dialogMsgHtml += "	<\/div>\n";
		dialogMsgHtml += "	<div class=\"modal-con\"><\/div>\n";
		dialogMsgHtml += "	<div class=\"modal-ft\">\n";
		dialogMsgHtml += "		<span class=\"btn cancel-btn\">取消<\/span>\n";
		dialogMsgHtml += "		<span class=\"btn confirm-btn\">确定<\/span>\n";
		dialogMsgHtml += "	<\/div>\n";
		dialogMsgHtml += "<\/div>\n";
	$("body").append(dialogMsgHtml + "<div class=\"mask-bg\"></div>");
	$(".dialog-msg .modal-hd h3").append(msgtitle);
	$(".dialog-msg .modal-con").append(msgtext);
	setTimeout(function(){
		$(".dialog-msg").css({transform:"translateY(0)"});
	},50);
	$(".mask-bg").on("click", function() {
		$(".dialog-msg").css({transform:"translateY(100%)"});
		setTimeout(function(){
			$(".dialog-msg").remove();
			$(".mask-bg").remove();
		},500);
	});
	$(".dialog-msg .confirm-btn").on("click", function () {
	    fn();
	});
	$(".dialog-msg .close-icon, .dialog-msg .cancel-btn, .dialog-msg .confirm-btn").on("click", function () {
		$(this).parents(".dialog-msg").css({transform:"translateY(100%)"});
		setTimeout(function(){
			$(".dialog-msg").remove();
			$(".mask-bg").remove();
		},500);
	});
}

//url para
function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); 
    var r = window.location.search.substr(1).match(reg);  
    if (r != null) return unescape(r[2]); return null; 
}

//alert message
function alertMsg(msgicon, msgtext) {
	var alertMsgHtml = "";
		alertMsgHtml += "<div class=\"alert-msg\">\n";
		alertMsgHtml += "	<div class=\"icon alert-icon\"><\/div>\n";
		alertMsgHtml += "	<div class=\"alert-text\"><\/div>\n";
		alertMsgHtml += "<\/div>\n";
	if($(".alert-msg").length == 0) {
		$("body").append(alertMsgHtml);
		$(".alert-msg .alert-text").append(msgtext);
		if(msgicon == "") {
			$(".alert-msg .alert-icon").remove();
		}else{
			$(".alert-msg .alert-icon").addClass(msgicon);
		}
	}
	setTimeout(function() {
		$(".alert-msg").remove();
	},3000);
}

function propBtn(show)
{
    if (show == 1) {
        
        if ($(".btn-box input.confirm-btn").length == 0) {  $(".btn-box").prepend('<input type="submit" value="确定捧场" class="form-btn confirm-btn">'); }
        if ($(".btn-box a.confirm-btn").length > 0) { $(".btn-box a.confirm-btn").remove(); }
    }
    else {
        if ($(".btn-box input.confirm-btn").length > 0) { $(".btn-box input.confirm-btn").remove(); }
        if ($(".btn-box a.confirm-btn").length == 0) { $(".btn-box").prepend('<a href="/pay/" class="form-btn confirm-btn">充值</a>'); }
    }
}

function check_login()
{
    if (userObj.id == 0)
    {
        if ($(".login-md").length == 0) {
            var loginHtml = '<div class="modal login-md"><div class="modal-hd">';
            loginHtml += '<h3>账号登录</h3><a href="javascript:void(0);" class="icon close-icon"></a></div>';
            loginHtml += '<div class="modal-con"><div class="acc-form"><form action="" onsubmit="return login()" method="post">';
            loginHtml += '<div class="form-item text-box"><i class="icon acc-icon"></i><input type="text" placeholder="请输入您的账号/手机号" value="" name="username" class="form-text"></div>';
            loginHtml += '<div class="form-item text-box"><i class="icon pw-icon"></i><input type="password" placeholder="请输入您的密码" value="" name="userpass" class="form-text"></div><input type="hidden" name="action" value="login" />';
            loginHtml += '<div class="form-item btn-box"><input type="submit" value="立即登录" class="form-btn"></div>';
            loginHtml += '</form></div>';
            loginHtml += '<div class="acc-hd"><h3>小说520账号登录</h3></div>';
            loginHtml += '<div class="other-acc"><a href="http://oauth.xiaoshuo520.com/OAuth/go.aspx?oType=4"><i class="icon wx-icon"></i><span>微信</span></a>';
            loginHtml += '<a href="http://oauth.xiaoshuo520.com/OAuth/go.aspx"><i class="icon qq-icon"></i><span>QQ</span></a>';
            loginHtml += '<a href="http://oauth.xiaoshuo520.com/OAuth/go.aspx?oType=2"><i class="icon wb-icon"></i><span>微博</span></a>';
            loginHtml += '</div></div></div>';
            $("body").append(loginHtml);
        }
        $("body").append("<div class=\"mask-bg modal-mask-bg\"></div>");
		setTimeout(function(){
			$(".login-md").addClass("show-md").css({transform:"translateY(0)"});
		},50);
        return false;
    }
    return true;
}

//Send Mobile Code
function SendCode(obj) {
    var reg = /^1[3|4|5|6|7|8][0-9]\d{4,8}$/;
    if ($(".tel").val().length == 0 || !reg.exec($(".tel").val()))
    {
        alertMsg("warning-icon", "请填写正确的手机号码");
        return false;
    }
    $.ajax({
        url: "/api/sendCode",
        type: "post",
        data: "tel=" + $(".tel").val() + "&iType=1&oType=" + getUrlParam("oType"),
        success: function (result) {
            if (result.Success) {
                alertMsg("success-icon", "验证码已成功发送");
                $("input[name='id']").val(result.data.id);
                var countdown = 60;
                settime(obj);
                function settime(obj) {
                    if (countdown == 0) {
                        $(obj).removeClass("countdown").attr("disabled", false).val("重新获取验证码");
                        countdown = 60;
                        return;
                    } else {
                        $(obj).addClass("countdown").attr("disabled", true).val(countdown + "s");
                        countdown--;
                    }
                    setTimeout(function () {
                        settime(obj);
                    }, 1000)
                }
            }
            else { alertMsg("fail-icon", result.Message.Content); }
        }
    });
}

//login
function login()
{
    var username = $("input[name='username']").val();
    var userpass = $("input[name='userpass']").val();
    if (username.length == 0) {
        alertMsg("fail-icon", "请填写登录账号!"); return false;
    }
    if (userpass.length == 0) {
        alertMsg("fail-icon", "请填写登录密码!"); return false;
    }
    $.ajax({
        url: "/api/login",
        type: "post",
        data: "username=" + encodeURI(username) + "&userpass=" + encodeURI(userpass),
        success: function (result) {
            if (result.Success) {
                alertMsg("success-icon", "登录成功！");
                window.location.reload();
            }
            else { alertMsg("fail-icon", result.Message.Content); }
        }
    });
    return false;
}

//+1
(function($) {
	$.extend({
		tipsBox: function(options) {
			options = $.extend({
				obj: null,
				str: "+1",
				startSize: "12px",
				endSize: "30px",
				interval: 600,
				color: "#e60000",
				weight: "normal",
				callback: function() {}
			}, options);
			$("body").append("<span id=\"PlusNum\">"+ options.str +"</span>");
			var box = $("#PlusNum");
			var left = options.obj.offset().left + options.obj.width() / 2;
			var top = options.obj.offset().top - box.height() - 10;
			box.css({
				"position": "absolute",
				"left": left + "px",
				"top": top + "px",
				"z-index": 9999,
				"font-size": options.startSize,
				"line-height": options.endSize,
				"color": options.color,
				"font-weight": options.weight
			});
			box.animate({
				"font-size": options.endSize,
				"opacity": "0",
				"top": top - parseInt(options.endSize) + "px"
			}, options.interval , function() {
				box.remove();
				options.callback();
			});
		}
	});
})(jQuery);

//Fast Click
;(function() {
	'use strict';
	function FastClick(layer, options) {
		var oldOnClick;
		options = options || {};
		this.trackingClick = false;
		this.trackingClickStart = 0;
		this.targetElement = null;
		this.touchStartX = 0;
		this.touchStartY = 0;
		this.lastTouchIdentifier = 0;
		this.touchBoundary = options.touchBoundary || 10;
		this.layer = layer;
		this.tapDelay = options.tapDelay || 200;
		this.tapTimeout = options.tapTimeout || 700;
		if(FastClick.notNeeded(layer)) {
			return;
		}
		function bind(method, context) {
			return function() { return method.apply(context, arguments);};
		}
		var methods = ['onMouse', 'onClick', 'onTouchStart', 'onTouchMove', 'onTouchEnd', 'onTouchCancel'];
		var context = this;
		for (var i = 0, l = methods.length; i < l; i++) {
			context[methods[i]] = bind(context[methods[i]], context);
		}
		if(deviceIsAndroid) {
			layer.addEventListener('mouseover', this.onMouse, true);
			layer.addEventListener('mousedown', this.onMouse, true);
			layer.addEventListener('mouseup', this.onMouse, true);
		}
		layer.addEventListener('click', this.onClick, true);
		layer.addEventListener('touchstart', this.onTouchStart, false);
		layer.addEventListener('touchmove', this.onTouchMove, false);
		layer.addEventListener('touchend', this.onTouchEnd, false);
		layer.addEventListener('touchcancel', this.onTouchCancel, false);
		if(!Event.prototype.stopImmediatePropagation) {
			layer.removeEventListener = function(type, callback, capture) {
				var rmv = Node.prototype.removeEventListener;
				if(type === 'click') {
					rmv.call(layer, type, callback.hijacked || callback, capture);
				}else{
					rmv.call(layer, type, callback, capture);
				}
			};
			layer.addEventListener = function(type, callback, capture) {
				var adv = Node.prototype.addEventListener;
				if(type === 'click') {
					adv.call(layer, type, callback.hijacked || (callback.hijacked = function(event) {
						if(!event.propagationStopped) {
							callback(event);
						}
					}), capture);
				}else{
					adv.call(layer, type, callback, capture);
				}
			};
		}
		if(typeof layer.onclick === 'function') {
			oldOnClick = layer.onclick;
			layer.addEventListener('click', function(event) {
				oldOnClick(event);
			}, false);
			layer.onclick = null;
		}
	}
	var deviceIsWindowsPhone = navigator.userAgent.indexOf("Windows Phone") >= 0;
	var deviceIsAndroid = navigator.userAgent.indexOf('Android') > 0 && !deviceIsWindowsPhone;
	var deviceIsIOS = /iP(ad|hone|od)/.test(navigator.userAgent) && !deviceIsWindowsPhone;
	var deviceIsIOS4 = deviceIsIOS && (/OS 4_\d(_\d)?/).test(navigator.userAgent);
	var deviceIsIOSWithBadTarget = deviceIsIOS && (/OS [6-7]_\d/).test(navigator.userAgent);
	var deviceIsBlackBerry10 = navigator.userAgent.indexOf('BB10') > 0;
	FastClick.prototype.needsClick = function(target) {
		switch (target.nodeName.toLowerCase()) {
		case 'button':
		case 'select':
		case 'textarea':
			if(target.disabled) {
				return true;
			}
			break;
		case 'input':
			if((deviceIsIOS && target.type === 'file') || target.disabled) {
				return true;
			}
			break;
		case 'label':
		case 'iframe':
		case 'video':
			return true;
		}
		return (/\bneedsclick\b/).test(target.className);
	};
	FastClick.prototype.needsFocus = function(target) {
		switch (target.nodeName.toLowerCase()) {
		case 'textarea':
			return true;
		case 'select':
			return !deviceIsAndroid;
		case 'input':
			switch (target.type) {
			case 'button':
			case 'checkbox':
			case 'file':
			case 'image':
			case 'radio':
			case 'submit':
				return false;
			}
			return !target.disabled && !target.readOnly;
		default:
			return (/\bneedsfocus\b/).test(target.className);
		}
	};
	FastClick.prototype.sendClick = function(targetElement, event) {
		var clickEvent, touch;
		if(document.activeElement && document.activeElement !== targetElement) {
			document.activeElement.blur();
		}
		touch = event.changedTouches[0];
		clickEvent = document.createEvent('MouseEvents');
		clickEvent.initMouseEvent(this.determineEventType(targetElement), true, true, window, 1, touch.screenX, touch.screenY, touch.clientX, touch.clientY, false, false, false, false, 0, null);
		clickEvent.forwardedTouchEvent = true;
		targetElement.dispatchEvent(clickEvent);
	};
	FastClick.prototype.determineEventType = function(targetElement) {
		if(deviceIsAndroid && targetElement.tagName.toLowerCase() === 'select') {
			return 'mousedown';
		}
		return 'click';
	};
	FastClick.prototype.focus = function(targetElement) {
		var length;
		if(deviceIsIOS && targetElement.setSelectionRange && targetElement.type.indexOf('date') !== 0 && targetElement.type !== 'time' && targetElement.type !== 'month') {
			length = targetElement.value.length;
			targetElement.setSelectionRange(length, length);
		}else{
			targetElement.focus();
		}
	};
	FastClick.prototype.updateScrollParent = function(targetElement) {
		var scrollParent, parentElement;
		scrollParent = targetElement.fastClickScrollParent;
		if(!scrollParent || !scrollParent.contains(targetElement)) {
			parentElement = targetElement;
			do {
				if(parentElement.scrollHeight > parentElement.offsetHeight) {
					scrollParent = parentElement;
					targetElement.fastClickScrollParent = parentElement;
					break;
				}
				parentElement = parentElement.parentElement;
			} while (parentElement);
		}
		if(scrollParent) {
			scrollParent.fastClickLastScrollTop = scrollParent.scrollTop;
		}
	};
	FastClick.prototype.getTargetElementFromEventTarget = function(eventTarget) {
		if(eventTarget.nodeType === Node.TEXT_NODE) {
			return eventTarget.parentNode;
		}
		return eventTarget;
	};
	FastClick.prototype.onTouchStart = function(event) {
		var targetElement, touch, selection;
		if(event.targetTouches.length > 1) {
			return true;
		}
		targetElement = this.getTargetElementFromEventTarget(event.target);
		touch = event.targetTouches[0];
		if(deviceIsIOS) {
			selection = window.getSelection();
			if(selection.rangeCount && !selection.isCollapsed) {
				return true;
			}
			if(!deviceIsIOS4) {
				if(touch.identifier && touch.identifier === this.lastTouchIdentifier) {
					event.preventDefault();
					return false;
				}
				this.lastTouchIdentifier = touch.identifier;
				this.updateScrollParent(targetElement);
			}
		}
		this.trackingClick = true;
		this.trackingClickStart = event.timeStamp;
		this.targetElement = targetElement;
		this.touchStartX = touch.pageX;
		this.touchStartY = touch.pageY;
		if((event.timeStamp - this.lastClickTime) < this.tapDelay) {
			event.preventDefault();
		}
		return true;
	};
	FastClick.prototype.touchHasMoved = function(event) {
		var touch = event.changedTouches[0], boundary = this.touchBoundary;
		if(Math.abs(touch.pageX - this.touchStartX) > boundary || Math.abs(touch.pageY - this.touchStartY) > boundary) {
			return true;
		}
		return false;
	};
	FastClick.prototype.onTouchMove = function(event) {
		if(!this.trackingClick) {
			return true;
		}
		if(this.targetElement !== this.getTargetElementFromEventTarget(event.target) || this.touchHasMoved(event)) {
			this.trackingClick = false;
			this.targetElement = null;
		}
		return true;
	};
	FastClick.prototype.findControl = function(labelElement) {
		if(labelElement.control !== undefined) {
			return labelElement.control;
		}
		if(labelElement.htmlFor) {
			return document.getElementById(labelElement.htmlFor);
		}
		return labelElement.querySelector('button, input:not([type=hidden]), keygen, meter, output, progress, select, textarea');
	};
	FastClick.prototype.onTouchEnd = function(event) {
		var forElement, trackingClickStart, targetTagName, scrollParent, touch, targetElement = this.targetElement;
		if(!this.trackingClick) {
			return true;
		}
		if((event.timeStamp - this.lastClickTime) < this.tapDelay) {
			this.cancelNextClick = true;
			return true;
		}
		if((event.timeStamp - this.trackingClickStart) > this.tapTimeout) {
			return true;
		}
		this.cancelNextClick = false;
		this.lastClickTime = event.timeStamp;
		trackingClickStart = this.trackingClickStart;
		this.trackingClick = false;
		this.trackingClickStart = 0;
		if(deviceIsIOSWithBadTarget) {
			touch = event.changedTouches[0];
			targetElement = document.elementFromPoint(touch.pageX - window.pageXOffset, touch.pageY - window.pageYOffset) || targetElement;
			targetElement.fastClickScrollParent = this.targetElement.fastClickScrollParent;
		}
		targetTagName = targetElement.tagName.toLowerCase();
		if(targetTagName === 'label') {
			forElement = this.findControl(targetElement);
			if(forElement) {
				this.focus(targetElement);
				if(deviceIsAndroid) {
					return false;
				}
				targetElement = forElement;
			}
		}else if(this.needsFocus(targetElement)) {
			if((event.timeStamp - trackingClickStart) > 100 || (deviceIsIOS && window.top !== window && targetTagName === 'input')) {
				this.targetElement = null;
				return false;
			}
			this.focus(targetElement);
			this.sendClick(targetElement, event);
			if(!deviceIsIOS || targetTagName !== 'select') {
				this.targetElement = null;
				event.preventDefault();
			}
			return false;
		}
		if(deviceIsIOS && !deviceIsIOS4) {
			scrollParent = targetElement.fastClickScrollParent;
			if(scrollParent && scrollParent.fastClickLastScrollTop !== scrollParent.scrollTop) {
				return true;
			}
		}
		if(!this.needsClick(targetElement)) {
			event.preventDefault();
			this.sendClick(targetElement, event);
		}
		return false;
	};
	FastClick.prototype.onTouchCancel = function() {
		this.trackingClick = false;
		this.targetElement = null;
	};
	FastClick.prototype.onMouse = function(event) {
		if(!this.targetElement) {
			return true;
		}
		if(event.forwardedTouchEvent) {
			return true;
		}
		if(!event.cancelable) {
			return true;
		}
		if(!this.needsClick(this.targetElement) || this.cancelNextClick) {
			if(event.stopImmediatePropagation) {
				event.stopImmediatePropagation();
			}else{
				event.propagationStopped = true;
			}
			event.stopPropagation();
			event.preventDefault();
			return false;
		}
		return true;
	};
	FastClick.prototype.onClick = function(event) {
		var permitted;
		if(this.trackingClick) {
			this.targetElement = null;
			this.trackingClick = false;
			return true;
		}
		if(event.target.type === 'submit' && event.detail === 0) {
			return true;
		}
		permitted = this.onMouse(event);
		if(!permitted) {
			this.targetElement = null;
		}
		return permitted;
	};
	FastClick.prototype.destroy = function() {
		var layer = this.layer;
		if(deviceIsAndroid) {
			layer.removeEventListener('mouseover', this.onMouse, true);
			layer.removeEventListener('mousedown', this.onMouse, true);
			layer.removeEventListener('mouseup', this.onMouse, true);
		}
		layer.removeEventListener('click', this.onClick, true);
		layer.removeEventListener('touchstart', this.onTouchStart, false);
		layer.removeEventListener('touchmove', this.onTouchMove, false);
		layer.removeEventListener('touchend', this.onTouchEnd, false);
		layer.removeEventListener('touchcancel', this.onTouchCancel, false);
	};
	FastClick.notNeeded = function(layer) {
		var metaViewport;
		var chromeVersion;
		var blackberryVersion;
		var firefoxVersion;
		if(typeof window.ontouchstart === 'undefined') {
			return true;
		}
		chromeVersion = +(/Chrome\/([0-9]+)/.exec(navigator.userAgent) || [,0])[1];
		if(chromeVersion) {
			if(deviceIsAndroid) {
				metaViewport = document.querySelector('meta[name=viewport]');
				if(metaViewport) {
					if(metaViewport.content.indexOf('user-scalable=no') !== -1) {
						return true;
					}
					if(chromeVersion > 31 && document.documentElement.scrollWidth <= window.outerWidth) {
						return true;
					}
				}
			}else{
				return true;
			}
		}
		if(deviceIsBlackBerry10) {
			blackberryVersion = navigator.userAgent.match(/Version\/([0-9]*)\.([0-9]*)/);
			if(blackberryVersion[1] >= 10 && blackberryVersion[2] >= 3) {
				metaViewport = document.querySelector('meta[name=viewport]');
				if(metaViewport) {
					if(metaViewport.content.indexOf('user-scalable=no') !== -1) {
						return true;
					}
					if(document.documentElement.scrollWidth <= window.outerWidth) {
						return true;
					}
				}
			}
		}
		if(layer.style.msTouchAction === 'none' || layer.style.touchAction === 'manipulation') {
			return true;
		}
		firefoxVersion = +(/Firefox\/([0-9]+)/.exec(navigator.userAgent) || [,0])[1];
		if(firefoxVersion >= 27) {
			metaViewport = document.querySelector('meta[name=viewport]');
			if(metaViewport && (metaViewport.content.indexOf('user-scalable=no') !== -1 || document.documentElement.scrollWidth <= window.outerWidth)) {
				return true;
			}
		}
		if(layer.style.touchAction === 'none' || layer.style.touchAction === 'manipulation') {
			return true;
		}
		return false;
	};
	FastClick.attach = function(layer, options) {
		return new FastClick(layer, options);
	};
	if(typeof define === 'function' && typeof define.amd === 'object' && define.amd) {
		define(function() {
			return FastClick;
		});
	}else if(typeof module !== 'undefined' && module.exports) {
		module.exports = FastClick.attach;
		module.exports.FastClick = FastClick;
	}else{
		window.FastClick = FastClick;
	}
}());