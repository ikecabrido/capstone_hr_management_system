import { NavLink } from 'react-router-dom';
import { FaHome, FaUsers, FaClipboardCheck, FaFileAlt, FaFileInvoice, FaBook, FaGavel, FaUserSecret, FaCalendarAlt, FaChartBar, FaCog } from 'react-icons/fa';
import bcpLogo from '../../../assets/pics/bcpLogo.png';
import './Sidebar.css';

const Sidebar = () => {
  const menuItems = [
    { path: '/', icon: FaHome, label: 'Dashboard' },
    { path: '/employees', icon: FaUsers, label: 'Employees' },
    { path: '/compliance', icon: FaClipboardCheck, label: 'Compliance' },
    { path: '/government-contributions', icon: FaFileInvoice, label: 'Government Contributions' },
    { path: '/documents', icon: FaFileAlt, label: 'Documents' },
    { path: '/policies', icon: FaBook, label: 'Policies' },
    { path: '/legal-cases', icon: FaGavel, label: 'Legal Cases' },
    { path: '/anonymous-reports', icon: FaUserSecret, label: 'Anonymous Reports' },
    { path: '/compliance-calendar', icon: FaCalendarAlt, label: 'Compliance Calendar' },
    { path: '/reports', icon: FaChartBar, label: 'Reports' },
    { path: '/settings', icon: FaCog, label: 'Settings' },
  ];

  return (
    <aside className="sidebar">
      <div className="sidebar-header">
        <NavLink to="/" className="sidebar-brand">
          <img src={bcpLogo} alt="BESTLINK Logo" />
          <div>
            <h2>BESTLINK</h2>
            <small style={{ opacity: 0.8 }}>HR Compliance</small>
          </div>
        </NavLink>
      </div>
      <ul className="sidebar-nav">
        {menuItems.map((item) => (
          <li key={item.path}>
            <NavLink
              to={item.path}
              className={({ isActive }) => isActive ? 'active' : ''}
              end={item.path === '/'}
            >
              <item.icon />
              <span>{item.label}</span>
            </NavLink>
          </li>
        ))}
      </ul>
    </aside>
  );
};

export default Sidebar;
