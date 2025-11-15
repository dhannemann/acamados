<?php
require_once '../header.php';
require_once '../config.php';
require_once __DIR__ . '/filtros.php'; 

// Lógica de Paginação e Filtros
$resultados_por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? $_GET['pagina'] : 1;
$sql_conditions = build_sql_conditions($conn, $_GET);

// Determina se o JOIN é necessário
$requires_patologia_join = false;
foreach ($sql_conditions as $condition) {
    if (strpos($condition, 'pp.id_patologia') !== false) {
        $requires_patologia_join = true;
        break;
    }
}

// Constrói as cláusulas SQL
$sql_select_fields = "p.numero_processo, p.termo_adesao, p.nome_segurado, p.data_inclusao, p.tempo_permanencia, p.data_remocao";
$sql_from_clause = "pacientes p";
$sql_join_clause = "";
$sql_group_by = "";

if ($requires_patologia_join) {
    $sql_join_clause = " JOIN paciente_patologia pp ON p.numero_processo = pp.numero_processo AND p.termo_adesao = pp.termo_adesao";
    // O GROUP BY é necessário para evitar linhas duplicadas ao fazer o JOIN na patologia
    $sql_group_by = " GROUP BY p.numero_processo, p.termo_adesao";
}

$sql_where_clause = count($sql_conditions) > 0 ? " WHERE " . implode(" AND ", $sql_conditions) : "";
$filtro_titulo = count($sql_conditions) > 0 ? "Resultados da Busca" : "Todos os Pacientes Cadastrados";

// --- 1. Consulta para contar o total de resultados ---
$sql_count = "SELECT COUNT(DISTINCT p.numero_processo, p.termo_adesao) AS total FROM pacientes p" . $sql_join_clause . $sql_where_clause;
$resultado_count = $conn->query($sql_count);
// Verifica se a consulta foi bem sucedida antes de buscar o resultado
if ($resultado_count === false) {
    die("Erro ao contar resultados: " . $conn->error);
}
$total_resultados = $resultado_count->fetch_assoc()['total'];
$total_paginas = ceil($total_resultados / $resultados_por_pagina);
$offset = ($pagina_atual - 1) * $resultados_por_pagina;

// --- 2. Consulta SQL principal com paginação ---
$sql = "SELECT " . $sql_select_fields . " FROM " . $sql_from_clause . $sql_join_clause . $sql_where_clause . $sql_group_by . " ORDER BY p.data_inclusao DESC LIMIT $resultados_por_pagina OFFSET $offset";
$result = $conn->query($sql);
if ($result === false) {
    die("Erro ao executar consulta principal: " . $conn->error . "<br>SQL: " . $sql);
}
$pacientes = $result->fetch_all(MYSQLI_ASSOC);

// Parâmetros para a paginação
$filtro_params = $_GET;
unset($filtro_params['pagina']);
$filtro_query = http_build_query($filtro_params);

// Carrega lista de patologias para o formulário de filtros
$patologias_list = [];
$result_pat = $conn->query("SELECT id_patologia, nome_patologia FROM patologias ORDER BY nome_patologia ASC");
if ($result_pat) {
    while ($row = $result_pat->fetch_assoc()) {
        $patologias_list[] = $row;
    }
}
?>

<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-12 text-center mb-4">
            <h1>Filtrar e Exibir Pacientes</h1>
        </div>
    </div>
    
    <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensagem_sucesso']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['mensagem_sucesso']); ?>
    <?php endif; ?>

    <?php 
    // Inclui o formulário de filtros
    include '_form_filtros.php'; 
    ?>

    <h2 class="text-center mb-4 mt-5"><?php echo $filtro_titulo; ?></h2>

    <?php 
    // Inclui a tabela de resultados e a paginação
    include '_tabela_resultados.php'; 
    ?>
</div>

<script src="https://unpkg.com/imask@6.0.7/dist/imask.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Script para máscaras de data
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

        // Rola a página para o final se a paginação for usada
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('pagina')) {
            window.scrollTo({
                top: document.body.scrollHeight,
                behavior: 'smooth'
            });
        }
    });
</script>

<?php require_once '../footer.php'; ?>