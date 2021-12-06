<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQlServer\Controller;

use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\GraphQl\Exception\ExceptionFormatter;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\GraphQlServer\Model\Server;

/**
 * Graphql front controller
 */
class Gateway implements FrontControllerInterface
{

    /**
     * @var SerializerInterface
     */
    private $jsonSerializer;

    /**
     * @var ExceptionFormatter
     */
    private $graphQlError;

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var HttpResponse
     */
    private $httpResponse;

    /**
     * @var Server
     */
    private $server;

    /**
     * @param SerializerInterface $jsonSerializer
     * @param ExceptionFormatter $graphQlError
     * @param JsonFactory $jsonFactory
     * @param HttpResponse $httpResponse
     * @param Server $server
     */
    public function __construct(
        SerializerInterface $jsonSerializer,
        ExceptionFormatter $graphQlError,
        JsonFactory $jsonFactory,
        HttpResponse $httpResponse,
        Server $server
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->graphQlError = $graphQlError;
        $this->jsonFactory = $jsonFactory;
        $this->httpResponse = $httpResponse;
        $this->server = $server;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws \Throwable
     */
    public function dispatch(RequestInterface $request): ResponseInterface
    {
        $statusCode = 200;
        $jsonResult = $this->jsonFactory->create();
        $data = $this->getDataFromRequest($request);
        $result = [];
        try {
            $query = $data['query'] ?? '';
            $variables = $data['variables'] ?? null;
            $result = $this->server->execute($query, $variables);
        } catch (\Exception $error) {
            $result['errors'] = isset($result['errors']) ? $result['errors'] : [];
            $result['errors'][] = $this->graphQlError->create($error);
            $statusCode = ExceptionFormatter::HTTP_GRAPH_QL_SCHEMA_ERROR_STATUS;
        }

        $jsonResult->setHttpResponseCode($statusCode);
        $jsonResult->setData($result);
        $jsonResult->renderResult($this->httpResponse);
        return $this->httpResponse;
    }

    /**
     * Get data from request body or query string
     *
     * @param RequestInterface $request
     * @return array
     */
    private function getDataFromRequest(RequestInterface $request): array
    {
        /** @var Http $request */
        if ($request->isPost()) {
            $data = $this->jsonSerializer->unserialize($request->getContent());
        } elseif ($request->isGet()) {
            $data = $request->getParams();
            $data['variables'] = isset($data['variables']) ?
                $this->jsonSerializer->unserialize($data['variables']) : null;
            $data['variables'] = is_array($data['variables']) ?
                $data['variables'] : null;
        } else {
            return [];
        }
        return $data;
    }
}
