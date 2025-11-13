<?php
// filtros.php

/**
 * Constrói as cláusulas SQL WHERE com base nos parâmetros GET.
 * @param mysqli $conn Conexão com o banco de dados.
 * @param array $get_params Parâmetros GET recebidos.
 * @return array Array de strings de condições SQL.
 */
function build_sql_conditions($conn, $get_params) {
    $sql_conditions = [];

    // Campos de texto/like (usa alias 'p.')
    $text_fields = [
        'numero_processo', 'termo_adesao', 'nome_segurado', 'profissao_paciente',
        'endereco', 'bairro', 'municipio', 'fone', 'cuidador_responsavel',
        'clinicas_credenciadas', 'ambulancia', 'conduta', 'patologia_base',
        'justificativa_remocao' // NOVO CAMPO
    ];

    foreach ($text_fields as $field) {
        if (!empty($get_params[$field])) {
            $sql_conditions[] = "p.$field LIKE '%" . $conn->real_escape_string($get_params[$field]) . "%'";
        }
    }

    // Campos de seleção exata/toggle/select (usa alias 'p.')
    $exact_fields = [
        'genero', 'status_paciente', 'vinculo', 'tempo_permanencia', 'atendimento_nutricional', 
        'fisioterapia_motora', 'fisioterapia_respiratoria', 'fonoterapia', 
        'terapia_ocupacional', 'psicologia', 'servico_social', 'supervisao_clinica', 
        'kit_sne', 'kit_lpp', 'kit_gtt', 'kit_tqt', 'kit_prevencao', 'alimentacao_enteral', 
        'oxigenio'
    ];
    
    foreach ($exact_fields as $field) {
        if (!empty($get_params[$field]) && $get_params[$field] !== '-') {
            $sql_conditions[] = "p.$field = '" . $conn->real_escape_string($get_params[$field]) . "'";
        }
    }
    
    // Filtro por Renda (intervalo)
    if (!empty($get_params['renda_min']) && is_numeric($get_params['renda_min'])) {
        $sql_conditions[] = "p.renda_paciente >= " . (float)$get_params['renda_min'];
    }
    if (!empty($get_params['renda_max']) && is_numeric($get_params['renda_max'])) {
        $sql_conditions[] = "p.renda_paciente <= " . (float)$get_params['renda_max'];
    }
    
    // Filtro por Patologia (usará JOIN)
    if (!empty($get_params['patologia_id']) && is_numeric($get_params['patologia_id'])) {
        $sql_conditions[] = "pp.id_patologia = " . (int)$get_params['patologia_id'];
    }

    // Filtros de data (usa alias 'p.')
    $date_filters = [
        'data_nascimento_inicio' => 'p.data_nascimento >=',
        'data_nascimento_fim' => 'p.data_nascimento <=',
        'data_entrada_pae_inicio' => 'p.data_entrada_pae >=',
        'data_entrada_pae_fim' => 'p.data_entrada_pae <=',
        'data_avaliacao_inicio' => 'p.data_avaliacao >=',
        'data_avaliacao_fim' => 'p.data_avaliacao <=',
        'data_inclusao_inicio' => 'p.data_inclusao >=',
        'data_inclusao_fim' => 'p.data_inclusao <=',
        'data_inicio_terapias_inicio' => 'p.data_inicio_terapias >=',
        'data_inicio_terapias_fim' => 'p.data_inicio_terapias <=',
        'data_reavaliacao_inicio' => 'p.data_reavaliacao >=',
        'data_reavaliacao_fim' => 'p.data_reavaliacao <=',
        // NOVOS FILTROS DE DATA
        'data_remocao_inicio' => 'p.data_remocao >=',
        'data_remocao_fim' => 'p.data_remocao <=',
        'data_real_remocao_inicio' => 'p.data_real_remocao >=',
        'data_real_remocao_fim' => 'p.data_real_remocao <=',
    ];

    foreach ($date_filters as $filter_name => $sql_operator) {
        if (!empty($get_params[$filter_name])) {
            $date_obj = DateTime::createFromFormat('d/m/Y', $get_params[$filter_name]);
            if ($date_obj) {
                $sql_conditions[] = "$sql_operator '" . $conn->real_escape_string($date_obj->format('Y-m-d')) . "'";
            }
        }
    }
    
    // NOVO: Filtro por Status de Remoção
    if (!empty($get_params['status_remocao'])) {
        if ($get_params['status_remocao'] == 'removido') {
            $sql_conditions[] = "p.data_remocao IS NOT NULL";
        } elseif ($get_params['status_remocao'] == 'nao_removido') {
            $sql_conditions[] = "p.data_remocao IS NULL";
        }
    }

    // Status Ativo/Inativo (baseado no tempo de permanência)
    if (!empty($get_params['status_acamado'])) {
        if ($get_params['status_acamado'] == 'ativo') {
            $sql_conditions[] = "DATE_ADD(p.data_inclusao, INTERVAL p.tempo_permanencia MONTH) >= CURDATE()";
        } elseif ($get_params['status_acamado'] == 'inativo') {
            $sql_conditions[] = "DATE_ADD(p.data_inclusao, INTERVAL p.tempo_permanencia MONTH) < CURDATE()";
        }
    }

    return $sql_conditions;
}