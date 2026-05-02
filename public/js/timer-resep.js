const recipeSteps = [
    {
        title: "Langkah 1/4: Persiapan Dasar",
        desc: "Oles roti dengan margarin di kedua sisinya secara merata. Pastikan seluruh permukaan roti terlapisi.",
        ingredients: ["Roti Tawar", "Margarin"],
        image: "https://images.unsplash.com/photo-1581574303858-f00f95088f7b?q=80&w=400",
        duration: 180 
    },
    {
        title: "Langkah 2/4: Pemanggangan",
        desc: "Panaskan pan anti lengket. Panggang roti hingga warna berubah menjadi coklat keemasan di kedua sisi.",
        ingredients: ["Roti yang sudah dioles"],
        image: "https://images.unsplash.com/photo-1525351484163-7529414344d8?q=80&w=400",
        duration: 300 
    },
    {
        title: "Langkah 3/4: Penambahan Topping",
        desc: "Taburkan keju parut selagi roti masih panas agar sedikit meleleh, lalu beri sedikit madu.",
        ingredients: ["Keju Parut", "Madu"],
        image: "https://images.unsplash.com/photo-1550617931-e17a7b70dce2?q=80&w=400",
        duration: 60 
    },
    {
        title: "Langkah 4/4: Finishing",
        desc: "Tambahkan kacang almond, coklat bubuk, dan susu bubuk sebagai sentuhan akhir. Sajikan!",
        ingredients: ["Almond", "Coklat Bubuk", "Susu Bubuk"],
        image: "https://images.unsplash.com/photo-1495147466023-ac5c588e2e94?q=80&w=400",
        duration: 30 
    }
    ];
    
    let currentIndex = 0;
    let timeLeft = 0;
    let timerInterval = null;
    let isPlaying = false;
    
    
    const stepTitle = document.getElementById('step-title');
    const stepDesc = document.getElementById('step-desc');
    const ingredientsList = document.getElementById('ingredients-list');
    const stepImg = document.getElementById('step-img');
    const timerDisplay = document.getElementById('timer-display');
    const progressCircle = document.getElementById('circle-progress');
    const btnStart = document.getElementById('btn-start');
    
    function formatTime(s) {
        const m = Math.floor(s / 60);
        const sec = s % 60;
        return `${m.toString().padStart(2, '0')}:${sec.toString().padStart(2, '0')}`;
    }
    
    function updateStepUI() {
        stopTimer();
        const data = recipeSteps[currentIndex];
        
        stepTitle.innerText = data.title;
        stepDesc.innerText = data.desc;
        stepImg.src = data.image;
        ingredientsList.innerHTML = data.ingredients.map(i => `<li>[✓] ${i}</li>`).join('');
        
        
        document.querySelectorAll('.step-circle').forEach((c, idx) => {
            c.classList.toggle('active', idx <= currentIndex);
        });
    
        
        timeLeft = data.duration;
        timerDisplay.innerText = formatTime(timeLeft);
        progressCircle.style.background = `conic-gradient(var(--primary) 0% 100%, #f0f0f0 100% 100%)`;
    }
    
    function startTimer() {
        if(isPlaying) {
            stopTimer();
        } else {
            isPlaying = true;
            btnStart.innerText = "JEDA";
            const total = recipeSteps[currentIndex].duration;
            
            timerInterval = setInterval(() => {
                if(timeLeft > 0) {
                    timeLeft--;
                    timerDisplay.innerText = formatTime(timeLeft);
                    const percent = (timeLeft / total) * 100;
                    progressCircle.style.background = `conic-gradient(var(--primary) 0% ${percent}%, #f0f0f0 ${percent}% 100%)`;
                } else {
                    stopTimer();
                    alert("Waktu habis!");
                }
            }, 1000);
        }
    }
    
    function stopTimer() {
        clearInterval(timerInterval);
        isPlaying = false;
        btnStart.innerText = "MULAI";
    }
    
    
    document.getElementById('btn-next').onclick = () => {
        if(currentIndex < recipeSteps.length - 1) { currentIndex++; updateStepUI(); }
    };
    document.getElementById('btn-prev').onclick = () => {
        if(currentIndex > 0) { currentIndex--; updateStepUI(); }
    };
    btnStart.onclick = startTimer;
    document.getElementById('btn-reset').onclick = updateStepUI;
    
    document.getElementById('btn-next').onclick = () => {
    if (currentIndex < recipeSteps.length - 1) { 
       
        currentIndex++; 
        updateStepUI(); 
    } else {
        
        const konfirmasi = confirm("Selamat! Masakan sudah selesai. Beri ulasan sekarang?");
        if (konfirmasi) {
            window.location.href = "ulasan.html"; 
        }
    }
    };
    
    updateStepUI();