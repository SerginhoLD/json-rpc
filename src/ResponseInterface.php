<?php
namespace SerginhoLD\Json\Rpc;

/**
 * Interface ResponseInterface
 * @package SerginhoLD\Json\Rpc
 */
interface ResponseInterface extends \JsonSerializable
{
    /**
     * @return string
     */
    public function getJsonRpc(): string;

    /**
     * @return mixed
     */
    public function getResult();

    /**
     * @param $result
     */
    public function setResult($result);

    /**
     * @return ErrorInterface|null
     */
    public function getError(): ?ErrorInterface;

    /**
     * @param ErrorInterface $error
     */
    public function setError(ErrorInterface $error);

    /**
     * @param int $code
     * @param string $message
     * @param array|null $data
     */
    public function withError(int $code, string $message, array $data = null);

    /**
     * @return int|string|null
     */
    public function getId();
}
