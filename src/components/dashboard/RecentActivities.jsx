import { FaClipboardCheck, FaFileAlt, FaUserPlus, FaUpload, FaGavel, FaUserSecret } from 'react-icons/fa';

const iconMap = {
  policy: FaClipboardCheck,
  contribution: FaFileAlt,
  employee: FaUserPlus,
  document: FaUpload,
  legal: FaGavel,
  report: FaUserSecret,
};

const RecentActivities = ({ activities }) => {
  const defaultActivities = [
    { id: 1, title: 'Policy Signed: Employee Handbook', time: '2 hours ago', user: 'John Doe', icon: FaClipboardCheck },
    { id: 2, title: 'Government Contribution Submitted', time: '5 hours ago', user: 'SSS Monthly', icon: FaFileAlt },
    { id: 3, title: 'Employee Added', time: '1 day ago', user: 'Maria Santos', icon: FaUserPlus },
    { id: 4, title: 'Document Uploaded', time: '2 days ago', user: 'Employment Contract', icon: FaUpload },
    { id: 5, title: 'Legal Case Updated', time: '3 days ago', user: 'Case #2024-001', icon: FaGavel },
    { id: 6, title: 'Anonymous Report Received', time: '4 days ago', user: 'Workplace Concern', icon: FaUserSecret },
  ];

  const items = activities && activities.length > 0
    ? activities.map((activity, index) => {
        const title = activity.action || activity.title || 'Activity';
        const time = activity.created_at ? new Date(activity.created_at).toLocaleString() : '';
        const user = activity.user_name || activity.user || 'System';
        const Icon = iconMap[activity.type] || FaClipboardCheck;
        return { id: activity.id || index, title, time, user, icon: Icon };
      })
    : defaultActivities;

  return (
    <div className="table-container">
      <div className="table-header">
        <h5 className="table-title">Recent Activities</h5>
      </div>
      <div className="p-3">
        <div className="timeline">
          {items.map((activity) => (
            <div key={activity.id} className="timeline-item">
              <div className="timeline-content">
                <div className="d-flex align-items-start gap-3">
                  <div className="timeline-icon">
                    <activity.icon size={16} />
                  </div>
                  <div className="flex-grow-1">
                    <div className="timeline-title">{activity.title}</div>
                    <div className="timeline-time">{activity.time} - {activity.user}</div>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default RecentActivities;
