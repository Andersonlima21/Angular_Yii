# Apresentação do Projeto — Kleber

---

## Ordem sugerida

1. Apresentação rápida da aplicação
2. PHP / Yii2
3. JavaScript / Angular / Bootstrap
4. Claude Code — produtividade com IA
5. GitKraken
6. Curva de aprendizado / conceitos novos

---

## 1. Visão geral da aplicação

Apresentar a aplicação de forma rápida e objetiva: o que ela faz, como está dividida (backend Yii2 + frontend AngularJS) e qual foi o objetivo do estudo.

---

## 2. PHP / Yii2

### 2.1 UserFilterHelper — catálogo de funções de array

O `UserFilterHelper` foi construído como um catálogo intencional de funções de array. Cada bloco usa uma função diferente:

| Função | O que fez no projeto |
|---|---|
| `array_filter` | Filtrar usuários por status ativo/inativo e por busca textual |
| `array_map` | Transformar todos os e-mails (remover domínio) e projetar campos |
| `array_reduce` | Acumular contagem de ativos e inativos num único valor |
| `array_reverse` | Inverter a ordem da lista |
| `array_chunk` | Dividir a lista em grupos de N |
| `array_slice` | Simular paginação sem tocar no SQL |
| `array_splice` | Limitar resultados modificando o array diretamente (in-place) |
| `array_column` | Extrair uma coluna inteira como array plano |
| `array_intersect_key` | Projeção de campos — retornar só o que foi pedido |
| `array_fill_keys` | Montar a máscara de campos para o intersect |
| `usort + strcmp` | Ordenar por nome A→Z ou Z→A |
| `usort + <=>` | Ordenar por qualquer campo, qualquer direção |
| `stripos` | Busca case-insensitive sem precisar de SQL LIKE |

> **Ideia central:** o SQL traz tudo, o PHP filtra. Intencional para estudar as funções — em produção, parte disso iria para o banco.

---

### 2.2 Yii2 — três conceitos principais

**1. Dois estilos de acesso ao banco (comparação intencional)**

- **Query Builder** → SQL explícito, mais controle, menos memória -- Testes realizados em Perfil de monitoramento
- **ActiveRecord** → mais expressivo, mais abstrato, mais memória -- Testes realizados em Perfil de monitoramento

---

### 2.3 Padrões de exceção e retorno de resposta

**Catch-and-rethrow (Service)**

O service captura qualquer erro (`Throwable`) e relança como exceção HTTP específica do Yii2, adicionando contexto à mensagem.

```php
// No service
try {
    if (empty($user)) throw new Exception('Nenhum usuário encontrado para o id ' . $id);
    // ...
} catch (Throwable $e) {
    throw new ServerErrorHttpException('Falha na busca do usuário. ' . $e->getMessage());
}
```

```php
  Por que dois catchs resolvem:

  } catch (HttpException $e) {
      // Só entra aqui se for uma exception HTTP — ela JÁ sabe o status correto.
      // NotFoundHttpException  → $e->statusCode = 404
      // ServerErrorHttpException → $e->statusCode = 500
      Yii::$app->response->statusCode = $e->statusCode; // respeita o que foi definido
      return ['success' => false, 'message' => $e->getMessage()];

  } catch (Throwable $e) {
      // Só entra aqui se for algo inesperado: PDOException, TypeError, etc.
      // Esses não têm statusCode, então 400 faz sentido como fallback.
      Yii::$app->response->statusCode = 400;
      return ['success' => false, 'message' => $e->getMessage()];
  }
```


> **Por que `Throwable` e não `Exception`?**
> `Throwable` captura tanto `Exception` quanto `Error` (erros fatais do PHP como `TypeError`, `ParseError`). É a rede mais larga possível.

**Catch-and-respond (Controller)**

O controller captura o que o service lançou e transforma em resposta JSON — nunca deixa o erro chegar cru ao cliente.

```php
// No controller
try {
    $data = $this->service->findAll($filtros);
    return ['success' => true, 'type' => 'success', 'data' => $data];
} catch (Throwable $e) {
    Yii::$app->response->statusCode = 400;
    return ['success' => false, 'type' => 'exception', 'message' => $e->getMessage()];
}
```

