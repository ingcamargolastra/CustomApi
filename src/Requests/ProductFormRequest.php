<?php


namespace App\Requests;


use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;

class ProductFormRequest extends FormRequest
{

    /**
     * @inheritDoc
     */
    public function rules(): Collection
    {
        return new Collection([
            'name' => new Required([
                new NotBlank()
            ]),
            'description' => new Required([
                new NotBlank()
            ]),
            'quantity' => new Required([
                new NotBlank()
            ]),
            'price' => new Required([
                new NotBlank()
            ]),
        ]);
    }
}