<?php

namespace App\Requests;

use App\Exception\ValidationException;
use App\Traits\ValidationAwareTrait;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\ServerBag;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class FormRequest
 *
 * @property ParameterBag   $attributes
 * @property ParameterBag   $request
 * @property ParameterBag   $query
 * @property ServerBag      $server
 * @property FileBag        $files
 * @property ParameterBag   $cookies
 * @property HeaderBag      $headers
 *
 * @method   duplicate(array $query, array $request, array $attributes, array $cookies, array $files, array $server)
 * @method   overrideGlobals()
 */
abstract class FormRequest
{
    use ValidationAwareTrait;

    /** @var ValidatorInterface */
    private ValidatorInterface $validator;

    final public function __construct(RequestStack $request)
    {
        $this->httpRequest = $request->getCurrentRequest();
        $this->validator = Validation::createValidator();

        $this->initialize();
    }

    final public function initialize() : void
    {
        if (!$this->passesAuthorization()) {
            $this->failedAuthorization();
        }

        $this->validate();
    }

    /**
     * Get all request parameters
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    final public function all()
    {
        /*return $this->httpRequest->attributes->all()
            + $this->httpRequest->query->all()
            + $this->httpRequest->request->all()
            + $this->httpRequest->files->all();*/
        return $this->transform($this->httpRequest);
    }

    public function transform(Request $request)
    {
        $data = \json_decode($request->getContent(), true);

        if (null === $data || \JSON_ERROR_NONE !== \json_last_error()) {
            throw new \InvalidArgumentException('Invalid JSON body');
        }

        return $data;
    }

    /**
     * Returns list of constraints for validation
     *
     * @return Collection
     */
    abstract public function rules() : Collection;

    /**
     * Determine if the request passes the authorization check.
     *
     * @return bool
     */
    final protected function passesAuthorization() : bool
    {
        if (method_exists($this, 'authorize')) {
            return $this->authorize();
        }

        return true;
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     * @throws AccessDeniedHttpException
     */
    final protected function failedAuthorization() : void
    {
        throw new AccessDeniedHttpException();
    }

    /**
     * @return bool
     * @throws ValidationException
     */
    final protected function validate() : bool
    {
        /** @var ConstraintViolationList $violations */
        $violations = $this->validator->validate($this->all(), $this->rules());


        if ($violations->count()) {
            throw new ValidationException($this->validator, $violations);
        }

        return true;
    }
}