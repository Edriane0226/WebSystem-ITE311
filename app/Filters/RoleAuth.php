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
        if ($role === 'admin') {
            if (strpos($uri, 'admin/') !== 0 && $uri !== 'announcements') {
                //redirect sila tanan sa /announcements page with an error flash message “Access Denied: Insufficient Permissions".
                return redirect()
                    ->to('/announcements')
                    ->with('error', 'Access Denied: Insufficient Permissions');
            }
        }
        // allowed ang teacher to access routes na starting with /teacher
        elseif ($role === 'teacher') {
            if (strpos($uri, 'teacher/') !== 0 && $uri !== 'announcements') {
                return redirect()
                    ->to('/announcements')
                    ->with('error', 'Access Denied: Insufficient Permissions');
            }
        }
        // allowed ang student to access routes na starting with /student
        elseif ($role === 'student') {
            if (strpos($uri, 'student/') !== 0 && $uri !== 'announcements') {
                return redirect()
                    ->to('/announcements')
                    ->with('error', 'Access Denied: Insufficient Permissions');
            }
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        
    }
}
