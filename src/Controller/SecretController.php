<?php

namespace App\Controller;

use App\Assert\SecretAssert;
use App\Repository\SecretRepository;
use App\Services\ApiResponseService;
use App\Services\SecretService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route('/api/v1', name: 'app_secret_')]
class SecretController extends AbstractController
{
    private ApiResponseService $apiResponseService;
    private SecretService $service;
    private SecretRepository $repository;
    private ValidatorInterface $validator;
    public function __construct(
        SecretService $service,
        ApiResponseService $apiResponseService,
        ValidatorInterface $validator,
        SecretRepository $repository
    ) {
        $this->apiResponseService = $apiResponseService;
        $this->service = $service;
        $this->validator = $validator;
        $this->repository = $repository;
    }

    /**
     * @throws \Exception
     */
    #[Route('/secret', name: 'create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $constraints = SecretAssert::getConstraints();
        $messages = [];

        $data = $request->request->all();
        $data['expireAfterViews'] = (int) $data['expireAfterViews'];
        $data['expireAfter'] = (int) $data['expireAfter'];

        $validationResult = $this->validator->validate($data, $constraints);

        foreach ($validationResult ?? [] as $message) {
            $messages['errors'][] = [
                'property' => $message->getPropertyPath(),
                'value' => $message->getInvalidValue(),
                'message' => $message->getMessage(),
            ];
        }

        if (count($messages) > 0) {
            return new JsonResponse($messages, Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $secret = $this->service->create($data);

        return $this->apiResponseService->createApiResponse(
            $this->repository->toArray($secret),
            Response::HTTP_CREATED
        );
    }

    /**
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    #[Route('/secret/{hash}', name: 'find_by_hash', methods: ['GET'])]
    public function findByHash(string $hash): Response
    {
        $secret = $this->service->findByHash($hash);

        if (!$secret) {
            return new JsonResponse([
                'error' => 'Not found secret by hash or secret is expired or remaining views is 0.'
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->apiResponseService->createApiResponse(
            $this->repository->toArray($secret)
        );
    }
}
