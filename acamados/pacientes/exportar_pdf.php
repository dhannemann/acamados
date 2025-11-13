<?php
require_once '../header.php'; // Inclui o arquivo de cabeçalho padrão.
require_once __DIR__ . '/../config.php'; // Inclui o arquivo de configuração do banco de dados.
require_once __DIR__ . '/filtros.php'; // Inclui a lógica para construir as condições SQL dos filtros.

function get_vencimento_status($data_inclusao, $tempo_permanencia) { // Função que calcula o status de vencimento com base na data de inclusão e tempo de permanência.
    if (empty($data_inclusao) || empty($tempo_permanencia) || !is_numeric($tempo_permanencia)) { // Verifica se os dados essenciais para o cálculo estão faltando.
        return ['status' => 'Inválido', 'class' => 'bg-secondary', 'message' => 'Dados Faltando']; // Retorna status 'Inválido' se os dados estiverem incompletos.
    }
    $data_inclusao_obj = DateTime::createFromFormat('Y-m-d', $data_inclusao); // Tenta criar um objeto DateTime a partir da data de inclusão.
    if ($data_inclusao_obj === false || $data_inclusao === '0000-00-00') { // Verifica se a data de inclusão é inválida ou nula.
        return ['status' => 'Data Inválida', 'class' => 'bg-secondary', 'message' => 'Data de Inclusão Nula ou Inválida']; // Retorna status de data inválida.
    }
    try { // Inicia um bloco try-catch para lidar com possíveis erros em cálculos de data.
        $data_vencimento = clone $data_inclusao_obj; // Cria uma cópia do objeto da data de inclusão para calcular o vencimento.
        $data_vencimento->modify("+$tempo_permanencia months"); // Adiciona o número de meses de permanência para obter a data de vencimento.
        $data_atual = new DateTime(); // Pega a data e hora atuais.
        $data_alerta_limite = clone $data_vencimento; // Cria uma cópia da data de vencimento para calcular o início do período de alerta.
        $data_alerta_limite->modify("-1 month"); // Define a data de alerta como um mês antes do vencimento.
        if ($data_atual > $data_vencimento) { // Verifica se a data atual já passou da data de vencimento.
            return ['status' => 'Vencido', 'class' => 'bg-danger', 'message' => 'Venceu no dia ' . $data_vencimento->format('d/m/Y')]; // Retorna status 'Vencido'.
        }
        if ($data_atual >= $data_alerta_limite && $data_atual <= $data_vencimento) { // Verifica se a data atual está dentro do período de alerta (último mês).
            $mensagem = ($data_atual->format('Y-m-d') === $data_vencimento->format('Y-m-d')) // Verifica se o vencimento é exatamente hoje.
                ? "Vence hoje" // Mensagem se vence hoje.
                : "Vence em " . $data_vencimento->diff($data_atual)->days . " dias"; // Mensagem se vence nos próximos dias.
            return ['status' => 'Vencimento Próximo', 'class' => 'bg-warning', 'message' => $mensagem]; // Retorna status 'Vencimento Próximo'.
        }
        return ['status' => 'OK', 'class' => 'bg-success', 'message' => 'Vencimento: ' . $data_vencimento->format('d/m/Y')]; // Se não estiver vencido ou próximo, retorna 'OK'.
    } catch (Exception $e) { // Captura qualquer exceção durante o cálculo.
        return ['status' => 'Erro', 'class' => 'bg-dark', 'message' => 'Erro de cálculo']; // Retorna um status de erro genérico.
    }
}

function formatar_data($data) { // Função para formatar uma data do formato 'Y-m-d' para 'd/m/Y'.
    if (empty($data) || $data === '0000-00-00') return ''; // Retorna vazio se a data for nula ou inválida.
    try {
        $data_obj = new DateTime($data);
        return $data_obj->format('d/m/Y H:i'); // Formata com hora para campos DATETIME
    } catch(Exception $e) {
        return '';
    }
}

function formatar_numero($numero) { // Função para formatar um número como moeda brasileira.
    if (empty($numero) || !is_numeric($numero)) return ''; // Retorna vazio se o valor não for um número válido.
    return number_format($numero, 2, ',', '.'); // Formata o número com 2 casas decimais, usando vírgula como separador decimal e ponto para milhares.
}

