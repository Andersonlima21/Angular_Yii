<?php

namespace app\services;

use app\models\UserApi;
use Throwable;
use Yii;
use yii\db\Exception;
use yii\db\Expression;
use yii\db\Query;
use yii\web\ServerErrorHttpException;

class UserService
{
    private UserConfigService $configService;
    private UserProfileService $profileService;
    private UserFilterHelper $filterHelper;

    public function __construct(UserConfigService $configService, UserProfileService $profileService, UserFilterHelper $filterHelper)
    {
        $this->configService = $configService;
        $this->profileService = $profileService;
        $this->filterHelper = $filterHelper;
    }

    public function findAll(array $filtros = []): array
    {
        try {
            $data = (new Query())
                ->from([UserApi::tableName()])
                ->orderBy(['id' => SORT_ASC])
                ->all();

            if (empty($data)) throw new Exception('Não foram encontrados registros na tabela de users');

            $retorno = [];
            foreach ($data as $item) {
                $retorno[] = [
                    'id' => (int)$item['id'],
                    'name' => $item['name'],
                    'email' => $item['email'],
                    'is_active' => (bool)$item['is_active'],
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['updated_at'],
                ];
            }

            if (!empty($filtros)) {
                $retorno = $this->filterHelper->handleArray($retorno, $filtros);
            }

            return $retorno;

        } catch (Throwable $e) {
            throw new ServerErrorHttpException('Falha na listagem de users. ' . $e->getMessage());
        }
    }

    /**
     * @throws ServerErrorHttpException
     */
    public function findById(int $id): array
    {
        try {
            $user = (new Query())
                ->select(['id', 'name', 'email', 'is_active', 'created_at', 'updated_at'])
                ->from(UserApi::tableName())
                ->where(['id' => $id])
                ->one();

            if (empty($user)) throw new Exception('Nenhum usuário encontrado para o id ' . $id);

            // Antes estava capturando as configs em uma query aqui. (Conceito de refatoração)
            $configs = $this->configService->findAll($id);

            // capturar os settings de profiles também, dentro do profiles.
            $profiles = $this->profileService->findAll($id);

            return [
                'id' => (int)$user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'ativo' => (bool)$user['is_active'],
                'created_at' => $user['created_at'],
                'updated_at' => $user['updated_at'],
                'configs' => $configs,
                'profiles' => $profiles,
            ];

        } catch (Throwable $e) {
            throw new ServerErrorHttpException('Falha na busca do usuário. ' . $e->getMessage());
        }
    }

    // (Conceito refatoração)
    // Não necessito mais deste metodo, pois já resolvi com os anteriormente existentes.
    // Melhor explicado em UserConfigService
//    private function getProfilesByUserId(int $userId): array
//    {
//        try {
//            $data = (new Query())
//                ->select(['phone', 'birth_date', 'bio', 'avatar_url', 'created_at', 'updated_at'])
//                ->from(UserProfile::tableName())
//                ->where(['user_id' => $userId])
//                ->orderBy(['id' => SORT_ASC])
//                ->all();
//
//            if (empty($data)) return [];
//
//            return $data;
//
//        } catch (Throwable $e) {
//            throw new ServerErrorHttpException('Falha na busca dos perfis do usuário. ' . $e->getMessage());
//        }
//    }

    /**
     * @throws ServerErrorHttpException
     */
    public function create(array $body): string
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {

            $now = new Expression("datetime('now')"); // no eloquent usava DB(raw'NOW()') do Facades - laravel

            $insert = [
                'name' => $body['name'],
                'email' => $body['email'],
                'created_at' => $now,
                'updated_at' => $now
            ];

            Yii::$app->db->createCommand()
                ->insert('users', $insert)
                ->execute();

            $transaction->commit();

            return 'Usuário ' . $body['name'] . ' cadastrado com sucesso!';

        } catch (Throwable $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('Falha na criação do user. ' . $e->getMessage());
        }
    }

    public function update(int $id, array $body): string
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $exists = (new Query())
                ->from('users')
                ->where(['id' => $id])
                ->exists();

            if (!$exists) throw new Exception('Nenhum usuário encontrado para o id ' . $id);

            $update = [
                'name' => $body['name'],
                'email' => $body['email'],
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $rows = Yii::$app->db->createCommand()
                ->update('users', $update, ['id' => $id])
                ->execute();

            $transaction->commit();

            return "Usuário #{$id} atualizado com sucesso! ({$rows} linha(s) afetada(s))";

        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('Falha na atualização do user. ' . $e->getMessage());
        }
    }

    public function toggleActive(int $id): array
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = (new Query())
                ->select(['id', 'is_active'])
                ->from(UserApi::tableName())
                ->where(['id' => $id])
                ->one();

            if (empty($user)) throw new Exception('Nenhum usuário encontrado para o id ' . $id);

            $newStatus = !(bool)$user['is_active'];

            Yii::$app->db->createCommand()
                ->update(UserApi::tableName(), [
                    'is_active' => (int)$newStatus,
                    'updated_at' => date('Y-m-d H:i:s'),
                ], ['id' => $id])
                ->execute();

            $transaction->commit();

            return [
                'id' => (int)$user['id'],
                'is_active' => $newStatus,
                'message' => 'Usuário ' . ($newStatus ? 'ativado' : 'inativado') . ' com sucesso.',
            ];

        } catch (Throwable $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('Falha ao alterar status do usuário. ' . $e->getMessage());
        }
    }

    public function delete(int $id): void
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $exists = (new Query())
                ->from(UserApi::tableName())
                ->where(['id' => $id])
                ->exists();

            if (!$exists) throw new Exception('Nenhum usuário encontrado para o id ' . $id);

            Yii::$app->db->createCommand()
                ->delete(UserApi::tableName(), ['id' => $id])
                ->execute();

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('Falha na remoção do usuário. ' . $e->getMessage());
        }
    }
}
