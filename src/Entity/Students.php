<?php

declare(strict_types=1);

namespace Modspace\Entity;

use Modspace\Core\Model\Model;
use Modspace\Core\Model\Relation\RelationType;
use Modspace\Repository\StudentsRepository;

class Students extends Model
{
	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var \Modspace\Entity\ContactInfo
	 */
	private $contactInfo;

	public function getId(): string
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

	public function getContactInfo(): ContactInfo
	{
		return $this->contactInfo;
	}

	public function setContactInfo(ContactInfo $contactInfo)
	{
		$this->contactInfo = $contactInfo;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPrimaryKey(): string
	{
		return 'id';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRelationTargetClass(): string
	{
		return ContactInfo::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRelationTargetPrimaryKey(): string
	{
		return 'id';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRelationBindObject(): string
	{
		return 'contactInfo';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRelationType(): int
	{
		return RelationType::ONE_TO_ONE;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getDefaultTable(): string
	{
		return 'students';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getDefaultRepository(): string
	{
		return StudentsRepository::class;
	}
}
