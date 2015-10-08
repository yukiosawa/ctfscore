<html>
  <head>
    <meta charset="utf-8">
    <title>管理コンソール</title>
    <?php echo Asset::css('bootstrap.css'); ?>
    <?php echo Asset::js('jquery-2.1.1.min.js'); ?>
    <?php echo Asset::js('socket.io.js'); ?>
    <?php echo Asset::css('animate.css'); ?>
    <?php echo Asset::js('jquery.lettering-0.6.min.js'); ?>
    <?php echo Asset::js('jquery.textillate.js'); ?>
    <?php echo Asset::js('jquery.bgswitcher.js'); ?>

    <style type='text/css'>
     .img-responsive-overwrite{
       margin: 0 auto;
     }

     #overlay{
       display : none;
       width: 100%;
       height: 100%;
       text-align: center;
       position: fixed;
       top: 0;
       z-index: 50;
       background: rgba(0,0,0,1);
     }

     #overlayText{
       font-size: 30px;
       color: rgba(255,0,0,1);
       padding-top: 100px;
       vertical-align: middle;
       font-weight: bold;
     }

     #overlay2{
       display : none;
       width: 100%;
       height: 100%;
       text-align: center;
       position: fixed;
       top: 0;
       z-index: 100;
       background: rgba(0,0,0,1);
     }

     body{
       background: rgba(0,100,0,1);
     }

     #messageArea{
       /* color: #32CD32; */
       font-size: 20px;
       color: rgba(0,0,0,0.9);
       font-weight: bold;
     }
    </style>

    <script>
     var socket = io(<?php echo '"http://'.$_SERVER['SERVER_NAME'].':8080"'; ?>);
     var overlayTimer;
     var overlayTimer2;

     socket.on('message', function (data) {
	 console.log(data);
	 addMessage('messageArea', 'message', data);
     });

     socket.on('success', function (data) {
	 console.log(data);
	 playAudio('audio-success');
	 showOverlay(data);
	 addMessage('messageArea', 'success', data.msg);
     });

     socket.on('levelup', function (data) {
	 console.log(data);
	 playAudio('audio-levelup');
	 showOverlay(data);
	 addMessage('messageArea', 'levelup', data.msg);
     });

     socket.on('failure', function (data) {
	 console.log(data);
	 playAudio('audio-failure');
	 //addMessage('messageArea', 'fail', data);
     });

     socket.on('notice', function (data) {
	 console.log(data);
	 playAudio('audio-notice');
	 addMessage('messageArea', 'notice', data);
     });

     function addMessage(targetName, className, msg){
	 var div = $('<div>');
	 var span1 = $('<span>');
	 var span2 = $('<span>');
	 span1.attr('class', 'datetime');
	 span1.text(new Date().toTimeString() + ': ');
	 span2.attr('class', className);
	 span2.text(msg + ' [' + className + ']');
	 div.append(span1).append(span2);
	 $('#' + targetName).prepend(div);
	 // テキストが増えてくると重くなるのでやめとく
	 //messageTextillate();
     }
     
     function sendMessage(){
	 var msg = $(':text[name="chatMessage"]').val();
	 if (msg == '') {return};
	 $(':text[name="chatMessage"]').val('');
	 socket.emit('message', msg);
	 addMessage('messageArea', 'message', msg);
     }

     function playAudio(className){
	 // var target = $('#'+id).get(0);
	 // ランダム再生とする
	 var targets = $('.' + className);
	 var index = Math.floor(Math.random() * targets.length);
	 var target = targets[index];
	 if (target){
	     target.currentTime = 0;
	     target.play();
	 }
     }

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

     function messageTextillate(){
	 var tlt = $('#messageArea > *').textillate({
	     loop: true,
	     minDisplayTime: 30000,
	     in:{
		 effect: 'fadeInLeftBig',
		 sync: true
	     },
	     out:{
		 effect: 'fadeOutDownBig',
		 shuffle: true
	     }
	 });
	 tlt.textillate('stop');
	 tlt.textillate('start');
     }
    </script>
  </head>

  <?php require(APPPATH.'views/_templateheader.php'); ?>

  <body>
    <?php echo Asset::img($logo_image, array('class' => 'img-responsive')); ?>

    <div id='messageArea'></div>

    <p>
    <form>
      <input type='text' name='chatMessage'></input>
      <input onclick='sendMessage(); return false;' type='submit' value='Send'></input>
    </form>
    </p>

    <?php
    foreach ($success_files as $file) {
	echo Html::audio($file, 'class=audio-success');
    }
    foreach ($failure_files as $file) {
	echo Html::audio($file, 'class=audio-failure');
    }
    foreach ($levelup_files as $file) {
	echo Html::audio($file, 'class=audio-levelup');
    }
    foreach ($notice_files as $file) {
	echo Html::audio($file, 'class=audio-notice');
    }
    ?>

    <div id='overlay'></div>
    <div id='overlay2'></div>

  </body>
</html>

