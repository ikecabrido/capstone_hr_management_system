const Employees = () => {
  return (
    <div className="page-content">
      <div className="dashboard-card">
        <div className="card-header">
          <h3 className="card-title">Employee Management</h3>
          <button className="btn-primary-custom">Add Employee</button>
        </div>
        <div className="table-responsive">
          <table className="table">
            <thead>
              <tr>
                <th>Employee ID</th>
                <th>Name</th>
                <th>Department</th>
                <th>Position</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>EMP-001</td>
                <td>Juan Dela Cruz</td>
                <td>Human Resources</td>
                <td>HR Manager</td>
                <td><span className="badge badge-success">Active</span></td>
                <td><button className="btn btn-sm btn-outline-primary">View</button></td>
              </tr>
              <tr>
                <td>EMP-002</td>
                <td>Maria Santos</td>
                <td>Finance</td>
                <td>Accountant</td>
                <td><span className="badge badge-success">Active</span></td>
                <td><button className="btn btn-sm btn-outline-primary">View</button></td>
              </tr>
              <tr>
                <td>EMP-003</td>
                <td>Pedro Garcia</td>
                <td>Operations</td>
                <td>Operations Lead</td>
                <td><span className="badge badge-warning">On Leave</span></td>
                <td><button className="btn btn-sm btn-outline-primary">View</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};

export default Employees;
