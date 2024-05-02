// JavaScriptでギャラリーの非表示/表示を切り替える
const toggleGalleryButton = document.getElementById('toggleGalleryButton');
const gallery = document.getElementById('gallery');

toggleGalleryButton.addEventListener('click', function() {
    if (gallery.style.display === 'none') {
        gallery.style.display = 'block';
        toggleGalleryButton.textContent = '非表示にする';
    } else {
        gallery.style.display = 'none';
        toggleGalleryButton.textContent = '表示する';
    }
});

    // 1st_script.js内のselectedKeywordの値を取得して表示する
    document.getElementById("selectedKeywordDisplay").innerText = "#" + selectedKeyword;