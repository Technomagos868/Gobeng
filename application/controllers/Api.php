<?php

defined('BASEPATH') || exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Api extends RestController
{
    private $API_GRANTED = false;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('api_model');
        $this->checkAPIKey();
    }

    public function index_get()
    {
        $this->TCatch(function () {
            $this->SanitizeResponse(200, [], "Welcome to Coralis REST API");
        });
    }

    private function checkAPIKey()
    {
        $api_key = $this->input->get_request_header('X-API-KEY');
        // TODO: Implement API Key checking
    }

    private function TCatch($callback, $is_guarded = false)
    {
        try {
            $user_id = null;
            if ($this->API_GRANTED === false) {
                return $this->UnauhtorizedResponse('Sorry, the API key you provided is invalid or has expired. Please check and try again.');
            }
            if ($is_guarded) {
                // TODO: Implement guard auth
            } else {
                // TODO: Implement non-guard auth
            }
            return $callback($user_id);
        } catch (\Throwable $th) {
            $this->InternalErrorResponse($th);
        }
    }

    private function SanitizeResponse($code, $data = [], $message = false)
    {
        switch ($code) {
            case 200:
                $this->OkResponse($data, $message);
                break;
            case 201:
                $this->CreatedResponse($data, $message);
                break;
            case 204:
                $this->NoContentResponse($data, $message);
                break;
            case 400:
                $this->BadRequestResponse($data, $message);
                break;
            case 401:
                $this->UnauhtorizedResponse($data, $message);
                break;
            case 403:
                $this->ForbiddenResponse($data, $message);
                break;
            case 404:
                $this->NotFoundResponse($data, $message);
                break;
            case 409:
                $this->ConflictResponse($data, $message);
                break;
            default:
                $this->OkResponse($data, $message);
                break;
        }
    }

    private function OkResponse($data = [], $message = false)
    {
        if ($message === false) {
            $message = "The request was successful.";
        }
        return $this->response([
            "data" => $data,
            "status" => "200 OK",
            "message" => $message
        ], 200);
    }

    private function CreatedResponse($data = [], $message = false)
    {
        if ($message === false) {
            $message = "The request was successful, and a new resource was created as a result.";
        }
        return $this->response([
            "data" => $data,
            "status" => "201 Created",
            "message" => $message
        ], 201);
    }

    private function NoContentResponse($data = [], $message = false)
    {
        if ($message === false) {
            $message = "The request was successful, but there is no data to return.";
        }
        return $this->response([
            "data" => $data,
            "status" => "204 No Content",
            "message" => $message
        ], 204);
    }

    private function BadRequestResponse($data = [], $message = false)
    {
        if ($message === false) {
            $message = "The request was invalid or could not be understood by the server.";
        }
        return $this->response([
            "data" => $data,
            "status" => "400 Bad Request",
            "message" => $message
        ], 400);
    }

    private function UnauhtorizedResponse($data = [], $message = false)
    {
        if ($message === false) {
            $message = "The client failed to provide valid authentication credentials.";
        }
        return $this->response([
            "data" => $data,
            "status" => "401 Unauthorized",
            "message" => $message
        ], 401);
    }

    private function ForbiddenResponse($data = [], $message = false)
    {
        if ($message === false) {
            $message = "The client does not have permission to access the requested resource.";
        }
        return $this->response([
            "data" => $data,
            "status" => "403 Forbidden",
            "message" => $message
        ], 403);
    }

    private function NotFoundResponse($data = [], $message = false)
    {
        if ($message === false) {
            $message = "The requested resource could not be found.";
        }
        return $this->response([
            "data" => $data,
            "status" => "404 Not Found",
            "message" => $message
        ], 404);
    }

    private function ConflictResponse($data = [], $message = false)
    {
        if ($message === false) {
            $message = "The requested resource already exist.";
        }
        return $this->response([
            "data" => $data,
            "status" => "409 Conflict",
            "message" => $message
        ], 404);
    }

    private function InternalErrorResponse($error, $message = false)
    {
        if ($message === false) {
            $message = "The server encountered an error while processing the request.";
        }
        $obj = new stdClass();
        $obj->message = $error->getMessage();
        $obj->file = $error->getFile();
        $obj->line = $error->getLine();
        $obj->stack = $error->getTraceAsString();
        return $this->response([
            "status" => "500 Internal Server Error",
            "message" => $message
        ], 500);
    }

    // ============= WRITE YOUR CODE HERE ================
}
