function ludou_getCookie( name ) {
	var start = document.cookie.indexOf( name + "=" );
	var len = start + name.length + 1;

	if ( ( !start ) && ( name != document.cookie.substring( 0, name.length ) ) )
		return null;

	if ( start == -1 )
		return null;

	var end = document.cookie.indexOf( ';', len );

	if ( end == -1 )
		end = document.cookie.length;
	return unescape( document.cookie.substring( len, end ) );
}

function ludou_isCookieEnable() {
	var today = new Date();
	today.setTime( today.getTime() );
	var expires_date = new Date( today.getTime() + (1000 * 60) );

	document.cookie = 'ludou_cookie_test=test;expires=' + expires_date.toGMTString() + ';path=/';
	var cookieEnable = (ludou_getCookie('ludou_cookie_test') == 'test') ?  true : false;
	document.cookie = 'ludou_cookie_test=;expires=Fri, 3 Aug 2001 20:47:11 UTC;path=/';
	return cookieEnable;
}

var ludou_xmlHttp = ludou_createXmlHttpRequestObject();
function ludou_createXmlHttpRequestObject() {
	var xmlHttp;
	try {
		xmlHttp = new XMLHttpRequest()
	}
	catch(e) {
		var XmlHttpVersions = new Array("MSXML2.XMlHTTP.6.0", "MSXML2.XMlHTTP.5.0", "MSXML2.XMlHTTP.4.0", "MSXML2.XMlHTTP.3.0", "MSXML2.XMlHTTP", "Microsoft.XMlHTTP");
		for (var i = 0; i < XmlHttpVersions.length && !xmlHttp; i++) {
			try {
				xmlHttp = new ActiveXObject(XmlHttpVersions[i])
			} catch(e) {}
		}
	}
	if (!xmlHttp) {} else {
		return xmlHttp
	}
}

/* 标记，防止重复提交 */
var ludou_token = 1;

function ludou_simple_vote(button, postid, fen) {
	if ( isNaN(postid) || isNaN(fen)) return;
	
	if( !ludou_isCookieEnable() ) {
		/*对于不支持COOKIE的客户端，禁止其投票*/
		alert("很抱歉，您不能给本文投票！");
		return;
	}
	
	if( ludou_getCookie( "ludou_simple_vote_" + postid ) != null) {
		alert("您已经给本文投过票了！");
		return;
	}
	
	if(ludou_token != 1) {
		alert("您的鼠标点得也太快了吧？！");
		return;
	}
	
	ludou_token = 0;
	var recommendv = document.getElementById("recommendv");
	recommendv.innerHTML = parseInt(recommendv.innerHTML) + fen;
	var parameters = "?action=ludousvote&id=" + postid + '&fen=' + fen + '&t=' + new Date().getTime();

	ludou_xmlHttp.open("GET", ludousvote.ajaxurl + parameters, true);
	ludou_xmlHttp.onreadystatechange = ludou_simple_vote_change;
	ludou_xmlHttp.send(null);
}

function ludou_simple_vote_change() {
	if (ludou_xmlHttp.readyState == 4) {
		if (ludou_xmlHttp.status == 200) {
			ludou_token = 1;
			return;
		}
	}
}