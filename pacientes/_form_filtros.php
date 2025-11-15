<div class="container-fluid mb-5">
    <form method="GET" action="exibir_dados.php" class="row g-4">

        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">🧑 Identificação do Paciente</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Nº Processo</label>
                        <input type="text" class="form-control" name="numero_processo" value="<?php echo htmlspecialchars($_GET['numero_processo'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Termo de Adesão</label>
                        <input type="text" class="form-control" name="termo_adesao" value="<?php echo htmlspecialchars($_GET['termo_adesao'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nome do Segurado</label>
                        <input type="text" class="form-control" name="nome_segurado" value="<?php echo htmlspecialchars($_GET['nome_segurado'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cuidador Responsável</label>
                        <input type="text" class="form-control" name="cuidador_responsavel" value="<?php echo htmlspecialchars($_GET['cuidador_responsavel'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sexo</label>
                        <select class="form-select" name="genero">
                            <option value="">Todos</option>
                            <option value="M" <?php echo (($_GET['genero'] ?? '') === 'M') ? 'selected' : ''; ?>>Masculino</option>
                            <option value="F" <?php echo (($_GET['genero'] ?? '') === 'F') ? 'selected' : ''; ?>>Feminino</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Vínculo</label>
                        <select class="form-select" name="vinculo">
                            <option value="">Todos</option>
                            <option value="Titular" <?php echo (($_GET['vinculo'] ?? '') === 'Titular') ? 'selected' : ''; ?>>Titular</option>
                            <option value="Dependente" <?php echo (($_GET['vinculo'] ?? '') === 'Dependente') ? 'selected' : ''; ?>>Dependente</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status_paciente">
                            <option value="">Todos</option>
                            <option value="Regular" <?php echo (($_GET['status_paciente'] ?? '') === 'Regular') ? 'selected' : ''; ?>>Regular</option>
                            <option value="Irregular" <?php echo (($_GET['status_paciente'] ?? '') === 'Irregular') ? 'selected' : ''; ?>>Irregular</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">📍 Dados Pessoais e Endereço</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Profissão</label>
                        <input type="text" class="form-control" name="profissao_paciente" value="<?php echo htmlspecialchars($_GET['profissao_paciente'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Renda (Mín)</label>
                        <input type="number" step="0.01" class="form-control" name="renda_min" value="<?php echo htmlspecialchars($_GET['renda_min'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Renda (Máx)</label>
                        <input type="number" step="0.01" class="form-control" name="renda_max" value="<?php echo htmlspecialchars($_GET['renda_max'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Fone</label>
                        <input type="text" class="form-control" name="fone" value="<?php echo htmlspecialchars($_GET['fone'] ?? ''); ?>">
                    </div>
                     <div class="col-md-3">
                        <label class="form-label">Município</label>
                        <input type="text" class="form-control" name="municipio" value="<?php echo htmlspecialchars($_GET['municipio'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Bairro</label>
                        <input type="text" class="form-control" name="bairro" value="<?php echo htmlspecialchars($_GET['bairro'] ?? ''); ?>">
                    </div>
                     <div class="col-md-5">
                        <label class="form-label">Endereço</label>
                        <input type="text" class="form-control" name="endereco" value="<?php echo htmlspecialchars($_GET['endereco'] ?? ''); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
             <div class="card-header bg-light">
                <h5 class="mb-0">🩺 Patologia e Conduta</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Patologia (Seleção)</label>
                        <select class="form-select" name="patologia_id">
                            <option value="">Todas</option>
                            <?php 
                            $patologia_selecionada = $_GET['patologia_id'] ?? '';
                            foreach ($patologias_list as $patologia): 
                                if ($patologia['nome_patologia'] === 'Adicionar Novo') continue;
                            ?>
                                <option value="<?php echo $patologia['id_patologia']; ?>" <?php echo ($patologia_selecionada == $patologia['id_patologia']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($patologia['nome_patologia']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Patologia (Texto Livre)</label>
                        <input type="text" class="form-control" name="patologia_base" value="<?php echo htmlspecialchars($_GET['patologia_base'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Conduta</label>
                        <select class="form-select" name="conduta">
                            <option value="">Todas</option>
                            <option value="Manutenção" <?php echo (($_GET['conduta'] ?? '') === 'Manutenção') ? 'selected' : ''; ?>>Manutenção</option>
                            <option value="Alta do Programa" <?php echo (($_GET['conduta'] ?? '') === 'Alta do Programa') ? 'selected' : ''; ?>>Alta do Programa</option>
                            <option value="Alta dos Serviços" <?php echo (($_GET['conduta'] ?? '') === 'Alta dos Serviços') ? 'selected' : ''; ?>>Alta dos Serviços</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">🗓️ Datas e Prazos</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-2"><label class="form-label">Nascimento (De)</label><input type="text" class="form-control date-input" name="data_nascimento_inicio" value="<?php echo htmlspecialchars($_GET['data_nascimento_inicio'] ?? ''); ?>"></div>
                    <div class="col-md-2"><label class="form-label">Nascimento (Até)</label><input type="text" class="form-control date-input" name="data_nascimento_fim" value="<?php echo htmlspecialchars($_GET['data_nascimento_fim'] ?? ''); ?>"></div>
                    <div class="col-md-2"><label class="form-label">Entrada PAE (De)</label><input type="text" class="form-control date-input" name="data_entrada_pae_inicio" value="<?php echo htmlspecialchars($_GET['data_entrada_pae_inicio'] ?? ''); ?>"></div>
                    <div class="col-md-2"><label class="form-label">Entrada PAE (Até)</label><input type="text" class="form-control date-input" name="data_entrada_pae_fim" value="<?php echo htmlspecialchars($_GET['data_entrada_pae_fim'] ?? ''); ?>"></div>
                    <div class="col-md-2"><label class="form-label">Inclusão (De)</label><input type="text" class="form-control date-input" name="data_inclusao_inicio" value="<?php echo htmlspecialchars($_GET['data_inclusao_inicio'] ?? ''); ?>"></div>
                    <div class="col-md-2"><label class="form-label">Inclusão (Até)</label><input type="text" class="form-control date-input" name="data_inclusao_fim" value="<?php echo htmlspecialchars($_GET['data_inclusao_fim'] ?? ''); ?>"></div>
                    <div class="col-md-2"><label class="form-label">Avaliação (De)</label><input type="text" class="form-control date-input" name="data_avaliacao_inicio" value="<?php echo htmlspecialchars($_GET['data_avaliacao_inicio'] ?? ''); ?>"></div>
                    <div class="col-md-2"><label class="form-label">Avaliação (Até)</label><input type="text" class="form-control date-input" name="data_avaliacao_fim" value="<?php echo htmlspecialchars($_GET['data_avaliacao_fim'] ?? ''); ?>"></div>
                    <div class="col-md-2"><label class="form-label">Início Terapias (De)</label><input type="text" class="form-control date-input" name="data_inicio_terapias_inicio" value="<?php echo htmlspecialchars($_GET['data_inicio_terapias_inicio'] ?? ''); ?>"></div>
                    <div class="col-md-2"><label class="form-label">Início Terapias (Até)</label><input type="text" class="form-control date-input" name="data_inicio_terapias_fim" value="<?php echo htmlspecialchars($_GET['data_inicio_terapias_fim'] ?? ''); ?>"></div>
                    <div class="col-md-2"><label class="form-label">Reavaliação (De)</label><input type="text" class="form-control date-input" name="data_reavaliacao_inicio" value="<?php echo htmlspecialchars($_GET['data_reavaliacao_inicio'] ?? ''); ?>"></div>
                    <div class="col-md-2"><label class="form-label">Reavaliação (Até)</label><input type="text" class="form-control date-input" name="data_reavaliacao_fim" value="<?php echo htmlspecialchars($_GET['data_reavaliacao_fim'] ?? ''); ?>"></div>
                    <div class="col-md-2"><label class="form-label">Permanência (Mín)</label><input type="number" class="form-control" name="tempo_permanencia_min" value="<?php echo htmlspecialchars($_GET['tempo_permanencia_min'] ?? ''); ?>"></div>
                    <div class="col-md-2"><label class="form-label">Permanência (Máx)</label><input type="number" class="form-control" name="tempo_permanencia_max" value="<?php echo htmlspecialchars($_GET['tempo_permanencia_max'] ?? ''); ?>"></div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">🚪 Status e Datas de Remoção</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Status de Remoção</label>
                        <select class="form-select" name="status_remocao">
                            <option value="">Todos</option>
                            <option value="removido" <?php echo (($_GET['status_remocao'] ?? '') === 'removido') ? 'selected' : ''; ?>>Removidos</option>
                            <option value="nao_removido" <?php echo (($_GET['status_remocao'] ?? '') === 'nao_removido') ? 'selected' : ''; ?>>Não Removidos</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Justificativa da Remoção</label>
                        <input type="text" class="form-control" name="justificativa_remocao" value="<?php echo htmlspecialchars($_GET['justificativa_remocao'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2"><label class="form-label">Data Remoção (De)</label><input type="text" class="form-control date-input" name="data_remocao_inicio" value="<?php echo htmlspecialchars($_GET['data_remocao_inicio'] ?? ''); ?>"></div>
                    <div class="col-md-2"><label class="form-label">Data Remoção (Até)</label><input type="text" class="form-control date-input" name="data_remocao_fim" value="<?php echo htmlspecialchars($_GET['data_remocao_fim'] ?? ''); ?>"></div>
                    <div class="col-md-2"><label class="form-label">Data Real Remoção (De)</label><input type="text" class="form-control date-input" name="data_real_remocao_inicio" value="<?php echo htmlspecialchars($_GET['data_real_remocao_inicio'] ?? ''); ?>"></div>
                    <div class="col-md-2"><label class="form-label">Data Real Remoção (Até)</label><input type="text" class="form-control date-input" name="data_real_remocao_fim" value="<?php echo htmlspecialchars($_GET['data_real_remocao_fim'] ?? ''); ?>"></div>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">🏥 Serviços e Recursos</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php
                    $servicos = [
                        'atendimento_nutricional' => 'Assist. Nutricional', 'fisioterapia_motora' => 'Fisio. Motora',
                        'fisioterapia_respiratoria' => 'Fisio. Resp.', 'fonoterapia' => 'Fonoterapia',
                        'terapia_ocupacional' => 'Terapia Ocup.', 'psicologia' => 'Psicologia',
                        'servico_social' => 'Serviço Social', 'supervisao_clinica' => 'Supervisão Clínica',
                        'oxigenio' => 'Oxigênio', 'kit_sne' => 'Kit SNE', 'kit_gtt' => 'Kit GTT',
                        'kit_tqt' => 'Kit TQT', 'kit_prevencao' => 'Kit Prevenção de LPP'
                    ];
                    foreach ($servicos as $name => $label):
                    ?>
                    <div class="col-md-2">
                        <label class="form-label"><?php echo $label; ?></label>
                        <select class="form-select" name="<?php echo $name; ?>">
                            <option value="">Todos</option>
                            <option value="Sim" <?php echo (($_GET[$name] ?? '') === 'Sim') ? 'selected' : ''; ?>>Sim</option>
                            <option value="Não" <?php echo (($_GET[$name] ?? '') === 'Não') ? 'selected' : ''; ?>>Não</option>
                        </select>
                    </div>
                    <?php endforeach; ?>

                    <div class="col-md-2">
                        <label class="form-label">Alim. Enteral</label>
                        <select class="form-select" name="alimentacao_enteral">
                            <option value="">Todas</option>
                            <option value="CONTÍNUA" <?php echo (($_GET['alimentacao_enteral'] ?? '') === 'CONTÍNUA') ? 'selected' : ''; ?>>Contínua</option>
                            <option value="CONTRATUAL" <?php echo (($_GET['alimentacao_enteral'] ?? '') === 'CONTRATUAL') ? 'selected' : ''; ?>>Contratual</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">LPP</label>
                        <select class="form-select" name="kit_lpp">
                            <option value="">Todas</option>
                            <option value="G1" <?php echo (($_GET['kit_lpp'] ?? '') === 'G1') ? 'selected' : ''; ?>>Grau 1</option>
                            <option value="G2" <?php echo (($_GET['kit_lpp'] ?? '') === 'G2') ? 'selected' : ''; ?>>Grau 2</option>
                        </select>
                    </div>
                     <div class="col-md-2">
                        <label class="form-label">Ambulância</label>
                        <select class="form-select" name="ambulancia">
                            <option value="">Todas</option>
                            <option value="U/E" <?php echo (($_GET['ambulancia'] ?? '') === 'U/E') ? 'selected' : ''; ?>>Urg./Emerg.</option>
                            <option value="Ambos (U/E e C/E)" <?php echo (($_GET['ambulancia'] ?? '') === 'Ambos (U/E e C/E)') ? 'selected' : ''; ?>>Ambos</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Clínica</label>
                        <select class="form-select" name="clinicas_credenciadas">
                            <option value="">Todas</option>
                            <option value="Medclin" <?php echo (($_GET['clinicas_credenciadas'] ?? '') === 'Medclin') ? 'selected' : ''; ?>>Medclin</option>
                            <option value="I-saúde" <?php echo (($_GET['clinicas_credenciadas'] ?? '') === 'I-saúde') ? 'selected' : ''; ?>>I-saúde</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success me-2">
                <i class="bi bi-funnel"></i> Aplicar Filtros
            </button>
            <a href="exibir_dados.php" class="btn btn-secondary me-2">
                <i class="bi bi-x-circle"></i> Limpar
            </a>
            <a href="../dashboard/dashboard.php" class="btn btn-primary">
                <i class="bi bi-arrow-left-circle"></i> Voltar
            </a>
        </div>
    </form>
</div>