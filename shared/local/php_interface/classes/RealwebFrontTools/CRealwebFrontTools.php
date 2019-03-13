<?class CRealwebFrontTools{
    
    private static $tmpPath = __DIR__."/templates";
    
    private static $pagesDirName = "/pages";
    
    private static $dataDirName = "/pages/data/";
    
    private static $componentTmpDirName = "/pages/components";
    
    private static $spaceDirPath = "/local";

    private static $redirectToIndex = false;

    /**
     * @param $data
     */
    private static function obToArrayRecursive(&$data){
        
        if(is_object($data)) $data = (array)$data;
        
        if(is_array($data) && count($data) > 0){
            foreach($data as &$dataItem){
                self::obToArrayRecursive($dataItem);
            }
            unset($dataItem);
        }
        
    }

    /**
     * @param $str
     */
    private static function showErrorMsg($str){
        
        echo $str."<br/>";
        
    }

    /**
     * @param $dirPath
     * @return bool
     */
    private static function createDir($dirPath){

        $absPath = $_SERVER["DOCUMENT_ROOT"].$dirPath;
        if(!is_dir($absPath)){
            chmod(self::$spaceDirPath, 0755);
            return mkdir($absPath, 0755, true);
        }

        return true;

    }

    /**
     * @param $filePath
     * @param string $fileContent
     * @return bool
     */
    private static function createFile($filePath, $fileContent = ""){
        $fileContent = (string)$fileContent;
        $fp = fopen($_SERVER["DOCUMENT_ROOT"].$filePath, "w+");
        if(!$fp) return false;
        else{
            fwrite($fp, $fileContent);
            fclose($fp);
        }

        return true;

    }

    /**
     * @param $compName
     * @return bool
     */
    private static function createComponentTemplate($compName){
        
        $return = true;
        $absFilePath = $_SERVER["DOCUMENT_ROOT"].self::$componentTmpDirName."/".$compName.".php";
        if(self::createDir(self::$spaceDirPath."/templates/.default/components/realweb/blank/".$compName)){
            if(!file_exists($absFilePath) || !self::createFile(self::$spaceDirPath."/templates/.default/components/realweb/blank/".$compName."/template.php", file_get_contents($absFilePath))){
                self::showErrorMsg("Ошибка при создании файла шаблона '".$compName."' компонента. Возможно, проблема связана с правами доступа!");
                $return = false;
            }
        }
        else{
            self::showErrorMsg("Ошибка при создании директории шаблона '".$compName."' компонента.");
            $return = false;
        }
        
        return $return;
        
    }

    /**
     * @param $filePathOrData
     * @param bool $toObject
     * @return array|bool|mixed
     */
    public static function getDataFromJson($filePathOrData, $toObject = false){

        $toObject = (bool)$toObject;
        if(is_string($filePathOrData)){
            if(!file_exists($_SERVER["DOCUMENT_ROOT"].self::$dataDirName."/".$filePathOrData)) return false;
            $result = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"].self::$dataDirName."/".$filePathOrData));
        }
        else{
            $result = (array)$filePathOrData;
        }


        if(!$toObject) self::obToArrayRecursive($result);
        
        return $result;
        
    }

    /**
     * @return bool
     */
    public static function isMain(){
        
        global $APPLICATION;
        
        return CSite::InDir(self::$pagesDirName."/main.php");
        
    }

    /**
     *
     */
    public static function isActive(){
        
        global $APPLICATION;
        
    }

    /**
     * @param string $format
     * @return false|string
     */
    public static function getDate($format = "Y"){
        
        return date($format);
        
    }

    /**
     * @param $dirPath,
     * @param $arNew,
     */
    public static function getDirItems($dirPath, &$arNew){
        
        $arExItems = array(".", "..", "_index", "data", "components");
        if($arSubItems = scandir($dirPath)){
            foreach($arSubItems as &$arSubItem){
                if(!in_array($arSubItem, $arExItems)) {

                    $path = $dirPath . "/" . $arSubItem;

                    if (is_dir($path)) {
                        self::getDirItems($path, $arNew);
                    } else {
                        $rgPath = explode('/', $path);
                        if ($rgPath[count($rgPath)-2] == 'pages' && $arSubItem == 'index.php') continue;
                        $arNew[] = array(
                            "FILE_NAME" => str_replace(array($_SERVER['DOCUMENT_ROOT'], "index.php"), "", $path),
                            "ABS_PATH" => $path
                        );
                    }
                }
            }
        }
        else{
            self::showErrorMsg("Директория '".self::$pagesDirName."' пуста!");
        }
    }

    /**
     * @param $pagePath
     * @return mixed|string
     */
    public static function getPageTitle($pagePath){
        
        if(file_exists($pagePath)){
            preg_match_all("/SetTitle\((.*)\)/", file_get_contents($pagePath), $matches, PREG_SET_ORDER, 0);
            if(count($matches) > 0){
                return str_replace(array("'", "\""), "", end($matches)[1]);
            }
        }
        else{
            self::showErrorMsg("Файл '".$pagePath."' не существует!");
        }
        
        return "";
        
    }

    /**
     *
     */
    public static function showIndexPage(){
        
        if(file_exists(self::$tmpPath."/index.php")){
            $arPages = array();
            self::getDirItems($_SERVER["DOCUMENT_ROOT"].self::$pagesDirName, $arPages);

            if(count($arPages) > 0){
                $content = "";
                foreach($arPages as $arPageItem){
                    $title = self::getPageTitle($arPageItem["ABS_PATH"]);
                    $content .= '
                        <tr class="">
                            <td width="30%">
                                <a href="'.$arPageItem["FILE_NAME"].'" target="_blank" class="page-url">'.$arPageItem["FILE_NAME"].'</a>
                            </td>
                            <td>'.(strlen($title) > 0 ? $title : $arPageItem["FILE_NAME"]).'</td>
                        </tr>';
                }
                
                echo str_replace("#PAGE_LIST#", $content, file_get_contents(self::$tmpPath."/index.php"));
            }
        }
        
    }

    /**
     * @param $compName
     * @param string $modClassName
     * @param string $filePathOrData
     * @param bool $toObject
     */
    public static function IncludeComponent($compName, $modClassName = "", $filePathOrData = "", $toObject = false){

        //TODO: Разобраться с правами при создании файла шаблона (сейчас созданный файл нельзя реадктировать!!!)
        global $APPLICATION;

        if(self::createComponentTemplate($compName)){
            $arParams = array();
            if(is_string($filePathOrData) || is_array($filePathOrData) || is_object($filePathOrData)){
                $arParams = array(
                    "DATA" => self::getDataFromJson($filePathOrData, $toObject),
                    "MOD_CLASS" => $modClassName
                );
            }
            else{
                $arParams = array("MOD_CLASS" => $modClassName);
            }
            $APPLICATION->IncludeComponent(
                "realweb:blank",
                $compName,
                $arParams
            );
        }
        
    }

    /**
     * @param $class
     * @param $fileName
     * @return string
     */
    public static function getSvg($class, $fileName = "symbol-sprite.svg"){

        $filePath = SITE_TEMPLATE_PATH."/images/".$fileName;
        if(!file_exists($_SERVER["DOCUMENT_ROOT"].$filePath)){
            self::showErrorMsg("Файл '".$filePath."' не существует!");
            return "";
        }

        return '<svg class="icon '.$class.'"><use xlink:href="'.$filePath.'#'.$class.'"></use></svg>';

    }

    /**
     *
     */
    public static function redirectToIndex(){

        if(self::$redirectToIndex){
            LocalRedirect(self::$pagesDirName."/");
            die();
        }

    }
    
}