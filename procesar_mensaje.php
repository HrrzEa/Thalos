<?php
include 'conexion.php';
header('Content-Type: application/json');

// Verificar autenticación
if (!isset($_COOKIE['usuario_logueado'])) exit(json_encode(['error' => 'No autenticado']));

$correo = $_COOKIE['usuario_logueado'];
$stmt = $conn->prepare("SELECT id, tokens FROM usuario WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$usuario_id = $user['id'];
$tokens_disponibles = $user['tokens'];

// ✅ NUEVO: Devolver número de tokens actuales
if (isset($_POST['obtener_tokens'])) {
    echo json_encode(['tokens' => $tokens_disponibles]);
    exit();
}

// Ver si el usuario ha alcanzado el límite de chats
if (isset($_POST['comprobar_limite_chats'])) {
    $res = $conn->prepare("SELECT COUNT(*) as total FROM chat WHERE usuario_id = ?");
    $res->bind_param("i", $usuario_id);
    $res->execute();
    $res_result = $res->get_result()->fetch_assoc();
    $maximo = $res_result['total'] >= 10;
    echo json_encode(['maximo_alcanzado' => $maximo]);
    exit();
}

// CREAR NUEVO CHAT (limitado a 10 simultáneos)
if (isset($_POST['titulo'], $_POST['modelo'], $_POST['averia'], $_POST['marca'], $_POST['anio'])) {
    $res = $conn->prepare("SELECT COUNT(*) as total FROM chat WHERE usuario_id = ?");
    $res->bind_param("i", $usuario_id);
    $res->execute();
    $res_result = $res->get_result()->fetch_assoc();
    if ($res_result['total'] >= 10) {
        echo json_encode(['error' => 'Has alcanzado el número máximo de 10 chats. Elimina alguno para continuar.']);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO chat (usuario_id, titulo, modelo, averia, marca, anio) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssi", $usuario_id, $_POST['titulo'], $_POST['modelo'], $_POST['averia'], $_POST['marca'], $_POST['anio']);
    $stmt->execute();
    echo json_encode(['chat_id' => $stmt->insert_id]);
    exit();
}

// GUARDAR MENSAJE Y RESPUESTA (requiere tokens)
if (isset($_POST['chat_id'], $_POST['pregunta'])) {
    if ($tokens_disponibles <= 0) {
        echo json_encode(['error' => 'No tienes tokens disponibles. Por favor, contacta con el administrador para recargar.']);
        exit();
    }

    $chat_id = intval($_POST['chat_id']);
    $pregunta = $_POST['pregunta'];

    // Guardar pregunta
    $conn->query("INSERT INTO mensaje (chat_id, emisor, contenido) VALUES ($chat_id, 'usuario', '" . $conn->real_escape_string($pregunta) . "')");

    $respuestaIA = consultarIA($chat_id, $pregunta, $conn);

    // Guardar respuesta
    $conn->query("INSERT INTO mensaje (chat_id, emisor, contenido) VALUES ($chat_id, 'ia', '" . $conn->real_escape_string($respuestaIA) . "')");

    // Descontar 1 token
    $conn->query("UPDATE usuario SET tokens = tokens - 1 WHERE id = $usuario_id");

    echo json_encode(['respuesta' => $respuestaIA]);
    exit();
}

// Cargar chat
if (isset($_POST['cargar_chat'])) {
    $chat_id = intval($_POST['cargar_chat']);
    $res = $conn->query("SELECT titulo FROM chat WHERE id = $chat_id AND usuario_id = $usuario_id");
    $titulo = $res->fetch_assoc()['titulo'];

    $res = $conn->query("SELECT emisor, contenido FROM mensaje WHERE chat_id = $chat_id ORDER BY fecha_envio ASC");
    $mensajes = [];
    while ($row = $res->fetch_assoc()) {
        $mensajes[] = $row;
    }
    echo json_encode(['titulo' => $titulo, 'mensajes' => $mensajes]);
    exit();
}

// Eliminar chat
if (isset($_POST['eliminar_chat'])) {
    $chat_id = intval($_POST['eliminar_chat']);
    $conn->query("DELETE FROM chat WHERE id = $chat_id AND usuario_id = $usuario_id");
    exit();
}

function obtenerContextoChat($chat_id, $conn) {
    $res = $conn->query("SELECT modelo, averia, marca, anio FROM chat WHERE id = $chat_id");
    $c = $res->fetch_assoc();
    return "{$c['marca']} {$c['modelo']} ({$c['anio']}): {$c['averia']}";
}

function consultarIA($chat_id, $pregunta, $conn) {
    $apiKey = "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"; // Sustituye por tu clave real
    $contexto = obtenerContextoChat($chat_id, $conn);

    $mensajes = [
        ["role" => "system", "content" => "Eres una inteligencia artificial experta en reparación y diagnóstico de vehículos. Trabajas como asistente interno para técnicos profesionales, tanto en grandes como en pequeños talleres..."]
    ];

    $res = $conn->query("SELECT emisor, contenido FROM mensaje WHERE chat_id = $chat_id ORDER BY fecha_envio ASC");
    while ($row = $res->fetch_assoc()) {
        $mensajes[] = [
            "role" => $row['emisor'] === 'usuario' ? 'user' : 'assistant',
            "content" => $row['contenido']
        ];
    }

    $mensajes[] = ["role" => "user", "content" => "Contexto del vehículo: $contexto\n\n$pregunta"];

    $body = json_encode([
        "model" => "gpt-4o-mini",
        "messages" => $mensajes
    ]);

    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['choices'][0]['message']['content'] ?? "Error al obtener respuesta de la IA.";
}
