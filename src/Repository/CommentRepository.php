<?php
namespace App\Repository;

use PDO;

class CommentRepository extends Connexion
{
    public function __construct($file = __DIR__ . '/../../config.ini')
    {
        if (!$settings = parse_ini_file($file, TRUE)) throw new exception('Unable to open ' . $file . '.');

        $dns = $settings['database']['driver'] .
            ':host=' . $settings['database']['host'] .
            ((!empty($settings['database']['port'])) ? (';port=' . $settings['database']['port']) : '') .
            ';dbname=' . $settings['database']['schema'];

        parent::__construct($dns, $settings['database']['username'], $settings['database']['password']);
    }

    public function update($post) {
        $query = 'UPDATE posts SET ';
        $k = 0;
        foreach($post as $key => $value) {
            if($key !== 'id') {
                if($k !== 0) {
                    $query .= ", ";
                }
                $query .= $key . ' = "' . $value . '"';
                $k++;
            }
        }
        $query .= "WHERE id = '" . $post->id . "'";
        $stmt = $this->bdd->prepare($query);
        $stmt->execute();
        return true;
    }

    public function post($values, $postId) {
        $col = [];
        $val = [];
        foreach($values as $key => $value) {
            $col[] = $key;
            $val[] = '"'.$value.'"';
        }

        $query = "INSERT INTO comments (" . implode(', ', $col) . ", post_id) VALUES (" . implode(", ", $val) . ", ". $postId .")";

        $stmt = $this->bdd->prepare($query);
        $stmt->execute();
        return true;
    }

    public function fillEntities($posts, $solo = false) {
        $userRepo = new UserRepository();
        if($solo) {
            $user_id = $posts->user_id;
            $user = $userRepo->findOneBy([
                "id" => $user_id
            ]);
            $posts->user = $user;
        } else {
            foreach ($posts as $post) {
                $user_id = $post->user_id;
                var_dump($user_id);
                $user = $userRepo->findOneBy([
                    "id" => $user_id
                ]);
                $post->user = $user;
            }
        }
        return $posts;

    }

    public function findNewPosts() {
        $query = "SELECT * FROM posts ORDER BY id DESC LIMIT 5";
        $stmt = $this->bdd->prepare($query);
        $stmt->execute();
        $posts = $stmt->fetchAll();
        return $posts;
    }

    public function findOneBy($params = []) {
        $query = "SELECT * FROM comments WHERE ";
        $k = 0;
        foreach ($params as $key => $param) {
            if($k !== 0) {
                $query .= 'ANDWHERE ';
            }
            $query .= "$key = $param ";
            $k ++;
        }
        $query .= "LIMIT 1";
        $stmt = $this->bdd->prepare($query);
        $stmt->execute();
        $com = $stmt->fetch();
        return $com;
    }

    public function findBy($params = []) {
        $query = "SELECT * FROM comments WHERE ";
        $k = 0;
        foreach ($params as $key => $param) {
            if($k !== 0) {
                $query .= 'ANDWHERE ';
            }
            $query .= "$key = $param ";
            $k ++;
        }
        $stmt = $this->bdd->prepare($query);
        $stmt->execute();
        $coms = $stmt->fetchAll();
        return $coms;
    }


    public function findAll($limit = null) {
        $query = "SELECT * FROM posts";
        if(!empty($limit)) {
            $query .= ' LIMIT ' . $limit;
        }
        $stmt = $this->bdd->prepare($query);
        $stmt->execute();
        $posts = $stmt->fetchAll();
        return $posts;
    }
}
