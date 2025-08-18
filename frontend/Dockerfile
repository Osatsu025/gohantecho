# 1. ベースとなるNede.jsのイメージを選択
FROM node:22-alpine

# 2. コンテナ内での作業ディレクトリを作成・指定
WORKDIR /app

# 3. 依存関係のインストールを効率化するため、package.json関連のファイルだけ先にコピー
COPY package*.json ./

# 4. 依存関係をインストール
RUN npm install

# 5. アプリケーションのソースコードを全てコピー
COPY . .

# 6. Next.jsが使用するポート3000番を公開
EXPOSE 3000

# 7. 開発サーバを起動するコマンドを実行
CMD ["npm", "run", "dev"]