<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AuthFlowTest extends WebTestCase
{
    private KernelBrowser $client;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $dbFilePath = '/tmp/excel_model_hydrator_test.db';
        if (file_exists($dbFilePath)) {
            unlink($dbFilePath);
        }

        self::bootKernel();

        $entityManager = self::entityManager();
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->createSchema($metadata);

        self::ensureKernelShutdown();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->clearUsers();
    }

    public function testHomePageIsPublicForGuest(): void
    {
        $this->client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertStringContainsString(
            'Вы не авторизованы.',
            (string) $this->client->getResponse()->getContent()
        );
    }

    public function testRegistrationCreatesUserAndRedirectsToLogin(): void
    {
        $this->client->request('POST', '/register', [
            'email' => 'new-user@example.com',
            'password' => 'secure123',
        ]);

        self::assertResponseRedirects('/login');

        $user = self::userRepository()->findOneBy(['email' => 'new-user@example.com']);
        self::assertNotNull($user);
        self::assertTrue(password_verify('secure123', (string) $user->getPassword()));
    }

    public function testRegistrationWithDuplicateEmailShowsValidationError(): void
    {
        $this->createUser('dup@example.com', 'secure123', ['ROLE_USER']);

        $this->client->request('POST', '/register', [
            'email' => 'dup@example.com',
            'password' => 'secure123',
        ]);

        self::assertResponseIsSuccessful();
        self::assertStringContainsString(
            'Пользователь с таким email уже существует.',
            (string) $this->client->getResponse()->getContent()
        );
    }

    public function testLoginWithValidCredentialsRedirectsToMainPage(): void
    {
        $this->createUser('user@user.ru', 'secret123', ['ROLE_USER']);

        $this->client->request('POST', '/login', [
            '_username' => 'user@user.ru',
            '_password' => 'secret123',
        ]);

        self::assertResponseRedirects('/');

        $this->client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertStringContainsString(
            'Вы вошли как',
            (string) $this->client->getResponse()->getContent()
        );
    }

    public function testLoginWithInvalidCredentialsKeepsUserLoggedOut(): void
    {
        $this->createUser('admin@admin.ru', 'admin123', ['ROLE_ADMIN']);

        $this->client->request('POST', '/login', [
            '_username' => 'admin@admin.ru',
            '_password' => 'wrong-password',
        ]);

        self::assertResponseRedirects('/login');

        $this->client->request('POST', '/logout');
        self::assertResponseRedirects('/login');
    }

    public function testLogoutRedirectsToMainPage(): void
    {
        $this->createUser('logout@example.com', 'secure123', ['ROLE_USER']);

        $this->client->request('POST', '/login', [
            '_username' => 'logout@example.com',
            '_password' => 'secure123',
        ]);
        self::assertResponseRedirects('/');

        $this->client->request('POST', '/logout');
        self::assertResponseRedirects('/');

        $this->client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertStringContainsString(
            'Вы не авторизованы.',
            (string) $this->client->getResponse()->getContent()
        );
    }

    private function createUser(string $email, string $plainPassword, array $roles): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);

        $passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

        $entityManager = self::entityManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }

    private function clearUsers(): void
    {
        self::entityManager()->createQuery('DELETE FROM App\Entity\User u')->execute();
    }

    private static function userRepository(): UserRepository
    {
        return self::getContainer()->get(UserRepository::class);
    }

    private static function entityManager(): EntityManagerInterface
    {
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
