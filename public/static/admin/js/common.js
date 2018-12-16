$(function () {
    if ($.cookie('ztLogWebIdV2') == null) {
        location.href = "login-web.html"
    }
    navShow();
})

function mainUrl() {
    return "https://fire.zt-ioe.com/jdxf/mergin_test/api/";
}

/*导航栏效果*/
function navShow() {
    $("#nav_all").mouseover(function () {
        $("#nav_show_btn").hide();
        $("#nav_above").show();
    });
    /*  $("#nav_all").mouseleave(function () {
          $("#nav_above").slideUp(800);
          console.log("导航栏test========> 1");
          $("#nav_show_btn").slideDown(100);
          console.log("导航栏test========> 2");
      });*/

}

function confirmsZt(content, confirmfunction, cancelfunction) {
    $.confirm({
        title: '确认!',
        content: content,
        theme: 'material',
        animation: 'right',
        closeAnimation: 'right',
        buttons: {
            confirm: {
                text: '确定',
                action: confirmfunction
            },
            cancel: {
                text: '取消',
                action: cancelfunction
            }
        }
    });
}


function tipsZt(content) {
    $.alert({
        title: '提示',
        content: content,
        animation: 'right',
        theme: 'material',
        closeAnimation: 'right',
        buttons: {
            confirm: {
                text: '确定'
            }
        }

    });
}

function getCookie(id) {
    var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
    if (arr = document.cookie.match(reg)) {
        return unescape(arr[2]);
    }
    else {
        return null;
    }
}


function logout() {
    confirmsZt("确定注销？", function () {
        $.cookie('ztLogWebIdV2', null, {expires: -1, path: '/'});
        $.cookie('ztLogWebRankV2', null, {expires: -1, path: '/'});
        $.cookie('ztLogWebNameV2', null, {expires: -1, path: '/'});
        console.log("注销")
        location.href = '../pages-v2/login-web.html';

    }, function () {
        console.log("注销错误 请检查网络")
    })

}
