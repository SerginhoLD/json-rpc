<?php
declare(strict_types = 1);

namespace SerginhoLD\Json\Rpc;

/**
 * Class Response
 * @package SerginhoLD\Json\Rpc
 */
class Response implements ResponseInterface
{
    /** @var mixed */
    private $result;

    /** @var ErrorInterface|null */
    private $error;

    /** @var int|string|null */
    private $id;

    /**
     * @param int|string|null $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getJsonRpc(): string
    {
        return RequestInterface::JSON_RPC_2;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @return ErrorInterface|null
     */
    public function getError(): ?ErrorInterface
    {
        return $this->error;
    }

    /**
     * @param ErrorInterface $error
     */
    public function setError(ErrorInterface $error)
    {
        $this->error = $error;
    }

    /**
     * @param int $code
     * @param string $message
     * @param array|null $data
     */
    public function withError(int $code, string $message, array $data = null)
    {
        $error = new Error($code, $message);

        if ($data !== null)
            $error->setData($data);

        $this->setError($error);
    }

    /**
     * @return int|string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $result = [
            'jsonrpc' => $this->getJsonRpc(),
        ];

        if ($error = $this->getError())
        {
            $result['error'] = $error;
        }
        else
        {
            $result['result'] = $this->getResult();
        }

        $result['id'] = $this->getId();
        return $result;
    }
}
