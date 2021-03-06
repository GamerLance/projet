<?php

namespace App\Domain\User\Service;
use App\Domain\User\Repository\UserCreatorRepository;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;
/**
 * Service.
 */
final class UserCreator
{
    /**
     * @var UserCreatorRepository
     */

    private $repository;
    private $logger;
    /**
     * The constructor.
     *
     * @param LoggerFactory $logger the logger
     * @param UserCreatorRepository $repository The repository
     */

    public function __construct(UserCreatorRepository $repository,LoggerFactory $logger)
    {
        $this->repository = $repository;
        $this->logger =$logger
            -> addFileHandler('AjouterUser.log')
            -> createLogger();
    }

    /**
     * Create a new user.
     *
     * @param array $data The form data
     *
     * @return int The new user ID
     */
    public function createUser(array $data): int
    {
        // Input validation
        $this->validateNewUser($data);

        // Insert user
        $userId = $this->repository->insertUser($data);

        // Logging here: User created successfully
        $this->logger->info(sprintf('User created successfully: %s', $userId));

        return $userId;
    }

    /**
     * Input validation.
     *
     * @param array $data The form data
     *
     * @throws ValidationException
     *
     * @return void
     */
    private function validateNewUser(array $data): void
    {
        $errors = [];

        // Here you can also use your preferred validation library

        if (empty($data['username'])) {
            $errors['username'] = 'Input required';
        }

        if (empty($data['email'])) {
            $errors['email'] = 'Input required';
        } elseif (filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'Invalid email address';
        }

        if ($errors) {
            throw new ValidationException('Please check your input', $errors);
        }
    }
}
