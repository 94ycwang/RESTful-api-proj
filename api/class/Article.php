<?php
require_once __DIR__ . '/Errorcode.php';
class Article
{
    /**
     * database connection object
     * @var PDO
     */
    private $_db;

    /**
     * User constructor,
     * @param PDO $_db
     */
    public function __construct(PDO $_db)
    {
        $this->_db = $_db;
    }

    /**
     * Input validation
     *
     * @param $val
     * @return bool
     */
    private function notValid($val)
    {
        if ($val === 0 or $val === "0") {
            return False;
        }
        return empty($val);
    }

    /**
     * Create article
     *
     * @param [string] $title
     * @param [string] $content
     * @param [int] $user_id
     * @return array
     * @throws Exception
     */
    public function create($title, $content, $user_id)
    {
        if ($this->notValid($title)) {
            throw new Exception("Title cannot be null!", Errorcode::ARTICLE_TITLE_CANNOT_NULL);
        }
        if ($this->notValid($content)) {
            throw new Exception("Content cannot be null!", Errorcode::ARTICLE_CONTENT_CANNOT_NULL);
        }
        $sql = "insert into `article` (`title`, `content`, `user_id`, `create_time`) values(:title, :content, :user_id, :create_time)";
        $time = date("Y-m-d H:i:s", time());
        $sm = $this->_db->prepare($sql);

        $sm->bindParam(':title', $title);
        $sm->bindParam(':content', $content);
        $sm->bindParam(':user_id', $user_id);
        $sm->bindParam(':create_time', $time);

        if (!$sm->execute()) {
            throw new Exception("Failed to publish!", Errorcode::ARTICLE_CREATE_FAIL);
        }

        return [
            'title' => $title,
            'content' => $content,
            'article_id' => $this->_db->lastInsertId(),
            'create_time' => $time,
            'user_id' => $user_id
        ];
    }

    /**
     * View article
     *
     * @param [int] $article_id
     * @return mixed
     * @throws Exception
     */
    public function view($article_id)
    {
        if ($this->notValid($article_id)) {
            throw new Exception("Article ID cannot be null!", Errorcode::ARTICLE_ID_CANNOT_NULL);
        }

        $sql = "select * from `article` where `id` = :id";
        $sm = $this->_db->prepare($sql);
        $sm->bindParam(':id', $article_id);
        if (!$sm->execute()) {
            throw new Exception("Failed to get article!", Errorcode::ARTICLE_GET_FAIL);
        }
        $article = $sm->fetch(PDO::FETCH_ASSOC);
        if ($this->notValid($article)) {
            throw new Exception("Article does not exist!", Errorcode::ARTICLE_NOT_EXIST);
        }
        return $article;
    }

    /**
     * Edit article
     *
     * @param [int] $article_id
     * @param [string] $title
     * @param [string] $content
     * @param [int] $user_id
     * @return mixed
     * @throws Exception
     */
    public function edit($article_id, $title, $content, $user_id)
    {
        $article = $this->view($article_id);
        if ($user_id !=  $article['user_id']) {
            throw new Exception("Permission denied!", Errorcode::PERMISSION_NOT_ALLOW);
        }
        $title = $this->notValid($title) ? $article['title'] : $title;
        $content = $this->notValid($content) ? $article['content'] : $content;
        if ($title == $article['title'] && $content == $article['content']) {
            return $article;
        }
        $sql = "update `article` set `title`=:title, `content`=:content where `id`=:id";
        $sm = $this->_db->prepare($sql);
        $sm->bindParam(':title', $title);
        $sm->bindParam(':content', $content);
        $sm->bindParam(':id', $article_id);
        if (!$sm->execute()) {
            throw new Exception("Failed to edit article!", Errorcode::ARTICLE_EDIT_FAIL);
        }
        return [
            'title' => $title,
            'content' => $content,
            'article_id' => $article_id,
            'user_id' => $user_id
        ];
    }

    /**
     * Delete article
     *
     * @param [int] $article_id
     * @param [int] $user_id
     * @return mixed
     * @throws Exception
     */
    public function delete($article_id, $user_id)
    {
        $article = $this->view($article_id);
        if ($user_id != $article['user_id']) {
            throw new Exception("Permission denied!", Errorcode::PERMISSION_NOT_ALLOW);
        }

        $sql = "delete from `article` where `id`=:id";
        $sm = $this->_db->prepare($sql);
        $sm->bindParam(':id', $article_id);
        if (!$sm->execute()) {
            throw new Exception("Failed to delete article!", Errorcode::ARTICLE_DELETE_FAIL);
        }
        return [
            'article_id' => $article_id,
            'user_id' => $user_id
        ];
    }

    public function _list($user_id, $page, $size)
    {
    }
}
