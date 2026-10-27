// Tema claro/escuro
function aplicarTemaSalvo() {
    const tema = localStorage.getItem('temaFindWare');
    if (tema === 'escuro') {
        document.body.setAttribute('data-tema', 'escuro');
    }
}

function trocarTema() {
    const atual = localStorage.getItem('temaFindWare');
    if (atual === 'escuro') {
        localStorage.setItem('temaFindWare', 'claro');
        document.body.removeAttribute('data-tema');
    } else {
        localStorage.setItem('temaFindWare', 'escuro');
        document.body.setAttribute('data-tema', 'escuro');
    }
}

// Menu lateral (mobile)
function alternarMenuMobile() {
    document.getElementById('sidebar')?.classList.toggle('ativa');
    document.getElementById('overlayMenu')?.classList.toggle('ativo');
}

document.addEventListener('DOMContentLoaded', aplicarTemaSalvo);
