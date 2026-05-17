document.addEventListener('DOMContentLoaded', async () => {
  const selectedRasaContainer = document.getElementById('selectedRasaContainer');
  const resepContainer = document.getElementById('resepContainer');
  const resultInfoText = document.getElementById('resultInfoText');

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function formatCookDuration(duration) {
    if (!duration) return '-';
    if (String(duration).includes(':')) {
      const parts = duration.split(':');
      const hour = parseInt(parts[0]) || 0;
      const minute = parseInt(parts[1]) || 0;
      if (hour > 0) return `${hour} jam ${minute} menit`;
      return `${minute} menit`;
    }
    return `${duration} menit`;
  }

  const urlParams = new URLSearchParams(window.location.search);
  const filterQuery = urlParams.get('filters');

  if (!filterQuery) {
    renderEmptySelection();
    return;
  }

  const filterIds = filterQuery.split(',')
    .map(id => Number(id))
    .filter(id => !isNaN(id) && id > 0);

  if (filterIds.length === 0) {
    renderEmptySelection();
    return;
  }

  await fetchResep(filterIds);

  async function fetchResep(filters) {
    try {
      const url = `${window.filterSwipeConfig.apiUrl}?filters=${filters.join(',')}`;
      const res = await fetch(url);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const result = await res.json();
      
      if (!result.success) {
        renderApiError();
        return;
      }
      if (result.selected_filters && Array.isArray(result.selected_filters)) {
        renderSelectedRasaFromObjects(result.selected_filters);
      }
      renderResep(result.data || [], filters.length);
    } catch (err) {
      console.error(err);
      renderNetworkError(err);
    }
  }

  function renderSelectedRasaFromObjects(selectedFilters) {
    if (!selectedRasaContainer) return;
    selectedRasaContainer.innerHTML = selectedFilters.map(filter => `
      <div class="selected-chip">
        ❤️ ${escapeHtml(filter.title)}
      </div>
    `).join('');
  }

  function renderResep(data, totalUserSelection) {
    if (!Array.isArray(data) || data.length === 0) {
      resepContainer.innerHTML = `
        <div class="empty-result">
          <h2>Tidak ada resep cocok</h2>
          <p>Coba kombinasi rasa lain</p>
        </div>
      `;
      resultInfoText.innerText = '0 resep ditemukan';
      return;
    }

    resultInfoText.innerText = `${data.length} resep ditemukan`;

    resepContainer.innerHTML = data.map(resep => {
      const chipsHtml = resep.filters?.map(filter => `
        <span class="resep-rasa-chip">
          ❤️ ${escapeHtml(filter.title)}
        </span>
      `).join('') || '';

      const imageUrl = resep.thumbnail ? escapeHtml(resep.thumbnail) : '/images/default-food.jpg';

      return `
        <div class="resep-card">
          <div class="resep-thumbnail">
            <img src="${imageUrl}" alt="${escapeHtml(resep.title)}" loading="lazy">
            <div class="cocok-badge">
              🔥 Cocok ${escapeHtml(String(resep.match_count ?? 0))}/${totalUserSelection} Rasa
            </div>
          </div>
          <div class="resep-content">
            <h3>${escapeHtml(resep.title || 'Tanpa Judul')}</h3>
            <div class="resep-meta">
              <div class="meta-pill">
                <span class="material-icons-round">schedule</span>
                <span>${formatCookDuration(resep.cook_duration)}</span>
              </div>
              <div class="meta-pill meta-pill-rating">
                <span class="material-icons-round">star</span>
                <span>${escapeHtml(String(resep.current_star ?? 0))}</span>
              </div>
              <div class="meta-pill meta-pill-orange">
                <span class="material-icons-round">visibility</span>
                <span>${escapeHtml(String(resep.views_count ?? 0))}</span>
              </div>
            </div>
            ${chipsHtml ? `
              <div class="resep-rasa-wrapper">
                <span class="resep-rasa-label">Rasa pada resep ini</span>
                <div class="resep-rasa-list">
                  ${chipsHtml}
                </div>
              </div>
            ` : ''}
            <div class="resep-author">
              <span class="material-icons-round">person</span>
              <span>${escapeHtml(resep.user?.name || 'Unknown')}</span>
            </div>
          </div>
        </div>
      `;
    }).join('');
  }

  function renderEmptySelection() {
    resepContainer.innerHTML = `
      <div class="empty-result">
        <h2>Belum ada rasa dipilih</h2>
        <p>Silakan swipe terlebih dahulu</p>
      </div>
    `;
    resultInfoText.innerText = 'Belum ada rekomendasi';
  }

  function renderApiError() {
    resepContainer.innerHTML = `
      <div class="empty-result">
        <h2>Gagal mengambil data</h2>
        <p>Server tidak mengembalikan data</p>
      </div>
    `;
    resultInfoText.innerText = 'Gagal memuat';
  }

  function renderNetworkError(err) {
    resepContainer.innerHTML = `
      <div class="empty-result">
        <h2>Network Error</h2>
        <p>${escapeHtml(err.message)}</p>
      </div>
    `;
    resultInfoText.innerText = 'Network error';
  }
});