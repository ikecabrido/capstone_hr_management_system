<?php
require_once __DIR__ . '/../models/RequestType.php';

class RequestTypeController
{
    public function index()
    {
        $model = new RequestType();
        $requestTypes = $model->all();

        $GLOBALS['page_content'] =
            __DIR__ . '/../views/request-types/main-content.php';

        require __DIR__ . '/../views/request-types/index.php';
    }
}
