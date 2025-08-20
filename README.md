# 環境構築
- `git clone`
  - WindowsならWSL上のUbuntuホームなどに
- ターミナルで、`gohantecho/backend`まで移動
- 下記コマンドで`composer install`を実行
  ```shell
  gohantecho/backend$ docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php84-composer:latest composer install --ignore-platform-reqs
  ```
- `.bashrc`に`sail`コマンドのエイリアスを設定
  - `vim .bashrc`で設定ファイルを編集  
  ```
  alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
  ```
- `sail up`で起動する
  - フロントのNext.jsもそのまま立ち上がる
