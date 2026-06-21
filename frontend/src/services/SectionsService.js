import { apiFetch } from './api';

export async function getSections() {
    try{
        const { res, content } = await apiFetch('/api/sections');
        if(!res.ok)
            return [];
        return content.data;
    }
    catch(e){ 
        return [];
    }
}