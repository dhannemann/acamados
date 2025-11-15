<script src="https://unpkg.com/imask@6.0.7/dist/imask.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Scripts de Máscara (IMask) ---
        // Máscara de Data (MANTIDA)
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

        // O BLOCO DE MÁSCARA DO CAMPO 'fone' FOI REMOVIDO AQUI!

        // --- 2. Lógica AJAX para Nova Patologia (MANTIDA) ---
        const btnSalvarPatologia = document.getElementById('btnSalvarPatologia');
        const inputNovaPatologia = document.getElementById('inputNovaPatologia');
        const patologiaFeedback = document.getElementById('patologia-feedback');
        const patologiasContainer = $('#patologias-container');
        
        btnSalvarPatologia.addEventListener('click', function() {
            const novaPatologiaNome = inputNovaPatologia.value.trim();
            if (!novaPatologiaNome) {
                patologiaFeedback.innerHTML = '<div class="alert alert-danger">Preencha o nome da patologia.</div>';
                return;
            }

            // Envia a requisição AJAX
            $.post('_ajax_patologia.php', { nova_patologia: novaPatologiaNome }, function(data) {
                patologiaFeedback.innerHTML = ''; 

                if (data.success) {
                    patologiaFeedback.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                    
                    // Cria o novo elemento HTML para a patologia
                    const newPatologiaHtml = `
                        <div class="form-check" id="patologia-item-${data.id}">
                            <input class="form-check-input" type="checkbox" name="patologias[]" value="${data.id}" id="patologia_${data.id}" checked>
                            <label class="form-check-label" for="patologia_${data.id}">
                                ${data.nome}
                            </label>
                        </div>
                    `;
                    
                    // Adiciona o novo checkbox ao primeiro bloco de colunas de patologia
                    const firstChunk = patologiasContainer.find('.patologia-chunk').first();
                    if (firstChunk.length) {
                        firstChunk.append(newPatologiaHtml);
                    } else {
                        // Se não houver nenhuma coluna, cria a primeira
                        patologiasContainer.html('<div class="col-md-4 patologia-chunk" data-chunk="0">' + newPatologiaHtml + '</div>');
                    }
                    
                    // Limpa o input e fecha o modal após um pequeno atraso
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

        // --- 3. Script de aviso de dados não salvos (MANTIDO) ---
        const form = document.getElementById('form-paciente');
        let formChanged = false;

        form.addEventListener('input', function() {
            formChanged = true;
        });

        window.addEventListener('beforeunload', function(event) {
            if (formChanged) {
                const message = 'Você tem certeza que deseja sair? Os dados não salvos serão perdidos.';
                event.returnValue = message;
                return message;
            }
        });

        form.addEventListener('submit', function() {
            formChanged = false;
        });
        
        // --- 4. NOVO CÓDIGO: Cálculo de Idade Dinâmico (jQuery) ---
        
        function atualizarIdadeDisplay() {
            var dataNascStr = $('#data_nascimento').val();
            var $display = $('#idade_display');

            if (dataNascStr.length !== 10) { // Espera o formato DD/MM/AAAA
                $display.text('(idade)');
                return;
            }

            // Tenta converter DD/MM/AAAA para um formato que o JS entenda
            var partes = dataNascStr.split('/');
            if (partes.length !== 3) {
                $display.text('(data inválida)');
                return;
            }
            
            // Formato YYYY-MM-DD (ano, mês-1, dia)
            var dataNascObj = new Date(partes[2], partes[1] - 1, partes[0]); 

            // Verifica se a data é realmente válida (ex: 31/02/2000)
            if (isNaN(dataNascObj.getTime()) || dataNascObj.getDate() != parseInt(partes[0])) {
                $display.text('(data inválida)');
                return;
            }

            // Calcula a idade
            var hoje = new Date();
            var idade = hoje.getFullYear() - dataNascObj.getFullYear();
            var m = hoje.getMonth() - dataNascObj.getMonth();
            
            if (m < 0 || (m === 0 && hoje.getDate() < dataNascObj.getDate())) {
                idade--;
            }

            // Exibe a idade
            if (idade >= 0) {
                $display.text('(' + idade + ' anos)');
            } else {
                 $display.text('(data futura)');
            }
        }

        // Gatilho: Dispara o cálculo quando o usuário termina de digitar (perde o foco)
        $('#data_nascimento').on('blur', function() {
            atualizarIdadeDisplay();
        });

        // --- FIM DO NOVO CÓDIGO ---

    });
</script>