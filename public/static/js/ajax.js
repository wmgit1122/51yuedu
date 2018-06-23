   /**
     * ajax封装   
     * url 发送请求的地址
     * data 发送到服务器的数据，数组存储，如：{"date": new Date().getTime(), "state": 1}
     * async 默认值: true。默认设置下，所有请求均为异步请求。如果需要发送同步请求，请将此选项设置为 false。
     *       注意，同步请求将锁住浏览器，用户其它操作必须等待请求完成才可以执行。
     * type 请求方式("POST" 或 "GET")， 默认为 "GET"
     * dataType 预期服务器返回的数据类型，常用的如：xml、html、json、text
     * successfn 成功回调函数
     * errorfn 失败回调函数
     */
    jQuery.axjson=function(url, data, async, type, dataType, successfn, errorfn) {
        async = (async==null || async=="" || typeof(async)=="undefined")? "true" : async;
        type = (type==null || type=="" || typeof(type)=="undefined")? "post" : type;
        dataType = (dataType==null || dataType=="" || typeof(dataType)=="undefined")? "json" : dataType;
        data = (data==null || data=="" || typeof(data)=="undefined")? {"date": new Date().getTime()} : data;
        $.ajax({
            type: type,
            async: async,
            data: data,
            url: url,
            dataType: dataType,
            success: function(d){
                successfn(d);
            },
            error: function(e){
                errorfn(e);
            }
        });
    };
    
    /**
     * ajax封装
     * url 发送请求的地址
     * data 发送到服务器的数据，数组存储，如：{"date": new Date().getTime(), "state": 1}
     * successfn 成功回调函数
     */
    jQuery.axs=function(url, data, type, successfn) {
        var loading='<p class="loading"><span class="load_more text_gray">加载中...</span></p>';
         $("bady").append(loading);
        data = (data==null || data=="" || typeof(data)=="undefined")? {"date": new Date().getTime()} : data;
        $.ajax({
            type: "post",
            async:true,
            data: data,
            url: url,
            dataType: type,
            success: function(d){
                $(".loading").remove();
                successfn(d);
            }
        });
    };
    
    /**
     * ajax封装
     * url 发送请求的地址
     * data 发送到服务器的数据，数组存储，如：{"date": new Date().getTime(), "state": 1}
     * dataType 预期服务器返回的数据类型，常用的如：xml、html、json、text
     * successfn 成功回调函数
     * errorfn 失败回调函数
     */
    jQuery.axse=function(url, data, successfn, errorfn) {
        data = (data==null || data=="" || typeof(data)=="undefined")? {"date": new Date().getTime()} : data;
        $.ajax({
            type: "post",
            data: data,
            url: url,
            dataType: "json",
            success: function(d){
                successfn(d);
            },
            error: function(e){
                errorfn(e);
            }
        });
    };
	
	
function getParameter(name){
		var paramStr = location.search;
		if (paramStr.length == 0) return null;
		if (paramStr.charAt(0) != '?') return null;
		paramStr = unescape(paramStr);
		paramStr = paramStr.substring(1);
		if (paramStr.length == 0) return null;
		var params = paramStr.split('&');
		for (var i = 0; i < params.length; i++){
			var parts = params[i].split('=', 2);
			if (parts[0] == name){
				if (parts.length < 2 || typeof(parts[1]) == "undefined" || parts[1] == "undefined" || parts[1] == "null") return "";
				return parts[1];
			}
		}
		return null;
}
function getNowFormatDate(){
	var day = new Date();
	var Year = 0;
	var Month = 0;
	var Day = 0;
	var CurrentDate = "";
	Year= day.getFullYear();
	Month= day.getMonth()+1;
	Day = day.getDate();
	CurrentDate += Year + "-";
	if (Month >= 10 ){
		CurrentDate += Month + "-";
	}
	else{
		CurrentDate += "0" + Month + "-";
	}
	if (Day >= 10 ){
		CurrentDate += Day ;
	}
	else{
		CurrentDate += "0" + Day ;
	}
	return CurrentDate;
} 

Date.prototype.format = function(format) {
       var date = {
              "M+": this.getMonth() + 1,
              "d+": this.getDate(),
              "h+": this.getHours(),
              "m+": this.getMinutes(),
              "s+": this.getSeconds(),
              "q+": Math.floor((this.getMonth() + 3) / 3),
              "S+": this.getMilliseconds()
       };
       if (/(y+)/i.test(format)) {
              format = format.replace(RegExp.$1, (this.getFullYear() + '').substr(4 - RegExp.$1.length));
       }
       for (var k in date) {
              if (new RegExp("(" + k + ")").test(format)) {
                     format = format.replace(RegExp.$1, RegExp.$1.length == 1
                            ? date[k] : ("00" + date[k]).substr(("" + date[k]).length));
              }
       }
       return format;
}
