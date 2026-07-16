document.getElementById('selectAllEmployees').addEventListener('click', function () {

    const select = document.getElementById('employeeSelect');

    for (let option of select.options) {
        option.selected = true;
    }

});

document.getElementById('clearEmployees').addEventListener('click', function () {

    const select = document.getElementById('employeeSelect');

    for (let option of select.options) {
        option.selected = false;
    }

});

