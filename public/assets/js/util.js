var Util = {
    zen2han: function(e)
    {
        var str = e.value;
        str = str.replace(/[Ａ-Ｚａ-ｚ０-９－！”＃＄％＆’（）＝＜＞，．？＿［］｛｝＠＾～￥]/g, function (s) {
            return String.fromCharCode(s.charCodeAt(0) - 65248);
        });
        e.value = str;
    },
    han2zen:function(e)
    {
        var str = e.value;
        str = Util.convertKanaToTwoByte(str);
        str = str.replace(/[!"#$%&'()*+,\-.\/0-9:;<=>?@A-Z\[\\\]^_`a-z{|}~]/g, function(s) {
            return String.fromCharCode(s.charCodeAt(0) + 0xFEE0);
        });

        e.value = str;
    },
    upercasetolow:function(e)
    {
        var str = e.value;
        str = str.toLowerCase();
        e.value = str;
    },
    convertPlateNo:function(e)
    {
        var value  = e.value;
        var length = value.length;
        if(length < 4){
            need_length = 4 - length;
            zero = '0';
            for(i=1;i<need_length;i++){
               zero += '0'; 
            }
            e.value = zero+''+value;
        }
    },
    setCookie:function(cname, cvalue, exdays)
    {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toGMTString();
        document.cookie = cname + "=" + cvalue + "; " + expires;
    },
    setCookieRedirect: function(url,start_time,name_cookie)
    {
        $.post(url+'ajax/common/setcookie',
            {'start_time':start_time,
             'name_cookie':name_cookie
            },
            function(data){}
        );
    },
    getCookie:function(cname)
    {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ')
                c = c.substring(1);
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    },
    checkCookie:function(cname)
    {
        var name = Util.getCookie(cname);
        if (name != "") {
            return name;
        } else {
            return false;
        }
    },
    confirmDel:function()
    {
        if (!confirm('削除します、よろしいですか？'))
        {
            return false;
        }
    },
    createKanaMap:function(properties, values)
    {
        var kanaMap = {};
        // 念のため文字数が同じかどうかをチェックする(ちゃんとマッピングできるか)
        if (properties.length === values.length) {
            for (var i = 0, len = properties.length; i < len; i++) {
                var property = properties.charCodeAt(i),
                        value = values.charCodeAt(i);
                kanaMap[property] = value;
            }
        }
        return kanaMap;
    },
    m:function()
    {
        return Util.createKanaMap(
        'アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲンァィゥェォッャュョ',
        'ｱｲｳｴｵｶｷｸｹｺｻｼｽｾｿﾀﾁﾂﾃﾄﾅﾆﾇﾈﾉﾊﾋﾌﾍﾎﾏﾐﾑﾒﾓﾔﾕﾖﾗﾘﾙﾚﾛﾜｦﾝｧｨｩｪｫｯｬｭｮ'
        );
    },
    mm:function()
    {
        return  Util.createKanaMap(
        'ｱｲｳｴｵｶｷｸｹｺｻｼｽｾｿﾀﾁﾂﾃﾄﾅﾆﾇﾈﾉﾊﾋﾌﾍﾎﾏﾐﾑﾒﾓﾔﾕﾖﾗﾘﾙﾚﾛﾜｦﾝｧｨｩｪｫｯｬｭｮ',
        'アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲンァィゥェォッャュョ'
        );
    },
    g:function()
    {
        return Util.createKanaMap(
        'ガギグゲゴザジズゼゾダヂヅデドバビブベボ',
        'ｶｷｸｹｺｻｼｽｾｿﾀﾁﾂﾃﾄﾊﾋﾌﾍﾎ'
        );
    },
    gg:function()
    {
        return Util.createKanaMap(
        'ｶｷｸｹｺｻｼｽｾｿﾀﾁﾂﾃﾄﾊﾋﾌﾍﾎ',
        'ガギグゲゴザジズゼゾダヂヅデドバビブベボ'
        );
    },
    p:function()
    {
        return Util.createKanaMap(
        'パピプペポ',
        'ﾊﾋﾌﾍﾎ'
        );
    },
    pp:function()
    {
        return Util.createKanaMap(
        'ﾊﾋﾌﾍﾎ',
        'パピプペポ'
        );
    },
    gMark:'ﾞ'.charCodeAt(0),
    pMark:'ﾟ'.charCodeAt(0),
    convertKanaToTwoByte: function(str)
    {
        var gg = Util.gg();
        var pp = Util.pp();
        var mm = Util.mm();
        for(var i=0, len=str.length; i<len; i++)
        {
            if(str.charCodeAt(i) === Util.gMark || str.charCodeAt(i) === Util.pMark) {
                if(str.charCodeAt(i) === Util.gMark && gg[str.charCodeAt(i-1)]) {
                    str = str.replace(str[i-1], String.fromCharCode(gg[str.charCodeAt(i-1)]))
                     .replace(str[i], '');
                }
            else if(str.charCodeAt(i) === Util.pMark && pp[str.charCodeAt(i-1)]) {
                str = str.replace(str[i-1], String.fromCharCode(pp[str.charCodeAt(i-1)]))
                     .replace(str[i], '');
            }
            else {
                break;
            }
            i--;
            len = str.length;
            }
            else {
                if(mm[str.charCodeAt(i)] && str.charCodeAt(i+1) !== Util.gMark && str.charCodeAt(i+1) !== Util.pMark) {
                    str = str.replace(str[i], String.fromCharCode(mm[str.charCodeAt(i)]));
                }
            }
        }

        return str;
    },
    convertKanaToOneByte: function(e)
    {
        var str = e.value;
        var g = Util.g();
        var p = Util.p();
        var m = Util.m();
        for (var i = 0, len = str.length; i < len; i++) {
            // 濁音もしくは半濁音文字
            if (g.hasOwnProperty(str.charCodeAt(i)) || p.hasOwnProperty(str.charCodeAt(i))) {
                // 濁音
                if (g[str.charCodeAt(i)]) {
                    str = str.replace(str[i], String.fromCharCode(g[str.charCodeAt(i)]) + String.fromCharCode(Util.gMark));
                }
                // 半濁音
                else if (p[str.charCodeAt(i)]) {
                    str = str.replace(str[i], String.fromCharCode(p[str.charCodeAt(i)]) + String.fromCharCode(Util.pMark));
                }
                else {
                    break;
                }
                // 文字列数が増加するため調整
                i++;
                len = str.length;
            }
            else {
                if (m[str.charCodeAt(i)]) {
                    str = str.replace(str[i], String.fromCharCode(m[str.charCodeAt(i)]));
                }
            }
        }
        //return str;
        e.value = str;
    },
    htmlspecialchars:function(string, quote_style, charset, double_encode)
    {
        var optTemp = 0,
            i = 0,
            noquotes = false;
        if (typeof quote_style === 'undefined' || quote_style === null) {
            quote_style = 2;
        }
        string = string.toString();
        if (double_encode !== false) {
            // Put this first to avoid double-encoding
            string = string.replace(/&/g, '&amp;');
        }
        string = string.replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');

        var OPTS = {
            'ENT_NOQUOTES': 0,
            'ENT_HTML_QUOTE_SINGLE': 1,
            'ENT_HTML_QUOTE_DOUBLE': 2,
            'ENT_COMPAT': 2,
            'ENT_QUOTES': 3,
            'ENT_IGNORE': 4
        };
        if (quote_style === 0) {
            noquotes = true;
        }
        if (typeof quote_style !== 'number') {
            // Allow for a single string or an array of string flags
            quote_style = [].concat(quote_style);
            for (i = 0; i < quote_style.length; i++) {
                // Resolve string input to bitwise e.g. 'ENT_IGNORE' becomes 4
                if (OPTS[quote_style[i]] === 0) {
                    noquotes = true;
                } else if (OPTS[quote_style[i]]) {
                    optTemp = optTemp | OPTS[quote_style[i]];
                }
            }
            quote_style = optTemp;
        }
        if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
            string = string.replace(/'/g, '&#039;');
        }
        if (!noquotes) {
            string = string.replace(/"/g, '&quot;');
        }

        return string;
    },
    pleaseWait:function()
    {
        $('.please-wait').show();
	setTimeout(function()
	{
		$('.please-wait').hide();
	}, 4500);
    }
};