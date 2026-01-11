<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use function App\Helpers\user;

class ComponentsFilter implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): ResponseInterface
    {
        helper('oauth');

        $beforeContent = view('components/header', ['user' => user()]);
        if (is_null($arguments) || !in_array('noNavbar', $arguments)) {
            $beforeContent .= view('components/navbar', ['user' => user()]);
        }

        $afterContent = "";
        if (is_null($arguments) || !in_array('noFooter', $arguments)) {
            $afterContent .= view('components/footer');
        }

        $response->setBody($beforeContent . $response->getBody() . $afterContent);

        return $response;
    }
}