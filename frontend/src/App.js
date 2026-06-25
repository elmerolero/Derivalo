import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import React, { Suspense } from "react";
import Header from './Header';
import { AuthProvider } from './context/AuthContext';
import './App.css';

const GuestRoute = React.lazy(() => import('./GuestRoute'));
const ProtectedRoute = React.lazy(() => import('./ProtectedRoute'));
const Home = React.lazy(() => import('./Home'));
const Console = React.lazy(() => import('./Console'));
const DocsBySection = React.lazy(() => import('./SectionsDocs'));
const LogIn = React.lazy(() => import('./LogIn'));
const UploadArticle = React.lazy(() => import('./UploadArticle'));

function App() {
  return (
    <AuthProvider>
    <Router>
      <Suspense fallback={<p>Cargando...</p>}>
        <div className='bg-teal-400'>
          <div className='md:w-10/12 m-auto bg-teal-500 h-screen'>
            <Header />
            <div className="text-neutral-900 h-[81vh] px-8 bg-teal-500">
              <Routes>
                <Route exact path="/" element={<Home />} />
                <Route exact path="/home" element={<Home />} />
                <Route exact path="/console" element={<ProtectedRoute><Console /></ProtectedRoute>}/>
                <Route exact path="/docs/*" element={<DocsBySection/>} />
                <Route exact path="/login" element={<GuestRoute><LogIn /></GuestRoute>}/>
                <Route exact path="/upload" element={<ProtectedRoute><UploadArticle /></ProtectedRoute>} />
                <Route path="*" element={<h2>404 Not found</h2>}/>
              </Routes>
            </div>
            <div className='border-t bg-teal-500'>
              <p className="text-neutral-100 px-8 text-center">Sitio realizado por elmerolero</p>
            </div>
          </div>
        </div>
        </Suspense>
      </Router>
      </AuthProvider>
    );
}

export default App;