$colunas_selecionadas = []; // Inicializa um array para armazenar as colunas selecionadas pelo usuário.
if (isset($_POST['colunas'])) { // Verifica se as colunas foram enviadas via método POST.
    $colunas_selecionadas = $_POST['colunas']; // Atribui as colunas do POST à variável.
} elseif (isset($_GET['colunas'])) { // Se não veio por POST, verifica se vieram via GET (ao voltar para a página).
    $colunas_selecionadas = explode(',', $_GET['colunas']); // Separa a string de colunas da URL em um array.
}

$autoload_path = __DIR__ . '/../vendor/autoload.php'; // Define o caminho para o autoloader do Composer.
$dompdf_available = file_exists($autoload_path); // Verifica se o arquivo de autoload existe.
if ($dompdf_available) { // Se o arquivo existe...
    require_once $autoload_path; // Inclui o autoloader.
    if (!class_exists('Dompdf\Dompdf')) { // Verifica se a classe principal do Dompdf pode ser encontrada.
        $dompdf_available = false; // Marca que o Dompdf não está disponível se a classe não existir.
    }
}
use Dompdf\Dompdf; // Importa a classe Dompdf para uso no script.

$get_params = $_GET; // Copia os parâmetros GET para uma variável que será usada para construir URLs.

// ALTERADO: Adicionada a data_cadastro
$colunas_disponiveis = [ 
    'numero_processo' => 'Nº Processo', 'termo_adesao' => 'Termo de Adesão', 'nome_segurado' => 'Nome Segurado', 'patologias' => 'Patologias', 'patologia_base' => 'Patologia Texto Livre', 'profissao_paciente' => 'Profissão', 'renda_paciente' => 'Renda', 'status_paciente' => 'Status', 'genero' => 'Gênero', 'data_nascimento' => 'Data Nascimento', 'vinculo' => 'Vínculo', 'data_entrada_pae' => 'Data Entrada PAE', 'data_avaliacao' => 'Data Avaliação', 'data_inclusao' => 'Data Inclusão', 'tempo_permanencia' => 'Tempo Permanência', 'data_inicio_terapias' => 'Data Início Terapias', 'clinicas_credenciadas' => 'Clínica', 'data_reavaliacao' => 'Data Reavaliação', 'conduta' => 'Conduta', 'endereco' => 'Endereço', 'bairro' => 'Bairro', 'municipio' => 'Município', 'fone' => 'Telefone', 'ambulancia' => 'Ambulância', 'atendimento_nutricional' => 'Assistência Nutricional', 'fisioterapia_motora' => 'Fisioterapia Motora', 'fisioterapia_respiratoria' => 'Fisioterapia Respiratória', 'fonoterapia' => 'Fonoterapia', 'terapia_ocupacional' => 'Terapia Ocupacional', 'psicologia' => 'Psicologia', 'servico_social' => 'Serviço Social', 'supervisao_clinica' => 'Supervisão Clínica', 'kit_sne' => 'Kit SNE', 'kit_lpp' => 'Kit LPP', 'kit_gtt' => 'Kit GTT', 'kit_tqt' => 'Kit TQT', 'kit_prevencao' => 'Kit Prevenção', 'alimentacao_enteral' => 'Alimentação Enteral', 'oxigenio' => 'Oxigênio', 'cuidador_responsavel' => 'Cuidador Responsável', 'alerta' => 'Alerta',
    'data_cadastro' => 'Data Cadastro', 'data_remocao' => 'Data Remoção', 'justificativa_remocao' => 'Justificativa Remoção', 'data_real_remocao' => 'Data Real Remoção'
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['colunas'])) { // Verifica se a requisição não é um POST ou se as colunas não foram enviadas.
?> 
<!doctype html> <html lang="pt-br">
<head>
    <meta charset="utf-8"><title>Selecionar Colunas para Exportar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>html, body { margin: 0; padding: 0; }</style>
</head>
<body class="bg-light">
    <?php require_once '../header.php'; ?>
    <div class="container bg-white p-4 rounded shadow-sm">
        <h3>Selecione até 8 Colunas para Exportar</h3>
        <form method="post" action="exportar_pdf.php?<?php echo htmlspecialchars(http_build_query($_GET)); ?>" onsubmit="return validateForm()"> <div class="row">
                <?php foreach ($colunas_disponiveis as $campo => $rotulo): ?> <div class="col-md-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                name="colunas[]" 
                                value="<?php echo $campo; ?>" 
                                id="col_<?php echo $campo; ?>"
                                <?php echo in_array($campo, $colunas_selecionadas ?: ['numero_processo','nome_segurado','patologias','alerta']) ? 'checked' : ''; ?>> <label class="form-check-label" for="col_<?php echo $campo; ?>"><?php echo $rotulo; ?></label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-3 border-top pt-3">
                <button type="button" class="btn btn-outline-secondary me-2" onclick="document.querySelectorAll('.form-check-input').forEach(c=>c.checked=false)">Desmarcar Todos</button> <button type="submit" class="btn btn-primary" name="client_pdf" value="1">Gerar PDF</button> <a href="exibir_dados.php?<?php echo htmlspecialchars(http_build_query($_GET)); ?>" class="btn btn-secondary">Voltar</a> </div>
        </form>
    </div>
    <script>
        function validateForm() { // Função JavaScript para validar a seleção de colunas.
            const checkedBoxes = document.querySelectorAll('.form-check-input:checked').length; // Conta quantos checkboxes estão marcados.
            if (checkedBoxes > 8) { // Verifica se mais de 8 colunas foram selecionadas.
                alert('Por favor, selecione no máximo 8 colunas.'); // Alerta o usuário.
                return false; // Impede o envio do formulário.
            }
            if (checkedBoxes === 0) { // Verifica se nenhuma coluna foi selecionada.
                alert('Por favor, selecione pelo menos 1 coluna.'); // Alerta o usuário.
                return false; // Impede o envio do formulário.
            }
            return true; // Permite o envio do formulário se a validação passar.
        }
    </script>
</body>
</html>
<?php
    exit; // Termina a execução do script após mostrar o formulário.
}

