<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Schema;

use Nette;


/**
 * Validation error.
 */
class ValidationException extends Nette\InvalidStateException
{
	public function __construct(
		?string $message,
		/** @var list<Message> */
<<<<<<< HEAD
		private readonly array $messages = [],
=======
		private array $messages = [],
>>>>>>> 9bc3608ef7b9c5a82d5f569b8ea6535b05e0f93b
	) {
		parent::__construct($message ?? $messages[0]->toString());
	}


<<<<<<< HEAD
	/**
	 * Returns all validation error messages as formatted strings.
	 * @return list<string>
	 */
=======
	/** @return list<string> */
>>>>>>> 9bc3608ef7b9c5a82d5f569b8ea6535b05e0f93b
	public function getMessages(): array
	{
		$res = [];
		foreach ($this->messages as $message) {
			$res[] = $message->toString();
		}

		return $res;
	}


<<<<<<< HEAD
	/**
	 * Returns all validation error messages as Message objects.
	 * @return list<Message>
	 */
=======
	/** @return list<Message> */
>>>>>>> 9bc3608ef7b9c5a82d5f569b8ea6535b05e0f93b
	public function getMessageObjects(): array
	{
		return $this->messages;
	}
}
