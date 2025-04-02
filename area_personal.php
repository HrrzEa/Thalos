<?php
if (!isset($_COOKIE['usuario_logueado'])) {
  header("Location: index.html");
  exit();
}

include 'conexion.php';
$correo = $_COOKIE['usuario_logueado'];
$stmt = $conn->prepare("SELECT id, nombre, tokens FROM usuario WHERE correo = ?");



$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
  setcookie("usuario_logueado", "", time() - 3600, "/");
  header("Location: index.php");
  exit();
}
$usuario_tokens = $user['tokens'];
$usuario_id = $user['id'];
$usuario_nombre = $user['nombre'];

$chats = [];
$stmt = $conn->prepare("SELECT id, titulo FROM chat WHERE usuario_id = ? ORDER BY fecha_creacion DESC");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
  $chats[] = $row;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Área Personal - IA para Talleres</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <style>
    body {
      overflow-x: hidden;
      font-family: Arial, sans-serif;
      background-color: #fff;
    }

    .sidebar {
      width: 250px;
      height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      background: #f8f9fa;
      padding: 1rem;
      border-right: 1px solid #dee2e6;
      overflow-y: auto;
    }

    @media (max-width: 768px) {
      .sidebar {
        display: none;
      }

      .sidebar.show {
        display: block;
        position: absolute;
        z-index: 1000;
        background: #f8f9fa;
      }
    }

    .main {
      margin-left: 270px;
      padding: 10rem;
    }

    @media (max-width: 768px) {
      .main {
        margin-left: 0;
        padding: 2rem;
      }
    }

    .chat-list button {
      width: 100%;
      margin-bottom: 0.5rem;
      text-align: left;
      position: relative;
    }

    .chat-list .delete-btn {
      position: absolute;
      right: 10px;
      top: 5px;
      background: transparent;
      border: none;
      color: white;
      display: none;
    }

    .chat-list button:hover .delete-btn {
      display: inline;
    }

    #historial {
      overflow-y: visible;
      display: flex;
      flex-direction: column;
    }

    .mensaje-usuario,
    .mensaje-ia {
      max-width: 80%;
      padding: 1rem;
      border-radius: 0.75rem;
      font-size: 1rem;
      line-height: 1.5;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
      opacity: 0;
      transform: translateY(10px);
      animation: aparecer 0.3s ease-out forwards;
      margin-bottom: 1rem;
    }

    .mensaje-usuario {
      background-color: #f1f1f1;
      align-self: flex-end;
    }

    .mensaje-ia {
      background-color: transparent;
      margin-right: auto;
      margin-left: 0;
      border: none;
    }

    @keyframes aparecer {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .titulo-sidebar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .btn-nuevo-chat {
      border: 1px solid #6c757d;
      background-color: transparent;
      color: #6c757d;
      padding: 0.25rem 0.6rem;
      font-size: 1rem;
      transition: all 0.2s ease-in-out;
    }

    .btn-nuevo-chat:hover {
      background-color: #e9ecef;
      color: #343a40;
    }


    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        position: fixed;
        z-index: 1050;
      }

      .sidebar.show {
        transform: translateX(0);
      }

      .main {
        margin-left: 0;
        padding: 6rem 1rem 1rem 1rem;
      }
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-light bg-light shadow-sm justify-content-between fixed-top">
    <div class="d-flex align-items-center">
      <button id="toggleSidebar" class="btn btn-outline-secondary d-md-none ms-2">
        <i class="bi bi-list"></i>
      </button>

      <span class="navbar-brand mb-0 h1 ms-3">
        Hola, <?php echo htmlspecialchars($usuario_nombre); ?>
        <span class="badge bg-primary ms-2">Tokens: <span id="tokenCount"><?php echo $usuario_tokens; ?></span></span>

      </span>

    </div>
    <button class="btn btn-outline-secondary me-3" onclick="cerrarSesion()">Cerrar Sesión</button>
  </nav>

  <div class="sidebar" id="sidebar" style="position: fixed; top: 56px; z-index: 1030;">

    <div class="titulo-sidebar">
      <h5 class="mb-0">Historial de casos</h5>
      <button class="btn btn-nuevo-chat" onclick="comprobarLimiteChats(); mostrarFormularioNuevoChat()" data-bs-toggle="tooltip" title="Crear nuevo chat">+</button>

    </div>
    <div class="chat-list" id="chatList">
      <?php foreach ($chats as $chat): ?>
        <button class="btn btn-outline-dark" onclick="cargarChat(<?php echo $chat['id']; ?>)">
          <?php echo htmlspecialchars($chat['titulo']); ?>
          <span class="delete-btn" onclick="confirmarEliminacion(event, <?php echo $chat['id']; ?>)">&times;</span>
        </button>
      <?php endforeach; ?>
    </div>
  </div>



  <div class="main">
    <h2 id="tituloChat">Crear Nuevo Chat</h2>
    <form id="formNuevoChat">
      <div class="mb-2"><input type="text" class="form-control" name="titulo" placeholder="Título del chat" required></div>
      <div class="mb-2"><input type="text" class="form-control" name="modelo" placeholder="Modelo del coche" required></div>
      <div class="mb-2"><input type="text" class="form-control" name="averia" placeholder="Descripción de la avería" required></div>
      <div class="mb-2"><input type="text" class="form-control" name="marca" placeholder="Marca" required></div>
      <div class="mb-3"><input type="number" class="form-control" name="anio" placeholder="Año" required></div>
      <button type="submit" id="btnGenerarChat" class="btn btn-primary">Generar Chat</button>


    </form>

    <div id="formularioChat" class="mt-5 d-none">
      <div id="historial"></div>
      <form id="formIA" class="mt-3">
        <textarea class="form-control" id="pregunta" rows="3" placeholder="Escribe tu mensaje..." required></textarea>
        <button type="submit" id="btnEnviar" class="btn btn-success mt-2">Enviar</button>
        <span id="cargandoIA" class="ms-2 text-muted d-none">⏳ Cargando respuesta...</span>
      </form>
    </div>
  </div>

  <!-- Modal de confirmación siii -->
  <div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">¿Eliminar chat?</h5>
        </div>
        <div class="modal-body">
          <p>¿Estás seguro de que deseas eliminar este chat? No podrás recuperarlo después.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-danger" id="btnConfirmDelete">Borrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de error -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="errorModalLabel">Atención</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="errorModalBody">
        <!-- Aquí se mostrará el mensaje de error dinámicamente -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


  <script>
    let chatIdActivo = null;
    let chatIdAEliminar = null;

    function cerrarSesion() {
      document.cookie = "usuario_logueado=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
      window.location.href = "index.php";
    }

    function mostrarFormularioNuevoChat() {
      document.getElementById("formNuevoChat").classList.remove("d-none");
      document.getElementById("formularioChat").classList.add("d-none");
      document.getElementById("tituloChat").innerText = "Crear Nuevo Chat";
    }

    document.getElementById("formNuevoChat").addEventListener("submit", async function(e) {
  e.preventDefault();

  const totalChats = document.querySelectorAll("#chatList button").length;

  if (totalChats >= 10) {
    // ⚠️ Mostrar error si ya hay 10
    const modal = new bootstrap.Modal(document.getElementById("errorModal"));
    document.getElementById("errorModalBody").innerText = "Has alcanzado el número máximo de 10 chats. Elimina uno para continuar.";
    modal.show();

    // 🔴 Cambiar botón a rojo
    const btnGenerar = document.getElementById("btnGenerarChat");
    btnGenerar.classList.remove("btn-primary");
    btnGenerar.classList.add("btn-danger");
    return;
  }

  // ✅ Restaurar color si antes estaba rojo
  const btnGenerar = document.getElementById("btnGenerarChat");
  btnGenerar.classList.remove("btn-danger");
  btnGenerar.classList.add("btn-primary");

  // Enviar datos
  const formData = new FormData(this);
  const res = await fetch("procesar_mensaje.php", {
    method: "POST",
    body: formData
  });

  const data = await res.json();

  if (data.error) {
    const modal = new bootstrap.Modal(document.getElementById("errorModal"));
    document.getElementById("errorModalBody").innerText = data.error;
    modal.show();
    return;
  }

  if (data.chat_id) {
    chatIdActivo = data.chat_id;
    document.getElementById("formularioChat").classList.remove("d-none");
    document.getElementById("tituloChat").innerText = formData.get("titulo");
    document.getElementById("historial").innerHTML = "";
    this.classList.add("d-none");
  }
});



    document.getElementById("toggleSidebar").addEventListener("click", function() {
      document.querySelector(".sidebar").classList.toggle("show");
    });



    document.getElementById("formIA").addEventListener("submit", async function(e) {
      e.preventDefault();

      if (tokensActuales <= 0) {
        alert("No tienes tokens disponibles. Contacta con el administrador.");
        return;
      }

      const pregunta = document.getElementById("pregunta").value.trim();
      if (!pregunta || !chatIdActivo) return;

      mostrarMensaje("usuario", pregunta);
      document.getElementById("btnEnviar").disabled = true;
      document.getElementById("cargandoIA").classList.remove("d-none");

      const formData = new FormData();
      formData.append("chat_id", chatIdActivo);
      formData.append("pregunta", pregunta);

      const res = await fetch("procesar_mensaje.php", {
        method: "POST",
        body: formData
      });
      const data = await res.json();

      if (data.error) {
        document.getElementById("errorModalBody").innerHTML = data.error;
        const modal = new bootstrap.Modal(document.getElementById("errorModal"));
        modal.show();
        actualizarTokens();
        document.getElementById("btnEnviar").disabled = false;
        document.getElementById("cargandoIA").classList.add("d-none");
        return;
      }

      escribirMensajeIAAnimado(data.respuesta);
      document.getElementById("pregunta").value = "";
      document.getElementById("btnEnviar").disabled = false;
      document.getElementById("cargandoIA").classList.add("d-none");
      actualizarTokens();
    });


    function mostrarMensaje(emisor, contenido) {
      const contenedor = document.getElementById("historial");
      const div = document.createElement("div");
      div.className = emisor === "usuario" ? "mensaje-usuario" : "mensaje-ia";
      div.innerHTML = render(contenido); // Aplica el formateo
      contenedor.appendChild(div);
    }




    async function cargarChat(id) {
      const formData = new FormData();
      formData.append("cargar_chat", id);
      const res = await fetch("procesar_mensaje.php", {
        method: "POST",
        body: formData
      });
      const data = await res.json();
      chatIdActivo = id;
      document.getElementById("formularioChat").classList.remove("d-none");
      document.getElementById("formNuevoChat").classList.add("d-none");
      document.getElementById("tituloChat").innerText = data.titulo;
      document.getElementById("historial").innerHTML = "";
      data.mensajes.forEach(m => mostrarMensaje(m.emisor, m.contenido));
      scrollAlFinal();

    }

    function confirmarEliminacion(event, id) {
      event.stopPropagation();
      chatIdAEliminar = id;
      const modal = new bootstrap.Modal(document.getElementById("confirmModal"));
      modal.show();
    }

    document.getElementById("btnConfirmDelete").addEventListener("click", async () => {
      if (!chatIdAEliminar) return;
      const formData = new FormData();
      formData.append("eliminar_chat", chatIdAEliminar);
      await fetch("procesar_mensaje.php", {
        method: "POST",
        body: formData
      });
      location.reload();
    });
  </script>
  <script>
    // Función global para formatear los mensajes
    function render(text) {
      return text
        .replace(/^###\s*(.+)$/gm, '<h3>$1</h3>') // Títulos con ###
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // esto es para **hola**
        .replace(/\n/g, '<br>'); // Saltos de línea
    }

    function escribirMensajeIAAnimado(texto) {
      const contenedor = document.getElementById("historial");
      const div = document.createElement("div");
      div.className = "mensaje-ia";
      contenedor.appendChild(div);

      let i = 0;
      let buffer = "";

      function escribir() {
        if (i < texto.length) {
          buffer += texto[i];
          div.innerHTML = render(buffer);
          i++;
          scrollAlFinal();
          setTimeout(escribir, 15);
        }
      }

      escribir();
    }

    function scrollAlFinal() {
      setTimeout(() => {
        window.scrollTo({
          top: document.body.scrollHeight,
          behavior: 'smooth'
        });
      }, 100);
    }
  </script>
  <script>
    async function actualizarTokens() {
      const formData = new FormData();
      formData.append("obtener_tokens", 1);

      try {
        const res = await fetch("procesar_mensaje.php", {
          method: "POST",
          body: formData
        });
        const data = await res.json();
        if (data.tokens !== undefined) {
          document.getElementById("tokenCount").textContent = data.tokens;
        }
      } catch (e) {
        console.error("Error al actualizar tokens", e);
      }
    }
  </script>
  <script>
    let tokensActuales = <?php echo $usuario_tokens; ?>;

    function actualizarTokens() {
      fetch('procesar_mensaje.php', {
          method: 'POST',
          body: new URLSearchParams({
            obtener_tokens: 1
          })
        })
        .then(res => res.json())
        .then(data => {
          tokensActuales = parseInt(data.tokens);
          document.querySelector('.badge.bg-primary').textContent = 'Tokens: ' + tokensActuales;

          const btn = document.getElementById("btnEnviar");
          if (tokensActuales <= 0) {
            btn.disabled = true;
            btn.textContent = "Sin tokens";
            btn.classList.remove("btn-success");
            btn.classList.add("btn-secondary");
          } else {
            btn.disabled = false;
            btn.textContent = "Enviar";
            btn.classList.add("btn-success");
            btn.classList.remove("btn-secondary");
          }
        });
    }

    // Llama a esta función al iniciar
    document.addEventListener('DOMContentLoaded', actualizarTokens);
  </script>
  <script>
    async function comprobarLimiteChats() {
      const formData = new FormData();
      formData.append("comprobar_limite_chats", 1);

      const res = await fetch("procesar_mensaje.php", {
        method: "POST",
        body: formData
      });

      const data = await res.json();

      mostrarFormularioNuevoChat(); // Mostrar el formulario

      const boton = document.getElementById("btnGenerarChat");

      if (data.maximo_alcanzado) {
        boton.disabled = true;
        boton.classList.remove("btn-primary");
        boton.classList.add("btn-danger");
        boton.innerText = "Máximo de chats alcanzado";

        // Mostrar modal si existe
        if (document.getElementById("errorModalBody")) {
          document.getElementById("errorModalBody").innerText = "Debes eliminar un chat antes de crear uno nuevo.";
          const modal = new bootstrap.Modal(document.getElementById("errorModal"));
          modal.show();
        }
      } else {
        boton.disabled = false;
        boton.classList.remove("btn-danger");
        boton.classList.add("btn-primary");
        boton.innerText = "Generar Chat";
      }
    }
  </script>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>



  <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="errorModalLabel">Error</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body" id="errorModalBody">
          <!-- Mensaje de error -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

</body>

</html>