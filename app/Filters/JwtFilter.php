<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtFilter implements FilterInterface
{
  public function before(RequestInterface $request, $arguments = null)
  {
    // is_ajax() || die('No direct access allowed');
    // dd("hasuidhasid");
    $key = getenv('JWT_SECRET');
    $header = $request->getHeaderLine('Authorization');
    $token = null;

    // Extract the token
    if (!empty($header)) {
      if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
        $token = $matches[1];
      }
    }

    // Check if token exists
    if (is_null($token) || empty($token)) {
      return service('response')
        ->setStatusCode(401)
        ->setJSON(['status' => false, 'message' => 'Access denied. Token required.']);
    }

    try {
      $decoded = JWT::decode($token, new Key($key, 'HS256'));
      // Make decoded token data available to controllers
      $request->decoded = $decoded;
    } catch (Exception $ex) {
      return service('response')
        ->setStatusCode(401)
        ->setJSON(['status' => false, 'message' => 'Access denied. Invalid token.']);
    }
  }

  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
  {
    // No action needed after the request
  }
}