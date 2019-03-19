<?

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

class LongbyteCSSCompilerTemplateComponent extends CBitrixComponent {

    /**
     * Prepare Component Params
     */
    public function onPrepareComponentParams($params) {
        if (preg_match('/\/local\//', $params['TEMPLATE_PATH'])) {
            $params['TEMPLATE_PATH'] = preg_replace('/^.*\/local\//', '/local/', $params['TEMPLATE_PATH']);
        } elseif (preg_match('/\/bitrix\//', $params['TEMPLATE_PATH'])) {
            $params['TEMPLATE_PATH'] = preg_replace('/^.*\/bitrix\//', '/bitrix/', $params['TEMPLATE_PATH']);
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
