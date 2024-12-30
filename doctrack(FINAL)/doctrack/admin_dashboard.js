function showMoreRows(tableId) {
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tr');
    let rowsRevealed = 0;
    for (let i = 0; i < rows.length; i++) {
        if (rows[i].classList.contains('hidden-row')) {
            rows[i].classList.remove('hidden-row');
            rowsRevealed++;
        }
        if (rowsRevealed >= 10) {
            break;
        }
    }
    const button = document.getElementById(`showMore${capitalizeFirstLetter(tableId)}Btn`);
    if (areAllRowsVisible(table)) {
        button.style.display = 'none';
    }
}
function areAllRowsVisible(table) {
    const rows = table.getElementsByTagName('tr');
    let hiddenRows = 0;
    for (let i = 0; i < rows.length; i++) {
        if (rows[i].classList.contains('hidden-row')) {
            hiddenRows++;
        }
    }
    return hiddenRows === 0;
}
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}
