<?php

use Illuminate\Support\Arr;

if (! function_exists('query_route')) {
  /**
   * クエリパラメータを追加/削除したルートを作るヘルパメソッド
   * 
   * @param string $name 名前付きルート
   * @param array $parameters 新しく渡したいルートパラメータやクエリパラメータ
   * @param array|string $except 取り除きたいクエリのキー
   * @param bool $absolute trueなら絶対ルートで表示する
   * @return string 新しいURL
   */

   function query_route(string $name, array $parameters = [], array|string $except = [], bool $absolute = true): string
   {
    $query = Arr::except(request()->query(), $except);

    return route($name, array_merge($query, $parameters), $absolute);
   }
}