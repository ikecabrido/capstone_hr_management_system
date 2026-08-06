const DepartmentComplianceTable = ({ data }) => {
  const defaultDepartments = [
    { name: 'Human Resources', compliance: 92, status: 'Good' },
    { name: 'Finance', compliance: 88, status: 'Good' },
    { name: 'Operations', compliance: 75, status: 'Warning' },
    { name: 'IT Department', compliance: 95, status: 'Good' },
  ];

  const departments = data && data.length > 0 ? data : defaultDepartments;

  const getStatusBadge = (status) => {
    switch (status) {
      case 'Good':
        return <span className="badge badge-success">Good</span>;
      case 'Warning':
        return <span className="badge badge-warning">Warning</span>;
      case 'Critical':
        return <span className="badge badge-danger">Critical</span>;
      default:
        return <span className="badge badge-info">{status}</span>;
    }
  };

  return (
    <div className="table-container">
      <div className="table-header">
        <h5 className="table-title">Department Compliance</h5>
      </div>
      <div className="table-responsive">
        <table className="table table-hover mb-0">
          <thead>
            <tr>
              <th>Department</th>
              <th>Compliance</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            {departments.map((dept, index) => (
              <tr key={index}>
                <td><strong>{dept.name}</strong></td>
                <td>
                  <div className="d-flex align-items-center gap-2">
                    <div className="progress-bar" style={{ width: '120px' }}>
                      <div className="progress-fill" style={{ width: `${dept.compliance}%` }}></div>
                    </div>
                    <span className="fw-semibold">{dept.compliance}%</span>
                  </div>
                </td>
                <td>{getStatusBadge(dept.status)}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
};

export default DepartmentComplianceTable;
