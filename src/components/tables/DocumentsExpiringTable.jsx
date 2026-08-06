const DocumentsExpiringTable = ({ data }) => {
  const defaultDocuments = [
    { employee: 'Juan Dela Cruz', document: 'Employment Contract', expiration: 'Aug 15, 2026', days: 7, priority: 'High' },
    { employee: 'Maria Santos', document: 'Medical Certificate', expiration: 'Aug 20, 2026', days: 12, priority: 'Medium' },
    { employee: 'Pedro Garcia', document: 'Clearance Certificate', expiration: 'Aug 25, 2026', days: 17, priority: 'Medium' },
  ];

  const documents = data && data.length > 0 ? data : defaultDocuments;

  const getPriorityBadge = (priority) => {
    switch (priority) {
      case 'High':
        return <span className="badge badge-danger">High</span>;
      case 'Medium':
        return <span className="badge badge-warning">Medium</span>;
      case 'Low':
        return <span className="badge badge-success">Low</span>;
      default:
        return <span className="badge badge-info">{priority}</span>;
    }
  };

  return (
    <div className="table-container">
      <div className="table-header">
        <h5 className="table-title">Documents Expiring Soon</h5>
        <button className="btn btn-sm btn-primary-custom">View All</button>
      </div>
      <div className="table-responsive">
        <table className="table table-hover mb-0">
          <thead>
            <tr>
              <th>Employee Name</th>
              <th>Document</th>
              <th>Expiration Date</th>
              <th>Days Remaining</th>
              <th>Priority</th>
            </tr>
          </thead>
          <tbody>
            {documents.map((doc, index) => (
              <tr key={index}>
                <td><strong>{doc.employee}</strong></td>
                <td>{doc.document}</td>
                <td>{doc.expiration}</td>
                <td>
                  <span className={`fw-semibold ${doc.days <= 7 ? 'text-danger' : doc.days <= 14 ? 'text-warning' : 'text-success'}`}>
                    {doc.days} days
                  </span>
                </td>
                <td>{getPriorityBadge(doc.priority)}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
};

export default DocumentsExpiringTable;
