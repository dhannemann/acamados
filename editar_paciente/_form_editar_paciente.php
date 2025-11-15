<?php
// dashboard/_form_editar_paciente.php
global $patologias_list;
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <h1 class="text-center mb-4">Editar Paciente</h1>

            <?php if ($mensagem_sucesso): ?>
                <div class="alert alert-success" role="alert"><?php echo $mensagem_sucesso; ?></div>
            <?php endif; ?>
            <?php if ($mensagem_erro): ?>
                <div class="alert alert-danger" role="alert"><?php echo $mensagem_erro; ?></div>
            <?php endif; ?>

            <?php if (!empty($numero_processo_val) && !empty($termo_adesao_val)): ?>
                <form method="POST" action="editar_paciente.php" id="form-paciente">
                    <input type="hidden" name="processo_original" value="<?php echo htmlspecialchars($processo_original); ?>">
                    <input type="hidden" name="termo_original" value="<?php echo htmlspecialchars($termo_original); ?>">

                    <fieldset id="main-form-fieldset" data-initial-state="<?php echo $form_disabled_state; ?>">

                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <h4 class="card-title mb-3">🧑 Informações Pessoais e Vínculo</h4>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="numero_processo" class="form-label">Nº DO PROCESSO <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="numero_processo" name="numero_processo" value="<?php echo htmlspecialchars($numero_processo_val); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="termo_adesao" class="form-label">TERMO DE ADESÃO <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="termo_adesao" name="termo_adesao" value="<?php echo htmlspecialchars($termo_adesao_val); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nome_segurado" class="form-label">NOME DO SEGURADO <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nome_segurado" name="nome_segurado" value="<?php echo htmlspecialchars($nome_segurado_val); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="cuidador_responsavel" class="form-label">CUIDADOR RESPONSÁVEL <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="cuidador_responsavel" name="cuidador_responsavel" value="<?php echo htmlspecialchars($cuidador_responsavel_val); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="data_nascimento" class="form-label">DATA NASCIMENTO <span class="text-danger">*</span></label>
                                            <?php $idade_atual = calcular_idade($data_nascimento_val); ?>
                                            <div class="input-group">
                                                <input type="text" class="form-control date-input" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($data_nascimento_val); ?>" placeholder="DD/MM/AAAA" required>
                                                <span class="input-group-text text-muted" id="idade_display" style="min-width: 80px;">
                                                    <?php echo ($idade_atual !== null) ? "($idade_atual anos)" : "(idade)"; ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="genero" class="form-label">SEXO <span class="text-danger">*</span></label>
                                            <select class="form-select" id="genero" name="genero" required>
                                                <option value="">Selecione...</option>
                                                <option value="M" <?php echo select_option($genero_val, 'M'); ?>>Masculino</option>
                                                <option value="F" <?php echo select_option($genero_val, 'F'); ?>>Feminino</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="vinculo" class="form-label">VÍNCULO <span class="text-danger">*</span></label>
                                            <select class="form-select" id="vinculo" name="vinculo" required>
                                                <option value="" disabled>Selecione...</option>
                                                <option value="-" <?php echo select_option($vinculo_val, '-'); ?>>-</option>
                                                <option value="Titular" <?php echo select_option($vinculo_val, 'Titular'); ?>>Titular</option>
                                                <option value="Dependente" <?php echo select_option($vinculo_val, 'Dependente'); ?>>Dependente</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="status_paciente" class="form-label">STATUS <span class="text-danger">*</span></label>
                                            <select class="form-select" id="status_paciente" name="status_paciente" required>
                                                <option value="Regular" <?php echo select_option($status_paciente_val, 'Regular'); ?>>Regular</option>
                                                <option value="Irregular" <?php echo select_option($status_paciente_val, 'Irregular'); ?>>Irregular</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="profissao_paciente" class="form-label">PROFISSÃO</label>
                                            <input type="text" class="form-control" id="profissao_paciente" name="profissao_paciente" value="<?php echo htmlspecialchars($profissao_paciente_val); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="renda_paciente" class="form-label">RENDA (R$)</label>
                                            <input type="number" step="0.01" class="form-control" id="renda_paciente" name="renda_paciente" value="<?php echo htmlspecialchars($renda_paciente_val); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="clinicas_credenciadas" class="form-label">CLÍNICAS CREDENCIADAS</label>
                                            <select class="form-select" id="clinicas_credenciadas" name="clinicas_credenciadas">
                                                <option value="-" <?php echo select_option($clinicas_credenciadas_val, '-'); ?>>-</option>
                                                <option value="Medclin" <?php echo select_option($clinicas_credenciadas_val, 'Medclin'); ?>>Medclin</option>
                                                <option value="I-saúde" <?php echo select_option($clinicas_credenciadas_val, 'I-saúde'); ?>>I-saúde</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <h4 class="card-title mb-3">🩺 PATOLOGIA DE BASE
                                    <button type="button" class="btn btn-sm btn-outline-success float-end" data-bs-toggle="modal" data-bs-target="#modalNovaPatologia">
                                        + Nova Patologia
                                    </button>
                                </h4>
                                <div class="row" id="patologias-container">
                                    <?php 
                                    $total_patologias = count($patologias_list);
                                    $chunk_size = ($total_patologias > 0) ? max(1, ceil($total_patologias / 3)) : 0;
                                    
                                    if ($chunk_size > 0) {
                                        $chunks = array_chunk($patologias_list, $chunk_size);
                                        foreach ($chunks as $i => $chunk): 
                                    ?>
                                    <div class="col-md-4 patologia-chunk" data-chunk="<?php echo $i; ?>">
                                        <?php foreach ($chunk as $patologia): ?>
                                            <div class="form-check" id="patologia-item-<?php echo $patologia['id_patologia']; ?>">
                                                <input class="form-check-input" type="checkbox" name="patologias[]" value="<?php echo $patologia['id_patologia']; ?>" id="patologia_<?php echo $patologia['id_patologia']; ?>"
                                                    <?php echo in_array($patologia['id_patologia'], $patologias_selecionadas_val) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="patologia_<?php echo $patologia['id_patologia']; ?>">
                                                    <?php echo htmlspecialchars($patologia['nome_patologia']); ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php 
                                        endforeach; 
                                    } else {
                                        echo '<div class="col-12"><p class="text-muted">Nenhuma patologia cadastrada. Use o botão acima para adicionar uma.</p></div>';
                                    }
                                    ?>
                                </div>
                                <div class="mb-3">
                                    <label for="patologia_base" class="form-label">Patologia Base (Texto Livre)</label>
                                    <input type="text" class="form-control" id="patologia_base" name="patologia_base" value="<?php echo htmlspecialchars($patologia_base_val ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <h4 class="card-title mb-3">📍 ENDEREÇO E CONTATO</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="endereco" class="form-label">ENDEREÇO</label>
                                            <input type="text" class="form-control" id="endereco" name="endereco" value="<?php echo htmlspecialchars($endereco_val); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="bairro" class="form-label">BAIRRO</label>
                                            <input type="text" class="form-control" id="bairro" name="bairro" value="<?php echo htmlspecialchars($bairro_val); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="municipio" class="form-label">MUNICÍPIO</label>
                                            <input type="text" class="form-control" id="municipio" name="municipio" value="<?php echo htmlspecialchars($municipio_val); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="fone" class="form-label">FONE</label>
                                            <input type="text" class="form-control" id="fone" name="fone" value="<?php echo htmlspecialchars($fone_val); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <h4 class="card-title mb-3">🗓️ DATAS DO ATENDIMENTO</h4>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="data_entrada_pae" class="form-label">ENTRADA PAE <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control date-input" id="data_entrada_pae" name="data_entrada_pae" value="<?php echo htmlspecialchars($data_entrada_pae_val); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="data_inicio_terapias" class="form-label">INÍCIO DAS TERAPIAS</label>
                                            <input type="text" class="form-control date-input" id="data_inicio_terapias" name="data_inicio_terapias" value="<?php echo htmlspecialchars($data_inicio_terapias_val); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="data_avaliacao" class="form-label">AVALIAÇÃO <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control date-input" id="data_avaliacao" name="data_avaliacao" value="<?php echo htmlspecialchars($data_avaliacao_val); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="data_reavaliacao" class="form-label">REAVALIAÇÃO</label>
                                            <input type="text" class="form-control date-input" id="data_reavaliacao" name="data_reavaliacao" value="<?php echo htmlspecialchars($data_reavaliacao_val); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="data_inclusao" class="form-label">INCLUSÃO <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control date-input" id="data_inclusao" name="data_inclusao" value="<?php echo htmlspecialchars($data_inclusao_val); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="tempo_permanencia" class="form-label">TEMPO DE PERMANÊNCIA (MESES) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="tempo_permanencia" name="tempo_permanencia" value="<?php echo htmlspecialchars($tempo_permanencia_val); ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <h4 class="card-title mb-3">🏥 RECURSOS E SERVIÇOS</h4>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="atendimento_nutricional" name="atendimento_nutricional" <?php echo is_toggle_checked($atendimento_nutricional_val); ?>>
                                            <label class="form-check-label" for="atendimento_nutricional">Assistência Nutricional</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="fisioterapia_motora" name="fisioterapia_motora" <?php echo is_toggle_checked($fisioterapia_motora_val); ?>>
                                            <label class="form-check-label" for="fisioterapia_motora">Fisioterapia Motora</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="fisioterapia_respiratoria" name="fisioterapia_respiratoria" <?php echo is_toggle_checked($fisioterapia_respiratoria_val); ?>>
                                            <label class="form-check-label" for="fisioterapia_respiratoria">Fisioterapia Respiratória</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="fonoterapia" name="fonoterapia" <?php echo is_toggle_checked($fonoterapia_val); ?>>
                                            <label class="form-check-label" for="fonoterapia">Fonoterapia</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="terapia_ocupacional" name="terapia_ocupacional" <?php echo is_toggle_checked($terapia_ocupacional_val); ?>>
                                            <label class="form-check-label" for="terapia_ocupacional">Terapia Ocupacional</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="psicologia" name="psicologia" <?php echo is_toggle_checked($psicologia_val); ?>>
                                            <label class="form-check-label" for="psicologia">Psicologia</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="supervisao_clinica" name="supervisao_clinica" <?php echo is_toggle_checked($supervisao_clinica_val); ?>>
                                            <label class="form-check-label" for="supervisao_clinica">Supervisão Clínica</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="oxigenio" name="oxigenio" <?php echo is_toggle_checked($oxigenio_val); ?>>
                                            <label class="form-check-label" for="oxigenio">Oxigênio</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="kit_sne" name="kit_sne" <?php echo is_toggle_checked($kit_sne_val); ?>>
                                            <label class="form-check-label" for="kit_sne">SNE (Sonda Nasoenteral)</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="kit_gtt" name="kit_gtt" <?php echo is_toggle_checked($kit_gtt_val); ?>>
                                            <label class="form-check-label" for="kit_gtt">GTT (Sonda de Gastrostomia)</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="kit_tqt" name="kit_tqt" <?php echo is_toggle_checked($kit_tqt_val); ?>>
                                            <label class="form-check-label" for="kit_tqt">TQT (Traqueostomia)</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="kit_prevencao" name="kit_prevencao" <?php echo is_toggle_checked($kit_prevencao_val); ?>>
                                            <label class="form-check-label" for="kit_prevencao">Kit Prevenção de LPP</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="servico_social" name="servico_social" <?php echo is_toggle_checked($servico_social_val); ?>>
                                            <label class="form-check-label" for="servico_social">Serviço Social</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="alimentacao_enteral" class="form-label">Alimentação Enteral</label>
                                                    <select class="form-select" id="alimentacao_enteral" name="alimentacao_enteral">
                                                        <option value="-" <?php echo select_option($alimentacao_enteral_val, '-'); ?>>-</option>
                                                        <option value="CONTÍNUA" <?php echo select_option($alimentacao_enteral_val, 'CONTÍNUA'); ?>>CONTÍNUA</option>
                                                        <option value="CONTRATUAL" <?php echo select_option($alimentacao_enteral_val, 'CONTRATUAL'); ?>>CONTRATUAL</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="kit_lpp" class="form-label">LPP (Lesão Por Pressão)</label>
                                                    <select class="form-select" id="kit_lpp" name="kit_lpp">
                                                        <option value="-" <?php echo select_option($kit_lpp_val, '-'); ?>>-</option>
                                                        <option value="G1" <?php echo select_option($kit_lpp_val, 'G1'); ?>>Grau 1</option>
                                                        <option value="G2" <?php echo select_option($kit_lpp_val, 'G2'); ?>>Grau 2</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="ambulancia" class="form-label">AMBULANCIA</label>
                                                    <select class="form-select" id="ambulancia" name="ambulancia">
                                                        <option value="-" <?php echo select_option($ambulancia_val, '-'); ?>>-</option>
                                                        <option value="U/E" <?php echo select_option($ambulancia_val, 'U/E'); ?>>Urgência/Emergência</option>
                                                        <option value="Ambos (U/E e C/E)" <?php echo select_option($ambulancia_val, 'Ambos (U/E e C/E)'); ?>>Ambos (Urgência/Emergência e Consutla/Exames)</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="conduta" class="form-label">CONDUTA</label>
                                                    <select class="form-select" id="conduta" name="conduta">
                                                        <option value="-" <?php echo select_option($conduta_val, '-'); ?>>-</option>
                                                        <option value="Manutenção" <?php echo select_option($conduta_val, 'Manutenção'); ?>>Manutenção</option>
                                                        <option value="Alta do Programa" <?php echo select_option($conduta_val, 'Alta do Programa'); ?>>Alta do Programa</option>
                                                        <option value="Alta dos Serviços" <?php echo select_option($conduta_val, 'Alta dos Serviços'); ?>>Alta dos Serviços</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </fieldset> 
                    <div class="card mb-4 shadow-sm border-danger">
                        <div class="card-body">
                            <h4 class="card-title mb-3 text-danger">🚪 Remoção do Paciente</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="data_remocao" class="form-label">Data da Remoção</label>
                                        <input type="text" class="form-control date-input" id="data_remocao" name="data_remocao" value="<?php echo htmlspecialchars($data_remocao_val); ?>">
                                        <small class="form-text text-muted">Deixe em branco se o paciente estiver ativo.</small>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="justificativa_remocao" class="form-label">Justificativa da Remoção</label>
                                        <textarea class="form-control" id="justificativa_remocao" name="justificativa_remocao" rows="3"><?php echo htmlspecialchars($justificativa_remocao_val); ?></textarea>
                                        <small class="form-text text-muted">A justificativa só é permitida se houver uma data de remoção.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="submit" class="btn btn-success">Salvar Alterações</button>
                        <a href="../pacientes/exibir_dados.php" class="btn btn-secondary">Voltar</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<br /><br />

<div class="modal fade" id="modalNovaPatologia" tabindex="-1" aria-labelledby="modalNovaPatologiaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNovaPatologiaLabel">Cadastrar Nova Patologia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="inputNovaPatologia" class="form-label">Nome da Patologia</label>
                    <input type="text" class="form-control" id="inputNovaPatologia" required>
                </div>
                <div id="patologia-feedback" class="mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="btnSalvarPatologia">Salvar e Adicionar</button>
            </div>
        </div>
    </div>
</div>