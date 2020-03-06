<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class ValidationException
 */
class ValidationException extends \RuntimeException
{
    const NOT_EXPECTED = "This field was not expected.";

    /** @var ValidatorInterface */
    private ValidatorInterface $validator;

    /** @var ConstraintViolationList|null */
    private ConstraintViolationList $violations;

    /** @var  */
    private PropertyAccessor $propertyAccessor;

    /**
     * ValidationException constructor.
     * @param ValidatorInterface $validator
     * @param ConstraintViolationList|null $violations
     * @throws \Exception
     */
    public function __construct(ValidatorInterface $validator, ConstraintViolationList $violations = null)
    {
        $message = 'The given data failed to pass validation.';

        $this->validator = $validator;
        $this->violations = $violations;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        parent::__construct($message);
    }

    public function getValidator() : ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getResponseData() : array
    {
        $errors = [];

        if ($this->violations instanceof ConstraintViolationList) {
            $iterator = $this->violations->getIterator();

            /** @var ConstraintViolation $violation */
            foreach ($iterator as $key => $violation) {
                $entryErrors = (array) $this->propertyAccessor->getValue($errors, $violation->getPropertyPath());

                if (self::NOT_EXPECTED !== $violation->getMessage())
                {
                    $entryErrors[] = $violation->getMessage();
                    $this->propertyAccessor->setValue($errors, $violation->getPropertyPath(), $entryErrors);
                }
            }
        }

        return $errors;
    }
}