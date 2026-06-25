import { useEffect, useState } from "react";
import { Link, useParams } from "react-router-dom";
import { useAuth } from './context/AuthContext';
import { apiFetch } from './services/api';
import { getSections } from "./services/SectionsService";

export default function SectionsDocs(){
    const [sections, setSections] = useState([]);
    const [documents, setDocuments] = useState([]);
    const [selected, setSelected] = useState(null);
    const { user } = useAuth();
    const { id } = useParams();

    useEffect(()=>{
        (async ()=> setSections(await getSections()))();
    },[]);

    useEffect(()=>{
        // if route param provided, select it; otherwise keep current
        if(id) {
            const sid = Number(id);
            if(!Number.isNaN(sid)) selectSection(sid);
        }
    // eslint-disable-next-line react-hooks/exhaustive-deps
    },[id]);

    async function selectSection(sectionId){
        setSelected(sectionId);
        setDocuments([]);
        try{
            const { res, content } = await apiFetch(`/api/content/${sectionId}`);
            if(!res.ok) { setDocuments([]); return; }
            setDocuments(content.data);
        }catch(e){ setDocuments([]); }
    }

    return (
        <div>
            <div className='flex flex-col md:flex-row md:gap-6'>
                <div className="flex-col gap-1 mb-4 md:overflow-auto w-full md:w-1/3 hidden md:flex">
                    <h2 className="pb-0">Secciones</h2>
                    {sections.map(s => (
                        <button key={s.pk_section}
                            className={`px-3 py-2 rounded ${selected===s.pk_section? 'bg-teal-700 text-white':'bg-teal-50'}`}
                            onClick={()=>selectSection(s.pk_section)}>
                            {s.name}
                        </button>
                    ))}
                </div>

                <div className="md:hidden">
                    <h2 className="pb-0">Secciones</h2>
                    <select onChange={(e)=>selectSection(e.target.value)}>
                        {sections.map(s => (
                            <option key={s.pk_section}
                                value={s.pk_section}>
                                {s.name}
                            </option>
                        ))}
                    </select>
                </div>

                <div className="flex flex-col w-full md:w-2/3 gap-1">
                    <div className="flex justify-between items-end w-full">
                        <h2>Documentos</h2>
                        {user ? ( <Link to="/upload" className="bg-amber-500 rounded hover:border p-2 mb-0 decoration-teal-500 hover:bg-teal-500 hover:bg-teal-50">
                                    <i className="bi bi-pencil-square px-1"></i>
                                </Link>) : <></>}
                    </div>
                    
                    <div className="flex flex-col w-full gap-3 md:max-h-[60vh] max-h-[50vh] overflow-auto">
                        {documents.map(doc => (
                            <a key={doc.pk_document} href={`/content/documents/${doc.pk_document}`} className="m-0">
                                <div className="w-full bg-teal-50 p-2 rounded">
                                    <h3 className="m-0">{doc.name}</h3>
                                    <p>{doc.description}</p>
                                </div>
                            </a>
                        ))}
                        {documents.length === 0 && <p className="text-sm text-gray-600">Esta sección no tiene documentos.</p>}
                    </div>
                </div>
            </div>
        </div>
    );
}
