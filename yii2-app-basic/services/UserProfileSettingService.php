<?php

namespace app\services;

use app\models\UserProfileSetting;
use Throwable;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\web\ServerErrorHttpException;

class UserProfileSettingService
{
    /**
     * @throws ServerErrorHttpException
     */
    public function findAll(): array
    {
        try {
            $data = (new Query())
                ->from(UserProfileSetting::tableName())
                ->orderBy(['id' => SORT_ASC])
                ->all();

            if (empty($data)) {
                throw new ServerErrorHttpException('Nenhum setting encontrado!');
            }

            $retorno = [];
            foreach ($data as $item) {
                $retorno[] = $this->hydrate($item);
            }

            return $retorno;

        } catch (Throwable $e) {
            throw new ServerErrorHttpException('Falha na busca de settings. ' . $e->getMessage());
        }
    }

    /**
     * @throws ServerErrorHttpException
     */
    public function findById(int $id): array
    {
        try {
            $setting = (new Query())
                ->from(UserProfileSetting::tableName())
                ->where(['id' => $id])
                ->one();

            if (empty($setting)) {
                throw new \Exception('Nenhum setting encontrado para o id ' . $id);
            }

            return $this->hydrate($setting);

        } catch (Throwable $e) {
            throw new ServerErrorHttpException('Falha na busca do setting. ' . $e->getMessage());
        }
    }

    /**
     * Centraliza o cast de tipos vindos do SQLite (int/bool) para o array de saída.
     * notifications_enabled é boolean no SQLite mas chega como 0/1.
     */
    private function hydrate(array $row): array
    {
        return [
            'id'              => (int)$row['id'],
            'user_profile_id' => (int)$row['user_profile_id'],
            'platform'        => $row['platform'],
            'stack'           => $row['stack'],
            'certificate_url' => $row['certificate_url'] ?? null,
            'created_at'      => $row['created_at'],
            'updated_at'      => $row['updated_at'],
        ];
    }

    /**
     * Cria um ou vários settings de uma vez. Aceita 3 formatos no body:
     *
     *   1) Objeto único:
     *      { user_profile_id, theme, ... }
     *
     *   2) Header + lista (formato preferido para bulk no mesmo profile):
     *      { user_profile_id, settings: [ { theme, ... }, { theme, ... } ] }
     *      → o user_profile_id "de fora" é aplicado em cada item que não trouxer
     *        o seu próprio (item ganha precedência).
     *
     *   3) Lista top-level (cada item carrega o seu user_profile_id):
     *      [ { user_profile_id, ... }, { user_profile_id, ... } ]
     *
     * Tudo roda dentro de UMA transação: se um item falhar a validação ou o insert,
     * nada é gravado.
     *
     * @throws ServerErrorHttpException
     */
    public function create(array $body): array
    {
        // Formato 2: header + lista de settings.
        if (isset($body['settings']) && is_array($body['settings'])) {
            $shared = $body['user_profile_id'] ?? null;
            $rows = array_map(function ($item) use ($shared) {
                if (!is_array($item)) return $item;
                if ($shared !== null && !array_key_exists('user_profile_id', $item)) {
                    $item['user_profile_id'] = $shared;
                }
                return $item;
            }, array_values($body['settings']));
            $isBulk = true;

        // Formato 3: lista top-level.
        } elseif (array_key_exists(0, $body) && is_array($body[0])) {
            $rows   = array_values($body);
            $isBulk = true;

        // Formato 1: objeto único.
        } else {
            $rows   = [$body];
            $isBulk = false;
        }

        if (empty($rows)) {
            throw new ServerErrorHttpException('Body vazio: nenhum setting para criar.');
        }

        // Valida cada item antes de abrir transação — falha rápido com índice do item ruim.
        foreach ($rows as $i => $row) {
            $model = new UserProfileSetting();
            $model->setAttributes($row);
            if (!$model->validate()) {
                $errors = [];
                foreach ($model->getFirstErrors() as $field => $msg) {
                    $errors[] = "{$field}: {$msg}";
                }
                $prefix = $isBulk ? "[item #{$i}] " : '';
                throw new ServerErrorHttpException($prefix . implode(' | ', $errors));
            }
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $now     = new Expression("datetime('now')");
            $ids     = [];
            $command = Yii::$app->db->createCommand();

            foreach ($rows as $row) {
                $insert = [
                    'user_profile_id' => $row['user_profile_id'],
                    'platform'        => $row['platform'],
                    'stack'           => $row['stack'],
                    'certificate_url' => $row['certificate_url'] ?? null,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];

                $command->insert(UserProfileSetting::tableName(), $insert)->execute();
                $ids[] = (int)Yii::$app->db->getLastInsertID();
            }

            $transaction->commit();

            return count($ids) === 1
                ? ['id' => $ids[0], 'message' => 'Setting criado com sucesso.']
                : ['ids' => $ids, 'message' => count($ids) . ' settings criados com sucesso.'];

        } catch (Throwable $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('Falha na criação dos settings. ' . $e->getMessage());
        }
    }

    /**
     * Atualização parcial: campos não enviados preservam o valor atual.
     * user_profile_id NÃO é atualizável — para mover um setting de profile,
     * delete e recrie.
     *
     * @throws ServerErrorHttpException
     */
    public function update(int $id, array $body): string
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $existing = (new Query())
                ->from(UserProfileSetting::tableName())
                ->where(['id' => $id])
                ->one();

            if (empty($existing)) {
                throw new \Exception('Nenhum setting encontrado para o id ' . $id);
            }

            $update = [
                'platform'        => array_key_exists('platform', $body)        ? $body['platform']        : $existing['platform'],
                'stack'           => array_key_exists('stack', $body)           ? $body['stack']           : $existing['stack'],
                'certificate_url' => array_key_exists('certificate_url', $body) ? $body['certificate_url'] : $existing['certificate_url'],
                'updated_at'      => date('Y-m-d H:i:s'),
            ];

            $rows = Yii::$app->db->createCommand()
                ->update(UserProfileSetting::tableName(), $update, ['id' => $id])
                ->execute();

            $transaction->commit();

            return "Setting #{$id} atualizado com sucesso! ({$rows} linha(s) afetada(s))";

        } catch (Throwable $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('Falha na atualização do setting. ' . $e->getMessage());
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
                ->from(UserProfileSetting::tableName())
                ->where(['id' => $id])
                ->exists();

            if (!$exists) {
                throw new \Exception('Nenhum setting encontrado para o id ' . $id);
            }

            Yii::$app->db->createCommand()
                ->delete(UserProfileSetting::tableName(), ['id' => $id])
                ->execute();

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('Falha na remoção do setting. ' . $e->getMessage());
        }
    }
}
