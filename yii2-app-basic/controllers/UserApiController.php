<?php

namespace app\controllers;

use app\models\UserApi;
use app\services\UserService;
use Throwable;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class UserApiController extends BaseRestController
{
    private UserService $service;

    public function __construct($id, $module, UserService $service, $config = [])
    {
        $this->service = $service;
        parent::__construct($id, $module, $config);
    }

    // Adiciona o verbo do toggle-active aos behaviors herdados da base.
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs']['actions']['toggle-active'] = ['PATCH', 'OPTIONS'];
        return $behaviors;
    }

    public function actionIndex(): array
    {
        try {
            $filtros = Yii::$app->request->getQueryParams();
            $data = $this->service->findAll($filtros);

            return ['success' => true, 'type' => 'success', 'data' => $data];
        }
//        catch (HttpException $e) {
//            // Usa o statusCode da própria exception (404, 500, etc.) em vez de forçar 400.
//            Yii::$app->response->statusCode = $e->statusCode;
//            return ['success' => false, 'type' => 'exception', 'message' => $e->getMessage()];
//        }
        catch (Throwable $e) {
            Yii::$app->response->statusCode = 500;
            return ['success' => false, 'type' => 'exception', 'message' => $e->getMessage()];
        }
    }

    public function actionView(int $id): array
    {
        try {
            return [
                'success' => true,
                'type' => 'success',
                'data' => $this->service->findById($id),
            ];
        } catch (HttpException $e) {
            Yii::$app->response->statusCode = $e->statusCode;
            return [
                'success' => false,
                'type' => 'exception',
                'message' => $e->getMessage(),
            ];
        } catch (Throwable $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'success' => false,
                'type' => 'exception',
                'message' => $e->getMessage(),
            ];
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
                // BadRequestHttpException = HTTP 400. Dado inválido enviado pelo cliente.
                throw new BadRequestHttpException(implode(' | ', $errors));
            }

            $created = $this->service->create($body);

            Yii::$app->response->statusCode = 201;
            return [
                'success' => true,
                'type' => 'success',
                'data' => $created,
            ];
        } catch (HttpException $e) {
            Yii::$app->response->statusCode = $e->statusCode;
            return [
                'success' => false,
                'type' => 'exception',
                'message' => $e->getMessage(),
            ];
        } catch (Throwable $e) {
            Yii::$app->response->statusCode = 500;
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

            $camposValidos = array_intersect_key($body, array_flip(['name', 'email']));
            if (empty($camposValidos)) {
                throw new BadRequestHttpException('Envie ao menos um campo para atualizar: name, email.');
            }

            // findOne em vez de new UserApi() para que o validator unique exclua o próprio registro.
            // Com isNewRecord = false, o Yii adiciona automaticamente "AND id != <id>" na query de unicidade.
            $user = UserApi::findOne($id);
            if (!$user) {
                throw new NotFoundHttpException("Usuário #{$id} não encontrado.");
            }

            $user->scenario = 'update';
            $user->setAttributes($body);

            if (!$user->validate()) {
                $errors = [];
                foreach ($user->getFirstErrors() as $field => $msg) {
                    $errors[] = "{$field}: {$msg}";
                }
                throw new BadRequestHttpException(implode(' | ', $errors));
            }

            $message = $this->service->update($id, $body);

            Yii::$app->response->statusCode = 200;
            return [
                'success' => true,
                'type' => 'success',
                'data' => $message,
            ];
        } catch (HttpException $e) {
            Yii::$app->response->statusCode = $e->statusCode;
            return [
                'success' => false,
                'type' => 'exception',
                'message' => $e->getMessage(),
            ];
        } catch (Throwable $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'success' => false,
                'type' => 'exception',
                'message' => $e->getMessage(),
            ];
        }
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
        } catch (HttpException $e) {
            Yii::$app->response->statusCode = $e->statusCode;
            return [
                'success' => false,
                'type' => 'exception',
                'message' => $e->getMessage(),
            ];
        } catch (Throwable $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'success' => false,
                'type' => 'exception',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionDelete(int $id): ?array
    {
        try {
            $this->service->delete($id);
            Yii::$app->response->statusCode = 204;
            return null;
        } catch (HttpException $e) {
            Yii::$app->response->statusCode = $e->statusCode;
            return [
                'success' => false,
                'type' => 'exception',
                'message' => $e->getMessage(),
            ];
        } catch (Throwable $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'success' => false,
                'type' => 'exception',
                'message' => $e->getMessage(),
            ];
        }
    }
}
