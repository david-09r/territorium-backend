<?php

namespace App\Utils\Enum;

class TextResponse
{
    const TYPE_USER_NOT_VALID = 'El tipo de usuario seleccionado no es valido';

    const NOT_BIRTH_SELECTED = 'Seleccione la fecha de nacimiento';

    const UNAUTHORIZED = 'Credenciales incorrectas';

    const NOT_PERMISSIONS = 'No tiene permisos';

    const NOT_FOUND_USER = 'Aprendiz o instructor no encontrado';

    const NOT_DATA_FORMATION = 'No existe la formacion buscada';

    const NOT_DATA_TASKS = 'No tienes tareas';

    const NOT_AREA_FORMATION = 'No tiene areas esta formacion';

    const NOT_EXIST_AREA = 'No existe la area';
}