<?php

declare(strict_types=1);

namespace App\Tests\Unit\Command;

use App\Command\CreateUserCommand;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CreateUserCommandTest extends TestCase
{
    private EntityManagerInterface&MockObject $em;
    private UserPasswordHasherInterface&MockObject $hasher;
    private ValidatorInterface&MockObject $validator;
    private CommandTester $tester;

    protected function setUp(): void
    {
        $this->em        = $this->createMock(EntityManagerInterface::class);
        $this->hasher    = $this->createMock(UserPasswordHasherInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);

        $this->hasher->method('hashPassword')->willReturn('hashed_password');
        $this->validator->method('validate')->willReturn(new ConstraintViolationList());

        $command     = new CreateUserCommand($this->em, $this->hasher, $this->validator);
        $application = new Application();
        $application->add($command);

        $this->tester = new CommandTester($application->find('app:user:create'));
    }

    public function testCreateUserSuccessfully(): void
    {
        $this->em->expects($this->once())->method('persist')->with($this->isInstanceOf(User::class));
        $this->em->expects($this->once())->method('flush');

        $this->tester->execute([
            'email'    => 'test@example.com',
            'fullname' => 'Test User',
            'password' => 'secret123',
        ]);

        $this->assertStringContainsString('created successfully', $this->tester->getDisplay());
        $this->assertSame(0, $this->tester->getStatusCode());
    }

    public function testCreateAdminUser(): void
    {
        $this->em->expects($this->once())->method('persist');
        $this->em->expects($this->once())->method('flush');

        $this->tester->execute([
            'email'    => 'admin@example.com',
            'fullname' => 'Admin User',
            'password' => 'secret123',
            '--admin'  => true,
        ]);

        $this->assertStringContainsString('[ADMIN]', $this->tester->getDisplay());
        $this->assertSame(0, $this->tester->getStatusCode());
    }
}
