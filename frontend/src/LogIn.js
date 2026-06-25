import { useState, useRef } from "react";
import { useNavigate } from 'react-router-dom';
import { useAuth } from './context/AuthContext';
import HCaptcha from '@hcaptcha/react-hcaptcha';

export default function LogIn()
{
    const auth = useAuth();
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [isPending, setIsPending] = useState(false);
    const [error, setError] = useState('');
    const [token, setToken] = useState(null);
    const captchaRef = useRef(null);
    const navigate = useNavigate();
    
    async function handleSubmit(e){
        e.preventDefault();
        await handleLogIn(email, password, token);
    }

    async function handleLogIn(email, password, token) {
        setIsPending(true);
        const {ok, content} = await auth.login(email, password, token);
        setIsPending(false);
        if(!ok){
            setError(content.error ? content.error : 'Error');
            return;
        }
        
        navigate('/console');
    }

    const onHCaptchaLoad = () => {
        // this reaches out to the hCaptcha JS API and runs the
        // execute function on it. you can use other functions as
        // documented here:
        // https://docs.hcaptcha.com/configuration#jsapi
        captchaRef.current.execute();
    };

    return (
        <div>
            <p className="bg-red-600 text-center m-0 text-teal-50">{error}</p>
            
            <div className="w-full md:w-[320px] m-auto">
                <h1 className="text-center">Iniciar sesión</h1><br />
                <form onSubmit={handleSubmit}>
                    <div>
                        <label className="w-full flex flex-col">
                            <span>Email</span>
                            <input type="email"
                                className="rounded p-1 mb-2"
                                onChange={(e) => setEmail(e.target.value)}
                                id="email"/>
                        </label>
                    </div>
                    <div>
                        <label className="w-full flex flex-col">
                            <span>Password</span>
                            <input type="password"
                                className="rounded p-1"
                                onChange={(e) => setPassword(e.target.value)}
                                id="password"/>
                        </label>
                    </div><br/>
                    <div className="flex items-center justify-center w-full">
                        <HCaptcha
                            onLoad={onHCaptchaLoad}
                            sitekey="62ba0834-af18-4792-9c4c-4c8ab26a24ec"
                            onVerify={setToken}
                            ref={captchaRef}
                        />
                    </div><br/>
                    <div className="flex w-full">
                        {!isPending && <button
                                className="bg-teal-950 text-teal-50 hover:bg-teal-50 hover:text-slate-950 rounded p-1 w-1/3 m-auto"
                                onSubmit={(e) => handleSubmit(e)}
                                disabled={!token}>Log In</button>}
                    </div>
                </form>
            </div>
        </div>
    );
}