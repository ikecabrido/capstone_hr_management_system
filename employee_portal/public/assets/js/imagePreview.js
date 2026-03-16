document.getElementById('profileImageInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (ev) {
            document.getElementById('profileImagePreview').src = ev.target.result;
        }
        reader.readAsDataURL(file);
    }
});

document.getElementById('coverImageInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (ev) {
            document.getElementById('coverImagePreview').src = ev.target.result;
        }
        reader.readAsDataURL(file);
    }
});