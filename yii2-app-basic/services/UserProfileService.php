<?php

namespace app\services;

use app\models\UserProfile;
use Throwable;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\web\ServerErrorHttpException;

class UserProfileService
{
    /**
     * @throws ServerErrorHttpException
     */
    public function findAll(?int $userId = null): array
    {
        try {
            $data = (new Query())
                ->from(UserProfile::tableName())
                ->filterWhere(['user_id' => $userId])
                ->orderBy(['id' => SORT_ASC])
                ->all();

            if (empty($data)) {
                if ($userId !== null) {
                    return [];
                }
                throw new ServerErrorHttpException('Nenhuma perfil encontrado!');
            }

            foreach ($data as $item) {
                $retorno[] = [
                    'id' => (int)$item['id'],
                    'user_id' => (int)$item['user_id'],
                    'phone' => $item['phone'],
                    'birth_date' => $item['birth_date'],
                    'bio' => $item['bio'],
                    'avatar_url' => $item['avatar_url'],
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['updated_at'],
                ];
            }

            return $retorno;

        } catch (Throwable $e) {
            throw new ServerErrorHttpException('Falha na busca de perfis. ' . $e->getMessage());
        }
    }

    /**
     * @throws ServerErrorHttpException
     */
    public function findById(int $id): array
    {
        try {
            $profile = (new Query())
                ->from(UserProfile::tableName())
                ->where(['id' => $id])
                ->one();

            if (empty($profile)) {
                throw new \Exception('Nenhum perfil encontrado para o id ' . $id);
            }

            return [
                'id'         => (int)$profile['id'],
                'user_id'    => (int)$profile['user_id'],
                'phone'      => $profile['phone'],
                'birth_date' => $profile['birth_date'],
                'bio'        => $profile['bio'],
                'avatar_url' => $profile['avatar_url'],
                'created_at' => $profile['created_at'],
                'updated_at' => $profile['updated_at'],
            ];
        } catch (Throwable $e) {
            throw new ServerErrorHttpException('Falha na busca do perfil. ' . $e->getMessage());
        }
    }

    /**
     * @throws ServerErrorHttpException
     */
    public function create(array $body)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $now = new Expression("datetime('now')");

            $insert = [
                'user_id' => $body['user_id'],
                'phone' => $body['phone'],
                'birth_date' => $body['birth_date'],
                'bio' => $body['bio'],
                'avatar_url' => $body['avatar_url'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            Yii::$app->db->createCommand()
                ->insert(UserProfile::tableName(), $insert)
                ->execute();

            $transaction->commit();

            return 'Perfil criado para o usuário de id ' . $body['user_id'] . '!';

        } catch (Throwable $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('Falha na criação do user. ' . $e->getMessage());
        }
    }

    /**
     * Atualização parcial: campos não enviados no body preservam o valor atual.
     * user_id NÃO é atualizável (relação 1:1 com users + unique).
     *
     * @throws ServerErrorHttpException
     */
    public function update(int $id, array $body): string
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $existing = (new Query())
                ->from(UserProfile::tableName())
                ->where(['id' => $id])
                ->one();

            if (empty($existing)) {
                throw new \Exception('Nenhum perfil encontrado para o id ' . $id);
            }

            $update = [
                'phone'      => array_key_exists('phone', $body)      ? $body['phone']      : $existing['phone'],
                'birth_date' => array_key_exists('birth_date', $body) ? $body['birth_date'] : $existing['birth_date'],
                'bio'        => array_key_exists('bio', $body)        ? $body['bio']        : $existing['bio'],
                'avatar_url' => array_key_exists('avatar_url', $body) ? $body['avatar_url'] : $existing['avatar_url'],
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $rows = Yii::$app->db->createCommand()
                ->update(UserProfile::tableName(), $update, ['id' => $id])
                ->execute();

            $transaction->commit();

            return "Perfil #{$id} atualizado com sucesso! ({$rows} linha(s) afetada(s))";

        } catch (Throwable $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('Falha na atualização do perfil. ' . $e->getMessage());
        }
    }

    /**
     * Remove o perfil. O setting associado some via FK ON DELETE CASCADE.
     *
     * @throws ServerErrorHttpException
     */
    public function delete(int $id): void
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $exists = (new Query())
                ->from(UserProfile::tableName())
                ->where(['id' => $id])
                ->exists();

            if (!$exists) {
                throw new \Exception('Nenhum perfil encontrado para o id ' . $id);
            }

            Yii::$app->db->createCommand()
                ->delete(UserProfile::tableName(), ['id' => $id])
                ->execute();

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('Falha na remoção do perfil. ' . $e->getMessage());
        }
    }
}
