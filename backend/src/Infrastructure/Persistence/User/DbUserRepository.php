<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserRepository;
use DateTime;
use DI\NotFoundException;
use InvalidArgumentException;
use PDO;

class DbUserRepository implements UserRepository
{
    private PDO $db;
    
    /**
    * @param PDO $db
    */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * {@inheritdoc}
     */
    public function findUserOfId(int $id): User
    {
        $sql = "select * from sc_user where pk_user = :pk_user";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindValue(':pk_user', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, User::class);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return new User(
            (int)$result['pk_user'],
            $result['email'],
            $result['password'],
            new DateTime($result['creation_date']),
            new DateTime($result['last_update_date'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findUserOfEmail(string $email): User
    {
        $sql = "select * from sc_user where email = :email";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, User::class);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result == null)
            throw new NotFoundException("User with given email not found");

        return new User(
            (int)$result['pk_user'],
            $result['email'],
            $result['password'],
            new DateTime($result['creation_date']),
            new DateTime($result['last_update_date'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        $stmt = $this->db->prepare("select * from sc_user");
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, User::class);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($row) {
            return new User(
                (int)$row['pk_user'],
                $row['email'],
                '',
                new DateTime($row['creation_date']),
                new DateTime($row['last_update_date'])
            );
        }, $rows);
    }

    /**
     * {@inheritdoc}
     */
    public function add(array $input): int
    {
        $email = trim($input['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new InvalidArgumentException('Invalid email address');
        }

        $password = trim($input['password']);
        if (strlen($password) < 8)
        {
            throw new InvalidArgumentException(
                'Password must be at least 8 characters long'
            );
        }

        $query = "INSERT INTO sc_user (email, password)
                  VALUES (:email, :password)";

        $stmt = $this->db->prepare($query);
        
        $stmt->execute([
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        // Get generated ID
        $pkUser = $this->db->lastInsertId();
        return $pkUser;
    }

    private function validateEmail(string $email) {

    }
}
