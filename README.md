ctfscore
========
CTFのスコアサーバです。

## 説明



## 使い方



## 環境構築(Ubuntu)
- 事前準備
```
sudo apt-get install -y git curl
```

- apache, mysql and php
途中MySQLのrootパスワード設定を(何度か)求められるが、後で設定するので、空欄のまま進める。
```
$ sudo apt-get install -y apache2 mysql-server php5 php5-mysql
```

- FuelPHP
```
$ curl get.fuelphp.com/oil | sh
$ oil create ctfscore
$ sudo mv ctfscore /var/www/.
$ sudo a2enmod rewrite
$ sudo service apache2 restart
```

- node.js and socket.io
```
$ sudo apt-get install -y nodejs npm
$ sudo update-alternatives --install /usr/bin/node nodejs /usr/bin/nodejs 100
$ npm install socket.io
$ sudo cp node_modules/socket.io/node_modules/socket.io-client/socket.io.js /var/www/ctfscore/public/assets/js/.
```

- socket.io-php-emitter
```
$ sudo apt-get install -y redis-server
$ npm install socket.io-redis
$ npm install socket.io-emitter
$ git clone https://github.com/ashiina/socket.io-php-emitter.git
$ cd socket.io-php-emitter
$ /var/www/ctfscore/composer.phar install
```

- ctfscoreアプリケーションのインストール
```
$ cd ~
$ git clone https://github.com/yukiosawa/ctfscore.git
$ sudo cp -r ctfscore/etc/fuelphp/* /var/www/ctfscore/.
$ sudo cp -r ctfscore/* /var/www/ctfscore/.
$ sudo cp -r node_modules /var/www/ctfscore/nodejs/.
```

- apache virtual hostの設定
```
$ sudo cp ctfscore/etc/apache2/ctfscore.conf /etc/apache2/sites-available/.
$ sudo a2dissite 000-default
$ sudo a2ensite ctfscore
$ sudo service apache2 restart
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
$ node /var/www/ctfscore/nodejs/app.js &
```

- 管理者ユーザの作成
    - Webブラウザでアクセス `http://<hostname>/`
    - `ログイン -> 新規ユーザ`で新規ユーザ登録する。初めて作成したユーザには自動的に管理者権限が付与される。

- [参考]管理コンソールが反応しない(音・メッセージ)時の調査方法
    - 管理コンソールで受信したメッセージをリアルタイム表示する。
      Webブラウザで管理コンソールを開き、`F12開発者ツール -> コンソールログ`
    - Redisサーバが受信したメッセージをリアルタイム表示する。
    ```
    $ redis-cli -h localhost monitor
    ```
    - 上記の状態で別のWebブラウザを開き、一般ユーザでログインしてflag(正解、不正解)をサブミットしてみる。

- エラーメッセージを非表示に  
`/var/www/ctfscore/public/.htaccess`の以下の行をコメントアウトして本番環境設定にする。
```
SetEnv FUEL_ENV production
```
`/var/www/ctfscore/public/index.php`を以下のとおり設定する(値0を設定)。
```
error_reporting(0);
ini_set('display_errors', 0);
```


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

