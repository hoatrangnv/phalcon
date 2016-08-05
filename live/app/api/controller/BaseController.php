<?php
namespace ITECH\Api\Controller;

class BaseController extends \Phalcon\Mvc\Controller {
    public function initialize() {
        //$token = $this->request->getQuery('token', array('striptags', 'trim'), '');
        $token = $this->config->application->token;
        if ($token != $this->config->application->token) {
            $response = array(
                'status' => \ITECH\Datasource\Lib\Constant::CODE_ERROR,
                'message' => 'Invalid token.'
            );
            
            return $this->outputJSON($response);
        }
    }

    public function outputJSON($response) {
        $this->view->disable();

        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setJsonContent($response);
        $this->response->send();
    }
}