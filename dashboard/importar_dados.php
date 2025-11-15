<?php
// dashboard/importar_dados.php
require_once '../header.php';
require_once '../config.php';

// --- CONFIGURAÇÕES BÁSICAS ---
mysqli_report(MYSQLI_REPORT_OFF);
ini_set('auto_detect_line_endings', true);
set_time_limit(300);

// --- VARIÁVEIS DE CONTROLE ---
$mensagem_sucesso = '';
$erros_detalhados = [];

// --- FUNÇÕES AUXILIARES (sem alterações) ---
function clean_text($value, $default = null) {
    $trimmed = trim($value);
    return !empty($trimmed) ? $trimmed : $default;
}

function parse_date_flexible($date_str, $is_required = false) {
    $generic_date = '1900-01-01';
    $trimmed = trim($date_str);
    if (empty($trimmed)) return $is_required ? $generic_date : null;
    try {
        if (preg_match('/(\d{2}\/\d{2}\/\d{2,4})/', $trimmed, $matches)) $trimmed = $matches[1];
        $date = DateTime::createFromFormat('d/m/Y', $trimmed);
        if ($date && $date->format('d/m/Y') === $trimmed) return $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/y', $trimmed);
        if ($date && $date->format('d/m/y') === $trimmed) return $date->format('Y-m-d');
    } catch (Exception $e) {}
    return $is_required ? $generic_date : null;
}

function to_sim_nao($value) {
    $trimmed = trim($value);
    return ($trimmed !== '' && strcasecmp($trimmed, 'X') !== 0) ? 'Sim' : 'Não';
}


