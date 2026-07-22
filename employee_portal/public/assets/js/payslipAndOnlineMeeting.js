function printDiv(divId) {
    const divContents = document.getElementById(divId).innerHTML;

    const printWindow = window.open('', '', 'height=600,width=900');
    printWindow.document.write('<html><head><title>Print</title>');

    const styles = Array.from(document.querySelectorAll('link[rel="stylesheet"], style'))
        .map(el => el.outerHTML)
        .join('');
    printWindow.document.write(styles);

    printWindow.document.write('</head><body>');
    printWindow.document.write(divContents);
    printWindow.document.write('</body></html>');

    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}

function copyLink(link) {
    navigator.clipboard.writeText(link).then(function () {

        const toast = document.getElementById('copyToast');

        toast.style.display = 'block';
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.5s ease';

        setTimeout(() => {
            toast.style.opacity = '1';
        }, 10);

        setTimeout(() => {
            toast.style.opacity = '0';

            setTimeout(() => {
                toast.style.display = 'none';
            }, 500);
        }, 4000);

    }, function () {
        alert("Failed to copy link.");
    });
}