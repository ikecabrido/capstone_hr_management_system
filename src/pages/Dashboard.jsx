import { useState, useEffect } from 'react';
import api from '../services/api';
import ComplianceOverviewChart from '../components/charts/ComplianceOverviewChart';
import MonthlyComplianceTrendChart from '../components/charts/MonthlyComplianceTrendChart';
import DepartmentComplianceTable from '../components/tables/DepartmentComplianceTable';
import DocumentsExpiringTable from '../components/tables/DocumentsExpiringTable';
import RecentActivities from '../components/dashboard/RecentActivities';
import ComplianceCalendar from '../components/calendar/ComplianceCalendar';
import './Dashboard.css';

const Dashboard = () => {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    const fetchDashboard = async () => {
      try {
        const response = await api.get('/dashboard.php');
        if (response.data?.success) {
          setData(response.data);
        } else {
          setError(response.data?.message || 'Failed to load dashboard');
        }
      } catch (err) {
        console.error('Dashboard API error:', err);
        const message = err?.response?.data?.message || err.message || 'Unable to load dashboard data';
        setError(message);
      } finally {
        setLoading(false);
      }
    };

    fetchDashboard();
  }, []);

  if (loading) {
    return (
      <div className="d-flex justify-content-center align-items-center" style={{ minHeight: '300px' }}>
        <div className="spinner-border text-primary" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="alert alert-danger">
        {error}
      </div>
    );
  }

  const stats = data?.stats || {};
  const statCards = [
    { title: 'Compliance Health', value: `${stats.complianceHealth ?? 0}%`, subtitle: 'Overall rate', icon: 'FiClipboardList', color: '#22C55E', bgColor: '#dcfce7' },
    { title: 'Pending Tasks', value: `${stats.pendingTasks ?? 0}`, subtitle: 'Need attention', icon: 'FiClock', color: '#F59E0B', bgColor: '#fef3c7' },
    { title: 'Legal Cases', value: `${stats.legalCases ?? 0}`, subtitle: 'Open cases', icon: 'FaGavel', color: '#EF4444', bgColor: '#fee2e2' },
    { title: 'Anonymous Reports', value: `${stats.anonymousReports ?? 0}`, subtitle: 'Under review', icon: 'FaUserSecret', color: '#8B5CF6', bgColor: '#f3e8ff' },
    { title: 'Govt Contributions', value: `${stats.govtContributions ?? 'N/A'}`, subtitle: 'Next: Aug 15', icon: 'FaFileInvoice', color: '#0A4D9B', bgColor: '#EAF4FF' },
    { title: 'Expiring Docs', value: `${stats.documentsExpiring ?? 0}`, subtitle: 'Within 30 days', icon: 'FiAlertTriangle', color: '#F59E0B', bgColor: '#fef3c7' },
    { title: 'Policies Pending', value: `${stats.policiesPending ?? 0}`, subtitle: 'Awaiting ack', icon: 'FiBookOpen', color: '#2F80ED', bgColor: '#EAF4FF' },
    { title: 'Employees Action', value: `${stats.employeesAction ?? 0}`, subtitle: 'Need follow-up', icon: 'FiUsers', color: '#EF4444', bgColor: '#fee2e2' },
  ];

  const renderIcon = (iconName, size = 20) => {
    const icons = {
      FiClipboardList: <svg xmlns="http://www.w3.org/2000/svg" width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M9 14h6"/><path d="M9 18h6"/></svg>,
      FiClock: <svg xmlns="http://www.w3.org/2000/svg" width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>,
      FaGavel: <svg xmlns="http://www.w3.org/2000/svg" width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m14.5 12.5-8 8a2.12 2.12 0 1 1-3-3l8-8"/><path d="m15 2 6 6"/><path d="M4 20 22 2"/></svg>,
      FaUserSecret: <svg xmlns="http://www.w3.org/2000/svg" width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>,
      FaFileInvoice: <svg xmlns="http://www.w3.org/2000/svg" width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><line x1="10" x2="8" y1="9" y2="9"/></svg>,
      FiAlertTriangle: <svg xmlns="http://www.w3.org/2000/svg" width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>,
      FiBookOpen: <svg xmlns="http://www.w3.org/2000/svg" width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>,
      FiUsers: <svg xmlns="http://www.w3.org/2000/svg" width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>,
    };
    return icons[iconName] || null;
  };

  return (
    <div className="dashboard">
      <div className="row g-3 mb-3">
        {statCards.map((card, index) => (
          <div key={index} className="col-6 col-md-4 col-xl-2">
            <div className="dashboard-card">
              <div className="card-header">
                <div>
                  <div className="card-title">{card.title}</div>
                  <div className="card-value">{card.value}</div>
                  <div className="card-subtitle">{card.subtitle}</div>
                </div>
                <div className="card-icon" style={{ background: card.bgColor, color: card.color }}>
                  {renderIcon(card.icon, 20)}
                </div>
              </div>
            </div>
          </div>
        ))}
      </div>

      <div className="row g-3 mb-3">
        <div className="col-12 col-lg-4">
          <div className="dashboard-card">
            <div className="card-header">
              <h5 className="card-title">Compliance Overview</h5>
            </div>
            <div className="chart-container">
              <ComplianceOverviewChart data={data?.charts?.complianceOverview} />
            </div>
          </div>
        </div>
        <div className="col-12 col-lg-8">
          <div className="dashboard-card">
            <div className="card-header">
              <h5 className="card-title">Monthly Compliance Trend</h5>
            </div>
            <div className="chart-container">
              <MonthlyComplianceTrendChart data={data?.charts?.monthlyTrend} />
            </div>
          </div>
        </div>
      </div>

      <div className="row g-3 mb-3">
        <div className="col-12">
          <DepartmentComplianceTable data={data?.departmentCompliance} />
        </div>
      </div>

      <div className="row g-3 mb-3">
        <div className="col-12 col-lg-8">
          <RecentActivities activities={data?.recentActivities} />
        </div>
        <div className="col-12 col-lg-4">
          <div className="dashboard-card">
            <div className="card-header">
              <h5 className="card-title">Policy Acknowledgement</h5>
            </div>
            <div className="policy-acknowledgement">
              {(data?.policyAcknowledgement?.length ?? 0) > 0 ? (
                data.policyAcknowledgement.map((policy, idx) => (
                  <div className="policy-item" key={idx}>
                    <div className="d-flex justify-content-between mb-1">
                      <span>{policy.name}</span>
                      <span className="fw-semibold">{policy.percentage}%</span>
                    </div>
                    <div className="progress-bar">
                      <div className="progress-fill" style={{ width: `${policy.percentage}%` }}></div>
                    </div>
                  </div>
                ))
              ) : (
                <p className="text-muted mb-0">No policy data available</p>
              )}
            </div>
          </div>
        </div>
      </div>

      <div className="row g-3 mb-3">
        <div className="col-12">
          <DocumentsExpiringTable data={data?.documentsExpiring} />
        </div>
      </div>

      <div className="row g-3 mb-3">
        <div className="col-12">
          <div className="dashboard-card">
            <div className="card-header">
              <h5 className="card-title">Legal Case Summary</h5>
            </div>
            <div className="row g-3">
              <div className="col-6 col-md-3">
                <div className="legal-stat">
                  <div className="legal-stat-value text-danger">{data?.legalCases?.open ?? 0}</div>
                  <div className="legal-stat-label">Open Cases</div>
                </div>
              </div>
              <div className="col-6 col-md-3">
                <div className="legal-stat">
                  <div className="legal-stat-value text-warning">{data?.legalCases?.underInvestigation ?? 0}</div>
                  <div className="legal-stat-label">Under Investigation</div>
                </div>
              </div>
              <div className="col-6 col-md-3">
                <div className="legal-stat">
                  <div className="legal-stat-value text-success">{data?.legalCases?.closed ?? 0}</div>
                  <div className="legal-stat-label">Closed Cases</div>
                </div>
              </div>
              <div className="col-6 col-md-3">
                <div className="legal-stat">
                  <div className="legal-stat-value text-info">{data?.legalCases?.recentlyResolved ?? 0}</div>
                  <div className="legal-stat-label">Recently Resolved</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="row g-3 mb-3">
        <div className="col-12">
          <div className="dashboard-card">
            <div className="card-header">
              <h5 className="card-title">Anonymous Reports</h5>
            </div>
            <div className="row g-3">
              <div className="col-4">
                <div className="anonymous-stat">
                  <div className="anonymous-stat-value text-warning">{data?.anonymousReports?.pending ?? 0}</div>
                  <div className="anonymous-stat-label">Pending</div>
                </div>
              </div>
              <div className="col-4">
                <div className="anonymous-stat">
                  <div className="anonymous-stat-value text-info">{data?.anonymousReports?.investigating ?? 0}</div>
                  <div className="anonymous-stat-label">Investigating</div>
                </div>
              </div>
              <div className="col-4">
                <div className="anonymous-stat">
                  <div className="anonymous-stat-value text-success">{data?.anonymousReports?.resolved ?? 0}</div>
                  <div className="anonymous-stat-label">Resolved</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="row g-3 mb-3">
        <div className="col-12">
          <ComplianceCalendar />
        </div>
      </div>
    </div>
  );
};

export default Dashboard;

