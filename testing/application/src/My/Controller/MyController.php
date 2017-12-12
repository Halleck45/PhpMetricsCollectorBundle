<?php

namespace My\Controller;

use Symfony\Component\HttpFoundation\Response;

class MyController
{
    public function route1()
    {
        return new Response('<html><body>Hello world</body></html>', 200);
    }
}
