<?php

namespace Smartcat\ConnectorAPI\API\Model;

class BadRequestBodyModel
{
    /**
     * @var string[][]
     */
    protected $modelState;
    /**
     * @var string
     */
    protected $requestId;
    /**
     * @var string
     */
    protected $error;
    /**
     * @var string
     */
    protected $errorDescription;
    /**
     * @return string[][]
     */
    public function getModelState()
    {
        return $this->modelState;
    }
    /**
     * @param string[][] $modelState
     *
     * @return self
     */
    public function setModelState(\ArrayObject $modelState = null)
    {
        $this->modelState = $modelState;
        return $this;
    }
    /**
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }
    /**
     * @param string $requestId
     *
     * @return self
     */
    public function setRequestId($requestId = null)
    {
        $this->requestId = $requestId;
        return $this;
    }
    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
    /**
     * @param string $error
     *
     * @return self
     */
    public function setError($error = null)
    {
        $this->error = $error;
        return $this;
    }
    /**
     * @return string
     */
    public function getErrorDescription()
    {
        return $this->errorDescription;
    }
    /**
     * @param string $errorDescription
     *
     * @return self
     */
    public function setErrorDescription($errorDescription = null)
    {
        $this->errorDescription = $errorDescription;
        return $this;
    }
}