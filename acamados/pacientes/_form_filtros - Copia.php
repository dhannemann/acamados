<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="exibir_dados.php" class="row g-3">

            <!-- Informações do Paciente -->
            <h5 class="mt-3">🧑 Informações do Paciente</h5>
            <div class="col-md-3">
                <label for="numero_processo" class="form-label">Nº Processo</label>
                <input type="text" class="form-control" id="numero_processo" name="numero_processo"
                       value="<?php echo htmlspecialchars($_GET['numero_processo'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label for="termo_adesao" class="form-label">Termo de Adesão</label>
                <input type="text" class="form-control" id="termo_adesao" name="termo_adesao"
                       value="<?php echo htmlspecialchars($_GET['termo_adesao'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label for="paciente_assistlar" class="form-label">Paciente Assistlar</label>
                <input type="text" class="form-control" id="paciente_assistlar" name="paciente_assistlar"
                       value="<?php echo htmlspecialchars($_GET['paciente_assistlar'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label for="genero" class="form-label">Gênero</label>
                <select class="form-select" id="genero" name="genero">
                    <option value="" <?php echo (($_GET['genero'] ?? '') == '') ? 'selected' : ''; ?>>Todos</option>
                    <option value="M" <?php echo (($_GET['genero'] ?? '') == 'M') ? 'selected' : ''; ?>>Masculino</option>
                    <option value="F" <?php echo (($_GET['genero'] ?? '') == 'F') ? 'selected' : ''; ?>>Feminino</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="data_nascimento_inicio" class="form-label">Data NASCIMENTO (Início)</label>
                <input type="text" class="form-control date-input" id="data_nascimento_inicio" name="data_nascimento_inicio"
                       value="<?php echo htmlspecialchars($_GET['data_nascimento_inicio'] ?? ''); ?>" placeholder="DD/MM/AAAA">
            </div>
            <div class="col-md-3">
                <label for="data_nascimento_fim" class="form-label">Data NASCIMENTO (Fim)</label>
                <input type="text" class="form-control date-input" id="data_nascimento_fim" name="data_nascimento_fim"
                       value="<?php echo htmlspecialchars($_GET['data_nascimento_fim'] ?? ''); ?>" placeholder="DD/MM/AAAA">
            </div>
            <div class="col-md-3">
                <label for="vinculo" class="form-label">Vínculo</label>
                <input type="text" class="form-control" id="vinculo" name="vinculo"
                       value="<?php echo htmlspecialchars($_GET['vinculo'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label for="tempo_permanencia" class="form-label">Tempo Permanência (meses)</label>
                <input type="text" class="form-control" id="tempo_permanencia" name="tempo_permanencia"
                       value="<?php echo htmlspecialchars($_GET['tempo_permanencia'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label for="status_acamado" class="form-label">Status</label>
                <select class="form-select" id="status_acamado" name="status_acamado">
                    <option value="" <?php echo (($_GET['status_acamado'] ?? '') == '') ? 'selected' : ''; ?>>Todos</option>
                    <option value="ativo" <?php echo (($_GET['status_acamado'] ?? '') == 'ativo') ? 'selected' : ''; ?>>Ativo</option>
                    <option value="inativo" <?php echo (($_GET['status_acamado'] ?? '') == 'inativo') ? 'selected' : ''; ?>>Inativo</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="patologia_base" class="form-label">Patologia de Base</label>
                <input type="text" class="form-control" id="patologia_base" name="patologia_base"
                       value="<?php echo htmlspecialchars($_GET['patologia_base'] ?? ''); ?>">
            </div>

            <!-- Contato e Localização -->
            <h5 class="mt-4">📍 Contato e Localização</h5>
            <div class="col-md-4">
                <label for="endereco" class="form-label">Endereço</label>
                <input type="text" class="form-control" id="endereco" name="endereco"
                       value="<?php echo htmlspecialchars($_GET['endereco'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label for="bairro" class="form-label">Bairro</label>
                <input type="text" class="form-control" id="bairro" name="bairro"
                       value="<?php echo htmlspecialchars($_GET['bairro'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label for="municipio" class="form-label">Município</label>
                <input type="text" class="form-control" id="municipio" name="municipio"
                       value="<?php echo htmlspecialchars($_GET['municipio'] ?? ''); ?>">
            </div>
            <div class="col-md-2">
                <label for="fone" class="form-label">Telefone</label>
                <input type="text" class="form-control" id="fone" name="fone"
                       value="<?php echo htmlspecialchars($_GET['fone'] ?? ''); ?>">
            </div>

            <!-- Serviços de Assistência -->
            <h5 class="mt-4">⚕️ Serviços de Assistência</h5>
            <div class="col-md-3">
                <label for="ambulancia" class="form-label">Ambulância</label>
                <input type="text" class="form-control" id="ambulancia" name="ambulancia"
                       value="<?php echo htmlspecialchars($_GET['ambulancia'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label for="atendimento_nutricional" class="form-label">Assistência Nutricional</label>
                <input type="text" class="form-control" id="atendimento_nutricional" name="atendimento_nutricional"
                       value="<?php echo htmlspecialchars($_GET['atendimento_nutricional'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label for="fisioterapia_motora" class="form-label">Fisioterapia Motora</label>
                <input type="text" class="form-control" id="fisioterapia_motora" name="fisioterapia_motora"
                       value="<?php echo htmlspecialchars($_GET['fisioterapia_motora'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label for="fisioterapia_respiratoria" class="form-label">Fisioterapia Respiratória</label>
                <input type="text" class="form-control" id="fisioterapia_respiratoria" name="fisioterapia_respiratoria"
                       value="<?php echo htmlspecialchars($_GET['fisioterapia_respiratoria'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label for="fonoterapia" class="form-label">Fonoterapia</label>
                <input type="text" class="form-control" id="fonoterapia" name="fonoterapia"
                       value="<?php echo htmlspecialchars($_GET['fonoterapia'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label for="terapia_ocupacional" class="form-label">Terapia Ocupacional</label>
                <input type="text" class="form-control" id="terapia_ocupacional" name="terapia_ocupacional"
                       value="<?php echo htmlspecialchars($_GET['terapia_ocupacional'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label for="psicologia" class="form-label">Psicologia</label>
                <input type="text" class="form-control" id="psicologia" name="psicologia"
                       value="<?php echo htmlspecialchars($_GET['psicologia'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label for="servico_social" class="form-label">Serviço Social</label>
                <input type="text" class="form-control" id="servico_social" name="servico_social"
                       value="<?php echo htmlspecialchars($_GET['servico_social'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label for="supervisao_clinica" class="form-label">Supervisão Clínica</label>
                <input type="text" class="form-control" id="supervisao_clinica" name="supervisao_clinica"
                       value="<?php echo htmlspecialchars($_GET['supervisao_clinica'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label for="kit_material" class="form-label">Kit de Material</label>
                <input type="text" class="form-control" id="kit_material" name="kit_material"
                       value="<?php echo htmlspecialchars($_GET['kit_material'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label for="oxigenio" class="form-label">Oxigênio</label>
                <input type="text" class="form-control" id="oxigenio" name="oxigenio"
                       value="<?php echo htmlspecialchars($_GET['oxigenio'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label for="cuidador_responsavel" class="form-label">Cuidador Responsável</label>
                <input type="text" class="form-control" id="cuidador_responsavel" name="cuidador_responsavel"
                       value="<?php echo htmlspecialchars($_GET['cuidador_responsavel'] ?? ''); ?>">
            </div>

            <!-- Datas de Inclusão -->
            <h5 class="mt-4">📅 Período de Inclusão</h5>
            <div class="col-md-3">
                <label for="mes_inclusao_inicio" class="form-label">Mês Inclusão (Início)</label>
                <input type="text" class="form-control date-input" id="mes_inclusao_inicio" name="mes_inclusao_inicio"
                       value="<?php echo htmlspecialchars($_GET['mes_inclusao_inicio'] ?? ''); ?>" placeholder="DD/MM/AAAA">
            </div>
            <div class="col-md-3">
                <label for="mes_inclusao_fim" class="form-label">Mês Inclusão (Fim)</label>
                <input type="text" class="form-control date-input" id="mes_inclusao_fim" name="mes_inclusao_fim"
                       value="<?php echo htmlspecialchars($_GET['mes_inclusao_fim'] ?? ''); ?>" placeholder="DD/MM/AAAA">
            </div>

            <!-- Botões -->
            <div class="col-12 text-center mt-4">
                <button type="submit" class="btn btn-success me-2">Aplicar Filtros</button>
                <a href="exibir_dados.php" class="btn btn-secondary me-2">Limpar</a>
                <a href="../dashboard/dashboard.php" class="btn btn-primary">Voltar</a>
            </div>
        </form>
    </div>
</div>
