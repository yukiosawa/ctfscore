ctfscore
========
CTFのスコアサーバです。

## 説明



## 使い方



## 環境構築(Ubuntu)
- apache, mysql and php
```
$ sudo apt-get install -y apache2 mysql-server php5 php5-mysql
```

- FuelPHP
```
$ curl get.fuelphp.com/oil | sh
$ cd /var/www
$ sudo oil create ctfscore
```

- apache Document Root
virtual hostの設定を作成
```
$ sudo cp etc/apache2/ctfscore.conf /etc/apache2/sites-available/.
$ sudo a2dissite 000-default
$ sudo a2ensite ctfscore
```

- apache mod rewrite
```
$ sudo a2enmod rewrite
$ sudo service apache2 restart
```

- node.js and socket.io
```
$ sudo apt-get install -y nodejs
$ sudo update-alternatives --install /usr/bin/node nodejs /usr/bin/nodejs 100
$ sudo mkdir /var/www/ctfscore/nodejs
$ cd /var/www/ctfscore/nodejs
$ curl https://www.npmjs.com/install.sh | sudo sh
$ sudo npm install socket.io
$ sudo cp node_modules/socket.io-client/socket.io.js /var/www/ctfscore/public/assets/js/.
```

- socket.io-php-emitter
```
$ sudo apt-get install -y redis-server
$ sudo npm install socket.io-redis
$ sudo npm install socket.io-emitter
$ sudo git clone https://github.com/ashiina/socket.io-php-emitter.git
$ cd socket.io-php-emitter
$ sudo /var/www/ctfscore/composer.phar install
```

- ctfscoreアプリケーションのインストール
```
$ cd ~
$ git clone https://github.com/yukiosawa/ctfscore.git
$ sudo cp -r ctfscore/* /var/www/ctfscore/.
```

- ctfscoreデータベースの初期化
```
$ sudo service mysql start
$ ~/ctfscore/etc/scripts/setup_mysql.sh
$ ~/ctfscore/etc/scripts/init_app_db.sh
```

- サービスの開始
```
$ sudo service mysql start
$ sudo service apache2 start
$ sudo service redis-server start
$ node /var/www/ctfscore/nodejs/app.js
```

- 動作確認
_全般_
Webブラウザでアクセス `{http://<hostname>/`

_管理コンソール_
(1) Redisサーバが受信したメッセージをリアルタイム表示する。
```
$ redis-cli -h localhost monitor
```
(2) 管理コンソールで受信したメッセージをリアルタイム表示する。  
Webブラウザで管理コンソールを開き、`F12開発者ツール -> コンソールログ`
(3) 上記の状態で別のWebブラウザを開き、一般ユーザでログインしてflag(正解、不正解)をサブミットしてみる。


## その他同梱している外部ライブラリ
* [jQuery](https://jquery.com/) v2.2.4
* [Trumbowyg](http://alex-d.github.io/Trumbowyg/) v2.3.0
* [Chart.js](http://www.chartjs.org/) Version: 2.2.2
* [jQuery Raty](https://github.com/wbotelhos/raty) v2.7.0
* [TableSorter](http://tablesorter.com/docs/) v2.0.5b
* [Textillate.js](http://textillate.js.org/) v0.4.0
    * (Textillate) [Lettering.js](https://github.com/davatron5000/Lettering.js)
    * (Textillate) [animate.css](https://github.com/daneden/animate.css)
* [FlipClock.js](http://flipclockjs.com/) 0.8.0 Beta
* [jQuery.floatThead](http://mkoryak.github.io/floatThead/) 1.4.3


