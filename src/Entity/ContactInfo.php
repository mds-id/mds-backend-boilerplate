<?php

declare(strict_types=1);

namespace Modspace\Entity;

use Modspace\Core\Model\Model;
use Modspace\Core\Model\Relation\RelationType;
use Modspace\Repository\ContactInfoRepository;

class ContactInfo extends Model
{
	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $city;

	/**
	 * @var string
	 */
	private $phone;

	/**
	 * @var \Modspace\Entity\Students
	 */
	private $student;

	public function getId(): string
	{
		return $this->id;
	}

	public function getCity(): string
	{
		return $this->city;
	}

	public function setCity(string $city)
	{
		$this->city = $city;
	}

	public function getPhone(): string
	{
		return $this->phone;
	}

	public function setPhone(string $phone)
	{
		$this->phone = $phone;
	}

	public function getStudent(): Students
	{
		return $this->student;
	}

	public function setStudent(Students $student)
	{
		$this->student = $student;
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
		return Students::class;
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
		return 'student';
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
		return 'contact_info';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getDefaultRepository(): string
	{
		return ContactInfoRepository::class;
	}
}