$colunas = $_POST['colunas']; // Pega as colunas selecionadas do POST.
$get_params['colunas'] = implode(',', $colunas); // Adiciona as colunas selecionadas aos parâmetros GET para uso em links de "voltar".

$sql_conditions = build_sql_conditions($conn, $get_params); // Constrói as condições SQL com base nos filtros.
$sql_where_clause = count($sql_conditions) > 0 ? " WHERE " . implode(" AND ", $sql_conditions) : ""; // Monta a cláusula WHERE da query SQL.

$sql_select_fields = "p.*, GROUP_CONCAT(DISTINCT pat.nome_patologia ORDER BY pat.nome_patologia SEPARATOR ', ') AS patologias";
$sql_from_clause = "pacientes p"; // Define a tabela principal.
$sql_join_clause = " LEFT JOIN paciente_patologia pp ON p.numero_processo = pp.numero_processo AND p.termo_adesao = pp.termo_adesao LEFT JOIN patologias pat ON pp.id_patologia = pat.id_patologia"; // Define os joins com outras tabelas.
$sql_group_by = " GROUP BY p.numero_processo, p.termo_adesao"; // Define o agrupamento para consolidar as patologias.
$sql = "SELECT $sql_select_fields FROM $sql_from_clause$sql_join_clause $sql_where_clause$sql_group_by ORDER BY p.data_inclusao DESC"; // Monta a query SQL completa.
$result = $conn->query($sql); // Executa a query no banco de dados.
if ($result === false) { // Verifica se a query falhou.
    die("Erro ao executar consulta SQL: " . $conn->error . "<br>SQL: " . $sql);
}
$pacientes = $result->fetch_all(MYSQLI_ASSOC); // Pega todos os resultados da query e armazena em um array.

// Lógica para expandir as patologias em linhas separadas, se a coluna "Patologias" for selecionada.
$dados_expandidos = []; // Inicializa um array para os dados expandidos.
$mostrar_patologias_em_linhas = in_array('patologias', $colunas); // Verifica se 'patologias' está entre as colunas selecionadas.

