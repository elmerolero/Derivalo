import React, { createContext, useContext, useEffect, useState } from 'react';
import { setAccessToken, clearAccessToken } from '../services/AuthStore';
import { apiFetch } from '../services/api';

const AuthContext = createContext(null);

export function AuthProvider({ children }){
  const [loading, setLoading] = useState(true);
  const [user, setUser] = useState(null);

  useEffect(() => {
    let mounted = true;
    getCurrent(mounted)
      .then( user => setUser(user) )
      .catch(() => setUser(null))
      .finally(() => setLoading(false));

    return () => { 
      mounted = false 
    };
  }, []);

  async function login(email, password, token) {
    const { res, content } = await apiFetch('/api/auth/login', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ email, password, token })
    });
    
    if(!res.ok) {
      return { ok: false, content };
    }
    
    setAccessToken(content.data.access_token);
    const current = await getCurrent(true);
    setUser(current);
    return { ok: true, content }; 
  }

  async function logout(){
    await apiFetch('/api/auth/logout', { method: 'POST' });
    clearAccessToken();
    setUser(null);
  }

  return (
    <AuthContext.Provider value={{ user, loading, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

async function getCurrent(mounted) {
  const { res, content } = await apiFetch('/api/auth/current');
  if(!mounted) return;
  if(!res.ok || !content?.data || content?.data === 0) {
    return null;
  }

  return content.data;
}

export function useAuth(){
  return useContext(AuthContext);
}
