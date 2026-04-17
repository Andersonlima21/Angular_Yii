<?php

namespace app\controllers;

use app\models\UserApi;
use app\services\UserService;
use Throwable;
use Yii;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\rest\OptionsAction;
use yii\web\Response;

class UserApiController extends Controller
{
    private UserService $service;

    public function __construct($id, $module, UserService $service, $config = [])
    {
        $this->service = $service;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors = array_merge([
            'corsFilter' => [
                'class' => Cors::class,
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => null,
                    'Access-Control-Max-Age' => 3600,
                ],
            ],
        ], $behaviors);
        $behaviors['contentNegotiator']['formats'] = [
            'application/json' => Response::FORMAT_JSON,
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
                'view' => ['GET'],
                'create' => ['POST'],
                'update' => ['PUT', 'PATCH'],
                'delete' => ['DELETE'],
                'toggle-active' => ['PATCH', 'OPTIONS'],
                'options' => ['OPTIONS'],
            ],
        ];
        return $behaviors;
    }

    public function actions()
    {
        return [
            'options' => [
                'class' => OptionsAction::class,
                'collectionOptions' => ['GET', 'POST', 'OPTIONS'],
                'resourceOptions' => ['GET', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
            ],
        ];
    }

    public function actionIndex(): array
    {
        try {
            $filtros = Yii::$app->request->getQueryParams();
            $data = $this->service->findAll($filtros);

            return ['success' => true, 'type' => 'success', 'data' => $data];
        } catch (Throwable $e) {
            Yii::$app->response->statusCode = 400;
            return ['success' => false, 'type' => 'exception', 'message' => $e->getMessage()];
        }
    }

    public function actionView(int $id): array
    {
        try {
            return [
                'success' => true,
                'type' => 'success',
                'data' => $this->service->findById($id)
//                'data' => $this->service->findById_new($id)
            ];
        } catch (Throwable $e) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'type' => 'exception',
                'message' => $e->getMessage()];
        }
    }

    public function actionCreate(): array
    {
        try {
            $body = Yii::$app->request->getBodyParams();

            // Instancia o model só pra validar usando as rules() já definidas
            $user = new UserApi();
            $user->setAttributes($body);

            if (!$user->validate()) {
                $errors = [];
                foreach ($user->getFirstErrors() as $field => $msg) {
                    $errors[] = "{$field}: {$msg}";
                }
                throw new \Exception(implode(' | ', $errors));
            }

            $created = $this->service->create($body);

            Yii::$app->response->statusCode = 201;
            return [
                'success' => true,
                'type' => 'success',
                'data' => $created,
            ];
        } catch (\Throwable $e) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'type' => 'exception',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionUpdate(int $id): array
    {
        try {
            $body = Yii::$app->request->getBodyParams();

            // Valida os campos vindos no body usando as rules() do model.
            // 'update' é um scenario nativo que ignora a unicidade do próprio registro.
            $user = new UserApi();
            $user->setAttributes($body);

            if (!$user->validate()) {
                $errors = [];
                foreach ($user->getFirstErrors() as $field => $msg) {
                    $errors[] = "{$field}: {$msg}";
                }
                throw new \Exception(implode(' | ', $errors));
            }

            $message = $this->service->update($id, $body);

            Yii::$app->response->statusCode = 200;
            return [
                'success' => true,
                'type' => 'success',
                'data' => $message,
            ];
        } catch (\Throwable $e) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'type' => 'exception',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionUpdate_old(int $id)
    {
        $user = $this->service->update($id, Yii::$app->request->getBodyParams());
        if ($user->hasErrors()) {
            Yii::$app->response->statusCode = 422;
            return ['errors' => $user->getErrors()];
        }
        return $user;
    }

    public function actionToggleActive(int $id): array
    {
        try {
            $result = $this->service->toggleActive($id);
            return [
                'success' => true,
                'type' => 'success',
                'data' => $result,
            ];
        } catch (Throwable $e) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'type' => 'exception',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionDelete(int $id)
    {
        $this->service->delete($id);
        Yii::$app->response->statusCode = 204;
    }
}
