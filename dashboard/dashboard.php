<?php
// dashboard/dashboard.php
require_once '../header.php';
require_once '../config.php';

if (!function_exists('select_option')) {
    function select_option($val, $option) {
        return ($val == $option) ? 'selected' : '';
    }
}
if (!function_exists('is_toggle_checked')) {
    function is_toggle_checked($val) {
        return $val == 'Sim' ? 'checked' : '';
    }
}

if (!function_exists('calcular_idade')) {
    function calcular_idade($data_nascimento_str) {
        if (empty($data_nascimento_str)) {
            return null;
        }

        $data_nascimento_obj = DateTime::createFromFormat('d/m/Y', $data_nascimento_str);

        if ($data_nascimento_obj === false) { // Se o formato for inválido (ex: data incompleta)
            return null;
        }

        $data_atual = new DateTime('now');
        $intervalo = $data_atual->diff($data_nascimento_obj);

        return $intervalo->y; // Retorna o componente 'y' (anos)
    }
}

$mensagem_sucesso = '';
$mensagem_erro = '';

// --- 1. Variáveis sem Valor padrão ---
$numero_processo_val = '';
$termo_adesao_val = '';
$nome_segurado_val = '';
$profissao_paciente_val = '';
$renda_paciente_val = '';
$status_paciente_val = '';
$genero_val = '';
$data_nascimento_val = '';
$vinculo_val = '';
$data_entrada_pae_val = '';
$data_avaliacao_val = ''; 
$data_inclusao_val = date('d/m/Y'); 
$tempo_permanencia_val = ''; 
$data_inicio_terapias_val = '';
$clinicas_credenciadas_val = '';
$data_reavaliacao_val = ''; 
$conduta_val = '';
$patologias_selecionadas_val = []; 
$endereco_val = '';
$bairro_val = '';
$municipio_val = '';
$fone_val = '';
$ambulancia_val = null;
$atendimento_nutricional_val = null;
$fisioterapia_motora_val = null;
$fisioterapia_respiratoria_val = null;
$fonoterapia_val = null;
$terapia_ocupacional_val = null;
$psicologia_val = null;
$servico_social_val = null; 
$supervisao_clinica_val = null;
$kit_sne_val = null;
$kit_lpp_val = null; 
$kit_gtt_val = null;
$kit_tqt_val = null;
$kit_prevencao_val = null;
$alimentacao_enteral_val = null;
$oxigenio_val = null;
$cuidador_responsavel_val = '';
$patologia_base_val = '';

