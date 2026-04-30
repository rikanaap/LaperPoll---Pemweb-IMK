 const dropdownSelect = document.querySelector('.unit-select');
          const amountSpans = document.querySelectorAll('.amt');

          amountSpans.forEach(span => {
              const baseValue = parseFloat(span.innerText.replace('g', '').trim());
              span.dataset.baseGram = baseValue; 
          });
        
          dropdownSelect.addEventListener('change', function() {
              const satuanPilihan = this.value;
              amountSpans.forEach(span => {
                  
                  const baseGram = parseFloat(span.dataset.baseGram);
                  let hasilHitung = 0;
                  let simbolSatuan = '';
      
                  if (satuanPilihan === 'gram') {
                    hasilHitung = baseGram;
                    simbolSatuan = 'g';
                  } else if (satuanPilihan === 'mililiter') {
                    hasilHitung = baseGram; 
                    simbolSatuan = 'ml';
                  } else if (satuanPilihan === 'sendok_makan') {
                     hasilHitung = baseGram / 15; 
                     simbolSatuan = ' sdm';
                  } else if (satuanPilihan === 'kilogram') {
                    hasilHitung = baseGram / 1000;
                    simbolSatuan = 'kg';
                }

                  span.innerText = `${hasilHitung}${simbolSatuan}`;
              });
          });