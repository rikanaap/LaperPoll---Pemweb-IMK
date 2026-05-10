/**
 * ======================================
 * FILTER RESEP SWIPE
 * ======================================
 */

document.addEventListener("DOMContentLoaded", () => {

    // ======================================
    // ELEMENT
    // ======================================

    const rasaContainer =
        document.getElementById("selectedRasaContainer");

    // ======================================
    // GET DATA FROM SESSION STORAGE
    // ======================================

    const savedRasa =
        sessionStorage.getItem("selectedRasa");

    // ======================================
    // VALIDATION
    // ======================================

    if (!savedRasa || !rasaContainer) {
        return;
    }

    // ======================================
    // PARSE DATA
    // ======================================

    const parsedRasa =
        JSON.parse(savedRasa);

    // ======================================
    // EMPTY CHECK
    // ======================================

    if (!Array.isArray(parsedRasa) ||
        parsedRasa.length === 0) {

        rasaContainer.innerHTML = `
            <p class="empty-history">
                Belum ada pilihan rasa
            </p>
        `;

        return;
    }

    // ======================================
    // RENDER CHIPS
    // ======================================

    rasaContainer.innerHTML =
        parsedRasa.map(item => `

            <div class="chip">

                <span>
                    ${item.title}
                </span>

            </div>

        `).join('');

});