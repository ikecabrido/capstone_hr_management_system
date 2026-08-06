import { Routes, Route, Navigate, Outlet } from 'react-router-dom';
import { useAuth } from './context/AuthContext';
import Sidebar from './components/layout/Sidebar';
import Header from './components/layout/Header';
import Dashboard from './pages/Dashboard';
import Login from './pages/Login';
import Employees from './pages/Employees';
import Compliance from './pages/Compliance';
import GovernmentContributions from './pages/GovernmentContributions';
import Documents from './pages/Documents';
import Policies from './pages/Policies';
import LegalCases from './pages/LegalCases';
import AnonymousReports from './pages/AnonymousReports';
import ComplianceCalendar from './pages/ComplianceCalendar';
import Reports from './pages/Reports';
import Settings from './pages/Settings';

const ProtectedRoute = ({ children }) => {
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <div className="d-flex justify-content-center align-items-center" style={{ height: '100vh' }}>
        <div className="spinner-border text-primary" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
      </div>
    );
  }

  if (!user) {
    return <Navigate to="/login" replace />;
  }

  return children || <Outlet />;
};

const AppLayout = () => {
  return (
    <div className="app-layout">
      <Sidebar />
      <div className="main-content">
        <Header />
        <main className="content-area">
          <Outlet />
        </main>
      </div>
    </div>
  );
};

function App() {
  return (
    <Routes>
      <Route path="/login" element={<Login />} />
      <Route element={<ProtectedRoute />}>
        <Route element={<AppLayout />}>
          <Route index element={<Dashboard />} />
          <Route path="employees" element={<Employees />} />
          <Route path="compliance" element={<Compliance />} />
          <Route path="government-contributions" element={<GovernmentContributions />} />
          <Route path="documents" element={<Documents />} />
          <Route path="policies" element={<Policies />} />
          <Route path="legal-cases" element={<LegalCases />} />
          <Route path="anonymous-reports" element={<AnonymousReports />} />
          <Route path="compliance-calendar" element={<ComplianceCalendar />} />
          <Route path="reports" element={<Reports />} />
          <Route path="settings" element={<Settings />} />
        </Route>
      </Route>
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  );
}

export default App;
