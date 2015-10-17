<html>
  <head>
    <meta charset="utf-8">
    <title>管理コンソール</title>
    <?php echo Asset::css('bootstrap.css'); ?>
    <?php echo Asset::css('ctfscore.css'); ?>
    <?php echo Asset::js('jquery-2.1.1.min.js'); ?>
    <?php echo Asset::js('socket.io.js'); ?>
    <?php echo Asset::css('animate.css'); ?>
    <?php echo Asset::js('jquery.lettering-0.6.min.js'); ?>
    <?php echo Asset::js('jquery.textillate.js'); ?>
    <?php echo Asset::js('ctfscore-overlay.js'); ?>

    <style type='text/css'>
     #messageArea{
       /* color: #32CD32; */
       font-size: 20px;
       color: rgba(0,0,0,0.9);
       font-weight: bold;
     }
    </style>

    <script>
     var socket = io(<?php echo '"http://'.$_SERVER['SERVER_NAME'].':8080"'; ?>);

     socket.on('message', function (msg) {
	 console.log(msg);
	 addMessage('messageArea', 'message', msg);
     });

     socket.on('success', function (data) {
	 console.log(data);
	 if (data.is_first_winner) {
	     playAudio('audio-first_winner');
	 }
	 else {
	     playAudio('audio-success');
	 }
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
	 showOverlay(data);
	 //addMessage('messageArea', 'failure', data.msg);
     });

     socket.on('duplicate', function (data) {
	 console.log(data);
	 playAudio('audio-notice');
	 addMessage('messageArea', 'notice', data.msg);
     });

     socket.on('notice', function (msg) {
	 console.log(msg);
	 playAudio('audio-notice');
	 addMessage('messageArea', 'notice', msg);
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

<!-- デバッグ用
    <p>
    <form>
      <input type='text' name='chatMessage'></input>
      <input onclick='sendMessage(); return false;' type='submit' value='Send'></input>
    </form>
    </p>
-->

    <?php
    foreach ($first_winner_files as $file) {
	echo Html::audio($file, 'class=audio-first_winner');
    }
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

