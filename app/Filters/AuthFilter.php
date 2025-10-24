<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Pastikan kunci session sesuai dengan yang ada di LoginController
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Jika filter punya argumen (misal role)
        if ($arguments && !in_array($session->get('role'), $arguments)) {
            return redirect()->to('/comingsoon')->with('error', 'Akses ditolak.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu apa-apa di sini
    }
}
