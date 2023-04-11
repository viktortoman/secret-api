<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ApiResponseService
{
    private SerializerInterface $serializer;
    private RequestStack $requestStack;
    public function __construct(
        SerializerInterface $serializer,
        RequestStack $requestStack
    ) {
        $this->serializer = $serializer;
        $this->requestStack = $requestStack;
    }

    protected function serialize($data, $format = 'json'): string
    {
        return $this->serializer->serialize($data, $format);
    }

    /**
     * @throws \Exception
     */
    public function createApiResponse($data, $statusCode = Response::HTTP_OK): Response
    {
        $request = $this->requestStack->getCurrentRequest();
        $requestedType = $request->headers->has('Accept')
            ? $request->headers->get('Accept')
            : 'application/json';

        $response = match ($requestedType) {
            'application/json' => $this->serialize($data),
            'application/xml' => $this->serialize($data, 'xml'),
            default => throw new \Exception('Request type ' . $requestedType . ' is not supported', Response::HTTP_BAD_REQUEST)
        };

        return new Response($response, $statusCode, array(
            'Content-Type' => $requestedType
        ));
    }
}