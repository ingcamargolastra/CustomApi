<?php


namespace App\Services;


use App\API\Action\RequestTransformer;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductService
{
    /**
     * @var ProductRepository
     */
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index(){
        $products = $this->productRepository->all();

        $data = [];

        foreach ($products as $product) $data[] = [
            "id" => $product->getId(),
            "name" => $product->getName(),
            "description" => $product->getDescription(),
            "quantity" => $product->getQuantity(),
            "price" => $product->getPrice(),
        ];

        return [
            'success' => true,
            'products' => $data
        ];
    }

    /**
     * @param Request|mixed $request
     * @return array
     * @throws \Exception
     */
    public function store(Request $request): array
    {
        $name = RequestTransformer::getRequiredField($request, 'name');
        $description =  RequestTransformer::getRequiredField($request, 'description');
        $quantity = RequestTransformer::getRequiredField($request, 'quantity');
        $price = RequestTransformer::getRequiredField($request, 'price');

        $product = new Product($name, $description, $quantity, $price);
        $this->productRepository->save($product);

        return ['success' => true];
    }
}