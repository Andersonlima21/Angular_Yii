<?php

namespace app\services;

use app\models\UserConfig;
use Throwable;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\web\ServerErrorHttpException;

class UserConfigService
{
    // (Conceito de refatoração)
    // Conceito de refactor -- Anteriormente eu tinha criado todos os cruds com cada metodo,
    // mas vi que eu preciso usar o getId de configs dentro do user,
    // então decidi trasformar esse metodo para que ele consiga ser usado em outros lugares que tenham um user e precisem das configs,
    // criando um parametro nao obrigatorio, mas que que quando venha eu faço um filterWhere que caso userId null o filtro é ignorado.
    /**
     * @throws ServerErrorHttpException
     */
    public function findAll(?int $userId = null): array
    {
        try {
            $data = (new Query())
                ->from(UserConfig::tableName())
                ->filterWhere(['user_id' => $userId])
                ->orderBy(['id' => SORT_ASC])
                ->all();

            // (Conceito de Exception)
            // tratamento de return, caso não venha o userId, eu retorno a exception para que quando
            // o metodo seja chamado de forma individual, ele tenha um retorno amigavel.
            // caso contrario, retorna um array vazio para o metodo que o chama.
            if (empty($data)) {
                if ($userId !== null) {
                    return [];
                }
                throw new ServerErrorHttpException('Nenhuma configuração encontrada!');
            }

            $retorno = [];
            foreach ($data as $item) {
                $retorno[] = [
                    'id' => (int)$item['id'],
                    'user_id' => (int)$item['user_id'],
                    'key' => $item['key'],
                    'value' => $item['value'],
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['updated_at'],
                ];
            }

            return $retorno;

        } catch (Throwable $e) {
            throw new ServerErrorHttpException('Falha na busca de configs. ' . $e->getMessage());
        }
    }

    /**
     * Busca uma config pelo id. Se $userId vier, restringe ao dono — útil para
     * evitar que um endpoint de "config do user X" retorne a config de outro user.
     *
     * @throws ServerErrorHttpException
     */
    public function findById(int $id, ?int $userId = null): array
    {
        try {
            $config = (new Query())
                ->from(UserConfig::tableName())
                ->where(['id' => $id])
                ->andFilterWhere(['user_id' => $userId])
                ->one();

            if (empty($config)) {
                throw new \Exception('Nenhuma configuração encontrada para o id ' . $id);
            }

            return [
                'id'         => (int)$config['id'],
                'user_id'    => (int)$config['user_id'],
                'key'        => $config['key'],
                'value'      => $config['value'],
                'created_at' => $config['created_at'],
                'updated_at' => $config['updated_at'],
            ];
        } catch (Throwable $e) {
            throw new ServerErrorHttpException('Falha na busca de config. ' . $e->getMessage());
        }
    }

    /**
     * @throws ServerErrorHttpException
     */
    public function create(array $body): string
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // A unicidade é composta (user_id + key), igual ao índice da migration
            // e à rule do model UserConfig. Bloqueia só se existir a mesma key
            // para o MESMO usuário; outros usuários podem ter a mesma key.
            $duplicate = (new Query())
                ->from(UserConfig::tableName())
                ->where([
                    'user_id' => $body['user_id'],
                    'key'     => $body['key'],
                ])
                ->exists();

            if ($duplicate) {
                throw new ServerErrorHttpException(
                    'Já existe uma config com a key "' . $body['key'] .
                    '" para o user_id ' . $body['user_id'] . '.'
                );
            }

            $now = new Expression("datetime('now')");

            $insert = [
                'user_id'    => $body['user_id'],
                'key'        => $body['key'],
                'value'      => $body['value'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            Yii::$app->db->createCommand()
                ->insert(UserConfig::tableName(), $insert)
                ->execute();

            $transaction->commit();

            return 'Config para o user id ' . $body['user_id'] . ' criado com sucesso';

        } catch (Throwable $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('Falha na criação do user. ' . $e->getMessage());
        }
    }

    /**
     * @throws ServerErrorHttpException
     */
    public function update(int $id, array $body): string
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $existing = (new Query())
                ->from(UserConfig::tableName())
                ->where(['id' => $id])
                ->one();

            if (empty($existing)) {
                throw new \Exception('Nenhuma configuração encontrada para o id ' . $id);
            }

            $newKey = $body['key'] ?? $existing['key'];

            // Se a key mudou, revalida a unicidade composta (user_id + key)
            // ignorando o próprio registro.
            if ($newKey !== $existing['key']) {
                $duplicate = (new Query())
                    ->from(UserConfig::tableName())
                    ->where([
                        'user_id' => $existing['user_id'],
                        'key'     => $newKey,
                    ])
                    ->andWhere(['<>', 'id', $id])
                    ->exists();

                if ($duplicate) {
                    throw new \Exception(
                        'Já existe uma config com a key "' . $newKey .
                        '" para este usuário.'
                    );
                }
            }

            $update = [
                'key'        => $newKey,
                'value'      => array_key_exists('value', $body) ? $body['value'] : $existing['value'],
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $rows = Yii::$app->db->createCommand()
                ->update(UserConfig::tableName(), $update, ['id' => $id])
                ->execute();

            $transaction->commit();

            return "Config #{$id} atualizada com sucesso! ({$rows} linha(s) afetada(s))";

        } catch (Throwable $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('Falha na atualização da config. ' . $e->getMessage());
        }
    }

    /**
     * @throws ServerErrorHttpException
     */
    public function delete(int $id): void
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $exists = (new Query())
                ->from(UserConfig::tableName())
                ->where(['id' => $id])
                ->exists();

            if (!$exists) {
                throw new \Exception('Nenhuma configuração encontrada para o id ' . $id);
            }

            Yii::$app->db->createCommand()
                ->delete(UserConfig::tableName(), ['id' => $id])
                ->execute();

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('Falha na remoção da config. ' . $e->getMessage());
        }
    }
}
