<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'sOHmQV4nWmr945SNAYoVqLAqWkuV3B5z',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,

        // =========================================================================
        // urlManager: traduz URL <-> "route" (string no formato "controller/action")
        // =========================================================================
        // IMPORTANTE: o urlManager NÃO chama o controller. Ele só converte
        // a URL em texto. Quem pega esse texto e instancia a classe é o Yii
        // (via Yii::createController), seguindo uma convenção de nomes.
        //
        // Fluxo completo de uma requisição:
        //
        //   POST /user-api
        //         │
        //         ▼
        //   urlManager compara a URL com cada 'rule' até achar match
        //         │
        //         ▼
        //   Resultado: route = "user-api/create"  (só uma string!)
        //         │
        //         ▼
        //   Yii quebra no "/" em controller-id e action-id:
        //     "user-api"  -> kebab-case vira CamelCase -> "UserApi"
        //                 -> adiciona sufixo            -> "UserApiController"
        //                 -> namespace padrão           -> app\controllers\UserApiController
        //                 -> arquivo                    -> controllers/UserApiController.php
        //
        //     "create"    -> kebab-case vira CamelCase -> "Create"
        //                 -> adiciona prefixo           -> "actionCreate"
        //         │
        //         ▼
        //   new UserApiController(...)    <- DI injeta UserService no construtor
        //   $controller->actionCreate()   <- Reflection preenche os argumentos
        //                                    (ex: <id:\d+> da URL vira $id no método)
        //         │
        //         ▼
        //   Retorno -> ContentNegotiator serializa como JSON -> resposta HTTP
        // =========================================================================
        'urlManager' => [
            // true  -> URLs amigáveis: /user-api/1
            // false -> URLs por query string: /index.php?r=user-api/view&id=1
            'enablePrettyUrl' => true,

            // true  -> só aceita URLs listadas em 'rules' (404 nas outras)
            // false -> se nenhuma rule casar, tenta roteamento automático
            //          por convenção /<controller>/<action> (ex: /site/index)
            'enableStrictParsing' => false,

            // Remove "index.php" da URL exibida.
            'showScriptName' => false,

            // -----------------------------------------------------------------
            // 'rules': lista de regras avaliadas na ORDEM declarada.
            // Primeira que casar com (verbo + URL) ganha.
            //
            // Dois formatos podem aparecer aqui:
            //   1) String curta:  'VERBO caminho' => 'controller-id/action-id'
            //                     (use quando a action NÃO for uma das 5 REST padrão)
            //   2) Classe:        ['class' => 'yii\rest\UrlRule', ...]
            //                     (gera automaticamente as 5 rotas REST padrão)
            // -----------------------------------------------------------------
            'rules' => [

                // Rota explícita para benchmark — precisa vir ANTES do UrlRule
                // de user-api, senão o UrlRule captura "v2" como :id e dá 404
                // (o regex de id é \d+).
                'GET user-api/v2' => 'user-api/index-v2',

                // Actions fora do padrão REST: precisam vir antes do UrlRule.
                // OPTIONS é necessário para o preflight CORS do Angular.
                'PATCH   user-api/<id:\d+>/toggle-active'   => 'user-api/toggle-active',
                'OPTIONS user-api/<id:\d+>/toggle-active'   => 'user-api/options',

                // -------------------------------------------------------------
                // "Fábrica de rules" do REST.
                // yii\rest\UrlRule NÃO é UMA rota: é uma classe que, no boot
                // do urlManager, EXPANDE sozinha em várias rules equivalentes.
                //
                // As 5 actions do UserApiController são mapeadas assim:
                //
                //   GET    /user-api         -> user-api/index    -> actionIndex()
                //   GET    /user-api/<id>    -> user-api/view     -> actionView($id)
                //   POST   /user-api         -> user-api/create   -> actionCreate()
                //   PUT    /user-api/<id>    -> user-api/update   -> actionUpdate($id)
                //   PATCH  /user-api/<id>    -> user-api/update   -> actionUpdate($id)
                //   DELETE /user-api/<id>    -> user-api/delete   -> actionDelete($id)
                //
                // Nenhuma dessas linhas está escrita explicitamente — o UrlRule
                // as gera em memória quando a app inicia.
                //
                // Parâmetros:
                //   'controller'  -> id do controller em kebab-case.
                //                    Yii converte para UserApiController
                //                    (Camel + sufixo Controller + namespace app\controllers).
                //                    Aceita array para expor vários recursos:
                //                    'controller' => ['user-api', 'post', 'comment']
                //
                //   'pluralize'   -> false: URL fica /user-api
                //                    true : URL fica /user-apis (padrão do Yii)
                //
                //   'tokens'      -> placeholders com regex usados dentro do UrlRule.
                //                    {id} -> <id:\d+>  (só aceita dígitos).
                //                    O valor capturado é injetado como $id no método.
                //
                // OBS: Se futuramente você criar uma action fora do padrão REST
                // (ex: actionArchive, actionDuplicate, actionIndexNew...), precisa
                // adicionar uma linha no FORMATO 1 antes/depois deste UrlRule:
                //
                //   'POST user-api/<id:\d+>/archive' => 'user-api/archive',
                // -------------------------------------------------------------
                ['class' => 'yii\rest\UrlRule', 'controller' => 'user-api', 'pluralize' => false, 'tokens' => ['{id}' => '<id:\\d+>']],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'user-config', 'pluralize' => false, 'tokens' => ['{id}' => '<id:\\d+>']],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'user-profile', 'pluralize' => false, 'tokens' => ['{id}' => '<id:\\d+>']],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'user-profile-setting', 'pluralize' => false, 'tokens' => ['{id}' => '<id:\\d+>']],
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
