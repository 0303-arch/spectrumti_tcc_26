<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=UTF-8");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
========================================
VALIDAÇÃO LOGIN
========================================
*/

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Usuário não autenticado"
    ]);

    exit;
}

$usuario_id = (int) $_SESSION["user_id"];

/*
========================================
CONEXÃO
========================================
*/

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "spectrum"
);

if ($conn->connect_error) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro conexão banco",
        "erro" => $conn->connect_error
    ]);

    exit;
}

$conn->set_charset("utf8mb4");

/*
========================================
BUSCA DADOS USUÁRIO
========================================
*/

$sqlUsuario = "
SELECT
    nome,
    email
FROM usuarios
WHERE id = ?
LIMIT 1
";

$stmtUsuario = $conn->prepare($sqlUsuario);

if (!$stmtUsuario) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro prepare usuário",
        "erro" => $conn->error
    ]);

    exit;
}

$stmtUsuario->bind_param("i", $usuario_id);

$stmtUsuario->execute();

$resultadoUsuario = $stmtUsuario->get_result();

$usuario = $resultadoUsuario->fetch_assoc();

if (!$usuario) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Usuário não encontrado"
    ]);

    exit;
}

$nome = trim($usuario["nome"]);
$email = trim($usuario["email"]);

/*
========================================
DEBUG POST
========================================
*/

if (empty($_POST)) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "POST vazio",
        "debug" => $_POST
    ]);

    exit;
}

/*
========================================
DADOS FORMULÁRIO
========================================
*/

$situacao_ti = trim($_POST["situacao_ti"] ?? "");
$curso_detalhes = trim($_POST["curso_detalhes"] ?? "");
$conteudos_ensino = trim($_POST["conteudos_ensino"] ?? "");
$experiencia_previa = trim($_POST["experiencia_previa"] ?? "");
$disponibilidade_horario = trim($_POST["disponibilidade_horario"] ?? "");
$acesso_tecnologia = trim($_POST["acesso_tecnologia"] ?? "");
$motivacao_voluntariado = trim($_POST["motivacao_voluntariado"] ?? "");
$observacoes_adicionais = trim($_POST["observacoes_adicionais"] ?? "");

/*
========================================
VALIDAÇÃO CAMPOS
========================================
*/

$camposObrigatorios = [

    "situacao_ti" => $situacao_ti,
    "conteudos_ensino" => $conteudos_ensino,
    "experiencia_previa" => $experiencia_previa,
    "disponibilidade_horario" => $disponibilidade_horario,
    "acesso_tecnologia" => $acesso_tecnologia,
    "motivacao_voluntariado" => $motivacao_voluntariado

];

foreach ($camposObrigatorios as $campo => $valor) {

    if (empty($valor)) {

        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Campo obrigatório vazio",
            "campo" => $campo
        ]);

        exit;
    }
}

/*
========================================
VERIFICA CANDIDATURA
========================================
*/

$sqlVerifica = "
SELECT id
FROM voluntarios
WHERE usuario_id = ?
LIMIT 1
";

$stmtVerifica = $conn->prepare($sqlVerifica);

if (!$stmtVerifica) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro prepare verificação",
        "erro" => $conn->error
    ]);

    exit;
}

$stmtVerifica->bind_param("i", $usuario_id);

$stmtVerifica->execute();

$resultadoVerifica = $stmtVerifica->get_result();

if ($resultadoVerifica->num_rows > 0) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Você já possui candidatura"
    ]);

    exit;
}

/*
========================================
STATUS
========================================
*/

$status_adm = "aguardando";

/*
========================================
INSERT
========================================
*/

$sqlInsert = "
INSERT INTO voluntarios (

    usuario_id,
    nome_completo,
    email,
    situacao_ti,
    curso_detalhes,
    conteudos_ensino,
    experiencia_previa,
    disponibilidade_horario,
    acesso_tecnologia,
    motivacao_voluntariado,
    observacoes_adicionais,
    status_adm

)
VALUES (

    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?

)
";

$stmtInsert = $conn->prepare($sqlInsert);

if (!$stmtInsert) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro prepare insert",
        "erro" => $conn->error
    ]);

    exit;
}

$stmtInsert->bind_param(

    "isssssssssss",

    $usuario_id,
    $nome,
    $email,
    $situacao_ti,
    $curso_detalhes,
    $conteudos_ensino,
    $experiencia_previa,
    $disponibilidade_horario,
    $acesso_tecnologia,
    $motivacao_voluntariado,
    $observacoes_adicionais,
    $status_adm

);

$executou = $stmtInsert->execute();

/*
========================================
RESULTADO
========================================
*/

if ($executou) {

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Candidatura enviada"
    ]);

} else {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro insert",
        "erro" => $stmtInsert->error
    ]);
}

/*
========================================
FECHA
========================================
*/

$stmtUsuario->close();
$stmtVerifica->close();
$stmtInsert->close();

$conn->close();

?>