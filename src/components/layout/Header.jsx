import { useState, useEffect } from 'react';
import { FiSearch, FiLogOut, FiChevronDown, FiCalendar } from 'react-icons/fi';
import { useAuth } from '../../context/AuthContext';
import NotificationDropdown from '../dashboard/NotificationDropdown';
import './Header.css';

const Header = () => {
  const { user, logout } = useAuth();
  const [showProfile, setShowProfile] = useState(false);
  const [showCalendarDot, setShowCalendarDot] = useState(false);

  useEffect(() => {
    const fetchCalendarStatus = async () => {
      try {
        const response = await fetch('/api/calendar_status.php', { credentials: 'same-origin' });
        const data = await response.json();
        if (data.success) {
          setShowCalendarDot(data.show_dot || false);
        }
      } catch (err) {
        console.error('Calendar status API error:', err);
      }
    };

    fetchCalendarStatus();
  }, []);

  return (
    <header className="top-header">
      <div className="header-left">
        <button className="sidebar-toggle d-none d-md-none">
          <i className="fas fa-bars"></i>
        </button>
        <h1 className="page-title">Dashboard</h1>
      </div>
      <div className="header-right">
        <div className="search-box">
          <FiSearch />
          <input type="text" placeholder="Search..." />
        </div>
        <button className="icon-btn calendar-btn" title="Calendar" onClick={() => window.location.href = '/compliance-calendar'}>
          <FiCalendar />
          <span className="cal-event-dot" id="calEventDot" style={{ display: showCalendarDot ? 'block' : 'none' }}></span>
        </button>
        <NotificationDropdown />
        <div className="user-profile" onClick={() => setShowProfile(!showProfile)}>
          <div className="user-avatar">
            {user?.name?.charAt(0).toUpperCase() || 'U'}
          </div>
          <div className="user-info">
            <span className="user-name">{user?.name || 'User'}</span>
            <span className="user-role">{user?.role || 'Admin'}</span>
          </div>
          <FiChevronDown />
        </div>
        {showProfile && (
          <div className="profile-dropdown">
            <button onClick={logout} className="logout-btn">
              <FiLogOut /> Logout
            </button>
          </div>
        )}
      </div>
    </header>
  );
};

export default Header;
