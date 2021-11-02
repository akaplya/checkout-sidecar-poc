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
use Magento\Framework\GraphQl\Query\Fields as QueryFields;
use Magento\Framework\GraphQl\Query\QueryProcessor;
use Magento\Framework\GraphQl\Schema\SchemaGeneratorInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\GraphQlServer\Model\Context\ContextFactory;

/**
 * Graphql front controller
 */
class Gateway implements FrontControllerInterface
{

    /**
     * @var SchemaGeneratorInterface
     */
    private $schemaGenerator;

    /**
     * @var SerializerInterface
     */
    private $jsonSerializer;

    /**
     * @var QueryProcessor
     */
    private $queryProcessor;

    /**
     * @var ExceptionFormatter
     */
    private $graphQlError;

    /**
     * @var QueryFields
     */
    private $queryFields;

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var HttpResponse
     */
    private $httpResponse;

    /**
     * @var ContextFactory
     */
    private $contextFactory;

    /**
     * @param SchemaGeneratorInterface $schemaGenerator
     * @param SerializerInterface $jsonSerializer
     * @param QueryProcessor $queryProcessor
     * @param ExceptionFormatter $graphQlError
     * @param QueryFields $queryFields
     * @param JsonFactory $jsonFactory
     * @param HttpResponse $httpResponse
     * @param ContextFactory $contextFactory
     */
    public function __construct(
        SchemaGeneratorInterface $schemaGenerator,
        SerializerInterface $jsonSerializer,
        QueryProcessor $queryProcessor,
        ExceptionFormatter $graphQlError,
        QueryFields $queryFields,
        JsonFactory $jsonFactory,
        HttpResponse $httpResponse,
        ContextFactory $contextFactory
    ) {
        $this->schemaGenerator = $schemaGenerator;
        $this->jsonSerializer = $jsonSerializer;
        $this->queryProcessor = $queryProcessor;
        $this->graphQlError = $graphQlError;
        $this->queryFields = $queryFields;
        $this->jsonFactory = $jsonFactory;
        $this->httpResponse = $httpResponse;
        $this->contextFactory = $contextFactory;
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
//        \GraphQL\Type\Definition\Type::overrideStandardTypes(
//            [
//                'JSON' => new \Magento\GraphQlServer\Type\Scalar\JsonScalarType()
//            ]
//        );

        try {
            $query = $data['query'] ?? '';
            $variables = $data['variables'] ?? null;
            $this->queryFields->setQuery($query, $variables);
            $schema = $this->schemaGenerator->generate();

            $result = $this->queryProcessor->process(
                $schema,
                $query,
                $this->contextFactory->create(),
                $data['variables'] ?? []
            );
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
