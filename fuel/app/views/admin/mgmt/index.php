<html>
  <head>
    <meta charset="utf-8">
    <title>管理コンソール</title>
    <?php echo Asset::css('bootstrap.css'); ?>
    <?php echo Asset::css('ctfscore.css'); ?>
    <?php echo Asset::js('jquery-2.2.4.min.js'); ?>
    <?php echo Asset::js('socket.io.js'); ?>
    <?php echo Asset::css('animate.css'); ?>
    <?php echo Asset::js('jquery.lettering.js'); ?>
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

     var success = '<?php echo Config::get('ctfscore.answer_result.success.event'); ?>';
     var failure = '<?php echo Config::get('ctfscore.answer_result.failure.event'); ?>';
     var duplicate = '<?php echo Config::get('ctfscore.answer_result.duplicate.event'); ?>';
     var over_limit = '<?php echo Config::get('ctfscore.answer_result.over_limit.event'); ?>';

     // 診断用メッセージ
     socket.on('message', function (msg) {
	 console.log(msg);
	 addMessage('messageArea', 'message', msg);
     });

     // 正解
     socket.on(success, function (data) {
	 console.log(data);
	 playAudio(data.sound_url);
	 showOverlay(data);
	 addMessage('messageArea', success, data.msg);
     });

     // 不正解
     socket.on(failure, function (data) {
	 console.log(data);
         playAudio(data.sound_url);
	 showOverlay(data);
	 //addMessage('messageArea', failure, data.msg);
     });

     // 回答済
     socket.on(duplicate, function (data) {
	 console.log(data);
         playAudio(data.sound_url);
	 addMessage('messageArea', duplicate, data.msg);
     });

     // 回数制限オーバー
     socket.on(over_limit, function (data) {
	 console.log(data);
         playAudio(data.sound_url);
	 addMessage('messageArea', over_limit, data.msg);
     });

     function addMessage(targetName, className, msg){
	 var div = $('<div>');
	 var span1 = $('<span>');
	 var span2 = $('<span>');
	 span1.attr('class', 'datetime');
         /* span1.text(new Date().toTimeString() + ': '); */
         span1.text('[' + getFormatDate() + '] ');
	 span2.attr('class', className);
         /* span2.text(msg + ' [' + className + ']'); */
	 span2.text(msg);
	 div.append(span1).append(span2);
	 $('#' + targetName).prepend(div);
	 // テキストが増えてくると重くなるのでやめとく
	 //messageTextillate();
     }

     function getFormatDate(){
         var d = new Date();
         var y = d.getFullYear();
         var mon = (d.getMonth() + 1 < 10) ? '0' + (d.getMonth() + 1) : d.getMonth() + 1;
         var day = (d.getDate() < 10) ? '0' + d.getDate() : d.getDate();
         var h = (d.getHours() < 10) ? '0' + d.getHours() : d.getHours();
         var min = (d.getMinutes() < 10) ? '0' + d.getMinutes() : d.getMinutes();
         var sec = (d.getSeconds() < 10) ? '0' + d.getSeconds() : d.getSeconds();
         return y + '-' + mon + '-' + day + ' ' + h + ':' + min + ':' + sec;
     }
     
     function sendMessage(){
	 var msg = $(':text[name="chatMessage"]').val();
	 if (msg == '') {return};
	 $(':text[name="chatMessage"]').val('');
	 socket.emit('message', msg);
	 addMessage('messageArea', 'message', msg);
     }

     function playAudio(sound_url){
         if (sound_url == '') return;

         $('#audio').remove();
         var audio = $('<audio autoplay>').attr({
             'id': 'audio',
             'src': sound_url
         });
         $('#audioArea').append(audio);
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
    <?php if ($logo_image): ?>
      <img src="<?php echo $logo_image; ?>" class="img-responsive" />
    <?php endif; ?>

    <div id='messageArea'>
      <?php $success = Config::get('ctfscore.answer_result.success.event'); ?>
      <?php foreach ($gained_history as $value): ?>
        <div>
          <span class='datetime'><?php echo '[' . $value['gained_at'] . '] '; ?></span>
          <span class='<?php echo $success; ?>'>
            <?php echo $value['username'] . ' は #' . $value['puzzle_id'] . ':' . $value['title'] . ' を解きました！ ['.$value['point'].'点 ('.$value['bonus_point'].'点)]'; ?>
          </span>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- メッセージ送信テスト -->
    <?php if ($diag_msg): ?>
      <p>
        <form>
          <span class='col-md-5'>
            <input class='form-control' type='text' name='chatMessage'></input>
          </span>
          <span class='col-md-7'>
            <input class='btn' onclick='sendMessage(); return false;' type='submit' value='Send'></input>
          </span>
        </form>
      </p>
    <?php endif; ?>

    <div id='overlay'></div>
    <div id='overlay2'></div>
    <div id='audioArea'></div>
  </body>
</html>

