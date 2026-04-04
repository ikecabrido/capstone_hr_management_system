 <div class="modal fade" id="editMeetingModal<?= $meeting['id'] ?>" tabindex="-1">
     <div class="modal-dialog modal-dialog-centered">
         <form method="POST" action="index.php?url=admin-online-meeting-update">
             <div class="modal-content">

                 <div class="modal-header bg-warning text-white">
                     <h5 class="modal-title text-4xl">Edit Meeting</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                 </div>
                 <form action="index.php?url=admin-online-meeting-update" method="POST">
                     <div class="modal-body">
                         <input type="hidden" name="id" value="<?= $meeting['id'] ?>">

                         <div class="mb-3">
                             <label class="form-label">Meeting Title</label>
                             <input type="text" name="title"
                                 value="<?= htmlspecialchars($meeting['title']) ?>"
                                 class="form-control" required>
                         </div>

                         <div class="mb-3">
                             <label class="form-label">Schedule</label>
                             <input type="datetime-local" name="scheduled_at"
                                 value="<?= date('Y-m-d\TH:i', strtotime($meeting['scheduled_at'])) ?>"
                                 class="form-control" required>
                         </div>
                     </div>

                     <div class="modal-footer">
                         <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                         <button type="submit" class="btn btn-warning">Update</button>
                     </div>
                 </form>
             </div>
         </form>
     </div>
 </div>