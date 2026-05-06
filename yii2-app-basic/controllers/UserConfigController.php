<?php

namespace app\controllers;

use app\models\UserConfig;
use app\services\UserConfigService;
use Throwable;
use Yii;

class UserConfigController extends BaseRestController
{
    private UserConfigService $service;

    public function __construct($id, $module, UserConfigService $service, $config = [])
    {
        $this->service = $service;
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): array
    {
        try {
            return [
                'success' => true,
                'type'    => 'success',
                'data'    => $this->service->findAll(),
            ];
        } catch (Throwable $e) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'type'    => 'exception',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionView(int $id): array
    {
        try {
            return [
                'success' => true,
                'type'    => 'success',
                'data'    => $this->service->findById($id),
            ];
        } catch (Throwable $e) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'type'    => 'exception',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionCreate(): array
    {
        try {
            $body = Yii::$app->request->getBodyParams();

            $userConfig = new UserConfig();
            $userConfig->setAttributes($body);

            if (!$userConfig->validate()) {
                $errors = [];
                foreach ($userConfig->getFirstErrors() as $field => $msg) {
                    $errors[] = "{$field}: {$msg}";
                }
                throw new \Exception(implode(' | ', $errors));
            }

            $created = $this->service->create($body);

            Yii::$app->response->statusCode = 201;
            return [
                'success' => true,
                'type'    => 'success',
                'data'    => $created,
            ];
        } catch (\Throwable $e) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'type'    => 'exception',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionUpdate(int $id): array
    {
        try {
            $body = Yii::$app->request->getBodyParams();

            // Carrega o AR já existente para validar:
            // assim a unique composta (user_id, key) ignora o próprio registro.
            $existing = UserConfig::findOne($id);
            if ($existing === null) {
                throw new \Exception('Nenhuma configuração encontrada para o id ' . $id);
            }

            $existing->setAttributes($body);

            if (!$existing->validate()) {
                $errors = [];
                foreach ($existing->getFirstErrors() as $field => $msg) {
                    $errors[] = "{$field}: {$msg}";
                }
                throw new \Exception(implode(' | ', $errors));
            }

            $message = $this->service->update($id, $body);

            Yii::$app->response->statusCode = 200;
            return [
                'success' => true,
                'type'    => 'success',
                'data'    => $message,
            ];
        } catch (\Throwable $e) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'type'    => 'exception',
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
