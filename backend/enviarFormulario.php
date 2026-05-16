<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

/*
========================================
VALIDAÇÃO DE SESSÃO
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
CONEXÃO BANCO
========================================
*/

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "spectrum"
);

/*
========================================
ERRO CONEXÃO
========================================
*/

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
BUSCA DADOS DO USUÁRIO LOGADO
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

/*
========================================
DADOS AUTOMÁTICOS
========================================
*/

$nome = $usuario["nome"];
$email = $usuario["email"];

/*
========================================
DADOS DO FORMULÁRIO
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
VALIDAÇÃO
========================================
*/

if (
    empty($situacao_ti) ||
    empty($conteudos_ensino) ||
    empty($experiencia_previa) ||
    empty($disponibilidade_horario) ||
    empty($acesso_tecnologia) ||
    empty($motivacao_voluntariado)
) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Preencha todos os campos obrigatórios"
    ]);

    exit;
}

/*
========================================
STATUS INICIAL
========================================
*/

$status_adm = "aguardando";

/*
========================================
VERIFICA CANDIDATURA EXISTENTE
========================================
*/

$sqlVerifica = "
SELECT id
FROM voluntarios
WHERE usuario_id = ?
LIMIT 1
";

$stmtVerifica = $conn->prepare($sqlVerifica);

$stmtVerifica->bind_param("i", $usuario_id);

$stmtVerifica->execute();

$resultadoVerifica = $stmtVerifica->get_result();

if ($resultadoVerifica->num_rows > 0) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Você já possui uma candidatura em avaliação"
    ]);

    exit;
}

/*
========================================
INSERT
========================================
*/

$sql = "
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

    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?,
    ?

)
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(

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

/*
========================================
EXECUTA
========================================
*/

if ($stmt->execute()) {

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Formulário enviado com sucesso"
    ]);

} else {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao salvar candidatura",
        "erro" => $stmt->error
    ]);
}

/*
========================================
FECHA CONEXÕES
========================================
*/

$stmt->close();
$stmtUsuario->close();
$stmtVerifica->close();

$conn->close();

?>