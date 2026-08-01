/**
 * Incident Reporting Module JavaScript
 * Handles all AJAX requests and UI interactions
 */

$(document).ready(function() {
    // Initialize DataTable for incidents
    var incidentsTable = $('#incidents-table').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "controllers/AjaxController.php?action=get_incidents",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [
            { 
                "data": "incident_id",
                "render": function(data) {
                    return data;
                }
            },
            { "data": "title" },
            { "data": "incident_type" },
            { 
                "data": "severity",
                "render": function(data) {
                    var badgeClass = {
                        'low': 'badge-low',
                        'medium': 'badge-medium',
                        'high': 'badge-high',
                        'critical': 'badge-critical'
                    };
                    return '<span class="badge ' + (badgeClass[data] || 'badge-secondary') + '">' + data + '</span>';
                }
            },
            { 
                "data": "status",
                "render": function(data) {
                    var badgeClass = {
                        'submitted': 'badge-submitted',
                        'under_review': 'badge-under_review',
                        'investigation': 'badge-investigation',
                        'escalated': 'badge-escalated',
                        'resolved': 'badge-resolved',
                        'closed': 'badge-closed'
                    };
                    return '<span class="badge ' + (badgeClass[data] || 'badge-secondary') + '">' + data.replace('_', ' ') + '</span>';
                }
            },
            { "data": "incident_date" },
            { 
                "data": null,
                "render": function(data) {
                    return data.reporter_name || (data.reporter_first_name || '') + ' ' + (data.reporter_last_name || '');
                }
            },
            {
                "data": null,
                "render": function(data) {
                    var respondentName = data.respondent_name || (data.respondent_first_name || '') + ' ' + (data.respondent_last_name || '');
                    return respondentName || 'N/A';
                }
            },
            {
                "data": null,
                "render": function(data) {
                    var buttons = '<div class="btn-group btn-group-sm">';
                    buttons += '<button class="btn btn-action btn-view btn-view-incident" data-id="' + data.id + '" title="View"><i class="fas fa-eye"></i></button>';
                    buttons += '<button class="btn btn-action btn-edit btn-edit-incident" data-id="' + data.id + '" title="Edit"><i class="fas fa-edit"></i></button>';
                    buttons += '<button class="btn btn-action btn-delete btn-delete-incident" data-id="' + data.id + '" title="Delete"><i class="fas fa-trash"></i></button>';
                    buttons += '</div>';
                    return buttons;
                }
            }
        ],
        "order": [[0, "desc"]],
        "pageLength": 25,
        "responsive": false,
        "autoWidth": false,
        "fixedHeader": false
    });

    // Initialize DataTable for disciplinary actions
    var disciplinaryTable = $('#disciplinary-table').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "controllers/AjaxController.php?action=get_disciplinary_actions",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [
            { "data": "action_reference" },
            { 
                "data": "incident_id",
                "render": function(data) {
                    return data;
                }
            },
            { 
                "data": null,
                "render": function(data) {
                    return (data.employee_first_name || '') + ' ' + (data.employee_last_name || '');
                }
            },
            { "data": "action_type" },
            { 
                "data": "status",
                "render": function(data) {
                    var badgeClass = {
                        'pending': 'badge-warning',
                        'issued': 'badge-info',
                        'appealed': 'badge-secondary',
                        'upheld': 'badge-success',
                        'dismissed': 'badge-danger'
                    };
                    return '<span class="badge ' + (badgeClass[data] || 'badge-secondary') + '">' + data + '</span>';
                }
            },
            { "data": "start_date" },
            { 
                "data": null,
                "render": function(data) {
                    return (data.issuer_first_name || '') + ' ' + (data.issuer_last_name || '');
                }
            },
            {
                "data": null,
                "render": function(data) {
                    var buttons = '<div class="btn-group btn-group-sm">';
                    buttons += '<button class="btn btn-action btn-view btn-view-disciplinary" data-id="' + data.id + '" title="View"><i class="fas fa-eye"></i></button>';
                    buttons += '<button class="btn btn-action btn-edit btn-edit-disciplinary" data-id="' + data.id + '" title="Edit"><i class="fas fa-edit"></i></button>';
                    buttons += '</div>';
                    return buttons;
                }
            }
        ],
        "order": [[0, "desc"]],
        "pageLength": 25,
        "responsive": false
    });

    // ============================================
    // VIEW TOGGLE BUTTONS
    // ============================================

    // View all incidents
    $('#btn-view-incidents').click(function() {
        $('#incidents-section').show();
        $('#disciplinary-section').hide();
        $(this).removeClass('btn-secondary').addClass('btn-primary');
        $('#btn-view-disciplinary').removeClass('btn-primary').addClass('btn-secondary');
    });

    // View disciplinary actions
    $('#btn-view-disciplinary').click(function() {
        $('#incidents-section').hide();
        $('#disciplinary-section').show();
        $(this).removeClass('btn-secondary').addClass('btn-primary');
        $('#btn-view-incidents').removeClass('btn-primary').addClass('btn-secondary');
        disciplinaryTable.ajax.reload();
    });

    // ============================================
    // INCIDENT CRUD OPERATIONS
    // ============================================

    // Open create incident modal (dashboard / pages that include the modal)
    $('#btn-create-incident').click(function() {
        var $modalForm = $('#form-create-incident');
        if ($modalForm.length && $modalForm[0].reset) {
            $modalForm[0].reset();
        }
        $('#modal-create-incident').modal('show');
    });

    // Initialize bs-custom-file-input
    if (typeof bsCustomFileInput !== 'undefined') {
        bsCustomFileInput.init();
    }

    // Handle file selection preview
    $(document).on('change', '.custom-file-input', function() {
        var files = this.files;
        var previewId = $(this).closest('.form-group').find('#evidence-preview, .evidence-preview').attr('id');
        
        if (previewId) {
            var $preview = $('#' + previewId);
            $preview.empty();
            
            if (files.length > 0) {
                var html = '<ul class="list-group mt-2">';
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    var size = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    html += '<li class="list-group-item d-flex justify-content-between align-items-center py-1">';
                    html += '<span><i class="fas fa-file mr-2"></i>' + file.name + '</span>';
                    html += '<span class="badge badge-primary badge-pill">' + size + '</span>';
                    html += '</li>';
                }
                html += '</ul>';
                $preview.html(html);
            }
        }
    });

    // Submit create incident form (inline "Report New Incident" uses #create-incident-form-element; modal uses #form-create-incident)
    $(document).on('submit', '#create-incident-form-element, #form-create-incident', function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: 'controllers/AjaxController.php?action=create_incident',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    if ($('#create-incident-form-element').length) {
                        if (response.evidence_warnings && response.evidence_warnings.length) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Incident saved',
                                html: '<p>Your report was saved, but some evidence files had problems:</p><ul style="text-align:left">' +
                                    response.evidence_warnings.map(function(w) {
                                        return '<li>' + $('<div>').text(w).html() + '</li>';
                                    }).join('') + '</ul>'
                            }).then(function() {
                                window.location.href = 'incident_reporting.php?success=created';
                            });
                            return;
                        }
                        window.location.href = 'incident_reporting.php?success=created';
                        return;
                    }
                    var successOpts = {
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    };
                    if (response.evidence_warnings && response.evidence_warnings.length) {
                        successOpts.icon = 'warning';
                        successOpts.html = '<p>' + $('<div>').text(response.message).html() + '</p><ul style="text-align:left">' +
                            response.evidence_warnings.map(function(w) {
                                return '<li>' + $('<div>').text(w).html() + '</li>';
                            }).join('') + '</ul>';
                        delete successOpts.text;
                    }
                    Swal.fire(successOpts).then(function() {
                        $('#modal-create-incident').modal('hide');
                        incidentsTable.ajax.reload();
                        updateStatistics();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while creating the incident'
                });
            }
        });
    });

    // View incident details
    $(document).on('click', '.btn-view-incident', function() {
        var incidentId = $(this).data('id');
        loadIncidentDetails(incidentId);
    });

    // Edit incident FROM lc_table - redirect to edit page
    $(document).on('click', '.btn-edit-incident', function() {
        var incidentId = $(this).data('id');
        window.location.href = 'incident_reporting.php?edit=' + incidentId;
    });

    // Create disciplinary action FROM lc_incident view
    $(document).on('click', '.btn-create-disciplinary', function() {
        var incidentId = $(this).data('id');
        openDisciplinaryModal(incidentId);
    });

    // Load incident details
    function loadIncidentDetails(incidentId) {
        $('#incident-details-content').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
        $('#modal-view-incident').modal('show');

        $.ajax({
            url: 'controllers/AjaxController.php?action=get_incident&id=' + incidentId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderIncidentDetails(response.data);
                } else {
                    $('#incident-details-content').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#incident-details-content').html('<div class="alert alert-danger">Failed to load incident details</div>');
            }
        });
    }

    // Render incident details
    function renderIncidentDetails(incident) {
        var statusBadge = {
            'submitted': 'badge-submitted',
            'under_review': 'badge-under_review',
            'investigation': 'badge-investigation',
            'escalated': 'badge-escalated',
            'resolved': 'badge-resolved',
            'closed': 'badge-closed'
        };

        var severityBadge = {
            'low': 'badge-low',
            'medium': 'badge-medium',
            'high': 'badge-high',
            'critical': 'badge-critical'
        };

        var html = '<div class="row">';
        
        // Basic Information
        html += '<div class="col-md-6">';
        html += '<h5>Basic Information</h5>';
        html += '<table class="table table-bordered">';
        html += '<tr><th>Incident ID</th><td>' + incident.incident_id + '</td></tr>';
        html += '<tr><th>Title</th><td>' + incident.title + '</td></tr>';
        html += '<tr><th>Type</th><td>' + incident.incident_type + '</td></tr>';
        html += '<tr><th>Severity</th><td><span class="badge ' + (severityBadge[incident.severity] || 'badge-secondary') + '">' + incident.severity + '</span></td></tr>';
        html += '<tr><th>Status</th><td><span class="badge ' + (statusBadge[incident.status] || 'badge-secondary') + '">' + incident.status.replace('_', ' ') + '</span></td></tr>';
        html += '<tr><th>Date</th><td>' + incident.incident_date + (incident.incident_time ? ' ' + incident.incident_time : '') + '</td></tr>';
        html += '<tr><th>Location</th><td>' + (incident.location || 'N/A') + '</td></tr>';
        html += '</table>';
        html += '</div>';

        // Reporter Information
        html += '<div class="col-md-6">';
        html += '<h5>Reporter Information</h5>';
        html += '<table class="table table-bordered">';
        html += '<tr><th>Name</th><td>' + (incident.reporter_name || 'N/A') + '</td></tr>';
        html += '<tr><th>Employee No</th><td>' + (incident.reporter_employee_no || 'N/A') + '</td></tr>';
        html += '<tr><th>Department</th><td>' + (incident.reporter_department || 'N/A') + '</td></tr>';
        html += '<tr><th>Position</th><td>' + (incident.reporter_position || 'N/A') + '</td></tr>';
        html += '</table>';
        html += '</div>';

        html += '</div>';

        // Respondent Information
        if (incident.respondent_name) {
            html += '<div class="row mt-3">';
            html += '<div class="col-md-6">';
            html += '<h5>Respondent Information</h5>';
            html += '<table class="table table-bordered">';
            html += '<tr><th>Name</th><td>' + incident.respondent_name + '</td></tr>';
            html += '<tr><th>Employee No</th><td>' + (incident.respondent_employee_no || 'N/A') + '</td></tr>';
            html += '<tr><th>Department</th><td>' + (incident.respondent_department || 'N/A') + '</td></tr>';
            html += '<tr><th>Position</th><td>' + (incident.respondent_position || 'N/A') + '</td></tr>';
            html += '</table>';
            html += '</div>';
            html += '</div>';
        }

        // Description
        html += '<div class="row mt-3">';
        html += '<div class="col-12">';
        html += '<h5>Description</h5>';
        html += '<div class="card card-body bg-light">';
        html += incident.description || 'No description provided';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        // Disciplinary Action Button
        html += '<div class="row mt-3">';
        html += '<div class="col-12 text-right">';
        html += '<button type="button" class="btn btn-danger btn-create-disciplinary" data-id="' + incident.id + '">';
        html += '<i class="fas fa-gavel mr-1"></i> Create Disciplinary Action';
        html += '</button>';
        html += '</div>';
        html += '</div>';

        // Evidence Files
        if (incident.evidence && incident.evidence.length > 0) {
            html += '<div class="row mt-3">';
            html += '<div class="col-12">';
            html += '<h5>Evidence Files</h5>';
            html += '<div class="table-responsive">';
            html += '<table class="table table-bordered">';
            html += '<thead><tr><th>File Name</th><th>Type</th><th>Size</th><th>Uploaded</th><th>Action</th></tr></thead>';
            html += '<tbody>';
            incident.evidence.forEach(function(file) {
                html += '<tr>';
                html += '<td><i class="fas fa-file"></i> ' + (file.file_name || '') + '</td>';
                html += '<td>' + (file.file_type || 'N/A') + '</td>';
                html += '<td>' + formatFileSize(file.file_size || 0) + '</td>';
                html += '<td>' + (file.uploaded_at || 'N/A') + '</td>';
                html += '<td><a href="' + (file.file_path || '#') + '" target="_blank" rel="noopener" class="btn btn-sm btn-info"><i class="fas fa-download"></i></a></td>';
                html += '</tr>';
            });
            html += '</tbody>';
            html += '</table>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
        }

        $('#incident-details-content').html(html);
        $('#modal-view-incident').data('incident-id', incident.id);
    }

    function formatIncidentStatusLabel(status) {
        if (!status) {
            return 'N/A';
        }
        return String(status).replace(/_/g, ' ').replace(/\b\w/g, function(ch) {
            return ch.toUpperCase();
        });
    }

    // Edit incident button click (in view modal)
    $('#edit-incident-btn').click(function() {
        var incidentId = $('#modal-view-incident').data('incident-id');
        if (incidentId) {
            window.location.href = 'incident_reporting.php?edit=' + incidentId;
        }
    });

    // Load incident for editing - populate modal
    function loadIncidentForEdit(incidentId) {
        $('#incident-details-content').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
        
        $.ajax({
            url: 'controllers/AjaxController.php?action=get_incident&id=' + incidentId,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var incident = response.data;
                    $('#modal-view-incident').modal('hide');
                    
                    // Populate the edit modal
                    $('#edit-incident-id').val(incident.id);
                    $('#edit-incident-title').val(incident.title);
                    $('#edit-incident-type').val(incident.incident_type).trigger('change');
                    $('#edit-incident-category').val(incident.type).trigger('change');
                    $('#edit-incident-severity').val(incident.severity).trigger('change');
                    $('#edit-incident-location').val(incident.location || '');
                    $('#edit-incident-date').val(incident.incident_date);
                    $('#edit-incident-time').val(incident.incident_time || '');
                    $('#edit-incident-description').val(incident.description || '');
                    
                    // Show the edit modal
                    setTimeout(function() {
                        $('#modal-edit-incident').modal('show');
                    }, 300);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load incident for editing'
                });
            }
        });
    }

    // Submit edit incident form via AJAX
    $('#form-edit-incident').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        
        $.ajax({
            url: 'controllers/AjaxController.php?action=update_incident',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    var successOpts = {
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    };
                    if (response.evidence_warnings && response.evidence_warnings.length) {
                        successOpts.icon = 'warning';
                        successOpts.html = '<p>' + $('<div>').text(response.message).html() + '</p><ul style="text-align:left">' +
                            response.evidence_warnings.map(function(w) {
                                return '<li>' + $('<div>').text(w).html() + '</li>';
                            }).join('') + '</ul>';
                        delete successOpts.text;
                    }
                    Swal.fire(successOpts).then(function() {
                        $('#modal-edit-incident').modal('hide');
                        incidentsTable.ajax.reload();
                        updateStatistics();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update incident'
                });
            }
        });
    });

    // Delete incident
    $(document).on('click', '.btn-delete-incident', function() {
        var incidentId = $(this).data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'controllers/AjaxController.php?action=delete_incident',
                    type: 'POST',
                    data: { id: incidentId },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message
                            });
                            incidentsTable.ajax.reload();
                            updateStatistics();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while deleting the incident'
                        });
                    }
                });
            }
        });
    });

    // ============================================
    // DISCIPLINARY ACTION OPERATIONS
    // ============================================

    // Update status FROM lc_incident view modal
    $('#btn-update-status-modal').click(function() {
        var incidentId = $('#modal-view-incident').data('incident-id');
        if (!incidentId) {
            return;
        }
        $('#status-incident-id').val(incidentId);
        $('#status-current').val('Loading…');
        $('#status-new').val('');
        $('#status-notes').val('');

        $('#modal-view-incident').modal('hide');
        $('#modal-update-status').modal('show');

        $.ajax({
            url: 'controllers/AjaxController.php?action=get_incident&id=' + incidentId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data && response.data.status) {
                    $('#status-current').val(formatIncidentStatusLabel(response.data.status));
                } else {
                    $('#status-current').val('N/A');
                }
            },
            error: function() {
                $('#status-current').val('');
            }
        });
    });

    // Update status form submit
    $('#form-update-status').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: 'controllers/AjaxController.php?action=update_incident_status',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    });
                    $('#modal-update-status').modal('hide');
                    incidentsTable.ajax.reload();
                    updateStatistics();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update incident status'
                });
            }
        });
    });

    // Open disciplinary modal
    function openDisciplinaryModal(incidentId) {
        var $form = $('#form-create-disciplinary');
        if ($form.length && $form[0].reset) {
            $form[0].reset();
        }
        $('#disciplinary-incident-id').val(incidentId);
        
        // Hide other modals if open
        $('#modal-view-incident').modal('hide');
        
        // Load incident reference
        $.ajax({
            url: 'controllers/AjaxController.php?action=get_incident&id=' + incidentId,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#disciplinary-incident-reference').text(response.data.incident_id);
                    
                    // Pre-select respondent if available
                    if (response.data.respondent_id) {
                        // We'll handle this after loading employees
                        window.pendingRespondentId = response.data.respondent_id;
                    }
                }
            }
        });

        // Load employees
        loadEmployeesForDisciplinary();
        $('#modal-create-disciplinary').modal('show');
    }

    // Load employees for disciplinary action
    function loadEmployeesForDisciplinary() {
        $.ajax({
            url: 'controllers/AjaxController.php?action=get_employees',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var select = $('#disciplinary-employee');
                    select.empty().append('<option value="">Select Employee</option>');
                    response.data.forEach(function(emp) {
                        var displayName = emp.first_name;
                        if (emp.last_name) displayName += ' ' + emp.last_name;
                        var empNo = emp.employee_no || emp.employee_id;
                        select.append('<option value="' + emp.id + '">' + displayName + ' (' + empNo + ')</option>');
                    });

                    // Pre-select if we have a pending respondent
                    if (window.pendingRespondentId) {
                        select.val(window.pendingRespondentId).trigger('change');
                        delete window.pendingRespondentId;
                    }
                }
            }
        });
    }

    // Submit create disciplinary form
    $(document).on('submit', '#form-create-disciplinary', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        formData.append('action', 'create_disciplinary_action');
        
        $.ajax({
            url: 'controllers/AjaxController.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    });
                    $('#modal-create-disciplinary').modal('hide');
                    // Reload disciplinary actions if visible
                    if ($('#disciplinary-section').is(':visible')) {
                        disciplinaryTable.ajax.reload();
                    }
                    updateStatistics();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while creating the disciplinary action'
                });
            }
        });
    });

    // View disciplinary action details
    $(document).on('click', '.btn-view-disciplinary', function() {
        var actionId = $(this).data('id');
        loadDisciplinaryDetail(actionId);
    });

    // Edit disciplinary action
    $(document).on('click', '.btn-edit-disciplinary', function() {
        var actionId = $(this).data('id');
        loadDisciplinaryForEdit(actionId);
    });

    // Load disciplinary action detail
    function loadDisciplinaryDetail(actionId) {
        $('#disciplinary-details').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
        $('#modal-view-disciplinary-detail').modal('show');

        $.ajax({
            url: 'controllers/AjaxController.php?action=get_disciplinary_action&id=' + actionId,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    renderDisciplinaryDetail(response.data);
                } else {
                    $('#disciplinary-details').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#disciplinary-details').html('<div class="alert alert-danger">Failed to load disciplinary action details</div>');
            }
        });
    }

    // Render disciplinary action detail
    function renderDisciplinaryDetail(action) {
        var statusBadge = {
            'pending': 'badge-warning',
            'issued': 'badge-info',
            'appealed': 'badge-secondary',
            'upheld': 'badge-success',
            'dismissed': 'badge-danger'
        };

        var actionType = {
            'verbal_warning': 'Verbal Warning',
            'written_warning': 'Written Warning',
            'suspension': 'Suspension',
            'termination': 'Termination',
            'final_warning': 'Final Warning',
            'other': 'Other'
        };

        var html = '<div class="row">';
        html += '<div class="col-md-6">';
        html += '<h5>Action Details</h5>';
        html += '<table class="table table-bordered">';
        html += '<tr><th>Reference</th><td>' + action.action_reference + '</td></tr>';
        html += '<tr><th>Incident</th><td>' + (action.incident_reference || 'N/A') + '</td></tr>';
        html += '<tr><th>Action Type</th><td>' + (actionType[action.action_type] || action.action_type) + '</td></tr>';
        html += '<tr><th>Status</th><td><span class="badge ' + (statusBadge[action.status] || 'badge-secondary') + '">' + action.status + '</span></td></tr>';
        html += '<tr><th>Start Date</th><td>' + action.start_date + '</td></tr>';
        html += '<tr><th>End Date</th><td>' + (action.end_date || 'N/A') + '</td></tr>';
        html += '<tr><th>Duration</th><td>' + (action.duration_days ? action.duration_days + ' days' : 'N/A') + '</td></tr>';
        html += '</table>';
        html += '</div>';

        html += '<div class="col-md-6">';
        html += '<h5>Employee Information</h5>';
        html += '<table class="table table-bordered">';
        html += '<tr><th>Name</th><td>' + (action.employee_name || 'N/A') + '</td></tr>';
        html += '<tr><th>Employee ID</th><td>' + (action.employee_number || 'N/A') + '</td></tr>';
        html += '<tr><th>Department</th><td>' + (action.department_name || 'N/A') + '</td></tr>';
        html += '<tr><th>Issued By</th><td>' + (action.issued_by_name || 'N/A') + '</td></tr>';
        html += '</table>';
        html += '</div>';
        html += '</div>';

        html += '<div class="row mt-3">';
        html += '<div class="col-12">';
        html += '<h5>Reason</h5>';
        html += '<div class="card card-body bg-light">';
        html += action.reason || 'No reason provided';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        if (action.violation_description) {
            html += '<div class="row mt-3">';
            html += '<div class="col-12">';
            html += '<h5>Violation Description</h5>';
            html += '<div class="card card-body bg-light">';
            html += action.violation_description;
            html += '</div>';
            html += '</div>';
            html += '</div>';
        }

        $('#disciplinary-details').html(html);
        $('#modal-view-disciplinary-detail').data('action-id', action.id);
    }

    // Edit disciplinary action button click
    $('#edit-disciplinary-btn').click(function() {
        var actionId = $('#modal-view-disciplinary-detail').data('action-id');
        if (actionId) {
            loadDisciplinaryForEdit(actionId);
        }
    });

    // Load disciplinary action for editing
    function loadDisciplinaryForEdit(actionId) {
        $.ajax({
            url: 'controllers/AjaxController.php?action=get_disciplinary_action&id=' + actionId,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var action = response.data;
                    $('#edit-disciplinary-id').val(action.id);
                    $('#edit-disciplinary-employee_id').val(action.employee_id);
                    $('#edit-disciplinary-action_type').val(action.action_type);
                    $('#edit-disciplinary-status').val(action.status);
                    $('#edit-disciplinary-start_date').val(action.start_date);
                    $('#edit-disciplinary-end_date').val(action.end_date);
                    $('#edit-disciplinary-duration_days').val(action.duration_days);
                    $('#edit-disciplinary-reason').val(action.reason);
                    $('#edit-disciplinary-violation_description').val(action.violation_description);
                    
                    $('#modal-view-disciplinary-detail').modal('hide');
                    $('#modal-edit-disciplinary').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load disciplinary action for editing'
                });
            }
        });
    }

    // Submit edit disciplinary form
    $('#edit-disciplinary-form').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'controllers/AjaxController.php?action=update_disciplinary_action',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    });
                    $('#modal-edit-disciplinary').modal('hide');
                    disciplinaryTable.ajax.reload();
                    updateStatistics();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating the disciplinary action'
                });
            }
        });
    });

    // ============================================
    // FILTER OPERATIONS
    // ============================================

    // Filter incidents by status
    $('#filter-status').change(function() {
        var status = $(this).val();
        incidentsTable.ajax.url('controllers/AjaxController.php?action=get_incidents&status=' + status).load();
    });

    // Filter incidents by severity
    $('#filter-severity').change(function() {
        var severity = $(this).val();
        incidentsTable.ajax.url('controllers/AjaxController.php?action=get_incidents&severity=' + severity).load();
    });

    // Search incidents
    $('#filter-search').on('keyup', function() {
        var search = $(this).val();
        incidentsTable.ajax.url('controllers/AjaxController.php?action=get_incidents&search=' + search).load();
    });

    // Filter disciplinary actions by status
    $('#filter-action-status').change(function() {
        var status = $(this).val();
        disciplinaryTable.ajax.url('controllers/AjaxController.php?action=get_disciplinary_actions&status=' + status).load();
    });

    // Filter disciplinary actions by type
    $('#filter-action-type').change(function() {
        var actionType = $(this).val();
        disciplinaryTable.ajax.url('controllers/AjaxController.php?action=get_disciplinary_actions&action_type=' + actionType).load();
    });

    // Search disciplinary actions
    $('#filter-action-search').on('keyup', function() {
        var search = $(this).val();
        disciplinaryTable.ajax.url('controllers/AjaxController.php?action=get_disciplinary_actions&search=' + search).load();
    });

    // ============================================
    // UTILITY FUNCTIONS
    // ============================================

    // Update statistics
    function updateStatistics() {
        $.ajax({
            url: 'controllers/AjaxController.php?action=get_incident_statistics',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    // Update incident statistics
                    $('.small-box.bg-info .inner h3').text(response.data.total || 0);
                    $('.small-box.bg-warning .inner h3').text(response.data.submitted || 0);
                    $('.small-box.bg-primary .inner h3').text(response.data.investigation || 0);
                    $('.small-box.bg-success .inner h3').text(response.data.resolved || 0);
                    $('.small-box.bg-danger .inner h3').text(response.data.critical_severity || 0);
                }
            }
        });

        $.ajax({
            url: 'controllers/AjaxController.php?action=get_disciplinary_statistics',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('.small-box.bg-secondary .inner h3').text(response.data.total || 0);
                }
            }
        });
    }

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        var k = 1024;
        var sizes = ['Bytes', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Initialize
    updateStatistics();
});
