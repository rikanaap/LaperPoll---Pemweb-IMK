const PAGE_PATH = {
    "main-menu": "main-menu.html",
    "search": "pencarian-resep.html"
}

export function changePage(pageName){
    const targetPath = PAGE_PATH[pageName];
    if(targetPath){
        window.location.href = targetPath
    }else{
        throw Error("pagename tidak ditemukan didalam PAGE_PATH")
    }
}