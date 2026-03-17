document.addEventListener("DOMContentLoaded", function () {

    const startInput = document.getElementById("start_date");
    const endInput = document.getElementById("end_date");
    const leaveDaysInput = document.getElementById("leave_days");

    if (!startInput || !endInput || !leaveDaysInput) return;

    function calculateLeaveDays() {
        let startDate = startInput.value;
        let endDate = endInput.value;

        if (startDate && endDate) {
            let start = new Date(startDate);
            let end = new Date(endDate);

            let difference = end - start;
            let days = difference / (1000 * 60 * 60 * 24) + 1;

            leaveDaysInput.value = (days > 0) ? days + " day(s)" : "";
        }
    }

    startInput.addEventListener("change", function () {
        let startDate = new Date(this.value);

        let minEndDate = new Date(startDate);
        minEndDate.setDate(minEndDate.getDate() + 2);
        let minEndStr = minEndDate.toISOString().split("T")[0];

        endInput.min = minEndStr;

        if (new Date(endInput.value) < minEndDate) {
            endInput.value = minEndStr;
        }

        calculateLeaveDays();
    });

    endInput.addEventListener("change", calculateLeaveDays);

    calculateLeaveDays();
});