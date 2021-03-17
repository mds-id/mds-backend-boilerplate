<?php

declare(strict_types=1);

namespace Bluepeer\Entity;

use Bluepeer\Core\Model\Model;
use Bluepeer\Core\Repository\RepositoryInterface;
use Bluepeer\Repository\UserRepository;

class User extends Model
{
	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $email;

	public function getId()
	{
		return $this->id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name)
	{
		$this->name = $name;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function setEmail(string $email)
	{
		$this->email = $email;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getDefaultTable(): string
	{
		return 'user';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getDefaultRepository(): string
	{
		return UserRepository::class;
	}
}
