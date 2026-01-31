<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->get('is_logged_in')) {
            // Remember the URL that was requested
            $session = session();
            $session->setFlashdata('redirect_url', current_url());
            
            // Redirect to login page with a message
            return redirect()
                ->to(base_url('/'))
                ->with('error', 'Please login to access this page.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No actions needed after the controller execution
    }
}