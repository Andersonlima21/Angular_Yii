<?php

namespace app\services;

class UserFilterHelper
{

    public function handleArray(array $users, array $filtros): array
    {
        // array_filter — percorre o array e mantém somente os elementos onde o callback
        // retorna true. Não altera o original; retorna um novo array (reindexado com array_values).
        if (!empty($filtros['status'])) {
            $ativo = $filtros['status'] === 'ativo';
            $users = array_values(
                array_filter($users, fn($u) => (bool)$u['is_active'] === $ativo)
            );
        }

        // usort — reordena o array in-place usando um comparador customizado.
        // strcmp devolve negativo, zero ou positivo comparando as strings caractere a caractere;
        // invertendo os operandos (b vs a) conseguimos a ordem Z→A sem precisar negar o resultado.
        if (!empty($filtros['ordenacao'])) {
            usort($users, fn($a, $b) => $filtros['ordenacao'] === 'az'
                ? strcmp($a['name'], $b['name'])
                : strcmp($b['name'], $a['name'])
            );
        }

        // array_reverse — devolve um novo array com os elementos na ordem inversa.
        // Preserva as chaves associativas do array original; aqui usamos array numérico,
        // então o efeito prático é simplesmente virar a lista de cabeça para baixo.
        if (!empty($filtros['inverter'])) {
            $users = array_reverse($users);
        }

        // array_map — transforma cada elemento aplicando o mesmo callback a todos.
        // Diferente do array_filter (que remove), o map sempre devolve um array
        // do mesmo tamanho — apenas com os valores alterados. Aqui usamos strstr
        // com terceiro argumento true para pegar tudo antes do '@', ocultando o domínio.
        if (!empty($filtros['sem_dominio'])) {
            $users = array_map(function ($u) {
                $u['email'] = strstr($u['email'], '@', true);
                return $u;
            }, $users);
        }

        // array_reduce — percorre o array acumulando um único valor de retorno.
        // Diferente dos outros filtros, muda o shape da resposta: devolve contagem, não lista.
        if (!empty($filtros['resumo'])) {
            $contagem = array_reduce($users, function ($carry, $u) {
                $u['is_active'] ? $carry['ativos']++ : $carry['inativos']++;
                return $carry;
            }, ['ativos' => 0, 'inativos' => 0]);
            return [
                ['status' => 'Ativos', 'total' => $contagem['ativos']],
                ['status' => 'Inativos', 'total' => $contagem['inativos']],
            ];
        }

        // array_chunk — divide o array em grupos de tamanho $n.
        // Retorna um array de arrays; o front renderiza uma tabela por grupo.
        if (!empty($filtros['chunk'])) {
            return array_chunk($users, max(1, (int)$filtros['chunk']));
        }

        // array_slice — extrai uma fatia do array a partir de um offset(índice inicial do corte),
        // sem alterar o original. O offset é calculado como (página - 1) × porPagina:
        // página 1 começa no offset(0), página 2 no offset(porPagina), e assim por diante —
        // simulando paginação server-side sem precisar de LIMIT/OFFSET(pular N linhas) no SQL.
        if (!empty($filtros['pagina'])) {
            $porPagina = max(1, (int)($filtros['por_pagina'] ?? 3));
            $offset = (max(1, (int)$filtros['pagina']) - 1) * $porPagina;
            $users = array_slice($users, $offset, $porPagina);
        }

        // array_filter + stripos — busca textual case-insensitive por substring.
        // stripos retorna a posição (pode ser 0, que é falsy!) ou false;
        // por isso a comparação obrigatória é !== false, nunca empty() ou !.
        // Pesquisa simultânea em 'name' e 'email': basta um dos campos conter o termo.
        if (!empty($filtros['busca'])) {
            $termo = $filtros['busca'];
            $users = array_values(
                array_filter($users, fn($u) => stripos($u['name'], $termo) !== false ||
                    stripos($u['email'], $termo) !== false
                )
            );
        }

        // array_intersect_key + array_fill_keys — projeção de campos (field projection).
        // array_fill_keys transforma ['name', 'email'] em ['name' => true, 'email' => true],
        // servindo de "máscara". array_intersect_key mantém apenas as chaves presentes
        // nessa máscara — o chamador decide quais campos receber sem conhecer a estrutura interna.
        if (!empty($filtros['campos']) && is_array($filtros['campos'])) {
            $mascara = array_fill_keys($filtros['campos'], true);
            $users = array_map(fn($u) => array_intersect_key($u, $mascara), $users);
        }

        // usort com spaceship operator (<=>) e campo dinâmico — generaliza a ordenação
        // para qualquer campo do array sem precisar de um if por campo.
        // Spaceship retorna -1, 0 ou 1 comparando dois valores (funciona com int, float e string).
        // Multiplicar pelo $direcao (1 ou -1) inverte a ordem sem duplicar o comparador.
        if (!empty($filtros['ordenar_por'])) {
            $campo = $filtros['ordenar_por'];
            $direcao = (($filtros['direcao'] ?? 'asc') === 'desc') ? -1 : 1;
            usort($users, function ($a, $b) use ($campo, $direcao) {
                if (!array_key_exists($campo, $a)) return 0;
                return ($a[$campo] <=> $b[$campo]) * $direcao;
            });
        }

        // array_column — extrai uma única coluna de um array multidimensional como array plano.
        // Útil quando o front precisa apenas de uma lista de ids ou nomes para um select/autocomplete,
        // sem carregar todos os campos. O segundo argumento é a coluna-valor;
        // o terceiro (não usado aqui) reindexaria o resultado por outra coluna.
        if (!empty($filtros['coluna'])) {
            return array_column($users, $filtros['coluna']);
        }

        // array_splice — remove elementos do array in-place a partir de um offset.
        // Diferente do array_slice (que não altera o original e retorna a fatia),
        // o splice modifica diretamente o array passado por referência e descarta o excedente.
        // Aqui impõe um teto de resultados independente de paginação — útil para previews.
        if (!empty($filtros['limite'])) {
            $n = max(1, (int)$filtros['limite']);
            if (count($users) > $n) {
                array_splice($users, $n);
            }
        }

        return $users;
    }
}
