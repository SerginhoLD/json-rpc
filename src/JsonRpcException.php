<?php
declare(strict_types = 1);

namespace SerginhoLD\Json\Rpc;

/**
 * Class JsonRpcException
 * @package SerginhoLD\Json\Rpc
 */
class JsonRpcException extends \Exception
{
    const CODE_SERVER_ERROR = -32000;
    const MESSAGE_SERVER_ERROR = 'Server error';

    const CODE_INVALID_REQUEST = -32600;
    const MESSAGE_INVALID_REQUEST = 'Invalid Request';

    const CODE_METHOD_NOT_FOUND = -32601;
    const MESSAGE_METHOD_NOT_FOUND = 'Method not found';

    const CODE_INVALID_PARAMS = -32602;
    const MESSAGE_INVALID_PARAMS = 'Invalid params';

    const CODE_INTERNAL_ERROR = -32603;
    const MESSAGE_INTERNAL_ERROR = 'Internal error';

    const CODE_PARSE_ERROR = -32700;
    const MESSAGE_PARSE_ERROR = 'Parse error';

    /** @var int */
    protected $code = self::CODE_INTERNAL_ERROR;
}
