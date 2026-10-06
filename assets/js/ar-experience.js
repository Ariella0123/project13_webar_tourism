document.addEventListener('DOMContentLoaded', function() {
    const sceneEl = document.querySelector('a-scene');
    const statusText = document.getElementById('ar-status-text');

    if (sceneEl) {
        sceneEl.addEventListener('targetFound', function() {
            if (statusText) {
                statusText.innerText = "已检测到海报：正在呈现 AR 内容";
                statusText.className = "ar-status-badge bg-success";
            }
        });

        sceneEl.addEventListener('targetLost', function() {
            if (statusText) {
                statusText.innerText = "未检测到海报：请对准宣传海报";
                statusText.className = "ar-status-badge bg-warning text-dark";
            }
        });
    }
});