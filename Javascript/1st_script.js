// JavaScriptでアラートを表示する関数
function showMessageSuccese() {
    alert("メッセージを投稿しました。");
}
function showMessageLike() {
    alert("いいねを押しました。");
}
function showMessageDelete() {
    alert("メッセージを削除しました。");
}

// APIKEYの配列
const apikeys = ["GY0Sfh9aFeyWedNqGpZvjwSFZQsWjgN5", "wvvSLW1nfe63cyLOuWqV13BTaeE4pfvi", "QxTJv4MvBPK0ioZdhhvbUJn7KYZhJ7rr", "AEnVg7ndl2SnxgZOjItBR0OlEgUEWltr", "brOFh11WsKKDVF8QlS7uFYuiTUc4G68Z"];

// ページ読み込み時にランダムにAPIキーを選択
const randomIndex1 = Math.floor(Math.random() * apikeys.length);
const selectedApikey = apikeys[randomIndex1];

// keywordの配列
const keywords = ["tranding", "laughing", "awesome", "pepe", "america", "cat", "dog", "pokemon", "ghibli"];

// ページ読み込み時にランダムにKeywordを選択
const randomIndex2 = Math.floor(Math.random() * keywords.length);
const selectedKeyword = keywords[randomIndex2];

// GIPHY APIのエンドポイントとAPIキー
const apiKey = 'AEnVg7ndl2SnxgZOjItBR0OlEgUEWltr';

// const apiUrl = `https://api.giphy.com/v1/gifs/trending?api_key=${apiKey}&limit=20`;
const apiUrl = `https://api.giphy.com/v1/gifs/search?q=${selectedKeyword}&api_key=${selectedApikey}&limit=20`;


// ページの初期読み込み時にGIFを表示
document.addEventListener('DOMContentLoaded', () => {
  fetchGIFs(apiUrl);
});

// GIFを取得して表示する関数
function fetchGIFs(url) {
  fetch(url)
    .then(response => response.json())
    .then(data => {
      const gifContainer = document.getElementById('gallery');
      data.data.forEach(gif => {
        const img = document.createElement('img');
        img.src = gif.images.fixed_height.url;
        img.alt = gif.title;
        img.classList.add('gif');
        gifContainer.appendChild(img);
      });
    })
    .catch(error => console.error('Error fetching GIFs:', error));
}

window.addEventListener('load', function() {
    document.getElementById('gallery').classList.add('loaded');
});
