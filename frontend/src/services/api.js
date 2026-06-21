import { getAccessToken, setAccessToken } from './AuthStore';

// Use a relative base by default so the dev server's proxy can forward requests
const API_BASE = process.env.REACT_APP_API_BASE || '';

export async function refreshAccessToken(){
  try{
    const res = await fetch(`${API_BASE}/api/auth/refresh`, { method: 'POST', credentials: 'include' });
    if(!res.ok) return false;
    const json = await res.json();
    if(json && json.data && json.data.access_token) {
      setAccessToken(json.data.access_token);
      return true;
    }
    return false;
  }
  catch(e){
    return false;
  }
}

export async function apiFetch(path, opts = {}){
  const url = `${API_BASE}${path}`;
  const { headers, ...rest } = opts;
  let accessToken = getAccessToken();
  
  const makeRequest = async () => {
    const merged = {
      credentials: 'include',
      headers: Object.assign({},
        { 'Accept': 'application/json' },
        accessToken ? { 'Authorization': `Bearer ${accessToken}` } : {},
        headers || {}
      ),
      ...rest
    };

    const res = await fetch(url, merged);
    const text = await res.text();
    try {
      const json = JSON.parse(text);
      return { res, content: json };
    }
    catch (e) {
      return { res, content: text };
    }
  };

  let result = await makeRequest();
  if(result.res && result.res.status === 401){
    const ok = await refreshAccessToken();
    if(ok){
      accessToken = getAccessToken();
      result = await makeRequest();
    }
  }

  return result;
}