if ($mostrar_patologias_em_linhas) { // Se for para mostrar patologias em linhas separadas...
    foreach ($pacientes as $p) { // Percorre cada paciente.
        if (!empty($p['patologias'])) { // Se o campo de patologias não estiver vazio.
            $lista_patologias = array_map('trim', explode(',', $p['patologias'])); // Divide a string de patologias em um array.
            foreach ($lista_patologias as $pat) { // Para cada patologia na lista...
                $novo = $p; // Cria uma cópia dos dados do paciente.
                $novo['patologias'] = $pat; // Substitui a lista de patologias por apenas uma.
                $dados_expandidos[] = $novo; // Adiciona o novo registro ao array de dados expandidos.
            }
        } else { // Se não houver patologias...
            $dados_expandidos[] = $p; // Apenas adiciona o registro original.
        }
    }
    $pacientes = $dados_expandidos; // Substitui o array original de pacientes pelo expandido.
}

?>
<!doctype html> <html lang="pt-br">
<head>
    <meta charset="utf-8"><title>Visualizar Relatório</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body { margin: 0; padding: 0; }
        .table-container { max-height: 80vh; overflow-y: auto; } /* Define uma altura máxima e rolagem para a tabela. */
        .table thead th { position: sticky; top: 0; background-color: #fff; z-index: 10; } /* Faz o cabeçalho da tabela ficar fixo no topo durante a rolagem. */
    </style>
</head>
<body class="bg-light">
    <?php require_once '../header.php'; ?>
    <div class="container-fluid bg-white pt-0 pb-3 px-3 rounded shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4">Relatório de Pacientes (<?php echo count($pacientes); ?> registros)</h2> <div>
                <button id="baixar_pdf" class="btn btn-primary">Baixar PDF</button> <a href="exportar_pdf.php?<?php echo htmlspecialchars(http_build_query($get_params)); ?>" class="btn btn-secondary">Voltar e Mudar Colunas</a> </div>
        </div>
        <div class="table-container" id="report_container">
            <table class="table table-bordered table-striped table-hover"> <thead>
                    <tr><?php foreach($colunas as $c):?><th><?php echo htmlspecialchars($colunas_disponiveis[$c]??$c);?></th><?php endforeach;?></tr> </thead>
                <tbody>
                    <?php if(empty($pacientes)):?><tr><td colspan="<?php echo count($colunas);?>">Nenhum registro.</td></tr><?php endif;?> <?php foreach($pacientes as $p):?><tr><?php foreach($colunas as $c):?><td><?php // Inicia o loop para preencher as células da tabela.
                    if($c==='alerta'){$v=get_vencimento_status($p['data_inclusao'],$p['tempo_permanencia']);$cl=$v['class']??'bg-secondary';echo'<span class="badge '.$cl.'">'.htmlspecialchars($v['status']).'</span><br><small>'.htmlspecialchars($v['message']).'</small>';} // Lógica para exibir o status de alerta.
                    elseif(strpos($c,'data_')===0){echo htmlspecialchars(formatar_data($p[$c]??''));} // Formata o campo se for uma data.
                    elseif($c==='renda_paciente'){echo 'R$ '.htmlspecialchars(formatar_numero($p[$c]??''));} // Formata o campo se for a renda.
                    else{echo htmlspecialchars($p[$c]??'');} // Exibe o valor do campo como texto.
                    ?></td><?php endforeach;?></tr><?php endforeach;?> </tbody>
            </table>
        </div>
    </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script> <script>
document.getElementById('baixar_pdf').addEventListener('click', async function() { // Adiciona um evento de clique ao botão "Baixar PDF".
    const btn = this; // Armazena a referência do botão.
    const originalText = btn.innerHTML; // Salva o texto original do botão.
    btn.disabled = true; // Desabilita o botão para evitar cliques múltiplos.
    btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Gerando...`; // Altera o texto do botão para indicar processamento.

    const totalTableWidth = document.querySelector('#report_container table').getBoundingClientRect().width; // Mede a largura total da tabela visível na tela para usar como referência de 100%.

    const colWidthsPercent = []; // Inicializa um array para armazenar as larguras percentuais.
    document.querySelectorAll('#report_container thead th').forEach(th => { // Percorre cada célula do cabeçalho da tabela visível.
        const colWidth = th.getBoundingClientRect().width; // Mede a largura em pixels da célula do cabeçalho.
        colWidthsPercent.push((colWidth / totalTableWidth) * 100); // Calcula e armazena a largura como uma porcentagem do total.
    });

    const headerCellsHtml = colWidthsPercent.map((width, index) => { // Percorre o array de larguras percentuais.
        const thElement = document.querySelectorAll('#report_container thead th')[index]; // Pega o elemento th original correspondente.
        const thClone = thElement.cloneNode(true); // Cria um clone do elemento para não modificar a página atual.
        thClone.style.width = `${width}%`; // Aplica a largura em porcentagem ao clone como um estilo inline.
        return thClone.outerHTML; // Retorna o HTML completo da célula de cabeçalho com o estilo aplicado.
    }).join(''); // Junta todos os HTMLs das células do cabeçalho em uma única string.

    const bodyRowsHtml = Array.from(document.querySelectorAll('#report_container tbody tr')).map(tr => tr.outerHTML); // Captura o HTML de todas as linhas do corpo da tabela.
    const columnCount = colWidthsPercent.length; // Conta o número de colunas.
    const tempContainer = document.createElement('div'); // Cria um container temporário e invisível para cálculos.
    tempContainer.style.cssText = `position: absolute; top: -9999px; left: -9999px; width: 1200px; font-family: Arial, sans-serif; font-size: 9px;`; // Estiliza o container temporário.
    document.body.appendChild(tempContainer); // Adiciona o container ao corpo do documento.
    const tempTable = document.createElement('table'); // Cria uma tabela temporária dentro do container.
    tempTable.style.borderCollapse = 'collapse'; // Define o estilo da tabela temporária.
    tempTable.style.width = '100%'; // Define o estilo da tabela temporária.
    tempTable.innerHTML = `<thead><tr><th colspan="${columnCount}">Relatório</th></tr><tr>${headerCellsHtml}</tr></thead><tbody></tbody>`; // Preenche a tabela temporária.
    tempContainer.appendChild(tempTable); // Adiciona a tabela ao container temporário.
    const tbody = tempTable.querySelector('tbody'); // Seleciona o corpo da tabela temporária.
    const maxPageHeightMM = 150; // Define a altura máxima útil de uma página A4 paisagem em mm.
    const pxPerMM = 3.78; // Define um fator de conversão aproximado de pixels para milímetros.
    const maxPageHeightPX = maxPageHeightMM * pxPerMM; // Calcula a altura máxima da página em pixels.
    let currentHeight = tempTable.getBoundingClientRect().height; // Mede a altura atual da tabela temporária.
    let rowsPerPage = 0; // Inicializa o contador de linhas por página.
    for (let rowHtml of bodyRowsHtml) { // Loop para determinar quantas linhas cabem em uma página.
        tbody.insertAdjacentHTML('beforeend', rowHtml); // Adiciona uma linha à tabela temporária.
        currentHeight = tempTable.getBoundingClientRect().height; // Recalcula a altura.
        if (currentHeight > maxPageHeightPX) break; // Se a altura exceder o limite, para o loop.
        rowsPerPage++; // Incrementa o contador de linhas.
    }
    document.body.removeChild(tempContainer); // Remove o container temporário do DOM.
    rowsPerPage = Math.min(Math.max(rowsPerPage - 2, 5), 26); // Ajusta o número de linhas por página, com um mínimo de 5 e máximo de 26.
    const totalPages = Math.ceil(bodyRowsHtml.length / rowsPerPage); // Calcula o número total de páginas.
    let pageBlocks = []; // Inicializa um array para armazenar o HTML de cada página.

    for (let i = 0; i < bodyRowsHtml.length; i += rowsPerPage) { // Loop para criar os blocos de HTML para cada página.
        const pageNumber = (i / rowsPerPage) + 1; // Calcula o número da página atual.
        const pageRows = bodyRowsHtml.slice(i, i + rowsPerPage).join(''); // Pega as linhas de dados para a página atual.
        
        const pageBlock = ` <div style="font-family: Arial, sans-serif; font-size: 9px; width: 100%; margin-bottom: 5px;">
                <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom: 5px; margin-bottom: 5px;">
                    <span style="font-size:14px; font-weight:bold;">Relatório de Pacientes</span>
                    <span style="font-size:10px;">Página ${pageNumber} de ${totalPages}</span>
                </div>
            </div>
            <table>
                <thead>
                    <tr>${headerCellsHtml}</tr>
                </thead>
                <tbody>${pageRows}</tbody>
            </table>
            <div class="page-break"></div>
        `;
        pageBlocks.push(pageBlock); // Adiciona o bloco da página ao array.
    }

    const pdfHtml = ` <html><head><style>
        body { font-family: Arial, sans-serif; font-size: 9px; margin: 0; width: 100%; }
        
        table { 
            table-layout: fixed; /* Força a tabela a respeitar as larguras definidas no cabeçalho. */
            width: 100%; /* Faz a tabela ocupar toda a largura disponível. */
            border-collapse: separate; /* Usa bordas separadas para controle preciso. */
            border-spacing: 0; /* Remove o espaçamento entre as células. */
            page-break-inside: avoid; /* Tenta evitar que a tabela seja quebrada no meio de uma página. */
            margin-bottom: 10px; /* Adiciona uma margem inferior. */
            border-top: 1px solid #000; /* Define a borda superior da tabela. */
            border-left: 1px solid #000; /* Define a borda esquerda da tabela. */
        }

        th, td {
            box-sizing: border-box; /* Garante que a largura definida inclua padding e bordas. */
            border-right: 1px solid #000; /* Define a borda direita de cada célula. */
            border-bottom: 1px solid #000; /* Define a borda inferior de cada célula. */
            border-left: none; /* Remove a borda esquerda para evitar duplicação. */
            border-top: none;  /* Remove a borda superior para evitar duplicação. */
            padding: 4px; /* Adiciona espaçamento interno nas células. */
            text-align: center; /* Centraliza o texto horizontalmente. */
            vertical-align: middle; /* Centraliza o texto verticalmente. */
            -webkit-print-color-adjust: exact; /* Força a impressão das cores de fundo. */
            word-wrap: break-word; /* Permite a quebra de palavras longas para evitar estouro da célula. */
        }

        thead tr:first-child th { 
            background-color: #e9ecef; /* Define a cor de fundo para o cabeçalho. */
            font-weight: bold; /* Deixa o texto do cabeçalho em negrito. */
        }
        
        tbody tr:nth-child(even) { background-color: #f2f2f2; } /* Adiciona um fundo listrado (zebrado) nas linhas do corpo da tabela. */
        .badge { background-color: transparent !important; color: #000 !important; font-weight: normal; } /* Estiliza os badges de alerta para o PDF. */
        .page-break { page-break-after: always; } /* Força uma quebra de página após cada bloco de tabela. */
    </style></head><body>
        ${pageBlocks.join('')} </body></html>`;
    
    const opt = { // Define as opções de configuração para a geração do PDF.
        margin: [10, 25, 10, 25], // Margens [topo, esquerda, baixo, direita] em milímetros.
        filename: 'relatorio_pacientes_<?php echo date("Y-m-d"); ?>.pdf', // Define o nome do arquivo PDF, incluindo a data atual.
        image: { type: 'jpeg', quality: 1 }, // Define as opções para conversão de imagens.
        html2canvas: { scale: 2, useCORS: true }, // Define a escala de renderização do html2canvas para melhor qualidade de texto e imagens.
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' } // Define as opções do jsPDF: unidade, formato do papel e orientação.
    };

    html2pdf().from(pdfHtml).set(opt).save().then(() => { // Inicia a geração do PDF a partir do HTML com as opções definidas.
        btn.disabled = false; // Reabilita o botão após a geração do PDF.
        btn.innerHTML = originalText; // Restaura o texto original do botão.
    }).catch(err => { // Captura qualquer erro que ocorra durante a geração.
        console.error('Erro ao gerar PDF:', err); // Exibe o erro no console do navegador.
        btn.disabled = false; // Reabilita o botão em caso de erro.
        btn.innerHTML = originalText; // Restaura o texto original do botão.
        alert('Erro ao gerar o PDF.'); // Alerta o usuário sobre o erro.
    });
});
</script>
</body>
</html>