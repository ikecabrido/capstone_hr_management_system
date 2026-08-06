import { useState, useRef, useEffect } from 'react';
import { FiBell, FiX, FiCheck, FiClock } from 'react-icons/fi';

const NotificationDropdown = () => {
  const [showDropdown, setShowDropdown] = useState(false);
  const dropdownRef = useRef(null);

  const notifications = [
    { id: 1, type: 'task', title: 'Pending Compliance Tasks', message: '12 tasks require your attention', time: '2 hours ago', unread: true },
    { id: 2, type: 'document', title: 'Document Expiration Alert', message: '5 documents expiring within 30 days', time: '5 hours ago', unread: true },
    { id: 3, type: 'report', title: 'Anonymous Report', message: 'New report submitted for review', time: '1 day ago', unread: true },
    { id: 4, type: 'contribution', title: 'Government Contribution Reminder', message: 'SSS submission due in 3 days', time: '2 days ago', unread: false },
    { id: 5, type: 'legal', title: 'Legal Case Update', message: 'Case #2024-001 status changed', time: '3 days ago', unread: false },
    { id: 6, type: 'calendar', title: 'Calendar Deadline', message: 'BIR filing deadline approaching', time: '4 days ago', unread: false },
  ];

  const unreadCount = notifications.filter(n => n.unread).length;

  useEffect(() => {
    const handleClickOutside = (event) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
        setShowDropdown(false);
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  return (
    <div className="notification-dropdown" ref={dropdownRef}>
      <button
        className="notification-btn"
        onClick={() => setShowDropdown(!showDropdown)}
      >
        <FiBell size={20} />
        {unreadCount > 0 && <span className="notification-badge">{unreadCount}</span>}
      </button>

      {showDropdown && (
        <div className="notification-panel">
          <div className="notification-header">
            <h6 className="mb-0">Notifications</h6>
            <button className="btn btn-sm btn-link p-0" onClick={() => setShowDropdown(false)}>
              <FiX size={18} />
            </button>
          </div>
          <div className="notification-list">
            {notifications.map((notification) => (
              <div
                key={notification.id}
                className={`notification-item ${notification.unread ? 'unread' : ''}`}
              >
                <div className="notification-icon">
                  <FiClock size={16} />
                </div>
                <div className="notification-content">
                  <div className="notification-title">{notification.title}</div>
                  <div className="notification-message">{notification.message}</div>
                  <div className="notification-time">{notification.time}</div>
                </div>
                {notification.unread && <div className="notification-dot"></div>}
              </div>
            ))}
          </div>
          <div className="notification-footer">
            <button className="btn btn-sm btn-link w-100">Mark all as read</button>
          </div>
        </div>
      )}
    </div>
  );
};

export default NotificationDropdown;
