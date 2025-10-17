<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $uri = $request->getUri()->getPath();

        if (!$session->get('role')) {
            return redirect()->to('/login');
        }

        $role = $session->get('role');
        // allowed ang admin to access routes na starting with /admin
        if ($role === 'admin' && strpos($uri, 'admin/') == 0) {
            return;
        }
        // allowed ang teacher to access routes na starting with /teacher
        elseif ($role == 'teacher' && strpos($uri, 'teacher/') === 0) {
            return;
        }
        // allowed ang student to access routes na starting with /student
        elseif ($role === 'student') {
            if (strpos($uri, 'student/') === 0 || strpos($uri, 'announcements') === 0) {
                return;
            } else {
                return redirect()->to('/login');
            }
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        
    }
}
