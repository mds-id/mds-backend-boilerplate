#### Entity model writing convention.

Imagine you have an entity named ```user``` and you want to write a class entity named ```User``` based on your currently defined entity. The current entity column definition is shown below:

```
+-------+------+------+-----+---------+----------------+
| Field | Type | Null | Key | Default | Extra          |
+-------+------+------+-----+---------+----------------+
| id    | int  | NO   | PRI | NULL    | auto_increment |
| name  | text | NO   |     | NULL    |                |
| email | text | NO   |     | NULL    |                |
+-------+------+------+-----+---------+----------------+
```

We have an ```id``` field which its attribute is ```auto_increment, int, not null```, a ```name``` field which its attribute is ```text, not null``` and ```email``` field which its attribute is ```text, not null```. So, the class entity is just like this:

```
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

	/**
	 * Get id.
	 *
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * Get name.
	 *
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * Set name.
	 *
	 * @param string $name
	 * @return void
	 */
	public function setName(string $name)
	{
		$this->name = $name;
	}

	/**
	 * Get email.
	 *
	 * @return string
	 */
	public function getEmail(): string
	{
		return $this->email;
	}

	/**
	 * Set email.
	 *
	 * @param string $email
	 * @return void
	 */
	public function setEmail(string $email)
	{
		$this->email = $email;
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
```
