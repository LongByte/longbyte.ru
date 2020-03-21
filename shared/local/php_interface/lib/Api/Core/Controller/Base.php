<?php

namespace Api\Core\Controller;

use Bitrix\Main\Context;

/**
 * Class \Api\Core\Controller\Base
 */
class Base {

    /**
     *
     * @var \Bitrix\Main\HttpRequest
     */
    protected $obRequest = null;

    /**
     *
     * @var string
     */
    protected $rawPost = null;

    public function __construct() {
        $this->obRequest = Context::getCurrent()->getRequest();
        $this->rawPost = file_get_contents('php://input');
    }

    /**
     * 
     * @return \Bitrix\Main\HttpRequest
     */
    protected function getRequest() {
        return $this->obRequest;
    }

    /**
     * 
     * @return string
     */
    protected function getPostData() {
        return $this->rawPost;
    }

}
