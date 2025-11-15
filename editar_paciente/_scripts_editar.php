<script src="https://unpkg.com/imask@6.0.7/dist/imask.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- 1. Máscaras para campos de data ---
        const dateInputs = document.querySelectorAll('.date-input');
        dateInputs.forEach(input => {
            IMask(input, {
                mask: Date,
                pattern: 'd{/}`m{/}`Y',
                lazy: false,
                overwrite: true,
                autofix: true,
                blocks: {
                    d: { mask: IMask.MaskedRange, from: 1, to: 31 },
                    m: { mask: IMask.MaskedRange, from: 1, to: 12 },
                    Y: { mask: IMask.MaskedRange, from: 1900, to: 2050 }
                },
                format: function (date) {
                    let day = date.getDate();
                    let month = date.getMonth() + 1;
                    const year = date.getFullYear();
                    if (day < 10) day = '0' + day;
                    if (month < 10) month = '0' + month;
                    return [day, month, year].join('/');
                },
                parse: function (str) {
                    const parts = str.split('/');
                    const date = new Date(parts[2], parts[1] - 1, parts[0]);
                    return date;
                }
            });
        });

        // --- 2. Lógica AJAX para nova patologia ---
        const btnSalvarPatologia = document.getElementById('btnSalvarPatologia');
        const inputNovaPatologia = document.getElementById('inputNovaPatologia');
        const patologiaFeedback = document.getElementById('patologia-feedback');
        const patologiasContainer = $('#patologias-container');
        
        if (btnSalvarPatologia) {
            btnSalvarPatologia.addEventListener('click', function() {
                const novaPatologiaNome = inputNovaPatologia.value.trim();
                if (!novaPatologiaNome) {
                    patologiaFeedback.innerHTML = '<div class="alert alert-danger">Preencha o nome da patologia.</div>';
                    return;
                }

                $.post('_ajax_patologia.php', { nova_patologia: novaPatologiaNome }, function(data) {
                    patologiaFeedback.innerHTML = ''; 

                    if (data.success) {
                        patologiaFeedback.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                        
                        const newPatologiaHtml = `
                            <div class="form-check" id="patologia-item-${data.id}">
                                <input class="form-check-input" type="checkbox" name="patologias[]" value="${data.id}" id="patologia_${data.id}" checked>
                                <label class="form-check-label" for="patologia_${data.id}">
                                    ${data.nome}
                                </label>
                            </div>
                        `;
                        
                        const firstChunk = patologiasContainer.find('.patologia-chunk').first();
                        if (firstChunk.length) {
                            firstChunk.append(newPatologiaHtml);
                        } else {
                            patologiasContainer.html('<div class="col-md-4 patologia-chunk" data-chunk="0">' + newPatologiaHtml + '</div>');
                        }
                        
                        inputNovaPatologia.value = '';
                        setTimeout(() => {
                            $('#modalNovaPatologia').modal('hide');
                        }, 1000);

                    } else {
                        patologiaFeedback.innerHTML = '<div class="alert alert-warning">' + data.message + '</div>';
                    }
                }, 'json').fail(function() {
                    patologiaFeedback.innerHTML = '<div class="alert alert-danger">Erro de comunicação com o servidor.</div>';
                });
            });
        }

        // --- 3. LÓGICA DE REMOÇÃO (Justificativa) ---
        const dataRemocaoInput = document.getElementById('data_remocao');
        const justificativaTextarea = document.getElementById('justificativa_remocao');

        function toggleJustificativa() {
            // A IMask pode deixar um valor de placeholder. Checamos pelo comprimento real.
            const hasValidDate = dataRemocaoInput.value && dataRemocaoInput.value.length === 10;
            
            if (justificativaTextarea) {
                justificativaTextarea.disabled = !hasValidDate;
                if (!hasValidDate) {
                    justificativaTextarea.value = ''; // Limpa o campo se a data for inválida ou removida
                }
            }
        }
        
        if(dataRemocaoInput) {
            toggleJustificativa(); // Verifica o estado inicial
            dataRemocaoInput.addEventListener('input', toggleJustificativa);
            dataRemocaoInput.addEventListener('change', toggleJustificativa);
            dataRemocaoInput.addEventListener('blur', toggleJustificativa);
        }

        // --- 4. Cálculo de Idade Dinâmico (jQuery) ---
        function atualizarIdadeDisplay() {
            var dataNascStr = $('#data_nascimento').val();
            var $display = $('#idade_display');

            if (dataNascStr.length !== 10) { 
                $display.text('(idade)');
                return;
            }
            var partes = dataNascStr.split('/');
            if (partes.length !== 3) {
                $display.text('(data inválida)');
                return;
            }
            var dataNascObj = new Date(partes[2], partes[1] - 1, partes[0]); 
            if (isNaN(dataNascObj.getTime()) || dataNascObj.getDate() != parseInt(partes[0])) {
                $display.text('(data inválida)');
                return;
            }
            var hoje = new Date();
            var idade = hoje.getFullYear() - dataNascObj.getFullYear();
            var m = hoje.getMonth() - dataNascObj.getMonth();
            if (m < 0 || (m === 0 && hoje.getDate() < dataNascObj.getDate())) {
                idade--;
            }
            if (idade >= 0) {
                $display.text('(' + idade + ' anos)');
            } else {
                 $display.text('(data futura)');
            }
        }
        $('#data_nascimento').on('blur', function() {
            atualizarIdadeDisplay();
        });
        atualizarIdadeDisplay(); // Dispara no carregamento da página

        // --- 5. Aviso de dados não salvos ---
        const form = document.getElementById('form-paciente');
        if (form) {
            let formChanged = false;
            form.addEventListener('input', function() {
                formChanged = true;
            });
            window.addEventListener('beforeunload', function(event) {
                if (formChanged) {
                    const message = 'Você tem certeza que deseja sair? As alterações não salvas serão perdidas.';
                    event.returnValue = message;
                    return message;
                }
            });
            form.addEventListener('submit', function() {
                formChanged = false;
            });
        }

        // ==========================================================
        // --- 6. NOVO: LÓGICA PARA TRAVAR/DESTRAVAR FORMULÁRIO ---
        // ==========================================================
        
        // Injeta o CSS para travar campos que não podem ser readonly (selects, checks)
        const style = document.createElement('style');
        style.innerHTML = `
            fieldset.form-locked select,
            fieldset.form-locked .form-check-input {
                pointer-events: none; /* Impede cliques */
                background-color: #e9ecef; /* Cor de "disabled" do Bootstrap */
                opacity: 0.8;
            }
            fieldset.form-locked .form-check-label {
                opacity: 0.8;
            }
        `;
        document.head.appendChild(style);

        const mainFieldset = document.getElementById('main-form-fieldset');
        // 'dataRemocaoInput' já foi definido na seção 3

        /**
         * Trava (locked = true) ou Destrava (locked = false) os campos do fieldset
         */
        function setFormEditability(locked) {
            if (!mainFieldset) return;

            // Adiciona/remove a classe CSS que trava selects/checkboxes
            if (locked) {
                mainFieldset.classList.add('form-locked');
            } else {
                mainFieldset.classList.remove('form-locked');
            }

            // Encontra todos os elementos de formulário dentro do fieldset
            const formElements = mainFieldset.querySelectorAll(
                'input, textarea, select, button'
            );

            formElements.forEach(element => {
                const tag = element.tagName.toLowerCase();
                const type = element.type ? element.type.toLowerCase() : '';

                // Inputs de texto/data/numero -> READONLY (para que seus valores sejam enviados)
                if (tag === 'input' && (type === 'text' || type === 'number' || type === 'date') || tag === 'textarea') {
                    element.readOnly = locked;
                } 
                // Botões (como "+ Nova Patologia") -> DISABLED (não envia valor)
                else if (tag === 'button') {
                    element.disabled = locked;
                }
                // Selects e Checkboxes -> Não usamos 'disabled'. 
                // A classe CSS 'form-locked' e o 'tabIndex' cuidam deles.
                else if (tag === 'select' || type === 'checkbox' || type === 'radio') {
                     element.tabIndex = locked ? -1 : 0; // Remove/adiciona da navegação por teclado
                }
            });
        }

        // --- Gatilhos da Lógica de Trava ---

        // 1. Verificação no Carregamento da Página:
        if (mainFieldset) {
            const initialState = mainFieldset.getAttribute('data-initial-state');
            if (initialState === 'disabled') {
                setFormEditability(true);
            }
        }

        // 2. Listener Dinâmico no campo data_remocao:
        if (dataRemocaoInput) {
            const checkRemocaoStatus = () => {
                // 'value.length === 10' é a verificação mais segura (DD/MM/AAAA)
                const hasDate = dataRemocaoInput.value.length === 10;
                setFormEditability(hasDate);
            };

            dataRemocaoInput.addEventListener('blur', checkRemocaoStatus);
            dataRemocaoInput.addEventListener('change', checkRemocaoStatus);
        }

    }); // <-- FIM DO DOMCONTENTLOADED
</script>