**Validate-then-throw (Controller antes de delegar ao service)**

Validação via `Model::validate()` — se inválido, lança `\Exception` com as mensagens concatenadas. O `catch` acima já trata.

```php
if (!$user->validate()) {
    $errors = [];
    foreach ($user->getFirstErrors() as $field => $msg) {
        $errors[] = "{$field}: {$msg}";
    }
    throw new \Exception(implode(' | ', $errors));
}
```

---

### 2.4 Refatoração de código PHP

**Decomposição de Serviços (Service Decomposition)**

Conceito que gosto de aplicar: extrair responsabilidades para classes dedicadas.

**Prós**
- Extração de responsabilidade para classes dedicadas (SRP — Single Responsibility Principle)
- Centraliza a lógica de cada domínio, facilitando manutenção e reuso
- Facilita escalabilidade: caso seja necessário mudar um comportamento de forma global, atuamos em apenas 1 ponto focal

**Contras**
- Pode criar acoplamento excessivo entre serviços se mal delimitado
- Services orquestradores (`UserService`) podem ficar sobrecarregados se acumularem muitas dependências — é necessário cuidado para não sair criando diversas classes sem critério (violando o próprio SRP)

---

### 2.5 XDebug — Perfis de monitoramento

Adicionar um breakpoint em algum endpoint e ir depurando até o final do mesmo.

- Apresentar o `php.ini` e explicar o `XDEBUG_TRIGGER`, definindo valores personalizados para ter controle do debugging
- **Geração do perfil de monitoramento:** pasta `yii2-app-basic/runtime/xdebug`
- Capturar e importar o perfil no **Analyze Xdebug Profile Snapshot** do PHPStorm

> Ainda preciso me aprofundar para entender melhor o que cada coisa está consumindo de memória — mas o conceito já está funcional.

---

## 3. JavaScript / Angular / Bootstrap

> *(Apresentar os componentes, filtros e diretivas implementados no frontend AngularJS)*

---

## 4. Como uso o Claude para aumentar a produtividade

> Ainda sou iniciante no Claude, mas vim de estudos que propuseram estratégias interessantes que ajudam no desenvolvimento do código.

### Passo a passo

**1. Estrutura inicial com o Claude**

Com o projeto já criado (ou mesmo antes), abro o prompt do Claude e peço toda a estrutura inicial — tanto do projeto quanto do próprio Claude. Gosto de centralizar tudo em uma pasta `/ai` dentro do projeto.

**2. A pasta `/ai` — o "cérebro" do projeto**

Essa pasta pode variar de projeto para projeto, mas algumas coisas eu sempre mantenho:

```
ai/
│
├── README.md          → porta de entrada — Claude lê esse primeiro em toda sessão
├── context-pack.md    → regras congeladas do projeto (stack, padrões, anti-padrões)
├── governance.md      → protocolo de como mudar uma regra congelada
├── FOLDER-GUIDE.md    → explica o que cada pasta faz
│
├── agents/            → os "papéis" que o Claude pode assumir
│                        ex: backend-developer, debugger, qa-engineer, tech-lead...
│
├── standards/         → padrões de código por camada
│   └── backend/       → regras específicas do Yii2
│                        controllers.md, services.md...
│
├── docs/              → documentação de cada endpoint da API
│
├── workflows/         → passo a passo de processos
│                        como entregar uma feature, como testar, o que fazer após um bug
│
├── templates/         → modelos em branco para criar documentos novos
│                        plano de feature, postmortem, ADR (decisão arquitetural)
│
├── skills/            → receitas executáveis para tarefas repetitivas
│
├── plans/             → planos de features em andamento
│   └── quick/2026-04/ → planos rápidos do mês atual
│
├── backlogs/          → ideias que ainda não viraram plano
├── postmortems/       → análise de bugs ou entregas problemáticas
└── evaluation/        → checklists de aprovação de código/plano
```

> Em uma frase: `ai/` é o cérebro do projeto — tudo que o Claude precisa saber para trabalhar sem que eu precise explicar do zero a cada sessão.

**3. Criação de planos e execução**

