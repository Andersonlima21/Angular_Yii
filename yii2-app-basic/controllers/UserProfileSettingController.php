<?php

namespace app\controllers;

use app\models\UserProfileSetting;
use app\services\UserProfileSettingService;
use Throwable;
use Yii;

class UserProfileSettingController extends BaseRestController
{
    private UserProfileSettingService $service;

    public function __construct($id, $module, UserProfileSettingService $service, $config = [])
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
            // O service trata tanto objeto único quanto lista (bulk) e faz
            // a validação por item, então o controller só repassa o body.
            $body = Yii::$app->request->getBodyParams();
            $created = $this->service->create($body);

            Yii::$app->response->statusCode = 201;
            return [
                'success' => true,
                'type'    => 'success',
                'data'    => $created,
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

    public function actionUpdate(int $id): array
    {
        try {
            $body = Yii::$app->request->getBodyParams();

            // Carrega o AR existente para que user_profile_id não seja obrigatório no body
            // e para que o exist validator ignore o próprio registro.
            $setting = UserProfileSetting::findOne($id);
            if ($setting === null) {
                throw new \Exception('Nenhum setting encontrado para o id ' . $id);
            }

            $setting->setAttributes($body);

            if (!$setting->validate()) {
                $errors = [];
                foreach ($setting->getFirstErrors() as $field => $msg) {
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
        } catch (Throwable $e) {
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
