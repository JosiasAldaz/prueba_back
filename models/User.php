<?php

namespace app\models;

use yii\mongodb\ActiveRecord;
use yii\web\IdentityInterface;
use Yii;

class User extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return ['Prueba_tecnica','usuarios']; 
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return ['_id', 'username', 'password_hash', 'auth_key'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password_hash'], 'required'],
            [['auth_key'], 'string', 'max' => 32],
        ];
    }

    /**
     * Encuentra una un usuario por el ID de usuario.
     * @param string|int $id El ID de usuario.
     * @return IdentityInterface|null Resultado de la búsqueda.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Encuentra una al usuario por el token de autenticación.
     * @param string $token El token de autenticación.
     * @param mixed $type El tipo de token.
     * @return IdentityInterface|null La identidad que coincide con el token de autenticación.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $authComponent = new \app\components\AuthComponent();
        $decoded = $authComponent->validateToken($token);
        if ($decoded) {
            return static::findOne($decoded['userId']);
        }
        return null;
    }

    //busca un usuario por su nombre de usuario
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    
    public function getId()
    {
        return (string)$this->_id;
    }

    
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    //valida la contraseña
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    //establece la contraseña
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Genera una clave de autenticación "recuerdame".
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
}
