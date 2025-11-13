<?php 
// Funções auxiliares para cálculo de alerta
function get_vencimento_status($data_inclusao, $tempo_permanencia) {
    if (empty($data_inclusao) || empty($tempo_permanencia) || !is_numeric($tempo_permanencia)) {
        return ['status' => 'Inválido', 'class' => 'bg-secondary', 'message' => 'Dados Faltando'];
    }

    // Validação de Data (formato 'Y-m-d' do MySQL)
    $data_inclusao_obj = DateTime::createFromFormat('Y-m-d', $data_inclusao);
    if ($data_inclusao_obj === false || $data_inclusao === '0000-00-00') {
        return ['status' => 'Data Inválida', 'class' => 'bg-secondary', 'message' => 'Data de Inclusão Nula ou Inválida'];
    }

    try {
        $data_vencimento = clone $data_inclusao_obj;
        // Calcula a data de vencimento (Data Inclusão + Tempo de Permanência)
        $data_vencimento->modify("+$tempo_permanencia months");
        
        $data_atual = new DateTime();

        // Calcula a data limite de alerta (Vencimento - 1 mês)
        $data_alerta_limite = clone $data_vencimento;
        $data_alerta_limite->modify("-1 month");
        
        // 1. Vencido: Data atual > Data de vencimento
        if ($data_atual > $data_vencimento) {
            $diff = $data_atual->diff($data_vencimento);
            $dias_diferenca = $diff->days;
            return ['status' => 'Vencido', 'class' => 'bg-danger', 'message' => 'Venceu no dia ' . $data_vencimento->format('d/m/Y')];
        }
        
        // 2. Vencimento Próximo: Data atual >= Limite de alerta e <= Data de vencimento
        if ($data_atual >= $data_alerta_limite && $data_atual <= $data_vencimento) {
            $mensagem = '';
            if ($data_atual->format('Y-m-d') === $data_vencimento->format('Y-m-d')) {
                $mensagem = "Vence hoje";
            } else {
                $diff = $data_vencimento->diff($data_atual);
                $dias_diferenca = $diff->days;
                $mensagem = "Vence em $dias_diferenca dias";
            }
            return ['status' => 'Vencimento Próximo', 'class' => 'bg-warning', 'message' => $mensagem];
        }

        // 3. OK: Data atual < Limite de alerta
        return ['status' => 'OK', 'class' => 'bg-success', 'message' => 'Vencimento: ' . $data_vencimento->format('d/m/Y')];

    } catch (Exception $e) {
        return ['status' => 'Erro', 'class' => 'bg-dark', 'message' => 'Erro de cálculo'];
    }
}
?>

<?php if ($total_resultados > 0): ?>
    <div class="text-center mb-3">
        Exibindo <?php echo count($pacientes); ?> de <?php echo $total_resultados; ?> resultados.
    </div>
    <div class="d-flex justify-content-end mb-3">
        <a href="exportar_pdf.php?<?php echo $filtro_query; ?>" class="btn btn-danger" target="_blank">
            Exportar PDF
        </a>
    </div>

    <div class="table-responsive" id="tabela">
        <table class="table table-striped table-hover table-bordered">
            <thead>
                <tr>
                    <th>Nº PROCESSO</th>
                    <th>TERMO ADESÃO</th>
                    <th>NOME DO SEGURADO</th>
                    <th>ALERTA</th>
                    <th>AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pacientes as $paciente): ?>
                    
                    <?php
                    // --- LÓGICA DE ALERTA MODIFICADA ---
                    $alerta_status = '';
                    $alerta_class = '';
                    $alerta_message = '';
                    $linha_estilo = ''; // Para deixar a linha semitransparente

                    // PRIORIDADE 1: Verifica se o paciente foi removido
                    // (Requer 'p.data_remocao' no SELECT de 'exibir_dados.php')
                    if (!empty($paciente['data_remocao'])) {
                        $alerta_status = 'Excluído';
                        $alerta_class = 'bg-danger'; // Vermelho
                        $alerta_message = 'Removido em ' . date('d/m/Y', strtotime($paciente['data_remocao']));
                        $linha_estilo = 'style="opacity: 0.6;"'; // Estilo para a linha <tr>
                    } 
                    // PRIORIDADE 2: Se não foi removido, calcula o status de vencimento
                    else {
                        $vencimento = get_vencimento_status($paciente['data_inclusao'], $paciente['tempo_permanencia']);
                        $alerta_status = $vencimento['status'];
                        $alerta_class = $vencimento['class'];
                        $alerta_message = $vencimento['message'];
                    }
                    // --- FIM DA LÓGICA DE ALERTA ---
                    ?>

                    <tr <?php echo $linha_estilo; ?>>
                        <td><?php echo htmlspecialchars($paciente['numero_processo']); ?></td>
                        <td><?php echo htmlspecialchars($paciente['termo_adesao']); ?></td>
                        <td><?php echo htmlspecialchars($paciente['nome_segurado']); ?></td>
                        <td class="text-center">
                            <span class="badge <?php echo $alerta_class; ?> text-light p-2">
                                <?php echo $alerta_status; ?>
                            </span>
                            <small class="d-block text-muted mt-1"><?php echo $alerta_message; ?></small>
                        </td>
                        <td>
                            <a href="../editar_paciente/editar_paciente.php?numero_processo=<?php echo urlencode($paciente['numero_processo']); ?>&termo_adesao=<?php echo urlencode($paciente['termo_adesao']); ?>" class="btn btn-sm btn-warning">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_paginas > 1): ?>
        <nav aria-label="Navegação da página">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if ($pagina_atual <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?pagina=1&<?php echo $filtro_query; ?>">Primeiro</a>
                </li>
                <li class="page-item <?php if ($pagina_atual <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?pagina=<?php echo $pagina_atual - 1; ?>&<?php echo $filtro_query; ?>">Anterior</a>
                </li>
                <?php for ($i = max(1, $pagina_atual - 2); $i <= min($pagina_atual + 2, $total_paginas); $i++): ?>
                    <li class="page-item <?php if ($pagina_atual == $i) echo 'active'; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>&<?php echo $filtro_query; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php if ($pagina_atual >= $total_paginas) echo 'disabled'; ?>">
                    <a class="page-link" href="?pagina=<?php echo $pagina_atual + 1; ?>&<?php echo $filtro_query; ?>">Próximo</a>
                </li>
                <li class="page-item <?php if ($pagina_atual >= $total_paginas) echo 'disabled'; ?>">
                    <a class="page-link" href="?pagina=<?php echo $total_paginas; ?>&<?php echo $filtro_query; ?>">Último</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
<?php else: ?>
    <div class="alert alert-info text-center" role="alert">
        Nenhum paciente encontrado com os filtros aplicados.
    </div>
<?php endif; ?>