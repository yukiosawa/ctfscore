var overlayTimer;
var overlayTimer2;
var overlayTimer3;

function showOverlay(data){
    clearTimeout(overlayTimer);
    $('#overlayText').remove();
    $('#overlayImg').remove();
    var div = $('<div>').attr('id', 'overlayText').text(data.msg);
    $('#overlay').append(div);
    var div2 = $('<div>').attr('id', 'overlayImg');
    for (var i=0; i<data.img_urls.length; i++) {
        var img = $('<img>').attr({
            src: data.img_urls[i],
        });
        img.addClass('img-responsive');
        img.addClass('img-responsive-overwrite');
        div2.append(img);
    }
    $('#overlay').append(div2);
    $('#overlay').fadeIn();
    $('#overlayText').textillate();
    overlayTimer = setTimeout('closeOverlay()', 10000);
    showOverlay2(data);
}

function closeOverlay(){
    $('#overlay').fadeOut();
}

function showOverlay2(data){
    clearTimeout(overlayTimer2);
    $('#overlayImg2').remove();
    if (data.first_bonus_img != '') {
        var img = $('<img>').attr({
            id: 'overlayImg2',
            src: data.first_bonus_img,
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

function showOverlay3(img_url){
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
    var btn = $('<img>', {src: '/assets/img/btn_go.png', 'id': 'overlay3-close'}).text('開始する').attr({
        onclick: 'closeOverlay3();',
        style: 'position: absolute; top: 0; bottom: 0; right: 0; left: 0; margin: auto; cursor: pointer;',
    });
    $('#overlay3').append(btn);
}

function closeOverlay3(){
    $('#overlay3').fadeOut();
}


