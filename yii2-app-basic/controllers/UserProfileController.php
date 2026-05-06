<?php

namespace app\controllers;

use app\models\UserProfile;
use app\services\UserProfileService;
use Throwable;
use Yii;

class UserProfileController extends BaseRestController
{
    private UserProfileService $service;

    public function __construct($id, $module, UserProfileService $service, $config = [])
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

            $userProfile = new UserProfile();
            $userProfile->setAttributes($body);

            if (!$userProfile->validate()) {
                $errors = [];
                foreach ($userProfile->getFirstErrors() as $field => $msg) {
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

            // Carrega o AR existente para que a unique de user_id ignore o próprio registro.
            $userProfile = UserProfile::findOne($id);
            if ($userProfile === null) {
                throw new \Exception('Nenhum perfil encontrado para o id ' . $id);
            }

            $userProfile->setAttributes($body);

            if (!$userProfile->validate()) {
                $errors = [];
                foreach ($userProfile->getFirstErrors() as $field => $msg) {
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
