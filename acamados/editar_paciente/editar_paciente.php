<?php
// editar_paciente.php
require_once '../header.php';
require_once '../config.php';

// Funções auxiliares
if (!function_exists('select_option')) {
    function select_option($val, $option) {
        return ($val === $option) ? 'selected' : '';
    }
}
if (!function_exists('is_toggle_checked')) {
    function is_toggle_checked($val) {
        return ($val === 'Sim') ? 'checked' : '';
    }
}

if (!function_exists('calcular_idade')) {
    function calcular_idade($data_nascimento_str) {
        if (empty($data_nascimento_str)) {
            return null;
        }

        $data_nascimento_obj = DateTime::createFromFormat('d/m/Y', $data_nascimento_str);

        // Se o formato for inválido (ex: data incompleta)
        if ($data_nascimento_obj === false) {
            return null;
        }

        $data_atual = new DateTime('now');
        $intervalo = $data_atual->diff($data_nascimento_obj);

        // Retorna o componente 'y' (anos)
        return $intervalo->y;
    }
}

$mensagem_sucesso = '';
$mensagem_erro = '';

// Inicializar variáveis...
$numero_processo_val = '';
$termo_adesao_val = '';
$nome_segurado_val = '';
$profissao_paciente_val = '';
$renda_paciente_val = null;
$status_paciente_val = 'Regular';
$genero_val = '';
$data_nascimento_val = '';
$vinculo_val = '';
$data_entrada_pae_val = '';
$data_avaliacao_val = '';
$data_inclusao_val = '';
$tempo_permanencia_val = null;
$data_inicio_terapias_val = '';
$clinicas_credenciadas_val = '';
$data_reavaliacao_val = '';
$conduta_val = '';
$patologias_selecionadas_val = [];
$endereco_val = '';
$bairro_val = '';
$municipio_val = '';
$fone_val = '';
$ambulancia_val = '';
$atendimento_nutricional_val = 'Não';
$fisioterapia_motora_val = 'Não';
$fisioterapia_respiratoria_val = 'Não';
$fonoterapia_val = 'Não';
$terapia_ocupacional_val = 'Não';
$psicologia_val = 'Não';
$servico_social_val = 'Não';
$supervisao_clinica_val = 'Não';
$kit_sne_val = 'Não';
$kit_lpp_val = '';
$kit_gtt_val = 'Não';
$kit_tqt_val = 'Não';
$kit_prevencao_val = 'Não';
$alimentacao_enteral_val = '';
$oxigenio_val = 'Não';
$cuidador_responsavel_val = '';
$patologia_base_val = '';
$processo_original = '';
$termo_original = '';
$data_remocao_val = '';
$justificativa_remocao_val = '';

// Variável para controlar o estado do formulário (travado ou não)
$form_disabled_state = '';

