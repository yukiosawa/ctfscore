var overlayTimer;
var overlayTimer2;
var overlayTimer3;

// 問題回答時の画像表示
function showOverlay(data){
    if (data.msg == '' && data.image_url == '') return;
    
    clearTimeout(overlayTimer);
    $('#overlayText').remove();
    $('#overlayImg').remove();

    var div = $('<div>').attr('id', 'overlayText').text(data.msg);
    $('#overlay').append(div);

    if (data.image_url != '') {
        var div2 = $('<div>').attr('id', 'overlayImg');
        var img = $('<img>').attr({
            src: data.image_url,
        });
        img.addClass('img-responsive');
        img.addClass('img-responsive-overwrite');
        div2.append(img);
        $('#overlay').append(div2);
    }

    $('#overlay').fadeIn();
    $('#overlayText').textillate();
    overlayTimer = setTimeout('closeOverlay()', 10000);

    if (data.first_bonus_img_url != '') {
        showOverlay2(data);
    }
}

function closeOverlay(){
    $('#overlay').fadeOut();
}

// 初正解のボーナス画像表示
function showOverlay2(data){
    clearTimeout(overlayTimer2);
    $('#overlayImg2').remove();
    if (data.first_bonus_img_url != '') {
        var img = $('<img>').attr({
            id: 'overlayImg2',
            src: data.first_bonus_img_url,
        });
        img.addClass('img-responsive');
        img.addClass('img-responsive-overwrite');
        $('#overlay2').append(img);
        $('#overlay2').fadeIn();
        overlayTimer2 = setTimeout('closeOverlay2()', 5000);
    }
}

function closeOverlay2(){
    $('#overlay2').fadeOut();
}

// ユーザ登録時の画像表示
function showOverlay3(img_url, btn_url){
    if (img_url == '') return;
    clearTimeout(overlayTimer3);
    $('#overlayImg').remove();
    var div = $('<div>').attr('id', 'overlayImg');
    var img = $('<img>').attr({
        src: img_url,
    });
    img.addClass('img-responsive');
    img.addClass('img-responsive-overwrite');
    div.append(img);
    $('#overlay3').append(div);
    $('#overlay3').fadeIn();
    if (btn_url == '') {
        setTimeout('closeOverlay3()', 10000);
    } else {
        var btn = $('<img>', {'src': btn_url, 'id': 'overlay3-close'}).text('開始する').attr({
            onclick: 'closeOverlay3();',
            style: 'position: absolute; top: 0; bottom: 0; right: 0; left: 0; margin: auto; cursor: pointer;',
            onerror: "setTimeout('closeOverlay3()', 10000);",
        });
        $('#overlay3').append(btn);
    }
}

function closeOverlay3(){
    $('#overlay3').fadeOut();
}


