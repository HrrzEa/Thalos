<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>IA para Talleres</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Ajustes para mejorar la visualización de los elementos en la barra de navegación */
    .navbar-nav {
      margin-left: auto;
    }

    .navbar {
      padding: 0.5rem 1rem;
    }

    .navbar-brand {
      font-size: 1.5rem;
    }
  </style>
</head>

<body>

  <!-- Barra de navegación fija -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="#">IA para Talleres</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="#informacion">Información</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#precios">Precios</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#contacto">Contacto</a>
          </li>
        </ul>

        <!-- Botones Login y Register -->
        <div class="d-flex">
          <button class="btn btn-outline-primary me-2" type="button" data-bs-toggle="modal"
            data-bs-target="#loginModal">Login</button>
          <button class="btn btn-outline-secondary" type="button" disabled style="opacity: 0.6; cursor: not-allowed;">
            Register (desactivado)
          </button>

        </div>
      </div>
    </div>
  </nav>


  <!-- Modal Login -->
  <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="loginModalLabel">Iniciar Sesión</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Formulario Login -->
          <form action="login.php" method="POST">
            <div class="mb-3">
              <label for="loginEmail" class="form-label">Correo Electrónico</label>
              <input type="email" class="form-control" id="loginEmail" name="correo" required>
            </div>
            <div class="mb-3">
              <label for="loginPassword" class="form-label">Contraseña</label>
              <input type="password" class="form-control" id="loginPassword" name="contrasena" required>
            </div>
            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const passwordInput = document.getElementById("loginPassword");

      const errorText = document.createElement("div");
      errorText.style.color = "red";
      errorText.style.fontSize = "0.9rem";
      errorText.style.display = "none";
      passwordInput.parentNode.appendChild(errorText);

      const regex = /^[a-zA-Z0-9!@#$%^&*()_\-+=\[\]{}|\\:;"'<>,.?/~`]{6,}$/;

      passwordInput.addEventListener("input", function() {
        if (!regex.test(passwordInput.value)) {
          errorText.textContent = "La contraseña debe tener al menos 6 caracteres válidos.";
          errorText.style.display = "block";
        } else {
          errorText.textContent = "";
          errorText.style.display = "none";
        }
      });

      const loginForm = document.querySelector('#loginModal form');
      loginForm.addEventListener("submit", function(e) {
        if (!regex.test(passwordInput.value)) {
          e.preventDefault(); // Bloquea el envío si es inválida
          errorText.textContent = "La contraseña debe tener al menos 6 caracteres válidos.";
          errorText.style.display = "block";
          passwordInput.focus();
        }
      });
    });
  </script>

  <!-- Modal de Error de Login -->
  <div class="modal fade" id="loginErrorModal" tabindex="-1" aria-labelledby="loginErrorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-danger">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="loginErrorModalLabel">Error al Iniciar Sesión</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body text-center">
          Usuario o contraseña incorrectos. Por favor, verifica tus datos.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Contraseña inválida -->
  <div class="modal fade" id="loginContrasenaModal" tabindex="-1" aria-labelledby="loginContrasenaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-danger">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="loginContrasenaModalLabel">Contraseña inválida</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body text-center">
          La contraseña ingresada no cumple con los requisitos mínimos de seguridad.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>



  <!-- Modal Usuario no encontrado -->
  <div class="modal fade" id="loginUsuarioModal" tabindex="-1" aria-labelledby="loginUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-warning">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="loginUsuarioModalLabel">Usuario no encontrado</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body text-center">
          No se ha encontrado ningún usuario con ese correo electrónico.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>





  <!-- Modal Register -->
  <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="registerModalLabel">Registrarse</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Formulario Register -->
          <form action="registra.php" method="POST">
            <div class="mb-3">
              <label for="registerNombre" class="form-label">Nombre</label>
              <input type="text" class="form-control" id="registerNombre" name="nombre" required>
            </div>
            <div class="mb-3">
              <label for="registerTaller" class="form-label">Nombre del Taller</label>
              <input type="text" class="form-control" id="registerTaller" name="taller" required>
            </div>
            <div class="mb-3">
              <label for="registerEmail" class="form-label">Correo Electrónico</label>
              <input type="email" class="form-control" id="registerEmail" name="correo" required>
            </div>
            <div class="mb-3">
              <label for="registerPassword" class="form-label">Contraseña</label>
              <input type="password" class="form-control" id="registerPassword" name="contrasena" required>
            </div>
            <button type="submit" class="btn btn-primary">Registrar</button>
          </form>

        </div>
      </div>
    </div>
  </div>

  <!-- Contenedor principal -->
  <div class="container mt-5 pt-5">
    <h1 class="text-center">Descubre la IA para Talleres</h1>

    <!-- Sección de información sobre la IA -->
    <section id="informacion">
      <h2 class="mt-5">¿Qué puede hacer nuestra IA?</h2>
      <div class="row mt-5">
        <div class="col-md-4">
          <div class="card">
            <img src="img/evaluacionDeDaños.png" class="card-img-top" alt="Imagen">
            <div class="card-body">
              <h5 class="card-title">Evaluación de Daños</h5>
              <p class="card-text">Sube fotos de piezas dañadas para que la IA evalúe su estado y te brinde posibles
                soluciones y recomendaciones para su reparación.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <img src="img/busqueda.png" class="card-img-top" alt="Imagen IA">
            <div class="card-body">
              <h5 class="card-title">Búsqueda en Documentación y Foros</h5>
              <p class="card-text">La IA puede buscar en documentación técnica oficial y foros especializados para
                encontrar soluciones a problemas comunes en talleres.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <img src="img/diagnostico.png" class="card-img-top" alt="Imagen IA">
            <div class="card-body">
              <h5 class="card-title">Diagnóstico Predictivo</h5>
              <p class="card-text">La IA puede predecir fallos potenciales en las piezas o maquinaria, basándose en
                datos históricos, patrones de desgaste y análisis de fallos previos.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Sección de precios -->
    <section id="precios">
      <h2 class="mt-5">Planes y Precios</h2>
      <div class="row">
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Plan Básico</h5>
              <p class="card-text">Acceso a 50 usos al mes con funciones básicas de diagnóstico y asesoría.</p>
              <h6 class="card-subtitle mb-2 text-muted">$10/mes</h6>
              <p class="card-text">50 usos por mes</p>
              <a href="#" class="btn btn-primary">Suscribirse</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Plan Intermedio</h5>
              <p class="card-text">Acceso a 250 usos al mes con diagnóstico completo y recomendaciones para
                optimización.</p>
              <h6 class="card-subtitle mb-2 text-muted">$30/mes</h6>
              <p class="card-text">250 usos por mes</p>
              <a href="#" class="btn btn-primary">Suscribirse</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Plan Profesional</h5>
              <p class="card-text">Acceso a 1000 usos al mes con soporte 24/7, análisis avanzado y optimización de
                procesos en tiempo real.</p>
              <h6 class="card-subtitle mb-2 text-muted">$100/mes</h6>
              <p class="card-text">1000 usos por mes</p>
              <a href="#" class="btn btn-primary">Suscribirse</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Sección de contacto -->
    <section id="contacto" class="mt-5">
      <h2>Contacto</h2>
      <p>Para más información o dudas, no dudes en contactarnos.</p>
      <form>
        <div class="mb-3">
          <label for="nombre" class="form-label">Nombre</label>
          <input type="text" class="form-control" id="nombre" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Correo Electrónico</label>
          <input type="email" class="form-control" id="email" required>
        </div>
        <button type="submit" class="btn btn-primary">Enviar</button>
      </form>
    </section>
  </div>




  <!-- Footer -->
  <footer class="bg-light text-center text-lg-start mt-5 border-top">
    <div class="container p-4">
      <div class="row">
        <div class="col-lg-6 col-md-12 mb-4 mb-md-0 text-start">
          <h5 class="text-uppercase">IA para Talleres</h5>
          <p>
            Optimiza tu trabajo con inteligencia artificial diseñada específicamente para talleres mecánicos. Rápido,
            confiable y al alcance de todos.
          </p>
        </div>

        <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
          <h5 class="text-uppercase">Enlaces</h5>
          <ul class="list-unstyled mb-0">
            <li><a href="#informacion" class="text-dark">Información</a></li>
            <li><a href="#precios" class="text-dark">Precios</a></li>
            <li><a href="#contacto" class="text-dark">Contacto</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
          <h5 class="text-uppercase">Contacto</h5>
          <ul class="list-unstyled mb-0">
            <li><span class="text-dark">Email: contacto@iatalleres.com</span></li>
            <li><span class="text-dark">Tel: +34 600 000 000</span></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="text-center p-3 bg-secondary text-white">
      © 2025 IA para Talleres · Todos los derechos reservados.
    </div>
  </footer>



  <!-- Modal de Éxito -->
  <div class="modal fade" id="registroExitosoModal" tabindex="-1" aria-labelledby="registroExitosoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-success">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="registroExitosoModalLabel">Registro Exitoso</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body text-center">
          ¡Tu cuenta ha sido creada con éxito! Ahora puedes iniciar sesión.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" data-bs-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>


  <!-- Modal de Error por Correo Repetido -->
  <div class="modal fade" id="registroDuplicadoModal" tabindex="-1" aria-labelledby="registroDuplicadoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-danger">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="registroDuplicadoModalLabel">Error de Registro</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body text-center">
          Ya existe una cuenta registrada con ese correo electrónico.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de Error Genérico -->
  <div class="modal fade" id="registroErrorModal" tabindex="-1" aria-labelledby="registroErrorModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-warning">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="registroErrorModalLabel">Error</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body text-center">
          Ha ocurrido un error durante el registro. Inténtalo de nuevo más tarde.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>



  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
  <script>
    // Función para obtener el valor de una cookie
    function getCookie(nombre) {
      const name = nombre + "=";
      const decodedCookie = decodeURIComponent(document.cookie);
      const ca = decodedCookie.split(';');
      for (let i = 0; i < ca.length; i++) {
        let c = ca[i].trim();
        if (c.indexOf(name) === 0) return c.substring(name.length, c.length);
      }
      return "";
    }

    /// Ejecutar al cargar la página
    document.addEventListener("DOMContentLoaded", function() {
      const loginBtn = document.querySelector('[data-bs-target="#loginModal"]');
      const registerBtn = document.querySelector('[data-bs-target="#registerModal"]');
      const container = document.querySelector(".navbar .d-flex");

      // Verificar si el usuario ya está logueado (cookie presente)
      if (getCookie("usuario_logueado") !== "") {
        if (loginBtn) loginBtn.remove();
        if (registerBtn) registerBtn.remove();

        // Botón Área Personal con redirección directa al hacer clic
        const areaPersonalBtn = document.createElement("button");
        areaPersonalBtn.className = "btn btn-outline-primary me-2";
        areaPersonalBtn.innerText = "Área Personal";
        areaPersonalBtn.onclick = function() {
          window.location.href = "area_personal.php";
        };

        // Botón Cerrar Sesión
        const logoutBtn = document.createElement("button");
        logoutBtn.className = "btn btn-outline-secondary";
        logoutBtn.innerText = "Cerrar Sesión";
        logoutBtn.onclick = function() {
          // Elimina cookie y recarga la página
          document.cookie = "usuario_logueado=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
          location.reload();
        };

        // Agregar ambos botones al navbar
        container.appendChild(areaPersonalBtn);
        container.appendChild(logoutBtn);
      }
    });
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const urlParams = new URLSearchParams(window.location.search);
      const tipo = urlParams.get("registro");
      const loginError = urlParams.get("login");

      if (tipo === "exito") {
        new bootstrap.Modal(document.getElementById('registroExitosoModal')).show();
      } else if (tipo === "correo_repetido") {
        new bootstrap.Modal(document.getElementById('registroDuplicadoModal')).show();
      } else if (tipo === "error") {
        new bootstrap.Modal(document.getElementById('registroErrorModal')).show();
      }

      // Limpiar URL
      if (tipo || loginError) {
        window.history.replaceState({}, document.title, window.location.pathname);
      }
    });
  </script>

  <script>
    const loginError = new URLSearchParams(window.location.search).get("login");
    console.log("loginError =", loginError); // 👈 Añade esto

    if (loginError === "error") {
      new bootstrap.Modal(document.getElementById('loginErrorModal')).show();
    } else if (loginError === "usuario_no_existe") {
      new bootstrap.Modal(document.getElementById('loginUsuarioModal')).show();
    } else if (loginError === "contrasena_invalida") {
      new bootstrap.Modal(document.getElementById('loginContrasenaModal')).show();
    }
  </script>




</body>

</html>