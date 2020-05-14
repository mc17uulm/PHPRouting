<?php

namespace phpRouter;

use JsonException;

final class Request
{

    private string $uri;
    private HTTPRequestType $type;
    private string $content_type;
    private array $parameters;
    private array $matches;
    private array $headers;
    private string $body;
    private array $payload;
    private array $files;

    /**
     * Request constructor.
     * @param string $uri
     * @param HTTPRequestType $type
     * @throws RouterException
     */
    public function __construct(string $uri, HTTPRequestType $type)
    {
        $this->uri = $uri;
        $this->type = $type;
        $this->content_type = $_SERVER["CONTENT_TYPE"] ?? "text/plain";
        $this->parameters = $this->load_parameters();
        $this->headers = apache_request_headers();
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
    }

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

    public function is_post_request() : bool
    {
        return $this->type->equals(HTTPRequestType::POST());
    }

    public function has_valid_csrf_token(string $token) : bool
    {
        if(isset($this->headers["csrf_token"])) {
            return $this->headers["csrf_token"] === $token;
        }
        return false;
    }

}