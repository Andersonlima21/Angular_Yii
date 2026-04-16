<?php

namespace app\services;

use app\models\UserApi;
use Throwable;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Versão alternativa do UserService usando ActiveRecord do Yii.
 *
 * Em standby: não está plugada em nenhum controller. Existe apenas como
 * referência do "jeito simplificado" (equivalente ao Eloquent do Laravel),
 * em contraste ao UserService que usa Query Builder / createCommand.
 *
 * Para usar, trocar o type-hint no construtor do UserApiController de
 * UserService para UserServiceSimplified — o DI do Yii resolve sozinho.
 */
class UserServiceSimplified
{
    /**
     * Equivale a: User::all() no Eloquent.
     */
    public function findAll(): array
    {
        try {
            return UserApi::find()
                ->orderBy(['id' => SORT_ASC])
                ->all();
        } catch (Throwable $e) {
            throw new ServerErrorHttpException('Falha na listagem de users. ' . $e->getMessage());
        }
    }

    /**
     * Equivale a: User::findOrFail($id) no Eloquent.
     */
    public function findById(int $id): UserApi
    {
        $user = UserApi::findOne($id);

        if ($user === null) {
            throw new NotFoundHttpException('Nenhum usuário encontrado para o id ' . $id);
        }

        return $user;
    }

    /**
     * Equivale a: User::create($data) no Eloquent.
     * O TimestampBehavior no model UserApi preenche created_at / updated_at.
     */
    public function create(array $body): UserApi
    {
        $user = new UserApi();
        $user->setAttributes($body);

        if (!$user->save()) {
            throw new ServerErrorHttpException('Falha na criação do user. ' . json_encode($user->getFirstErrors()));
        }

        return $user;
    }

    /**
     * Equivale a: $user->update($data) no Eloquent.
     */
    public function update(int $id, array $body): UserApi
    {
        $user = $this->findById($id);
        $user->setAttributes($body);

        if (!$user->save()) {
            throw new ServerErrorHttpException('Falha na atualização do user. ' . json_encode($user->getFirstErrors()));
        }

        return $user;
    }

    /**
     * Equivale a: $user->delete() no Eloquent.
     */
    public function delete(int $id): void
    {
        $this->findById($id)->delete();
    }
}
