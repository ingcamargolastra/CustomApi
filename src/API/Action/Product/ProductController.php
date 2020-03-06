<?php


namespace App\API\Action\Product;


use App\API\Action\RequestTransformer;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Requests\ProductFormRequest;
use App\Services\ProductService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController
{
    /**
     * @var ProductService
     */
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @Route("/products/index", methods={"POST"})
     */
    public function index(){
        $products = $this->productService->index();
        return new JsonResponse($products);
    }

    /**
     * @param ProductFormRequest $request
     * @return JsonResponse
     * @throws \Exception
     * @Route("/products/store", methods={"POST"})
     */
    public function store(ProductFormRequest $request): JsonResponse
    {
        return new JsonResponse($this->productService->store($request));
    }
}