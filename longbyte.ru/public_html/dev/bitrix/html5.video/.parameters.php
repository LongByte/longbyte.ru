<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$arComponentParameters = array(
    "GROUPS" => array(
        "DATA_SOURCE" => array(
            "NAME" => 'Параметры',
            "SORT" => "100"
        ),
    ),
    "PARAMETERS" => array(
        "FILE_PATH" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => 'Путь к файлу фидео',
            "TYPE" => "FILE",
            "FD_TARGET" => "F",
            "FD_EXT" => 'mp4',
            "FD_UPLOAD" => true,
            "FD_USE_MEDIALIB" => true,
            "FD_MEDIALIB_TYPES" => Array('video')
        ),
        "PREVIEW_PATH" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => 'Путь к файлу заставка (превью)',
            "TYPE" => "FILE",
            "FD_TARGET" => "F",
            "FD_EXT" => 'jpg, jpeg, png, gif, bmp',
            "FD_UPLOAD" => true,
            "FD_USE_MEDIALIB" => true,
            "FD_MEDIALIB_TYPES" => Array('image')
        ),
        "WIDTH" => array(
            "PARENT" => "DATA_SOURCE",
            "TYPE" => "TEXT",
            "NAME" => 'Ширина видео в пикселях',
        ),
        "HEIGHT" => array(
            "PARENT" => "DATA_SOURCE",
            "TYPE" => "TEXT",
            "NAME" => 'Высота видео в пикселях',
        ),
    ),
);
?>