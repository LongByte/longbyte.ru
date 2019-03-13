<?php

namespace Realweb\Builder;

class FormBuilder extends \WS\ReduceMigrations\Builder\FormBuilder {

    public function GetForm($FORM_SID) {
        if (\CModule::IncludeModule("form")) {
            $rsForm = \CForm::GetBySID($FORM_SID);
            if ($arForm = $rsForm->Fetch()) {
                return $arForm;
            }
        }
        return false;
    }

    public function GetQuestions($FORM_ID) {

        $arFilter = Array();
        $arQuestions = array();

        $rsQuestions = \CFormField::GetList(
                $FORM_ID, "N", $by = "s_id", $order = "desc", $arFilter, $is_filtered
        );

        while ($arQuestion = $rsQuestions->Fetch()) {
            $arQuestions[$arQuestion["SID"]] = $arQuestion;
        }

        return $arQuestions;
    }

    public function GetStatus($FORM_ID, $STATUS) {
        $status = \CFormStatus::GetList($FORM_ID, $by, $order, array(
                'TITLE' => $STATUS,
                ), $isFiltered)->Fetch();
        return $status;
    }

}