// Carregar dados do paciente (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['numero_processo']) && isset($_GET['termo_adesao'])) {
    $numero_processo_param = $_GET['numero_processo'];
    $termo_adesao_param = $_GET['termo_adesao'];

    try {
        $sql_select = "SELECT * FROM pacientes WHERE numero_processo = ? AND termo_adesao = ?";
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->bind_param("ss", $numero_processo_param, $termo_adesao_param);
        $stmt_select->execute();
        $result = $stmt_select->get_result();

        if ($result->num_rows > 0) {
            $dados = $result->fetch_assoc();

            $numero_processo_val = htmlspecialchars($dados['numero_processo'] ?? '');
            $termo_adesao_val = htmlspecialchars($dados['termo_adesao'] ?? '');
            $nome_segurado_val = htmlspecialchars($dados['nome_segurado'] ?? '');
            $profissao_paciente_val = htmlspecialchars($dados['profissao_paciente'] ?? '');
            $renda_paciente_val = $dados['renda_paciente'] !== null ? floatval($dados['renda_paciente']) : null;
            $status_paciente_val = htmlspecialchars($dados['status_paciente'] ?? 'Regular');
            $genero_val = htmlspecialchars($dados['genero'] ?? '');
            $data_nascimento_val = $dados['data_nascimento'] ? date('d/m/Y', strtotime($dados['data_nascimento'])) : '';
            $vinculo_val = htmlspecialchars($dados['vinculo'] ?? '');
            $data_entrada_pae_val = $dados['data_entrada_pae'] ? date('d/m/Y', strtotime($dados['data_entrada_pae'])) : '';
            $data_avaliacao_val = $dados['data_avaliacao'] ? date('d/m/Y', strtotime($dados['data_avaliacao'])) : '';
            $data_inclusao_val = $dados['data_inclusao'] ? date('d/m/Y', strtotime($dados['data_inclusao'])) : '';
            $tempo_permanencia_val = $dados['tempo_permanencia'] !== null ? (int)$dados['tempo_permanencia'] : null;
            $data_inicio_terapias_val = $dados['data_inicio_terapias'] ? date('d/m/Y', strtotime($dados['data_inicio_terapias'])) : '';
            $clinicas_credenciadas_val = htmlspecialchars($dados['clinicas_credenciadas'] ?? '');
            $data_reavaliacao_val = $dados['data_reavaliacao'] ? date('d/m/Y', strtotime($dados['data_reavaliacao'])) : '';
            $conduta_val = htmlspecialchars($dados['conduta'] ?? '');
            $patologia_base_val = htmlspecialchars($dados['patologia_base'] ?? '');
            $endereco_val = htmlspecialchars($dados['endereco'] ?? '');
            $bairro_val = htmlspecialchars($dados['bairro'] ?? '');
            $municipio_val = htmlspecialchars($dados['municipio'] ?? '');
            $fone_val = htmlspecialchars($dados['fone'] ?? '');
            $ambulancia_val = htmlspecialchars($dados['ambulancia'] ?? '');
            $atendimento_nutricional_val = htmlspecialchars($dados['atendimento_nutricional'] ?? 'Não');
            $fisioterapia_motora_val = htmlspecialchars($dados['fisioterapia_motora'] ?? 'Não');
            $fisioterapia_respiratoria_val = htmlspecialchars($dados['fisioterapia_respiratoria'] ?? 'Não');
            $fonoterapia_val = htmlspecialchars($dados['fonoterapia'] ?? 'Não');
            $terapia_ocupacional_val = htmlspecialchars($dados['terapia_ocupacional'] ?? 'Não');
            $psicologia_val = htmlspecialchars($dados['psicologia'] ?? 'Não');
            $servico_social_val = htmlspecialchars($dados['servico_social'] ?? 'Não');
            $supervisao_clinica_val = htmlspecialchars($dados['supervisao_clinica'] ?? 'Não');
            $kit_sne_val = htmlspecialchars($dados['kit_sne'] ?? 'Não');
            $kit_lpp_val = htmlspecialchars($dados['kit_lpp'] ?? '');
            $kit_gtt_val = htmlspecialchars($dados['kit_gtt'] ?? 'Não');
            $kit_tqt_val = htmlspecialchars($dados['kit_tqt'] ?? 'Não');
            $kit_prevencao_val = htmlspecialchars($dados['kit_prevencao'] ?? 'Não');
            $alimentacao_enteral_val = htmlspecialchars($dados['alimentacao_enteral'] ?? '');
            $oxigenio_val = htmlspecialchars($dados['oxigenio'] ?? 'Não');
            $cuidador_responsavel_val = htmlspecialchars($dados['cuidador_responsavel'] ?? '');
            $processo_original = $numero_processo_val;
            $termo_original = $termo_adesao_val;
            $data_remocao_val = $dados['data_remocao'] ? date('d/m/Y', strtotime($dados['data_remocao'])) : '';
            $justificativa_remocao_val = htmlspecialchars($dados['justificativa_remocao'] ?? '');

            // Define o estado inicial do formulário baseado na data de remoção
            if (!empty($data_remocao_val)) {
                $form_disabled_state = 'disabled';
            }

            $sql_patologias = "SELECT id_patologia FROM paciente_patologia WHERE numero_processo = ? AND termo_adesao = ?";
            $stmt_patologias = $conn->prepare($sql_patologias);
            $stmt_patologias->bind_param("ss", $numero_processo_param, $termo_adesao_param);
            $stmt_patologias->execute();
            $result_patologias = $stmt_patologias->get_result();
            while ($row = $result_patologias->fetch_assoc()) {
                $patologias_selecionadas_val[] = (int)$row['id_patologia'];
            }
        } else {
            $mensagem_erro = "Paciente não encontrado.";
        }
    } catch (Exception $e) {
        $mensagem_erro = "Erro ao carregar dados do paciente: " . $e->getMessage();
    }
}

