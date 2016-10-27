<?php

namespace route;

try {
    $route = new Route();

    $route->post('authentication/', array(
        'action' => 'logar'
    ));
    $route->get('build/', array(
        'action' => 'get',
        'params' => array(
            'program'
        ),
        'requestHeaders' => array(
            'Authorization'
        )
    ));


    $route->init();
} catch (\exceptions\restException $e) {
    $e->httpOutput();
} catch (\Exception $e) {
    $rs = array(
        'status' => false,
        'message' => $e->getMessage(),
        'error' => $e->getCode()
    );
    echo toJson($rs);
}