// --- PROCESSAMENTO DO ARQUIVO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['file']['tmp_name'];
        $linhas_para_inserir = [];
        $linha_atual = 1;

        if (($handle = fopen($file_tmp_path, "r")) !== FALSE) {
            fgetcsv($handle, 0, "\t"); // Pula cabeçalho

            while (($data = fgetcsv($handle, 0, "\t")) !== FALSE) {
                $linha_atual++;
                if (count($data) != 39) {
                    $erros_detalhados[] = ['linha' => $linha_atual, 'erro' => "Número de colunas incorreto. Esperado: 39, Encontrado: " . count($data)];
                    continue;
                }
                
                // Prepara um array associativo limpo para cada linha
                $paciente_raw = clean_text($data[2], 'Paciente Pendente');
                $nome_segurado = preg_replace('/\s*(Regular|Irregular).*/i', '', $paciente_raw);
                $status_paciente = preg_match('/(Irregular)/i', $paciente_raw) ? 'Irregular' : 'Regular';
                $kit_material_raw = strtolower(clean_text($data[35] ?? ''));

                $linhas_para_inserir[] = [
                    'numero_processo'        => clean_text($data[0], 'PROC_PENDENTE_' . $linha_atual),
                    'termo_adesao'           => clean_text($data[1], 'TERMO_PENDENTE_' . $linha_atual),
                    'nome_segurado'          => $nome_segurado,
                    'profissao_paciente'     => clean_text($data[3]),
                    'renda_paciente'         => !empty($data[4]) ? (float)preg_replace('/[^\d,.]/', '', str_replace(',', '.', $data[4])) : null,
                    'status_paciente'        => $status_paciente,
                    'genero'                 => clean_text($data[6], 'I'),
                    'data_nascimento'        => parse_date_flexible($data[7], true),
                    'vinculo'                => clean_text($data[8], 'Pendente'),
                    'data_entrada_pae'       => parse_date_flexible($data[9], true),
                    'data_avaliacao'         => parse_date_flexible($data[10]),
                    'data_inclusao'          => parse_date_flexible($data[11], true),
                    'tempo_permanencia'      => !empty($data[12]) ? (int)filter_var($data[12], FILTER_SANITIZE_NUMBER_INT) : 0,
                    'data_inicio_terapias'   => parse_date_flexible($data[13]),
                    'clinicas_credenciadas'  => clean_text($data[14]),
                    'data_reavaliacao'       => parse_date_flexible($data[15]),
                    'patologia_base'         => clean_text($data[16]),
                    'conduta'                => clean_text($data[17]),
                    'endereco'               => clean_text($data[18]),
                    'bairro'                 => clean_text($data[19]),
                    'municipio'              => clean_text($data[20]),
                    'fone'                   => clean_text($data[21]),
                    'ambulancia'             => clean_text($data[22]),
                    'atendimento_nutricional'=> to_sim_nao($data[23]),
                    'fisioterapia_motora'    => to_sim_nao($data[24]),
                    'fisioterapia_respiratoria' => to_sim_nao($data[25]),
                    'fonoterapia'       => to_sim_nao($data[26]),
                    'terapia_ocupacional'    => to_sim_nao($data[27]),
                    'psicologia'             => to_sim_nao($data[28]),
                    'servico_social'         => to_sim_nao($data[29]),
                    'supervisao_clinica'     => to_sim_nao($data[30]),
                    'kit_sne'                => (stripos($kit_material_raw, 'sne') !== false) ? 'Sim' : 'Não',
                    'kit_lpp'                => (stripos($kit_material_raw, 'lpp') !== false || stripos($kit_material_raw, 'upp') !== false) ? 'Sim' : 'Não',
                    'kit_gtt'                => (stripos($kit_material_raw, 'gtt') !== false) ? 'Sim' : 'Não',
                    'kit_tqt'                => (stripos($kit_material_raw, 'tqt') !== false) ? 'Sim' : 'Não',
                    'kit_prevencao'          => (stripos($kit_material_raw, 'Kit Prevenção de LPP') !== false) ? 'Sim' : 'Não',
                    'alimentacao_enteral'    => clean_text($data[36]),
                    'oxigenio'               => to_sim_nao($data[37]),
                    'cuidador_responsavel'   => clean_text($data[38], 'Cuidador Pendente'),
                ];
            }
            fclose($handle);
        }

        // --- NOVA LÓGICA DE INSERÇÃO: MANUAL E DIRETA ---
        $sucesso_count = 0; $duplicado_count = 0; $falha_count = 0;

        if (!empty($linhas_para_inserir)) {
            $conn->begin_transaction();
            try {
                // Pega os nomes das colunas do primeiro item (serão os mesmos para todos)
                $colunas = implode('`, `', array_keys($linhas_para_inserir[0]));

                foreach ($linhas_para_inserir as $linha) {
                    $valores = [];
                    foreach ($linha as $valor) {
                        if ($valor === null) {
                            $valores[] = "NULL";
                        } elseif (is_numeric($valor) && !is_string($valor)) {
                            $valores[] = $valor; // Não coloca aspas em números
                        } else {
                            // Escapa o valor e o envolve em aspas simples
                            $valores[] = "'" . $conn->real_escape_string($valor) . "'";
                        }
                    }
                    $valores_str = implode(', ', $valores);
                    
                    $sql = "INSERT INTO `pacientes` (`$colunas`) VALUES ($valores_str)";
                    
                    if (!$conn->query($sql)) {
                        if ($conn->errno == 1062) {
                            $duplicado_count++;
                        } else {
                            $falha_count++;
                            $erros_detalhados[] = ['linha' => 'N/A', 'erro' => "Falha SQL no processo {$linha['numero_processo']}: " . $conn->error];
                        }
                    } else {
                        $sucesso_count++;
                    }
                }
                $conn->commit();
            } catch (Exception $e) {
                $conn->rollback();
                $erros_detalhados[] = ['linha' => 'CRÍTICO', 'erro' => "A transação falhou: " . $e->getMessage()];
            }
        }

        $mensagem_sucesso = "Processamento finalizado. Total de linhas de dados válidas: " . count($linhas_para_inserir) . ".<br>";
        $mensagem_sucesso .= "<strong>Importadas com sucesso:</strong> $sucesso_count<br>";
        if ($duplicado_count > 0) $mensagem_sucesso .= "<strong>Ignoradas por duplicidade:</strong> $duplicado_count<br>";
        if ($falha_count > 0) $mensagem_sucesso .= "<strong>Falhas de inserção no banco:</strong> $falha_count<br>";
        $total_erros = count($erros_detalhados);
        if ($total_erros > 0) $mensagem_sucesso .= "<strong>Linhas com erros (não importadas):</strong> $total_erros<br>";
        
    } else {
        $erros_detalhados[] = ['linha' => 'N/A', 'erro' => "Erro no upload do arquivo: " . ($_FILES['file']['error'] ?? 'desconhecido')];
    }
}
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h1 class="text-center mb-4">Importar Pacientes</h1>
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if ($mensagem_sucesso): ?>
                        <div class="alert alert-info"><?php echo $mensagem_sucesso; ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($erros_detalhados)): ?>
                        <div class="alert alert-danger">
                            <h5 class="alert-heading">Relatório de Erros</h5>
                            <ul class="mb-0" style="max-height: 400px; overflow-y: auto;">
                                <?php foreach ($erros_detalhados as $erro): ?>
                                    <li><strong>Linha Aprox. <?php echo htmlspecialchars($erro['linha']); ?>:</strong> <?php echo htmlspecialchars($erro['erro']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="file" class="form-label">Arquivo CSV (delimitado por tabulação)</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".csv" required>
                        </div>
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">Importar</button>
                            <a href="dashboard.php" class="btn btn-secondary">Voltar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>