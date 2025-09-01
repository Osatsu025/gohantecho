# 環境構築
## リポジトリのクローン
  ```shell
    git clone https://github.com/Osatsu025/gohantecho gohantecho
  ```
- WindowsならWSL上のUbuntuホームなどにクローンするのがおすすめ

## バックエンド(Laravel)のセットアップ
- ターミナルで、`gohantecho/backend`まで移動

- Composerの依存関係をインストール
  ```shell
  docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php84-composer:latest composer install --ignore-platform-reqs
  ```

- `.env`ファイルを作成し、`APP_KEY`を作成
  ```shell
    cp .env.example .env
    ./vendor/bin/sail artisan key:generate
  ```
- 必要な環境変数を埋める

## フロントエンド(Next.js)のセットアップ
- ターミナルで`gohantecho/frontend`に移動
- 依存パッケージのインストール
  ```shell
  npm install
  ```
- `.env.local`を作成する
  ```shell
    cp .env.example .env.local
  ```
- `.env.local`の環境変数を埋める

## 起動確認
- ターミナルで`gohantecho/backend`に移動
- コンテナを起動
  ```shell
  ./vendor/bin/sail up
  ```
- バックエンドとフロントエンドが同時に起動する
  - バックエンド: http://localhost:8000
  - フロントエンド: http://localhost:3000

- データベースのマイグレーションを実行
  ```shell
    ./vendor/bin/sail artisan migrate
  ```
- コンテナの停止
  ```shell
   ./vendor/bin/sail down
  ```

## (推奨)エイリアスの設定
コマンドの`vendor/bin/sail`を`sail`にする方法
- `.bashrc`に`sail`コマンドのエイリアスを設定
  - `vim .bashrc`で設定ファイルを編集  
  ```
  alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
  ```
- 設定ファイルを再読み込みする
  ```shell
  source ~/.bashrc
  ```