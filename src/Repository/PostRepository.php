<?php
namespace App\Repository;

use PDO;

class PostRepository extends Connexion
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

    public function fillEntities($posts, $solo = false) {
        $userRepo = new UserRepository();
        $commentRepo = new CommentRepository();
        if($solo) {
            $user_id = $posts->user_id;
            $user = $userRepo->findOneBy([
                "id" => $user_id
            ]);
            $posts->user = $user;

            $post_id = $posts->id;
            $comments = $commentRepo->findBy([
                "post_id" => $post_id
            ]);
            $posts->comments = $comments;
        } else {
            foreach ($posts as $post) {
                $user_id = $post->user_id;
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
        $query = "SELECT * FROM posts WHERE ";
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
        $post = $stmt->fetch();
        return $post;
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