/*
// 1. Variáveis de Valor (Dados Hipotéticos - Defaults Iniciais)
$numero_processo_val = '00000';
$termo_adesao_val = 'TA-0000';
$nome_segurado_val = 'Paciente Hipotético';
$profissao_paciente_val = 'Aposentado';
$renda_paciente_val = '1500,00';
$status_paciente_val = 'Regular';
$genero_val = 'F';
$data_nascimento_val = '01/01/1950';
$vinculo_val = 'TIT';
$data_entrada_pae_val = date('d/m/Y', strtotime('-1 year'));
$data_avaliacao_val = ''; 
$data_inclusao_val = date('d/m/Y', strtotime('-10 months')); 
$tempo_permanencia_val = '12'; 
$data_inicio_terapias_val = date('d/m/Y', strtotime('-10 months + 1 day'));
$clinicas_credenciadas_val = 'Medclin';
$data_reavaliacao_val = ''; 
$conduta_val = 'Manutenção';
$patologias_selecionadas_val = []; 
$endereco_val = 'Rua Hipotética, 1';
$bairro_val = 'Centro';
$municipio_val = 'Belém';
$fone_val = '(91) 98765-4321';
$ambulancia_val = null;
$atendimento_nutricional_val = null;
$fisioterapia_motora_val = null;
$fisioterapia_respiratoria_val = null;
$fonoterapia_val = null;
$terapia_ocupacional_val = null;
$psicologia_val = null;
$servico_social_val = null; 
$supervisao_clinica_val = null;
$kit_sne_val = null;
$kit_lpp_val = null; 
$kit_gtt_val = null;
$kit_tqt_val = null;
$kit_prevencao_val = null;
$alimentacao_enteral_val = null;
$oxigenio_val = null;
$cuidador_responsavel_val = 'Cuidador Exemplo';
$patologia_base_val = '';
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. Coleta de Dados do Formulário
    $numero_processo_val = $_POST['numero_processo'] ?? '';
    $termo_adesao_val = $_POST['termo_adesao'] ?? '';
    $nome_segurado_val = $_POST['nome_segurado'] ?? '';
    $profissao_paciente_val = $_POST['profissao_paciente'] ?? '';
    $renda_paciente_val = str_replace(',', '.', $_POST['renda_paciente'] ?? null);
    $status_paciente_val = $_POST['status_paciente'] ?? 'Regular';
    $genero_val = $_POST['genero'] ?? '';
    $data_nascimento_val = $_POST['data_nascimento'] ?? '';
    
    // CAMPOS DE SELECT COM TRATAMENTO DE NULL
    $vinculo_val = $_POST['vinculo'] ?? null;
    $vinculo_val = ($vinculo_val === '-') ? null : $vinculo_val;

    $clinicas_credenciadas_val = $_POST['clinicas_credenciadas'] ?? null;
    $clinicas_credenciadas_val = ($clinicas_credenciadas_val === '-') ? null : $clinicas_credenciadas_val;

    $ambulancia_val = $_POST['ambulancia'] ?? null;
    $ambulancia_val = ($ambulancia_val === '-') ? null : $ambulancia_val;

    $conduta_val = $_POST['conduta'] ?? null;
    $conduta_val = ($conduta_val === '-') ? null : $conduta_val; 
    
    $kit_lpp_val = $_POST['kit_lpp (Lesão Por Pressão)'] ?? null;
    $kit_lpp_val = ($kit_lpp_val === '-') ? null : $kit_lpp_val;

    $alimentacao_enteral_val = $_POST['alimentacao_enteral'] ?? null;
    $alimentacao_enteral_val = ($alimentacao_enteral_val === '-') ? null : $alimentacao_enteral_val;
    // FIM DOS CAMPOS DE SELECT COM TRATAMENTO DE NULL

    $data_entrada_pae_val = $_POST['data_entrada_pae'] ?? '';
    $data_avaliacao_val = $_POST['data_avaliacao'] ?? '';
    $data_inclusao_val = $_POST['data_inclusao'] ?? '';
    
    // Garante que tempo_permanencia é um inteiro (ou null se vazio)
    $tempo_permanencia_val = is_numeric($_POST['tempo_permanencia'] ?? null) ? (int)$_POST['tempo_permanencia'] : null;

    $data_inicio_terapias_val = $_POST['data_inicio_terapias'] ?? '';
    $data_reavaliacao_val = $_POST['data_reavaliacao'] ?? '';
    
    $patologias_selecionadas_val = $_POST['patologias'] ?? []; 
    $endereco_val = $_POST['endereco'] ?? '';
    $bairro_val = $_POST['bairro'] ?? '';
    $municipio_val = $_POST['municipio'] ?? '';
    $fone_val = $_POST['fone'] ?? '';
    
    // Toggle fields
    $atendimento_nutricional_val = isset($_POST['atendimento_nutricional']) ? 'Sim' : null;
    $fisioterapia_motora_val = isset($_POST['fisioterapia_motora']) ? 'Sim' : null;
    $fisioterapia_respiratoria_val = isset($_POST['fisioterapia_respiratoria']) ? 'Sim' : null;
    $fonoterapia_val = isset($_POST['fonoterapia']) ? 'Sim' : null;
    $terapia_ocupacional_val = isset($_POST['terapia_ocupacional']) ? 'Sim' : null;
    $psicologia_val = isset($_POST['psicologia']) ? 'Sim' : null;
    $servico_social_val = isset($_POST['servico_social']) ? 'Sim' : null;
    $supervisao_clinica_val = isset($_POST['supervisao_clinica']) ? 'Sim' : null;
    $kit_sne_val = isset($_POST['kit_sne']) ? 'Sim' : null;
    $kit_gtt_val = isset($_POST['kit_gtt']) ? 'Sim' : null;
    $kit_tqt_val = isset($_POST['kit_tqt']) ? 'Sim' : null;
    $kit_prevencao_val = isset($_POST['kit_prevencao']) ? 'Sim' : null;
    $oxigenio_val = isset($_POST['oxigenio']) ? 'Sim' : null;
    
    $cuidador_responsavel_val = $_POST['cuidador_responsavel'] ?? '';
    $patologia_base_val = $_POST['patologia_base'] ?? '';

    try {
        // 3. Validação de campos obrigatórios
        if ($tempo_permanencia_val === null) { 
            throw new Exception("O campo 'Tempo de Permanência (Meses)' é obrigatório.");
        }

        // Validação e Formatação das Datas (Obrigatórias)
        $data_nascimento_obj = DateTime::createFromFormat('d/m/Y', $data_nascimento_val);
        $data_entrada_pae_obj = DateTime::createFromFormat('d/m/Y', $data_entrada_pae_val);
        $data_inclusao_obj = DateTime::createFromFormat('d/m/Y', $data_inclusao_val);
        $data_avaliacao_obj = DateTime::createFromFormat('d/m/Y', $data_avaliacao_val);

        if (!$data_nascimento_obj || !$data_entrada_pae_obj || !$data_inclusao_obj || !$data_avaliacao_obj) {
            throw new Exception("Formato de data obrigatório inválido. Verifique: Nascimento, Entrada PAE, Inclusão e Avaliação. Use DD/MM/AAAA.");
        }

        $data_nascimento_formatada = $data_nascimento_obj->format('Y-m-d');
        $data_entrada_pae_formatada = $data_entrada_pae_obj->format('Y-m-d');
        $data_inclusao_formatada = $data_inclusao_obj->format('Y-m-d');
        $data_avaliacao_formatada = $data_avaliacao_obj->format('Y-m-d');
        
        // 4. Formatação de datas opcionais      
        $data_inicio_terapias_formatada = null;
        if (!empty($data_inicio_terapias_val)) {
            $data_inicio_terapias_obj = DateTime::createFromFormat('d/m/Y', $data_inicio_terapias_val);
            if ($data_inicio_terapias_obj !== false) {
                $data_inicio_terapias_formatada = $data_inicio_terapias_obj->format('Y-m-d');
            }
        }

        $data_reavaliacao_formatada = null;
        if (!empty($data_reavaliacao_val)) {
            $data_reavaliacao_obj = DateTime::createFromFormat('d/m/Y', $data_reavaliacao_val);
            if ($data_reavaliacao_obj !== false) {
                $data_reavaliacao_formatada = $data_reavaliacao_obj->format('Y-m-d');
            }
        }
        
        // 5. Início da transação
        $conn->begin_transaction();
        
        // 6. Inserção na tabela `pacientes` (com chave primária composta)
        $sql = "INSERT INTO pacientes (
            numero_processo, termo_adesao, nome_segurado, profissao_paciente, renda_paciente, 
            status_paciente, genero, data_nascimento, vinculo, data_entrada_pae, 
            data_avaliacao, data_inclusao, tempo_permanencia, data_inicio_terapias, 
            clinicas_credenciadas, data_reavaliacao, conduta, patologia_base, endereco, bairro, 
            municipio, fone, ambulancia, atendimento_nutricional, fisioterapia_motora, 
            fisioterapia_respiratoria, fonoterapia, terapia_ocupacional, psicologia, 
            servico_social, supervisao_clinica, kit_sne, kit_lpp, kit_gtt, kit_tqt, 
            kit_prevencao, alimentacao_enteral, oxigenio, cuidador_responsavel
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);

        // - 4s (numero_processo, termo_adesao, nome_segurado, profissao_paciente)
        // - d (renda_paciente)
        // - 6s (status_paciente, genero, data_nascimento, vinculo, data_entrada_pae, data_avaliacao)
        // - s (data_inclusao)
        // - i (tempo_permanencia)
        // - 26s (restantes: data_inicio_terapias, clinicas_credenciadas, ..., cuidador_responsavel)
        $stmt->bind_param("ssssdsssssssissssssssssssssssssssssssss", // Total 39 colunas
            $numero_processo_val, $termo_adesao_val, $nome_segurado_val, $profissao_paciente_val, $renda_paciente_val,
            $status_paciente_val, $genero_val, $data_nascimento_formatada, $vinculo_val, $data_entrada_pae_formatada,
            $data_avaliacao_formatada, $data_inclusao_formatada, $tempo_permanencia_val, $data_inicio_terapias_formatada,
            $clinicas_credenciadas_val, $data_reavaliacao_formatada, $conduta_val, $patologia_base_val, $endereco_val, $bairro_val,
            $municipio_val, $fone_val, $ambulancia_val, $atendimento_nutricional_val, $fisioterapia_motora_val,
            $fisioterapia_respiratoria_val, $fonoterapia_val, $terapia_ocupacional_val, $psicologia_val,
            $servico_social_val, $supervisao_clinica_val, $kit_sne_val, $kit_lpp_val, $kit_gtt_val, $kit_tqt_val,
            $kit_prevencao_val, $alimentacao_enteral_val, $oxigenio_val, $cuidador_responsavel_val
        );

        $stmt->execute();
        
        // 7. Inserção na tabela `paciente_patologia` (Múltiplas patologias)
        if (!empty($patologias_selecionadas_val) && is_array($patologias_selecionadas_val)) {
            $sql_rel = "INSERT INTO paciente_patologia (numero_processo, termo_adesao, id_patologia) VALUES (?, ?, ?)";
            $stmt_rel = $conn->prepare($sql_rel);
            
            foreach ($patologias_selecionadas_val as $id_patologia) {
                if (is_numeric($id_patologia)) {
                    $id_patologia = (int)$id_patologia;
                    $stmt_rel->bind_param("ssi", $numero_processo_val, $termo_adesao_val, $id_patologia);
                    $stmt_rel->execute();
                }
            }
        }
        
        // 8. Confirmação da Transação
        $conn->commit();
        $mensagem_sucesso = "Paciente cadastrado com sucesso! Nº Processo/Termo: {$numero_processo_val}/{$termo_adesao_val}";

        // 9. Resetar TODAS as variáveis (evita carry-over no formulário)
        $numero_processo_val = '';
        $termo_adesao_val = '';
        $nome_segurado_val = '';
        $profissao_paciente_val = '';
        $renda_paciente_val = null;
        $status_paciente_val = 'Regular';
        $genero_val = '';
        $data_nascimento_val = '';
        $vinculo_val = null;
        $data_entrada_pae_val = '';
        $data_avaliacao_val = '';
        $data_inclusao_val = '';
        $tempo_permanencia_val = null;
        $data_inicio_terapias_val = '';
        $clinicas_credenciadas_val = null;
        $data_reavaliacao_val = '';
        $conduta_val = null;
        $patologias_selecionadas_val = [];
        $endereco_val = '';
        $bairro_val = '';
        $municipio_val = '';
        $fone_val = '';
        $ambulancia_val = null;
        $atendimento_nutricional_val = null;
        $fisioterapia_motora_val = null;
        $fisioterapia_respiratoria_val = null;
        $fonoterapia_val = null;
        $terapia_ocupacional_val = null;
        $psicologia_val = null;
        $servico_social_val = null;
        $supervisao_clinica_val = null;
        $kit_sne_val = null;
        $kit_lpp_val = null;
        $kit_gtt_val = null;
        $kit_tqt_val = null;
        $kit_prevencao_val = null;
        $alimentacao_enteral_val = null;
        $oxigenio_val = null;
        $cuidador_responsavel_val = '';
        $patologia_base_val = '';

    } catch (Exception $e) {
        $conn->rollback();
        // Mensagem de erro para duplicidade de chave composta (código 1062)
        if ($conn->errno == 1062) {
            $mensagem_erro = "Erro: A combinação de **Nº do Processo ({$numero_processo_val})** e **Termo de Adesão ({$termo_adesao_val})** já existe. Não é possível cadastrar um paciente duplicado.";
        } else {
            // Mensagem mais detalhada para debug e para os novos erros de validação
            $mensagem_erro = "Erro no cadastro: " . $e->getMessage();
            // $mensagem_erro = "Erro no cadastro. Verifique as datas e o formato. Detalhe: " . $e->getMessage() . " (MySQL Error: " . $conn->error . ")"; // Versão antiga
        }
    }
}

// --- 10. Carregar Patologias para o Formulário ---
$patologias_list = [];
try {
    $result = $conn->query("SELECT id_patologia, nome_patologia FROM patologias ORDER BY nome_patologia ASC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $patologias_list[] = $row;
        }
    }
} catch (Exception $e) {
    $mensagem_erro = ($mensagem_erro ? $mensagem_erro . "<br>" : "") . "<b>Atenção:</b> Não foi possível carregar a lista de patologias. (" . $e->getMessage() . ")";
}

include '_form_cadastro.php';
include '_scripts_dashboard.php';
require_once '../footer.php';
?>