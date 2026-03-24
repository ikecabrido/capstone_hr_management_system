<!-- Send Reminder Modal - Standalone Modal (No Backdrop) -->
<div class="modal fade" id="sendReminderModal" tabindex="-1" role="dialog" aria-labelledby="sendReminderModalLabel" aria-hidden="true" data-backdrop="false" data-keyboard="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="z-index: 100001;">
            <div class="modal-header" style="background: #2c3e50; color: white;">
                <h4 class="modal-title" id="sendReminderModalLabel" style="font-weight: 600;">
                    <i class="fas fa-bell"></i> Send Reminder
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="reminderSubject" style="font-weight: 600;">Subject</label>
                    <input type="text" class="form-control" id="reminderSubject" 
                           placeholder="Compliance Reminder - Action Required" 
                           value="Compliance Reminder - Action Required"
                           style="border: 1px solid #ced4da;">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="reminderMessage" style="font-weight: 600;">Message</label>
                    <textarea class="form-control" id="reminderMessage" rows="5" 
                              placeholder="Dear [Employee Name],&#10;&#10;This is a reminder regarding your pending compliance requirements. Please complete the necessary documents and training at your earliest convenience.&#10;&#10;Thank you,
Human Resources Department"
                              style="border: 1px solid #ced4da; resize: vertical;"></textarea>
                </div>
            </div>
            <div class="modal-footer" style="padding: 15px 20px;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="padding: 10px 20px;">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="submitReminder()" style="padding: 10px 20px;">
                    <i class="fas fa-paper-plane"></i> Send
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Send Reminder Modal - Standalone Styles */
#sendReminderModal {
    z-index: 99999 !important;
}

#sendReminderModal.show {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

#sendReminderModal .modal-dialog {
    z-index: 100000 !important;
    margin: 1.75rem auto;
    max-width: 500px;
}

#sendReminderModal .modal-content {
    z-index: 100001 !important;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

#sendReminderModal .modal-header {
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}

#sendReminderModal .modal-footer {
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
}

#sendReminderModal .form-control:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
}
</style>
