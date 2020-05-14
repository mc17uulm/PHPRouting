<?php

namespace phpRouter;

use JsonException;

/**
 * Class Request
 * @package phpRouter
 */
final class Request
{

    /**
     * @var string
     */
    private string $uri;
    /**
     * @var HTTPRequestType
     */
    private HTTPRequestType $type;
    /**
     * @var mixed|string
     */
    private string $content_type;
    /**
     * @var array
     */
    private array $parameters;
    /**
     * @var array
     */
    private array $matches;
    /**
     * @var array|false
     */
    private array $headers;
    /**
     * @var array<string, string>
     */
    private array $queries;
    /**
     * @var false|string
     */
    private string $body;
    /**
     * @var array|mixed
     */
    private array $payload;
    /**
     * @var array
     */
    private array $files;
    /**
     * @var string
     */
    private string $ip;
    /**
     * @var string
     */
    private string $user_agent;

    /**
     * Request constructor.
     * @param string $uri
     * @param HTTPRequestType $type
     * @param array<string, string> $queries
     * @throws RouterException
     */
    public function __construct(string $uri, HTTPRequestType $type, array $queries)
    {
        $this->uri = $uri;
        $this->type = $type;
        $this->content_type = $_SERVER["CONTENT_TYPE"] ?? "text/plain";
        $this->parameters = $this->load_parameters();
        $this->headers = apache_request_headers();
        $this->queries = $queries;
        $this->body = file_get_contents("php://input");
        $this->payload = [];
        if($this->content_type === "application/json"){
            try {
                $this->payload = json_decode($this->body, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                throw new RouterException($e->getMessage());
            }
        }
        $this->files = $_FILES ?? [];
        $this->ip =  $_SERVER["REMOTE_ADDR"];
        $this->user_agent = $_SERVER["HTTP_USER_AGENT"];
    }

    /**
     * @return array
     */
    private function load_parameters() : array
    {
        $parameters = [];
        if(isset($_GET)) {
            foreach($_GET as $key => $value) {
                $parameters[$key] = $value;
            }
        }
        return $parameters;
    }

    /**
     * @param array $matches
     */
    public function set_matches(array $matches) : void
    {
        $this->matches = $matches;
    }

    /**
     * @return array
     */
    public function get_payload() : array
    {
        return $this->payload;
    }

    /**
     * @return string
     */
    public function get_uri(): string
    {
        return $this->uri;
    }

    /**
     * @return HTTPRequestType
     */
    public function get_type(): HTTPRequestType
    {
        return $this->type;
    }

    /**
     * @return mixed|string
     */
    public function get_content_type()
    {
        return $this->content_type;
    }

    /**
     * @return array
     */
    public function get_parameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return array
     */
    public function get_matches(): array
    {
        return $this->matches;
    }

    /**
     * @return array|false
     */
    public function get_headers()
    {
        return $this->headers;
    }

    /**
     * @return array<string, string>
     */
    public function get_queries() : array
    {
        return $this->queries;
    }

    /**
     * @return false|string
     */
    public function get_body()
    {
        return $this->body;
    }

    /**
     * @return array
     */
    public function get_files(): array
    {
        return $this->files;
    }

    /**
     * @return string
     */
    public function get_ip_address() : string
    {
        return $this->ip;
    }

    /**
     * @return string
     */
    public function get_user_agent() : string
    {
        return $this->user_agent;
    }

    /**
     * @return string
     */
    public function hash_user_agent() : string
    {
        return sha1($this->user_agent);
    }

    /**
     * @return bool
     */
    public function is_post_request() : bool
    {
        return $this->type->equals(HTTPRequestType::POST());
    }

    /**
     * @param string $token
     * @return bool
     */
    public function has_valid_csrf_token(string $token) : bool
    {
        if(isset($this->headers["csrf_token"])) {
            return $this->headers["csrf_token"] === $token;
        }
        return false;
    }

}