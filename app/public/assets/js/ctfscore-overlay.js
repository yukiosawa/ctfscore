var overlayTimer;
var overlayTimer2;

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




/*
function showOverlay(msg, img_urls){
    clearTimeout(overlayTimer);
    $('#overlayText').remove();
    $('#overlayImg').remove();
    var div = $('<div>').attr('id', 'overlayText').text(msg);
    $('#overlay').append(div);
    var div2 = $('<div>').attr('id', 'overlayImg');
    for (var i=0; i<img_urls.length; i++) {
	var img = $('<img>').attr({
	    src: img_urls[i],
	});
	img.addClass('img-responsive');
	img.addClass('img-responsive-overwrite');
	div2.append(img);
    }
    $('#overlay').append(div2);
    $('#overlay').fadeIn();
    //	 $('#overlayText').textillate();
    overlayTimer = setTimeout('closeOverlay()', 10000);
    //	 showOverlay2(data);
}

function closeOverlay(){
    $('#overlay').fadeOut();
}

*/
