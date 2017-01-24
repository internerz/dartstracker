function toLocalStorage(name, item) {
    localStorage.setItem(name, JSON.stringify(item));
}

function fromLocalStorage(name) {
    return JSON.parse(localStorage.getItem('gameInfo'));
}
