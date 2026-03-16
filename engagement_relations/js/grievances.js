// ============================================
// GRIEVANCE STATUS COLOR CODING
// Provides color coding for grievance status
// ============================================

function getStatusColor(status) {
    switch(status.toLowerCase()) {
        case 'open': return '#ec1c1c';
        case 'in_progress': return '#f76b1c';
        case 'resolved': return '#2e7d32';
        default: return '#666';
    }
}
