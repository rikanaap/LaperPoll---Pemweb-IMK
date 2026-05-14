// timer-resep.js — redesign

const recipeSteps = [
  {
    title: "Langkah 1: Persiapan Dasar",
    desc: "Oles roti dengan margarin di kedua sisinya secara merata. Pastikan seluruh permukaan roti terlapisi agar hasil panggang lebih sempurna dan tidak kering.",
    ingredients: ["Roti Tawar", "Margarin"],
    image: "https://images.unsplash.com/photo-1581574303858-f00f95088f7b?q=80&w=400",
    duration: 180
  },
  {
    title: "Langkah 2: Pemanggangan",
    desc: "Panaskan pan anti lengket di atas api sedang. Panggang roti hingga warna berubah menjadi coklat keemasan di kedua sisi.",
    ingredients: ["Roti yang sudah dioles"],
    image: "https://images.unsplash.com/photo-1525351484163-7529414344d8?q=80&w=400",
    duration: 300
  },
  {
    title: "Langkah 3: Penambahan Topping",
    desc: "Taburkan keju parut selagi roti masih panas agar sedikit meleleh, lalu beri sedikit madu untuk sentuhan manis yang alami.",
    ingredients: ["Keju Parut", "Madu"],
    image: "https://images.unsplash.com/photo-1550617931-e17a7b70dce2?q=80&w=400",
    duration: 60
  },
  {
    title: "Langkah 4: Finishing & Sajikan",
    desc: "Tambahkan kacang almond cincang, coklat bubuk, dan susu bubuk sebagai sentuhan akhir. Sajikan selagi hangat.",
    ingredients: ["Almond Cincang", "Coklat Bubuk", "Susu Bubuk"],
    image: "https://images.unsplash.com/photo-1495147466023-ac5c588e2e94?q=80&w=400",
    duration: 30
  }
];

let currentIndex = 0;
let timeLeft     = 0;
let totalTime    = 0;
let timerInterval = null;
let isPlaying    = false;

// ── DOM refs ──
const stepTitle       = document.getElementById('step-title');
const stepDesc        = document.getElementById('step-desc');
const ingredientsList = document.getElementById('ingredients-list');
const stepImg         = document.getElementById('step-img');
const timerDisplay    = document.getElementById('timer-display');
const progressCircle  = document.getElementById('circle-progress');
const btnStart        = document.getElementById('btn-start');

// ── Buat SVG ring di dalam .timer-circle ──
function buildRingSVG() {
  const size   = 180;
  const stroke = 12;
  const r      = (size - stroke) / 2;
  const circ   = 2 * Math.PI * r;
  const cx     = size / 2;

  const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
  svg.setAttribute('width',  size);
  svg.setAttribute('height', size);
  svg.setAttribute('viewBox', `0 0 ${size} ${size}`);

  // Track (abu)
  const track = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
  track.setAttribute('cx', cx);
  track.setAttribute('cy', cx);
  track.setAttribute('r',  r);
  track.setAttribute('fill', 'none');
  track.setAttribute('stroke', '#F0E8E0');
  track.setAttribute('stroke-width', stroke);

  // Progress (oranye)
  const progress = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
  progress.setAttribute('cx', cx);
  progress.setAttribute('cy', cx);
  progress.setAttribute('r',  r);
  progress.setAttribute('fill', 'none');
  progress.setAttribute('stroke', '#E65100');
  progress.setAttribute('stroke-width', stroke);
  progress.setAttribute('stroke-linecap', 'round');
  progress.setAttribute('stroke-dasharray', circ);
  progress.setAttribute('stroke-dashoffset', 0);
  progress.id = 'ring-progress';
  progress.style.transition = 'stroke-dashoffset 0.9s linear';

  svg.appendChild(track);
  svg.appendChild(progress);
  progressCircle.prepend(svg);

  return { progress, circ };
}

const { progress: ringEl, circ: ringCirc } = buildRingSVG();

// ── Helpers ──
function formatTime(s) {
  const m   = Math.floor(s / 60);
  const sec = s % 60;
  return `${m.toString().padStart(2,'0')}:${sec.toString().padStart(2,'0')}`;
}

function setRingProgress(fraction) {
  // fraction: 1 = penuh, 0 = kosong
  const offset = ringCirc * (1 - fraction);
  ringEl.setAttribute('stroke-dashoffset', offset);
}

// ── Update stepper ──
function updateStepper() {
  const circles = document.querySelectorAll('.step-circle');
  const lines   = document.querySelectorAll('.step-line');

  circles.forEach((c, i) => {
    c.classList.remove('active', 'done');
    if (i < currentIndex)  c.classList.add('done');
    if (i === currentIndex) c.classList.add('active');
  });

  lines.forEach((l, i) => {
    l.classList.toggle('done', i < currentIndex);
  });
}

// ── Render step ──
function updateStepUI() {
  stopTimer();
  const data = recipeSteps[currentIndex];

  // Fade out → in untuk gambar
  stepImg.style.opacity = '0';
  stepImg.style.transform = 'scale(0.97)';
  setTimeout(() => {
    stepImg.src = data.image;
    stepImg.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
    stepImg.style.opacity = '1';
    stepImg.style.transform = 'scale(1)';
  }, 150);

  stepTitle.innerText = data.title;
  stepDesc.innerText  = data.desc;

  ingredientsList.innerHTML = data.ingredients
    .map(i => `<li>${i}</li>`)
    .join('');

  // Update tombol next label
  const nextLabel = document.getElementById('next-label');
  nextLabel.innerText = currentIndex === recipeSteps.length - 1 ? 'Selesai' : 'Lanjut';

  updateStepper();

  totalTime = data.duration;
  timeLeft  = data.duration;
  timerDisplay.innerText = formatTime(timeLeft);
  setRingProgress(1); // ring penuh
}

// ── Timer ──
function startTimer() {
  if (isPlaying) {
    // Jeda
    clearInterval(timerInterval);
    isPlaying = false;
    btnStart.innerText = 'LANJUT';
    return;
  }

  isPlaying = true;
  btnStart.innerText = 'JEDA';

  timerInterval = setInterval(() => {
    if (timeLeft > 0) {
      timeLeft--;
      timerDisplay.innerText = formatTime(timeLeft);
      setRingProgress(timeLeft / totalTime);
    } else {
      stopTimer();
      // Notifikasi selesai tanpa alert bawaan browser
      timerDisplay.innerText = '00:00';
      btnStart.innerText = 'SELESAI ✓';
      btnStart.style.background = '#2D6A27';
      btnStart.style.borderColor = '#2D6A27';
    }
  }, 1000);
}

function stopTimer() {
  clearInterval(timerInterval);
  isPlaying = false;
  btnStart.innerText = 'MULAI';
  btnStart.style.background = '';
  btnStart.style.borderColor = '';
}

// ── Event listeners ──

document.getElementById('btn-prev').onclick = () => {
  if (currentIndex > 0) {
    currentIndex--;
    updateStepUI();
  }
};

document.getElementById('btn-next').onclick = () => {
  if (currentIndex < recipeSteps.length - 1) {
    currentIndex++;
    updateStepUI();
  } else {
    const konfirmasi = confirm('Selamat! Masakan sudah selesai 🎉\nBeri ulasan sekarang?');
    if (konfirmasi) window.location.href = '/ulasan';
  }
};

btnStart.onclick = startTimer;

document.getElementById('btn-reset').onclick = () => {
  stopTimer();
  timeLeft = totalTime;
  timerDisplay.innerText = formatTime(timeLeft);
  setRingProgress(1);
};


// ── Init ──
updateStepUI();