import { createContext, useContext, useState, useEffect } from 'react';

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const initUser = () => {
      if (typeof window !== 'undefined' && window.__USER__) {
        setUser(window.__USER__);
        localStorage.setItem('user', JSON.stringify(window.__USER__));
      } else {
        const stored = localStorage.getItem('user');
        if (stored) {
          setUser(JSON.parse(stored));
        }
      }
      setLoading(false);
    };

    initUser();
  }, []);

  const login = (userData) => {
    setUser(userData);
    localStorage.setItem('user', JSON.stringify(userData));
  };

  const logout = () => {
    setUser(null);
    localStorage.removeItem('user');
  };

  return (
    <AuthContext.Provider value={{ user, login, logout, loading }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);
