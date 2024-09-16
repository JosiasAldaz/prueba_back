<?php

namespace app\models;

use yii\mongodb\ActiveRecord;

class Autor extends ActiveRecord
{
    public static function collectionName()
    {
        return 'autores';
    }

    public function attributes()
    {
        return ['_id', 'nombre', 'fecha_nacimiento', 'libros_escritos'];
    }

    public function rules()
    {
        return [
            [['nombre', 'fecha_nacimiento'], 'required', 'message' => 'El campo {attribute} es obligatorio.'],
            [['libros_escritos'], 'safe'],
            ['fecha_nacimiento', 'date', 'format' => 'php:Y-m-d', 'message' => 'La fecha de nacimiento debe ser una fecha válida en el formato YYYY-MM-DD.'],
            ['nombre', 'validateNombre'],
        ];        
    }

    public function getLibrosEscritos()
    {
        return $this->hasMany(Libro::className(), ['_id' => 'libro_id'])->via('librosEscritosRelacion');
    }

    public function messages()
    {
        return [
            'fecha_nacimiento.date' => 'El formato de la fecha de nacimiento no es válido',
            'nombre.number' => 'El no nombre puede incluir números',
        ];
    }

    public function validateNombre($attribute, $params) {
        $messages = $this->messages();
        // Evitar números, permitir letras con tildes y espacios
        if (!is_string($this->$attribute) || !preg_match('/^[\p{L}\s]+$/u', $this->$attribute)) {
            $this->addError($attribute, $messages['nombre.number']);
        } elseif (strlen($this->$attribute) > 255) {
            $this->addError($attribute, $messages['nombre.max']);
        }
    }

}

