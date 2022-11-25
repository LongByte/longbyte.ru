<?

$lib = __DIR__ . '/lib';
loadRecursive($lib);
$lib = __DIR__ . '/libs/autoprefixer';
loadRecursive($lib);

function loadRecursive($lib)
{
    spl_autoload_register(function ($class) use ($lib) {
        $class = ltrim($class, '\\');
        $pieces = explode('\\', $class);

        array_shift($pieces);
        array_shift($pieces);

        $className = array_pop($pieces);
        $fileName = $className . '.php';
        $path = $lib . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pieces) . DIRECTORY_SEPARATOR . $fileName;

        if (!file_exists($path)) {
            return false;
        }
        require_once $path;
        return true;
    });

    $arGlob = glob($lib . '/*');

    foreach ($arGlob as $strFilePath) {

        if (is_dir($strFilePath)) {
            loadRecursive($strFilePath . '/');
        }
    }
}
