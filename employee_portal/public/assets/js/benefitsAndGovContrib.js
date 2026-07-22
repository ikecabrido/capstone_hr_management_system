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

document.addEventListener('DOMContentLoaded', function () {

    const editModal = document.getElementById('editBenefitModal');

    editModal.addEventListener('show.bs.modal', function (event) {

        const button = event.relatedTarget;

        document.getElementById('editBenefitId').value =
            button.getAttribute('data-id');

        document.getElementById('editEmployee').value =
            button.getAttribute('data-employee');

        document.getElementById('editRecordType').value =
            button.getAttribute('data-record');

        document.getElementById('editPeriod').value =
            button.getAttribute('data-period');

        document.getElementById('editDescription').value =
            button.getAttribute('data-description');

    });

});