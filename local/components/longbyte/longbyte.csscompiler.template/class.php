<?

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

class LongbyteCSSCompilerTemplateComponent extends CBitrixComponent {

    /**
     * Prepare Component Params
     */
    public function onPrepareComponentParams($params) {
        if (preg_match('/\\' . DIRECTORY_SEPARATOR . 'local\\' . DIRECTORY_SEPARATOR . '/', $params['TEMPLATE_PATH'])) {
            $params['TEMPLATE_PATH'] = preg_replace('/^.*\\' . DIRECTORY_SEPARATOR . 'local\\' . DIRECTORY_SEPARATOR . '/', '/local/', $params['TEMPLATE_PATH']);
        } elseif (preg_match('/\\' . DIRECTORY_SEPARATOR . 'bitrix\\' . DIRECTORY_SEPARATOR . '/', $params['TEMPLATE_PATH'])) {
            $params['TEMPLATE_PATH'] = preg_replace('/^.*\\' . DIRECTORY_SEPARATOR . 'bitrix\\' . DIRECTORY_SEPARATOR . '/', '/bitrix/', $params['TEMPLATE_PATH']);
        }

        return $params;
    }

    /**
     * Start Component
     */
    public function executeComponent() {
        $this->includeComponentTemplate();
    }

}
