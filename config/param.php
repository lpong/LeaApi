<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/3/30
 * Time: 16:04
 */
return [
    'secret'         => 'ZFYbTYULnmAKB2eQ1ygNQiVWIELDSI6S',
    'request_mothod' => [
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'COPY',
        'HEAD',
        'OPTIONS',
        'LINK',
        'UNLINK',
        'PUAGE',
        'LOCK',
        'UNLOCK',
        'PROPFIND',
        'VIEW',
    ],
    'request_model'  => [
        'formdata'   => 'form-data',
        'urlencoded' => 'x-www-form-urlencode',
        'raw'        => 'raw',
    ],

    'request_header' => [
        ''                       => 'Text',
        'text/plain'             => 'Text(text/plain)',
        'application/json'       => 'JSON(application/json)',
        'application/javascript' => 'Javascript(application/javascript)',
        'application/xml'        => 'XML(application/xml)',
        'text/xml'               => 'XML(text/xml)',
        'text/html'              => 'HTML(text/html)',
    ]
];