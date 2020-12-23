<?php

class Rest
{
    /**
     * @var [User]
     */
    private $_user;

    /**
     * @var [Article]
     */
    private $_article;

    /**
     * Reuqest method
     *
     * @var
     */
    private $_requestMethod;

    /**
     * Request resource
     *
     * @var 
     */
    private $_requestResource;

    /**
     * Allowed request reources
     *
     * @var array
     */
    private $_allowResource = ['users', 'articles'];

    /**
     * Allowed request methods
     *
     * @var array
     */
    private $_allowMethod = ['GET', 'POST', 'PUT', 'DELETE'];

    /**
     * Request version
     *
     * @var
     */
    private $_version;

    /**
     * Request resource identity
     *
     * @var 
     */
    private $_requestUri;

    /**
     * Status code
     *
     * @var array
     */
    private $_statusCode = [
        200 => 'OK',
        204 => 'No Content',
        400 => 'Bad Request',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        500 => 'Server Internal Error'
    ];

    /**
     * Rest constructor
     *
     * @param User $_user
     * @param Article $_article
     */
    public function __construct(User $_user, Article $_article)
    {
        $this->_user = $_user;
        $this->_article = $_article;
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
     * API startup method
     *
     * @return void
     */
    public function run()
    {
        try {
            $this->setMethod();
            $this->setResource();

            if ($this->_requestResource == 'users') {
                $this->sendUsers();
            } else {
                $this->sendArticles();
            }
        } catch (Exception $e) {
            $this->_json($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Set API request method
     *
     * @throws Exception
     */
    private function setMethod()
    {
        $this->_requestMethod = $_SERVER['REQUEST_METHOD'];
        if (!in_array($this->_requestMethod, $this->_allowMethod)) {
            throw new Exception("Request method not allowed!", 405);
        }
    }

    /**
     * Set API rquest resource
     *
     * @throws Exception
     */
    private function setResource()
    {
        $path = $_SERVER['PATH_INFO'];
        $params = explode('/', $path);
        $this->_requestResource = $params[2];
        if (!in_array($this->_requestResource, $this->_allowResource)) {
            throw new Exception("Request resource not allowed!", 405);
        }

        $this->_version = $params[1];

        if (!$this->notValid($params[3])) {
            $this->_requestUri = $params[3];
        }
    }

    /**
     * Output data
     *
     * @param [string] $message
     * @param [int] $code
     */
    private function _json($message, $code)
    {
        if ($code !== 200 && $code > 200) {
            header('HTTP/1.1 ' . $code . ' ' . $this->_statusCode[$code]);
        }
        header("Conetnt-Type:application/json;charset:utf-8");
        if (!empty($message)) {
            echo json_encode(['message' => $message, 'code' => $code]);
        }
        die;
    }

    /**
     * Users logics
     *
     * @return void
     */
    private function sendUsers()
    {
        if ($this->_requestMethod !== 'POST') {
            throw new Exception("Request method not allowed!", 405);
        }
        if ($this->notValid($this->_requestUri)) {
            throw new Exception("Request resource noy allowed!", 405);
        }
        if ($this->_requestUri == 'login') {
            $this->dologin();
        } elseif ($this->_requestUri == 'register') {
            $this->doregister();
        } else {
            throw new Exception("Request resource not allowed!", 405);
        }
    }

    /**
     * User login API
     *
     */
    private function dologin()
    {
        $data = $this->getBody();
        if ($this->notValid($data['name'])) {
            throw new Exception("Username cannot be null!", 400);
        }
        if ($this->notValid($data['password'])) {
            throw new Exception("Password cannot be null!", 400);
        }
        $user_res = $this->_user->login($data['name'], $data['password']);
        $output = [
            'data' => [
                'user_id' => $user_res['id'],
                'name' => $user_res['name'],
                'token' => session_id()
            ],
            'message' => 'Login success!',
            'code' => 200
        ];

        $_SESSION['userInfo']['id'] = $user_res['id'];

        echo json_encode($output);
    }

    /**
     * User registration API
     *
     * @throws Exception
     */
    private function doregister()
    {
        $data = $this->getBody();
        if ($this->notValid($data['name'])) {
            throw new Exception("Username cannot be null!", 400);
        }
        if ($this->notValid($data['password'])) {
            throw new Exception("Password cannot be null!", 400);
        }
        $user_res = $this->_user->register($data['name'], $data['password']);
        if (!empty($user_res)) {
            $this->_json("Registration success!", 200);
        }
    }

    private function getBody()
    {
        $data = file_get_contents("php://input");
        if (empty($data)) {
            throw new Exception("Request params cannot be null!", 400);
        }
        return json_decode($data, true);
    }

    /**
     * Articles logics
     * 
     *  @throws Exception
     */
    private function sendArticles()
    {
        switch ($this->_requestMethod) {
            case 'POST':
                return $this->articleCreate();
            case 'PUT':
                return $this->articleEdit();
            case 'DELETE':
                return $this->articleDelete();
            case 'GET':
                if ($this->_requestUri == 'list') {
                    return $this->articleList();
                } elseif ($this->_requestUri >= 0) {
                    return $this->articleView();
                } else {
                    throw new Exception("Request resource not allowed!", 405);
                }
            default:
                throw new Exception("Request resource noy allowed!", 405);
        }
    }

    /**
     * Login status function
     *
     * @param  $token
     * @return boolean
     */
    private function isLogin($token)
    {
        $sessionID = session_id();
        if ("$sessionID" != $token) {
            return false;
        }
        return true;
    }

    /**
     * View article API
     *
     * @throws Exception
     */
    private function articleView()
    {
        if ($this->notValid($this->_requestUri)) {
            throw new Exception("Request resource noy allowed!", 405);
        }
        $article_res = $this->_article->view($this->_requestUri);
        if (!empty($article_res)) {
            $output = [
                'data' => [
                    'title' => $article_res['title'],
                    'content' => $article_res['content'],
                    'user_id' => $article_res['user_id'],
                    'create_time' => $article_res['create_time']
                ],
                'message' => 'View article success!',
                'code' => 200
            ];
            echo json_encode($output);
            die;
        }
        $this->_json("View article fail!", 500);
    }

    /**
     * Create article API
     *
     * @throws Exception
     */
    private function articleCreate()
    {
        $data = $this->getBody();
        if ($this->notValid($data['title'])) {
            throw new Exception('Title cannot be null!', 400);
        }
        if ($this->notValid($data['content'])) {
            throw new Exception('Content cannot be null!', 400);
        }

        if ($this->isLogin($data['token'])) {
            throw new Exception("Please login!", 403);
        }
        $user_id = $_SESSION['userInfo']['id'];
        $article_res = $this->_article->create($data['title'], $data['content'], $user_id);
        if (!empty($article_res)) {
            $this->_json("Creation success!", 200);
        }
        $this->_json("Creation fail!", 500);
    }

    /**
     * Edit article API
     *
     * @throws Exception
     */
    private function articleEdit()
    {
        $data = $this->getBody();
        if (!$this->isLogin($data['token'])) {
            throw new Exception("Please login!", 403);
        }
        $article_res = $this->_article->view($this->_requestUri);
        if ($article_res['user_id'] != $_SESSION['userInfo']['id']) {
            throw new Exception("Permission not allowed!", 403);
        }
        $res = $this->_article->edit($this->_requestUri, $data['title'], $data['content'], $_SESSION['userInfo']['id']);
        if (!empty($res)) {
            $output = [
                'data' => [
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'user_id' => $article_res['user_id'],
                    'create_time' => $article_res['create_time']
                ],
                'message' => 'Edit success!',
                'code' => 200
            ];
            echo json_encode($output);
            die;
        }

        $output = [
            'data' => [
                'title' => $article_res['title'],
                'content' => $article_res['content'],
                'user_id' => $article_res['user_id'],
                'create_time' => $article_res['create_time']
            ],
            'message' => 'Edit fail!',
            'code' => 500
        ];
        echo json_encode($output);
    }

    /**
     * Delete article API
     *
     * @throws Exception
     */
    private function articleDelete()
    {
        $data = $this->getBody();
        if (!$this->isLogin($data['token'])) {
            throw new Exception("Please login!", 403);
        }
        $article_res = $this->_article->view($this->_requestUri);
        if ($article_res['user_id'] != $_SESSION['userInfo']['id']) {
            throw new Exception("Permission not allowed!", 403);
        }
        $res = $this->_article->delete($this->_requestUri, $_SESSION['userInfo']['id']);
        if (!empty($res)) {
            $this->_json("Deletion success!", 200);
        }
        $this->_json("Deletion fail!", 500);
    }

    /**
     * List article API
     *
     * @throws Exception
     */
    private function articleList()
    {
    }
}
