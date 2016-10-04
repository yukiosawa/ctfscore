function startCountdown(unixtime) {
    FlipClock.Lang['ja'] = {
        'years'  : '年',
        'months' : '月',
        'days'   : '日',
        'hours'  : '時',
        'minutes': '分',
        'seconds': '秒' 
    };
    var currentDate = new Date();
    var countdownDate  = new Date(unixtime);
    var diff = countdownDate.getTime() / 1000 - currentDate.getTime() / 1000;
    if (diff < 0) {
         $('#countdown').text(
             'CTFは ' + countdownDate.toString() + ' に終了しました。'
         );
    } else {
        $('#countdown').FlipClock(diff, {
            clockFace: 'DailyCounter',
            countdown: true,
            language: 'ja'
        });
    }
}

