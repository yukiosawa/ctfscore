ctfscore
========
CTFのスコアサーバです。

## 説明
CTFスコアサーバとしての基本的な機能のほか、CTFを楽しく開催するための便利機能もつけています。
- 問題ごとのポイント・初回ボーナスポイント
- フラグサブミット時の画像、音声、テキスト(正解、不正解それぞれ指定可)
    - 連動する管理コンソールで会場スクリーンへ表示も可
- プロフィール画面(CTFプレイヤー間の比較も可)
- 解いた問題数に応じたCTFプレイヤーのレベル(称号)
- 問題のレビュー投稿
- チャート表示(順位、正解者分布)
- CTF終了までのカウントダウン
- 問題へのヒントリクエスト
- 各CTFプレイヤー向けの賞状
- 管理者からのお知らせ
- 管理者によるボーナスポイント付与
- 一定時間内のサブミット回数制限(ブルートフォース禁止)
- ロゴ画像、背景画像のカスタマイズ
- CTFプレイヤーは個人(チーム参加には未対応)


## 使い方
- スコアサーバ設置場所は任意
    - 管理コンソールとスコアサーバがネットワーク的に離れる場合は、管理コンソールとスコアサーバ間のWebSocket(8080/TCP)が接続可能であることを確認しておくこと(特にProxyやFWが間にある環境で要注意)。
- 管理者の作成(初めて作成するユーザが自動的に管理者となる)
    - Webブラウザでアクセス `http://<hostname>/`
    - `ログイン -> 新規ユーザ`で新規ユーザ登録する。
- 管理者ページから事前登録
    - 問題のカテゴリ　[必須]
    - 問題　[必須]
    - サイト説明、競技ルール説明ページ　[必須]
    - 開始終了時刻　[必須]
    - CTFの名称(賞状表示用)
    - 正解時、不正解時の画像・音声・テキストメッセージ
    - 問題正解数に応じたレベル
    - その他細かい設定が必要であれば
- CTF開始
    - 管理コンソールを開いておく。
    - 開始時刻になると自動的に問題公開。
    - 終了時刻以降はサブミット不可。賞状へのアクセス可。


## 環境構築
- Ubuntu 14.04 LTS, Raspbian Jessie で検証済み

- 事前準備
```
$ cd ~
$ sudo apt-get install -y git curl
```

- apache, mysql and php  
途中MySQLのrootパスワード設定を(何度か)求められるが、後で設定するので、空欄のまま進める。
```
$ sudo apt-get install -y apache2 mysql-server php5 php5-mysql
```

- FuelPHP
```
$ curl https://get.fuelphp.com:443/oil | sh
$ oil create ctfscore
$ cd ctfscore
$ oil refine install
$ cd ..
$ rm ctfscore/public/favicon.ico
$ sudo mv ctfscore /var/www/.
$ sudo a2enmod rewrite
$ sudo service apache2 restart
```

- node.js and socket.io
ubuntuリポジトリにあるnodejsはバージョンが低いので、nを使って最新版nodejsを導入。
```
$ sudo apt-get install -y nodejs npm
$ sudo npm install n -g
$ sudo n stable
$ sudo apt-get purge -y nodejs npm
$ npm install socket.io
$ find node_modules -name socket.io.js | sudo xargs -i cp -p {} /var/www/ctfscore/public/assets/js/.
```

- socket.io-php-emitter
```
$ sudo apt-get install -y redis-server
$ npm install socket.io-redis
$ npm install socket.io-emitter
$ git clone https://github.com/ashiina/socket.io-php-emitter.git
$ cd socket.io-php-emitter
$ /var/www/ctfscore/composer.phar install
$ cd ..
$ mkdir -p /var/www/ctfscore/nodejs
$ sudo cp -r node_modules /var/www/ctfscore/nodejs/.
$ sudo cp -r socket.io-php-emitter /var/www/ctfscore/nodejs/.
```

- ctfscoreアプリケーションのインストール
```
$ cd ~
$ git clone https://github.com/yukiosawa/ctfscore.git
$ sudo cp -r ctfscore/etc/fuelphp/* /var/www/ctfscore/.
$ sudo cp -r ctfscore/* /var/www/ctfscore/.
```

- Webサーバ権限設定  
所有権をapacheユーザとする
```
$ sudo chown -R www-data:www-data /var/www/ctfscore
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
$ sudo ~/ctfscore/etc/scripts/setup_mysql.sh
$ ~/ctfscore/etc/scripts/init_app_db.sh
```

- サービスの開始  
自動起動していれば不要
```
$ sudo service mysql start
$ sudo service apache2 start
$ sudo service redis-server start
$ node /var/www/ctfscore/nodejs/app.js &
```

- 管理者ユーザの作成
    - Webブラウザでアクセス `http://<hostname>/`
    - `ログイン -> 新規ユーザ`で新規ユーザ登録する。初めて作成したユーザには自動的に管理者権限が付与される。

- エラーメッセージを非表示に  
    - `/var/www/ctfscore/public/.htaccess`の以下の行を有効化して本番環境設定にする(コメントアウトされているので先頭の#を削除)。
    ```
    SetEnv FUEL_ENV production
    ```
    - `/var/www/ctfscore/public/index.php`を以下のとおり設定する(値0を設定)。
    ```
    error_reporting(0);
    ini_set('display_errors', 0);
    ```

- ファイアウォール設定  
以下のポートを開放する。
    - HTTP(80/TCP)
    - WebSocket(8080/TCP) *管理者(管理コンソール)のみが利用
    - SSHなどその他必要に応じて

- [参考]管理コンソールが反応しない(音・メッセージ)時の調査方法
    - 管理コンソールで受信したメッセージをリアルタイム表示する。
      Webブラウザで管理コンソールを開き、`F12開発者ツール -> コンソールログ`
    - Redisサーバが受信したメッセージをリアルタイム表示する。
    ```
    $ redis-cli -h localhost monitor
    ```
    - 上記の状態で別のWebブラウザを開き、一般ユーザでログインしてflag(正解、不正解)をサブミットしてみる。

- [参考]ctfscoreアプリケーションの更新  
データベースや設定は保持してアプリケーションコードのみ更新する。
```
$ cd ~/ctfscore
$ git pull
$ cd ~
$ sudo cp -r ctfscore/* /var/www/ctfscore/.
```

- [参考]MySQLユーザ/パスワードを手動設定する場合  
ctfscoreアプリケーションからDB接続するためのユーザを作成したあと、以下のファイルを修正しておく。
    - /var/www/ctfscore/fuel/app/config/production/db.php
    - /var/www/ctfscore/fuel/app/config/development/db.php
    - ~/ctfscore/etc/scripts/.mysql_password (なければ作成。保守スクリプト用。)


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

