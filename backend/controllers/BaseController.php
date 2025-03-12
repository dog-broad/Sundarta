<?php

class BaseController {
    /**
     * Send a JSON response
     * 
     * @param array $data Data to send
     * @param int $statusCode HTTP status code
     */
    protected function sendJsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Send a success response
     * 
     * @param array $data Data to send
     * @param string $message Success message
     * @param int $statusCode HTTP status code
     */
    protected function sendSuccess($data = [], $message = 'Success', $statusCode = 200) {
        $this->sendJsonResponse([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Send an error response
     * 
     * @param string $message Error message
     * @param int $statusCode HTTP status code
     * @param array $errors Validation errors
     */
    protected function sendError($message = 'Error', $statusCode = 400, $errors = []) {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        $this->sendJsonResponse($response, $statusCode);
    }

    /**
     * Validate required fields
     * 
     * @param array $data Data to validate
     * @param array $requiredFields Required fields
     * @return array Array of missing fields
     */
    protected function validateRequired($data, $requiredFields) {
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missingFields[] = $field;
            }
        }

        return $missingFields;
    }

    /**
     * Get JSON data from request
     * 
     * @return array JSON data
     */
    protected function getJsonData() {
        $json = file_get_contents('php://input');
        return json_decode($json, true) ?? [];
    }

    /**
     * Get request method
     * 
     * @return string Request method
     */
    protected function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Check if request method is allowed
     * 
     * @param string|array $allowedMethods Allowed methods
     * @return bool True if method is allowed, false otherwise
     */
    protected function isMethodAllowed($allowedMethods) {
        $method = $this->getMethod();
        
        if (is_array($allowedMethods)) {
            return in_array($method, $allowedMethods);
        }
        
        return $method === $allowedMethods;
    }

    /**
     * Ensure request method is allowed
     * 
     * @param string|array $allowedMethods Allowed methods
     */
    protected function ensureMethodAllowed($allowedMethods) {
        if (!$this->isMethodAllowed($allowedMethods)) {
            $this->sendError('Method not allowed', 405);
        }
    }

    /**
     * Get query parameters
     * 
     * @return array Query parameters
     */
    protected function getQueryParams() {
        return $_GET;
    }

    /**
     * Get a specific query parameter
     * 
     * @param string $name Parameter name
     * @param mixed $default Default value
     * @return mixed Parameter value
     */
    protected function getQueryParam($name, $default = null) {
        return $_GET[$name] ?? $default;
    }

    /**
     * Get pagination parameters
     * 
     * @param int $defaultPerPage Default items per page
     * @return array Pagination parameters (page, per_page)
     */
    protected function getPaginationParams($defaultPerPage = 10) {
        $page = max(1, (int)($this->getQueryParam('page', 1)));
        $perPage = max(1, (int)($this->getQueryParam('per_page', $defaultPerPage)));
        
        return [
            'page' => $page,
            'per_page' => $perPage
        ];
    }
} 