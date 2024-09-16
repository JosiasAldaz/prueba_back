<?php

namespace app\models;

use yii\mongodb\ActiveRecord;

class Libros extends ActiveRecord
{
    const PATCH_EDIT = 'update';

    public static function collectionName()
    {
        return 'libros';
    }

    public function attributes()
    {
        return ['_id', 'titulo', 'autores', 'anio_publicacion', 'descripcion'];
    }

    public function rules()
    {
        return [
            [['titulo', 'anio_publicacion', 'descripcion'], 'required', 'message' => 'El campo {attribute} es obligatorio.'],
            [['anio_publicacion'], 'date', 'format' => 'php:Y', 'message' => 'El añoñ de publicación solo incluye el año'],
            [['autores'], 'safe']

        ];
    }

    public function getAutores()
    {
        return $this->hasMany(Autor::className(), ['_id' => 'autor_id']);
    }

    public function messages()
    {
        return [
            'anio_publicacion.date' => 'La fecha de publicación solo incluye el año'
            
        ];
    }



    
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::PATCH_EDIT] = $scenarios[self::SCENARIO_DEFAULT];
        return $scenarios;
    }
}
