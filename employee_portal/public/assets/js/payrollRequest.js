document.addEventListener('DOMContentLoaded', function () {

    const searchInput =
        document.getElementById('payrollSearch');

    const table =
        document.getElementById('payrollRequestTable');

    if (searchInput && table) {

        searchInput.addEventListener('keyup', function () {

            const search =
                this.value.toLowerCase().trim();

            const rows =
                table.querySelectorAll('tbody tr');

            rows.forEach(function (row) {

                if (row.id === 'emptyPayrollRow') {
                    return;
                }

                const text =
                    row.textContent.toLowerCase();

                row.style.display =
                    text.includes(search) ?
                        '' :
                        'none';

            });

        });

    }


    // Prevent invalid payroll period
    const startDate =
        document.getElementById('payroll_period_start');

    const endDate =
        document.getElementById('payroll_period_end');

    if (startDate && endDate) {

        startDate.addEventListener('change', function () {

            endDate.min = this.value;

        });

    }

});
