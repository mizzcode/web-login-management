<?php

namespace Mizz\Belajar\PHP\MVC\Repository;

use PDO;
use Mizz\Belajar\PHP\MVC\Domain\User;

class UserRepository
{
    private PDO $connection;
    public function __construct(pdo $connection)
    {
        $this->connection = $connection;
    }

    public function save(User $user): User
    {
        $sql = "INSERT INTO users (id,name,password) VALUES (?,?,?)";
        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $user->id, $user->name, $user->password
        ]);
        return $user;
    }

    public function update(User $user): User
    {
        $sql = "UPDATE users SET name = ?, password = ? WHERE id = ? ";
        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $user->name, $user->password, $user->id
        ]);
        return $user;
    }

    public function findById(string $id): ?User
    {
        $sql = "SELECT id, name, password FROM users WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$id]);

        try {
            if ($row = $statement->fetch()) {
                $user = new User();
                $user->id = $row['id'];
                $user->name = $row['name'];
                $user->password = $row['password'];
                return $user;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }


    public function deleteAll()
    {
        $this->connection->exec('DELETE FROM users');
    }
}
