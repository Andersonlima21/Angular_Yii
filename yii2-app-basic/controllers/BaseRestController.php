<?php

namespace app\controllers;

use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\rest\OptionsAction;
use yii\web\Response;

// Centraliza os behaviors compartilhados por todos os controllers REST do projeto.
// Qualquer controller que extender esta classe herda CORS, JSON e VerbFilter automaticamente.
class BaseRestController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors = array_merge([
            'corsFilter' => [
                'class' => Cors::class,
                'cors'  => [
                    'Origin'                         => ['*'],
                    'Access-Control-Request-Method'  => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => null,
                    'Access-Control-Max-Age'         => 3600,
                ],
            ],
        ], $behaviors);

        $behaviors['contentNegotiator']['formats'] = [
            'application/json' => Response::FORMAT_JSON,
        ];

        $behaviors['verbs'] = [
            'class'   => VerbFilter::class,
            'actions' => [
                'index'   => ['GET'],
                'view'    => ['GET'],
                'create'  => ['POST'],
                'update'  => ['PUT', 'PATCH'],
                'delete'  => ['DELETE'],
                'options' => ['OPTIONS'],
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        return [
            'options' => [
                'class'             => OptionsAction::class,
                'collectionOptions' => ['GET', 'POST', 'OPTIONS'],
                'resourceOptions'   => ['GET', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
            ],
        ];
    }
}
