import { useState, useEffect } from 'react';
import { apiFetch } from './services/api';
import { getSections } from './services/SectionsService';

export default function UploadArticle(){
    const [name, setName] = useState('');
    const [description, setDescription] = useState('');
    const [file, setFile] = useState(null);
    const [fkSection, setFkSection] = useState('');
    const [sections, setSections] = useState([]);
    const [msg, setMsg] = useState('');

    useEffect(()=>{
        (async ()=> setSections(await getSections()))();
    }, []);

    function handleSubmit(e){
        e.preventDefault();
        if(!file){
            setMsg('Selecciona un archivo.');
            return;
        }

        const form = new FormData();
        if(!name){  setMsg('Debes de establecer un nombre al artículo.'); return; }
        if(!description) setDescription('');
        if(!file){ setMsg('Debes de subir un archivo markdown válido.'); return; }
        if(!fkSection){ setMsg('Debes de establecer una categoría válida.'); return; }
        form.append('name', name);
        form.append('description', description);
        form.append('file', file);
        form.append('fk_section', fkSection);

        (async ()=>{
            try{
                const { res, content } = await apiFetch('/api/content/add', { method: 'POST', body: form });
                if(!res.ok){ setMsg('Error: ' + JSON.stringify(content.error || await res.text())); return; }

                setMsg('Artículo subido. ' + (content && content.data && content.data.slug ? content.data.slug : 'ok'));
            }
            catch(e){ 
                setMsg('Error: ' + e.message);
            }
        })();
    }

    return (
        <div className="w-1/2 m-auto">
            <h2>Subir artículo markdown</h2>
            <form onSubmit={handleSubmit}>
                <div>
                    <label className="w-full flex flex-col">Nombre
                        <input type="text" onChange={e=>setName(e.target.value)} className="rounded p-1 mb-2" required/>
                    </label>
                </div>
                <div>
                    <label className="w-full flex flex-col">Descripción
                        <textarea type="text" onChange={e=>setDescription(e.target.value)} className="rounded p-1 mb-2" required/>
                    </label>
                </div>
                <div>
                    <label className="w-full flex flex-col">Archivo markdown
                        <input type="file" accept=".md,text/markdown" onChange={e=>setFile(e.target.files[0])} className="rounded p-1 mb-2" required/>
                    </label>
                </div>
                <div>
                    <label className="w-full flex flex-col">Sección
                        <select value={fkSection} onChange={e=>setFkSection(e.target.value)} className="rounded p-1 mb-2">
                            <option value="">(ninguna)</option>
                            {sections.map(s=> <option key={s.pk_section} value={s.pk_section}>{s.name}</option>)}
                        </select>
                    </label>
                </div>
                <div className="mt-2 flex">
                    <button className="bg-teal-950 text-teal-50 hover:bg-teal-50 hover:text-slate-950 rounded p-1 w-1/5 m-auto" type="submit">Subir</button>
                </div>
            </form>
            <p>{msg}</p>
        </div>
    );
}
