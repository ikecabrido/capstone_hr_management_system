<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bestlink</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <div class="overlay">
    <div class="container">
      <img src="assets/pics/bcpLogo.png" alt="" />
      <h1>BESTLINK COLLEGE OF THE PHILIPPINES</h1>
      <div class="buttons">
        <a href="login_form.php">Admin Login</a>
        <a href="engagement_relations/employee_portal/index.php">Portal Login</a>
      </div>
    </div>
  </div>
</body>

</html>

<?php
// Example view for displaying employees
if ($_GET['view'] === 'employees') {
    $controller = new \App\Controllers\EmployeeController();
    $employees = $controller->getAllEmployees();
    echo '<h1>Employees</h1>';
    echo '<table border="1">';
    echo '<tr><th>ID</th><th>Name</th><th>Department</th><th>Position</th><th>Email</th><th>Phone</th></tr>';
    foreach ($employees as $employee) {
        echo '<tr>';
        echo '<td>' . $employee['employee_id'] . '</td>';
        echo '<td>' . $employee['name'] . '</td>';
        echo '<td>' . $employee['department'] . '</td>';
        echo '<td>' . $employee['position'] . '</td>';
        echo '<td>' . $employee['email'] . '</td>';
        echo '<td>' . $employee['phone'] . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}

// Example view for displaying grievances
if ($_GET['view'] === 'grievances') {
    $controller = new \App\Controllers\GrievanceController();
    $grievances = $controller->getAllGrievances();
    echo '<h1>Grievances</h1>';
    echo '<table border="1">';
    echo '<tr><th>ID</th><th>Employee ID</th><th>Subject</th><th>Description</th><th>Status</th><th>Category</th></tr>';
    foreach ($grievances as $grievance) {
        echo '<tr>';
        echo '<td>' . $grievance['eer_grievance_id'] . '</td>';
        echo '<td>' . $grievance['employee_id'] . '</td>';
        echo '<td>' . $grievance['subject'] . '</td>';
        echo '<td>' . $grievance['description'] . '</td>';
        echo '<td>' . $grievance['status'] . '</td>';
        echo '<td>' . $grievance['category'] . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}