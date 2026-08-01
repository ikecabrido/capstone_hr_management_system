<!-- Policy Documentation Modals -->

<!-- Add Policy Modal -->
<div class="modal fade" id="addPolicyModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Policy</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data" class="policy-form" data-action="add">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label>Policy Title *</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category" class="form-control" required>
                            <option value="HR Policies">HR Policies</option>
                            <option value="Code of Conduct">Code of Conduct</option>
                            <option value="Legal Policies">Legal Policies</option>
                            <option value="Compliance Guidelines">Compliance Guidelines</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Department Owner</label>
                        <input type="text" name="department_owner" class="form-control" placeholder="e.g., Human Resources, IT Department">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Policy File (PDF/DOC)</label>
                        <input type="file" name="policy_file" class="form-control" accept=".pdf,.doc,.docx">
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="requires_acknowledgment" class="form-check-input" id="ackCheck" value="1" checked>
                            <label class="form-check-label" for="ackCheck">Require employee acknowledgment</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Policy</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send Reminder Modal -->
<div class="modal fade" id="reminderModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h4 class="modal-title"><i class="fas fa-bell mr-2"></i>Send Policy Acknowledgment Reminder</h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST" action="" id="reminderForm" class="reminder-form">
                <div class="modal-body">
                    <input type="hidden" name="action" value="send_reminder">
                    <input type="hidden" name="ack_id" id="reminderAckId">
                    
                    <div class="form-group">
                        <label>Recipient</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text" class="form-control" id="reminderEmployeeName" readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Policy</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-file-alt"></i></span>
                            </div>
                            <input type="text" class="form-control" id="reminderPolicyTitle" readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Reminder Message *</label>
                        <textarea name="message" id="reminderMessage" class="form-control" rows="4" required placeholder="Enter your reminder message..."></textarea>
                        <small class="text-muted">You can use placeholders: {employee_name}, {policy_name}, {due_date}</small>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="useTemplate" checked>
                            <label class="form-check-label" for="useTemplate">Use default template</label>
                        </div>
                    </div>
                    
                    <div id="reminderPreview" class="alert alert-info">
                        <strong><i class="fas fa-envelope mr-1"></i>Preview:</strong>
                        <p class="mb-0 mt-2" id="previewText"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info"><i class="fas fa-paper-plane mr-1"></i>Send Reminder</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Policy Modals (Generated dynamically for each policy) -->
<?php foreach ($policies as $policy): ?>
    
    <!-- View Policy Modal -->
    <div class="modal fade" id="viewPolicyModal<?= $policy['id'] ?>">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= htmlspecialchars($policy['title']) ?></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Category:</strong> <?= htmlspecialchars($policy['category']) ?></p>
                            <p><strong>Department:</strong> <?= htmlspecialchars($policy['department_owner'] ?? 'N/A') ?></p>
                            <p><strong>Version:</strong> <span class="badge badge-secondary">v<?= $policy['version_number'] ?></span></p>
                            <p><strong>Last Updated:</strong> <?= date('M d, Y h:i A', strtotime($policy['updated_at'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Acknowledgments:</strong></p>
                            <span class="badge badge-success"><?= $policy['acknowledged_count'] ?? 0 ?> Acknowledged</span>
                            <span class="badge badge-warning"><?= $policy['unread_count'] ?? 0 ?> Pending</span>
                            <?php if ($policy['requires_acknowledgment']): ?>
                                <span class="badge badge-info ml-1">Required</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>Description</label>
                        <p><?= nl2br(htmlspecialchars($policy['description'] ?? 'No description')) ?></p>
                    </div>
                    <?php if (!empty($policy['file_path'])): 
                        $downloadUrl = str_replace(' ', '%20', $policy['file_path']);
                        $fileExists = file_exists(__DIR__ . '/../' . $policy['file_path']);
                    ?>
                        <div class="form-group">
                            <label>Attached Document</label>
                            <br>
                            <?php if ($fileExists): ?>
                                <a href="<?= htmlspecialchars($downloadUrl) ?>" class="btn btn-sm btn-primary" target="_blank" download="<?= htmlspecialchars($policy['file_name']) ?>">
                                    <i class="fas fa-download mr-1"></i> Download
                                </a>
                                <small class="text-muted ml-2"><?= htmlspecialchars($policy['file_name']) ?></small>
                            <?php else: ?>
                                <div class="alert alert-warning py-1 px-2 d-inline-block mb-0">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> File not found on server
                                </div>
                                <small class="text-muted ml-2"><?= htmlspecialchars($policy['file_name']) ?></small>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Policy Modal -->
    <div class="modal fade" id="editPolicyModal<?= $policy['id'] ?>">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Policy - <?= htmlspecialchars($policy['title']) ?></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="" enctype="multipart/form-data" class="policy-form" data-action="update">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= $policy['id'] ?>">
                        <div class="form-group">
                            <label>Policy Title *</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($policy['title']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Category *</label>
                            <select name="category" class="form-control" required>
                                <option value="HR Policies" <?= $policy['category'] === 'HR Policies' ? 'selected' : '' ?>>HR Policies</option>
                                <option value="Code of Conduct" <?= $policy['category'] === 'Code of Conduct' ? 'selected' : '' ?>>Code of Conduct</option>
                                <option value="Legal Policies" <?= $policy['category'] === 'Legal Policies' ? 'selected' : '' ?>>Legal Policies</option>
                                <option value="Compliance Guidelines" <?= $policy['category'] === 'Compliance Guidelines' ? 'selected' : '' ?>>Compliance Guidelines</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Department Owner</label>
                            <input type="text" name="department_owner" class="form-control" value="<?= htmlspecialchars($policy['department_owner'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($policy['description'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Upload New Version (PDF/DOC)</label>
                            <input type="file" name="policy_file" class="form-control" accept=".pdf,.doc,.docx">
                            <small class="text-muted">Leave empty to keep current file. Uploading a new file will create version <?= $policy['version_number'] + 1 ?></small>
                        </div>
                        <div class="form-group">
                            <label>Change Notes</label>
                            <input type="text" name="change_notes" class="form-control" placeholder="Describe what changed in this version">
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" name="requires_acknowledgment" class="form-check-input" id="ackCheckEdit<?= $policy['id'] ?>" value="1" <?= $policy['requires_acknowledgment'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ackCheckEdit<?= $policy['id'] ?>">Require employee acknowledgment</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Policy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Policy Modal -->
    <div class="modal fade" id="deletePolicyModal<?= $policy['id'] ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h4 class="modal-title">Confirm Delete</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="" class="policy-form" data-action="delete">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $policy['id'] ?>">
                        <p>Are you sure you want to delete this policy?</p>
                        <strong><?= htmlspecialchars($policy['title']) ?></strong>
                        <p class="text-muted mt-2">This action cannot be undone. The policy will be marked as inactive.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php endforeach; ?>
