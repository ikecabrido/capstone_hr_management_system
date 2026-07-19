document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('viewBenefitModal');

    modal.addEventListener('show.bs.modal', function (event) {

        const button = event.relatedTarget;

        document.getElementById('viewEmployee').textContent =
            button.getAttribute('data-employee');

        document.getElementById('viewRecordType').textContent =
            button.getAttribute('data-record');

        document.getElementById('viewPeriod').textContent =
            button.getAttribute('data-period');

        document.getElementById('viewDescription').textContent =
            button.getAttribute('data-description');

        document.getElementById('viewUploadedBy').textContent =
            button.getAttribute('data-uploadedby');

        document.getElementById('viewUploadedAt').textContent =
            button.getAttribute('data-uploadedat');

        document.getElementById('viewFile').href =
            button.getAttribute('data-file');

    });

});

const file = button.getAttribute('data-file');

const preview = document.getElementById('currentFilePreview');

preview.innerHTML = "";

if (file) {

    const extension = file.split('.').pop().toLowerCase();

    if (['jpg', 'jpeg', 'png'].includes(extension)) {

        preview.innerHTML = `
            <img src="${file}"
                 class="img-fluid rounded"
                 style="max-height:220px;">
        `;

    } else if (extension === 'pdf') {

        preview.innerHTML = `
            <iframe
                src="${file}"
                width="100%"
                height="250"
                style="border:none;">
            </iframe>
        `;

    } else {

        preview.innerHTML = `
            <a href="${file}" target="_blank" class="btn btn-info btn-sm">
                <i class="fas fa-file"></i>
                View Current File
            </a>
        `;
    }

}