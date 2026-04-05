const PAGE_PATH = {
    "main-menu-index": "html/main-menu.html",
    "main-menu": "main-menu.html",
    "search": "pencarian-resep.html",
    "profile": "profile.html",
    "detail-resep": "detail-resep.html",
    "search": "pencarian-resep.html",
    
}

function changePage(pageName) {
    const targetPath = PAGE_PATH[pageName];
    if (targetPath) {
        event.preventDefault()
        window.location.href = targetPath
    } else {
        throw Error("pagename tidak ditemukan didalam PAGE_PATH")
    }
}

document.querySelectorAll(".resep").forEach((resep) => {
    console.log(resep)
    resep.addEventListener('click', () => {
        changePage('detail-resep')
    })
})