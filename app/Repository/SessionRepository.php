<?php

namespace Mizz\Belajar\PHP\MVC\Repository;

use Mizz\Belajar\PHP\MVC\Domain\Session;
use PDO;

class SessionRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    function save(Session $session): Session
    {
        $sql = "INSERT INTO sessions (id, user_id) VALUES (?,?)";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$session->id, $session->userId]);
        return $session;
    }

    function findById(string $id): ?Session
    {
        $sql = "SELECT id, user_id FROM sessions WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$id]);

        try {
            if ($row = $statement->fetch()) {
                $session = new Session();
                $session->id = $row['id'];
                $session->userId = $row['user_id'];
                return $session;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    function deleteById(string $id): void
    {
        $sql = "DELETE FROM sessions WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$id]);
    }

    function deleteAll(): void
    {
        $this->connection->exec("DELETE FROM sessions");
    }
}