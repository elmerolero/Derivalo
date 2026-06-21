import { useState } from "react";
import { useNavigate } from 'react-router-dom';
import { useAuth } from './context/AuthContext';

export default function LogIn()
{
    const auth = useAuth();
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [isPending, setIsPending] = useState(false);
    const [error, setError] = useState('');
    const navigate = useNavigate();    

    async function handleLogIn( email, password ) {
        setIsPending(true);
        const {ok, content} = await auth.login(email, password);
        setIsPending(false);
        if(!ok){
            setError(content.error ? content.error : 'Error');
            return;
        }
        
        navigate('/home');
    }

    async function handleSubmit(e){
        e.preventDefault();
        await handleLogIn(email, password);
    }

    return (
        <div>
            <p className="bg-red-600 text-center m-0 text-teal-50">{error}</p>
            
            <div className="w-full md:w-1/4 m-auto">
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
                    <div className="flex w-full">
                    {!isPending && <button
                            className="bg-teal-950 text-teal-50 hover:bg-teal-50 hover:text-slate-950 rounded p-1 w-1/3 m-auto"
                            onSubmit={(e) => handleSubmit(e)}>Log In</button>}
                    </div>
                </form>
            </div>
        </div>
    );
}