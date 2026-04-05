const timeInput = document.getElementById('timeInput');
const hourBox = document.getElementById('hours');
const minBox = document.getElementById('minutes');
const secBox = document.getElementById('seconds');

timeInput.addEventListener('input', (e) => {
  const timeValue = e.target.value; // Formatnya "HH:mm" atau "HH:mm:ss"
  const parts = timeValue.split(':');

  // Update teks di dalam kotak
  if (parts.length >= 2) {
    hourBox.textContent = parts[0].padStart(2, '0');
    minBox.textContent = parts[1].padStart(2, '0');
    // Jika input time mendukung detik (step="1")
    secBox.textContent = parts[2] ? parts[2].padStart(2, '0') : "00";
  }
});