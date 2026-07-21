document.querySelectorAll('.icon-select').forEach(button => {
    button.addEventListener('click', function () {

        const icon = this.dataset.icon;

        document.getElementById('selectedIcon').value = icon;

        const preview = document.getElementById('iconPreview');
        preview.className = 'fa-solid ' + icon + ' fa-lg';
    });
});

document.querySelectorAll('.edit-modal').forEach(modal => {
    const typeId = modal.dataset.typeId;

    const iconInput = modal.querySelector(`#selectedIcon${typeId}`);
    const iconPreview = modal.querySelector(`#iconPreview${typeId}`);

    modal.querySelectorAll('.icon-select').forEach(icon => {
        icon.addEventListener('click', () => {
            const iconClass = icon.dataset.icon;

            iconInput.value = iconClass;

            iconPreview.className = 'fa-solid ' + iconClass + ' fa-lg';
        });
    });
});