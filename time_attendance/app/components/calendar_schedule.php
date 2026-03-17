<?php
/**
 * Calendar Schedule Component
 * Displays employee schedule in calendar format with daily timeline editor
 */

// Check if this is an AJAX request for employee search
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    if ($_GET['action'] === 'search_employees') {
        require_once __DIR__ . '/../config/Database.php';
        
        $search = $_GET['q'] ?? '';
        
        $query = "SELECT employee_id, full_name 
                  FROM employees 
                  WHERE employment_status = 'Active' 
                  AND full_name LIKE ?
                  ORDER BY full_name
                  LIMIT 20";
        
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->prepare($query);
        $search_param = "%$search%";
        $stmt->bindParam(1, $search_param);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($results);
        exit;
    }
}
?>

<div id="calendar-schedule-container" class="card">
    <div class="card-header">
        <h5 class="card-title">Employee Schedule Calendar</h5>
        <p class="text-muted mb-0"><small>
            <strong>How to use:</strong>
            <br>• View all employees' schedules below (colored by work status)
            <br>• Click any date to see a 24-hour timeline for that day
            <br>• Click day headers (Sun, Mon, etc.) to switch to week view
            <br>• Search for a specific employee to see their individual schedule
        </small></p>
    </div>
    <div class="card-body">
        <!-- Employee Search Section -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="employee-search">Search Employee (Optional)</label>
                    <input 
                        type="text" 
                        id="employee-search" 
                        class="form-control" 
                        placeholder="Enter name or employee ID..."
                        autocomplete="off">
                    <div id="search-results" class="list-group mt-2" style="display:none; max-height: 300px; overflow-y: auto; position: absolute; width: calc(100% - 30px); z-index: 1000;"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div id="selected-employee" class="alert alert-info" style="display:none;">
                    <p class="mb-0">
                        Selected: <strong id="employee-name"></strong>
                        <button class="btn btn-sm btn-outline-secondary float-right" id="clear-employee">Clear & Show All</button>
                    </p>
                </div>
            </div>
        </div>

        <hr>

        <!-- Calendar Legend -->
        <div class="alert alert-light border mb-3">
            <strong>Legend:</strong>
            <span class="badge badge-success ml-2">Shift Assigned</span>
            <span class="badge badge-info ml-2">Present/Checked In</span>
            <span class="badge badge-warning ml-2">Late</span>
            <span class="badge badge-danger ml-2">Absent</span>
        </div>

        <!-- Calendar View -->
        <div id="calendar-section">
            <!-- View Navigation Buttons (Top Right) -->
            <div class="mb-3 d-flex justify-content-end">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary side-month-btn" title="Switch to Month View">
                        <i class="fas fa-calendar"></i> Month
                    </button>
                    <button type="button" class="btn btn-outline-primary side-week-btn" title="Switch to Week View">
                        <i class="fas fa-calendar-alt"></i> Week
                    </button>
                    <button type="button" class="btn btn-outline-primary side-day-btn" title="Switch to Day View">
                        <i class="fas fa-clock"></i> Day
                    </button>
                </div>
            </div>

            <!-- Single Calendar Container (shown for Month and Week views) -->
            <div id="calendar-container" style="background: white; padding: 20px; border: 1px solid #ddd; border-radius: 4px; min-height: 700px; display: block; width: 100%;">
                <div id="calendar"></div>
            </div>

            <!-- Day View Tab Content -->
            <div class="tab-content">
                <div class="tab-pane fade" id="day-view" role="tabpanel">
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="btn-group" role="group" style="width: 100%;">
                                <button type="button" class="btn btn-outline-secondary" id="prev-day" onclick="handlePrevDay()">
                                    <i class="fas fa-chevron-left"></i> Previous Day
                                </button>
                                <button type="button" class="btn btn-outline-secondary disabled" id="current-day-display" style="flex: 1;">
                                    <span id="current-day-text">Today</span>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="next-day" onclick="handleNextDay()">
                                    Next Day <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="day-timeline-container" style="overflow-y: auto; padding: 20px; background: white; border: 1px solid #ddd; border-radius: 4px; min-height: 600px;">
                        <canvas id="day-timeline-canvas"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