// Processar edição (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_processo_val = $_POST['numero_processo'] ?? '';
    $termo_adesao_val = $_POST['termo_adesao'] ?? '';
    $nome_segurado_val = $_POST['nome_segurado'] ?? '';
    $profissao_paciente_val = $_POST['profissao_paciente'] ?? '';
    $renda_paciente_val = !empty($_POST['renda_paciente']) ? floatval($_POST['renda_paciente']) : null;
    $status_paciente_val = $_POST['status_paciente'] ?? 'Regular';
    $genero_val = $_POST['genero'] ?? '';
    $data_nascimento_val = $_POST['data_nascimento'] ?? '';
    $vinculo_val = $_POST['vinculo'] ?? '';
    $data_entrada_pae_val = $_POST['data_entrada_pae'] ?? '';
    $data_avaliacao_val = $_POST['data_avaliacao'] ?? '';
    $data_inclusao_val = $_POST['data_inclusao'] ?? '';
    $tempo_permanencia_val = !empty($_POST['tempo_permanencia']) ? (int)$_POST['tempo_permanencia'] : null;
    $data_inicio_terapias_val = $_POST['data_inicio_terapias'] ?? '';
    $clinicas_credenciadas_val = $_POST['clinicas_credenciadas'] ?? '';
    $data_reavaliacao_val = $_POST['data_reavaliacao'] ?? '';
    $conduta_val = $_POST['conduta'] ?? '';
    $patologia_base_val = $_POST['patologia_base'] ?? '';
    $endereco_val = $_POST['endereco'] ?? '';
    $bairro_val = $_POST['bairro'] ?? '';
    $municipio_val = $_POST['municipio'] ?? '';
    $fone_val = $_POST['fone'] ?? '';
    $ambulancia_val = $_POST['ambulancia'] ?? '';
    $atendimento_nutricional_val = isset($_POST['atendimento_nutricional']) ? 'Sim' : 'Não';
    $fisioterapia_motora_val = isset($_POST['fisioterapia_motora']) ? 'Sim' : 'Não';
    $fisioterapia_respiratoria_val = isset($_POST['fisioterapia_respiratoria']) ? 'Sim' : 'Não';
    $fonoterapia_val = isset($_POST['fonoterapia']) ? 'Sim' : 'Não';
    $terapia_ocupacional_val = isset($_POST['terapia_ocupacional']) ? 'Sim' : 'Não';
    $psicologia_val = isset($_POST['psicologia']) ? 'Sim' : 'Não';
    $servico_social_val = isset($_POST['servico_social']) ? 'Sim' : 'Não';
    $supervisao_clinica_val = isset($_POST['supervisao_clinica']) ? 'Sim' : 'Não';
    $kit_sne_val = isset($_POST['kit_sne']) ? 'Sim' : 'Não';
    $kit_lpp_val = $_POST['kit_lpp'] ?? '';
    $kit_gtt_val = isset($_POST['kit_gtt']) ? 'Sim' : 'Não';
    $kit_tqt_val = isset($_POST['kit_tqt']) ? 'Sim' : 'Não';
    $kit_prevencao_val = isset($_POST['kit_prevencao']) ? 'Sim' : 'Não';
    $alimentacao_enteral_val = $_POST['alimentacao_enteral'] ?? '';
    $oxigenio_val = isset($_POST['oxigenio']) ? 'Sim' : 'Não';
    $cuidador_responsavel_val = $_POST['cuidador_responsavel'] ?? '';
    $patologias_selecionadas_val = $_POST['patologias'] ?? [];
    $processo_original = $_POST['processo_original'] ?? '';
    $termo_original = $_POST['termo_original'] ?? '';
    $data_remocao_val = $_POST['data_remocao'] ?? '';
    $justificativa_remocao_val = $_POST['justificativa_remocao'] ?? '';

    if (empty(trim($data_remocao_val))) {
        $justificativa_remocao_val = '';
    }

    try {
        // 3. Validação de campos obrigatórios
        if ($tempo_permanencia_val === null) { 
            throw new Exception("O campo 'Tempo de Permanência (Meses)' é obrigatório.");
        }

        // Validação e Formatação das Datas (Obrigatórias)
        $data_nascimento_obj = DateTime::createFromFormat('d/m/Y', $data_nascimento_val);
        $data_entrada_pae_obj = DateTime::createFromFormat('d/m/Y', $data_entrada_pae_val);
        $data_inclusao_obj = DateTime::createFromFormat('d/m/Y', $data_inclusao_val);
        $data_avaliacao_obj = DateTime::createFromFormat('d/m/Y', $data_avaliacao_val); // Adicionado

        if (!$data_nascimento_obj || !$data_entrada_pae_obj || !$data_inclusao_obj || !$data_avaliacao_obj) { // Adicionado
            throw new Exception("Formato de data obrigatório inválido. Verifique: Nascimento, Entrada PAE, Inclusão e Avaliação. Use DD/MM/AAAA.");
        }

        $data_nascimento_formatada = $data_nascimento_obj->format('Y-m-d');
        $data_entrada_pae_formatada = $data_entrada_pae_obj->format('Y-m-d');
        $data_inclusao_formatada = $data_inclusao_obj->format('Y-m-d');
        $data_avaliacao_formatada = $data_avaliacao_obj->format('Y-m-d'); // Adicionado

        // Formatação de datas opcionais
        $data_inicio_terapias_formatada = !empty($data_inicio_terapias_val) ? (DateTime::createFromFormat('d/m/Y', $data_inicio_terapias_val) ? DateTime::createFromFormat('d/m/Y', $data_inicio_terapias_val)->format('Y-m-d') : null) : null;
        $data_reavaliacao_formatada = !empty($data_reavaliacao_val) ? (DateTime::createFromFormat('d/m/Y', $data_reavaliacao_val) ? DateTime::createFromFormat('d/m/Y', $data_reavaliacao_val)->format('Y-m-d') : null) : null;
        
        $data_remocao_para_db = null;
        $justificativa_para_db = null;

        if (!empty(trim($data_remocao_val))) {
            $data_remocao_obj = DateTime::createFromFormat('d/m/Y', $data_remocao_val);
            if ($data_remocao_obj) {
                $data_remocao_obj->setTime(date('H'), date('i'), date('s'));
                $data_remocao_para_db = $data_remocao_obj->format('Y-m-d H:i:s');
                $justificativa_para_db = $justificativa_remocao_val;
            }
        }

        $conn->begin_transaction();

        $stmt_antes = $conn->prepare("SELECT * FROM pacientes WHERE numero_processo = ? AND termo_adesao = ? FOR UPDATE");
        $stmt_antes->bind_param("ss", $processo_original, $termo_original);
        $stmt_antes->execute();
        $result_antes = $stmt_antes->get_result();
        if ($result_antes->num_rows === 0) {
            throw new Exception("Paciente original não encontrado para historização.");
        }
        $paciente_antes = $result_antes->fetch_assoc();
        $stmt_antes->close();

        $colunas = array_keys($paciente_antes);
        $sql_historico = "INSERT INTO pacientes_historico (" . implode(', ', $colunas) . ") VALUES (" . rtrim(str_repeat('?, ', count($colunas)), ', ') . ")";
        $stmt_historico = $conn->prepare($sql_historico);
        $stmt_historico->bind_param(str_repeat('s', count($colunas)), ...array_values($paciente_antes));
        $stmt_historico->execute();
        $stmt_historico->close();

        $data_real_remocao_para_db = $paciente_antes['data_real_remocao'];
        if (empty($paciente_antes['data_remocao']) && !empty($data_remocao_para_db)) {
            $data_real_remocao_para_db = date('Y-m-d H:i:s');
        } elseif (!empty($paciente_antes['data_remocao']) && empty($data_remocao_para_db)) {
            $data_real_remocao_para_db = null;
        }

        if ($numero_processo_val !== $processo_original || $termo_adesao_val !== $termo_original) {
            $sql_check = "SELECT 1 FROM pacientes WHERE numero_processo = ? AND termo_adesao = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("ss", $numero_processo_val, $termo_adesao_val);
            $stmt_check->execute();
            if ($stmt_check->get_result()->num_rows > 0) {
                throw new Exception("A combinação de Nº do Processo ({$numero_processo_val}) e Termo de Adesão ({$termo_adesao_val}) já existe.");
            }
        }

        $sql_update = "UPDATE pacientes SET 
            numero_processo = ?, termo_adesao = ?, nome_segurado = ?, profissao_paciente = ?, renda_paciente = ?, 
            status_paciente = ?, genero = ?, data_nascimento = ?, vinculo = ?, data_entrada_pae = ?, 
            data_avaliacao = ?, data_inclusao = ?, tempo_permanencia = ?, data_inicio_terapias = ?, 
            clinicas_credenciadas = ?, data_reavaliacao = ?, conduta = ?, patologia_base = ?, endereco = ?, bairro = ?, 
            municipio = ?, fone = ?, ambulancia = ?, atendimento_nutricional = ?, fisioterapia_motora = ?, 
            fisioterapia_respiratoria = ?, fonoterapia = ?, terapia_ocupacional = ?, psicologia = ?, 
            servico_social = ?, supervisao_clinica = ?, kit_sne = ?, kit_lpp = ?, kit_gtt = ?, kit_tqt = ?, 
            kit_prevencao = ?, alimentacao_enteral = ?, oxigenio = ?, cuidador_responsavel = ?,
            data_remocao = ?, justificativa_remocao = ?, data_real_remocao = ?
            WHERE numero_processo = ? AND termo_adesao = ?";

        $stmt_update = $conn->prepare($sql_update);
        
        $stmt_update->bind_param(
            "ssssdsssssssisssssssssssssssssssssssssssssss",
            $numero_processo_val, $termo_adesao_val, $nome_segurado_val, $profissao_paciente_val, $renda_paciente_val,
            $status_paciente_val, $genero_val, $data_nascimento_formatada, $vinculo_val, $data_entrada_pae_formatada,
            $data_avaliacao_formatada, $data_inclusao_formatada, $tempo_permanencia_val, $data_inicio_terapias_formatada,
            $clinicas_credenciadas_val, $data_reavaliacao_formatada, $conduta_val, $patologia_base_val, $endereco_val, $bairro_val,
            $municipio_val, $fone_val, $ambulancia_val, $atendimento_nutricional_val, $fisioterapia_motora_val,
            $fisioterapia_respiratoria_val, $fonoterapia_val, $terapia_ocupacional_val, $psicologia_val,
            $servico_social_val, $supervisao_clinica_val, $kit_sne_val, $kit_lpp_val, $kit_gtt_val, $kit_tqt_val,
            $kit_prevencao_val, $alimentacao_enteral_val, $oxigenio_val, $cuidador_responsavel_val,
            $data_remocao_para_db, $justificativa_para_db, $data_real_remocao_para_db,
            $processo_original, $termo_original
        );

        $stmt_update->execute();

        $sql_delete_patologias = "DELETE FROM paciente_patologia WHERE numero_processo = ? AND termo_adesao = ?";
        $stmt_delete_patologias = $conn->prepare($sql_delete_patologias);
        $stmt_delete_patologias->bind_param("ss", $processo_original, $termo_original);
        $stmt_delete_patologias->execute();

        if (!empty($patologias_selecionadas_val) && is_array($patologias_selecionadas_val)) {
            $sql_insert_patologia = "INSERT INTO paciente_patologia (numero_processo, termo_adesao, id_patologia) VALUES (?, ?, ?)";
            $stmt_insert_patologia = $conn->prepare($sql_insert_patologia);
            foreach ($patologias_selecionadas_val as $id_patologia) {
                if (is_numeric($id_patologia)) {
                    $id_patologia = (int)$id_patologia;
                    $stmt_insert_patologia->bind_param("ssi", $numero_processo_val, $termo_adesao_val, $id_patologia);
                    $stmt_insert_patologia->execute();
                }
            }
        }

        $conn->commit();
        $_SESSION['mensagem_sucesso'] = "Paciente **" . htmlspecialchars($nome_segurado_val) . "** atualizado com sucesso!";
        header("Location: ../pacientes/exibir_dados.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $mensagem_erro = "Erro ao atualizar paciente: " . $e->getMessage();
    }
}

// Carregar patologias para o formulário
$patologias_list = [];
try {
    $result = $conn->query("SELECT id_patologia, nome_patologia FROM patologias ORDER BY nome_patologia ASC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $patologias_list[] = $row;
        }
    }
} catch (Exception $e) {
    $mensagem_erro = "Erro ao carregar a lista de patologias: " . $e->getMessage();
    $patologias_list = [];
}

include '_form_editar_paciente.php';
include '_scripts_editar.php';
require_once '../footer.php';
?>