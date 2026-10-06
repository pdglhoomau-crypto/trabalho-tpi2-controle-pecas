<?php
// Endpoint JSON separado do HTML. Nenhum dado é persistido neste momento.
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
function responder(int $status, array $dados): void {
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(405, ['mensagem' => 'Utilize POST.']);
$schema = json_decode(file_get_contents(__DIR__ . '/../assets/schema.json'), true, 512, JSON_THROW_ON_ERROR);
$entidade = $_GET['entidade'] ?? '';
if (!is_string($entidade) || !isset($schema[$entidade])) responder(404, ['mensagem' => 'Formulário desconhecido.']);
$dados = []; $erros = [];
foreach ($schema[$entidade][1] as [$campo, $rotulo, $tipo]) {
    $valor = $_POST[$campo] ?? '';
    if (!is_string($valor)) { $erros[] = "$rotulo: valor inválido."; continue; }
    $valor = trim($valor);
    if ($valor === '' || strlen($valor) > 600) { $erros[] = "$rotulo: preencha corretamente."; continue; }
    if ($tipo === 'email' && !filter_var($valor, FILTER_VALIDATE_EMAIL)) $erros[] = "$rotulo: e-mail inválido.";
    if ($tipo === 'tel' && !preg_match('/^\d{10,11}$/', preg_replace('/\D/', '', $valor))) $erros[] = "$rotulo: informe 10 ou 11 dígitos.";
    if ($tipo === 'date') {
        $data = DateTimeImmutable::createFromFormat('!Y-m-d', $valor);
        if (!$data || $data->format('Y-m-d') !== $valor) $erros[] = "$rotulo: data inválida.";
    }
    if ($tipo === 'number') {
        $numero = filter_var($valor, FILTER_VALIDATE_FLOAT);
        $inteiro = !in_array($campo, ['preco', 'desconto'], true);
        $minimo = str_ends_with($campo, '_id') || ($campo === 'quantidade' && $entidade === 'itens_venda') ? 1 : 0;
        if ($numero === false || !is_finite((float)$numero) || $numero < $minimo || ($inteiro && floor((float)$numero) !== (float)$numero) || ($campo === 'desconto' && $numero > 100)) $erros[] = "$rotulo: número fora do intervalo permitido.";
    }
    $dados[$campo] = $valor;
}
if (isset($dados['documento']) && !preg_match('/^(\d{11}|\d{14})$/', $dados['documento'])) $erros[] = 'CPF/CNPJ: informe 11 ou 14 dígitos.';
if (isset($dados['cnpj']) && !preg_match('/^\d{14}$/', $dados['cnpj'])) $erros[] = 'CNPJ: informe 14 dígitos.';
if ($entidade === 'usuarios') {
    if (isset($dados['perfil']) && !in_array(strtolower($dados['perfil']), ['vendedor', 'administrador'], true)) $erros[] = 'Perfil: vendedor ou administrador.';
    if (isset($dados['ativo']) && !in_array(strtolower($dados['ativo']), ['sim', 'não', 'nao'], true)) $erros[] = 'Ativo: sim ou não.';
}
if ($erros) responder(422, ['erros' => $erros]);
function moeda(float $valor): string { return 'R$ ' . number_format($valor, 2, ',', '.'); }
switch ($entidade) {
    case 'pecas': $resultado = 'Valor do estoque informado: ' . moeda((float)$dados['preco'] * (int)$dados['quantidade']); break;
    case 'clientes': $resultado = 'Tipo de cliente: ' . (strlen($dados['documento']) === 11 ? 'Pessoa física' : 'Pessoa jurídica'); break;
    case 'fornecedores': $resultado = 'Identificação: ' . $dados['razao_social'] . ' - ' . $dados['cidade']; break;
    case 'categorias': $resultado = 'Ponto de alerta de reposição: ' . $dados['estoque_minimo'] . ' unidades. Local: ' . $dados['setor'] . ' / ' . $dados['localizacao']; break;
    case 'vendas': $resultado = 'Percentual a pagar após o desconto: ' . (100 - (float)$dados['desconto']) . '%. O total depende dos itens da venda.'; break;
    case 'itens_venda': $resultado = 'Subtotal com desconto: ' . moeda((int)$dados['quantidade'] * (float)$dados['preco'] * (1 - (float)$dados['desconto'] / 100)); break;
    case 'usuarios': $resultado = 'Acesso planejado: ' . (strtolower($dados['ativo']) === 'sim' ? strtolower($dados['perfil']) : 'inativo'); break;
}
responder(200, ['mensagem' => 'Dados validados. Nenhum registro foi gravado.', 'resultado' => $resultado]);
