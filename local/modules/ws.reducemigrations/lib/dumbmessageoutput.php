<?php

/**
 * @author Maxim Sokolovsky
 */

namespace WS\ReduceMigrations;

class DumbMessageOutput implements MessageOutputInterface {

    /**
     * @param $message
     */
    public function println($message) {
        
    }

    /**
     * @param $message
     */
    public function printError($message) {
        
    }

    /**
     * @param $message
     */
    public function printInProgress($message) {
        
    }

    /**
     * @param $message
     */
    public function printSuccess($message) {
        
    }

}
