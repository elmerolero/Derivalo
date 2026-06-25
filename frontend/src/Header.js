import { Link, useNavigate } from "react-router-dom";
import { useAuth } from './context/AuthContext';
import { useState } from 'react';

export default function Header(){
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  const [open, setOpen] = useState(false);

  function handleLogout(){
    logout();
    setOpen(false);
    navigate('/');
  }

  return (
    <header className='relative bg-teal-500 text-neutral-50 border-b'>
      <div className='md:flex md:items-center md:justify-between px-4 py-3 md:px-8'>
        <div className='flex items-center justify-between'>
          <div>
            <Link to="/" className='text-2xl font-semibold decoration-teal-400'>Derívalo</Link>
            <p className="text-neutral-800 mt-0 text-sm">Desarrollando software sin causa.</p>
          </div>
          <div className='md:hidden'>
            <button aria-label='Toggle menu' onClick={()=>setOpen(o=>!o)} className='p-2'>
              {open ? (
                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                </svg>
              ) : (
                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                </svg>
              )}
            </button>
          </div>
        </div>

        <nav className={`mt-3 md:mt-0 ${open ? 'block' : 'hidden'} md:block`}>
          <ul className='flex flex-col md:flex-row md:items-center md:gap-4'>
            <li className='flex justify-center order-2 md:order-1 md:justify-end md:mr-2'>
              <Link to="/docs" onClick={()=>setOpen(false)} className="block px-2 py-1 hover:underline decoration-amber-300">Contenido</Link>
            </li>
            {user ? (
              <li className='flex items-center gap-2 order-1 md:order-2'>
                  <div className='flex flex-col items-start  md:items-end'>
                    <p className='text-sm text-neutral-900'>{user.email}</p>
                    <button onClick={handleLogout} className="hover:underline text-sm decoration-blue-600">Cerrar sesión</button>
                  </div>
                  <i className="bi bi-person text-4xl border-2 rounded p-1 hidden md:block"></i>
              </li>
            ) : null}
          </ul>
        </nav>
      </div>
    </header>
  );
}