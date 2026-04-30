const params = new URLSearchParams(window.location.search);
const tab = params.get("tab");

if (tab === "mengikuti") {
    document.getElementById("tab-mengikuti").checked = true;
} else {
    document.getElementById("tab-pengikut").checked = true;
}