<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Asset_Filter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri = $request->getUri()->getPath();
        log_message('debug', 'Original URI: ' . $uri);

        // Remove any duplicated base URL
        $uri = str_replace(base_url(), '', $uri);
        log_message('debug', 'Processed URI: ' . $uri);

        // Match pattern for assets/modules
        if (preg_match('/assets\/modules\/(.*)$/', $uri, $matches)) {
            log_message('debug', 'Matched URI: ' . $matches[1]);

            // Make uppercase
            $path = $matches[1];
            log_message('debug', 'Uppercase Path: ' . $path);

            // Convert path to Windows directory format
            $modulePath = APPPATH . 'Views\\' . str_replace('/', DIRECTORY_SEPARATOR, $path);
            log_message('debug', 'Module Path: ' . $modulePath);

            // Ensure 'js' folder is only added if not present
            if (strpos($modulePath, DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR) === false) {
                $modulePath = rtrim($modulePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'js';
            }
            log_message('debug', 'Final Module Path: ' . $modulePath);

            // Final file path
            if (substr($modulePath, -strlen(basename($uri))) !== basename($uri)) {
                $filePath = $modulePath . DIRECTORY_SEPARATOR . basename($uri);
            } else {
                $filePath = $modulePath;
            }
            log_message('debug', 'File Path: ' . $filePath);

            // Check if file exists
            if (file_exists($filePath)) {
                log_message('debug', 'Serving file: ' . $filePath);
                $this->serveFile($filePath);
                return;
            }

            log_message('error', 'File not found: ' . $filePath);
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Asset not found');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing here
    }

    private function serveFile($filePath)
    {
        $mime = mime_content_type($filePath);
        header('Content-Type: ' . $mime);
        header('Cache-Control: public, max-age=3600');
        readfile($filePath);
        exit;
    }

    private function makeUppercase ($path) {
        $parts = explode('/', $path);
        if (isset($parts[0])) {
            $parts[0] = ucfirst($parts[0]);
        }
        if (isset($parts[1])) {
            $parts[1] = ucfirst($parts[1]);
        }
        return implode('/', $parts);
    }
}
