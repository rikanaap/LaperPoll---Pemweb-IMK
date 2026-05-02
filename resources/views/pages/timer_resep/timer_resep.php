<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/global.css">
    <link rel="stylesheet" href="../styles/atomic.css">
    <link rel="stylesheet" href="../styles/pages/timer-resep.css">
    <title>Laperpoll</title>
</head>
<body>
    <div class="app-container">
        <main class="instruction-card">
            <header class="header-step">
                <h1 id="step-title" class="font-jakarta">Memuat...</h1>
            </header>

            <div class="step-content">
                <div class="step-text-zone">
                    <div class="info-group">
                        <h2 class="info-label"><span class="material-icons-round">receipt_long</span> Tahap</h2>
                        <p id="step-desc" class="info-detail">Deskripsi sedang dimuat...</p>
                    </div>
                    
                    <hr class="divider">

                    <div class="info-group">
                        <h2 class="info-label"><span class="material-icons-round">menu_book</span> Bahan</h2>
                        <ul id="ingredients-list" class="ingredients-list">
                            </ul>
                    </div>
                </div>

                <div class="step-image-zone">
                    <img id="step-img" src="https://images.unsplash.com/photo-1581574303858-f00f95088f7b?q=80&w=400" alt="Langkah Masak" class="recipe-img">
                </div>
            </div>
        </main>

        <aside class="control-card">
            <div class="stepper">
                <div class="step-circle" data-step="1">1</div>
                <div class="step-line"></div>
                <div class="step-circle" data-step="2">2</div>
                <div class="step-line"></div>
                <div class="step-circle" data-step="3">3</div>
                <div class="step-line"></div>
                <div class="step-circle" data-step="4">4</div>
            </div>

            <div class="nav-wrapper">
                <div class="nav-item">
                    <button id="btn-prev" class="btn-round"><span class="material-icons-round">arrow_back</span></button>
                    <span>Sebelumnya</span>
                </div>
                <div class="nav-item">
                    <button id="btn-next" class="btn-round"><span class="material-icons-round">arrow_forward</span></button>
                    <span id="next-label">Lanjut</span>
                </div>
            </div>

            <div id="circle-progress" class="timer-circle">
                <span id="timer-display" class="time-text">00:00</span>
            </div>

            <div class="timer-actions">
                <button id="btn-start" class="btn-outline">MULAI</button>
                <button id="btn-reset" class="btn-outline">RESET</button>
            </div>
        </aside>
    </div>

    <script src="../js/timer-resep.js"></script>
</body>
</html>