<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProductValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(array $data) : array{

        $constraints = new Assert\Collection([
            'gtin' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 10,'max' => 20])
            ],
            'language' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 2,'max' => 5])
            ],
            'title' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 255])
            ],
            'picture' => [
                new Assert\NotBlank(),
                new Assert\Url()
            ],
            'description' => [
                new Assert\NotBlank()],
            'price' => [
                new Assert\NotBlank(),
                new Assert\Type('numeric'),
                new Assert\Positive()],
            'stock' => [
                new Assert\NotBlank(),
                new Assert\Regex(['pattern' => '/^\d+$/']),
                new Assert\PositiveOrZero()
            ]
        ]);

        $violations = $this->validator->validate($data, $constraints);
        $errors = [];

        foreach ($violations as $violation){
            $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
        }

        return $errors;
    }

}