Tenho uma estratégia de observabilidade para que o Claude não aplique o que ele bem entender no código. Para isso, crio os planos, acompanho o que ele vai fazer, o que ele fez, e realizo alterações quando necessário. Cada tarefa tem seus próprios checks, para que saibamos o que de fato aconteceu.

- Pasta separada para **quick plans** (coisas que exigem menos esforço)
- Planos organizados por **mês/ano** para manter histórico

**4. Preflight do plano**

Peço ao Claude para reler o plano criado e fazer um checklist de preflight, respeitando as regras "congeladas" e os antipadrões do projeto atual. Esse preflight verifica se o plano está de acordo com os padrões definidos para aquele projeto.

**5. Execução do plano**

Etapa que exige atenção: é sempre interessante acompanhar cada tarefa concluída e validar que o Claude não vai se perder criando coisas fora do padrão estabelecido.

**6. Verificação exaustiva**

Varredura do repositório inteiro (não só dos arquivos do plano) para encontrar todas as violações de uma vez, corrigir em lote, e só dar como concluído depois que lint/typecheck/testes/build passarem e os greps voltarem zerados.

**7. Skill Delta Check**

Decide o que virou padrão reutilizável nessa feature e encaixa cada coisa no menor lugar possível (`context-pack`, `standards` ou `workflows`), criando uma skill nova somente se passar nos critérios de reuso.

---

## 5. GitKraken — ferramenta de produtividade com Git

O GitKraken me ajuda muito no dia a dia com Git, pois disponibiliza uma interface gráfica e interativa que executa os comandos por baixo dos panos — liberando o tempo que o dev gastaria fazendo tudo via linha de comando.

**Principais recursos que uso:**

- **GitFlow integrado:** configura automaticamente `feature/`, `release/`, `hotfix/` e, ao finalizar cada fluxo, já dispara o merge nas branches corretas
- **Resolução de merge conflitante:** abre um monitor com as duas versões comparadas lado a lado, com checkboxes para manter ou descartar cada mudança. É possível ainda adicionar ou remover linhas específicas
- **Gerador de mensagem de commit com IA:** a IA lê os arquivos alterados, entende o que foi feito e cria título e corpo do commit automaticamente — mantendo uma timeline de commits auto-explicativa
- **Agentes no repositório** *(em estudo)*: o GitKraken está sendo atualizado para transportar agentes criados diretamente no repositório — ainda estou acompanhando como isso vai funcionar na prática

---

## 6. Curva de aprendizado — conceitos novos

### Rotas no Yii2

Estava muito acostumado com o Laravel, onde as definições de rotas são mais explícitas. No Yii2 o roteamento é mais implícito e configurado por regras no `config/web.php`, o que exigiu uma adaptação.

### Monitoramento com XDebug

Não estava habituado a avaliar além do tempo de resposta dos endpoints. O conceito de criação de perfis de monitoramento e a avaliação de memória alocada por funcionalidade me pareceu muito interessante. Pretendo me aprofundar mais nisso para dar ainda mais leveza e credibilidade aos projetos.

### Angular vs React

Não diria que foi uma dificuldade, mas sei que preciso de reforços pontuais em Angular — o framework que mais utilizei em toda a minha carreira foi React, e ainda continuo estudando sobre ele. Gosto muito do conceito de componentização, que é muito forte no React.

### Sintaxe de query — Yii2 vs Laravel

No Laravel eu usava bastante o `Facades DB`, que disponibilizava diversas facilidades. No Yii2 a sintaxe é diferente, e isso foi um ponto de adaptação:

**Query Builder — Yii2:**
```php
$data = (new Query())
    ->from([UserApi::tableName()])
    ->orderBy(['id' => SORT_ASC])
    ->all();
```

**Facades DB — Laravel:**
```php
$data = DB::table('user_api')
    ->orderBy('id', 'asc')
    ->get()
    ->toArray();
```

Algumas coisas são mais verbosas no Yii, diferentes do Laravel:

```php
// Yii2
$now = new Expression("datetime('now')");

// Laravel
$now = DB::raw('NOW()');
```

> O mesmo vale para abertura de conexões, commits e rollbacks.
