# 環境構築

`.env.example`の内容を`.env`にコピー
```shell
cp .env.example .env
```

`APP_KEY`を生成する。
```shell
php artisan key:generate
```
これで`.env`で空欄になっている`APP_KEY`にランダムな文字列が入る。
この値は環境ごとに用意するもの。
