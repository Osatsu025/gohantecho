function add_tag(tagJsonString, checkboxElement) {
  let parsedTag;
  try {
    parsedTag = JSON.parse(tagJsonString);
  } catch (e) {
    console.error("Error parsing tag JSON:", e, "Input was:", tagJsonString);
    return; // JSONのパースに失敗したら処理を中断
  }

  // パース後のオブジェクトと必須プロパティの存在を確認
  if (!parsedTag || typeof parsedTag.name !== 'string') {
    console.error("Invalid tag data after parsing. Expected name. Received:", parsedTag);
    return;
  }
  // checkboxElementが渡されているか確認
  if (!checkboxElement) {
    console.error("Checkbox element was not provided to add_tag function.");
    return;
  }

  // `input`要素を取得
  const tagsInputElement =  document.getElementById('tag_input');
  if (!tagsInputElement) {
    console.error("Element with ID 'tag_input' not found.");
    return; // input要素が見つからなければ処理を中断
  }

  const tagName = parsedTag.name;
  const separator = ' '; // タグの区切り文字

  // 現在のinput値を区切り文字で配列にし、空の要素を除去し、各要素のトリムを行う
  let currentTagsArray = tagsInputElement.value
    .split(separator)
    .map(t => t.trim())
    .filter(t => t.length > 0);

  if (checkboxElement.checked) {
    // チェックされた場合：タグがまだ配列になければ追加
    if (!currentTagsArray.includes(tagName)) {
      currentTagsArray.push(tagName);
    }
  } else {
    // チェックが外された場合：配列から該当タグをフィルタリングして削除
    currentTagsArray = currentTagsArray.filter(t => t !== tagName);
  }

  // 配列を区切り文字で結合してinputの値を更新
  tagsInputElement.value = currentTagsArray.join(separator);
}

function syncCheckboxesWithInput() {
  const tagsInputElement = document.getElementById('tag_input');
  if (!tagsInputElement) {
    // console.warn("Element with ID 'tag_input' not found for syncing");
    return;
  }

  const separator = ' ';
  const currentTagsInInput = tagsInputElement.value
    .split(separator)
    .map(t => t.trim())
    .filter(t => t.length > 0);

    // タグ選択用のチェックボックスをすべて取得する
    const allTagCheckboxes = document.querySelectorAll('.tag-checkbox-selector');

    allTagCheckboxes.forEach(checkbox => {
      const tagName = checkbox.dataset.tagName;  // data-tag-name属性からタグ名を取得
      if (tagName) { //タグ名が取得できたら
        checkbox.checked = currentTagsInInput.includes(tagName);
      }
    });
}

window.add_tag = add_tag; 

// DOMが完全に読み込まれた後、またはAlpine.jsの初期化後などにイベントリスナを設定
document.addEventListener('DOMContentLoaded', () => {
  const tagsInputElementForSync = document.getElementById('tag_input');
  if (tagsInputElementForSync) {
    tagsInputElementForSync.addEventListener('input', syncCheckboxesWithInput);
    // 初期化時にも一度同期を実行 (ページ読み込み時にinputに値がセットされている場合のため)
    syncCheckboxesWithInput();
  }
});
