<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseController extends AbstractController
{
    protected function getCurrentRouteCacheKey(Request $request, ?string $suffix = null): string {
        $key = $request->attributes->get('_route');

        if($suffix) {
            $key .= ':'.$suffix;
        }

        return $key;
    }
}
