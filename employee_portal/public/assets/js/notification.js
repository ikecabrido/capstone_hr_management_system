document.querySelectorAll(".viewBtn").forEach(button => {

    button.addEventListener("click", async function () {

        const id = this.dataset.id;

        const response = await fetch(
            `index.php?url=notification-view&id=${id}`
        );

        const data = await response.json();

        if (!data.success) return;

        const notification = data.notification;

        document.getElementById("viewTitle").textContent =
            notification.title;

        document.getElementById("viewMessage").textContent =
            notification.message;

        document.getElementById("viewCreated").textContent =
            notification.created_at;

        //------------------------------------------------
        // Type Badge
        //------------------------------------------------

        const typeBadge = document.getElementById("viewType");

        const typeClass = {
            announcement: "bg-primary",
            payroll: "bg-success",
            leave: "bg-warning text-dark",
            training: "bg-info text-dark",
            performance: "bg-secondary",
            document: "bg-dark",
            meeting: "bg-info",
            compliance: "bg-danger",
            general: "bg-secondary"
        };

        typeBadge.className =
            "badge " +
            (typeClass[notification.type] || "bg-secondary");

        typeBadge.textContent =
            notification.type.charAt(0).toUpperCase() +
            notification.type.slice(1);

        //------------------------------------------------
        // Priority Badge
        //------------------------------------------------

        const priorityBadge =
            document.getElementById("viewPriority");

        const priorityClass = {
            normal: "bg-success",
            important: "bg-warning text-dark",
            urgent: "bg-danger"
        };

        priorityBadge.className =
            "badge " +
            (priorityClass[notification.priority] || "bg-secondary");

        priorityBadge.textContent =
            notification.priority.charAt(0).toUpperCase() +
            notification.priority.slice(1);

        //------------------------------------------------
        // Recipients
        //------------------------------------------------

        const tbody =
            document.getElementById("viewRecipients");

        tbody.innerHTML = "";

        if (data.recipients.length === 0) {

            tbody.innerHTML = `
                <tr>
                    <td colspan="3"
                        class="text-center text-muted">

                        No recipients found.

                    </td>
                </tr>
            `;

            return;
        }

        data.recipients.forEach(employee => {

            tbody.innerHTML += `
                <tr>

                    <td>${employee.employee_no}</td>

                    <td>${employee.full_name}</td>

                    <td>${employee.department}</td>

                </tr>
            `;

        });

    });

});