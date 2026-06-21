<?php
// Simple admin page for login and creating articles (minimal)
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin - Derívalo</title>
  <style>body{font-family:Arial,Helvetica,sans-serif;margin:20px}label{display:block;margin-top:8px}</style>
</head>
<body>
  <h1>Admin (mínimo)</h1>

  <section id="login-section">
    <h2>Login</h2>
    <label>Email: <input id="email" type="email"></label>
    <label>Password: <input id="password" type="password"></label>
    <button id="login">Login</button>
    <div id="login-result"></div>
  </section>

  <section id="editor-section" style="display:none">
    <h2>Crear artículo</h2>
    <label>Slug: <input id="slug" placeholder="mi-articulo"></label>
    <label>Markdown:</label>
    <textarea id="markdown" rows="12" cols="80"># Título

Contenido...</textarea>
    <br>
    <button id="save">Guardar</button>
    <div id="save-result"></div>
  </section>

  <script>
    const loginBtn = document.getElementById('login');
    const saveBtn = document.getElementById('save');

    loginBtn.addEventListener('click', async () => {
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;
      const res = await fetch('/api/auth/login', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({email,password}),
        credentials: 'same-origin'
      });
      const txt = await res.text();
      if (res.ok) {
        document.getElementById('login-result').innerText = 'Login ok';
        document.getElementById('login-section').style.display = 'none';
        document.getElementById('editor-section').style.display = 'block';
      } else {
        document.getElementById('login-result').innerText = txt;
      }
    });

    saveBtn.addEventListener('click', async () => {
      const slug = document.getElementById('slug').value;
      const markdown = document.getElementById('markdown').value;
      const res = await fetch('/api/content/add', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({slug, markdown}),
        credentials: 'same-origin'
      });
      const json = await res.json().catch(()=>null);
      if (res.ok) {
        document.getElementById('save-result').innerText = 'Guardado: ' + (json && json.slug ? json.slug : 'ok');
      } else {
        document.getElementById('save-result').innerText = 'Error: ' + JSON.stringify(json || await res.text());
      }
    });
  </script>
</body>
</html>
