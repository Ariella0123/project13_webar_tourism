document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('poster-container');
    const img = document.getElementById('poster-img');
    const saveBtn = document.getElementById('save-btn');
    const normXInput = document.getElementById('norm_x');
    const normYInput = document.getElementById('norm_y');

    if (container && img) {
        container.addEventListener('click', function(e) {
            const rect = img.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const clickY = e.clientY - rect.top;

            // 计算相对百分比坐标 (0.0000 ~ 1.0000)
            const normX = (clickX / rect.width).toFixed(4);
            const normY = (clickY / rect.height).toFixed(4);

            normXInput.value = normX;
            normYInput.value = normY;

            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.classList.remove('btn-secondary');
                saveBtn.classList.add('btn-success');
                saveBtn.innerText = `保存热点位置 (${(normX * 100).toFixed(1)}%, ${(normY * 100).toFixed(1)}%)`;
            }
        });
    }
});