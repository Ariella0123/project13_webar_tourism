<?php

require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'AR Experience';
$posters = [];
$databaseError = null;

try {
    $posters = db()->query(
        "SELECT id, name, description, youtube_url, target_status
         FROM ar_posters
         WHERE status = 'active'
         ORDER BY name"
    )->fetchAll();
} catch (PDOException $exception) {
    $databaseError = 'The AR poster library is not available right now.';
}

require __DIR__ . '/includes/header.php';
?>

<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/mind-ar@1.2.2/dist/mindar-image.prod.css"
>

<section class="ar-shell">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="text-center mb-5">
                    <span class="eyebrow">Immersive tourism</span>
                    <h1 class="display-4 fw-bold mt-2">Explore with augmented reality</h1>
                    <p class="lead text-secondary mx-auto" style="max-width: 680px">
                        Choose a ready AR poster, point your camera at it and discover
                        stories, places and videos hidden inside the image.
                    </p>
                </div>

                <?php if ($databaseError): ?>
                    <div class="alert alert-warning shadow-sm">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        <?= e($databaseError) ?>
                        Start MySQL in XAMPP and try again.
                    </div>
                <?php endif; ?>

                <?php
                $isLocalhost = in_array(
                    $_SERVER['HTTP_HOST'] ?? '',
                    ['localhost', '127.0.0.1'],
                    true
                );
                ?>

                <?php if (!isset($_SERVER['HTTPS']) && !$isLocalhost): ?>
                    <div class="alert alert-warning shadow-sm">
                        <i class="fa-solid fa-lock me-2"></i>
                        Camera access requires HTTPS. Use
                        <strong>localhost</strong> during local development.
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-lg overflow-hidden">
                    <div class="card-body p-4 p-lg-5">
                        <div class="row g-4 align-items-end">
                            <div class="col-lg-8">
                                <label class="form-label fw-semibold" for="poster">
                                    Choose an AR poster
                                </label>
                                <select id="poster" class="form-select form-select-lg">
                                    <option value="">Select a poster</option>
                                    <?php foreach ($posters as $poster): ?>
                                        <option
                                            value="<?= url(
                                                'api/ar/poster.php?id=' . (int) $poster['id']
                                            ) ?>"
                                            data-ready="<?= e($poster['target_status']) ?>"
                                        >
                                            <?= e($poster['name']) ?>
                                            —
                                            <?= e($poster['target_status']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-lg-4 d-flex gap-2">
                                <button
                                    id="startAr"
                                    class="btn btn-warning btn-lg flex-grow-1"
                                    type="button"
                                    disabled
                                >
                                    <i class="fa-solid fa-camera me-2"></i>
                                    Start AR
                                </button>
                                <button
                                    id="stopAr"
                                    class="btn btn-outline-secondary btn-lg d-none"
                                    type="button"
                                >
                                    <i class="fa-solid fa-stop"></i>
                                </button>
                            </div>
                        </div>

                        <div
                            id="arMessage"
                            class="alert alert-light border mt-4 mb-0"
                            role="status"
                            aria-live="polite"
                        >
                            Select a compiled poster to begin.
                        </div>
                    </div>
                </div>

                <div
                    id="arViewport"
                    class="position-relative rounded-4 overflow-hidden mt-4 d-none"
                    style="min-height: 55vh; background: #102a2d"
                >
                    <div id="mindarContainer" class="w-100 h-100"></div>

                    <div
                        id="arContent"
                        class="ar-card position-absolute top-0 end-0 m-3 d-none text-start"
                        style="max-width: 340px; z-index: 10"
                    >
                        <span class="eyebrow">Point found</span>
                        <h2 id="arName" class="h4 mt-2 mb-2"></h2>
                        <p id="arDescription" class="mb-3"></p>
                        <div class="d-flex flex-wrap gap-2">
                            <a
                                id="arMap"
                                class="btn btn-sm btn-success d-none"
                                target="_blank"
                                rel="noopener"
                            >
                                <i class="fa-solid fa-map me-1"></i>
                                View map
                            </a>
                            <a
                                id="arVideo"
                                class="btn btn-sm btn-outline-danger d-none"
                                target="_blank"
                                rel="noopener"
                            >
                                <i class="fa-brands fa-youtube me-1"></i>
                                Watch video
                            </a>
                        </div>
                    </div>

                    <div
                        id="arScanning"
                        class="position-absolute top-50 start-50 translate-middle text-white text-center"
                        style="z-index: 5"
                    >
                        <i class="fa-solid fa-crosshairs fa-2x mb-2"></i>
                        <p class="mb-0">Point your camera at the poster</p>
                    </div>
                </div>

                <div class="row g-3 mt-4">
                    <div class="col-md-4">
                        <div class="d-flex gap-3 align-items-start">
                            <span class="brand-mark flex-shrink-0">1</span>
                            <div>
                                <h3 class="h6">Choose a poster</h3>
                                <p class="small text-secondary mb-0">
                                    Only compiled posters can start AR tracking.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-3 align-items-start">
                            <span class="brand-mark flex-shrink-0">2</span>
                            <div>
                                <h3 class="h6">Allow camera access</h3>
                                <p class="small text-secondary mb-0">
                                    Use HTTPS or localhost for browser camera permission.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-3 align-items-start">
                            <span class="brand-mark flex-shrink-0">3</span>
                            <div>
                                <h3 class="h6">Scan and discover</h3>
                                <p class="small text-secondary mb-0">
                                    Keep the poster visible until the hotspot appears.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/mind-ar@1.2.2/dist/mindar-image-three.prod.js"></script>
<script>
    (() => {
        const posterSelect = document.querySelector('#poster');
        const startButton = document.querySelector('#startAr');
        const stopButton = document.querySelector('#stopAr');
        const message = document.querySelector('#arMessage');
        const viewport = document.querySelector('#arViewport');
        const content = document.querySelector('#arContent');
        const scanning = document.querySelector('#arScanning');
        const nameElement = document.querySelector('#arName');
        const descriptionElement = document.querySelector('#arDescription');
        const mapLink = document.querySelector('#arMap');
        const videoLink = document.querySelector('#arVideo');

        let mindar = null;
        let renderer = null;
        let animationFrame = null;

        function setMessage(text, type = 'light') {
            message.className = `alert alert-${type} border mt-4 mb-0`;
            message.textContent = text;
        }

        function resetContent() {
            content.classList.add('d-none');
            scanning.classList.remove('d-none');
            mapLink.classList.add('d-none');
            videoLink.classList.add('d-none');
            mapLink.removeAttribute('href');
            videoLink.removeAttribute('href');
        }

        function showHotspot(hotspot, poster) {
            const title = hotspot.attraction_name || hotspot.label || poster.name;
            const description =
                hotspot.short_description ||
                poster.description ||
                'Explore this attraction through AR Tourism.';
            const mapUrl = hotspot.maps_url || '';
            const youtubeUrl = hotspot.youtube_url || poster.youtube_url || '';

            nameElement.textContent = title;
            descriptionElement.textContent = description;
            content.classList.remove('d-none');
            scanning.classList.add('d-none');

            if (mapUrl) {
                mapLink.href = mapUrl;
                mapLink.classList.remove('d-none');
            }

            if (youtubeUrl) {
                videoLink.href = youtubeUrl;
                videoLink.classList.remove('d-none');
            }
        }

        function stopExperience() {
            if (animationFrame) {
                cancelAnimationFrame(animationFrame);
                animationFrame = null;
            }

            if (mindar) {
                mindar.stop();
                mindar = null;
            }

            if (renderer) {
                renderer.dispose();
                renderer.domElement.remove();
                renderer = null;
            }

            viewport.classList.add('d-none');
            startButton.classList.remove('d-none');
            stopButton.classList.add('d-none');
            startButton.disabled = !posterSelect.value;
            resetContent();
        }

        posterSelect.addEventListener('change', () => {
            const option = posterSelect.selectedOptions[0];
            const isReady = option && option.dataset.ready === 'READY';

            stopExperience();
            startButton.disabled = !posterSelect.value || !isReady;

            if (!posterSelect.value) {
                setMessage('Select a compiled poster to begin.', 'light');
            } else if (!isReady) {
                setMessage(
                    'This poster has not been compiled yet. Ask an administrator to prepare its .mind target file.',
                    'warning'
                );
            } else {
                setMessage('Ready to start. Allow camera access when prompted.', 'success');
            }
        });

        startButton.addEventListener('click', async () => {
            if (!posterSelect.value) {
                return;
            }

            startButton.disabled = true;
            setMessage('Loading poster data and camera tracking...', 'info');

            try {
                const response = await fetch(posterSelect.value, {
                    headers: {Accept: 'application/json'}
                });
                const data = await response.json();

                if (!response.ok || !data.poster) {
                    throw new Error(data.error || 'Poster data could not be loaded.');
                }

                if (!data.poster.target_url) {
                    throw new Error(
                        'This poster has no compiled target file. Ask an administrator to compile it.'
                    );
                }

                if (!window.MINDAR || !window.THREE) {
                    throw new Error('The AR library could not be loaded.');
                }

                viewport.classList.remove('d-none');
                stopButton.classList.remove('d-none');
                startButton.classList.add('d-none');
                resetContent();

                mindar = new MINDAR.IMAGE.MindARThree({
                    container: document.querySelector('#mindarContainer'),
                    imageTargetSrc: data.poster.target_url,
                    uiLoading: 'yes',
                    uiScanning: 'no',
                    uiError: 'yes'
                });

                const {renderer: arRenderer, scene, camera} = mindar;
                renderer = arRenderer;

                const anchor = mindar.addAnchor(0);
                const marker = new THREE.Mesh(
                    new THREE.PlaneGeometry(1, 0.6),
                    new THREE.MeshBasicMaterial({
                        color: 0xf2b84b,
                        transparent: true,
                        opacity: 0.12
                    })
                );
                anchor.group.add(marker);

                anchor.onTargetFound = () => {
                    showHotspot(data.hotspots[0] || {}, data.poster);
                    setMessage('Poster detected. Explore the AR information card.', 'success');
                };

                anchor.onTargetLost = () => {
                    resetContent();
                    setMessage('Poster lost. Point the camera at the poster again.', 'info');
                };

                await mindar.start();
                setMessage('AR is running. Point your camera at the selected poster.', 'success');

                const renderLoop = () => {
                    if (!mindar) {
                        return;
                    }

                    animationFrame = requestAnimationFrame(renderLoop);
                    renderer.render(scene, camera);
                };

                renderLoop();
            } catch (error) {
                stopExperience();
                setMessage(
                    error.message || 'Camera access was denied or AR could not start.',
                    'danger'
                );
            }
        });

        stopButton.addEventListener('click', () => {
            stopExperience();
            setMessage('AR stopped. Select a poster to start again.', 'light');
        });
    })();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
