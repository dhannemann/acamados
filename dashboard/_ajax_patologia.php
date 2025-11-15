<?php
// dashboard/_ajax_patologia.php
require_once '../config.php'; // Inclua a conexão com o banco

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nova_patologia'])) {
    $nova_patologia = trim($_POST['nova_patologia']);

    if (empty($nova_patologia)) {
        echo json_encode(['success' => false, 'message' => 'O nome da patologia não pode ser vazio.']);
        exit;
    }

    try {
        // Verifica se já existe para evitar duplicidade (embora o UNIQUE faça isso)
        $stmt_check = $conn->prepare("SELECT id_patologia FROM patologias WHERE nome_patologia = ?");
        $stmt_check->bind_param("s", $nova_patologia);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode(['success' => false, 'message' => 'Patologia já cadastrada!', 'id' => $row['id_patologia']]);
            exit;
        }

        // Insere a nova patologia
        $stmt = $conn->prepare("INSERT INTO patologias (nome_patologia) VALUES (?)");
        $stmt->bind_param("s", $nova_patologia);
        $stmt->execute();

        $novo_id = $conn->insert_id;
        echo json_encode(['success' => true, 'message' => 'Patologia cadastrada com sucesso!', 'id' => $novo_id, 'nome' => $nova_patologia]);

    } catch (mysqli_sql_exception $e) {
        // Captura o erro 1062 para violação de chave UNIQUE (Patologia já existe)
        if ($e->getCode() == 1062) {
            echo json_encode(['success' => false, 'message' => 'Patologia já existe.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao inserir: ' . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
}

$conn->close();
?>