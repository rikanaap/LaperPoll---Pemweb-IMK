document.addEventListener("DOMContentLoaded", () => {

    const rasaContainer =
        document.getElementById("selectedRasaContainer");

    const savedRasa =
        sessionStorage.getItem("selectedRasa");

    if (!savedRasa || !rasaContainer) {
        return;
    }

    const parsedRasa =
        JSON.parse(savedRasa);

    if (!Array.isArray(parsedRasa) ||
        parsedRasa.length === 0) {

        rasaContainer.innerHTML = `
            <p class="empty-history">
                Belum ada pilihan rasa
            </p>
        `;

        return;
    }

    rasaContainer.innerHTML =
        parsedRasa.map(item => `

            <div class="chip">

                <span>
                    ${item.title}
                </span>

            </div>

        `).join('');

});