<?php


namespace App\Repository;


use App\Entity\Product;

class ProductRepository extends BaseRepository
{

    protected static function entityClass(): string
    {
        return Product::class;
    }

    public function findOneByCode(string $code): ?Product
    {
        /** @var Product $product */
        $product = $this->objectRepository->findOneBy(['id' => $code]);

        return $product;
    }

    public function save(Product $product): void
    {
        $this->saveEntity($product);
    }

    public function all(): array
    {
        return $this->objectRepository->findAll();
    }
}